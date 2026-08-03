<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
// Login as user ID 1
Auth::loginUsingId(1);

$request = Illuminate\Http\Request::create('/dashboard/research/create', 'GET');
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . PHP_EOL;

$content = $response->getContent();
if (strpos($content, '<option value="') !== false) {
    echo "Options found.\n";
    // extract all options
    preg_match_all('/<option value="([^"]*)">([^<]*)<\/option>/', $content, $matches);
    for($i=0; $i<count($matches[0]); $i++) {
        echo "Option: " . $matches[1][$i] . " -> " . trim($matches[2][$i]) . "\n";
    }
} else {
    echo "No options found.\n";
    echo substr($content, 0, 1000);
}
