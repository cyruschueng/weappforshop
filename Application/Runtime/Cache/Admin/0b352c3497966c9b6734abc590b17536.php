<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>帐号编辑-美客系统管理</title>
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
                <div class=""><a href="<?php echo U('/short_video_admin/index');?>" target="_self" class="btn" style="background: #e00214;color: #fff;">返回</a>
                </div>
                <div class="ibox-title">
                    <h5><a href="<?php echo U('/short_video_admin/index');?>" style="color: #e00214;"> 账号管理</a> >
                        <small>账号编辑</small>
                    </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="post" id="form1" name="edit" target="_self" action="<?php echo U('/short_video_admin/edit');?>"
                          class="form-horizontal">
                        <input type="hidden" name="id" value="<?php echo ($short_video_admin["id"]); ?>"/>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">登陆用户 <span style="color:#e91e63 ">*</span>：</label>

                            <div class="col-sm-6">
                                <input type="text" name="uname"  maxlength="26" onkeyup="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')"
                                       onpaste="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')"
                                       oncontextmenu="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')"
                                       value="<?php echo ($short_video_admin["uname"]); ?>" class="form-control relate-handle"
                                       required="" aria-required="true" placeholder="输入英文或者数字 不小于6个字符">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">用户昵称 <span style="color:#e91e63 ">*</span>：</label>

                            <div class="col-sm-6">
                                <input type="text" name="nickname"  maxlength="26"
                                       value="<?php echo ($short_video_admin["nickname"]); ?>" class="form-control relate-handle"
                                       required="" aria-required="true" placeholder="用户昵称">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">手机号 <span style="color:#e91e63 ">*</span>：</label>

                            <div class="col-sm-6">
                                <input type="text" name="mobile"
                                       value="<?php echo ($short_video_admin["mobile"]); ?>" class="form-control relate-handle"
                                       required="" aria-required="true" maxlength="11" placeholder="输入正确手机号">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">头像： <span style="color:#e91e63 ">*</span>：</label>

                            <div class="col-sm-6">
                                <img class="pic_view_detail" src="<?php if( $short_video_admin['pic']): echo ($short_video_admin['pic']); else: ?>/Public/images/header.jpg<?php endif; ?>" style="max-width: 320px; max-height: 320px;" />
                                <div style="margin: 4px; color:red">*推荐尺寸为：320*320</div>
                                <input type="hidden" id="pic" data-url="<?php echo ($short_video_admin["pic"]); ?>" name="pic" value="<?php echo ($short_video_admin["pic"]); ?>" class="view_img form-control" required="" aria-required="true" placeholder="请上传Banner图片" />
                                <span class="help-block m-b-none"><iframe src="<?php echo U('/admin/upload/show',array('sid'=>'short_video_banner','fileback'=>'pic'));?>" scrolling="no" topmargin="0" width="300" height="36" marginwidth="0" marginheight="0" frameborder="0" align="left" style="margin-top:3px; float:left"></iframe></span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">密码 <span style="color:#e91e63 ">*</span>：</label>

                            <div class="col-sm-6">
                                <input type="password" name="pwd"  maxlength="26" onkeyup="value=value.replace(/[^\a-\z\A-\Z0-9._]/g,'')"
                                       onpaste="value=value.replace(/[^\a-\z\A-\Z0-9._]/g,'')"
                                       oncontextmenu="value=value.replace(/[^\a-\z\A-\Z0-9._]/g,'')" value=""
                                       class="form-control  relate-handle"
                                       required="" aria-required="true" placeholder="输入英文或者数字或.或_ 不小于6个字符">
                            </div>
                        </div>
                        <?php if(!$short_video_admin): ?><div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">角色<span style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-6">
                                <label class="checkbox-inline i-checks">
                                    <input type="radio" name="role" <?php if( 4 == $short_video_admin[role]): ?>checked<?php endif; ?> value="4">OGC帐号</label>
                                <label class="checkbox-inline i-checks">
                                    <input type="radio" name="role" <?php if( 1 == $short_video_admin[role]): ?>checked<?php endif; ?> value="1">系统管理员</label>
                            </div>
                        </div><?php endif; ?>

                        <?php if(!$short_video_admin OR $short_video_admin['role'] == 4): ?><div class="hr-line-dashed"></div>
                        <div class="form-group data-tv">
                            <label class="col-sm-3 control-label">所属节目<span style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-6">
                                <?php if(is_array($tv_list)): $i = 0; $__LIST__ = $tv_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tv): $mod = ($i % 2 );++$i;?><label class="checkbox-inline i-checks">
                                    <input type="radio" name="tv_id" <?php if( $tv[id] == $short_video_admin[tv_id]): ?>checked<?php endif; ?> value="<?php echo ($tv["id"]); ?>"><?php echo ($tv["tv_name"]); ?></label><?php endforeach; endif; else: echo "" ;endif; ?>
                                <label>* 仅系OGC账户才需要设置</label>
                            </div>
                        </div><?php endif; ?>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="button" class="btn" onclick="checktosubmit('<?php echo ($short_video_admin["id"]); ?>')" style="background: #e00214;color: #fff;">保存内容</button>
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


    <!-- layerDate plugin javascript -->
    <!--<script src="/Public/js/plugins/layer/laydate/laydate.js"></script>-->
    <!--<script src="/Public/js/plugins/layer/layer.js"></script>-->
    <script type="text/javascript">

        $(document).ready(function() {
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
        function checktosubmit(id) {

            var isSuccess = 1;
            if (id == '') {
                if ($("input[name='uname']").val().length < 6) {
                    layer.msg('帐号名称长度不能够小于6个字符' || "提示信息！");

                    isSuccess = 0;
                    return;
                }
            }

            if (id == '') {
                if ($("input[name='pwd']").val().length < 6) {
                    layer.msg('密码长度不能够小于6' || "提示信息！");

                    isSuccess = 0;
                    return;
                }
            }

            if (isSuccess == 1) {
                form1.submit();
            }
        }
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