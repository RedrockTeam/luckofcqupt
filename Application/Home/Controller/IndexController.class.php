<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    //显示主页
    public function index(){
        $openid = I('get.openid');
        $info = $this->bindVerify($openid);
        if ($info->status != 200) {
            session('issetopenid', false);
        }
        else{
            session('issetopenid', true);
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
                    'photo' => $photo
                );
                $message->add($data);
            }
        }
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
        $type = json_decode(strip_tags(file_get_contents("php://input")));;
        $info = M('message')->where(array(
            "openid"=>session("info")['openid'],
        ))->find();
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
                default:
                        $map['openid'] = array('like', '%');
                        break;
            }
        }

        $pos_tar = $this->getLocation(session('info')['openid']);
        $sf = M('message')
            ->where($map) //todo 筛选!
            ->order("has_img desc")
            ->select();
        $count = count($sf);
        for ($i=0; $i<$count; $i++){
            $pos_fri = $this->getLocation($sf[$i]['openid']);
            if ($this->computeDis($pos_tar['lat'], $pos_fri['lat'], $pos_tar['long'], $pos_fri['long'])>1000){//lan->lat by Lich
                unset($sf[$i]);
            }
        }
        foreach($sf as $v){
            $v['stuid'] = substr($v['stuid'], 0, 4).'级';
            $data[] = $v;
        }
        if(IS_POST) {
            if($data == null)
                $data = [];
            $ajax['data'] = $data;
            $this->ajaxReturn($ajax);
        }
        $flag = 0;
        if(strlen($info['hometown']) == 0) {
            $flag = 1;
        }
        $this->assign('flag', $flag);
        $this->assign('friend', $data);
        $this->display();
    }
    //完善信息表单提交处理
    public function perfectInfo(){
        $imgName = session('info')['stuid'].time();
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
        );
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
        $data['headimgurl'] = $this->getHeadImgUrl(session('info')['openid']);
        M('message')->where(array(
            'openid' => session('info')['openid'],
        ))->data($data)->save();
    }

    //完善信息页面
    public function information(){
        if(!session('issetopenid')) {
            $this->error('亲, 你还没有绑定学号哟~~ <br/> 请关注重邮小帮手(cyxbswx), 输入关键字"绑定"即可.');
        }
        $this->assign("info",M('message')->where(array(
            "openid" => session('info')['openid'],
        ))->select());
        $this->display("information");
    }

    //显示详细信息页面
    public function showDetail(){
        if(!session('issetopenid')) {
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
    public function computeDis($lat1, $lat2, $lung1, $lung2){
        $a = $lat1 - $lat2;
        $b = $lung1 - $lung2;
        $dis = 2*asin(sqrt(sin($a/2)*sin($a/2)+cos($lat1)*cos($lat2)*sin($b/2)*sin($b/2)))*6378137;
        return $dis;
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
        return $rel->data->headimgurl;
    }
}