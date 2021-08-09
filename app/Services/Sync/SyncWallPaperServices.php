<?php

namespace App\Services\Sync;

use App\Jobs\SyncUnsplashWallPaper;
use App\Jobs\SyncWallHavenWallPaper;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use QL\QueryList;
use App\Models\WallPaperCategory;
use App\Interfaces\Sync\SyncInterface;
use Illuminate\Support\Facades\Log;

class SyncWallPaperServices implements SyncInterface
{

    protected $redis;
    protected $curl;
    const WALLPAPER_CATEGORY_PAGE_KEY = 'wallpaper_category_page:';

    const UNSPLASH_API = 'https://unsplash.com/';
    const UNSPLASH_PER_PAGE = 12;

    public function __construct()
    {
        $this->redis = Redis::connection();
        $this->curl = new Client();
    }

    /**
     * @param $source
     * @return array
     * 同步源壁纸的壁纸类型
     */
    public function syncCategory($source)
    {
        // TODO: Implement sync() method.
        $source_url = $source->url;
        $source_id = $source->id;
        $selector = $source->selector;
        $find_name = $source->find_name;
        $find_url = $source->find_url;
        $query_list = QueryList::getInstance()->get($source_url, null, [
            'headers' => self::HEADERS
        ])->find($selector)->map(function ($query) use ($find_name, $find_url, $source_url) {
            $find_name = explode('@', $find_name);
            $find_url = explode('@', $find_url);
            $name_tag = $find_name[0];
            $name_attr = $find_name[1];
            $url_tag = $find_url[0];
            $url_attr = $find_url[1];
            if ($name_attr === 'text' || $name_attr === 'html')
                $name = $query->find($name_tag)->$name_attr();
            else
                $name = $query->find($name_tag)->attr($name_attr);
            if ($url_attr === 'text' || $url_attr === 'html')
                $url = $query->find($url_tag)->$url_attr();
            else
                $url = $query->find($url_tag)->attr($url_attr);
            if (!$name) {
                $name_info = explode('/', $url);
                $name = strtoupper(end($name_info));
            }
            if (!strstr($url, 'http')) {
                $url = $source_url . $url;
            }
            return [
                'name' => $name,
                'url' => $url,
            ];
        });
        Log::info("针对源:{$source_url}获取到的分类数据:" . json_encode($query_list));
        if ($query_list) {
            foreach ($query_list as $query) {
                $query['source_id'] = $source_id;
                WallPaperCategory::query()->updateOrCreate([
                    'source_id' => $source_id,
                    'name' => $query['name']
                ], $query);
            }
        }
        return (array)$query_list;
    }

    /**
     * @param $category
     * 同步WallHaven网站的壁纸
     */
    public function syncWallHaven($category)
    {
        // TODO: Implement syncWallHaven() method.
        $category_id = $category['id'];
        $source_id = $category['source_id'];
        $page = $this->redis->get(self::WALLPAPER_CATEGORY_PAGE_KEY . $category_id);
        $page = max(($page ?? 0), 0) + 1;
        $url = $category['url'] . "?page=$page";
        QueryList::getInstance()->get($url, null, [
            'headers' => self::HEADERS
        ])->find('.thumb-listing-page>ul>li')->map(function ($query) use ($source_id, $category_id) {
            $cover = $query->find('figure>img')->attr('data-src');
            $original_url = $query->find('figure>a')->attr('href');
            $favor = trim($query->find('figure>.thumb-info>.wall-favs')->text());
            $data = [
                'cover' => $cover,
                'original_url' => $original_url,
                'favor' => is_numeric($favor) ? $favor : 0,
                'source_id' => $source_id,
                'category_id' => $category_id
            ];
            //将任务分发到队列里，延时3秒执行，防止频繁请求报错
            SyncWallHavenWallPaper::dispatch(new SyncWallHavenWallPaper($data));
        });
        $this->redis->set(self::WALLPAPER_CATEGORY_PAGE_KEY . $category_id, $page);
    }

    /**
     * @param $category
     * @throws \GuzzleHttp\Exception\GuzzleException
     * 同步Unsplash网站的壁纸
     */
    public function syncUnsplash($category)
    {
        // TODO: Implement syncUnsplash() method.
        $explode_url = explode('/', $category['url']);
        $category_id = $category['id'];
        $source_id = $category['source_id'];
        $key = end($explode_url);
        $action = $key ? ('napi/topics/' . $key . '/photos') : 'napi/photos';
        $page = $this->redis->get(self::WALLPAPER_CATEGORY_PAGE_KEY . $category_id);
        $page = max(($page ?? 0), 0) + 1;
        $url = self::UNSPLASH_API . $action . '?per_page=' . self::UNSPLASH_PER_PAGE . '&page=' . $page;
        $curl_list = $this->curl->get($url, [])->getBody()->getContents();
        $data_list = json_decode($curl_list, true);
        foreach ($data_list as $data) {
            $data['source_id'] = $source_id;
            $data['category_id'] = $category_id;
            //将任务分发到队列里，延时3秒执行，防止频繁请求报错
            SyncUnsplashWallPaper::dispatch(new SyncUnsplashWallPaper($data));
        }
        $this->redis->set(self::WALLPAPER_CATEGORY_PAGE_KEY . $category_id, $page);
    }

    public function syncWallPapersHome($category)
    {
        // TODO: Implement syncWallPapersHome() method.
    }
}
