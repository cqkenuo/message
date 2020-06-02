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
use App\Utils\AppleSms;
use App\Utils\LemonSms;
use App\Utils\SMSActivate;
use Illuminate\Http\Request;

class GetCodeController extends Controller
{
    //

    /**
     * 取码首页
     */
    public function index()
    {
        $operate = Operator::all();
        $platform = Platform::all();
        $segment = Segment::all();
        $area = Area::where('pid', 0)->get();
        return view('getCode/index', compact('platform', 'operate', 'segment', 'area'));
    }

    public function getNextArea(Request $request)
    {
        return response()->json([
            'code' => 0,
            'data' => Area::where('pid', $request->id)->get()
        ]);
    }


    public function getNumber(Request $request)
    {

        $project = Project::find($request->projectId);
        $country = Country::find($request->countryId);


        $user = User::find($request->user()->id);

        if ($user->money < $project->price) {

            return response()->json([
                'code' => 1,
                'msg' => '请先充值后再进行获取手机号',
                'data' => [

                ]
            ]);
        }

        $platform = $request->platform;

        switch ($platform) {
            case 1:
                $data = SMSActivate::getInstance()->getNumber($project->number, $country->number);

                $result = SMSActivate::getInstance()->setStatus($data['id'], 1);
                break;
            case 2:
                // 苹果接码
                $operate = Operator::find($request->operate);
                if ($request->province == '不限') {
                    $province = '不限';
                } else {
                    $province = Area::find($request->province)->number;
                }
                if ($request->city == '不限') {
                    $city = '不限';
                } else {
                    $city = Area::find($request->city)->number;
                }
                $segment = Segment::find($request->segment);
                $data = AppleSms::getInstance()->getNumber($project->number, $operate->number, $province, $city, $segment->number);
                if ($data[0] != 1) {
                    return response()->json([
                        'code' => 0,
                        'msg' => array_key_exists($data[1], SmsConstant::$APPLE_STATIC) ? SmsConstant::$APPLE_STATIC[$data[1]] : ""
                    ]);
                }
                $data = [
                    'id' => $data[1],
                    'number' => $data[4]
                ];

                break;
            case 3:
                $number = LemonSms::getInstance()->getNumber($country->number, $project->number);
                if ($number->code != 200) {
                    return response()->json([
                        'code' => 0,
                        'msg' => '系统错误' . array_key_exists($number->code, SmsConstant::$LEMON_STATIC) ? SmsConstant::$LEMON_STATIC[$number->code] : ""
                    ]);
                }
                $data = [
                    'id' => 0,
                    'number' => $number->data
                ];
                break;
        }


        $receive = CodeReceivingRecord::create([
            'project_id' => $project->id,
            'platform_id' => $platform,
            'thirty_id' => $data['id'],
            'user_id' => $request->user()->id,
            'phone' => $data['number'],
            'country_id' => $country->id,
            'amount' => $project->price,
            'content' => '',
            'status' => 0,
        ]);

        return response()->json([
            'code' => 1,
            'msg' => '获取成功',
            'data' => [
                'number' => $data['number'],
                'id' => $receive->id
            ]
        ]);
    }

    public function getSmsContent(Request $request)
    {

        $orderid = $request->orderid;

        $codeReceive = CodeReceivingRecord::find($orderid);

        switch ($codeReceive->platform_id) {
            case 1:
                $data = SMSActivate::getInstance()->getStatus($codeReceive->thirty_id);

                if ($data['status'] == 0) {
                    return response()->json([
                        'code' => 0,
                        'msg' => '正在获取验证码',
                        'data' => [
                        ]
                    ]);
                } else {
                    if ($codeReceive->status != 1) {

                        $codeReceive->update([
                            'content' => $data['code'],
                            'status' => 1
                        ]);

                        // 扣钱
                        $user = User::find($request->user()->id);
                        $project = Project::find($codeReceive->project_id);
                        $user->update([
                            'money' => $user->money - $project->price
                        ]);
                    }

                    return response()->json([
                        'code' => 1,
                        'msg' => '获取成功',
                        'data' => [
                            'times' => date('Y-m-d H:i:s', time()),
                            'messages' => $data['code']
                        ]
                    ]);
                }
                break;
            case 2:
                $data = AppleSms::getInstance()->getCode($codeReceive->thirty_id);

                if ($data[0] == 0 && $data[1] == -3) {
                    return response()->json([
                        'code' => 0,
                        'msg' => '正在获取验证码',
                        'data' => [
                        ]
                    ]);
                } else if ($data[0] == 1) {
                    if ($codeReceive->status != 1) {

                        $codeReceive->update([
                            'content' => $data[2],
                            'status' => 1
                        ]);

                        // 扣钱
                        $user = User::find($request->user()->id);
                        $project = Project::find($codeReceive->project_id);
                        $user->update([
                            'money' => $user->money - $project->price
                        ]);
                    }

                    return response()->json([
                        'code' => 1,
                        'msg' => '获取成功',
                        'data' => [
                            'times' => date('Y-m-d H:i:s', time()),
                            'messages' => $data[2]
                        ]
                    ]);
                }

                break;
            case 3:
                $project = Project::find($codeReceive->project_id);
                $data = LemonSms::getInstance()->getCode($project->number, $codeReceive->phone);

                if ($data->code == 908 || $data->code == 405) {
                    return response()->json([
                        'code' => 0,
                        'msg' => '正在获取验证码',
                        'data' => [
                        ]
                    ]);
                } else if ($data->code == 200) {
                    if ($codeReceive->status != 1) {

                        $codeReceive->update([
                            'content' => $data->data,
                            'status' => 1
                        ]);

                        // 扣钱
                        $user = User::find($request->user()->id);
                        $project = Project::find($codeReceive->project_id);
                        $user->update([
                            'money' => $user->money - $project->price
                        ]);
                    }

                    return response()->json([
                        'code' => 1,
                        'msg' => '获取成功',
                        'data' => [
                            'times' => date('Y-m-d H:i:s', time()),
                            'messages' => $data->data
                        ]
                    ]);
                }
                break;

        }


    }

    // 取消手机号
    public function releaseNumber(Request $request)
    {
        $orderid = $request->orderid;

        $codeReceive = CodeReceivingRecord::find($orderid);


        switch ($codeReceive->platform_id) {
            case 1:
                $result = SMSActivate::getInstance()->setStatus($codeReceive->thirty_id, 6);
                break;
            case 2:
                $result = AppleSms::getInstance()->setRelease($codeReceive->thirty_id);
                break;
            case 3:
                $result = LemonSms::getInstance()->setRelease($codeReceive->project_id, $codeReceive->phone);
                break;
        }


        return response()->json([
            'code' => 1,
            'msg' => '释放成功，请重新获取手机号',
            'data' => [
            ]
        ]);

    }

    // 拉黑
    public function shieldNumber(Request $request)
    {
        $orderid = $request->orderid;

        $codeReceive = CodeReceivingRecord::find($orderid);

        switch ($codeReceive->platform_id) {
            case 1:
                $result = SMSActivate::getInstance()->setStatus($codeReceive->thirty_id, 8);
                break;
            case 2:
                $result = AppleSms::getInstance()->setBlack($codeReceive->thirty_id, "对接用户拉黑");
                break;
            case 3:
                $result = LemonSms::getInstance()->setBlack($codeReceive->project_id, $codeReceive->phone);
                break;
        }

        return response()->json([
            'code' => 1,
            'msg' => '拉黑成功，请重新获取手机号',
            'data' => [
            ]
        ]);

    }
}
