<?php

namespace App\Http\Controllers\Home;

use App\Constant\SmsConstant;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CodeReceivingRecord;
use App\Models\Country;
use App\Models\Operator;
use App\Models\Platform;
use App\Models\Project;
use App\Models\Segment;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderGroup;
use App\Utils\AppleSms;
use App\Utils\LemonSms;
use App\Utils\SMSActivate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    //

    /**
     * 工单列表首页
     */
    public function index()
    {
        return view('workOrder/index');
    }

    public function getList(Request $request)
    {
        $limit = $request->limit;
        $status = $request->status;
        $page = $request->page - 1;
        $total = DB::table("work_orders");
        if ($status && $status != -1) {
            $total = $total->where('status', $status);
        }
        $total = $total->groupBy()
            ->count();

        $items = DB::table("work_orders");
        if ($status && $status != -1) {
            $items = $items->where('status', $status);
        }
        $items = $items
            ->skip($page * $limit)
            ->take($limit)
            ->get();

        foreach ($items as $itemData) {

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
                r.id=" . $itemData->record_id . "
                AND p.id = r.platform_id
                AND r.project_id = ps.id
                ORDER BY r.created_at DESC";

            $result = DB::select($sql);

            $item = $result[0];
            $itemData->record = "ID:{$item->id} - 【" . $item->platName . "-" . $item->proName . "-" . $item->phone . "】 - 金额：" . $item->amount;
        }

        return response()->json([
            'code' => 0,
            'msg' => '获取成功',
            'data' => $items,
            'count' => $total
        ]);
    }

    public function getMyRecord(Request $request)
    {
        $userId = $request->user()->id;
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
            r.user_id = " . $userId . "
            AND p.id = r.platform_id
            AND r.project_id = ps.id
            and
            r.id not in (select record_id from work_orders)
            ORDER BY r.created_at DESC";

        $result = DB::select($sql);

        $data = [];
        foreach ($result as $item) {
            $data[] = [
                'name' => "ID:{$item->id} - 【" . $item->platName . "-" . $item->proName . "-" . $item->phone . "】 - 金额：" . $item->amount,
                'value' => $item->id
            ];
        }
        return response()->json([
            'code' => 0,
            'data' => $data
        ]);
    }

    public function create()
    {
        return view('workOrder/create');
    }

    public function store(Request $request)
    {
        $reason = $request->reason;
        $selectArr = $request->selectArr;

        foreach ($selectArr as $item) {
            WorkOrder::create([
                'user_id' => $request->user()->id,
                'record_id' => $item,
                'status' => 0,
                'reason' => $reason,
                'manager_back' => '',
            ]);
        }
        return response()->json([
            'code' => 0,
        ]);
    }
}
