<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Segment;
use App\Models\Platform;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Controllers\AdminController;

class SegmentController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Segment(), function (Grid $grid) {
            $grid->id->sortable();
            $grid->platform_id->display(function ($platform) {
                return Platform::find($platform)->name;
            });
            $grid->number;
            $grid->name;
            $grid->created_at;
            $grid->updated_at->sortable();

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
        return Show::make($id, new Segment(), function (Show $show) {
            $show->id;
            $show->platform_id;
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
        return Form::make(new Segment(), function (Form $form) {
            $form->display('id');
            $platform = Platform::all();
            $data = [];
            foreach ($platform as $item) {
                $data[$item->id] = $item->name;
            }
            $form->select('platform_id', '平台')->options($data);

            $form->text('number');
            $form->text('name');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
