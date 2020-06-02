<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Area;
use App\Models\Platform;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Controllers\AdminController;

class AreaController extends AdminController
{

    public function index(Content $content)
    {
        Admin::style('.tab-content .da-box{margin-bottom:0}');
        return $content
            ->header('区域')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Area());
        $grid->id('ID')->bold()->sortable();
        $grid->name->tree();
        $grid->order->orderable();
        $grid->platform_id->display(function ($platform) {
            return Platform::find($platform)->name;
        });

        $grid->quickSearch(['name']);
        $grid->created_at;
        $grid->updated_at->sortable();
        $grid->resource('area');
        $grid->disableEditButton();
        $grid->showQuickEditButton();
        $grid->enableDialogCreate();
        $grid->disableBatchDelete();
        $grid->disableViewButton();

        return $grid;
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
        return Show::make($id, new Area(), function (Show $show) {
            $show->id;
            $show->platform_id;
            $show->pid;
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
        return Form::make(new Area(), function (Form $form) {

            $platform = Platform::all();
            $data = [];
            foreach ($platform as $item) {
                $data[$item->id] = $item->name;
            }
            $form->select('platform_id', '平台')->options($data);

            $form->display('id');

            $area = \App\Models\Area::where('pid', 0)->get();
            $data = [
                0 => '顶级'
            ];
            foreach ($area as $item) {
                $data[$item->id] = $item->name;
            }
            $form->select('pid', '上级')->options($data);


            $form->text('number');
            $form->text('name');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
