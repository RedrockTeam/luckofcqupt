<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>重邮缘, 重邮人的朋友圈</title>
    <script src="/RedrockPro/luckofcqupt/Public/js/lib/init.js"></script>
    <link rel="stylesheet" type="text/css" href="/RedrockPro/luckofcqupt/Public/css/lib/base.css">
    <link rel="stylesheet" type="text/css" href="/RedrockPro/luckofcqupt/Public/css/index.css">
</head>
<body>

<div class="g-container">
     <div style="display:none">
        <img src="http://hongyan.cqupt.edu.cn/cquptluck/Public/images/index/share.jpg" style="width:300px;height:300px;">
    </div>
    <i class="s-logo"></i>
    <menu class="u-links">
        <a href="/RedrockPro/luckofcqupt/Home/Index/findSchoolfellow">寻找身边重邮人</a>
        <a href="/RedrockPro/luckofcqupt/Home/Index/findAllSchoolfellow">查找重邮人</a>
        <a href="/RedrockPro/luckofcqupt/Home/Index/information">完善或修改信息</a>
    </menu>
    <dl class="s-tips">
        <dt class="u-sth">使用说明</dt>
        <dd>1.关注重邮小帮手(cyxbswx),绑定学号打开提供"位置信息"</dd>
        <dd>2.打开手机GPS功能</dd>
    </dl>

    <!--<span class="s-comp">©红岩网校出品</span>-->
</div>
</body>
<script src="/RedrockPro/luckofcqupt/Public/js/index.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script>
    console.log(<?php echo ($signature); ?>);
        wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: 'wx81a4a4b77ec98ff4', // 必填，公众号的唯一标识
        timestamp: "<?php echo ($signature['timestamp']); ?>", // 必填，生成签名的时间戳
        nonceStr: "<?php echo ($signature['string']); ?>", // 必填，生成签名的随机串
        signature: "<?php echo ($signature['signature']); ?>",// 必填，签名，见附录1
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'hideAllNonBaseMenuItem'
        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    wx.ready(function(){
        wx.onMenuShareTimeline({
            title: '重邮缘, 重邮人的朋友圈', // 分享标题
            link: "http://hongyan.cqupt.edu.cn/cquptluck/Home/Index/index.html",
            imgUrl: "http://hongyan.cqupt.edu.cn/cquptluck/Public/images/index/share.jpg",
            success: function () {
                alert('分享成功!');// 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.onMenuShareAppMessage({
            title: '重邮缘, 重邮人的朋友圈', // 分享标题
            desc: '重邮缘, 重邮人的朋友圈, 快来寻找你的校友吧~', // 分享描述
            link: "http://hongyan.cqupt.edu.cn/cquptluck/Home/Index/index.html",
            imgUrl: 'http://hongyan.cqupt.edu.cn/cquptluck/Public/images/index/share.jpg', // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                alert('分享成功!');// 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.onMenuShareQQ({
            title: '重邮缘, 重邮人的朋友圈', // 分享标题
            desc: '重邮缘, 重邮人的朋友圈, 快来寻找你的校友吧~', // 分享描述
            link: "http://hongyan.cqupt.edu.cn/cquptluck/Home/Index/index.html",
            imgUrl: 'http://hongyan.cqupt.edu.cn/cquptluck/Public/images/index/share.jpg', // 分享图标
            success: function () {
                alert('分享成功!');// 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
</script>
</html>