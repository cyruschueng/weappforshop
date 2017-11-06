<?php if (!defined('THINK_PATH')) exit();?><link rel="shortcut icon" href="favicon.ico">
<link href="/Public/css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
<link href="/Public/css/font-awesome.css?v=4.4.0" rel="stylesheet">
<!-- Data Tables -->
<link href="/Public/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
<link href="/Public/css/animate.css" rel="stylesheet">
<link href="/Public/css/style.css?v=4.0.0" rel="stylesheet">
<style>
    body{
        background: #0F1217;height: 100%; width: 100%; margin: 0px; padding: 0px ;
    }
    .block{
        position: relative;
        width: 75px;
        height: 24px;
        float: left;
    }
    input[type="file"]{
        position:absolute;
        right:0;
        top:0;
        font-size:14px;
        opacity:0;
        filter:alpha(opacity=0);
    }
</style>

<form action="<?php echo U('/login/upload');?>" method="post" enctype="multipart/form-data" name="myform" id="myform" style="height: 100%; width: 100%">
    <input name="sid" type="hidden" value="<?php echo (htmlspecialchars($_GET['sid'])); ?>"/>
    <input name="fileback" type="hidden" value="<?php echo (htmlspecialchars($_GET['fileback'])); ?>"/>

    <div class="btn btn-default  block" style="float: left; width: 70px; height: 24px;padding: 0;">
        浏览
        <input type="file" name="upthumb" value="浏览" />
    </div>
</form>
<script src="/Public/js/jquery.min.js?v=2.1.4"></script>
<script src="/Public/js/bootstrap.min.js?v=3.3.5"></script>
<script type="text/javascript">
    $('input[name="upthumb"]').change(function(){
        $('form[name="myform"]').submit();
    });
</script>