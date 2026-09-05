<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\VehicleSelectionSession;
use App\Models\Product;
use PDO;

class ShopController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index()
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $q = trim((string) ($_GET['q'] ?? ''));
        $category = isset($_GET['category']) && $_GET['category'] !== '' ? (int) $_GET['category'] : null;
        $sort = trim((string) ($_GET['sort'] ?? 'newest'));

        $sessionVehicle = VehicleSelectionSession::getSelectedVehicle();
        $vehicleBrandId = isset($_GET['vehicle_brand_id']) && $_GET['vehicle_brand_id'] !== '' ? (int) $_GET['vehicle_brand_id'] : (int) ($sessionVehicle['vehicle_brand_id'] ?? 0);
        $vehicleModelId = isset($_GET['vehicle_model_id']) && $_GET['vehicle_model_id'] !== '' ? (int) $_GET['vehicle_model_id'] : (int) ($sessionVehicle['vehicle_model_id'] ?? 0);
        $vehicleYear = trim((string) ($_GET['vehicle_year'] ?? ''));
        if ($vehicleYear === '' && isset($sessionVehicle['vehicle_year'])) {
            $vehicleYear = trim((string) $sessionVehicle['vehicle_year']);
        }

        if (isset($_GET['clear_vehicle']) || (isset($_GET['vehicle_action']) && $_GET['vehicle_action'] === 'clear')) {
            VehicleSelectionSession::clearSelectedVehicle();
            $sessionVehicle = [];
            $vehicleBrandId = null;
            $vehicleModelId = null;
            $vehicleYear = '';
        }

        if (isset($_GET['vehicle_brand_id']) || isset($_GET['vehicle_model_id']) || isset($_GET['vehicle_year'])) {
            $selectedVehicle = [
                'vehicle_brand_id' => $vehicleBrandId ?: null,
                'vehicle_model_id' => $vehicleModelId ?: null,
                'vehicle_year' => $vehicleYear,
            ];
            if (!empty($selectedVehicle['vehicle_brand_id']) || !empty($selectedVehicle['vehicle_model_id']) || $selectedVehicle['vehicle_year'] !== '') {
                VehicleSelectionSession::setSelectedVehicle($selectedVehicle);
                $sessionVehicle = VehicleSelectionSession::getSelectedVehicle();
            }
        }

        if ($vehicleModelId !== null && $vehicleModelId <= 0) {
            $vehicleModelId = null;
        }
        if ($vehicleBrandId !== null && $vehicleBrandId <= 0) {
            $vehicleBrandId = null;
        }
        if ($vehicleYear !== '' && !ctype_digit($vehicleYear)) {
            $vehicleYear = '';
        }

        $searchResult = $this->productModel->smartSearch($q, [
            'category' => $category,
            'sort' => $sort,
            'vehicle_brand_id' => $vehicleBrandId,
            'vehicle_model_id' => $vehicleModelId,
            'vehicle_year' => $vehicleYear,
        ], $page, $perPage);

        $products = $searchResult['products'] ?? [];
        $total = (int) ($searchResult['total'] ?? 0);
        $pages = max(1, (int) ($searchResult['pages'] ?? ceil($total / $perPage)));

        $suggestions = [];
        if ($q !== '') {
            $suggestions = $this->productModel->getSearchSuggestions($q, 5, [
                'vehicle_model_id' => $vehicleModelId,
                'vehicle_brand_id' => $vehicleBrandId,
            ]);
        }

        $categories = [];
        $vehicleBrands = [];
        $vehicleModels = [];

        try {
            $cats = $this->productModel->db->query('SELECT id, name_fa, name_en, slug, parent_id FROM product_categories WHERE status = 1 ORDER BY parent_id ASC, name_fa ASC')->fetchAll();
            $categories = $cats;
        } catch (\Throwable $e) {
            $categories = [];
        }

        try {
            $brandsStmt = $this->productModel->db->prepare('SELECT id, name_fa, name_en, slug FROM vehicle_brands WHERE status = 1 ORDER BY name_fa ASC, name_en ASC');
            $brandsStmt->execute();
            $vehicleBrands = $brandsStmt->fetchAll();
        } catch (\Throwable $e) {
            $vehicleBrands = [];
        }

        try {
            if ($vehicleBrandId && $vehicleBrandId > 0) {
                $modelsStmt = $this->productModel->db->prepare('SELECT id, name_fa, name_en, slug, year_from, year_to, engine_type FROM vehicle_models WHERE status = 1 AND brand_id = ? ORDER BY name_fa ASC, name_en ASC');
                $modelsStmt->execute([(int) $vehicleBrandId]);
                $vehicleModels = $modelsStmt->fetchAll();
            }
        } catch (\Throwable $e) {
            $vehicleModels = [];
        }

        $this->view('shop/index', [
            'products' => $products,
            'filteredProducts' => $products,
            'categories' => $categories,
            'vehicleBrands' => $vehicleBrands,
            'vehicleModels' => $vehicleModels,
            'selectedVehicle' => $sessionVehicle,
            'searchSuggestions' => $suggestions,
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
            'pagination' => ['current' => $page, 'pages' => $pages, 'total' => $total, 'perPage' => $perPage],
            'filters' => ['q' => $q, 'category' => $category, 'sort' => $sort, 'vehicle_brand_id' => $vehicleBrandId, 'vehicle_model_id' => $vehicleModelId, 'vehicle_year' => $vehicleYear],
            'filter' => ['q' => $q, 'category' => $category, 'sort' => $sort, 'vehicle_brand_id' => $vehicleBrandId, 'vehicle_model_id' => $vehicleModelId, 'vehicle_year' => $vehicleYear],
        ]);
    }

    public function search()
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $q = trim((string) ($_GET['query'] ?? $_GET['q'] ?? ''));
        $brand = isset($_GET['brand']) && $_GET['brand'] !== '' ? trim((string) $_GET['brand']) : '';
        $model = isset($_GET['model']) && $_GET['model'] !== '' ? trim((string) $_GET['model']) : '';
        $year = trim((string) ($_GET['year'] ?? ''));

        $sessionVehicle = VehicleSelectionSession::getSelectedVehicle();
        $vehicleBrandId = isset($_GET['vehicle_brand_id']) && $_GET['vehicle_brand_id'] !== '' ? (int) $_GET['vehicle_brand_id'] : (int) ($sessionVehicle['vehicle_brand_id'] ?? 0);
        $vehicleModelId = isset($_GET['vehicle_model_id']) && $_GET['vehicle_model_id'] !== '' ? (int) $_GET['vehicle_model_id'] : (int) ($sessionVehicle['vehicle_model_id'] ?? 0);
        if ($vehicleBrandId <= 0) {
            $vehicleBrandId = null;
        }
        if ($vehicleModelId <= 0) {
            $vehicleModelId = null;
        }

        $results = $this->productModel->smartSearch($q, [
            'vehicle_brand_id' => $vehicleBrandId,
            'vehicle_model_id' => $vehicleModelId,
            'vehicle_year' => $year,
        ], $page, $perPage);

        $brands = [];
        $models = [];
        $years = [];

        try {
            $brandStmt = $this->productModel->db->prepare('SELECT DISTINCT vb.name_fa AS brand FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 ORDER BY brand ASC');
            $brandStmt->execute();
            $brands = array_filter(array_map(static function ($row) {
                return $row['brand'] ?? '';
            }, $brandStmt->fetchAll(PDO::FETCH_ASSOC)));
        } catch (\Throwable $e) {
            $brands = [];
        }

        try {
            $modelStmt = $this->productModel->db->prepare('SELECT DISTINCT vm.name_fa AS model FROM vehicle_models vm WHERE vm.status = 1 ORDER BY model ASC');
            $modelStmt->execute();
            $models = array_filter(array_map(static function ($row) {
                return $row['model'] ?? '';
            }, $modelStmt->fetchAll(PDO::FETCH_ASSOC)));
        } catch (\Throwable $e) {
            $models = [];
        }

        try {
            $yearStmt = $this->productModel->db->prepare('SELECT DISTINCT year_from AS year FROM vehicle_models WHERE status = 1 AND year_from IS NOT NULL ORDER BY year_from ASC LIMIT 30');
            $yearStmt->execute();
            foreach ($yearStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (!empty($row['year'])) {
                    $years[] = (string) $row['year'];
                }
            }
        } catch (\Throwable $e) {
            $years = [];
        }

        $this->view('shop/search', [
            'products' => $results['products'] ?? [],
            'brands' => $brands,
            'models' => $models,
            'years' => $years,
            'selectedVehicle' => $sessionVehicle,
            'filter' => ['brand' => $brand, 'model' => $model, 'year' => $year, 'query' => $q],
            'page' => $page,
            'pages' => (int) ($results['pages'] ?? 1),
            'total' => (int) ($results['total'] ?? 0),
        ]);
    }

    public function product($slug)
    {
        $product = null;

        if ($slug) {
            $product = $this->productModel->findBySlug($slug);
        }

        if (!$product && isset($_GET['id'])) {
            $product = $this->productModel->findById((int) $_GET['id']);
        }

        if ($product) {
            $product['title'] = $product['title_fa'] ?? $product['title_en'] ?? $product['slug'] ?? '';
            $product['description'] = $product['description_fa'] ?? $product['description_en'] ?? '';
            $related = $this->productModel->getRelatedProducts((int) ($product['id'] ?? 0), (int) ($product['category_id'] ?? 0), 4);
            $product['related_products'] = $related;

            try {
                $productCompatibility = new \App\Models\ProductCompatibility();
                $product['compatible_models'] = $productCompatibility->getCompatibleVehicleModels((int) ($product['id'] ?? 0));
            } catch (\Throwable $e) {
                $product['compatible_models'] = [];
            }
        } else {
            $product = ['related_products' => [], 'compatible_models' => []];
        }

        $this->view('shop/product', [
            'slug' => $slug,
            'product' => $product,
        ]);
    }

    public function cart()
    {
        $this->view('shop/cart');
    }

    public function checkout()
    {
        $this->view('shop/checkout');
    }
}

