<?php

namespace App\Models;

class ServiceSubcategory extends Model
{
    public function getAll(): array
    {
        $dataFile = __DIR__ . '/../Data/ServiceSubcategories.php';
        if (!file_exists($dataFile)) {
            return [];
        }

        $data = require $dataFile;
        $items = [];

        foreach ($data as $serviceSlug => $subservices) {
            foreach ((array) $subservices as $subservice) {
                if (empty($subservice['slug'])) {
                    continue;
                }
                if (empty($subservice['is_active'])) {
                    continue;
                }
                $items[] = array_merge(['service_slug' => $serviceSlug], $subservice);
            }
        }

        return $items;
    }

    public function getByService(string $serviceSlug): array
    {
        $serviceSlug = trim((string) $serviceSlug);
        if ($serviceSlug === '') {
            return [];
        }

        $items = $this->getAll();
        return array_values(array_filter($items, static function ($item) use ($serviceSlug) {
            return ($item['service_slug'] ?? '') === $serviceSlug;
        }));
    }

    public function getByServiceAndSlug(string $serviceSlug, string $subSlug): ?array
    {
        $serviceSlug = trim((string) $serviceSlug);
        $subSlug = trim((string) $subSlug);
        if ($serviceSlug === '' || $subSlug === '') {
            return null;
        }

        foreach ($this->getByService($serviceSlug) as $item) {
            if (($item['slug'] ?? '') === $subSlug) {
                return $item;
            }
        }

        return null;
    }

    public function findBySlug(string $serviceSlug, string $subSlug): ?array
    {
        return $this->getByServiceAndSlug($serviceSlug, $subSlug);
    }
}
