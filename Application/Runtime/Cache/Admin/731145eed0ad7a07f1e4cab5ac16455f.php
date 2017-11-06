<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品管理-美客系统管理</title>
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
                <div class="ibox-title">
                    <h5>商品列表</h5>

                    <div class="ibox-content">
                        <div class="">
                            <!--<a href="<?php echo U('/shop_goods/edit');?>" target="_self" class="btn" style="background: #e00214;color: #fff;">添加banner</a>-->
                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover " id="editable">
                        <thead>
                            <tr>
                                <th>名称</th>
                     
                                <th>图片</th>
                                <th>权重</th>
                                <th>定价</th>
                                <th>库存</th>
                                <th>操作</th>
                                <th style="white-space: nowrap; text-align: center">
                                    <button type="button" class="btn" id="delete-shop_goods" style="background: #e00214;color: #fff;">删除</button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><td><?php echo (mb_substr($item["name"],0,30)); ?></td>
                                    <td>
                                    
                                        <?php if( $item["pic"] ): ?><img src="<?php echo ($item["pic"]); ?> " class="view_img " data-url="<?php echo ($item["pic"]); ?> " style="max-width: 100px; max-height: 80px " /><?php endif; ?>
                                    </td>
                                    <td><?php echo ($item["order"]); ?></td>
                                    
                                    <td><?php echo ($item["minprice"]); ?></td>
                                    <td><?php echo ($item["stores"]); ?></td>
                                    <td class="center " style="white-space: nowrap; ">
                                        <a href="<?php echo U( '/shop_goods/edit', array( 'id'=>$item['id']));?>"><i class="fa fa-check text-navy"></i> 编辑</a>
                                    </td>
                                    
                                    <td style="text-align: center">
                                        <input type="checkbox" name="delete-id[]" value="<?php echo ($item['id']); ?>">
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table>
                    <div class="row ">
                        <div class="pages">
                            <?php echo ($page); ?>
                        </div>
                    </div>
                </div>
            </div>
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

    <script type="text/javascript">
        $('select[name="search"]').change(function() {
            window.location.href = "<?php echo U('/shop_goods/index');?>&class=" + $(this).val();
        });

        $("#delete-shop_goods").click(function() {
            if (!confirm("确定要删除吗？")) {
                return false;
            }

            var idList = [];
            $("input[name='delete-id[]']:checked").each(function() {
                idList.push($(this).val());
            });
            window.location.href = "/index.php?s=/shop_goods/delete/list/" + idList.toString() + ".html";
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