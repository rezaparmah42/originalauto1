<?php
/**
 * Partial to render a JSON-LD schema block safely.
 * Usage: set $schemaData = [...]; require __DIR__ . '/partials/schema.php';
 */
if (!empty($schemaData) && is_array($schemaData)) {
    echo "<script type=\"application/ld+json\">" . json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</script>\n";
}
?>
