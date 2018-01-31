<?php
/**
 * 用户
 */
namespace app\wap\controller;
use app\wap\model\Users as UsersModel;
use app\index\logic\UsersLogic;
use app\home\logic\Orders;
use app\wap\model\PointsLog;
use think\Loader;
use app\home\controller\Express;

class Users extends Base{

    public $user_id = 0;
    public $user = array();

    /*
    * 初始化操作
    */
   public function _initialize()
   {
       parent::_initialize();
       if (session('?user')) {
           $user = session('user');
           $user = db('users')->where("user_id = {$user['user_id']}")->find();
           session('user', $user);  //覆盖session 中的 user
           session('user_id',$user['user_id']);
           $this->user = $user;
           $this->user_id = $user['user_id'];
           $this->assign('user', $user); //存储用户信息
       }
       $nologin = array(
           'login', 'add','protocol','forget','resetpassword','regsendcode','forgetsendcode'
       );
       $request = \think\Request::instance();
       $action = $request->action();
       if (!$this->user_id && !in_array($action, $nologin)) {
           $this->error('请登录',url('Login/index'));
       }
   }

    //我的（用户中心）
    public function index(){
        $level_name = db('user_level')->where('level_id',$this->user['level'])->value('level_name');
        //跑马灯
        $log = new PointsLog();
        $news = $log->ad(20);
        $this->assign('news',$news);

        //查是否有要审核的
        $point_map = [
            'status' => 0,
            'type' => 4,
            'user_id_pusher' => session('user_id'),
            ];
        $has_check = db('points_log')->where($point_map)->count();

        //查是否有新信息
        $message_map = [
            'status' => 0,
            'user_id' => session('user_id'),
            ];
        $has_message = db('user_message')->where($message_map)->count();


        $this->assign([
            'level_name' => $level_name,
            'head_pic' => $this->user['head_pic'],
            'has_message' => $has_message,
            'has_check' => $has_check,

            ]);

        return view();
    }

    //注册
    public function add(){
        if (request()->isPost()){
            //短信验证
            /*if(input('code')<> cookie('sendcode') || empty(cookie('sendcode'))){
                $this->error("手机验证码不正确！");
            }*/
            $data = array(
                'mobile'=>input('mobile'),
                'password'=>input('password'),
                'pay_code'=>input('password'),
                'reg_time'=>time(),
            );

            $result = $this->validate($data,'User.add');
            if(true !== $result){
                // 验证失败 输出错误信息
                $this->error($result);
            }

            $expressArea = '';
            $users = new UsersModel();
            $res = $users->reg($data,$expressArea);
            if ($res){
                $user = db('users')->where('mobile',$data['mobile'])->find();
                session('user',$user);
                session('user_id',$user['user_id']);
                $this->success("注册成功",url('Login/index'));
            }else{
                $this->error("注册失败！");
            }
            return;
        }
        return view();
    }

    //注册时验证码
    public function regsendcode(){
        if (request()->isAjax()){
            $map['mobile'] = input('post.mobile');
            $has = db('users')->where($map)->find();
            if($has){
                $this->error('已经存在这个手机号，不能注册！');
            }

            $this->sendcode();
        }
        return view();
    }

    //注册协议
    public function protocol(){
        return view();
    }

    //服务协议
    public function service(){
        return view();
    }

    //消息
    public function news(){
        return view();
    }

    //修改手机号码
    public function modify(){
        if (request()->isPost()){
            //短信验证
            if(input('code')<> cookie('sendcode') || empty(cookie('sendcode'))){
                $this->error("手机验证码不正确！");
            }
            $post = input('post.');
            if ($post['mobile'] == $this->user['mobile']){
                $this->success('手机号已设置为'.$post['mobile'].'！');
            }
            if ($post['mobile']){
                $res = db('users')->where('user_id', session('user_id'))->setField('mobile', $post['mobile']);
                if($res !== flase){
                    $this->user['mobile'] = $post['mobile'];
                    session('user', $this->user);  //覆盖session 中的 user
                    $this->success('手机号已设置为'.$post['mobile'].'！');
                }
            }
            $this->error('手机号码不存在，请检查！');
        }
        return view();
    }
    //忘记时验证码
    public function forgetsendcode(){
        if (request()->isAjax()){
            $map['mobile'] = input('post.mobile');
            $has = db('users')->where($map)->find();
            if(!$has){
                $this->error('手机号码不存在，请检查！');
            }

            $this->sendcode();
        }
        return view();
    }

    //修改时验证码
    public function editsendcode(){
        if (request()->isAjax()){
            $post = input('post.');
            
            if ($post['mobile'] == $this->user['mobile']){
                $this->error('手机号与原来的相同，不用修改！');
            }
            //验证用户手机号          
            $map['mobile'] = $post['mobile'];
            $has = db('users')->where($map)->find();
            if($has){
                $this->error('这个账号手机号码已经存在，请检查！');
            }

            $this->sendcode($map['mobile']);
            
        }
        
    }

