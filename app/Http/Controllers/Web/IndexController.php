<?php

namespace App\Http\Controllers\Web;

use App\Models\Source;
use App\Models\WallPaper;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;

class IndexController extends Controller
{
    public function index()
    {
        $sources = Source::getSources();
        $selected_sid = Cookie::get(Source::SELECTED_SID_COOKIE_KEY, 0);
        return view('web.index', [
            'sources' => $sources,
            'selected_sid' => $selected_sid
        ]);
    }

    public function getWallPaperList()
    {

        $page = request()->get('page', 1);
        $per_page = request()->get('per_page', 24);
        $selected_sid = request()->cookie(Source::SELECTED_SID_COOKIE_KEY, 0);

        $query = WallPaper::query();

        if ($selected_sid) {
            $query->where('source_id', $selected_sid);
        }

        $count = $query->count();

        $wallpapers = $query->forPage($page, $per_page)->get();

        return response()->json([
            'code' => 0,
            'data' => [
                'wallpapers' => $wallpapers,
                'total_page' => ceil($count / $per_page)
            ]
        ]);
    }

    public function changeSource()
    {
        $source_id = request()->post('source_id', 0);

        return response()->json([
            'code' => 0,
            'message' => '切换成功'
        ])->cookie(Source::SELECTED_SID_COOKIE_KEY, $source_id);
    }
}
