<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>视频管理-美客系统管理</title>
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
                    <h5>视频举报</h5>

                    <div class="ibox-content">
                        <div class="">
                            <div class="input-group  col-sm-6 pull-right form-group" id='pickfiles'>

                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover " id="editable">
                            <thead>
                            <tr>
                                <th>视频ID</th>
                                <th>视频信息</th>
                                <th>视频时长</th>
                                <th>视频大小</th>
                                <th>播放</th>
                                <th>点赞</th>
                                <th>状态</th>
                                <th>封面</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr class="gradeX" url="<?php echo ($item["url"]); ?>">
                                    <td><?php echo ($item["id"]); ?></td>
                                    <td align="left" title="<?php echo ($item["title"]); ?>">
                                        <div style="float: left; clear: both; margin: 6px 0; line-height: 22px;"><b><?php echo (mb_substr($item["title"],0,30)); ?></b> <span style="padding-left: 12px;"><i class="fa fa-user"></i> <?php echo ((isset($item["username"]) && ($item["username"] !== ""))?($item["username"]):'未知'); ?></span></div>
                                        <div style="float: left; clear: both; margin: 6px 0; line-height: 22px; font-size: 12px"><span style="background: red; padding: 4px; color: #fff; border-radius: 10px; font-size: 11px;"><?php if($item.is_show): if($item["active_id"] > 0): ?>我要上电视<?php else: ?>用户上传<?php endif; else: ?>后台上传<?php endif; ?></span> <span style="padding: 4px; color: #666; border-radius: 10px; font-size: 11px;">举报次数：<?php echo ($item["total_report"]); ?></span> <span style="color: #999"><?php echo date("Y-m-d H:i",$item['create_time']);?></span></div>
                                        <div style="float: left; clear: both; margin: 6px 0; line-height: 22px; font-size: 12px">
                                            <?php $report_list = M('short_report')->where(['video_id'=>$item[id],'status'=>0])->group("report_type")->select(); ?>
                                            <?php if(is_array($report_list)): $i = 0; $__LIST__ = $report_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$report): $mod = ($i % 2 );++$i;?><span style="background: #999; padding: 4px; color: #fff; border-radius: 8px; font-size: 11px; margin: 6px 6px 0 0; "><?php echo getReportMsg($report);?></span><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo tans_human_minutes($item[duration]);?></td>
                                    <td><?php echo ($item["size_text"]); ?></td>
                                    <td>
                                        <?php echo ($item["play_num"]); ?>
                                    </td>
                                    <td><?php echo ($item["favorite_num"]); ?></td>
                                    <td><?php echo get_video_status($item[status]);?></td>
                                    <td align="center">
                                        <?php if($item.pic): ?><div><img src="<?php echo ($item["pic"]); ?>" class="view_img" data-url="<?php echo ($item["pic"]); ?>" style="max-width: 100px; max-height: 80px" /></div><?php endif; ?>
                                        <?php if($item.ourl): ?><div><a href="<?php echo ($item["ourl"]); ?>" style="padding: 8px; color: #666; font-size: 12px;">点击下载</a></div><?php endif; ?>
                                    </td>
                                    <td class="center" style="white-space: nowrap;">
                                        <a href="<?php echo U('/short_video/check_report', array('id'=>$item['id'],'status'=>9));?>" onclick="return confirm('确定要下架且屏蔽该视频发布者吗？')">下架且屏蔽发布者</a>
                                        <a href="<?php echo U('/short_video/check_report', array('id'=>$item['id'],'status'=>7));?>" onclick="return confirm('确定要下架该视频吗？')">审核下架</a>
                                        <a href="<?php echo U('/short_video/check_report', array('id'=>$item['id'],'status'=>1));?>" onclick="return confirm('确定要忽略该视频吗？')">忽略投诉</a>
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
        $('button[data-role="search"]').click(function() {
            window.location.href = "<?php echo U('/short_video/showvideo');?>&kw="+$('input[name="kw"]').val()+"&status=" + $('select[name="status"]').val();
        });

        $("#delete-short_video").click(function() {
            if (!confirm("确定要删除吗？")) {
                return false;
            }
            var idList = [];
            $("input[name='delete-id[]']:checked").each(function() {

                // qcVideo.uploader.deleteFile($(this).parent().parent().attr('url'));

                idList.push($(this).val());
            });
            window.location.href = "/index.php?s=/short_video/delete/list/" + idList.toString() + ".html";
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