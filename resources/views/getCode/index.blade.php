<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>获取簡訊</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css" media="all"/>
    <style>
        #main {
            padding: 10px;
            background-color: #F2F2F2;
            color: #ff0000;
        }

        form {
            padding: 10px;
        }

        form input.layui-input {
            width: 400px;
        }

        .list-item {
            margin-bottom: 20px;
            width: 1024px;
        }

        #getcontentmsg, .attach {
            display: none;
        }
    </style>
</head>
<body>
<!--<div id="main">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">请新手们注意仔细阅读（大牛请忽略）</div>
                <div class="layui-card-body">
                    1.平台无中国大陆号码!!! 不做腾讯、阿里、银行、政府、金融、违法、违规等相关项目！<br/>
                    2.多个号同时请下载电脑客户端软件 , 大客户合作API请找客服洽谈！多家供应商，折扣力度诱人！<br/>
                    3.部分项目需要使用VPN,同一设备尽量不注册第二个！具体注册技巧请自行网络寻找<br/>
                    4.每个号码最长有效期10分钟,如长时间获取不到验证码,请点击进行释放,然后再獲取號碼进行操作<br/>
                    5.放弃使用时号码时必须释放或拉黑，避免后台收到验证码簡訊扣费! 恶意取号拉黑系统自动锁定账户!不予解封!
                </div>
            </div>
        </div>
    </div>
</div>-->
<form class="layui-form layui-form-pane" action="">
    <div class="list-item">
        <label class="layui-form-label">簡訊通道</label>
        <div class="layui-input-inline">
            <select id="platform" lay-filter="platformChange">
                @foreach($platform as $item)
                    <option value="{{$item->id}}">{{ $item->display_name }}</option>
                @endforeach
            </select>
        </div>

    </div>

    <div class="list-item">
        <label class="layui-form-label">項目</label>
        <div class="layui-input-inline">
            <input type="text" id="proname" value="" placeholder="请点击右侧按钮选择" disabled class="layui-input">
            <input type="hidden" id="proid" value="">
        </div>
        <input class="layui-btn layui-btn-normal submit" lay-submit="" readonly lay-filter="selectpro" value="選擇項目"
               style="width:234px;">
    </div>
    <div class="layui-card" style="width: 800px" id="showRemark">
        <div class="layui-card-header">項目描述</div>
        <div class="layui-card-body" id="projectRemark">暫無</div>
    </div>

    <div class="list-item" id="showCountry">
        <label class="layui-form-label">國家</label>
        <div class="layui-input-inline">
            <input type="text" id="country" value="" placeholder="必選擇" disabled class="layui-input">
            <input type="hidden" id="domain" value="">
        </div>
        <input class="layui-btn layui-btn-normal submit" lay-submit="" readonly lay-filter="selectcountry" value="選擇國家"
               style="width:234px;">
    </div>

    <div id="appleShow" style="display: none">
        <div class="list-item">
            <label class="layui-form-label">运营商</label>
            <div class="layui-input-inline">
                <select id="operator">
                    @foreach($operate as $item)
                        <option value="{{$item->id}}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="list-item">
            <div class="layui-input-inline">
                <label class="layui-form-label">省</label>
                <div class="layui-input-inline">
                    <select id="province" lay-filter="provinceChange">
                        <option value="不限">不限省份</option>
                        @foreach($area as $item)
                            <option value="{{$item->id}}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="list-item">
            <div class="layui-input-inline">
                <label class="layui-form-label">市</label>
                <div class="layui-input-inline">
                    <select id="city">
                        <option value="不限">不限市</option>
                    </select>
                </div>
            </div>
        </div>


        <div class="list-item">
            <label class="layui-form-label">号段</label>
            <div class="layui-input-inline">
                <select id="segment">
                    @foreach($segment as $item)
                        <option value="{{$item->id}}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>


    </div>


    <div class="list-item">
        <label class="layui-form-label">手機號碼</label>
        <div class="layui-input-inline">
            <input type="text" id="number" value="" disabled placeholder="右邊獲取新號碼" class="layui-input">
            <input type="hidden" id="orderid" value="">
            <input type="hidden" id="orderids" value="">
        </div>
        <input class="layui-btn layui-btn-normal submit" id="selectnumnode" lay-submit="" readonly
               lay-filter="selectnum" value="獲取號碼" style="width:110px;">

    </div>


    <div class="list-item">
        <input class="layui-btn layui-btn-danger submit" readonly lay-submit="" lay-filter="againgetsms"
               id="getcontentmsg" value="准备获取簡訊内容...">
        <input class="layui-btn layui-btn-normal submit" readonly lay-submit="" lay-filter="releasember" value="釋放號碼">
        <input class="layui-btn layui-btn-normal submit" readonly lay-submit="" lay-filter="deletebymber" value="封鎖號碼">
    </div>
    <div class="layui-form-item layui-form-text">
        <div class="layui-form">
            <table class="layui-table">
                <colgroup>
                    <col width="15%">
                    <col width="85%">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>時間</th>
                    <th>簡訊内容</th>
                </tr>
                </thead>
                <tbody id="smscontent">
                </tbody>
            </table>
        </div>
    </div>
