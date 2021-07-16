<?php

namespace App\Console\Commands;

use App\Models\Source;
use Illuminate\Console\Command;
use App\Services\Sync\SyncWallPaperServices;

class syncWallPaperCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-wallpaper-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步壁纸源的壁纸类型';

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
        $source_list = Source::all();
        $services = new SyncWallPaperServices();
        $info = [];
        foreach ($source_list as $source) {
            array_merge($info, $services->syncCategory($source));
        }
        $this->info('同步壁纸源的壁纸类型执行成功，同步数据:' . json_encode($info));
    }
}