    //忘记密码
    public function forget(){
        if (request()->isPost()){
            //短信验证
            if(input('code')<> cookie('sendcode') || empty(cookie('sendcode'))){
                $this->error("手机验证码不正确！");
            }
            $post = input('post.');
            $user = db('users')->where('mobile',$post['mobile'])->find();
            if ($user){
                $res = db('users')->where('mobile',$post['mobile'])->setField('password',md5($post['password']."sunflower"));
                if($res !== flase){
                    $this->success('新密码已设置行牢记新密码！',url('Login/index'));
                }
            }
            $this->error('手机号码不存在，请检查！');
        }
        return view();
    }

    //增加收货地址
    public function addAddress(){
        $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url("Users/index");
        if (request()->isPost()){
            $data = array(
                'user_id'=>$this->user_id,
                'consignee'=>input('consignee'),
                'address'=>input('address').' '.input('street'),
                'mobile'=>input('mobile'),
                'is_default'=>input('is_default'),
            );
 
            if ($data['is_default'] == 1){
                db('user_address')->where('user_id',$data['user_id'])->setField('is_default','0');
            }
            if(input('address_id')){
                $res = db('user_address')->where(['user_id' => $data['user_id'], 'address_id' => input('address_id')] )->update($data);
            }
            else{
                $res = db('user_address')->insert($data);
                if ($data['is_default'] == 1){
                    $userId = db('user_address')->getLastInsID();
                    db('users')->where('user_id',$data['user_id'])->setField('address_id',$userId);
                }
            }
            if ($res == 1){
                $this->success('提交成功！',$referurl);
            }else{
                $this->error("提交失败！");
            }
        }
        $address_id = input('address_id');
        if($address_id){
            $address = db('user_address')->find($address_id);
            $address['address'] = explode(' ', $address['address']);
            $address['area'] = $address['address'][0].' '.$address['address'][1].' '.$address['address'][2];
            $address['address'] = $address['address'][3];
            $this->assign('address', $address);
        }
        $this->assign('referurl', $referurl);
        return view();
    }

    //收货地址列表
    public function address(){
        $address = db('user_address')->field('address_id,consignee,address,mobile,is_default')->where('user_id',$this->user_id)->order('address_id desc')->select();
        $this->assign('address',$address);
        return view();
    }

    //删除收货地址
    public function delAddress(){
        $address_id = input('address_id');
        $res = db('user_address')->where(['user_id' => session('user_id'),'address_id' => $address_id ])->delete();
        if ($res > 0){
            db('users')->where(['user_id' => session('user_id'),'address_id' => $address_id ])->setField('address_id',0);
            $this->success("删除地址成功！",url('address'));
        }else{
            $this->error("删除地址失败！");
        }
    }

    //设置默认收货地址
    public function ajaxSet(){
        if (request()->isAjax()){
            $address_id = input('address_id');
            db('user_address')->where('user_id',$this->user_id)->setField('is_default','0');
            db('user_address')->where('address_id',$address_id)->setField('is_default','1');
            $result = db('users')->where('user_id',$this->user_id)->setField('address_id',$address_id);
            if($result !== 'false'){
                $this->success('修改成功'); 
            }
            else{
               $this->error('修改失败'); 
            }
            
        }
    }

    //返利推荐
    public function promotion(){
        $res = $this->myqrcode();
        $this->assign('res', $res);
        return view();
    }

    

    //我的订单
    public function order(){
        return view();
    }

    //ajax订单列表
    public function ajaxOrder(){
        if (request()->isAjax()){
            $where['user_id'] = $this->user_id;
            $type = input('type');
            $page = input('page');
            $where['order_status'] = ['in','0,1,2,4'];
            if ($type == 1){
                //待付款
                $where['order_status'] = 0;
                $where['pay_status'] = 0;
            }elseif ($type == 2){
                //待发货
                $where['order_status'] = ['<','2'];
                $where['pay_status'] = 1;
                $where['shipping_status'] = 0;
            }elseif ($type == 3){
                //待收货
                $where['order_status'] = 1;
                $where['pay_status'] = 1;
                $where['shipping_status'] = 1;
            }elseif ($type == 4){
                //完成订单
                $where['order_status'] = 2;
                $where['pay_status'] = 1;
                $where['shipping_status'] = 1;
            }
            $orders = db('order')->field('order_id,order_status,order_sn,total_amount,pay_status,shipping_status,confirm_time,order_amount')->where($where)->order('order_id desc')->page($page,3)->select();
            $orderLogic = new Orders();
            foreach ($orders as $k => $v){
                $data = $orderLogic->getOrderGoods($v['order_id']);
                $orders[$k]['goods'] = $data;
            }
            return $orders;
        }
    }

//    //ajax订单列表
//    public function ajaxOrder(){
//        if (request()->isAjax()){
//            $where['user_id'] = $this->user_id;
//            $type = input('type');
//            if ($type){
//                if ($type == 'waitpay'){
//                    $where['order_status'] = 0;
//                    $where['pay_status'] = 0;
//                }elseif ($type == 'waitsend'){
//                    $where['order_status'] = ['<','2'];
//                    $where['pay_status'] = 1;
//                    $where['shipping_status'] = 0;
//                }elseif ($type == 'waitreceive'){
//                    $where['order_status'] = 1;
//                    $where['pay_status'] = 1;
//                    $where['shipping_status'] = 1;
//                }elseif ($type == 'finish'){
//                    $where['order_status'] = 2;
//                    $where['pay_status'] = 1;
//                    $where['shipping_status'] = 1;
//                }
//            }
//            $page = input('page');
//            $orderLogic = new Orders();
//            $orders = db('order')->field('order_id,order_status,order_sn,total_amount,pay_status,shipping_status,confirm_time')->where($where)->order('order_id desc')->page($page,3)->select();
//            $now = time();
//            $goods_return_time = db('config')->where('name','goods_return_time')->value('value');
//            foreach ($orders as $k => $v){
//                $data = $orderLogic->getOrderGoods($v['order_id']);
//                if ($v['confirm_time']){
//                    if ((($now - $v['confirm_time'])/86400) < $goods_return_time){
//                        $orders[$k]['return'] = 1;
//                    }
//                }
//                $orders[$k]['goods'] = $data;
//            }
//            return $orders;
//        }
//    }

