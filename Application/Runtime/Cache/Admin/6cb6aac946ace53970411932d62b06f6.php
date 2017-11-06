<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>评论管理-美客系统管理</title>
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
                    <h5>评论管理</h5>

                    <div class="ibox-content">
                        <!--<div class=""> <a href="<?php echo U('/short_video_comment/edit');?>" target="_self" class="btn btn-primary ">添加banner</a>-->
                        <div class=""> <button type="button" class="btn" id="delete-short_video_comment" style="background: #e00214;color: #fff;">删除</button>

                        <div class="input-group  col-sm-6 pull-right form-group" id='pickfiles'>
                            <div class="col-sm-2 padding-item">
                                <select class="form-control " name="from_platform">
                                    <option value="0">视频来源</option>
                                    <option value="1" <?php if($from_platform == 1): ?>selected<?php endif; ?>>H5上传</option>
                                    <option value="2" <?php if($from_platform == 2): ?>selected<?php endif; ?>>APP上传</option>
                                    <option value="3" <?php if($from_platform == 3): ?>selected<?php endif; ?>>后台上传</option>
                                </select>
                            </div>

                            <div class="col-sm-2 padding-item">
                                <select class="form-control " name="platform">
                                    <option value="0">评论来源</option>
                                    <option value="1" <?php if($platform == 1): ?>selected<?php endif; ?>>H5评论</option>
                                    <option value="2" <?php if($platform == 2): ?>selected<?php endif; ?>>APP评论</option>
                                    <option value="3" <?php if($platform == 3): ?>selected<?php endif; ?>>后台评论</option>
                                </select>
                            </div>
                            <div class="col-sm-2 padding-item">
                                <select class="form-control " name="search">
                                    <option value="0">所有视频</option>
                                    <?php if(is_array($short_video_tag)): $i = 0; $__LIST__ = $short_video_tag;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tag["id"]); ?>" <?php if($class == $tag['id']): ?>selected<?php endif; ?>><?php echo ($tag["tag_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
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
                        </div></div>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover " id="editable" video_id='<?php echo ($id); ?>'>
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="selectall" value="" /></th>
                            <th>评论内容</th>
                            <th>评论视频</th>
                            <th>评论来源</th>
                            <th>视频来源</th>
                            <th>所在分类</th>
                            <th>评论时间</th>
                            <th style="text-align: center">置顶</th>
                            <th style="white-space: nowrap; text-align: center">
                                操作
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr class="gradeX" url="<?php echo ($item["url"]); ?>" id="<?php echo ($item["id"]); ?>">
                                <td><input type="checkbox" name="delete-id[]" value="<?php echo ($item['id']); ?>"></td>
                                <td><?php echo ($item["content"]); if($item['reply']): ?><div style="width:100%; display:inline-block; background:#fefefe; margin-left:-1px; ; float:left;border: 1px #ddd solid; padding: 6px; margin: 6px;"><?php echo ($item[reply]['nickname']); ?>：<?php echo ($item[reply]['content']); ?></div><?php endif; ?></td>
                                <td title="<?php echo ($item["title"]); ?>">【视频ID：<?php echo ($item["video_id"]); ?>】<?php echo (mb_substr($item["title"],0,20)); ?></td>
                                <td><?php if($item[platform] == 1): ?>H5评论<?php elseif($item[platform] == 2): ?>APP评论<?php elseif($item[platform] == 3): ?>后台评论<?php endif; ?></td>
                                <td><?php if($item[from_platform] == '0'): ?>后台上传<?php elseif($item[platform] == 1): ?>H5上传<?php else: ?>APP上传<?php endif; ?></td>
                                <td><?php if($item['type']): echo get_video_type($item['type']); else: ?>无<?php endif; ?></td>
                                <td><?php echo (date('Y-m-d H:i:s',$item["create_time"])); ?></td>
                                <td style="text-align: center">
                                    <?php if(1 == $item['is_top']): ?>是<?php else: ?>否<?php endif; ?>
                                </td>
                                <td style="text-align: center">
                                    <?php if($item['is_show'] == '0'): ?><a href="javascript:" data-id="<?php echo ($item["id"]); ?>" data-role="reply" >回复</a>
                                    <?php if(1 == $item['is_top']): ?><a href="<?php echo U('/short_video_comment/top', array('id'=>$item['id'],'checked'=>0));?>" onclick="return confirm('确定取消置顶吗？')">取消置顶</a>
                                    <?php else: ?>
                                        <a href="<?php echo U('/short_video_comment/top', array('id'=>$item['id'],'checked'=>1));?>" onclick="return confirm('确定置顶该评论吗？')">置顶</a><?php endif; endif; ?>

                                    <a href="<?php echo U('/short_video_comment/del', array('id'=>$item['id']));?>" onclick="return confirm('确定要删除该评论吗？')">删除</a>
                                </td>

                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
                <div class="row">
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
        // $('select[name="search"]').change(function() {
        //     window.location.href = "<?php echo U('/short_video_comment/index');?>&class=" + $(this).val();
        // });
        $('a[data-role="reply"]').click(function(){
            var _this = this;
            //页面层
            parent.layer.prompt({
                title: '请输入回复内容',
                formType: 0 //prompt风格，支持0-2
            }, function(text){
               var text = $.trim(text);
                if(!text){
                    return parent.layer.msg("请输入回复内容~");
                }
                var id = $(_this).attr('data-id');
                var loading = layer.load();
                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {id: id, text:text},
                    url: "<?php echo U('/short_video_comment/reply');?>",
                    success: function (data) {
                        layer.close(loading);
                        if (data.state == 200) {
                            parent.layer.msg("回复成功~");
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
        });
        $('button[data-role="search"]').click(function() {
            window.location.href = "<?php echo U('/short_video_comment/index');?>&class=" + $('select[name="search"]').val()
                    +"&kw="+$('input[name="kw"]').val()+"&platform="+$('select[name="platform"]').val()+"&from_platform="+$('select[name="from_platform"]').val();
        });

        $("#delete-short_video_comment").click(function() {
            if (!confirm("确定要删除吗？")) {
                return false;
            }

            var idList = [];
            $("input[name='delete-id[]']:checked").each(function() {
                idList.push($(this).val());
            });
            window.location.href = "/index.php?s=/short_video_comment/delete/list/" + idList.toString() + ".html" + '&id=' + $('#editable').attr('video_id');
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