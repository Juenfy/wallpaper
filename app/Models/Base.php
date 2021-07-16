<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    protected $connection = 'wallpaper';
    public $timestamps = true;
    protected $dateFormat = 'U';
    protected $guarded = [];
}
