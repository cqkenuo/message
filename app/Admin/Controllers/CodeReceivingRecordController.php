<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\CodeReceivingRecord;
use App\Models\Platform;
use App\Models\Project;
use App\Models\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Controllers\AdminController;

class CodeReceivingRecordController extends AdminController
{
    protected $title = '记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(\App\Models\CodeReceivingRecord::orderBy('created_at', 'desc'), function (Grid $grid) {


            $grid->id->sortable();
            $grid->platform_id->display(function ($platform) {
                $platform = Platform::find($platform);
                if ($platform) {
                    return $platform->name;
                } else {
                    return "无";
                }
            });
            $grid->project_id->display(function ($project_id) {
                $platform = Project::find($project_id);

                if ($platform) {
                    return $platform->name;
                } else {
                    return "无";
                }

            });
            $grid->column('user_id')->display(function ($user_id) {
                $user = User::find($user_id);
                if ($user) {
                    return $user->name;
                } else {
                    return "无此用户";
                }
            });
            $grid->phone;
            $grid->amount;
            $grid->content;
            $grid->status->display(function ($status) {
                return $status == 0 ? '未获取验证码' : '已接码';
            });
            $grid->created_at;


            $grid->filter(function (Grid\Filter $filter) {


                $filter->equal('id');

                $platform = Platform::all();
                $data = [];
                foreach ($platform as $item) {
                    $data[$item->id] = $item->name;
                }
                $filter->equal('platform_id')->select($data);

                $platform = User::all();
                $data = [];
                foreach ($platform as $item) {
                    $data[$item->id] = $item->name;
                }
                $filter->equal('user_id')->select($data);


                $project = Project::all();
                $data = [];
                foreach ($project as $item) {
                    $data[$item->id] = "【".Platform::find($item->platform_id)->name."】" . $item->name;
                }
                $filter->equal('project_id')->select($data);


                $filter->equal('status')->select([
                    0 => '未接码',
                    1 => '已接码'
                ]);

                $filter->between('created_at')->datetime();
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
        return Show::make($id, new CodeReceivingRecord(), function (Show $show) {
            $show->id;
            $show->project_id;
            $show->phone;
            $show->country_id;
            $show->amount;
            $show->content;
            $show->status;
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
        return Form::make(new CodeReceivingRecord(), function (Form $form) {
            $form->display('id');
            $form->text('project_id');
            $form->text('phone');
            $form->text('country_id');
            $form->text('amount');
            $form->text('content');
            $form->text('status');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
