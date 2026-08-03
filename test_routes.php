<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$reqs = ['/research?query=test', '/research?category=1', '/research?permission=free', '/research?sort=most_viewed', '/research?year=2025'];
foreach($reqs as $uri) {
    $request = Illuminate\Http\Request::create($uri, 'GET');
    $response = $kernel->handle($request);
    echo 'URI: ' . $uri . ' -> Status: ' . $response->getStatusCode() . PHP_EOL;
    if ($response->getStatusCode() === 500) {
        $content = strip_tags($response->getContent());
        echo substr($content, 0, 1000) . PHP_EOL;
    }
}
