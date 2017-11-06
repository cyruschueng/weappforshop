<?php if (!defined('THINK_PATH')) exit();?><link rel="shortcut icon" href="favicon.ico">
<link href="/Public/css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
<link href="/Public/css/font-awesome.css?v=4.4.0" rel="stylesheet">
<!-- Data Tables -->
<link href="/Public/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
<link href="/Public/css/animate.css" rel="stylesheet">
<link href="/Public/css/style.css?v=4.0.0" rel="stylesheet">
<style>
    .block{ position: relative; width: 100px; height: 35px; float: left; margin-right: 8px;}
    input[type="file"]{position:absolute; right:0; top:0; font-size:100px; opacity:0; filter:alpha(opacity=0);}
</style>

<form action="<?php echo U('/admin/upload/upload');?>" method="post" enctype="multipart/form-data" name="myform" id="myform">
    <input name="sid" type="hidden" value="<?php echo (htmlspecialchars($_GET['sid'])); ?>"/>
    <input name="fileback" type="hidden" value="<?php echo (htmlspecialchars($_GET['fileback'])); ?>"/>
 <input name="i" type="hidden" value="<?php echo (htmlspecialchars($_GET['i'])); ?>"/>
    <div class="btn btn-default  block">
        浏览
        <input type="file" name="upthumb" value="浏览" />
    </div>
&nbsp;
    <button style="float: left;" type="submit" class="btn btn-default "><i class="fa fa-upload"></i>&nbsp;&nbsp;<span class="bold">上传</span></button>
    <button style="float: left; margin-left: 2px;background: #e00214;color: #fff;" type="button" id="dd" class="btn"><i class="fa fa-upload"></i>&nbsp;&nbsp;<span class="bold">图片库</span></button>

</form>
<script src="/Public/js/jquery.min.js?v=2.1.4"></script>
<script src="/Public/js/bootstrap.min.js?v=3.3.5"></script>

<script type="text/javascript">
    $('#dd').click(function(){

        parent.index = parent.layer.open({
            type: 2,
            title: '图片管理',
            shadeClose: true,
            shade: 0.8,
            area: ['70%', '60%'],
            content: '<?php echo U("upload/select",array("sid"=>$sid,"fileback"=>$fileback));?>' //iframe的url
        });
    });

</script>