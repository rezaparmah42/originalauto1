<?php

require_once __DIR__ . '/../app/Services/VehicleKnowledgeImportPlanner.php';

use App\Services\VehicleKnowledgeImportPlanner;

$planner = new VehicleKnowledgeImportPlanner();
$rootCandidates = [
    __DIR__ . '/../ai_sources',
    __DIR__ . '/../ai_sources/vehicle_knowledge',
    __DIR__ . '/ai_sources',
    __DIR__ . '/ai_sources/vehicle_knowledge',
];

$sourceRoot = null;
foreach ($rootCandidates as $candidate) {
    if (is_dir($candidate)) {
        $sourceRoot = $candidate;
        break;
    }
}

if ($sourceRoot === null) {
    echo "No ai_sources directory found. Add PDFs to ai_sources/ or ai_sources/vehicle_knowledge/ before running the import pipeline.\n";
    exit(0);
}

$plan = $planner->buildPlan($sourceRoot);
$output = __DIR__ . '/../storage/vehicle_knowledge_import_plan.json';
if (!is_dir(dirname($output))) {
    mkdir(dirname($output), 0777, true);
}
file_put_contents($output, json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo "Source root: {$sourceRoot}\n";
echo "PDF documents: {$plan['pdf_count']}\n";
echo "Target pages: {$plan['page_target']}\n";
echo "Brands: " . implode(', ', $plan['summary']['brands']) . "\n";
echo "Models: " . implode(', ', $plan['summary']['models']) . "\n";
echo "Service categories: " . implode(', ', $plan['summary']['service_categories']) . "\n";
echo "Knowledge categories: " . implode(', ', $plan['summary']['knowledge_categories']) . "\n";
echo "Plan file: {$output}\n";
