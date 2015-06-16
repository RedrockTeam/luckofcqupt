<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>校友</title>
    <script src="/luckofcqupt/Public/js/lib/init.js"></script>
    <link rel="stylesheet" type="text/css" href="/luckofcqupt/Public/css/lib/base.css">
    <link rel="stylesheet" type="text/css" href="/luckofcqupt/Public/css/alumnus.css">
</head>
<body>
<div class="s-wrap">
    <header class="u-menu">
        <a href="<?php echo U('Index/findSchoolfellow');?>" class="u-left"></a>
        <span>校友详细信息</span>
    </header>
    <div class="u-b_s" style="background-image: url('<?php echo ($info["photo"]); ?>')">  <!--外层头像-->
        <i class="s-h_c" style="background-image: url('<?php echo ($info["photo"]); ?>')"></i><!--内层头像-->
    </div>
    <div class="t-i">
        <div class="row">
            <div class="col">姓名</div>
            <div class="col"><?php echo ($info['name'] == null ? '他还没填名字' : $info['name']); ?></div>
        </div>
        <div class="row">
            <div class="col">性别</div>
            <div class="col"><?php echo ($info['sex'] == null ? '他还没填性别' : $info['sex']); ?></div>
        </div>
        <div class="row">
            <div class="col">家乡</div>
            <div class="col"><?php echo ($info['hometown'] == null ? '他还没填家乡' : $info['hometown']); ?></div>
        </div>
        <div class="row">
            <div class="col">联系方式</div>
            <div class="col"><?php echo ($info['contact'] == null ? '他还没填联系方式' : $info['contact']); ?> <?php echo ($info['contact'] == null ? '' : $info['way']); ?> </div>
        </div>
        <div class="row">
            <div class="col">简介</div>
            <div class="col"><?php echo ($info['introduce'] == null ? '他还没填简介' : $info['introduce']); ?></div>
        </div>
    </div>
</div>
</body>
</html>