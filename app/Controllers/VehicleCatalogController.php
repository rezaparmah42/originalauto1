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
        $vehicles = $this->attachVariants($result['vehicles'] ?? []);
        $popular = $this->attachVariants($this->catalog->getPopularVehicles(6));
        $this->view('vehicles/catalog', [
            'vehicles' => $vehicles,
            'popular' => $popular,
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
        $brandSlug = $brandRow['slug'] ?? $brand;
        $vehicles = $brandRow ? $this->catalog->getModelsByBrand($brandSlug) : $this->catalog->getModelsByBrand($brand);
        $vehicles = $this->attachVariants($vehicles);

        if (!$brandRow && !$this->catalog->getVehicle($brand)) {
            http_response_code(404);
            $this->view('vehicles/not-found', ['title' => 'برند خودرو یافت نشد | ' . SITE_NAME, 'message' => 'برند یا دسته خودروی موردنظر در کاتالوگ موجود نیست.']);
            return;
        }

        $this->view('vehicles/brand', ['brand' => $brandName, 'brandSlug' => $brandSlug, 'vehicles' => $vehicles]);
    }

    public function model($brand, $model, $variant = null)
    {
        $brand = trim((string) $brand);
        $model = trim((string) $model);
        $requestedVariant = trim((string) $variant);
        $variantRecord = null;
        $legacyYear = null;
        $variantRouteMiss = false;

        // The three-segment public route is variant-first. Numeric segments
        // retain the old year behavior only when no matching variant exists.
        if ($requestedVariant !== '') {
            $variantRecord = $this->catalog->findVehicleVariant($brand, $model, $requestedVariant);
            if (!$variantRecord && ctype_digit($requestedVariant)) {
                $legacyYear = $requestedVariant;
            } elseif (!$variantRecord) {
                $variantRouteMiss = true;
            }
        }

        $vehicle = $variantRecord;
        if (!$vehicle && !$variantRouteMiss) {
            $vehicle = $this->catalog->getVehicle($brand, $model, $legacyYear);
            if (!$vehicle) {
                $brandRow = $this->catalog->getBrandBySlug($brand);
                if ($brandRow && $model !== '') {
                    $vehicle = $this->catalog->getVehicle($brandRow['slug'] ?? $brand, $model, $legacyYear);
                }
            }
        }

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

        $generatedModel = $this->catalog->getGeneratedModelData($vehicle);

        $this->view('vehicles/detail', [
            'vehicle' => $vehicle,
            'variant' => $variantRecord['variant'] ?? null,
            'symptoms' => $this->catalog->getSymptomsForVehicle((int) $vehicle['id']),
            'services' => $this->catalog->getRecommendedServices(),
            'serviceLinks' => $this->catalog->getVehicleServiceMatrixLinks($vehicle, 6),
            'articles' => $this->catalog->getRelatedArticles($vehicle),
            'products' => $products,
            'generatedModel' => $generatedModel,
            'vehicleFaq' => $generatedModel['faqSchema'] ?? [],
        ]);
    }

    private function attachVariants($vehicles): array
    {
        if (!is_array($vehicles)) {
            return [];
        }

        foreach ($vehicles as &$vehicle) {
            if (!is_array($vehicle)) {
                continue;
            }

            $modelId = (int) ($vehicle['id'] ?? $vehicle['model_id'] ?? 0);
            $vehicle['variants'] = $modelId > 0
                ? $this->catalog->getActiveVariantsByModelId($modelId)
                : [];
        }
        unset($vehicle);

        return $vehicles;
    }
}
