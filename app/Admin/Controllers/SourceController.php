<?php

namespace App\Admin\Controllers;

use App\Models\Source;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\Consume\ImportPlanConsume;

class SourceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Source';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Source());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('selector', __('Selector'));
        $grid->column('find_url', __('Find url'));
        $grid->column('find_name', __('Find name'));
        $grid->column('url', __('Url'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ImportPlanConsume());
        });

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
        $show = new Show(Source::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('selector', __('Selector'));
        $show->field('find_url', __('Find url'));
        $show->field('find_name', __('Find name'));
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
        $form = new Form(new Source());

        $form->text('name', __('Name'))->rules('required');
        $form->text('selector', __('Selector'))->rules('required');
        $form->text('find_url', __('Find url'))->rules('required');
        $form->text('find_name', __('Find name'))->rules('required');
        $form->url('url', __('Url'))->rules('required');
        $form->text('sync_func', __('Sync func'))->rules('required')->help('这是同步源网站壁纸的方法名');
        return $form;
    }
}
