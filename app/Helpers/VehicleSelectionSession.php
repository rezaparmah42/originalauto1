<?php

namespace App\Helpers;

class VehicleSelectionSession
{
    public static function getSelectedVehicle(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $vehicle = $_SESSION['selected_vehicle'] ?? [];
        return self::normalizeSelection($vehicle);
    }

    public static function setSelectedVehicle(array $vehicle): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $normalized = self::normalizeSelection($vehicle);

        if (empty($normalized)) {
            unset($_SESSION['selected_vehicle']);
            return [];
        }

        $_SESSION['selected_vehicle'] = $normalized;
        return $normalized;
    }

    public static function clearSelectedVehicle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['selected_vehicle']);
    }

    private static function normalizeSelection(array $vehicle): array
    {
        $brandId = isset($vehicle['vehicle_brand_id']) ? (int) $vehicle['vehicle_brand_id'] : 0;
        $modelId = isset($vehicle['vehicle_model_id']) ? (int) $vehicle['vehicle_model_id'] : 0;
        $year = trim((string) ($vehicle['vehicle_year'] ?? ''));

        if ($year !== '' && !ctype_digit($year)) {
            $year = '';
        }

        if ($modelId <= 0 && $brandId <= 0 && $year === '') {
            return [];
        }

        if ($modelId > 0 && $brandId <= 0) {
            $brandId = self::getBrandIdByModelId($modelId);
        }

        if ($brandId > 0 && $modelId <= 0) {
            $modelId = self::getFirstModelIdForBrand($brandId);
        }

        if ($brandId <= 0 && $modelId <= 0) {
            return [];
        }

        $brandName = self::getBrandName($brandId);
        $modelName = self::getModelName($modelId);

        return [
            'vehicle_brand_id' => $brandId,
            'vehicle_model_id' => $modelId,
            'vehicle_year' => $year,
            'brand_name' => $brandName,
            'model_name' => $modelName,
        ];
    }

    private static function getBrandIdByModelId(int $modelId): int
    {
        try {
            $db = \App\Core\Database::connect();
            $stmt = $db->prepare('SELECT brand_id FROM vehicle_models WHERE id = ? LIMIT 1');
            $stmt->execute([$modelId]);
            $row = $stmt->fetch();
            return $row && !empty($row['brand_id']) ? (int) $row['brand_id'] : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private static function getFirstModelIdForBrand(int $brandId): int
    {
        try {
            $db = \App\Core\Database::connect();
            $stmt = $db->prepare('SELECT id FROM vehicle_models WHERE status = 1 AND brand_id = ? ORDER BY name_fa ASC, name_en ASC LIMIT 1');
            $stmt->execute([$brandId]);
            $row = $stmt->fetch();
            return $row && !empty($row['id']) ? (int) $row['id'] : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private static function getBrandName(int $brandId): string
    {
        if ($brandId <= 0) {
            return '';
        }

        try {
            $db = \App\Core\Database::connect();
            $stmt = $db->prepare('SELECT name_fa, name_en, name FROM vehicle_brands WHERE id = ? LIMIT 1');
            $stmt->execute([$brandId]);
            $row = $stmt->fetch();
            if (!$row) {
                return '';
            }
            return trim((string) ($row['name_fa'] ?? $row['name_en'] ?? $row['name'] ?? ''));
        } catch (\Throwable $e) {
            return '';
        }
    }

    private static function getModelName(int $modelId): string
    {
        if ($modelId <= 0) {
            return '';
        }

        try {
            $db = \App\Core\Database::connect();
            $stmt = $db->prepare('SELECT name_fa, name_en, slug FROM vehicle_models WHERE id = ? LIMIT 1');
            $stmt->execute([$modelId]);
            $row = $stmt->fetch();
            if (!$row) {
                return '';
            }
            return trim((string) ($row['name_fa'] ?? $row['name_en'] ?? $row['slug'] ?? ''));
        } catch (\Throwable $e) {
            return '';
        }
    }
}
