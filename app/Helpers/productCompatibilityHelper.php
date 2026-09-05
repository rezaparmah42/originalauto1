<?php

namespace App\Helpers;

use App\Models\ProductCompatibility;

class ProductCompatibilityHelper
{
    public static function getCompatibleProducts($brandId, $modelId = null, $year = null, $engineType = null)
    {
        if (empty($brandId) && empty($modelId)) {
            return [];
        }

        try {
            $db = \App\Core\Database::connect();
            $sql = 'SELECT pc.*, p.id AS product_id, p.title_fa, p.title_en, p.slug, p.price, p.stock
                    FROM product_compatibility pc
                    INNER JOIN products p ON p.id = pc.product_id
                    WHERE 1=1';
            $params = [];

            if ($modelId) {
                $sql .= ' AND pc.model_id = ?';
                $params[] = (int) $modelId;
            }

            if ($brandId) {
                $sql .= ' AND EXISTS (SELECT 1 FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.id = pc.model_id AND vb.id = ?)';
                $params[] = (int) $brandId;
            }

            if ($year !== null && $year !== '') {
                $sql .= ' AND (pc.year IS NULL OR pc.year = ? OR pc.year = ? )';
                $params[] = trim((string) $year);
                $params[] = trim((string) $year);
            }

            if ($engineType !== null && $engineType !== '') {
                $sql .= ' AND (pc.engine_type IS NULL OR pc.engine_type = ?)';
                $params[] = trim((string) $engineType);
            }

            $sql .= ' ORDER BY p.created_at DESC';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }
    }
}