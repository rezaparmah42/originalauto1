<?php
// run_verify_inproc.php -- in-process runner that requires the verify script by wrapping it
$scriptPath = __DIR__ . DIRECTORY_SEPARATOR . 'verify_customer_smart_garage.php';
if (!file_exists($scriptPath)) {
    echo "=== GARAGE_VERIFY_START ===\n";
    echo "STDOUT:\n";
    echo "\n";
    echo "STDERR:\n";
    echo "verify script not found: $scriptPath\n";
    echo "EXIT_CODE: 2\n";
    echo "=== GARAGE_VERIFY_END ===\n";
    exit(2);
}
$code = file_get_contents($scriptPath);
// Strip opening <?php and closing ?>
$code = preg_replace('/^\s*<\?php\s*/i', '', $code);
$code = preg_replace('/\?>\s*$/', '', $code);
// Prepare wrapper variables
$wrapper = "<?php\n";
$wrapper .= "function __verify_wrapper() {\n";
$wrapper .= "    $__VERIFIER_EXIT_CODE = null;\n";
$wrapper .= "    $__VERIFIER_EXIT_MESSAGE = null;\n";
$wrapper .= "    try {\n";
// replace exit/die patterns in original code to set $__VERIFIER_EXIT_CODE and return
$transformed = preg_replace_callback('/\b(exit|die)\s*\(\s*([^)]*)\s*\)\s*;/', function($m){
    $arg = trim($m[2]);
    // if numeric literal
    if (preg_match('/^\d+$/', $arg)) {
        return "\$__VERIFIER_EXIT_CODE = (int)$arg; return $__VERIFIER_EXIT_CODE;";
    }
    // if empty
    if ($arg === '') {
        return "\$__VERIFIER_EXIT_CODE = 0; return $__VERIFIER_EXIT_CODE;";
    }
    // else treat as message or expression: set message and code 1
    return "\$__VERIFIER_EXIT_MESSAGE = ($arg); \$__VERIFIER_EXIT_CODE = 1; return $__VERIFIER_EXIT_CODE;";
}, $code);
// Also catch bare exit; or die; (without parentheses)
$transformed = preg_replace('/\bexit\s*\;/i', '\$__VERIFIER_EXIT_CODE = 0; return $__VERIFIER_EXIT_CODE;', $transformed);
$transformed = preg_replace('/\bdie\s*\;/i', '\$__VERIFIER_EXIT_CODE = 1; return $__VERIFIER_EXIT_CODE;', $transformed);
// Indent transformed code
$lines = explode('\n', $transformed);
foreach ($lines as $line) {
    $wrapper .= '        ' . $line . "\n";
}
$wrapper .= "    } catch (Throwable $e) {\n";
$wrapper .= "        echo 'UNCAUGHT_EXCEPTION: ' . $e->getMessage() . '\n';\n";
$wrapper .= "        return 1;\n";
$wrapper .= "    }\n";
$wrapper .= "    if (isset($__VERIFIER_EXIT_CODE) && $__VERIFIER_EXIT_CODE !== null) return $__VERIFIER_EXIT_CODE;\n";
$wrapper .= "    return 0;\n";
$wrapper .= "}\n";
// Evaluate wrapper in isolated scope
$full = $wrapper;
// Change working directory to script dir so relative includes work
chdir(dirname($scriptPath));
// Capture output
ob_start();
$exitCode = 1;
$stderr = '';
try {
    // set error handler to convert warnings/errors to exceptions
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    });
    // evaluate the wrapper
    eval($full);
    // call the wrapper
    $res = __verify_wrapper();
    $exitCode = is_int($res) ? $res : 0;
    restore_error_handler();
} catch (Throwable $t) {
    $stderr = 'Exception: ' . $t->getMessage() . " in " . $t->getFile() . ':' . $t->getLine() . "\n";
    $exitCode = 1;
    restore_error_handler();
}
$stdout = ob_get_clean();
// Print structured output
echo "=== GARAGE_VERIFY_START ===\n";
echo "STDOUT:\n";
echo $stdout . "\n";
echo "STDERR:\n";
echo $stderr . "\n";
echo "EXIT_CODE: " . intval($exitCode) . "\n";
echo "=== GARAGE_VERIFY_END ===\n";
exit(intval($exitCode));
