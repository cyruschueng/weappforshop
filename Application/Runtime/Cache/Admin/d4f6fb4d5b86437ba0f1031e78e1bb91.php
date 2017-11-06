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
                    <h5>我要上电视</h5>

                    <div class="ibox-content">
                        <div class=""> <a href="<?php echo ($jump_ipm_url); ?>" target="_self" class="btn" style="background: #e00214;color: #fff;">发起新一期征集活动</a>
                            <div class="input-group  col-sm-6 pull-right form-group" id='pickfiles'>
                                <div class="col-sm-3 padding-item">
                             <select class="form-control " name="tv_id">
                                  <option value="0">所有节目</option>
                                  <?php if(is_array($tv_list)): $i = 0; $__LIST__ = $tv_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tv): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tv["id"]); ?>" <?php if($tv_id == $tv['id']): ?>selected<?php endif; ?>><?php echo ($tv["tv_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                             </select>
                                    </div>

                                <div class="col-sm-3 padding-item">
                                    <select class="form-control " name="status">
                                        <option value="0">所有状态</option>
                                        <option value="1" <?php if($status == 1): ?>selected<?php endif; ?>>已上架</option>
                                        <option value="4" <?php if($status == 4): ?>selected<?php endif; ?>>已下架</option>
                                        <option value="9" <?php if($status == 9): ?>selected<?php endif; ?>>待转码</option>
                                        <option value="5" <?php if($status == 5): ?>selected<?php endif; ?>>转码失败</option>
                                        <option value="2" <?php if($status == 2): ?>selected<?php endif; ?>>审核驳回</option>
                                        <option value="7" <?php if($status == 7): ?>selected<?php endif; ?>>举报下架</option>
                                    </select>
                                </div>
                                <div class="col-sm-5 padding-item">
                                <input class="form-control" name="kw" value="<?php echo ($kw); ?>"  placeholder="视频标题" type="text" />
                            </div>

                            <div class="input-group-btn">
                              <button type="submit" data-role="search" class="btn btn-sm" style="background: #e00214;color: #fff;">
                                搜索
                              </button>
                            </div>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover " id="editable">
                            <thead>
                                <tr>
                                    <th>视频ID</th>
                                    <th>视频信息</th>
                                    <th>电视台</th>
                                    <th>视频时长</th>
                                    <th>视频大小</th>
                                    <th>是否下载</th>
                                    <th>是否征用</th>
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
                                            <div style="float: left; clear: both; margin: 6px 0; line-height: 22px; font-size: 12px"><span style="background: red; padding: 4px; color: #fff; border-radius: 10px; font-size: 11px;">我要上电视</span> <span style="padding: 4px; color: #999"><?php echo getActiveName($item['active_id'])['active_name'];?></span> <span style="color: #999"><?php echo date("Y-m-d H:i",$item['create_time']);?></span></div>
                                        </td>
                                        <td><?php echo getTvName($item[tv_id])['tv_name'];?></td>
                                        <td><?php echo tans_human_minutes($item[duration]);?></td>
                                        <td><?php echo ($item["size_text"]); ?></td>
                                        <td>
                                            <?php if($item["is_down"] == 1): ?><font color="green" >已下载</font><?php else: ?><font color="#a9a9a9" >未下载</font><?php endif; ?>
                                            <?php if($item["is_hot"] == 1): ?><font color="red" >[推荐]</font><?php endif; ?>
                                        </td>
                                        <td><?php if($item['is_used'] == 1): ?><font color="green">已征用</font><?php else: ?><font color="#a9a9a9" >未征用</font><?php endif; ?></td>
                                        <td><?php echo get_video_status($item[status]);?></td>
                                        <td align="center">
                                            <?php if($item.pic): ?><div><img src="<?php echo ($item["pic"]); ?>" class="view_img" data-url="<?php echo ($item["pic"]); ?>" style="max-width: 100px; max-height: 80px" /></div><?php endif; ?>
                                            <?php if($item.ourl): ?><div><a href="<?php echo U('/short_video/downvideo',array('id'=>$item[id]));?>" style="padding: 8px; color: #666; font-size: 12px;">点击下载</a></div><?php endif; ?>
                                        </td>
                                        <td class="center" style="white-space: nowrap;">
                                            <?php if(0 == $item[status]): ?>转码中...
                                            <?php elseif(-1 == $item[status]): ?>
                                                <a href="<?php echo U('/short_video/edit', array('id'=>$item['id']));?>"> 重传视频</a>
                                            <?php else: ?>
                                                <?php if(1 == $item['status']): ?><a href="<?php echo U('/short_video/drop', array('id'=>$item['id'],'status'=>4));?>" onclick="return confirm('确定要下架该视频吗？')">下架</a>
                                                <?php elseif(4 == $item['status']): ?>
                                                    <a href="<?php echo U('/short_video/drop', array('id'=>$item['id'],'status'=>1));?>" onclick="return confirm('确定要上架该视频吗？')"> 上架</a><?php endif; ?>
                                                <?php if($item['is_used'] == 0): ?><a href="<?php echo U('/short_video/usevideo', array('id'=>$item['id']));?>" onclick="return confirm('确定要征用该视频吗？')">征用该视频</a><?php endif; ?>
                                                <a href="<?php echo U('/short_video/del', array('id'=>$item['id']));?>" onclick="return confirm('确定要删除该视频吗？')"> 删除</a>
                                                <a href="<?php echo U('/short_video_comment/index', array('id'=>$item['id']));?>"> 管理评论</a><?php endif; ?>
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
            window.location.href = "<?php echo U('/short_video/tvlist');?>&tv_id=" + $('select[name="tv_id"]').val()+"&kw="+$('input[name="kw"]').val()+"&status=" + $('select[name="status"]').val();
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