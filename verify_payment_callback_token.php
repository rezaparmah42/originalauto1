<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';
require __DIR__ . '/app/Models/Model.php';
require __DIR__ . '/app/Models/Payment.php';

$m = new App\Models\Payment();
$payment = $m->findByOrderId(1);
if (!$payment) {
    echo "NO_PAYMENT_ROW_FOUND\n";
    exit(0);
}

$token = $m->buildCallbackToken(
    (int) $payment['order_id'],
    (int) $payment['id'],
    (int) $payment['user_id'],
    (float) $payment['amount']
);

$ok = $m->verifyCallbackToken(
    (int) $payment['order_id'],
    (int) $payment['id'],
    (int) $payment['user_id'],
    (float) $payment['amount'],
    $token
);

$tamperRejected = !$m->verifyCallbackToken(
    (int) $payment['order_id'],
    (int) $payment['id'],
    (int) $payment['user_id'],
    (float) $payment['amount'],
    'tampered'
);

echo "PAYMENT_VERIFIED=" . ($ok ? 'true' : 'false') . PHP_EOL;
echo "TAMPER_REJECTED=" . ($tamperRejected ? 'true' : 'false') . PHP_EOL;
