<?php if (!defined('THINK_PATH')) exit();?><link href="/Public/css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
<link href="/Public/css/font-awesome.css?v=4.4.0" rel="stylesheet">
<link href="/Public/css/plugins/iCheck/custom.css" rel="stylesheet">
<link href="/Public/css/animate.css" rel="stylesheet">
<link href="/Public/css/style.css?v=4.0.0" rel="stylesheet">
<style type="text/css">
    .pages{ margin-right: 8px; width: 98%!important;}
    li{ float: left; width: 82px; height: 82px; overflow: hidden; margin: 4px; display: table-cell; list-style: none; border:#999 1px solid; padding: 1px;}
    li img{ display: block; margin: 1px; padding: 1px;}
</style>
<div class="ibox-content">

<?php if(is_array($data['list'])): $i = 0; $__LIST__ = $data['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><li data-url="<?php echo ($item["url"]); ?>" class="fancybox"><img src="<?php echo ($item["url"]); ?>" style="width: 80px;"></li><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div class="pages" id="pages"><?php echo ($page); ?></div>
<script src="/Public/js/jquery.min.js?v=2.1.4"></script>
<script type="text/javascript">
    $('li').click(function(){
        parent.$('#<?php echo ($fileback); ?>').val($(this).attr('data-url'));
        parent.$('#<?php echo ($fileback); ?>').siblings('.pic_kdd').attr('src',$(this).attr('data-url'));

        parent.layer.close(parent.index);
    });
</script>