<?php

namespace App\Admin\Actions\Tools;

use App\Models\Source;
use App\Services\Sync\SyncWallPaperServices;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class RefreshCategory extends Action
{
    protected $selector = '.refresh-category';

    public function handle(Request $request)
    {
        // $request ...
        $source_list = Source::all();
        $services = new SyncWallPaperServices();
        $info = [];
        foreach ($source_list as $source) {
            array_merge($info, $services->syncCategory($source));
        }
        return $this->response()->success('刷新成功')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定刷新吗？');
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-danger refresh-category">刷新</a>
HTML;
    }
}
