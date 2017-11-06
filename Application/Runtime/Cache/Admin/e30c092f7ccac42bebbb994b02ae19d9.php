<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>屏蔽词管理-美客系统管理</title>
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
    
    <style>.ibox .open > .dropdown-menu{ left: 0!important;} .ml6{ margin-left: 6px;}</style>

    <base target="_self">
    <style type="text/css">
        .view_image_block{ position: absolute; width: 200px; height: 200px; display: table-cell; overflow: hidden; background: #fff; border: 1px #ddd solid; z-index: 1000;}
        .view_image_block img{ max-width: 200px; text-align: center; margin: 0 auto;}
        .ticketinfo{display:none;}
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    
    <!--//宜-->
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>屏蔽词管理：</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-3 m-b-xs">
                            <div class="input-group" >
                                <textarea data-role="content" placeholder="如果添加多个屏蔽词，每行一个" style="width: 735px; height: 196px;" class="input-sm form-control" type="text"></textarea>
                                <span class="input-group-btn col-sm-3 m-b-xs" style="margin-left: 0; padding-left: 0; margin-top: 10px">
                                        <button type="button" data-role="badword" class="btn btn-sm" style="background: #e00214;color: #fff;"> 新增屏蔽词</button>
                                <a type="button" data-role="badword" class="btn btn-sm btn-default" style="margin-left: 10px;" href="<?php echo U('/short_video_admin/badword',['show'=>'yes']);?>">点击查看</a> </span>
                            </div>
                        </div>

                    </div>
                    <hr />
                    <div class="row">
                        <input type="text" name="kw"  class="input-sm form-control" style="width: 200px;" value="<?php echo ($kw); ?>" /> <button type="button" data-role="search" class="btn btn-sm"> 搜索</button>
                    </div>
                    <div class="row">

                    <?php if(is_array($badword_list)): $i = 0; $__LIST__ = $badword_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$badword): $mod = ($i % 2 );++$i;?><div class="btn-group ml6">
                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"><?php echo ($badword["badword"]); ?> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-id="<?php echo ($badword["id"]); ?>" data-role="delword">删除</a>
                                </li>
                            </ul>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                    <div class="row">
                        <div class="pages">
                            <?php echo ($page); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--//宜-->

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
        $('button[data-role="search"]').click(function(){
            var kw = $('input[name="kw"]').val();
            window.location.href = "/index.php?s=/short_video_admin/badword&show=yes&kw="+kw;
        });
        $('button[data-role="badword"]').click(function () {
            var content = $.trim($('textarea[data-role="content"]').val());
            if (content == '') {
                parent.layer.msg("请输入屏蔽词信息~");
                return false;
            }
            var loading = layer.load();

            $.ajax({
                type: "post",
                dataType: "json",
                data: {content: content},
                url: "<?php echo U('/short_video_admin/dobadword');?>",
                success: function (data) {
                    layer.close(loading);
                    if (data.state == 200) {
                        parent.layer.msg("添加成功~");
                        window.location.reload();
                    } else {
                        parent.layer.msg(data.msg);
                    }
                },
                error: function () {
                    layer.close(loading);
                    // alert('删除失败，稍后再试');
                }
            });
        });

        $('a[data-role="delword"]').click(function(){
            if (!confirm("确定要删除吗？")) {
                return false;
            }
            var id = $(this).attr('data-id');
            var loading = layer.load();
            $.ajax({
                type: "post",
                dataType: "json",
                data: {id: id},
                url: "<?php echo U('/short_video_admin/delbadword');?>",
                success: function (data) {
                    layer.close(loading);
                    if (data.state == 200) {
                        parent.layer.msg("删除成功~");
                        window.location.reload();
                    } else {
                        parent.layer.msg(data.msg);
                    }
                },
                error: function () {
                    layer.close(loading);
                    // alert('删除失败，稍后再试');
                }
            });

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