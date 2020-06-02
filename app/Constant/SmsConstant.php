<?php

namespace App\Constant;
class SmsConstant
{

    // 苹果接码 token值
    public static $AppleSmsToken = "AppleSmsToken";
    // 柠檬平台接码
    public static $LemonSmsToken = "LemonSmsToken";


    public static $LEMON_STATIC = [
        '906' => '手机号列表为空'
    ];

    public static $APPLE_STATIC = [
        '-1' => "暂时无号",
        '-2' => "Token不存在",
        '-3' => "项目ID不存在",
        '-4' => "未知错误码",
        '-5' => "项目未审核",
        '-6' => "项目已禁用",
        '-7' => "用户已禁用",
    ];
}
