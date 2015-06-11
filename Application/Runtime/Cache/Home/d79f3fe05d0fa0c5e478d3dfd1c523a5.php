<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>信息</title>
    <script src="/cyKnot/Public/js/lib/init.js"></script>
    <link rel="stylesheet" type="text/css" href="/cyKnot/Public/css/lib/base.css">
    <link rel="stylesheet" type="text/css" href="/cyKnot/Public/css/info.css">
</head>
<body>
<div class="s-wrap">
    <header class="u-menu">
        <a href="<?php echo U('Index/index');?>?openid=<?php echo session('info.openid');?>" class="u-left"></a>
        <a class="f-sub js-form_sub" href="javascript:void(0)">完成</a>
        <span>完善或修改信息</span>
    </header>
    <form action="/cyKnot/home/index/perfectInfo" method="post" id="info_form" enctype="multipart/form-data">
        <div class="m-inputs">
            <div class="item"><!--姓名-->
                <span class="filed_name">姓名</span>
                <input type="text" class="need_input" name="uname" <?php if($info[0]['name']): ?>value="<?php echo ($info[0]['name']); ?>"<?php else: ?>placeholder='输入姓名'<?php endif; ?> /><!--默认的姓名-->
            </div>
            <div class="item">
                <span class="filed_name">性别</span>
                <div class="de_label">
                   <span class="js-label_way">
                        <?php if($info[0]['sex']): echo ($info[0]['sex']); ?>
                            <?php else: ?>
                            男<?php endif; ?>
                   </span>
                    <i class="s-arrow"></i></div>
                <select name="ugender" id="s_sex" class="select_cahnge js-c_way">
                    <option value="男">男</option>
                    <option value="女">女</option>
                </select>
            </div>
            <div class="item">
                <span class="filed_name">学院</span>
                <div class="de_label">
                    <span class="js-label_way"><?php if($info[0]['college']): echo ($info[0]['college']); ?>
                        <?php else: ?>
                        填入学院<?php endif; ?></span>
                    <i class="s-arrow"></i></div>
                <select name="college"  class="select_cahnge js-c_way">
                    <option value="光电/半导体">光电/半导体</option>
                    <option value="计算机">计算机</option>
                    <option value="通信">通信</option>
                    <option value="生物">生物</option>
                    <option value="经管">经管</option>
                    <option value="外国语">外国语</option>
                    <option value="国际">国际</option>
                    <option value="软件">软件</option>
                    <option value="体育">体育</option>
                    <option value="理学院">理学院</option>
                    <option value="自动化">自动化</option>
                    <option value="传媒">传媒</option>
                    <option value="先进制造">先进制造</option>
                    <option value="法学院">法学院</option>
                </select>
            </div>
            <div class="item">
                <span class="filed_name">家乡 </span>
                <div class="de_label">
                    <span class="js-label_way"><?php if($info[0]['hometown']): echo ($info[0]['hometown']); ?>
                        <?php else: ?>
                        重庆<?php endif; ?></span>
                    <i class="s-arrow"></i></div>
                <select name="hometown" id="" class="select_cahnge js-c_way">
                    <option value="北京">北京</option>
                    <option value="上海">上海</option>
                    <option value="天津">天津</option>
                    <option value="重庆">重庆</option>
                    <option value="黑龙江">黑龙江</option>
                    <option value="吉林">吉林</option>
                    <option value="辽宁">辽宁</option>
                    <option value="江苏">江苏</option>
                    <option value="山东">山东</option>
                    <option value="安徽">安徽</option>
                    <option value="河北">河北</option>
                    <option value="河南">河南</option>
                    <option value="湖北">湖北</option>
                    <option value="湖南">湖南</option>
                    <option value="江西">江西</option>
                    <option value="山西">山西</option>
                    <option value="陕西">陕西</option>
                    <option value="四川">四川</option>
                    <option value="青海">青海</option>
                    <option value="海南">海南</option>
                    <option value="广东">广东</option>
                    <option value="贵州">贵州</option>
                    <option value="浙江">浙江</option>
                    <option value="福建">福建</option>
                    <option value="台湾">台湾</option>
                    <option value="甘肃">甘肃</option>
                    <option value="云南">云南</option>
                    <option value="内蒙古">内蒙古</option>
                    <option value="宁夏">宁夏</option>
                    <option value="新疆">新疆</option>
                    <option value="西藏">西藏</option>
                    <option value="云南">云南</option>
                </select>
            </div>
            <div class="item">
                <span class="filed_name">联系方式</span>
                <div class="de_label">
                    <span class="js-label_way">
                        <?php if($info[0]['contact']): echo ($info[0]['contact']); ?>
                            <?php else: ?>
                            微信<?php endif; ?>
                    </span>
                    <i class="s-arrow"></i></div>
                <select name="connect"  required="required"  data-tag="spec" class="select_cahnge js-c_way">
                    <option value="QQ" <?php echo ($info[0]['contact'] == 'QQ'? "selected='selected'" : ''); ?>>QQ</option>
                    <option value="微信号" <?php echo ($info[0]['contact'] == '微信号'? "selected='selected'" : ''); ?>>微信号</option>
                    <option value="电话号码" <?php echo ($info[0]['contact'] == '电话号码'? "selected='selected'" : ''); ?>>电话号码</option>
                    <option value="邮箱" <?php echo ($info[0]['contact'] == '邮箱'? "selected='selected'" : ''); ?>>邮箱</option>
                </select>
            </div>
            <div class="s-c_way">
                <input type="text" required="required" name="ucv"
                <?php if($info[0]['way']): ?>value="<?php echo ($info[0]['way']); ?>"<?php else: ?>placeholder='输入号码'<?php endif; ?>
                       class="js-way_tip"/>
            </div>
            <div class="item">
                <span class="filed_name">简介</span>
                <input name="introduce" type="text" class="need_input"
                <?php if($info[0]['introduce']): ?>value="<?php echo ($info[0]['introduce']); ?>"<?php else: ?>placeholder='输入简介'<?php endif; ?> />
            </div>
            <div class="upload_img js-up_hcon">
                上传图片(默认为微信头像)
                <input type="file" name="photo" class="u-fup"/>
            </div>
        </div>
    </form>
</div>
</body>
<script src="/cyKnot/Public/js/info.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</html>