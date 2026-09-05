<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

$db = App\Core\Database::connect();
$schema = [];
foreach ($db->query('SHOW TABLES') as $row) {
    $table = array_values($row)[0];
    $schema[$table] = [];
    $stmt = $db->query('SHOW COLUMNS FROM `' . str_replace('`', '``', $table) . '`');
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        $schema[$table][] = $col['Field'];
    }
}

function extractSqlStrings(string $content): array
{
    $sqlStrings = [];
        $regex = '/\b(?:prepare|query|exec|execute)\s*\(\s*(["\'])(.*?)(?<!\\\\)\1/si';
    if (preg_match_all($regex, $content, $matches)) {
        foreach ($matches[2] as $sql) {
            $sqlStrings[] = $sql;
        }
    }
    return array_unique($sqlStrings);
}

$models = glob(__DIR__ . '/../app/Models/*.php');
$issues = [];
foreach ($models as $file) {
    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }
    $sqlStrings = extractSqlStrings($content);
    foreach ($sqlStrings as $sql) {
        $sqlNorm = preg_replace('/\s+/', ' ', trim($sql));
        $lower = strtolower($sqlNorm);
        if (!preg_match('/\b(select|insert|update|delete|replace)\b/', $lower)) {
            continue;
        }

        $tables = [];
        if (preg_match_all('/\bfrom\s+`?([a-zA-Z0-9_]+)`?/i', $sqlNorm, $m)) {
            $tables = array_merge($tables, $m[1]);
        }
        if (preg_match_all('/\bjoin\s+`?([a-zA-Z0-9_]+)`?/i', $sqlNorm, $m)) {
            $tables = array_merge($tables, $m[1]);
        }
        if (preg_match_all('/\b(?:insert|replace)\s+into\s+`?([a-zA-Z0-9_]+)`?/i', $sqlNorm, $m)) {
            $tables = array_merge($tables, $m[1]);
        }
        if (preg_match_all('/\bupdate\s+`?([a-zA-Z0-9_]+)`?/i', $sqlNorm, $m)) {
            $tables = array_merge($tables, $m[1]);
        }
        if (preg_match_all('/\bdelete\s+from\s+`?([a-zA-Z0-9_]+)`?/i', $sqlNorm, $m)) {
            $tables = array_merge($tables, $m[1]);
        }
        $tables = array_unique($tables);
        foreach ($tables as $table) {
            if (!isset($schema[$table])) {
                $issues[] = [
                    'file' => $file,
                    'sql' => $sqlNorm,
                    'problem' => "Missing table: $table",
                ];
            }
        }

        if (preg_match('/\bupdate\s+`?([a-zA-Z0-9_]+)`?\s+set\s+(.*?)(?:\bwhere\b|$)/i', $sqlNorm, $m)) {
            $table = $m[1];
            if (isset($schema[$table])) {
                $assignments = preg_split('/\s*,\s*/', $m[2]);
                foreach ($assignments as $assignment) {
                    if (preg_match('/^`?([a-zA-Z0-9_]+)`?\s*=/', trim($assignment), $colMatch)) {
                        $col = $colMatch[1];
                        if (!in_array($col, $schema[$table], true)) {
                            $issues[] = [
                                'file' => $file,
                                'sql' => $sqlNorm,
                                'problem' => "Missing column: $table.$col",
                            ];
                        }
                    }
                }
            }
        }

        if (preg_match('/\b(?:insert|replace)\s+into\s+`?([a-zA-Z0-9_]+)`?\s*\(([^)]+)\)/i', $sqlNorm, $m)) {
            $table = $m[1];
            if (isset($schema[$table])) {
                $columns = array_map('trim', explode(',', $m[2]));
                foreach ($columns as $col) {
                    $col = trim($col, ' `');
                    if ($col !== '' && !in_array($col, $schema[$table], true)) {
                        $issues[] = [
                            'file' => $file,
                            'sql' => $sqlNorm,
                            'problem' => "Missing column: $table.$col",
                        ];
                    }
                }
            }
        }
    }
}

if (empty($issues)) {
    echo "NO SCHEMA ISSUES FOUND\n";
    exit(0);
}
foreach ($issues as $issue) {
    echo "FILE: " . str_replace(__DIR__ . '/../', '', $issue['file']) . "\n";
    echo "PROBLEM: " . $issue['problem'] . "\n";
    echo "SQL: " . $issue['sql'] . "\n";
    echo "---\n";
}
