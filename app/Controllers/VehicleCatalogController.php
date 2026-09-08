<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\VehicleCatalog;

class VehicleCatalogController extends Controller
{
    private $catalog;

    public function __construct()
    {
        $this->catalog = new VehicleCatalog();
    }

    public function index()
    {
        $filters = [
            'brand' => trim((string) ($_GET['brand'] ?? '')),
            'model' => trim((string) ($_GET['model'] ?? '')),
            'engine' => trim((string) ($_GET['engine'] ?? '')),
            'year' => trim((string) ($_GET['year'] ?? '')),
        ];
        $result = $this->catalog->search($_GET['q'] ?? '', $filters, $_GET['page'] ?? 1, 18);
        $this->view('vehicles/catalog', [
            'vehicles' => $result['vehicles'],
            'popular' => $this->catalog->getPopularVehicles(6),
            'brands' => $this->catalog->getBrands(),
            'models' => $this->catalog->getModels($filters['brand'] ?: null),
            'engines' => $this->catalog->getEngines($filters['brand'] ?: null, $filters['model'] ?: null),
            'filters' => $filters,
            'query' => trim((string) ($_GET['q'] ?? '')),
            'page' => $result['page'],
            'pages' => $result['pages'],
            'total' => $result['total'],
        ]);
    }

    public function brand($brand)
    {
        $brand = trim((string) $brand);
        $brandRow = $this->catalog->getBrandBySlug($brand);
        $brandName = $brandRow['name_fa'] ?? ($brandRow['name_en'] ?? $brand);
        $vehicles = $brandRow ? $this->catalog->getModelsByBrand($brandRow['slug'] ?: $brand) : $this->catalog->getModelsByBrand($brand);

        if (!$vehicles && !$this->catalog->getVehicle($brand)) {
            http_response_code(404);
            $this->view('vehicles/not-found', ['title' => 'برند خودرو یافت نشد | ' . SITE_NAME, 'message' => 'برند یا دسته خودروی موردنظر در کاتالوگ موجود نیست.']);
            return;
        }

        $this->view('vehicles/brand', ['brand' => $brandName, 'brandSlug' => $brandRow['slug'] ?? $brand, 'vehicles' => $vehicles]);
    }

    public function model($brand, $model, $year = null)
    {
        $vehicle = $this->catalog->getVehicle($brand, $model, $year);
        if (!$vehicle) {
            http_response_code(404);
            $this->view('vehicles/not-found', ['title' => 'خودرو یافت نشد | ' . SITE_NAME, 'message' => 'مدل یا سال خودروی موردنظر در کاتالوگ موجود نیست.']);
            return;
        }
        $products = [];
        try {
            $compat = new \App\Models\ProductCompatibility();
            $products = $compat->getProductsByVehicle((int) $vehicle['id']);
        } catch (\Throwable $e) {
            $products = [];
        }

        $this->view('vehicles/detail', [
            'vehicle' => $vehicle,
            'symptoms' => $this->catalog->getSymptomsForVehicle((int) $vehicle['id']),
            'services' => $this->catalog->getRecommendedServices(),
            'articles' => $this->catalog->getRelatedArticles($vehicle),
            'products' => $products,
        ]);
    }
}
