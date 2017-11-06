<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品编辑-美客系统管理</title>
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
                <div class=""> <a href="<?php echo U('/shop_goods/index');?>" target="_self" class="btn" style="background: #e00214;color: #fff;">返回</a> </div>
                <div class="ibox-title">
                    <h5><a href="<?php echo U('/shop_goods/index');?>" style="color: #e00214;">添加商品</a> > <small>商品编辑</small></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="post" id="form1" name="edit" target="_self" action="<?php echo U('/shop_goods/edit');?>" class="form-horizontal">
                        <input type="hidden" name="id" value="<?php echo ($shop_goods["id"]); ?>" />

                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称 <span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-8">
                                <input type="text" name="name" value="<?php echo ($shop_goods["name"]); ?>" class="form-control" required="" aria-required="true" placeholder="输入标题">
                            </div>
                        </div>
                  
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">上传素材： <span  style="color:#e91e63 ">*</span>：</label>
              
                            <div class="col-sm-3">
                                <img class="pic_view_detail  pic_kdd" src="<?php if($shop_goods['pic']): echo ($shop_goods['pic']); else: ?>/Public/img/4041.jpg<?php endif; ?>" style="max-width: 320px; max-height: 160px;" />
                                <div style="margin: 4px; color:red">*标准尺寸为：640*320</div>
                                <input type="hidden" id="pic" data-url="<?php echo ($shop_goods["pic"]); ?>" name="pic" value="<?php echo ($shop_goods["pic"]); ?>" class="view_img form-control" required="" aria-required="true" placeholder="请上传商品图片">
                                <span class="help-block m-b-none"><iframe src="<?php echo U('/admin/upload/show',array('sid'=>'shop_goods','fileback'=>'pic'));?>" scrolling="no" topmargin="0" width="300" height="36" marginwidth="0" marginheight="0" frameborder="0" align="left" style="margin-top:3px; float:left"></iframe></span>
                            </div>
                            <div class="col-sm-3">
                                <img class="pic_view_detail1 pic_kdd" src="<?php if($shop_goods['pic1']): echo ($shop_goods['pic1']); else: ?>/Public/img/4041.jpg<?php endif; ?>" style="max-width: 320px; max-height: 160px;" />
                                <div style="margin: 4px; color:red">*标准尺寸为：640*320</div>
                                <input type="hidden" id="pic1" data-url="<?php echo ($shop_goods["pic"]); ?>" name="pic1" value="<?php echo ($shop_goods["pic1"]); ?>" class="view_img form-control" required="" aria-required="true" placeholder="请上传商品图片">
                                <span class="help-block m-b-none"><iframe src="<?php echo U('/admin/upload/show',array('sid'=>'shop_goods','fileback'=>'pic1','i'=>'1'));?>" scrolling="no" topmargin="0" width="300" height="36" marginwidth="0" marginheight="0" frameborder="0" align="left" style="margin-top:3px; float:left"></iframe></span>
                            </div>
                            
                            
                            <div class="col-sm-3">
                                <img class="pic_view_detail2 pic_kdd" src="<?php if($shop_goods['pic2']): echo ($shop_goods['pic2']); else: ?>/Public/img/4041.jpg<?php endif; ?>" style="max-width: 320px; max-height: 160px;" />
                                <div style="margin: 4px; color:red">*标准尺寸为：640*320</div>
                                <input type="hidden" id="pic2" data-url="<?php echo ($shop_goods["pic"]); ?>" name="pic2" value="<?php echo ($shop_goods["pic2"]); ?>" class="view_img form-control" required="" aria-required="true" placeholder="请上传商品图片">
                                <span class="help-block m-b-none"><iframe src="<?php echo U('/admin/upload/show',array('sid'=>'shop_goods','fileback'=>'pic2','i'=>'2'));?>" scrolling="no" topmargin="0" width="300" height="36" marginwidth="0" marginheight="0" frameborder="0" align="left" style="margin-top:3px; float:left"></iframe></span>
                            </div>
                        </div>

               <div class="hr-line-dashed"></div>
                        <div class="form-group">
                       
                            <label class="col-sm-2 control-label">分类<span  style="color:#e91e63 ">*</span>：</label>
                            <div class="col-sm-4">
                                <?php if(is_array($shop_goods_tag)): $i = 0; $__LIST__ = $shop_goods_tag;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($i % 2 );++$i;?><label class="checkbox-inline i-checks">
                                    <input type="radio" name="category_id" <?php if($tag[id] == $shop_goods[category_id]): ?>checked<?php endif; ?> value="<?php echo ($tag[id]); ?>"><?php echo ($tag["name"]); ?></label><?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                
                        </div>
       <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">价格： </label>
                            <div class="col-sm-8">
                                <input type="text" name="minprice" value="<?php echo ($shop_goods["minprice"]); ?>" class="form-control" required="" aria-required="true" placeholder="权重越大越靠前展示">
                            </div>
                        </div>

                 <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">详情：</label>
                            <div class="col-sm-10">
                                <script type="text/plain" id="content"  name="content"><?php echo ($shop_goods["content"]); ?></script>
                                <span class="help-block m-b-none"></span> </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">权重： </label>
                            <div class="col-sm-8">
                                <input type="text" name="order" value="<?php echo ($shop_goods["order"]); ?>" class="form-control" required="" aria-required="true" placeholder="权重越大越靠前展示">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                               <div class="form-group">
                            <label class="col-sm-2 control-label">库存： </label>
                            <div class="col-sm-8">
                                <input type="text" name="stores" value="<?php echo ($shop_goods["stores"]); ?>" class="form-control" required="" aria-required="true" placeholder="100">
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





      var editor_a = UE.getEditor('content', {
                initialFrameHeight: 100,
                serverUrl: '/Public/js/plugins/ueditor/php/controller.php'
            });
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