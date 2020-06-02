<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Project;
use App\Models\Platform;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Controllers\AdminController;

class ProjectController extends AdminController
{
    protected $title = '项目列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(\App\Models\Project::orderBy('created_at', 'desc'), function (Grid $grid) {

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

                $platform = Platform::all();
                $data = [];
                foreach ($platform as $item) {
                    $data[$item->id] = $item->name;
                }
                $filter->equal('platform_id')->select($data);

            });


            $grid->id->sortable();
            $grid->icon->display(function ($icon) {
                return "<img style='max-width: 30px;' src='/uploads/{$icon}'/>";
            });
            $grid->platform_id->display(function ($platform) {
                return Platform::find($platform)->name;
            });
            $grid->number;
            $grid->name;
            $grid->price;
            $grid->description;

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Project(), function (Show $show) {
            $show->id;
            $show->icon;
            $show->number;
            $show->name;
            $show->price;
            $show->description;
            $show->created_at;
            $show->updated_at;
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Project(), function (Form $form) {
            $form->display('id');

            $platform = Platform::all();
            $data = [];
            foreach ($platform as $item) {
                $data[$item->id] = $item->name;
            }

            $form->select('platform_id', '平台')->options($data);
            $form->image('icon', '图标')->rules('required');
            $form->text('number', '编号')->rules('required');
            $form->text('name', '项目名称')->rules('required');
            $form->decimal('price', '金额')->rules('required');
            $form->editor('description', '描述');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
