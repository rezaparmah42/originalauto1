<?php
ob_start();
require __DIR__ . '/tools/schema_verify.php';
$output = ob_get_clean();
file_put_contents(__DIR__ . '/tmp_schema_verify_output.txt', $output);
echo $output;
