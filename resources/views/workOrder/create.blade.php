<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>个人资料</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css" media="all"/>
    <style>
        form input.layui-input[disabled] {
            background: #f2f2f2;
            color: #595963 !important;
        }

        .user_left {
            width: 100%;
            margin: 20px 0 0 20px;
        }

        .userAddress.layui-form-item .layui-input-inline {
            width: 23%;
        }

        .userAddress.layui-form-item .layui-input-inline:last-child {
            margin-right: 0;
        }

        .mustnode {
            display: block;
            margin-top: 6px;
            color: red;
            margin-left: 5px;
        }

        .layui-form-pane .layui-form-label {
            width: 150px;
        }

        .notice {
            color: #ff0000;
        }

        form input.layui-input {
            width: 195px;
        }

        /*适配*/
        @media screen and (max-width: 1050px) {
            /*用户信息*/
            .user_left {
                width: 100%;
                float: none;
                margin-left: 0;
            }

        }

        @media screen and (max-width: 450px) {
            .userAddress.layui-form-item .layui-input-inline {
                width: auto;
            }
        }


    </style>
</head>
<body class="childrenBody">
<div class="layui-fuild">

    <div class="layui-card">
        <div class="layui-card-header">個人訊息</div>
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">

                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">申訴原因</label>
                    <div class="layui-input-block">
                        <textarea placeholder="請輸入內容 " name="reason" class="layui-textarea"></textarea>
                    </div>
                </div>


                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label"> 投訴收碼紀錄</label>
                    <div class="layui-input-block">
                        <div id="test3"></div>

                    </div>
                </div>


                {{ csrf_field() }}


                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <button class="layui-btn layui-btn-normal submit" lay-submit="" lay-filter="add">提交申訴
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<script type="text/javascript" src="/layui/layui.js"></script>
<script type="text/javascript">
    layui.config({
        base: "/layui/lay/modules/"
    }).use(['form', 'layer', 'transfer', 'util', 'xmSelect'], function () {
        $ = layui.jquery;
        var form = layui.form
            , transfer = layui.transfer
            , layer = layui.layer
            , util = layui.util
            , xmSelect = layui.xmSelect;

        //显示搜索框
        var select = null;

        $.ajax({
            url: '/workOrder/getMyRecord',
            dataType: 'json',
            success: function (res) {
                //渲染多选
                select = xmSelect.render({
                    el: '#test3',
                    filterable: true,
                    toolbar: {
                        show: true,
                    },
                    delay: 1000,
                    paging: true,
                    data: res.data
                })
            }
        })

        form.on('submit(add)', function (obj) {
            console.log(obj);
            if (obj.field.reason == '') {
                layer.msg('請輸入申訴原因');
                return false;
            }
            var selectArr = select.getValue('value');
            if (selectArr.length == 0) {
                layer.msg('請選擇一条记录');
                return false;
            }


            obj.field.selectArr = selectArr;

            $.ajax({
                type: 'get',
                url: '/workOrder/store',
                data: obj.field,
                dataType: 'json',
                success: function (res) {
                    layer.msg('申訴成功', {icon: 6, time: 1000}, function () {
                        window.location.href = '/workOrder/index'
                    });
                },
                error: function () {
                    layer.msg(res.msg, {icon: 5, time: 3000, shade: 0.8});
                }
            });

            return false;
        });

    });
</script>

</body>
</html>
