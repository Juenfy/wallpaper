<?php


namespace App\Models;


class WallPaperCategory extends Base
{
    protected $table = 'wallpaper_category';

    public function source()
    {
        return $this->hasOne(Source::class, 'id', 'source_id');
    }
}
