<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    private $appid = 'wx81a4a4b77ec98ff4';
    private $acess_token = 'gh_68f0a1ffc303';
    private $wx_url = 'http://hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/';
    /**
     * 这代码太渣了, 没时间重构了, 怎么快怎么来
     * 妈的, 逼不得已加了新功能
     * 我绝对不会承认这代码是我的, 妈逼
     */
    //显示主页
    public function index(){
        $code = I('get.code');
        if($code == null){
            return redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=http%3a%2f%2fhongyan.cqupt.edu.cn%2fcquptluck%2fHome%2fIndex%2findex.html&response_type=code&scope=snsapi_userinfo&state=sfasdfasdfefvee#wechat_redirect");
        }else{
            session('code', $code);
            $return =  json_decode($this->getOpenId());
            $openid = $return->data->openid;
        }
//        $openid = I('get.openid');
        $info = $this->bindVerify($openid);
        $care = $this->getOpenidVerify($openid);
        if ($info->status != '200') {//绑定学号没
            session('stu', false);
        }
        else{
            session('stu', true);
        };
        if ($care->status != '200') {//关注小帮手没
            session('carexbs', false);
        }
        else{
            session('carexbs', true);
            //如果绑定了学号
            if(session('stu')) {
                session('info',array(
                    "openid" => $openid,
                    "stuid" => $info->stuId,
                ));
                //by Lich, 如果不存在这条记录就把openid和学号存入数据库
                $message = M('message');
                $map = array(
                    'openid' => $openid
                );
                $count = $message->where($map)->count();
                if($count == 0) {
                    $photo = $this->getHeadImgUrl($openid);
                    $data = array(
                        'openid' => $openid,
                        'stuid' => $info->stuId,
                        'photo' => $photo->data->headimgurl
                    );
                    $message->add($data);
                }
            }
        }
        $address = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//        var_dump($address);
        $signature = $this->signature($address);
        $this->assign('signature', $signature);
        $this->display("index");
    }

    //查看来访者是否绑定学号,未绑定则跳转到绑定学号页面,绑定了的话将用户openid存储起来
