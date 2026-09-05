<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\VehicleCatalog;
use PDO;

class BrandController extends Controller
{
    private function getDatabase()
    {
        $db = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        return $db;
    }

    public function index()
    {
        try {
            $db = $this->getDatabase();
            try {
                $stmt = $db->query('SELECT DISTINCT vb.id, vb.name AS name, COALESCE(vb.name_fa, vb.name, vb.name_en, vb.slug) AS name_fa, COALESCE(vb.name_en, vb.name, vb.name_fa, vb.slug) AS name_en FROM vehicle_brands vb INNER JOIN vehicle_models vm ON vm.brand_id = vb.id WHERE vb.status = 1 AND vm.status = 1 ORDER BY name_fa ASC, name_en ASC, name ASC');
                $brands = $stmt->fetchAll();
            } catch (\Throwable $e) {
                // Fallback to minimal available columns
                $stmt = $db->query('SELECT DISTINCT vb.id, vb.name FROM vehicle_brands vb INNER JOIN vehicle_models vm ON vm.brand_id = vb.id WHERE vb.status = 1 AND vm.status = 1 ORDER BY vb.name ASC');
                $brands = $stmt->fetchAll();
            }
        } catch (\Throwable $e) {
            $brands = [];
        }

        $this->view('brands/index', ['brands' => $brands]);
    }

    public function show($brand)
    {
        if (!$brand) {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = rtrim($uri, '/') ?: '/';
            // basename may be percent-encoded when reached via direct file lookup;
            // decode to get the real UTF-8 brand segment.
            $brand = rawurldecode(basename($uri));
        }

        try {
            $db = $this->getDatabase();
            // Try robust brand lookup; fall back to conservative name-only match on error
            try {
                $stmt = $db->prepare('SELECT vb.id, COALESCE(vb.name_fa, vb.name, vb.name_en, vb.slug) AS brand_name, COALESCE(vb.name_en, vb.name, vb.name_fa, vb.slug) AS brand_name_en, vb.status FROM vehicle_brands vb WHERE vb.status = 1 AND ' . \brand_match_sql('vb', 3) . ' LIMIT 1');
                $stmt->execute([$brand, $brand, $brand]);
                $brandRow = $stmt->fetch();
            } catch (\Throwable $e) {
                $stmt = $db->prepare('SELECT vb.id, vb.name AS brand_name, vb.status FROM vehicle_brands vb WHERE vb.status = 1 AND (LOWER(vb.name) = LOWER(?) OR LOWER(vb.name_fa) = LOWER(?)) LIMIT 1');
                $stmt->execute([$brand, $brand]);
                $brandRow = $stmt->fetch();
            }
            if ($brandRow) {
                $modelStmt = $db->prepare('SELECT vm.id, vm.name_fa AS model_name, COALESCE(vm.name_en, vm.name_fa, vm.slug) AS model_name_en, vm.slug, vm.engine_type, vm.year_from AS year_start, vm.year_to AS year_end FROM vehicle_models vm WHERE vm.status = 1 AND vm.brand_id = ? ORDER BY vm.name_fa ASC, model_name_en ASC');
                $modelStmt->execute([(int) $brandRow['id']]);
                $models = $modelStmt->fetchAll();

                // Fallback label/slug handling when slug column is not present
                $brandLabel = $brandRow['brand_name'] ?: ($brandRow['brand_name_en'] ?: $brand);
                $brandSlug = $brandRow['brand_name_en'] ?? ($brandRow['brand_name'] ?? $brand);
                $this->view('vehicles/brand', ['brand' => $brandLabel, 'vehicles' => $models, 'brandSlug' => $brandSlug]);
                return;
            }
        } catch (\Throwable $e) {
            // fall through to legacy static pages only when no DB match exists
        }

        $viewPath = 'brands/' . $brand;
        if (file_exists(__DIR__ . '/../Views/' . $viewPath . '.php')) {
            $this->view($viewPath);
            return;
        }

        http_response_code(404);
        echo 'صفحه مورد نظر یافت نشد.';
    }
}
