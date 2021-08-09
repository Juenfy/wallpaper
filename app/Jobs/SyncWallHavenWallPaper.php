<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\WallPaper;
use QL\QueryList;

class SyncWallHavenWallPaper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    const HEADERS = [
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36'
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $data = $this->data->data;
        $cover = $data['cover'];
        $original_url = $data['original_url'];
        $favor = $data['favor'];
        $source_id = $data['source_id'];
        $category_id = $data['category_id'];
        $ql = QueryList::getInstance()->get($original_url, null, [
            'headers' => self::HEADERS
        ]);
        $preview = $ql->find('.scrollbox>img')->attr('src');
        $original_pixel = $ql->find('.showcase-resolution')->text();
        $uploader = $ql->find('.sidebar-section>dl>dd')->eq(0);
        $author_info = [
            'avatar' => str_replace('/32', '/128', $uploader->find('.avatar>img')->attr('src')),
            'name' => $uploader->find('.username')->text()
        ];
        $size = $ql->find('.sidebar-section>dl>dd')->eq(3)->text();
        $view = trim(str_replace(',', '', $ql->find('.sidebar-section>dl>dd')->eq(4)->text()));
        $wallpaper = [
            'source_id' => $source_id,
            'category_id' => $category_id,
            'cover' => $cover,
            'preview' => $preview,
            'original_pixel' => $original_pixel,
            'size' => $size,
            'downloads' => json_encode([
                'original' => $preview
            ]),
            'author_info' => json_encode($author_info),
            'view' => is_numeric($view) ? $view : 0,
            'favor' => $favor ?? 0,
            'original_url' => $original_url,
            'down' => 0
        ];
        var_dump($wallpaper);
        WallPaper::query()->updateOrCreate([
            'source_id' => $source_id,
            'category_id' => $category_id,
            'cover' => $cover
        ], $wallpaper);
        sleep(3);//睡眠3秒防止频繁请求被拦截
    }
}