</form>
<script type="text/javascript" src="https://www.layuicdn.com/layui/layui.js"></script>
<script type="text/javascript">


    var getSmsTask;

    layui.config({
        base: "/"
    }).use(['form', 'layer'], function () {
        $ = layui.jquery;
        var form = layui.form;

        $(document).keyup(function (event) {
            if (event.keyCode == 13) {
                $("#selectnumnode").trigger("click");
            }
        });

        form.on('submit(selectpro)', function () {

            layer.open({
                type: 2,
                title: '项目列表',
                area: ['80%', '90%'],
                content: '/project/selectList?platform=' + $('#platform').val(),
                btn: ['确定'],
                yes: function (index, layero) {
                    var proname = $('#proname').val();
                    var proid = $('#proid').val();
                    if ($('#projectRemark').html() == '') {
                        $('#showRemark').hide();
                    } else {
                        $('#showRemark').show();
                    }

                    if (proname && proid) {
                        layer.close(index);
                    } else {
                        layer.alert('选择项目错误，请重新选择！');
                    }

                },
            });

            return false;
        });

        form.on("select(platformChange)", function (obj) {
            $('#proid').val("");
            $('#domain').val("");
            $('#projectRemark').html("");
            if (obj.value == 2) {
                $('#showCountry').hide();
                $('#appleShow').show();
            } else {
                $('#showCountry').show();
                $('#appleShow').hide();
            }
        })
        // 省切换
        form.on("select(provinceChange)", function (obj) {
            $.ajax({
                url: '/getNextArea?id=' + obj.value,
                dataType: 'json',
                success: function (res) {
                    var html = "<option value=\"不限\">不限市</option>";
                    for (let i = 0; i < res.data.length; i++) {
                        html += `<option value="${res.data[i].id}">${res.data[i].name}</option>`;
                    }
                    $('#city').html(html);
                    form.render();
                }
            })
        })

        form.on('submit(selectcountry)', function () {

            layer.open({
                type: 2,
                title: '国家列表',
                area: ['80%', '90%'],
                content: '/selectCountryPage?platform=' + $('#platform').val(),
                btn: ['确定'],
                yes: function (index, layero) {

                    var country = $('#country').val();
                    var domain = $('#domain').val();

                    if (country && domain) {
                        layer.close(index);
                    } else {
                        layer.alert('选择国家错误，请重新选择！');
                    }

                },
            });

            return false;

        });

        form.on('submit(selectnum)', function () {

            var lastorder = $('#orderids').val();

            if (lastorder) {

                $.ajax({
                    url: '/releaseNumber/',
                    type: 'get',
                    data: 'orderid=' + lastorder,
                    success: function (msg) {

                    },
                    error: function () {

                    }

                });
            }

            var proid = $('#proid').val();

            if (!proid) {

                layer.alert('选择项目错误，请重新选择！');
                return false;

            }

            var domain = $('#domain').val();

            if ($('#platform').val() != 2 && !domain) {

                layer.alert('选择国家错误，请重新选择！');
                return false;

            }

            var obj = this;

            $(obj).val('获取中...');
            $(obj).attr('lay-filter', '');

            lockBtn('lock');

            $.ajax({
                url: '/getNumber/',
                type: 'get',
                data: {
                    projectId: proid,
                    countryId: domain,
                    platform: $('#platform').val(),
                    operate: $('#operator').val(),
                    province: $('#province').val(),
                    city: $('#city').val(),
                    segment: $('#segment').val(),
                },
                success: function (msg) {
                    if (msg.code == 0) {
                        layer.alert(msg.msg);
                        lockBtn('unlock');
                    } else if (msg.code == 1) {

                        stopGet();

                        $('#smscontent').html('');

                        layer.msg(msg.msg);

                        $('#number').val(msg.data.number);
                        $('#orderid').val(msg.data.id);
                        getsms();

                    }

                    $(obj).val('獲取號碼');
                    $(obj).attr('lay-filter', 'selectnum');
                },
                error: function () {
                    $(obj).val('獲取號碼');
                    $(obj).attr('lay-filter', 'selectnum');
                    layer.msg('獲取號碼失败！请刷新重试！');
                    lockBtn('unlock');
                }
            });

            return false;

        });

        form.on('submit(selectednum)', function () {

            var lastorder = $('#orderids').val();

            if (lastorder) {

                $.ajax({
                    url: '/releaseNumber/',
                    type: 'get',
                    data: 'orderid=' + lastorder,
                    success: function (msg) {

                    },
                    error: function () {
                    }

                });
            }


            var proid = $('#proid').val();

            if (!proid) {

                layer.alert('选择项目错误，请重新选择！');
                return false;

            }

            var domain = $('#domain').val();

            if (!domain) {

                layer.alert('选择国家错误，请重新选择！');
                return false;

            }


            var phoneNumber = $('#number').val();
            if (!phoneNumber) {

                layer.alert('请输入手机号码！');
                return false;

            }
            phoneNumber = encodeURIComponent(phoneNumber);

            var obj = this;

            $(obj).val('获取中...');
            $(obj).attr('lay-filter', '');

            lockBtn('lock');

            $.ajax({
                url: '/getNumber/',
                type: 'get',
                data: {
                    projectId: proid,
                    countryId: domain,
                    platform: $('#platform').val(),
                    operate: $('#operator').val(),
                    province: $('#province').val(),
                    city: $('#city').val(),
                    segment: $('#segment').val(),
                },
                success: function (msg) {
                    if (msg.code == 0) {
                        layer.alert(msg.msg);
                        lockBtn('unlock');
                    } else if (msg.code == 1) {

                        stopGet();

                        $('#smscontent').html('');

                        layer.msg(msg.msg);

                        $('#number').val(msg.data[0].phoneNumber);
                        $('#orderid').val(msg.data[0].orderid);
                        $('#orderids').val(msg.data[0].orderid);

                        getsms();

                    }
                    $(obj).val('获取指定号');
                    $(obj).attr('lay-filter', 'selectednum');
                },
                error: function () {
                    $(obj).val('获取指定号');
                    $(obj).attr('lay-filter', 'selectednum');
                    layer.msg('獲取號碼失败！请刷新重试！');
                    lockBtn('unlock');
                }
            });

            return false;

        });

        function stopGet() {

            clearInterval(getSmsTask);
            $('#getcontentmsg').css('display', 'none');
            $('#getcontentmsg').val('準備獲取簡訊內容...');

        }

        function lockBtn(type) {
            switch (type) {
                case 'lock':
                    $("#selectnumnode").attr("disabled", true);
                    $("#selectnumnode").attr("class", 'layui-btn layui-btn-disabled');

                    $("#selectednumnode").attr("disabled", true);
                    $("#selectednumnode").attr("class", 'layui-btn layui-btn-disabled');
                    break;
                case 'unlock':

                    $("#selectnumnode").attr("disabled", false);
                    $("#selectnumnode").attr("class", 'layui-btn layui-btn-normal submit');

                    $("#selectednumnode").attr("disabled", false);
                    $("#selectednumnode").attr("class", 'layui-btn submit');
            }

        }

        function getsms() {

            var orderid = $('#orderid').val();

            var i = 132;

            $('#getcontentmsg').css('display', 'inline');

            getSmsTask = setInterval(function () {

                $('#getcontentmsg').val('剩餘 ' + i + ' 次獲取簡訊...');

                $.ajax({
                    url: '/getSmsContent/',
                    data: 'orderid=' + orderid,
                    type: 'get',
                    success: function (msg) {

                        if (msg.code == 1) {

                            var str = '<tr><td>' + msg.data.times + '</td><td>' + msg.data.messages + '</td></tr>';
                            playSound();
                            $('#smscontent').append(str);

                            stopGet();
                            lockBtn('unlock');

                        } else if (msg.code == 0) {
                            // stopGet();
                            lockBtn('unlock');
                            layer.msg(msg.msg);
                        }

                    },
                    error: function () {

                        lockBtn('unlock');
                        layer.msg('网络错误!', {icon: 5, time: 3000, shade: 0.8});

                    }

                });

                i--;

                if (i <= 0) {

                    stopGet();
                    lockBtn('unlock');

                    layer.msg('获取簡訊内容超时！请重新獲取號碼！');

                    $.ajax({
                        url: '/releaseNumber/',
                        type: 'get',
                        data: 'orderid=' + orderid,
                        success: function (msg) {

                        },
                        error: function () {

                        }

                    });

                }

            }, 5000);

        }

        function playSound() {
            var borswer = window.navigator.userAgent.toLowerCase();
            if (borswer.indexOf("ie") >= 0) {
                //IE内核浏览器
                var strEmbed = '<embed name="embedPlay" src="https://www.5188ma.com/sms1.mp3"></embed>';
                if ($("body").find("embed").length <= 0)
                    $("body").append(strEmbed);
                var embed = document.embedPlay;

                //浏览器不支持 audion，则使用 embed 播放
                embed.volume = 100;
                //embed.play();这个不需要
            } else {
                //非IE内核浏览器
                var strAudio = "<audio id='audioPlay' src='https://www.5188ma.com/sms1.mp3' hidden='true'>";
                if ($("body").find("audio").length <= 0)
                    $("body").append(strAudio);
                var audio = document.getElementById("audioPlay");

                //浏览器支持 audion
                audio.play();
            }
        }

        form.on('submit(releasember)', function () {

            var orderid = $('#orderid').val();

            $.ajax({
                url: '/releaseNumber/',
                type: 'get',
                data: 'orderid=' + orderid,
                success: function (msg) {

                    if (msg.code == 1) {
                        stopGet();
                        lockBtn('unlock');
                    }

                    layer.msg(msg.msg);

                },
                error: function () {
                    layer.msg('网络错误！请重试！');
                }

            });


        });

        form.on('submit(deletebymber)', function () {

            var orderid = $('#orderid').val();

            $.ajax({
                url: '/shieldNumber/',
                type: 'get',
                data: 'orderid=' + orderid,
                success: function (msg) {

                    if (msg.code == 1) {
                        stopGet();
                        lockBtn('unlock');
                    }

                    layer.msg(msg.msg);

                },
                error: function () {
                    layer.msg('网络错误！请重试！');
                }

            });

        });

    });
</script>
<script src="https://ajax.cloudflare.com/cdn-cgi/scripts/7089c43e/cloudflare-static/rocket-loader.min.js"
        data-cf-settings="f22083469a3eab6cdb75ca4f-|49" defer=""></script>
</body>
</html>
