<?php
namespace App\Interfaces\Sync;

interface SyncInterface
{
    const HEADERS = [
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36'
    ];

    public function syncCategory($source);

    public function syncWallHaven($category);

    public function syncUnsplash($category);

    public function syncWallPapersHome($category);
}
