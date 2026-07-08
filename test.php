<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $rule = new App\Rules\Turnstile();
    $fail = function($msg) { echo "FAIL: $msg\n"; };
    $rule->validate('cf-turnstile-response', 'test-token', $fail);
    echo "TURNSTILE SUCCESS\n";
} catch (\Exception $e) {
    echo "TURNSTILE ERROR: " . $e->getMessage() . "\n";
}
