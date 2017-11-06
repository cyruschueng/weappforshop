<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>帐号管理-美客系统管理</title>
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
                    <h5>帐号列表</h5>

                    <div class="ibox-content">
                        <div class="">
                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover " id="editable">
                        <thead>
                            <tr>

                                <th style="white-space: nowrap; text-align: center">
                                    <button type="button" class="btn" id="delete-short_video_admin" style="background: #e00214;color: #fff;">删除</button>
                                </th>
                                <th>用户编号</th>
                                <th>用户名</th>
                                <th>角色</th>
                                <th>手机</th>
                                <th>所属公司</th>
                                <th>身份证件</th>
                                <th>营业执照</th>
                                <th>作品链接</th>
                                <th>用户状态</th>
                                <th>注册时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr class="gradeX" id="<?php echo ($item["id"]); ?>">

                                    <td style="text-align: center">
                                        <input type="checkbox" name="delete-id[]" value="<?php echo ($item['id']); ?>">
                                    </td>
                                    <td><?php echo ($item["id"]); ?></td>
                                    <td><?php echo ($item["uname"]); ?></td>
                                    <td>
                                        <?php if(2 == $item[role]): ?>PGC帐号个人
                                            <?php else: ?> PGC帐号公司<?php endif; ?>
                                    </td>
                                    <td><?php echo ($item["mobile"]); ?></td>
                                    <td><?php echo ($item["company"]); ?></td>
                                    <td><?php if($item.identity): ?><a href="<?php echo ($item["identity"]); ?>" target="_blank"><img src="<?php echo ($item["identity"]); ?>" style="max-height: 200px; max-width: 200px;" /></a><?php endif; ?></td>
                                    <td><?php if($item.licence): ?><a href="<?php echo ($item["identity"]); ?>" target="_blank"><img src="<?php echo ($item["licence"]); ?>" style="max-height: 200px; max-width: 200px;"  /></a><?php endif; ?></td>
                                    <td><?php if($item[url]): ?><a href="<?php echo ($item["url"]); ?>" target="_blank">作品链接</a><?php endif; ?></td>
                                    <td><?php if($item[status] == '0'): ?>待审核<?php elseif($item[status] == 1): ?><font color="green">审核通过</font><?php else: ?><font color="red">审核驳回</font><?php endif; ?></td>
                                    <td><?php echo (date('Y-m-d H:i:s',$item["create_time"])); ?></td>
                                    <td class="center " style="white-space: nowrap; ">
                                        <?php if($item[status] == 0): ?><a href="<?php echo U( '/short_video_admin/checkuser', array( 'id'=>$item['id'],'status'=>1));?>" onclick="return confirm('您确定通过该用户申请吗？')"><i class="fa fa-check text-navy"></i> 审核通过</a>
                                        <a href="<?php echo U( '/short_video_admin/checkuser', array( 'id'=>$item['id'],'status'=>2));?>" onclick="return confirm('您确定驳回该用户申请吗？')"><i class="fa fa-check text-navy"></i> 审核驳回</a><?php endif; ?>
                                        <a href="<?php echo U( '/short_video_admin/edit', array( 'id'=>$item['id']));?>"><i class="fa fa-check text-navy"></i> 编辑</a>
                                        <a href="<?php echo U( '/short_video_admin/delete', array( 'list'=>$item['id']));?>"  onclick="return confirm('您确定删除该用户申请吗？')"><i class="fa fa-check text-navy"></i> 删除</a>
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
            window.location.href = "<?php echo U('/short_video_admin/index');?>&class=" + $(this).val();
        });

        $("#delete-short_video_admin").click(function() {
            if (!confirm("确定要删除吗？")) {
                return false;
            }

            var idList = [];
            $("input[name='delete-id[]']:checked").each(function() {
                idList.push($(this).val());
            });
            window.location.href = "/index.php?s=/short_video_admin/delete/list/" + idList.toString() + ".html";
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