    //取消订单
    public function cancel_order(){
        $id = input('get.id');
        $logic = new UsersLogic();
        $data = $logic->cancel_order($this->user_id, $id);
        if ($data['status'] < 0)
            $this->error($data['msg']);
        $this->success($data['msg']);
    }

    //确认收货
    public function order_confirm(){
        $order_id = input('order_id');
        $data = confirm_order($order_id, $this->user_id);
        if (!$data['status'])
            $this->error($data['msg']);
        else
            $this->success($data['msg']);
    }

    //物流
    public function logistics(){
        $order_id = input('order_id');
        $order_goods = db('order_goods')->field('count(og.goods_id) as num,g.original_img')->alias('og')->join('goods g','og.goods_id=g.goods_id')->where('og.order_id',$order_id)->find();
        $info = db('delivery_doc')->field('shipping_code,shipping_name,invoice_no,create_time')->where('order_id',$order_id)->find();
        $express = new Express();
        $res = $express->getOrderTraces($info['shipping_code'],$info['invoice_no']);
        $this->assign(array(
            'res' => $res,
            'order_goods' => $order_goods,
            'info' => $info,
        ));
        return view();
    }

 

    //忘记登陆密码再设置
    public function resetpassword(){
        if (request()->isPost()){

            $data = input('post.');
            /*if($data['code']<> cookie('sendcode') || empty(cookie('sendcode'))){
                $this->error("手机验证码不正确！");
            }*/
            $url = url('Login/index');
            $res = db('users')->where('mobile', $data['mobile'])->setField('password',md5(md5($data['password']).config('md5_key')) );
            session(null);
            if($res !== 'false'){
                //重新取用户信息
                $this->success('新密码已设置行牢记新密码！',$url);
            }
            else{
                $this->error('设置失败，请重新设置！');
            }
        }
        return view();
    }

    //修改登陆密码
    public function password(){
        if (request()->isPost()){

            $post = input('post.');
            $this->setPwd('1',$post);
        }
        return view();
    }

    /*
     *  修改密码
     *  $type   int     1-修改登陆密码；
     *  $data   array   表单数据
     * */
    public function setPwd($type,$data){
        //修改数据
        if ($type == 1){
            $url = url('Login/index',array('isurl'=>1));
            $data['password'] = md5(md5($data['password']).config('md5_key'));
            if($data['password'] <> $this->user['password']){
                $this->error('旧密码错误，请检查！');
            }
            $res = db('users')->where('user_id', session('user_id'))->setField('password',md5(md5($data['pwd1']).config('md5_key')) );
            session(null);
        }
        if($res !== flase){
            $this->success('新密码已设置行牢记新密码！',$url);
        }
        else{
            $this->error('手机号码错误，请检查！');
        }
    }


    //实名验证
    public function verified(){
        if (request()->isPost()){
            $post = input('post.');
            //上传图片
            if ($_FILES['front_pic']['tmp_name']) {
                $file = request()->file('front_pic');
                $info = $file->rule('uniqid')->move(ROOT_PATH . 'public/public' . '/' . 'card_pic' . '/' . $this->user_id);
                if ($info) {
                    $front_pic =  '/' .'public' . '/' . 'card_pic' . '/' . $this->user_id . '/' . $info->getSaveName();
                    $post['front_pic'] = $front_pic;
                }
            }
            if ($_FILES['back_pic']['tmp_name']) {
                $file = request()->file('back_pic');
                $info = $file->rule('uniqid')->move(ROOT_PATH . 'public/public' . '/' . 'card_pic' . '/' . $this->user_id);
                if ($info) {
                    $back_pic =  '/' .'public' . '/' . 'card_pic' . '/' . $this->user_id . '/' . $info->getSaveName();
                    $post['back_pic'] = $back_pic;
                }
            }
            if ($_FILES['pic']['tmp_name']) {
                $file = request()->file('pic');
                $info = $file->rule('uniqid')->move(ROOT_PATH . 'public/public' . '/' . 'card_pic' . '/' . $this->user_id);
                if ($info) {
                    $pic =  '/' .'public' . '/' . 'card_pic' . '/' . $this->user_id . '/' . $info->getSaveName();
                    $post['pic'] = $pic;
                }
            }

            $post['status'] = 0;
            if ($post['card_id']){
                $res = db('user_card')->update($post);
            }else{
                $post['user_id'] = $this->user_id;
                $post['add_time'] = time();
                $res = db('user_card')->insert($post);
            }

            if ($res !== false){
                $this->success('身份证上传成功，我们正在对您信息进行',url('index'));
            }
            $this->error('身份证上传失败，请重试！');
        }
        $card = db('user_card')->field('card_id,front_pic,back_pic,pic,status')->where('user_id',$this->user_id)->find();
        $this->assign('card',$card);
        return view();
    }

