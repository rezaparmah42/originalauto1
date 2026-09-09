<?php

namespace App\Models;

use PDO;

/**
 * Read-only access to the public vehicle variant catalog.
 */
class VehicleVariant extends Model
{
    public function findBySlug($modelId, $slug)
    {
        $modelId = (int) $modelId;
        $slug = trim((string) $slug);
        if ($modelId <= 0 || $slug === '') {
            return false;
        }

        try {
            $stmt = $this->db->prepare(
                'SELECT vv.*
                 FROM vehicle_variants vv
                 WHERE vv.model_id = ?
                   AND LOWER(vv.slug) = LOWER(?)
                   AND vv.status = 1
                 LIMIT 1'
            );
            $stmt->execute([$modelId, $slug]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (\Throwable $e) {
            // The migration may not have been applied yet; keep public pages usable.
            return false;
        }
    }

    public function getByModelId($modelId): array
    {
        $modelId = (int) $modelId;
        if ($modelId <= 0) {
            return [];
        }

        try {
            $stmt = $this->db->prepare(
                'SELECT vv.*
                 FROM vehicle_variants vv
                 WHERE vv.model_id = ?
                   AND vv.status = 1
                 ORDER BY vv.year_from ASC, vv.year_to ASC, vv.name_fa ASC, vv.id ASC'
            );
            $stmt->execute([$modelId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getActiveByModelId($modelId): array
    {
        return $this->getByModelId($modelId);
    }

    public function findForVehicleRoute($brandSlug, $modelSlug, $variantSlug)
    {
        $brandSlug = trim((string) $brandSlug);
        $modelSlug = trim((string) $modelSlug);
        $variantSlug = trim((string) $variantSlug);
        if ($brandSlug === '' || $modelSlug === '' || $variantSlug === '') {
            return false;
        }

        try {
            $sql = <<<'SQL'
SELECT
    vm.*,
    COALESCE(vb.name_fa, vb.name_en, vb.slug) AS brand,
    vb.name_fa AS brand_name_fa,
    vb.name_en AS brand_name_en,
    vb.slug AS brand_slug,
    COALESCE(vm.name_fa, vm.name_en, vm.slug) AS model,
    vm.slug AS model_slug,
    vv.id AS variant_id,
    vv.model_id AS variant_model_id,
    vv.name_fa AS variant_name_fa,
    vv.name_en AS variant_name_en,
    vv.slug AS variant_slug,
    vv.engine_code AS variant_engine_code,
    vv.engine_type AS variant_engine_type,
    vv.fuel_type AS variant_fuel_type,
    vv.transmission AS variant_transmission,
    vv.year_from AS variant_year_from,
    vv.year_to AS variant_year_to,
    vv.description_fa AS variant_description_fa,
    vv.description_en AS variant_description_en,
    vv.seo_title_fa AS variant_seo_title_fa,
    vv.seo_description_fa AS variant_seo_description_fa,
    vv.search_keywords_fa AS variant_search_keywords_fa,
    vv.status AS variant_status,
    vv.created_at AS variant_created_at,
    vv.updated_at AS variant_updated_at
FROM vehicle_variants vv
INNER JOIN vehicle_models vm ON vm.id = vv.model_id
INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id
WHERE vv.status = 1
  AND vm.status = 1
  AND vb.status = 1
  AND (
      LOWER(vb.slug) = LOWER(?)
      OR LOWER(vb.name_fa) = LOWER(?)
      OR LOWER(COALESCE(vb.name_en, '')) = LOWER(?)
  )
  AND (
      LOWER(vm.slug) = LOWER(?)
      OR LOWER(vm.name_fa) = LOWER(?)
      OR LOWER(COALESCE(vm.name_en, '')) = LOWER(?)
  )
  AND LOWER(vv.slug) = LOWER(?)
LIMIT 1
SQL;

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $brandSlug,
                $brandSlug,
                $brandSlug,
                $modelSlug,
                $modelSlug,
                $modelSlug,
                $variantSlug,
            ]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getYearsByModelId($modelId): array
    {
        $modelId = (int) $modelId;
        if ($modelId <= 0) {
            return [];
        }

        try {
            $stmt = $this->db->prepare(
                'SELECT DISTINCT vv.year_from, vv.year_to
                 FROM vehicle_variants vv
                 WHERE vv.model_id = ?
                   AND vv.status = 1
                 ORDER BY vv.year_from ASC, vv.year_to ASC'
            );
            $stmt->execute([$modelId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getEnginesByModelId($modelId): array
    {
        $modelId = (int) $modelId;
        if ($modelId <= 0) {
            return [];
        }

        try {
            $stmt = $this->db->prepare(
                'SELECT DISTINCT vv.engine_type
                 FROM vehicle_variants vv
                 WHERE vv.model_id = ?
                   AND vv.status = 1
                   AND COALESCE(vv.engine_type, \'\') <> \'\'
                 ORDER BY vv.engine_type ASC'
            );
            $stmt->execute([$modelId]);

            return array_values(array_filter(array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'engine_type')));
        } catch (\Throwable $e) {
            return [];
        }
    }
}
