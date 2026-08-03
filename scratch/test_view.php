<?php

use App\Enums\DownloadPermission;
use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    $research = new Research([
        'title' => 'Test Paper',
        'slug' => 'test-paper',
        'abstract' => 'Abstract text',
        'status' => ResearchStatus::PUBLISHED,
        'download_permission' => DownloadPermission::FREE,
    ]);
    $research->user = new User(['name' => 'John Doe']);
    $research->category = new ResearchCategory(['name' => 'AI', 'slug' => 'ai']);

    echo view('research.show', compact('research'))->render();
    echo "SUCCESS\n";
} catch (Throwable $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    echo 'FILE: '.$e->getFile().':'.$e->getLine()."\n";
    echo $e->getTraceAsString()."\n";
}