    //绑定银行
    public function bank(){
        if (request()->isPost()){
            $post = input('post.');
            $data = [
                'bank' => $post['bank'],
                'openbank' => $post['openbank'],
                'bankcard' => $post['bankcard'],
            ];
            $result = db('users')->where('user_id', session('user_id'))->update($data);
            if($result !== 'false'){
                //重新取用户信息
                $user = db('users')->where("user_id = ".session('user_id') )->find();
                session('user', $user);  //覆盖session 中的 user
                session('user_id',$user['user_id']);
                $this->user = $user;
                $this->user_id = $user['user_id'];
                $this->success('修改成功');
            }
            else{
                $this->error('修改失败');
            }
            

        }
        $referer =  $_SERVER['HTTP_REFERER'];
        if(!$referer){
            $referer = url('user/index');
        }
        

        $this->user['referer'] = $referer;
        $this->assign('user',$this->user);
        return view();
    }

    //个人头像
    public function headPic(){
        if (request()->isAjax()){
            //上传图片
            $head_pic = input('head_pic');
            if ($head_pic) {
                $img = $this->get_base64_img($head_pic,'./' .'public' . '/' . 'head_pic' . '/' . $this->user_id .'/');
                $res = db('users')->where('user_id',$this->user_id)->setField('head_pic',substr($img,1));
//                $file = request()->file('head_pic');
//                $info = $file->validate([ 'size' => 20000000, 'ext' => 'gif,png,jpg,jpeg,bmp' ])->move(ROOT_PATH . 'public/public' . '/' . 'head_pic' . '/' . $this->user_id);
//                if ($info) {
//                    $head_pic =  '/' .'public' . '/' . 'head_pic' . '/' . $this->user_id . '/' . $info->getSaveName();
//                    $image = \think\Image::open('./'.$head_pic);
//                    // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
//                    $image->thumb(150, 150)->save('./' .'public' . '/' . 'head_pic' . '/' .$this->user_id . '/thumb.png');
//                    $data['head_pic'] = '/' .'public' . '/' . 'head_pic' . '/' . $this->user_id . '/thumb.png';
//                    $res = db('users')->where('user_id',$this->user_id)->update($data);
//                }
            }

            if ($res !== false){
                $data['state'] = 0;
                $data['msg'] = "上传头像成功！";
            }else{
                $data['state'] = -1;
                $data['msg'] = "上传头像失败，稍后再试！";
            }
            return $data;
        }
    }

