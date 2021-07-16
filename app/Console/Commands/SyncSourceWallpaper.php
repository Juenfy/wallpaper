<?php

namespace App\Console\Commands;

use App\Models\WallPaperCategory;
use App\Services\Sync\SyncWallPaperServices;
use Illuminate\Console\Command;

class SyncSourceWallpaper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-source-wallpaper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步壁纸源的各类型壁纸';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        set_time_limit(0);
        $services = new SyncWallPaperServices();
        $categories = WallPaperCategory::query()->with('source')->get();
        foreach ($categories as $category) {
            $sync_func = $category->source->sync_func;
            $services->$sync_func($category);
        }
        unset($category);
        $this->info('执行成功');
    }
}