//    public function beforeIndex(){
//
//    }

    /**
     * 查看校友 逻辑: 先输出周围所有的校友
     * @param array $pos_tar 获取用户的地理位置
     * @param array $pos_fri 获取校友的的地理位置
     *
     */
    public function findSchoolfellow() {
        //获取查询条件
        $type = json_decode(strip_tags(file_get_contents("php://input")));

        $info = M('message')->where(array(
            "openid"=>session("info")['openid'],
        ))->find();
        //前端没时间改, 通过session解决选择分类后分页问题
        if($type->type != null)
            session('type', $type->type);

        $type->type = session('type');
        foreach($type->type as $value){
            switch($value) {
                case 1:
                        $map['openid'] = array('like', '%');
                        break;
                case 2:
                        $map['hometown'] = $info['hometown'];
                        break;
                case 3:
                        $map['sex'] = '男';

                        break;
                case 4:
                        $map['sex'] = '女';

                        break;
                case 5: 
                        $map['college'] = $info['college'];
                        break;
                case 6:
                        $map['stuid'] = array('like', substr($info['stuid'], 0, 4).'%');
                        break;
                default:
                        $map['openid'] = array('like', '%');
                        break;
            }
        }
        if($type->is_dog || I('get.is_dog_page')){
            $gender = $info['sex'] == '男'? '女' : '男';
            $map['sex'] = $gender;
        }
        $pos_tar = $this->getLocation(session('info')['openid']);

        $post = json_decode(strip_tags(file_get_contents("php://input")));
        $page = $post->page? $post->page:1;
        $offset = ($page - 1) * 10;//分页
        var_dump($map);
        return;
        $sf = M('message')
            ->where($map) //todo 筛选!
            ->order("perfect desc")
            ->limit($offset, 10)
            ->select();
        $count = count($sf);
        for ($i=0; $i<$count; $i++){
            $pos_fri = $this->getLocation($sf[$i]['openid']);
            $sf[$i]['distance'] = $this->computeDis($pos_tar['lat'], $pos_fri['lat'], $pos_tar['long'], $pos_fri['long']);
            if ($sf[$i]['distance']>5000){//接口为lat, lan->lat by Lich
                unset($sf[$i]);
            }
        }
        foreach($sf as $v){
            $v['stuid'] = substr($v['stuid'], 0, 4).'级';//直接转换年级 20xx级
            $data[] = $v;
        }

        if(IS_POST) {//瀑布流, ajax请求此方法时
            if($data == null)
                $data = [];
            $ajax['data'] = $data;
            $ajax['page'] = $page;
            $this->ajaxReturn($ajax);
        }

        $flag = 0;
        if(strlen($info['hometown']) == 0) {
            $flag = 1;
        }
        //flag检测家乡填没


        $this->assign('flag', $flag);
        $this->assign('friend', $data);
        if(I('get.is_dog_page')) {
            $this->display('is_dog');
            return;
        }
        $this->display();
    }

    //查找所有重邮人
    public function findAllSchoolfellow() {
        //获取查询条件
        $type = json_decode(strip_tags(file_get_contents("php://input")));

        $info = M('message')->where(array(
            "openid"=>session("info")['openid'],
        ))->find();
        //前端没时间改, 通过session解决选择分类后分页问题
        if($type->type != null)
            session('type_all', $type->type);

        $type->type = session('type_all');
        foreach($type->type as $value){
            switch($value) {
                case 1:
                    $map['openid'] = array('like', '%');
                    break;
                case 2:
                    $map['hometown'] = $info['hometown'];
                    break;
                case 3:
                    $map['sex'] = '男';

                    break;
                case 4:
                    $map['sex'] = '女';

                    break;
                case 5: 
                        $map['college'] = $info['college'];
                        break;
                case 6:
                    $map['stuid'] = array('like', substr($info['stuid'], 0, 4).'%');
                        break;
                default:
                    $map['openid'] = array('like', '%');
                    break;
            }
        }

        $pos_tar = $this->getLocation(session('info')['openid']);

        $post = json_decode(strip_tags(file_get_contents("php://input")));
        $page = $post->page? $post->page:1;
        $offset = ($page - 1) * 10;//分页

        $sf = M('message')
            ->where($map) //todo 筛选!
            ->order("perfect desc")
            ->limit($offset, 10)
            ->select();
        $count = count($sf);
        for ($i=0; $i<$count; $i++){
            $pos_fri = $this->getLocation($sf[$i]['openid']);
            if($pos_fri['lat'] == null) {
                $sf[$i]['distance'] = '未知';
            }
            else{
            $sf[$i]['distance'] = $this->computeDis($pos_tar['lat'], $pos_fri['lat'], $pos_tar['long'], $pos_fri['long']);
            }
        }
        foreach($sf as $v){
            $v['stuid'] = substr($v['stuid'], 0, 4).'级';//直接转换年级 20xx级
            $data[] = $v;
        }

        if(IS_POST) {//瀑布流, ajax请求此方法时
            if($data == null)
                $data = [];
            $ajax['data'] = $data;
            $ajax['page'] = $page;
            $this->ajaxReturn($ajax);
        }

        $flag = 0;
        if(strlen($info['hometown']) == 0) {
            $flag = 1;
        }
        //flag检测家乡填没
        $this->assign('flag', $flag);
        $this->assign('friend', $data);
        $this->display('findSchoolfellow');
    }
    //完善信息表单提交处理
    public function perfectInfo(){
        $exixtUser = M('message')->where(array(
            "openid" => session('info')['openid']
        ))->select();

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =  'Public/photos/';
        // 上传文件
        $info = $upload->upload();
        $data = array(
            "name" => trim(I('post.uname')),
            "sex" => trim(I('post.ugender')),
            "college" => trim(I("post.college")),
            "stuid" => trim(session('info')['stuid']),
            "hometown" => trim(I('post.hometown')),
            'contact' => trim(I('post.connect')),
            'way' => trim(I('post.ucv')),
            'introduce' => trim(I('post.introduce')),
            'is_dog'  => trim(I('post.is_dog'))
        );
        if($data['hometown'] != null)
            $data['perfect'] = 1;
        if(strlen($data['name']) == 0) {
            $this->error('姓名不能为空');
        }
        if($data['contact'] == 'QQ') {
            if(!is_numeric($data['way']) || strlen($data['way']) > 11)
                $this->error('QQ号填写错误');
        }
        if($data['contact'] == '微信号') {
            if(strlen($data['way']) == 0)
                $this->error('微信号不能为空');
        }
        if($data['contact'] == '电话号码') {
            if(!is_numeric($data['way']) || strlen($data['way']) != 11)
                $this->error('电话号码填写错误');
        }
        if($data['contact'] == '邮箱') {
            if(strlen($data['way']) == 0) {
                $this->error('邮箱不能为空');
            }
            $pattern = '/^(.*)@(.*)\.(.*)/';
            if(!preg_match($pattern, $data['way']))
                $this->error('邮箱填写错误');
        }
        if ($info) {
            foreach($info as $file) {
                $data['photo'] = '../../Public/photos/'. $file['savepath'] . $file['savename'];
                $data['has_img'] = 1;
                $where = array(
                    'openid' => session("info")['openid']
                );
                M('message')->where($where)->save($data);
            }
        }
        if ($exixtUser){
            M('message')->where(array(
                "openid"=>session('info')['openid']
            ))->data($data)->save();
        } else {
            M('message')->where(array(
                "openid"=>session('info')['openid']
            ))->data($data)->add();
        }
        $id = M('message')->where(array("openid"=>session('info')['openid']))->find();
            $this->success('完善信息成功', U('Index/showDetail').'?id='.$id['id']);
    }
    public function _after_perfectInfo(){
        $rel = M('message')->where(array(
            "openid" => session('info')['openid'],
        ))->find();
        $bool = true;
        for ($i=0; $i<9; $i++){
            if (!$rel[$i]) {
                $bool = false;
                break;
            }
        }
        if ($bool) {
            $data['perfect'] = 1;
        }
        $result = $this->getHeadImgUrl(session('info')['openid']);
        $data['headimgurl'] = $result->data->headimgurl;
        M('message')->where(array(
            'openid' => session('info')['openid'],
        ))->data($data)->save();
    }

    //完善信息页面
    public function information(){
        if(!session('carexbs')) {
            $this->error('亲, 你还没有关注重邮小帮手(cyxbswx)哟~~');
        }
        if(!session('stu')) {
            $this->error('亲, 你还没有绑定学号哟~~ <br/> 请关注重邮小帮手(cyxbswx), 输入关键字"绑定"即可.');
        }
        $this->assign("info",M('message')->where(array(
            "openid" => session('info')['openid'],
        ))->select());
        $this->display("information");
    }

    //显示详细信息页面
    public function showDetail(){
        if(!session('carexbs')) {
            $this->error('亲, 你还没有关注重邮小帮手(cyxbswx)哟~~');
        }
        if(!session('stu')) {
            $this->error('亲, 你还没有绑定学号哟~~ <br/> 请关注重邮小帮手(cyxbswx), 输入关键字"绑定"即可.');
        }
        $this->assign("info",M('message')->where(array(
            "id" => I('get.id'),
        ))->find());
        $this->display();
    }

    /*
     *
     * 一些前面判断、计算要用到的函数
     *
     * */

    //通过经纬度计算两点的距离，返回单位米
    public function computeDis($lat1, $lat2, $lung1, $lung2) {
        $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        $radLat2=deg2rad($lat2);
        $radLng1=deg2rad($lung1);
        $radLng2=deg2rad($lung2);
        $a=$radLat1-$radLat2;
        $b=$radLng1-$radLng2;
        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
        return round($s);
    }
    /*
     *
     *判断该用户是否绑定了学号
     * 返回值是这个样子
     * object(stdClass)[6]
     * public 'status' => int 200
     * public 'info' => string 'success' (length=7)
     * public 'stuId' => string '2013211689' (length=10)
     *
     * */
    public function bindVerify($openid){
        $url = "http://Hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/bindVerify";
        $timestamp = time();
        $string = "";
        $arr = "abcdefghijklmnopqistuvwxyz0123456789ABCDEFGHIGKLMNOPQISTUVWXYZ";
        for ($i=0; $i<16; $i++) {
            $y = rand(0,41);
            $string .= $arr[$y];
        }
        $secret = sha1(sha1($timestamp).md5($string).'redrock');
        $post_data = array (
            "timestamp" => $timestamp,
            "string" => $string,
            "secret" => $secret,
            "openid" => $openid,
            "token" => "gh_68f0a1ffc303",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }
    /*
     *
     * 获得用户的经纬度
     * 接口返回值是这个样子
     * object(stdClass)[6]
     * public 'status' => string '-400' (length=4)
     * public 'info' => string 'success' (length=7)
     * public 'data' =>
     *     object(stdClass)[7]
     *     public 'openid' => string 'ouRCyjo24q67OUj5uH-e-ra_Jcp8' (length=28)
     *     public 'latitude' => string '29.535849' (length=9)
     *     public 'longitude' => string '106.605179' (length=10)
     *
     * */
    public function getLocation($openid){
        $url = "http://Hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/location";
        $timestamp = time();
        $string = "";
        $arr = "abcdefghijklmnopqistuvwxyz0123456789ABCDEFGHIGKLMNOPQISTUVWXYZ";
        for ($i=0; $i<16; $i++) {
            $y = rand(0,41);
            $string .= $arr[$y];
        }
        $secret = sha1(sha1($timestamp).md5($string).'redrock');
        $post_data = array (
            "timestamp" => $timestamp,
            "string" => $string,
            "secret" => $secret,
            "openid" => $openid,
            "token" => "gh_68f0a1ffc303",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        $rel = json_decode($output);
        return array(
            "lat" => $rel->data->latitude,
            "long" => $rel->data->longitude,
        );
    }
    /*
     *
     * 获得用户头像url
     * 返回url字符串
     *
     * */
    public function getHeadImgUrl($openid){
//        $openid = 'ouRCyjpvLulo8TzHsMmGY2bTP13c';杨奇凡的openid, 2333333
        $url = "http://Hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/userInfo";
        $timestamp = time();
        $string = "";
        $arr = "abcdefghijklmnopqistuvwxyz0123456789ABCDEFGHIGKLMNOPQISTUVWXYZ";
        for ($i=0; $i<16; $i++) {
            $y = rand(0,41);
            $string .= $arr[$y];
        }
        $secret = sha1(sha1($timestamp).md5($string).'redrock');
        $post_data = array (
            "timestamp" => $timestamp,
            "string" => $string,
            "secret" => $secret,
            "openid" => $openid,
            "token" => "gh_68f0a1ffc303",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        $rel = json_decode($output);
//        print_r($rel);
        return $rel;
    }

    //关注认证
    public function getOpenidVerify($openid){
//        $openid = 'ouRCyjpvLulo8TzHsMmGY2bTP13c';
        $url = "http://Hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/openidVerify";
        $timestamp = time();
        $string = "";
        $arr = "abcdefghijklmnopqistuvwxyz0123456789ABCDEFGHIGKLMNOPQISTUVWXYZ";
        for ($i=0; $i<16; $i++) {
            $y = rand(0,41);
            $string .= $arr[$y];
        }
        $secret = sha1(sha1($timestamp).md5($string).'redrock');
        $post_data = array (
            "timestamp" => $timestamp,
            "string" => $string,
            "secret" => $secret,
            "openid" => $openid,
            "token" => "gh_68f0a1ffc303",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        $rel = json_decode($output);
//        print_r($rel);
        return $rel;
    }

    private function getOpenId () {
        $code = session('code');
        $time=time();
        $str = 'abcdefghijklnmopqrstwvuxyz1234567890ABCDEFGHIJKLNMOPQRSTWVUXYZ';
        $string='';
        for($i=0;$i<16;$i++){
            $num = mt_rand(0,61);
            $string .= $str[$num];
        }
        $secret =sha1(sha1($time).md5($string)."redrock");
        $t2 = array(
            'timestamp'=>$time,
            'string'=>$string,
            'secret'=>$secret,
            'token'=>$this->acess_token,
            'code' => $code,
        );
        $url = "http://hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/webOauth";
        return json_encode($this->curl_api($url, $t2));
    }

    private function signature($address) {
        $url = "http://Hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/apiJsTicket";
        $timestamp = time();
        $string = "";
        $arr = "abcdefghijklmnopqistuvwxyz0123456789ABCDEFGHIGKLMNOPQISTUVWXYZ";
        for ($i = 0; $i < 16; $i++) {
            $y = rand(0, 41);
            $string .= $arr[$y];
        }
        $secret = sha1(sha1($timestamp) . md5($string) . 'redrock');
        $post_data = array(
            "timestamp" => $timestamp,
            "string" => $string,
            "secret" => $secret,
            "token" => "gh_68f0a1ffc303",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        $rel = json_decode($output);
        $ticket = $rel->data;
        $key = "jsapi_ticket=$ticket&noncestr=$string&timestap=$timestamp&url=$address";
        $data['ticket'] = $ticket;
        $data['timestamp'] = $timestamp;
        $data['string'] = $string;
        $data['signature'] = sha1($key);
        return $data;
    }

    /*curl通用函数*/
        private function curl_api($url, $data=''){
            // 初始化一个curl对象
            $ch = curl_init();
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_POST, 1 );
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
            // 运行curl，获取网页。
            $contents = json_decode(curl_exec($ch));
            // 关闭请求
            curl_close($ch);
            return $contents;
        }
}
