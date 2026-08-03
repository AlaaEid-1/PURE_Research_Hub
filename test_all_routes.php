<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
Illuminate\Support\Facades\Auth::loginUsingId(1);
$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    if (!in_array('GET', $route->methods()) || str_contains($route->uri(), '{')) continue;
    $request = Illuminate\Http\Request::create($route->uri(), 'GET');
    $response = $kernel->handle($request);
    echo $route->uri() . ' -> ' . $response->getStatusCode() . PHP_EOL;
    if ($response->getStatusCode() === 500) {
        $content = strip_tags($response->getContent());
        echo substr($content, 0, 500) . PHP_EOL;
    }
}
