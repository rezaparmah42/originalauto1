<?php

namespace App\Models;

use PDO;

class DiagnosticAI extends Model
{
    public function analyzeCode($code)
    {
        $stmt = $this->db->prepare('SELECT * FROM diagnostic_knowledge WHERE dtc_code = ? LIMIT 1');
        $stmt->execute([trim((string)$code)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getKnowledgeCount()
    {
        try {
            $stmt = $this->db->query('SELECT COUNT(*) FROM diagnostic_knowledge');
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function analyzeVehicle($vehicleId)
    {
        // aggregate diagnostic history, maintenance and repair history
        $vehicleModel = new Vehicle();
        $history = $vehicleModel->getDiagnosticHistory($vehicleId);
        $repairs = $vehicleModel->getVehicleHistory($vehicleId);

        // basic aggregation
        $analysis = [
            'vehicle_id' => (int)$vehicleId,
            'issues' => [],
            'summary' => '',
        ];

        foreach ($history as $item) {
            $code = $item['code'] ?? null;
            if (!$code) continue;
            $knowledge = $this->analyzeCode($code);
            if ($knowledge) {
                $analysis['issues'][] = $knowledge;
            }
        }

        $analysis['summary'] = 'تحلیل اولیه بر اساس خطاها و سابقه تعمیرات.';
        return $analysis;
    }

    public function getRecommendations($diagnosticId)
    {
        $stmt = $this->db->prepare('SELECT * FROM repair_recommendations WHERE diagnostic_id = ? ORDER BY priority DESC');
        $stmt->execute([(int)$diagnosticId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculateHealthScore($vehicleId)
    {
        $vehicleModel = new Vehicle();
        return $vehicleModel->getHealthScore((int)$vehicleId);
    }
}
