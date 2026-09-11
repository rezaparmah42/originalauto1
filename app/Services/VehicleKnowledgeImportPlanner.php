<?php

namespace App\Services;

class VehicleKnowledgeImportPlanner
{
    private const DEFAULT_ROOTS = [
        'ai_sources',
        'ai_sources/vehicle_knowledge',
    ];

    private const BRAND_ALIASES = [
        'bmw' => 'bmw',
        'mercedes' => 'mercedes-benz',
        'mercedes-benz' => 'mercedes-benz',
        'benz' => 'mercedes-benz',
        'audi' => 'audi',
        'volkswagen' => 'volkswagen',
        'vw' => 'volkswagen',
        'ford' => 'ford',
        'toyota' => 'toyota',
        'honda' => 'honda',
        'nissan' => 'nissan',
        'mazda' => 'mazda',
        'subaru' => 'subaru',
        'mitsubishi' => 'mitsubishi',
        'peugeot' => 'peugeot',
        'renault' => 'renault',
        'skoda' => 'skoda',
        'kia' => 'kia',
        'hyundai' => 'hyundai',
        'lexus' => 'lexus',
        'volvo' => 'volvo',
        'chevrolet' => 'chevrolet',
        'opel' => 'opel',
        'seat' => 'seat',
        'saab' => 'saab',
        'mini' => 'mini',
        'mg' => 'mg',
        'changan' => 'changan',
        'proton' => 'proton',
    ];

    private const SERVICE_TOKENS = [
        'engine' => 'engine',
        'motor' => 'engine',
        'gearbox' => 'gearbox',
        'transmission' => 'gearbox',
        'brake' => 'brakes',
        'brakes' => 'brakes',
        'electrical' => 'electrical',
        'elec' => 'electrical',
        'ecu' => 'ecu',
        'ac' => 'air-conditioning',
        'cooling' => 'cooling',
        'aircond' => 'air-conditioning',
        'suspension' => 'suspension',
        'paint' => 'paint',
        'body' => 'body-repair',
        'diagnostic' => 'diagnostic',
        'diag' => 'diagnostic',
        'maintenance' => 'maintenance',
        'service' => 'maintenance',
        'faq' => 'faq',
        'troubleshooting' => 'diagnostic',
    ];

    private const CONTENT_TYPES = [
        'introduction' => 'introduction',
        'overview' => 'overview',
        'problem' => 'common-problems',
        'problems' => 'common-problems',
        'symptom' => 'symptoms',
        'symptoms' => 'symptoms',
        'diagnostic' => 'diagnostics',
        'diagnostics' => 'diagnostics',
        'dtc' => 'dtc',
        'ecu' => 'ecu',
        'maintenance' => 'maintenance',
        'service' => 'services',
        'repair' => 'repair-solutions',
        'faq' => 'faq',
        'history' => 'introduction',
    ];

    public function buildPlan(string $root): array
    {
        $root = rtrim((string) $root, DIRECTORY_SEPARATOR);
        $pdfFiles = $this->discoverPdfFiles($root);

        $plan = [
            'source_root' => $root,
            'pdf_count' => count($pdfFiles),
            'page_target' => 595,
            'documents' => [],
            'summary' => [
                'brands' => [],
                'models' => [],
                'service_categories' => [],
                'knowledge_categories' => [],
            ],
        ];

        foreach ($pdfFiles as $pdfPath) {
            $analysis = $this->analyzePdf($pdfPath);
            $plan['documents'][] = $analysis;

            if (!empty($analysis['brand'])) {
                $plan['summary']['brands'][$analysis['brand']] = true;
            }
            if (!empty($analysis['model'])) {
                $plan['summary']['models'][$analysis['model']] = true;
            }
            if (!empty($analysis['service_category'])) {
                $plan['summary']['service_categories'][$analysis['service_category']] = true;
            }
            if (!empty($analysis['knowledge_category'])) {
                $plan['summary']['knowledge_categories'][$analysis['knowledge_category']] = true;
            }
        }

        $plan['summary']['brands'] = array_keys($plan['summary']['brands']);
        $plan['summary']['models'] = array_keys($plan['summary']['models']);
        $plan['summary']['service_categories'] = array_keys($plan['summary']['service_categories']);
        $plan['summary']['knowledge_categories'] = array_keys($plan['summary']['knowledge_categories']);

        return $plan;
    }

    public function discoverPdfFiles(string $root): array
    {
        if (!is_dir($root)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile() && strtolower($fileInfo->getExtension()) === 'pdf') {
                $files[] = $fileInfo->getPathname();
            }
        }

