<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Examples;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Dcat\Admin\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Card;
use function foo\func;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('总览')
            ->description('系统总览...')
            ->body(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->row(Card::make('今日注册用户', \App\Models\User::whereDate('created_at', '>', Carbon::today())->count()));
                });
                $row->column(4, function (Column $column) {
                    $column->row(Card::make('今日总销售', \App\Models\CodeReceivingRecord::whereDate('created_at', '>', Carbon::today())->where('status', '1')->sum('amount')));
                });
                $row->column(4, function (Column $column) {
                    $column->row(Card::make('今日接码总数', \App\Models\CodeReceivingRecord::whereDate('created_at', '>', Carbon::today())->count()));
                });


                $row->column(4, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(12, new Examples\SmsActive('Sms.Active'));
                    });
                });

                $row->column(4, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(12, new Examples\AppleSms('苹果平台'));
                    });
                });

                $row->column(4, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(12, new Examples\LemonSms('柠檬平台'));
                    });
                });


                $row->column(6, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(12, new Examples\NewUsers());
                    });
                });


            });
    }
}
