<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>项目列表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css" media="all"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style type="text/css">
        @media screen and (max-width: 720px) {
            body {
                width: 850px;
            }
        }

        .demoTable {
            padding: 5px;
        }

        .layui-input {
            width: 300px;
        }
    </style>
</head>
<body>
<div class="layui-fuild">
    <div class="demoTable">
        <div class="layui-inline">
            <select class="layui-select" name="status" id="status">
                <option value="-1">请选择工单状态</option>
                <option value="0">未处理</option>
                <option value="1">已退款</option>
                <option value="2">已拒绝</option>

            </select>
        </div>
        <button class="layui-btn layui-btn-normal" data-type="reload">搜尋</button>
        <a href="/workOrder/create" class="layui-btn layui-btn-sm">提交工单</a>

    </div>
    <script type="text/html" id="imgnode">

        <img width="25" src="/uploads/@{{ d.icon}}">

    </script>
    <table class="layui-hide" id="prolist" lay-filter="list"></table>

</div>

<script type="text/javascript" src="https://www.layuicdn.com/layui/layui.js"></script>
<script type="text/javascript">
    layui.use('table', function () {
        var table = layui.table;
        var $ = layui.$;
        table.render({
            elem: '#prolist'
            , method: 'get'
            , url: '/workOrder/getList/'
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , limit: 10
            , skin: 'row'
            , even: true
            , cols: [[
                {field: 'id', width: "10%", title: '工单ID'}
                , {field: 'reason', title: '原因'}
                , {field: 'manager_back', title: '管理员回复'}
                , {field: 'record', title: '接码单'}
                , {
                    field: 'status', title: '状态', width: "10%", templet: function (data) {
                        if (data.status == 0) {
                            return "未处理";
                        } else if (data.status == 1) {
                            return "已退款";
                        } else {
                            return '已拒绝';
                        }
                    }
                }
            ]]
            , page: true
            , id: 'proReload'
            , text: {
                none: '無資料' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
            }
        });

        active = {
            reload: function () {
                var proReload = $('#proReload');

                //执行重载
                table.reload('proReload', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    , where: {

                        status: $('#status').val()
                    }
                });
            },
            myreload: function () {
                table.reload('proReload', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    , where: {
                        type: 'mycollect'
                    }
                });
            }
        };

        $(document).keydown(function (e) {
            if (e.keyCode === 13) {
                var proReload = $('#proReload');

                //执行重载
                table.reload('proReload', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    , where: {
                        keyword: proReload.val()
                    }
                });
            }
        });

        $('.demoTable .layui-btn').on('click', function () {
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });


    });
</script>
</html>
