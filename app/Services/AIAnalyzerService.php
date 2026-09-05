<?php
namespace App\Services;

use App\Models\FaultKnowledge;
use App\Models\Vehicle;

class AIAnalyzerService
{
    protected $knowledgeModel;

    public function __construct()
    {
        $this->knowledgeModel = new FaultKnowledge();
    }

    // Analyze DTCs and return structured result
    public function analyzeDTC(array $codes)
    {
        $codes = array_values(array_unique(array_map('strtoupper', array_filter(array_map('trim', $codes)))));
        $analysis = [];
        $totalSeverityScore = 0;

        foreach ($codes as $code) {
            if ($code === '') continue;
            $k = $this->knowledgeModel->findByCode($code);
            if ($k) {
                $sev = $this->mapSeverity($k['severity']);
                $score = $this->severityScore($sev);
                $totalSeverityScore += $score;
                $analysis[] = [
                    'code' => $code,
                    'title' => $k['title'],
                    'description' => $k['description'],
                    'severity' => $k['severity'],
                    'severity_numeric' => $score,
                    'possible_causes' => $k['possible_causes'],
                    'recommended_actions' => $k['recommended_actions'],
                ];
            } else {
                $analysis[] = [
                    'code' => $code,
                    'title' => 'کد ناشناخته',
                    'description' => 'این کد در دانش محلی ثبت نشده است.',
                    'severity' => 'unknown',
                    'severity_numeric' => 10,
                    'possible_causes' => '',
                    'recommended_actions' => 'اجرای دیاگ و ثبت دانش جدید در پنل ادمین',
                ];
                $totalSeverityScore += 10;
            }
        }

        $healthScore = $this->calculateHealthScore(count($codes), $totalSeverityScore);
        $recommendations = $this->gatherRecommendations($analysis);

        return [
            'health_score' => (int)$healthScore,
            'analysis' => $analysis,
            'recommendations' => $recommendations,
            'summary' => $this->generatePersianSummary($analysis, $healthScore),
        ];
    }

    public function calculateRisk(array $analysis)
    {
        $risk = 0;
        foreach ($analysis as $item) {
            $sev = strtolower($item['severity'] ?? 'unknown');
            if ($sev === 'critical') $risk += 40;
            elseif ($sev === 'high') $risk += 25;
            elseif ($sev === 'medium') $risk += 10;
            else $risk += 2;
        }
        return min(100, $risk);
    }

    public function generateReport($vehicleId, array $analysisResult)
    {
        $vehicle = null;
        try {
            $vModel = new Vehicle();
            $vehicle = $vModel->findById((int)$vehicleId);
        } catch (\Throwable $e) {
            $vehicle = null;
        }

        $analysis = $analysisResult['analysis'] ?? [];
        $health = $analysisResult['health_score'] ?? $this->calculateHealthScore(count($analysis), array_sum(array_column($analysis, 'severity_numeric')));
        $risk = $this->calculateRisk($analysis);

        return [
            'vehicle' => $vehicle,
            'vehicle_id' => (int)$vehicleId,
            'generated_at' => date('Y-m-d H:i:s'),
            'health_score' => (int)$health,
            'risk_level' => $this->riskLabel($risk),
            'analysis' => $analysis,
            'recommendations' => $analysisResult['recommendations'] ?? [],
            'summary' => $analysisResult['summary'] ?? '',
        ];
    }

    protected function gatherRecommendations(array $analysis)
    {
        $out = [];
        foreach ($analysis as $a) {
            if (!empty($a['recommended_actions'])) {
                $out[] = [
                    'code' => $a['code'],
                    'action' => $a['recommended_actions'],
                    'severity' => $a['severity'],
                ];
            }
        }
        return $out;
    }

    protected function mapSeverity($sev)
    {
        $sev = strtolower((string)$sev);
        if (in_array($sev, ['critical','high','medium','low'], true)) return $sev;
        return 'unknown';
    }

    protected function severityScore($severity)
    {
        switch ($severity) {
            case 'critical': return 100;
            case 'high': return 75;
            case 'medium': return 50;
            case 'low': return 25;
            default: return 10;
        }
    }

    protected function calculateHealthScore($countCodes, $totalSeverityScore)
    {
        if ($countCodes <= 0) return 100;
        $avg = $totalSeverityScore / max(1, $countCodes);
        $deduction = min(80, ($avg / 100) * 80);
        $score = max(0, 100 - round($deduction));
        return $score;
    }

    protected function generatePersianSummary(array $analysis, $healthScore)
    {
        $parts = [];
        $parts[] = "امتیاز سلامت خودرو: {$healthScore}%";
        if (empty($analysis)) {
            $parts[] = 'هیچ خطایی شناسایی نشد.';
            return implode("\n", $parts);
        }
        $counts = ['critical'=>0,'high'=>0,'medium'=>0,'low'=>0,'unknown'=>0];
        foreach ($analysis as $a) {
            $k = strtolower($a['severity'] ?? 'unknown');
            if (!isset($counts[$k])) $k = 'unknown';
            $counts[$k]++;
        }
        foreach ($counts as $k => $v) {
            if ($v > 0) {
                $label = $k === 'critical' ? 'حیاتی' : ($k === 'high' ? 'زیاد' : ($k === 'medium' ? 'متوسط' : ($k === 'low' ? 'کم' : 'نامشخص')));
                $parts[] = "تعداد خطاهای {$label}: {$v}";
            }
        }
        $parts[] = 'لطفاً اقدامات پیشنهادی را بررسی کنید و در صورت نیاز به تعمیر فوری مراجعه نمایید.';
        return implode("\n", $parts);
    }

    protected function riskLabel($risk)
    {
        if ($risk >= 70) return 'High';
        if ($risk >= 40) return 'Medium';
        return 'Low';
    }
}
