<?php

namespace App\Services;

use App\Core\Database;

class PdfKnowledgeImporter
{
    protected $db;

    public function __construct($db = null)
    {
        $this->db = $db ?: Database::connect();
    }

    public function importDirectory(string $directory, bool $extractText = false): array
    {
        $directory = rtrim($directory, DIRECTORY_SEPARATOR);
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException('Knowledge directory not found: ' . $directory);
        }

        $files = $this->discoverFiles($directory);
        $summary = [
            'files' => 0,
            'registered' => 0,
            'extracted' => 0,
            'skipped' => 0,
            'items' => [],
        ];

        foreach ($files as $file) {
            $summary['files']++;
            $meta = $this->registerSource($file, $extractText);
            if ($meta['registered']) {
                $summary['registered']++;
            } else {
                $summary['skipped']++;
            }

            if ($meta['extracted']) {
                $summary['extracted']++;
            }

            $summary['items'][] = $meta;
        }

        return $summary;
    }

    public function discoverFiles(string $directory): array
    {
        $files = glob($directory . DIRECTORY_SEPARATOR . '*.pdf');
        return is_array($files) ? array_values(array_filter($files, 'is_file')) : [];
    }

    public function registerSource(string $pdfPath, bool $extractText = false): array
    {
        if (!is_file($pdfPath)) {
            return ['registered' => false, 'path' => $pdfPath, 'reason' => 'missing'];
        }

        $filename = basename($pdfPath);
        $hash = hash_file('sha256', $pdfPath);
        $source = ['source_document' => $pdfPath, 'source_name' => $filename, 'source_hash' => $hash, 'source_type' => 'pdf'];
        $source['normalized_text'] = $extractText ? $this->extractText($pdfPath) : null;
        $source['classification'] = $this->classifyDocument($filename, $source['normalized_text'] ?? '');
        $source['mapped_vehicle'] = $this->mapVehicle($source['classification']);
        $source['draft'] = $this->createDraft($source);
        $source['is_valid'] = $this->validateContent($source['draft']);

        $status = $source['is_valid'] ? ($source['normalized_text'] !== null && trim((string) $source['normalized_text']) !== '' ? 'mapped' : 'imported') : 'rejected';

        $stmt = $this->db->prepare('INSERT INTO knowledge_source_documents (source_path, source_name, source_hash, source_type, language, extracted_text, status) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE source_path = VALUES(source_path), source_name = VALUES(source_name), extracted_text = COALESCE(VALUES(extracted_text), extracted_text), updated_at = CURRENT_TIMESTAMP');
        $stmt->execute([
            $pdfPath,
            $filename,
            $hash,
            'pdf',
            $source['classification']['language'] ?? 'fa',
            $source['normalized_text'],
            $status,
        ]);

        return [
            'registered' => true,
            'path' => $pdfPath,
            'file' => $filename,
            'hash' => $hash,
            'brand' => $source['mapped_vehicle']['brand'] ?? null,
            'model' => $source['mapped_vehicle']['model'] ?? null,
            'category' => $source['classification']['category'] ?? null,
            'service' => $source['classification']['service'] ?? null,
            'content_type' => $source['classification']['content_type'] ?? null,
            'language' => $source['classification']['language'] ?? 'fa',
            'extracted' => $source['normalized_text'] !== null && trim((string) $source['normalized_text']) !== '',
            'valid' => $source['is_valid'],
            'status' => $status,
        ];
    }

    public function normalizeText(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', (string) $text);
        return trim((string) $text);
    }

    public function extractText(string $pdfPath): ?string
    {
        $bin = getenv('PDFTOTEXT_BIN') ?: (PHP_OS_FAMILY === 'Windows' ? 'pdftotext.exe' : 'pdftotext');
        $temp = tempnam(sys_get_temp_dir(), 'oa_pdf_');
        if ($temp === false) {
            return null;
        }

        $command = escapeshellarg($bin) . ' -layout ' . escapeshellarg($pdfPath) . ' ' . escapeshellarg($temp) . ' 2>NUL';
        $output = [];
        $code = 0;
        @exec($command, $output, $code);

        if ($code !== 0 || !is_file($temp)) {
            @unlink($temp);
            return null;
        }

        $content = file_get_contents($temp);
        @unlink($temp);
        $content = is_string($content) ? $content : '';
        $normalized = $this->normalizeText($content);
        return $normalized !== '' ? $normalized : null;
    }

    public function classifyDocument(string $filename, ?string $text = null): array
    {
        $base = strtolower(pathinfo($filename, PATHINFO_FILENAME));
        $serviceMap = [
            'brk' => 'brakes',
            'clg' => 'cooling',
            'diag' => 'diagnostic',
            'ele' => 'electrical',
            'eng' => 'engine',
            'fue' => 'fuel-system',
            'gbx' => 'gearbox',
            'sus' => 'suspension',
            'svc' => 'maintenance',
            'maintenance' => 'maintenance',
            'repair' => 'repair',
            'dtc' => 'diagnostic',
            'ecu' => 'electrical',
            'faq' => 'faq',
        ];

        $service = 'general';
        $contentType = 'overview';
        foreach ($serviceMap as $token => $mappedService) {
            if (strpos($base, $token) !== false) {
                $service = $mappedService;
                $contentType = $mappedService;
                break;
            }
        }

        $category = $service;
        if ($service === 'general' && is_string($text) && $text !== '') {
            $keywords = strtolower($text);
            foreach (['موتور' => 'engine', 'گیربکس' => 'transmission', 'برق' => 'electrical', 'ECU' => 'ecu', 'خطا' => 'diagnostic', 'مشکل' => 'common-problem', 'نگهداری' => 'maintenance'] as $term => $mapped) {
                if (strpos($keywords, $term) !== false) {
                    $category = $mapped;
                    break;
                }
            }
        }

        return [
            'brand' => null,
            'model' => null,
            'service' => $service,
            'category' => $category,
            'content_type' => $contentType,
            'language' => 'fa',
        ];
    }

    public function mapVehicle(array $classification): array
    {
        $brand = null;
        $model = null;
        $base = (string) ($classification['source_name'] ?? '');
        $clean = strtolower(pathinfo($base, PATHINFO_FILENAME));
        $segments = preg_split('/[^a-z0-9]+/i', $clean, -1, PREG_SPLIT_NO_EMPTY);
        if (!empty($segments)) {
            $model = $segments[count($segments) - 1];
            if (count($segments) > 1) {
                $brand = $segments[0];
            }
        }

        return [
            'brand' => $brand,
            'model' => $model,
            'service' => $classification['service'] ?? 'general',
            'category' => $classification['category'] ?? 'general',
        ];
    }

    public function validateContent(array $draft): bool
    {
        if (!is_array($draft)) {
            return false;
        }

        $title = trim((string) ($draft['title'] ?? ''));
        $body = trim((string) ($draft['body'] ?? ''));
        return $title !== '' && $body !== '';
    }

    public function deduplicate(array $draft): bool
    {
        $slug = trim((string) ($draft['slug'] ?? ''));
        if ($slug === '') {
            return false;
        }

        $sourceHash = trim((string) ($draft['source_hash'] ?? ''));
        $sourceId = null;

        if ($sourceHash !== '') {
            $sourceStmt = $this->db->prepare('SELECT id FROM knowledge_source_documents WHERE source_hash = ? LIMIT 1');
            $sourceStmt->execute([$sourceHash]);
            $sourceId = $sourceStmt->fetchColumn();
        }

        if ($sourceId !== false && $sourceId !== null && $sourceId !== '') {
            $stmt = $this->db->prepare('SELECT id FROM vehicle_knowledge_contents WHERE slug = ? OR source_document_id = ? LIMIT 1');
            $stmt->execute([$slug, $sourceId]);
            return (bool) $stmt->fetchColumn();
        }

        $stmt = $this->db->prepare('SELECT id FROM vehicle_knowledge_contents WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return (bool) $stmt->fetchColumn();
    }

    public function createDraft(array $source): array
    {
        $base = strtolower(pathinfo((string) ($source['source_name'] ?? ''), PATHINFO_FILENAME));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $base);
        $slug = trim((string) $slug, '-');
        $slug = $slug !== '' ? strtolower($slug) : 'vehicle-knowledge-draft';

        $title = ucfirst(str_replace('-', ' ', $slug));
        $body = $source['normalized_text'] ?? '';

        return [
            'title' => $title,
            'slug' => $slug,
            'body' => $body,
            'source_hash' => $source['source_hash'] ?? '',
            'source_document' => $source['source_document'] ?? null,
            'source_type' => $source['source_type'] ?? 'pdf',
            'brand' => $source['mapped_vehicle']['brand'] ?? null,
            'model' => $source['mapped_vehicle']['model'] ?? null,
        ];
    }
}
