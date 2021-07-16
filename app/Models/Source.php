<?php


namespace App\Models;


use Illuminate\Support\Facades\Cache;

class Source extends Base
{
    protected $table = 'source';

    const SOURCES_CACHE_KEY = 'SOURCE_CACHE_KEY';

    const SOURCES_CACHE_TTL = 500;

    const SELECTED_SID_COOKIE_KEY = 'SELECTED_SOURCE_ID';

    /**
     * @return mixed
     * 获取壁纸源
     */
    public static function getSources()
    {
        return Cache::remember(static::SOURCES_CACHE_KEY, static::SOURCES_CACHE_TTL, function () {
            return static::all();
        });
    }
}
