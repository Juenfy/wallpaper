<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Tools\RefreshCategory;
use App\Models\Source;
use App\Models\WallPaperCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WallPaperCategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'WallPaperCategory';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WallPaperCategory());

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new RefreshCategory());
        });
        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->select('source_id')->options(Source::selectOptions());
            $create->text('name');
            $create->text('url');

        });
        $grid->column('id', __('Id'));
        $grid->column('source_id', __('Source id'));
        $grid->column('name', __('Name'));
        $grid->column('url', __('Url'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WallPaperCategory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('source_id', __('Source id'));
        $show->field('name', __('Name'));
        $show->field('url', __('Url'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WallPaperCategory());

        $form->number('source_id', __('Source id'));
        $form->text('name', __('Name'));
        $form->url('url', __('Url'));

        return $form;
    }
}
