<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\CodeReceivingRecord;
use App\Admin\Repositories\WorkOrder;
use App\Models\Platform;
use App\Models\RechargeDetails;
use App\Models\User;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Show;
use Dcat\Admin\Controllers\AdminController;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(\App\Models\WorkOrder::orderBy('created_at', 'desc'), function (Grid $grid) {
            $grid->id->sortable();
            $grid->column('user_id')->display(function ($user_id) {
                $user = User::find($user_id);
                if ($user) {
                    return $user->name;
                } else {
                    return "无此用户";
                }
            });
            $grid->column('record_id')->display(function ($record_id) {

                $sql = "SELECT
                r.id,
                p.NAME AS platName,
                ps.NAME AS proName,
                r.phone,
                r.amount
            FROM
                `code_receiving_records` r,
                platforms p,
                projects ps
            WHERE
                r.id=" . $record_id . "
                AND p.id = r.platform_id
                AND r.project_id = ps.id
                ORDER BY r.created_at DESC";
                $result = DB::select($sql);

                $item = $result[0];
                return "ID:{$item->id} - 【" . $item->platName . "-" . $item->proName . "-" . $item->phone . "】 - 金额：" . $item->amount;
            });

            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->disableViewButton();
            $grid->disableDeleteButton();


            $grid->column('status')->display(function ($status) {
                switch ($status) {
                    case 0:
                        return "未处理";
                        break;
                    case 1:
                        return "已退款";
                        break;
                    case 2:
                        return "已拒绝";
                        break;
                }

            });
            $grid->reason;
            $grid->manager_back;
            $grid->created_at;

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new WorkOrder(), function (Form $form) {

            $form->tools(function (Form\Tools $tools) {
                // 去掉跳转列表按钮
                $tools->disableList();

                // 去掉删除按钮
                $tools->disableDelete();

            });


            $platform = User::all();
            $data = [];
            foreach ($platform as $item) {
                $data[$item->id] = $item->name;
            }

            $form->select('user_id')->options($data);

            $sql = "SELECT
                r.id,
                p.NAME AS platName,
                ps.NAME AS proName,
                r.phone,
                r.amount
            FROM
                `code_receiving_records` r,
                platforms p,
                projects ps
            WHERE
                 p.id = r.platform_id
                AND r.project_id = ps.id
                ORDER BY r.created_at DESC";
            $result = DB::select($sql);

            $data = [];
            foreach ($result as $item) {
                $data[$item->id] = "ID:{$item->id} - 【" . $item->platName . "-" . $item->proName . "-" . $item->phone . "】 - 金额：" . $item->amount;
            }

            $form->select('record_id')->options($data);

            $form->select('status')->options([
                0 => '未处理',
                1 => '退款',
                2 => '拒绝'
            ]);
            $form->textarea('reason', '客户反馈原因')->disable();
            $form->textarea('manager_back')->required();


            if ($form->isEditing()) {
                $status = $form->input('status');
                if ($status == 1) {
                    $amount = \App\Models\CodeReceivingRecord::find($form->input('record_id'))->amount;
                    RechargeDetails::create([
                        'admin_user_id' => Admin::user()->id,
                        'user_id' => $form->input('user_id'),
                        'amount' => $amount,
                    ]);
                    $user = User::find($form->input('user_id'));
                    $user->update([
                        'money' => $user->money + $amount
                    ]);
                }
            }
        });
    }
}
