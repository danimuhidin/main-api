<?php
// Ganti dengan secret token yang akan Anda buat di GitHub
$secret = 'Laleur@123';

// Path ke skrip deployment
$deploy_script = __DIR__ . '/../deploy.sh';

// Ambil signature dari header GitHub
$hub_signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';

// Pisahkan metode hash dan signature
list($hash_method, $signature) = explode('=', $hub_signature, 2);

// Ambil payload dari request
$payload = file_get_contents('php://input');

// Hitung signature dari payload menggunakan secret Anda
$calculated_signature = hash_hmac($hash_method, $payload, $secret);

// Verifikasi apakah signature cocok
if (!hash_equals($signature, $calculated_signature)) {
    // Jika tidak cocok, kirim response error dan hentikan eksekusi
    http_response_code(403);
    die('Forbidden: Invalid signature');
}

// Jika signature cocok, jalankan skrip deployment
// Output akan ditulis ke log untuk debugging
$output = shell_exec("bash $deploy_script 2>&1");
echo "<pre>$output</pre>";

// Log eksekusi (opsional, tapi sangat membantu untuk debugging)
file_put_contents('deploy_log.txt', $output . PHP_EOL, FILE_APPEND);

http_response_code(200);
echo "Deployment successful.";