        sort($files, SORT_STRING);
        return $files;
    }

    public function analyzePdf(string $pdfPath): array
    {
        $baseName = basename($pdfPath);
        $fileName = pathinfo($baseName, PATHINFO_FILENAME);
        $normalized = strtolower($fileName);
        $tokens = preg_split('/[^a-z0-9]+/i', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        $tokens = array_values(array_filter($tokens, static fn ($token) => $token !== ''));

        $brand = $this->inferBrand($tokens);
        $model = $this->inferModel($tokens, $brand);
        $generation = $this->inferGeneration($tokens);
        $engine = $this->inferEngine($tokens);
        $serviceCategory = $this->inferServiceCategory($tokens);
        $knowledgeCategory = $this->inferKnowledgeCategory($tokens, $serviceCategory);

        return [
            'source_file' => $pdfPath,
            'filename' => $baseName,
            'brand' => $brand,
            'model' => $model,
            'generation' => $generation,
            'engine' => $engine,
            'service_category' => $serviceCategory,
            'knowledge_category' => $knowledgeCategory,
            'content_type' => $this->inferContentType($tokens, $knowledgeCategory),
            'tokens' => $tokens,
        ];
    }

    public function inferBrand(array $tokens): ?string
    {
        foreach ($tokens as $token) {
            $canonical = self::BRAND_ALIASES[$token] ?? null;
            if ($canonical !== null) {
                return $canonical;
            }
        }

        foreach ($tokens as $token) {
            foreach (self::BRAND_ALIASES as $alias => $value) {
                if (strpos($alias, $token) !== false || strpos($token, $alias) !== false) {
                    return $value;
                }
            }
        }

        return null;
    }

    public function inferModel(array $tokens, ?string $brand = null): ?string
    {
        if ($brand !== null) {
            $filtered = [];
            foreach ($tokens as $token) {
                if ($token === $brand || str_replace('-', '', $token) === str_replace('-', '', $brand)) {
                    continue;
                }
                $filtered[] = $token;
            }
        } else {
            $filtered = $tokens;
        }

        $ignored = ['pdf', 'vehicle', 'cars', 'car', 'manual', 'guide', 'knowledge', 'service', 'repair', 'maintenance', 'diagnostic', 'system'];
        foreach ($filtered as $token) {
            if (in_array($token, $ignored, true)) {
                continue;
            }

            if (preg_match('/^(c|e|a|s|x|m|g|t|q|r|p|v)[0-9]/i', $token) === 1) {
                return strtoupper($token);
            }

            if (preg_match('/^[a-z]+[0-9]+[a-z0-9]*$/i', $token) === 1) {
                return strtoupper($token);
            }

            if (preg_match('/^[a-z]+$/i', $token) === 1 && strlen($token) >= 3) {
                return $token;
            }
        }

        return $filtered[0] ?? null;
    }

    public function inferGeneration(array $tokens): ?string
    {
        foreach ($tokens as $token) {
            if (preg_match('/^(gen|generation|mk|series|g|gen[0-9]|mk[0-9]|series[0-9])$/i', $token) === 1) {
                return strtoupper($token);
            }

            if (preg_match('/^(gen|mk|series)[-_]?[0-9]+$/i', implode('-', $tokens)) === 1) {
                $match = preg_match('/(gen|mk|series)[-_]?[0-9]+/i', implode('-', $tokens), $m);
                return $match ? strtoupper($m[0]) : null;
            }
        }

        foreach ($tokens as $token) {
            if (preg_match('/^[0-9]{4}$/', $token) === 1) {
                return $token;
            }
        }

        return null;
    }

    public function inferEngine(array $tokens): ?string
    {
        foreach ($tokens as $token) {
            if (preg_match('/^(2\.0|2\.4|2\.8|3\.0|3\.2|3\.5|4\.0|4\.2|5\.0|1\.6|1\.8|2\.5|1\.4|1\.5|1\.9|2\.7|3\.6|6\.0|v6|v8|i4|i5|i6|tdi|tdi2|fsi|tsi|tfs|hybrid)$/i', $token) === 1) {
                return strtoupper($token);
            }

            if (preg_match('/^(t|f|s|fsi|tsi|tdi|petrol|diesel|hybrid|gasoline|benzin)$/i', $token) === 1) {
                return strtoupper($token);
            }
        }

        return null;
    }

    public function inferServiceCategory(array $tokens): string
    {
        foreach ($tokens as $token) {
            if (isset(self::SERVICE_TOKENS[$token])) {
                return self::SERVICE_TOKENS[$token];
            }
        }

        foreach ($tokens as $token) {
            foreach (self::SERVICE_TOKENS as $needle => $mapped) {
                if (strpos($needle, $token) !== false || strpos($token, $needle) !== false) {
                    return $mapped;
                }
            }
        }

        return 'maintenance';
    }

    public function inferKnowledgeCategory(array $tokens, string $serviceCategory): string
    {
        $combined = implode(' ', $tokens);
        foreach (self::CONTENT_TYPES as $needle => $mapped) {
            if (stripos($combined, $needle) !== false) {
                return $mapped;
            }
        }

        return $serviceCategory === 'diagnostic' ? 'diagnostics' : 'overview';
    }

    public function inferContentType(array $tokens, string $knowledgeCategory): string
    {
        foreach (self::CONTENT_TYPES as $needle => $mapped) {
            if (in_array($needle, $tokens, true) || stripos(implode(' ', $tokens), $needle) !== false) {
                return $mapped;
            }
        }

        return $knowledgeCategory;
    }
}
