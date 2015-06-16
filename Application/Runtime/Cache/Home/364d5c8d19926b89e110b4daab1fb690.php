<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>首页</title>
    <script src="/luckofcqupt/Public/js/lib/init.js"></script>
    <link rel="stylesheet" type="text/css" href="/luckofcqupt/Public/css/lib/base.css">
    <link rel="stylesheet" type="text/css" href="/luckofcqupt/Public/css/p_list.css">
</head>
<body>
<div class="s-wrap">
    <header class="u-menu">
        <a href="<?php echo U('Index/index');?>?openid=<?php echo session('info.openid');?>" class="u-left"></a>
        <i class="s-logo"></i>
        <span class="u-s_all js-s_all">
          全部
        </span>
    </header>
    <div class="m-lists js-w_l">
        <?php if(is_array($friend)): $i = 0; $__LIST__ = $friend;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="u-item f-cb">
                <a href="<?php echo U('Index/showDetail');?>?id=<?php echo ($vo["id"]); ?>">
                    <i class="s-icon" style="background-image: url(' <?php echo ($vo["photo"]); ?> ')"></i>
                    <aside>
                        <h2 class="name"><span><?php echo ($vo['name']!=null?$vo['name']:'他还没填名字'); ?></span> <?php echo ($vo['sex']!='女'?'<i class="i_m"></i>':'<i class="i_w"></i>'); ?> <span class="s-sl">距离   100米<!--空格不要省略--></span></h2>
                        <span class="trip s-gap"><?php echo ($vo["stuid"]); ?></span>
                        <span class="trip"><?php echo ($vo['college']?$vo['college']:'他还没填学院'); ?></span>
                    </aside>
                </a>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <div class="u-loading js-loading">
        <div class="w-l f-cb"><i class="s-l_i"></i><span>正在加载中...</span></div>
    </div>
    <!--选择-->
    <div class="m-s_t js-w_all">
        <header class="u-s_c">
            <a href="javascript:void(0)" class="u-left js-close"></a>
            <a class="f-sub js-sub" href="javascript:void(0)">完成</a>
            <span class="title">类型 (多选)</span>
        </header>
        <dl>
            <!--将 类型id存在data-id上, 以供数据传输-->
            <dd class="z-active" data-id="1"><span>全部</span><i class="s-add"></i></dd>
            <dd class="item" data-id="2"><span>老乡</span><i class="s-add"></i></dd>
            <dd class="item" data-id="3"><span>男</span><i class="s-add"></i></dd>
            <dd class="item" data-id="4"><span>女</span><i class="s-add"></i></dd>
        </dl>
    </div>
</div>
</body>
<script>
    var type_path = '<?php echo U('Index/findSchoolfellow');?>';
    var people = '<?php echo U('Index/showDetail');?>';
</script>
<script src="/luckofcqupt/Public/js/p_list.js"></script>
</html>