<?php

namespace App\Jobs;

use App\Models\WallPaper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use QL\QueryList;

class SyncUnsplashWallPaper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    public $tries = 1;

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
        $category_id = $data['category_id'];
        $source_id = $data['source_id'];
        $original_url = $data['links']['html'];
        $cover = $data['urls']['thumb'];
        $favor = $data['likes'];
        $original_pixel = "{$data['width']} x {$data['height']}";
        $author_info = [
            'avatar' => $data['user']['profile_image']['large'],
            'name' => $data['user']['name']
        ];
        var_dump($original_url);
        $ql = QueryList::getInstance()->get($original_url, null, [
            'headers' => self::HEADERS
        ]);
        $view = str_replace(',', '', $ql->find('._2dX3B>div')->eq(0)->children('span')->text());
        $down = str_replace(',', '', $ql->find('._2dX3B>div')->eq(1)->children('span')->text());
        $preview = array_shift($data['urls']);//取第一张
        array_pop($data['urls']);
        $headers = get_headers($data['links']['download']);
        $location = str_replace('Location: ', '', $headers[8]);
        $data['urls']['original'] = $location;
        $headers = get_headers($location);
        $bytes = str_replace('Content-Length: ', '', $headers[2]);
        $size = $bytes / pow(1024, 2);
        $size = $size >= 1 ? number_format($size, 1) . ' MiB' : number_format($size * 1000, 1) . ' KiB';
        $wallpaper = [
            'source_id' => $source_id,
            'category_id' => $category_id,
            'cover' => $cover,
            'preview' => $preview,
            'original_pixel' => $original_pixel,
            'size' => $size,
            'downloads' => json_encode($data['urls']),
            'author_info' => json_encode($author_info),
            'view' => empty(trim($view)) ? 0 : $view,
            'favor' => $favor ?? 0,
            'original_url' => $original_url,
            'down' => empty(trim($down)) ? 0 : $down
        ];
        var_dump($wallpaper);
        WallPaper::query()->updateOrCreate([
            'source_id' => $source_id,
            'category_id' => $category_id,
            'cover' => $cover
        ], $wallpaper);
    }
}
