<?php

namespace App\Models;

use PDO;

class RepairPart extends Model
{
    private function hasColumn(string $column): bool
    {
        static $columns = null;

        if ($columns === null) {
            $columns = [];
            try {
                $stmt = $this->db->query('SHOW COLUMNS FROM repair_parts');
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $columns[$row['Field']] = true;
                }
            } catch (\Throwable $e) {
                $columns = [];
            }
        }

        return isset($columns[$column]);
    }

    public function getByRepairId($repairId)
    {
        if ($this->hasColumn('product_id')) {
            $stmt = $this->db->prepare('SELECT rp.*, p.title_fa, p.title_en, p.stock FROM repair_parts rp LEFT JOIN products p ON p.id = rp.product_id WHERE rp.repair_id = ? ORDER BY rp.id ASC');
            $stmt->execute([(int) $repairId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $stmt = $this->db->prepare('SELECT rp.*, rp.part_name AS title_fa, rp.part_name AS title_en, rp.quantity AS stock FROM repair_parts rp WHERE rp.repair_id = ? ORDER BY rp.id ASC');
        $stmt->execute([(int) $repairId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPart($repairId, $productId, $quantity, $notes = '', $createdBy = null)
    {
        $repairId = (int) $repairId;
        $productId = (int) $productId;
        $quantity = max(1, (int) $quantity);

        if ($repairId <= 0) {
            return false;
        }

        $partName = trim((string) $notes);
        $hasProductColumn = $this->hasColumn('product_id');
        $existingSql = 'SELECT id, quantity FROM repair_parts WHERE repair_id = ?';
        $existingParams = [$repairId];
        if ($hasProductColumn && $productId > 0) {
            $existingSql .= ' AND product_id = ?';
            $existingParams[] = $productId;
        } elseif ($partName !== '') {
            $existingSql .= ' AND part_name = ?';
            $existingParams[] = $partName;
        }
        $existingSql .= ' LIMIT 1';

        $existing = $this->db->prepare($existingSql);
        $existing->execute($existingParams);
        $row = $existing->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $newQuantity = (int) $row['quantity'] + $quantity;

            $updateParts = ['quantity = ?'];
            $updateParams = [$newQuantity];

            if ($this->hasColumn('part_name')) {
                $updateParts[] = 'part_name = ?';
                $updateParams[] = $partName !== '' ? $partName : null;
            }

            if ($this->hasColumn('unit_price')) {
                $updateParts[] = 'unit_price = COALESCE(unit_price, 0)';
            }

            if ($this->hasColumn('updated_at')) {
                $updateParts[] = 'updated_at = ?';
                $updateParams[] = date('Y-m-d H:i:s');
            }

            $updateSql = 'UPDATE repair_parts SET ' . implode(', ', $updateParts) . ' WHERE id = ?';
            $updateParams[] = (int) $row['id'];

            $update = $this->db->prepare($updateSql);
            $ok = $update->execute($updateParams);
            if ($ok && $productId > 0) {
                $productModel = new Product();
                $productModel->decreaseStock($productId, $quantity, 'Repair usage #' . $repairId . ($notes !== '' ? ' - ' . $notes : ''));
            }
            return $ok ? (int) $row['id'] : false;
        }

        $insertColumns = ['repair_id'];
        $insertValues = ['?'];
        $insertParams = [$repairId];

        if ($this->hasColumn('part_name')) {
            $insertColumns[] = 'part_name';
            $insertValues[] = '?';
            $insertParams[] = $partName !== '' ? $partName : 'Part';
        }

        if ($this->hasColumn('product_id') && $productId > 0) {
            $insertColumns[] = 'product_id';
            $insertValues[] = '?';
            $insertParams[] = $productId;
        }

        if ($this->hasColumn('quantity')) {
            $insertColumns[] = 'quantity';
            $insertValues[] = '?';
            $insertParams[] = $quantity;
        }

        if ($this->hasColumn('unit_price')) {
            $insertColumns[] = 'unit_price';
            $insertValues[] = '?';
            $insertParams[] = 0.00;
        }

        if ($this->hasColumn('total_price')) {
            $insertColumns[] = 'total_price';
            $insertValues[] = '?';
            $insertParams[] = 0.00;
        }

        if ($this->hasColumn('created_at')) {
            $insertColumns[] = 'created_at';
            $insertValues[] = '?';
            $insertParams[] = date('Y-m-d H:i:s');
        }

        $stmt = $this->db->prepare('INSERT INTO repair_parts (' . implode(', ', $insertColumns) . ') VALUES (' . implode(', ', $insertValues) . ')');
        $ok = $stmt->execute($insertParams);
        if (!$ok) {
            return false;
        }

        if ($productId > 0) {
            $productModel = new Product();
            $productModel->decreaseStock($productId, $quantity, 'Repair usage #' . $repairId . ($notes !== '' ? ' - ' . $notes : ''));
        }

        return (int) $this->db->lastInsertId();
    }

    public function removePart($repairId, $productId)
    {
        $repairId = (int) $repairId;
        $productId = (int) $productId;

        $hasProductColumn = $this->hasColumn('product_id');
        $sql = 'SELECT * FROM repair_parts WHERE repair_id = ?';
        $params = [$repairId];
        if ($hasProductColumn && $productId > 0) {
            $sql .= ' AND product_id = ?';
            $params[] = $productId;
        }
        $sql .= ' LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }

        if ($productId > 0) {
            $productModel = new Product();
            $reverseQty = (int) ($row['quantity'] ?? 0);
            if ($reverseQty > 0) {
                $productModel->increaseStock($productId, $reverseQty, 'Repair part reversal #' . $repairId);
            }
        }

        $delete = $this->db->prepare('DELETE FROM repair_parts WHERE id = ?');
        return $delete->execute([(int) $row['id']]);
    }
}
