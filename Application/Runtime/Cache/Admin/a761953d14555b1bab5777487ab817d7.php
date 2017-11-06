<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner编辑-美客系统管理</title>
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
                <div class=""> <a href="<?php echo U('/short_vbanner/index');?>" target="_self" class="btn" style="background: #e00214;color: #fff;">返回</a> </div>
                <div class="ibox-title">
                    <h5><a href="<?php echo U('/short_vbanner/index');?>" style="color: #e00214;">添加Banner</a> > <small>Banner编辑</small></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="post" id="form1" name="edit" target="_self" action="<?php echo U('/short_vbanner/edit');?>" class="form-horizontal">
                        <input type="hidden" name="id" value="<?php echo ($short_vbanner["id"]); ?>" />

                        <div class="form-group">
                            <label class="col-sm-2 control-label">标题 <span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-8">
                                <input type="text" name="title" value="<?php echo ($short_vbanner["title"]); ?>" class="form-control" required="" aria-required="true" placeholder="输入标题">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">发布平台：</label>
                            <div class="col-sm-4">
                                <label class="checkbox-inline i-checks">
                                    <input type="radio"  name="platform" <?php if(empty($short_vbanner['platform'])): ?>checked<?php endif; ?> value="0" /> 不限</label>
                                <label class="checkbox-inline i-checks">
                                    <input type="radio" name="platform" <?php if($short_vbanner['platform'] == 1): ?>checked<?php endif; ?> value="1" /> 短秀H5</label>
                                <label class="checkbox-inline i-checks">
                                    <input type="radio" name="platform" <?php if($short_vbanner['platform'] == 2): ?>checked<?php endif; ?> value="2" /> 短秀App</label>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">上传素材： <span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-10">
                                <img class="pic_view_detail" src="<?php if($short_vbanner['image']): echo ($short_vbanner['image']); else: ?>/Public/img/4041.jpg<?php endif; ?>" style="max-width: 320px; max-height: 160px;" />
                                <div style="margin: 4px; color:red">*标准尺寸为：640*320</div>
                                <input type="hidden" id="image" data-url="<?php echo ($short_vbanner["image"]); ?>" name="image" value="<?php echo ($short_vbanner["image"]); ?>" class="view_img form-control" required="" aria-required="true" placeholder="请上传Banner图片">
                                <span class="help-block m-b-none"><iframe src="<?php echo U('/admin/upload/show',array('sid'=>'short_video_banner','fileback'=>'image'));?>" scrolling="no" topmargin="0" width="300" height="36" marginwidth="0" marginheight="0" frameborder="0" align="left" style="margin-top:3px; float:left"></iframe></span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">跳转类型： <span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-8">
                                <select class="form-control m-b" name="jump_type">
                                    <option value="0" <?php if($short_vbanner['jump_type'] == '0'): ?>selected<?php endif; ?>>普通链接</option>
                                    <option value="1" <?php if($short_vbanner['jump_type'] == '1'): ?>selected<?php endif; ?>>视频详情</option>
                                    <option value="2" <?php if($short_vbanner['jump_type'] == '2'): ?>selected<?php endif; ?>>我要上电视</option>
                                </select>
                                <label>* 视频详情对应跳转链接为视频ID,我要上电视对应跳转链接为活动ID</label>
                            </div>
                        </div>


                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">跳转链接： <span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-8">
                                <input type="text" name="url" value="<?php echo ($short_vbanner["url"]); ?>" class="form-control" required="" aria-required="true" placeholder="输入一个可以在浏览器中打开的网址">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">权重： </label>
                            <div class="col-sm-8">
                                <input type="text" name="order" value="<?php echo ($short_vbanner["order"]); ?>" class="form-control" required="" aria-required="true" placeholder="权重越大越靠前展示">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group" style="display: none">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10">
                                <input id="hello" class="laydate-icon form-control layer-date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">时间区间：</label>
                            <div class="col-sm-10">
                                <input placeholder="开始日期" name="begin_time" value="<?php echo ($short_vbanner["begin_time"]); ?>" class="form-control layer-date" id="start" required="" aria-required="true">
                                <input placeholder="结束日期" name="end_time" value="<?php echo ($short_vbanner["end_time"]); ?>" class="form-control layer-date" id="end" required="" aria-required="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn" type="submit" style="background: #e00214;color: #fff;">保存内容</button>
                                <!--<button class="btn btn-white" type="reset">取消</button>-->
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



    <script type="text/javascript">
        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }






        //外部js调用
        laydate({
            elem: '#hello', //目标元素。由于laydate.js封装了一个轻量级的选择器引擎，因此elem还允许你传入class、tag但必须按照这种方式 '#id .class'
            event: 'focus' //响应事件。如果没有传入event，则按照默认的click
        });
        //日期范围限制
        var start = {
            elem: '#start',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: laydate.now(), //设定最小日期为当前日期
            max: '2099-06-16 23:59:59', //最大日期
            istime: true,
            istoday: false,
            choose: function(datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#end',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: laydate.now(),
            max: '2099-06-16 23:59:59',
            istime: true,
            istoday: false,
            choose: function(datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);
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