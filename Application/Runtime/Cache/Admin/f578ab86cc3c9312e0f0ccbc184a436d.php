<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>视频编辑-美客系统管理</title>
    <meta name="keywords" content="美客系统管理">
    <meta name="description" content="美客系统管理">
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/Public/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/Public/css/font-awesome.css?v=4.4.0" rel="stylesheet">
    <link href="/Public/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/Public/css/animate.css" rel="stylesheet">
    <link href="/Public/css/style.css?v=4.0.0" rel="stylesheet">
    <script type="text/javascript" charset="utf-8" src="/Public/js/plugins/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Public/js/plugins/ueditor/ueditor.all.js"></script>
    

    <base target="_self">
    <style type="text/css">
        .view_image_block{ position: absolute; width: 200px; height: 200px; display: table-cell; overflow: hidden; background: #fff; border: 1px #ddd solid; z-index: 1000;}
        .view_image_block img{ max-width: 200px; text-align: center; margin: 0 auto;}
        .ticketinfo{display:none;}
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class=""> <a href="<?php echo U('/short_video/index');?>" target="_self" class="btn" style="background: #e00214;color: #fff;">返回</a> </div>
                <div class="ibox-title">
                    <h5><a href="<?php echo U('/short_video/index');?>" style="color: #e00214;"> 发布视频</a> > <small>视频编辑</small></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="post" id="form1" name="edit" target="_self" action="<?php echo U('/short_video/edit');?>" class="form-horizontal">
                        <input type="hidden" name="id" value="<?php echo ($short_video["id"]); ?>" />
                        <input type="hidden" name="url" value="<?php echo ($short_video["url"]); ?>" />
                        <input type="hidden" name="size" value="<?php echo ($short_video["size"]); ?>" />
                        <input type="hidden" name="size_text" value="<?php echo ($short_video["size_text"]); ?>" />
                        <div class="form-group">
                            <label class="col-sm-2 control-label">视频标题 <span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-4">
                                <input type="text" title="<?php echo ($short_video["original_title"]); ?>" name="title" value="<?php echo ($short_video["title"]); ?>" class="form-control" required="" aria-required="true" placeholder="请输入视频标题">
                            </div>
                            <label class="col-sm-2 control-label">发布平台：</label>
                            <div class="col-sm-4">
                                <label class="checkbox-inline i-checks">
                                <input type="radio"  name="platform" <?php if(empty($short_video['platform'])): ?>checked<?php endif; ?> value="0" /> 不限</label>
                                <label class="checkbox-inline i-checks">
                                <input type="radio" name="platform" <?php if($short_video['platform'] == 1): ?>checked<?php endif; ?> value="1" /> 短秀H5</label>
                                    <label class="checkbox-inline i-checks">
                                <input type="radio" name="platform" <?php if($short_video['platform'] == 2): ?>checked<?php endif; ?> value="2" /> 短秀App</label>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">视频详情：</label>
                            <div class="col-sm-10">
                                <script type="text/plain" id="subtitle"  name="subtitle"><?php echo ($short_video["subtitle"]); ?></script>
                                <span class="help-block m-b-none"></span> </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <?php if($short_video[is_show] == 0 OR empty($short_video)): ?><label class="col-sm-2 control-label">视频分类<span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-4">
                                <?php if(is_array($short_video_tag)): $i = 0; $__LIST__ = $short_video_tag;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($i % 2 );++$i; if($tag["id"] != 2): ?><label class="checkbox-inline i-checks">
                                    <input type="radio" name="type" <?php if($tag[id] == $short_video[type]): ?>checked<?php endif; ?> value="<?php echo ($tag[id]); ?>"><?php echo ($tag["tag_name"]); ?></label><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                            </div><?php endif; ?>
                            <?php if($user['role'] < 2): ?><label <?php if($short_video[is_show] == 0 OR empty($short_video)): ?>class="col-sm-1 control-label"<?php else: ?>class="col-sm-2 control-label"<?php endif; ?>>权重：</label>
                            <div class="col-sm-2">
                                <input type="text" name="weight" value="<?php echo ($short_video["weight"]); ?>" class="form-control" placeholder="值越大越靠前">
                            </div>

                            <!--<label class="col-sm-2 control-label">是否首页显示：</label>-->
                            <!--<div class="col-sm-1">-->
                                <!--<label class="checkbox-inline i-checks">-->
                                    <!--<input type="checkbox" name="is_index" <?php if( 1 == $short_video[is_index]): ?>checked<?php endif; ?> value="1">是</label>-->
                            <!--</div>-->
                            <label class="col-sm-2 control-label">是否推荐：</label>
                            <div class="col-sm-1">
                                <label class="checkbox-inline i-checks">
                              <input type="checkbox" name="is_hot" <?php if(1 == $short_video[is_hot]): ?>checked<?php endif; ?> value="1" />是</label>
                            </div><?php endif; ?>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">视频标签<span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-10">
                                <?php if(is_array($goods_tags)): $i = 0; $__LIST__ = $goods_tags;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($i % 2 );++$i;?><label class="checkbox-inline i-checks">
                                        <input type="checkbox" class="tag" data-id="<?php echo ($tag["id"]); ?>" name="tag_id[]" <?php if(in_array($tag['id'], explode(',',$short_video['tag_list']))): ?>checked<?php endif; ?> value="<?php echo ($tag["id"]); ?>" /> <?php echo ($tag["tag_name"]); ?></label><?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">视频文件(不可修改)<span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-4" id="pickfilesvideo">

                                <div class="bootstrap-filestyle input-group">
                                    <div id='newresult' class="view_img form-control" style="max-width:400px;max-hight:20px;overflow-y: scroll; " placeholder="请输入视频标题">
                                        <?php echo ($short_video["filename"]); ?>
                                    </div>
                                    <span class="group-span-filestyle input-group-btn" tabindex="0">
                                  <?php if(!$short_video['url']): ?><label for="filestyle-0" class="btn btn-default " id="pickfiles"><span class="glyphicon glyphicon-folder-open"></span> 选择视频</label><?php endif; ?>
                                </div>
                                </span>
                                <span class="help-block m-b-none" id='progress'></span>
                            </div>
                            <label class="col-sm-2 control-label">视频封面(可选)：</label>
                            <div class="col-sm-4">
                                <img class="pic_view_detail" src="<?php if($short_video['pic']): echo ($short_video['pic']); else: ?>/Public/img/4041.jpg<?php endif; ?>" style="max-width: 150px; max-height: 150px;" /> <div style="margin: 4px; color:red">*标准
                                横版尺寸为：366*184
                                竖版为：366*488</div>
                                <input type="hidden" id="pic" data-url="<?php echo ($short_video["pic"]); ?>" name="pic" value="<?php echo ($short_video["pic"]); ?>" class="view_img form-control" placeholder="不上传封面将取视频的第一帧作为封面">
                                <span class="help-block m-b-none"><iframe src="<?php echo U('/admin/upload/show',array('sid'=>'short_video','fileback'=>'pic'));?>" scrolling="no" topmargin="0" width="300" height="36" marginwidth="0" marginheight="0" frameborder="0" align="left" style="margin-top:3px; float:left"></iframe></span>
                            </div>

                        </div>

                        <?php if($use_agreement): ?><input type="hidden" name="use_agreement" value="1" />
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">发布协议：</label>
                            <div class="col-sm-10">
                                <input type="checkbox" name="agreement" checked value="1" /> 同意《<a href="http://hd.kuaiduodian.com/index.php?s=/tvshow/agreement2" target="_blank" >短秀平台发布协议</a>》
                                <span class="help-block m-b-none"></span> </div>
                        </div>
                            <?php else: ?>
                            <input type="hidden" name="use_agreement" value="0" /><?php endif; ?>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <!--<button class="btn btn-primary" type="submit" onclick="checktosubmit()">保存内容</button>-->
                                <button type="button" class="btn" onclick="checktosubmit()" style="background: #e00214;color: #fff;padding: 5px 20px;">发布</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- 全局js -->
<script src="/Public/js/jquery.min.js?v=2.1.4"></script>
<script src="/Public/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/Public/js/plugins/layer/layer.min.js"></script>
<!-- 自定义js -->
<script src="/Public/js/plugins/layer/laydate/laydate.js"></script>
<script src="/Public/js/content.js?v=1.0.0"></script>
<script src="/Public/js/active-msdt.js?v=1.0.0"></script>

    <!-- iCheck -->
    <script src="/Public/js/plugins/iCheck/icheck.min.js"></script>
    <!--<script src="/Public/js/plugins/layer/layer.js"></script>-->
    <script type="text/javascript">
        var serverFileId = 0;
        $(document).ready(function() {
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });

            $('input.tag').on('ifChecked', function(event){ //ifCreated 事件应该在插件初始化之前绑定
                if($('input.tag:checked').size()>3){
                    $(this).iCheck('uncheck'); //移除 checked 状态
                    $(this).parent().removeClass('checked');
                        layer.msg("最多只能选择3个标签~", function(){
                            $('input.tag').each(function(){

                                if(!$(this).is(":checked")){
                                    $(this).parent().removeClass('checked');
                                }
                            });

                        });

                    return true;
                }
            });

            var editor_a = UE.getEditor('subtitle', {
                initialFrameHeight: 100,
                serverUrl: '/Public/js/plugins/ueditor/php/controller.php'
            });

        });

        function checktosubmit() {
            var txt_firstname = $.trim($("#firstname").attr("value"))
            var txt_lastname = $.trim($("#lastname").attr("value"))


            var isSuccess = 1;
            if ($("input[name='title']").val().length == 0) {
                layer.msg('视频标题不能够为空' || "提示信息！");

                isSuccess = 0;
                return;
            }

            if($('input[name="id"]').val()=="" || '<?php echo ($short_video["is_show"]); ?>' == "0"){
                if ($("input[name='type']:checked").val() == undefined) {
                    layer.msg('请您选择一个分类' || "提示信息！");

                    isSuccess = 0;
                    return;
                }
            }

            if ($("input[name='url']").val() != '') {

            } else {
                if ($.trim($('#newresult').text()) != '' && serverFileId == 0) {
                    layer.msg('等待视频上传完毕' || "提示信息！");
                    isSuccess = 0;
                    return;
                }
            }
            if (serverFileId == 0 && $("input[name='url']").val() == '') {

                layer.msg('请您上传视频' || "提示信息！");
                isSuccess = 0;
                return;
            }
            if ($("input[name='url']").val() != '') {

            } else {
                $("input[name='url']").val(serverFileId);
                $("input[name='size_text']").val(size_text);
                $("input[name='size']").val(size);
            }

            // 必须要同意协议才能发布视频
            if($('input[name="use_agreement"]').val() == 1){
                if($("input[name='agreement']:checked").size() == 0){
                    layer.msg('必须同意短秀发布协议才能发布视频！');
                    return false;
                }
            }
            if (isSuccess == 1) {
                form1.submit();
            }
        }

        function changeGoodsClass(_this) {
            var v = $(_this).val();
            if (v == 2) {
                $("#probabilityShow").show();
                $("#short_videoPriceShow").hide();
                $("#short_videoPriceShowLine").hide();
            } else if (v == 3) {
                $("#short_videoPriceShow").show();
                $("#short_videoPriceShowLine").show();
                $("#probabilityShow").hide();

            } else {
                $("#short_videoPriceShow").hide();
                $("#short_videoPriceShowLine").hide();
                $("#probabilityShow").hide();
            }
        }

        function changeGoodsType(_this) {
            var v = $(_this).val();
            if (v == 2) {
                $("#cardShow").show();
                $("#cardShowLine").show();
                $("#jumpUrlShow").hide();
                $("#jumpUrlShowLine").hide();
            } else if (v == 3 || v == 4) {
                $("#jumpUrlShow").show();
                $("#jumpUrlShowLine").show();
                $("#cardShow").hide();
                $("#cardShowLine").hide();
            } else {
                $("#cardShow").hide();
                $("#cardShowLine").hide();
                $("#jumpUrlShow").hide();
                $("#jumpUrlShowLine").hide();

            }
        }
    </script>

    <script src="//qzonestyle.gtimg.cn/open/qcloud/js/vod/sdk/uploaderh5V3.js" charset="utf-8"></script>
    <script>
        var serverFileId = 0;
        var size_text = 0;
        var size = 0,
            Log = qcVideo.get('Log'),
            JSON = qcVideo.get('JSON'),
            util = qcVideo.get('util'),
            Code = qcVideo.get('Code'),
            Version = qcVideo.get('Version');
        var ErrorCode = qcVideo.get('ErrorCode');
        ErrorCode.UN_SUPPORT_BROWSE !== qcVideo.uploader.initUGC(
            //1: 上传基础条件
            {
                upBtnId: "pickfiles", //上传按钮ID（任意页面元素ID）
                //
                //isTranscode: true, //是否转码
                // isWatermark:true,
                //       transcodeNotifyUrl: 
                /*
                    @desc 从服务端获取签名的函数。该函数包含两个参数：
                    argObj: 待上传文件的信息，关键信息包括：
                        f: 视频文件名(可从getSignature的argObj中获取)，
                        ft: 视频文件的类型(可从getSignature的argObj中获取)，
                        fs: 视频文件的sha1值(必须从getSignature的argObj中获取)
                    callback：客户端从自己的服务端得到签名之后，调用该函数将签名传递给SDK                    
                */
                getSignature: function(argObj, callback) {
                    // 调用APP后台服务器，返回签名
                    console.log(argObj);

                    var sigUrl = '/auto/getSign.php?' +
                        'f=' + encodeURIComponent(argObj.f) +
                        '&ft=' + encodeURIComponent(argObj.ft) +
                        '&fs=' + encodeURIComponent(argObj.fs);
                    $.get(sigUrl).done(function(ret) {
                        console.log(ret);
                        callback(ret);
                    })
                },
                after_sha_start_upload: true //上传分为两个阶段，sha计算和文件网络传输；这个选项设置是否在sha计算完成后，立即进行网络传输上传 (默认非立即上传)
                    ,
                sha1js_path: '/Public/js/calculator_worker_sha1.js' //计算sha1的位置  ，默认为 'http://你的域名/calculator_worker_sha1.js'
            }
            //2: 回调函数
            , {
                /**
                 * 更新文件状态和进度
                 * @param args { id: 文件ID, size: 文件大小, name: 文件名称, status: 状态, percent: 进度,speed: 速度, errorCode: 错误码 }
                 */
                onFileUpdate: function(args) {
                    console.log(args);
                    if (args.code == Code.SHA_FAILED)
                        return alert('该浏览器无法计算SHA')
                    var $line = $('#' + args.id);
                    if (!$line.get(0)) {
                        $('#result').append('<div class="line" id="' + args.id + '"></div>');
                        $line = $('#' + args.id);
                    }

                    var finalFileId = '';
                    if (args.code == Code.UPLOAD_WAIT) {
                        // $('#pickfiles').addClass()
                        qcVideo.uploader.startUpload();
                    }


                    if (args.code == Code.UPLOAD_DONE) {
                        finalFileId = '文件ID>>' + args.serverFileId;
                        serverFileId = args.serverFileId;
                        size_text = args.size_text;
                        size = args.size;
                        $("input[name='url']").val(serverFileId);
                        $("input[name='size_text']").val(size_text);
                        $("input[name='size']").val(size);
                    }
                    console.log(args);

                    $('#newresult').html('' +
                        '' + args.name + " (" + util.getHStorage(args.size) + ")"
                    );
                    $('#progress').html(

                        '状态：' + util.getFileStatusName(args.status) + '' +
                        (args.percent ? '<strong style="color: #92002c" > >> 进度：' + args.percent + '%' : '</strong>') +
                        (args.speed ? ' >> 速度：' + args.speed + '' : '') + finalFileId);
                },
                /**
                 * 文件状态发生变化
                 * @param info  { done: 完成数量 , fail: 失败数量 , sha: 计算SHA或者等待计算SHA中的数量 , wait: 等待上传数量 , uploading: 上传中的数量 }
                 */
                onFileStatus: function(info) {
                    console.log('各状态总数', info);
                },
                /**
                 *  上传时错误文件过滤提示
                 * -1: 文件类型异常,-2: 文件名异常 , message: 错误原因 ， solution: 解决方法 }
                 */
                onFilterError: function(args) {
                    layer.msg(args.message + (args.solution ? (';solution==' + args.solution) : '') || "提示信息！");
                    console.log('message:' + args.message + (args.solution ? (';solution==' + args.solution) : ''));
                }
            }
        );



        /**功能： 启动上传
        参数： 无;
        返回： 无;*/
        $("#start").click(function() {
            qcVideo.uploader.startUpload();
        })


        /**功能： 停止上传
        参数： 无;
        返回： 无;*/
        $("#stop").click(function() {
            qcVideo.uploader.stopUpload()
        });

        /**
        功能： 恢复上传（错误文件重新上传）
        参数： 无;
        返回： 无;
        */
        $("#restart").click(function() {
            qcVideo.uploader.reUpload();
        })

        /**功能： 删除本地上传任务
        参数： fid 文件id;
        返回： 无;*/
        $("#delit").click(function() {
            qcVideo.uploader.deleteFile(finalFileId)
        })
    </script>

<script type="text/javascript">
    $('.view_img').hover(function(e){
        var x = e.pageX;
        var y = e.pageY;
        if($(this).attr('data-url')){
            $(document.body).append("<div class='view_image_block'><img src='"+$(this).attr('data-url')+"' /> </div>");
            $('.view_image_block').css('left',x+'px');
            $('.view_image_block').css('top',y+'px');
        }

    },function(){
        $(".view_image_block").remove();
    });
</script>
</body>
</html>