    public function get_base64_img($base64,$path = 'data/upload/sign/'){
        if(!is_readable($path))
        {
            is_file($path) or mkdir($path,0700);
        }
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
            $type = $result[2];
            $new_file = $path.time().".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64)))){
                return $new_file;
            }else{
                return	false;
            }
        }
    }

    //我的二维码
    public function myqrcode(){
        $user_id = $this->user_id;
        $host = request()->domain();
        $url = $host.'/index.php/wap/users/add.html'."?tguid=".$user_id;

        $src = ROOT_PATH . "public/public/qrcode/qrcode".$user_id.".png";
        if(!file_exists($src)){
            Loader::import('phpqrcode.phpqrcode');
            error_reporting(E_ERROR);
            $img = new \QRcode();

            $file_name = "public/qrcode/qrcode".$user_id.".png";
            $img->png($url, $file_name, '', '4', '2', 'true');
        }
        $src = "/public/qrcode/qrcode".$user_id.".png";
        $data = array(
            'url' => $url,
            'src' => $src,
        );
        return  $data;
    }

    //我的返利、工资
    public function reward(){
        $map['user_id'] = session('user_id');
        $user['user_money'] = db('users')->where($map)->value('user_money');
        //工资start
        $where['type'] = 4;
        $where['status'] = 1;
        $where['user_id'] = session('user_id');

        $user['points_wages'] = db('points_log')->where($where)->sum('points_wages');
        //工资end

        //返利start
        $where2['type'] = 1;
        $where2['status'] = 1;
        $where2['user_id_pusher|user_id'] = session('user_id');

        $result = db('points_log')->where($where2)->order('add_time desc')->limit(1000)->select();
        $user['points_return'] = 0;
        foreach ($result as $k => $v) {
            if(session('user_id') == $v['user_id']){
                $user[$k]['points'] = $v['points_user'];
            }
            else{
                $user[$k]['points'] = $v['points_user_pusher'];
            }
            $user['points_return'] += $user[$k]['points'];
        }

        //返利end

        $this->assign('user', $user);

        return $this->fetch();
    }

    //返利列表
    public function rebatelist(){
        if(request()->isAjax()){
            $post = input('post.');
            if(strtotime($post['day'])){
                $where['add_time'] = ['between', [strtotime($post['day']), strtotime($post['day']." 23:59:59")]];

            }
            //返利start
            $where['type'] = 1;
            $where['status'] = 1;
            $where['user_id_pusher|user_id'] = session('user_id');

            $user = db('points_log')->where($where)->order('id desc')->limit(1000)->select();
            foreach ($user as $k => $v) {
                $user[$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d');
                $user[$k]['time'] = formatTime($v['add_time'], 'H:i:s');
                if(session('user_id') == $v['user_id']){
                    $user[$k]['points'] = $v['points_user'];
                    $user[$k]['pay'] = '消费返利';
                }
                else{
                    $user[$k]['points'] = $v['points_user_pusher'];
                    $user[$k]['pay'] = '推荐返利';
                }
                //清除为零的

                if($user[$k]['points'] == '0.00'){
                    unset($user[$k]);
                }
            }
            //返利end
            sort($user);

            return $user;
        }
        $this->assign('title', '返利记录');

        return $this->fetch('points_list');
    }

    //分享推广
    public function share(){
        $level_name = db('user_level')->where('level_id',$this->user['level'])->value('level_name');
        $this->assign(array(
            'level_name' => $level_name,
        ));
        return view();
    }

    //ajax获取分享
    public function ajaxShare(){
        if (request()->isAjax()){
            $page = input('page');
            $res = db('share')->page($page,'3')->select();
            foreach ($res as $k => $v){
                $res[$k]['add_time'] = date("Y-m-d H:i:s",$v['add_time']);
            }
            return $res;
        }
    }

    //分享活动详情页
    public function shareDetails(){
        $share_id = input('share_id');
        $res = db('share')->find($share_id);

        $myqrcode = $this->myqrcode();
        $file_url = ROOT_PATH . 'public' . $res['pic'];

        $mtime = filemtime($file_url);
        //文件不存在或者生成的时间比更新时间早，生成新图片
        if(!file_exists($file_url) || ($mtime< $res['update_time'])){
            $image = \think\Image::open("./".$res['pic']);
            switch ($res['water'])
            {
                case 1:
                    $image->water("./".$myqrcode['src'],\think\Image::WATER_SOUTHWEST)->save(ROOT_PATH . 'public/public' . '/' .'share'.'/'."share".$share_id."_".session('user_id').".png");
                    break;
                case 2:
                    $image->water("./".$myqrcode['src'],\think\Image::WATER_SOUTH)->save(ROOT_PATH . 'public/public' . '/' .'share'.'/'."share".$share_id."_".session('user_id').".png");
                    break;
                case 3:
                    $image->water("./".$myqrcode['src'],\think\Image::WATER_CENTER)->save(ROOT_PATH . 'public/public' . '/' .'share'.'/'."share".$share_id."_".session('user_id').".png");
                    break;
                case 4:
                    $image->water("./".$myqrcode['src'],\think\Image::WATER_SOUTHEAST)->save(ROOT_PATH . 'public/public' . '/' .'share'.'/'."share".$share_id."_".session('user_id').".png");
                    break;
                default:
                    $image->water("./".$myqrcode['src'],\think\Image::WATER_SOUTHEAST)->save(ROOT_PATH . 'public/public' . '/' .'share'.'/'."share".$share_id."_".session('user_id').".png");
            }
        }
        $res['pic'] = '/public' . '/' .'share'.'/'."share".$share_id."_".session('user_id').".png";
        $this->assign('res',$res);
        return view();
    }

    //工资列表
    public function wageslist(){
        if(request()->isAjax()){
            $post = input('post.');
            if(strtotime($post['day'])){
                $where['add_time'] = ['between', [strtotime($post['day']), strtotime($post['day']." 23:59:59")]];

            }
            //工资start
            $where['type'] = 4;
            $where['status'] = 1;
            $where['user_id'] = session('user_id');

            $user = db('points_log')->where($where)->order('add_time desc')->limit(1000)->select();
            foreach ($user as $k => $v) {
                $user[$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d');
                $user[$k]['time'] = formatTime($v['add_time'], 'H:i:s');
                $user[$k]['points'] = $v['points_wages'];
                $user[$k]['pay'] = '工资收入';
            }
            return $user;
        }
        //工资end


        $this->assign('title', '工资记录');

        return $this->fetch('points_list');
    }

    //积分使用列表
    public function usedlist(){
        if(request()->isAjax()){
            $post = input('post.');
            if(strtotime($post['day'])){
                $where['add_time'] = ['between', [strtotime($post['day']), strtotime($post['day']." 23:59:59")]];

            }
            $where['status'] = ['in',[1,2,3, 5]];
            $where['points_pay'] = ["<>", '0.00'];
            $where['user_id'] = session('user_id');

            $user = db('points_log')->where($where)->order('id desc')->limit(1000)->select();
            foreach ($user as $k => $v) {
                $user[$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d');
                $user[$k]['time'] = formatTime($v['add_time'], 'H:i:s');
                $user[$k]['points'] = $v['points_pay'];
                $user[$k]['pay'] = config('points_type')[$v['type']];
            }
            return $user;
        }

        $this->assign('title', '使用记录');
        return $this->fetch('points_list');
    }

    //充值列表
    public function rechargelist(){
        if(request()->isAjax()){
            $post = input('post.');
            if(strtotime($post['day'])){
                $where['pay_time'] = ['between', [strtotime($post['day']), strtotime($post['day']." 23:59:59")]];

            }
            $where['pay_status'] = 1;
            $where['user_id'] = session('user_id');

            $user = db('recharge')->where($where)->order('order_id desc')->limit(1000)->select();
            foreach ($user as $k => $v) {
                $user[$k]['add_time'] = formatTime($v['pay_time'], 'Y-m-d');
                $user[$k]['time'] = formatTime($v['pay_time'], 'H:i:s');
                $user[$k]['points'] = $v['account'];
                $user[$k]['pay'] = $v['pay_name'];
            }
            return $user;
        }

        $this->assign('title', '充值记录');
        return $this->fetch('points_list');
    }

    //验证码
    public function sendcode($mobile=''){
        $post = input('post.');
        if(empty($post['mobile'])){
            $post['mobile'] = $mobile;
        }

        $send = new Alidayu;
        $send->sendcode($mobile);
    }

    //退出登录
    public function logout(){
        session(null);
        $this->success('退出成功！',url('Login/index',array("isurl"=>1)));
    }

    //升级
    public function upgrade(){
        if (request()->isAjax()){
            $data = array();
            $level_info = db('user_level')->find($this->user['level']-1);
            if ($level_info){
                $num = $this->user['pay_money_used'] - $level_info['amount'];
                //业绩达标
                if ($num >= 0){
                    $up_data = array(
                        'level' => $this->user['level']-1,
                    );
                    $msg = "恭喜您！已成功升级。";
                    $points_pay = 0;
                }else{
                    //用报单币升级  $num为负数
                    if (($this->user['pay_money'] + $num) >= 0){
                        $up_data = array(
                            'pay_money' => ($this->user['pay_money'] + $num),
                            'user_money' => ($this->user['user_money'] - $num),
                            'pay_money_used' => ($this->user['pay_money_used'] - $num),
                            'level' => $this->user['level']-1,
                        );
                        $msg = "升级成功！已将".abs($num)."报单币转换为购物币！";
                        $points_pay = abs($num);
                    }else{
                        //业绩和报单币不足提示充值
                        $data['state'] = -1;
                        $data['msg'] = "升级失败！请充值。";
                        return $data;
                    }
                }
                $res = db('users')->where('user_id',$this->user_id)->update($up_data);
                if ($res !== false){
                    $log = array(
                        'add_time' => time(),
                        'type' => 2,
                        'user_id' => $this->user_id,
                        'points_pay' => $points_pay,
                        'des' => '用于会员等级升级到'.$level_info['level_name'],
                        'status' => 1,
                    );
                    db('points_log')->insert($log);
                    if ($points_pay){
                        $log['type'] = 6;
                        $log['status'] = 1;
                        db('points_log')->insert($log);
                    }
                    $data['state'] = 1;
                    $data['msg'] = $msg;
                }else{
                    $data['state'] = 0;
                    $data['msg'] = "升级失败！请稍后再试！";
                }
            }else{
                $data['state'] = 0;
                $data['msg'] = "您已经是最高等级，无法继续升级！";
            }
            return $data;
        }
        $this->assign('pay_money',$this->user['pay_money']);
        return view();
    }

    //个人资料
    public function personal(){
        return view();
    }

    //个人信息
    public function personalinfo(){
        if(request()->isPost()){
            $post = input('post.');
            $data = [
                'username' => $post['username'],
                'idno' => $post['idno'],
            ];
            $idno = mb_substr($this->user['idno'],0,-6,'utf-8').'******';  //隐藏对比身份证
            if($idno == $post['idno']){
                unset($data['idno']);
            }
            $result = db('users')->where('user_id', session('user_id'))->update($data);
            if($result !== 'false'){
                $this->success('修改成功');
            }
            else{
                $this->error('修改失败');
            }
        }
        return view();
    }

    //用户昵称
    public function nickname(){
        if(request()->isAjax()){
            $data['nickname'] = input('post.nickname');
            //查找有没有重复的
            $has = db('users')->where($data)->find();

            if($has){
                $this->error('已经存在这个昵称，不能再使用啦！');
            }
            else{
                $map['user_id'] = session('user_id');
                $result = db('users')->where($map)->update($data);
                if($result){
                    $this->success('修改成功');
                }
                else{
                    $this->error('修改失败');
                }
            }
        }
        return view();
    }

    //全返还账号
    public function fullback(){
        if(request()->isAjax()){
            $data['rebate_name'] = input('post.rebate_name');
            $result = db('users')->where('user_id', session('user_id'))->update($data);
            if($result !== 'false'){
                $this->success('修改成功');
            }
            else{
                $this->error('修改失败');
            }
        }
        return view();
    }

    //我的部门
    public function department(){
        $first_leader = input('first_leader',$this->user_id);
        $this->assign('first_leader',$first_leader);
        return view();
    }

    public function ajaxDepartment(){
        if (request()->isAjax()){
            $page = input('page');
            $pagesize = input('pagesize');
            $first_leader = input('first_leader');
            $res = db('users')->field('u.user_id,u.nickname,u.mobile,u.reg_time,ul.level_name')->alias('u')->join('user_level ul','u.level=ul.level_id')->where('first_leader',$first_leader)->page($page,$pagesize)->select();
            foreach ($res as $k => $v){
                $res[$k]['reg_time'] = date("Y-m-d H:i:s",$v['reg_time']);
            }
            return $res;
        }
    }

    //交易记录
    public function trade(){
        //0未分类，1商城消费，2升级，3提现 ,4充值,5购物邮费，6报单币换购物7返利币换报单币8返利币换购物币9分红
        $map['user_id|user_id_pusher'] = session('user_id');
        $day = input('day');

        if(strtotime($day)){
            $start = strtotime($day);
            $end = strtotime($day." 23:59:59");
            $map['add_time'] = ['between', [$start, $end] ];
            $data = db('points_log')->where($map)->order('id desc')->select();
        }
        else{
            $day = '';
            $data = db('points_log')->where($map)->order('id desc')->limit(50)->select();
        }
        $this->assign('day', $day);

        $data_all = [];
        $data_recharge = [];
        $data_withdraw = [];
        $data_exchange = [];
        $data_order = [];
        $data_upgrade = []; 
        foreach ($data as $k => $v) {
            $data_all[$k]['type'] = config('points_type')[$v['type']];
            $data_all[$k]['status'] = config('points_status')[$v['status']];
            switch ($v['type']) {
                case '1':
                    $data_all[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_all[$k]['money'] = '-'.$v['points_money'];
                    $data_order[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_order[$k]['money'] = '-'.$v['points_money'];
                    $data_order[$k]['type'] = config('points_type')[$v['type']];
                    $data_order[$k]['status'] = config('points_status')[$v['status']];
                    break;
                case '2':
                    $data_all[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_all[$k]['money'] = '-'.$v['points_money'];
                    $v['des'] = str_replace('用于会员等级升级到', '', $v['des']) . '会员';
                    $data_all[$k]['type'] = config('points_type')[$v['type']] . $v['des'];
                    $data_upgrade[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_upgrade[$k]['money'] = '-'.$v['points_money'];
                    $data_upgrade[$k]['type'] = config('points_type')[$v['type']] . $v['des'];
                    $data_upgrade[$k]['status'] = config('points_status')[$v['status']];
                    break;
                case '3':
                    $time = $v['update_time']? $v['update_time']:$v['add_time'];
                    $data_all[$k]['time'] = formatTime($time, 'Y-m-d');
                    $data_all[$k]['money'] = '-'.$v['points_money'];
                    $data_withdraw[$k]['time'] = formatTime($time, 'Y-m-d');
                    $data_withdraw[$k]['money'] = '-'.$v['points_money'];
                    $data_withdraw[$k]['type'] = config('points_type')[$v['type']];
                    $data_withdraw[$k]['status'] = config('points_status')[$v['status']];
                    break;
                case '4':
                    $time = $v['update_time']? $v['update_time']:$v['add_time'];
                    $data_all[$k]['time'] = formatTime($time, 'Y-m-d');
                    //帮下线充值为减，自己冲值为加
                    if($v['user_id_pusher'] == session('user_id')){
                        $data_all[$k]['type'] = config('points_type')[$v['type']].'-'.$v['points_recharge'];
                        $data_all[$k]['money'] = '(+返利'.$v['points_user_pusher'].')';
                        $data_recharge[$k]['money'] = '(+返利'.$v['points_user_pusher'].')';
                    }
                    else{
                        $data_all[$k]['money'] = '+'.$v['points_recharge'];
                        $data_recharge[$k]['money'] = '+'.$v['points_recharge'];
                    }

                    $data_recharge[$k]['time'] = formatTime($time, 'Y-m-d');
                    $data_recharge[$k]['type'] = config('points_type')[$v['type']];
                    $data_recharge[$k]['status'] = config('points_status')[$v['status']];
                    break;
                case '5':
                    $data_all[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_all[$k]['money'] = '-'.$v['points_money'];
                    $data_order[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_order[$k]['money'] = '-'.$v['points_money'];
                    $data_order[$k]['type'] = config('points_type')[$v['type']];
                    $data_order[$k]['status'] = config('points_status')[$v['status']];
                    break;
                case '6':
                    $data_all[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_all[$k]['money'] = $v['points_pay'];
                    $data_exchange[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_exchange[$k]['money'] = $v['points_pay'];
                    $data_exchange[$k]['type'] = config('points_type')[$v['type']];
                    $data_exchange[$k]['status'] = config('points_status')[$v['status']];
                    break;
                case '7':

                case '8':  //78都是返利币兑换
                    $data_all[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_all[$k]['money'] = $v['points_rebate'];
                    $data_exchange[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_exchange[$k]['money'] = $v['points_rebate'];
                    $data_exchange[$k]['type'] = config('points_type')[$v['type']];
                    $data_exchange[$k]['status'] = config('points_status')[$v['status']];
                    break;    
                case '9':  //分红
                    $data_all[$k]['time'] = formatTime($v['add_time'], 'Y-m-d');
                    $data_all[$k]['money'] = $v['points_rebate'];
                    $data_all[$k]['type'] = formatTime($v['update_time'], 'Y-m').config('points_type')[$v['type']];
                   
                    break; 
                default:
                    break;
            }
            

        }
        //批量赋值
        $this->assign([
            'data_all'  => $data_all,
            'data_recharge' => $data_recharge ,
            'data_withdraw' => $data_withdraw ,
            'data_exchange' => $data_exchange ,
            'data_order' => $data_order ,
            'data_upgrade' => $data_upgrade
        ]);
        return view();
    }

    //排行榜
    public function rank(){
        if(request()->isAjax()){
            $get = input('get.');
            $page = $get['page']?$get['page']:1;
            $limit = $get['pagesize']?$get['pagesize']:1;
            $offset = $limit*($page-1);
            if(input('type')==2){
                $data['lists'] = db('users')->field('nickname, user_money_used as money, mobile')->order('user_money_used desc')->limit($offset, $limit)->select();
            }
            else{
                $data['lists'] = db('users')->field('nickname, pay_money_used as money, mobile')->order('pay_money_used desc')->limit($offset, $limit)->select();
            }
            return $data;
        }

        return view();
    }

    //消息
    public function message(){
        $type = input('type')?input('type'):2;
        if(request()->isAjax()){
            $get = input('get.');
            $page = $get['num']?$get['num']:1;
            $pagesize = $get['size']?$get['size']:10;
            $offset = ($page-1)*$pagesize;
            if($type == 1){
                $data['lists'] = db('article')->where(['is_open' =>1, 'cat_id' => 1 ])->field('article_id, title, publish_time')->order('publish_time desc')->limit($offset, $pagesize)->select();
                $data['length'] = db('article')->where(['is_open' =>1, 'cat_id' => 1 ])->field('article_id, title, publish_time')->order('publish_time desc')->count();
                if($data['length']){
                    foreach ($data['lists'] as $k => $v) {
                        $data['lists'][$k]['publish_time'] = formatTime($v['publish_time'], 'Y-m-d H:i:s');
                        $data['lists'][$k]['page']=$page;
                        $data['lists'][$k]['url'] = url('messageinfo', ['article_id' => $v['article_id']]);
                        $data['lists'][$k]['page'] = $page;
                    }
                }
            }  
            else{

                $where = [
                    'a.is_open' =>1, 
                    'a.cat_id' => 2 ,
                    'm.user_id' => session('user_id'),
                ];
                $data['length'] = db('user_message')->alias('m')->join('article a','a.article_id = m.message_id')->where($where)->field('a.article_id as article_id, a.title as title, a.publish_time as publish_time, m.status as status')->order('a.publish_time desc')->count();
                if($data['length']){
                    $data['lists'] = db('user_message')->alias('m')->join('article a','a.article_id = m.message_id')->where($where)->field('a.article_id as article_id, a.title as title, a.publish_time as publish_time, m.status as status')->order('a.publish_time desc')->limit($offset, $pagesize)->select();
                    foreach ($data['lists'] as $k => $v) {
                        $data['lists'][$k]['publish_time'] = formatTime($v['publish_time'], 'Y-m-d H:i:s');
                        $data['lists'][$k]['page'] = $page;
                        $data['lists'][$k]['url'] = url('messageinfo', ['article_id' => $v['article_id']]);
                    }
                }

            }  
            return $data;
        }
        $map = [
            'user_id' => session('user_id'),
            'status' => 0,
            ];
        $noread = db('user_message')->where($map)->count();
        $this->assign('noread', $noread);
        $this->assign('type', $type);
        return view();
    }


    //消息详情
    public function messageinfo(){
        $article_id = input('article_id');
        $article = db('article')->where('article_id', $article_id)->find();
        if($article['cat_id']==2){ //消息处理
            $r = db('user_message')->where(['message_id'=> $article['article_id'], 'user_id' => session('user_id')])->find();
            if(!$r){
                $this->error('用户权限不足！');
            }
            if($r['status']==0){
                db('user_message')->where(['message_id'=> $article['article_id'], 'user_id' => session('user_id')])->setField('status', 1);
            }
        }
        if(!$article){
            $this->error('信息页面不存在！');
        }
        if($article['is_open'] <>1){
            $this->error('信息页面设置了前台不显示！');
        }
        $this->assign('article', $article);
        return view();
    }

    //收藏
    public function collection(){
        
        return view();
    }

    //卡包
    public function cardpack(){
        return view();
    }

    //红包、优惠券、兑换券
    public function red(){
        return view();
    }

}
?>