<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Country;
use App\Models\Platform;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Controllers\AdminController;

class CountryController extends AdminController
{
    protected $title = '国家列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(\App\Models\Country::orderBy('created_at','desc'), function (Grid $grid) {

            $grid->filter(function ($filter) {
                // 展开过滤器

                // 在这里添加字段过滤器
                $filter->equal('number', '编号');

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

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

                $platform = Platform::all();
                $data = [];
                foreach ($platform as $item) {
                    $data[$item->id] = $item->name;
                }
                $filter->equal('platform_id')->select($data);

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
        return Show::make($id, new Country(), function (Show $show) {
            $show->id;
            $show->icon;
            $show->number;
            $show->name;
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
        return Form::make(new Country(), function (Form $form) {
            $form->display('id');

            $platform = Platform::all();
            $data = [];
            foreach ($platform as $item) {
                $data[$item->id] = $item->name;
            }
            $form->select('platform_id', '平台')->options($data);

            $form->image('icon')->rules('required');
            $form->text('number')->rules('required');
            $form->text('name')->rules('required');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
