<?php
require __DIR__ . '/config/config.php';

echo 'httponly=' . ini_get('session.cookie_httponly') . "\n";
echo 'samesite=' . ini_get('session.cookie_samesite') . "\n";
echo 'secure=' . ini_get('session.cookie_secure') . "\n";
