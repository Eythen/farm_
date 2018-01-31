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
        
        //查是否有新信息
        $message_map = [
            'status' => 0,
            'user_id' => session('user_id'),
            ];
        $has_message = db('user_message')->where($message_map)->count();
        //站内信息
        $map3['is_open'] = 1;
        $map3['cat_id'] = 1;
        $system_total = db('article')->where($map3)->count();
        $map4['user_id'] = session('user_id');
        $map4['category'] = 1;
        $system_seed = db('user_message')->where($map4)->count();
        $system_num = $system_total - $system_seed; //相差数就是未看的
        //未看的总量
        $has_message += $system_num;

        $this->assign([
            'level_name' => $level_name,
            'head_pic' => $this->user['head_pic'],
            'has_message' => $has_message,
            ]);

        return view();
    }

    //注册
    public function add(){
        if (request()->isPost()){
            //短信验证
            if(input('code')<> cookie('sendcode') || empty(cookie('sendcode'))){
                $this->error("手机验证码不正确！");
            }
            $data = array(
                'mobile'=>input('mobile'),
                'password'=>input('password'),
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
                $this->error('已经存在这个手机号，不能再使用！');
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
        //站内信息
        $map3['is_open'] = 1;
        $map3['cat_id'] = 1;
        $system_total = db('article')->where($map3)->count();
        $map4['user_id'] = session('user_id');
        $map4['category'] = 1;
        $system_seed = db('user_message')->where($map4)->count();
        $system_num = $system_total - $system_seed; //相差数就是未看的

        //优惠信息
        $map['category'] = 2;
        $map['status'] = 0;
        $map['user_id'] = session('user_id');
        $coupon_num = db('user_message')->where($map)->count();
        //发货信息
        $map2['category'] = 3;
        $map2['status'] = 0;
        $map2['user_id'] = session('user_id');
        $express_num = db('user_message')->where($map2)->count();
        
        
        $this->assign([
            'coupon_num' => $coupon_num,
            'express_num' => $express_num,
            'system_num' => $system_num,
            ]);
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
                $orders[$k]['num'] = count($data);
                if ($v['order_status'] != 3){
                    if ($v['shipping_status'] > 0 && $v['order_status'] == 1){
                        $orders[$k]['status'] = '待收货';
                    }elseif ($v['pay_status'] == 0){
                        $orders[$k]['status'] = '待付款';
                    }elseif ($v['shipping_status'] == 0){
                        $orders[$k]['status'] = '待发货';
                    }else{
                        $orders[$k]['status'] = '交易成功';
                    }
                }
            }
            return $orders;
        }
    }

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

    //退订
    public function returns(){
        $map['og.order_id'] = input('order_id');
        $goods_id = input('goods_id');
        if ($goods_id){
            //退单个商品
            $map['og.goods_id'] = $goods_id;
        }
        $returns = db('order_goods')
            ->alias('og')
            ->field('o.order_id,o.order_sn,og.goods_id,og.goods_num')
            ->join('order o','o.order_id=og.order_id')
            ->where($map)
            ->select();
        foreach ($returns as $k => $v){
            $map['og.goods_id'] = $v['goods_id'];
            db('order_goods')->alias('og')->where($map)->setField('is_send','4');
            $returns[$k]['addtime'] = time();
            $returns[$k]['user_id'] = $this->user_id;
        }
        $res = db('return')->insertAll($returns);
        if ($res){
            $this->success('申请退订成功，请等待客服处理！',url('users/order'));
        }else{
            $this->error('申请退订失败，请稍后再试！');
        }
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
                        $map['user_id'] = session('user_id');
                        $map['message_id'] = $v['article_id'];
                        $has_message = db('user_message')->where($map)->find();
                        if(!$has_message){
                            $data['lists'][$k]['status'] = 0;
                        }
                        else{
                            $data['lists'][$k]['status'] = 1;
                        }
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
                    'a.cat_id' => $type ,
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
        if(in_array($article['cat_id'],[2,3])){ //消息处理
            $r = db('user_message')->where(['message_id'=> $article['article_id'], 'user_id' => session('user_id')])->find();
            if(!$r){
                $this->error('用户权限不足！');
            }
            if($r['status']==0){
                db('user_message')->where(['message_id'=> $article['article_id'], 'user_id' => session('user_id')])->setField('status', 1);
            }
        }
        if($article['cat_id']==1){ //内部处理 未读取就不记录，已读取记录到数据表
            $r = db('user_message')->where(['message_id'=> $article['article_id'], 'user_id' => session('user_id')])->find();
            if(!$r){
                $data = [
                    'message_id' => $article_id,
                    'user_id' => session('user_id'),
                    'category' => 1,
                    'status' => 1,
                ];
                db('user_message')->insert($data);
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
        if(request()->isAjax()){
            $get = input('get.');
            $page = $get['num']?$get['num']:1;
            $pagesize = $get['size']?$get['size']:10;
            $offset = ($page-1)*$pagesize;

            $data['lists'] = [];
            $data['length'] = db('goods_collect')->where($map)->order('add_time desc')->count();
            if($data['length']){
                $map['c.user_id'] = session('user_id');
                $data['lists'] = db('goods_collect')
                    ->alias('c')
                    ->field('g.goods_name, g.goods_remark, g.original_img, g.shop_price, c.goods_id, c.add_time')
                    ->where($map)
                    ->join('goods g', 'c.goods_id = g. goods_id')
                    ->order('c.add_time desc')
                    ->limit($offset, $pagesize)
                    ->select();
                foreach ($data['lists'] as $k => $v) {
                    $data['lists'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                    $data['lists'][$k]['url'] = url('product/details', ['goods_id' => $v['goods_id']]);
                    $data['lists'][$k]['page'] = $page;
                }
            }
            return $data;
        }

        return view();
    }

    //卡包
    public function cardpack(){
        return view();
    }

    //红包、优惠券、兑换券
    public function red(){
        $type = input('type');
        $this->assign('type', $type);
        $type_name = config('coupon_type')[$type];
        if($type == 1){
            $type_name = '优惠券';
        }
        $this->assign('type_name', $type_name);
        return view();
    }


    //优惠券
    public function coupon(){
        if(request()->isAjax()){
            $request = input('get.');
            $page = $request['num']?$request['num']:1;
            $pagesize = $request['size']?$request['size']:10;
            $offset = ($page-1)*$pagesize;

            $map['c.type'] = $request['type'];
            $curNavIndex = $request['curNavIndex'];
            $time = time();
            if($curNavIndex == 0){  //可用              
                $map['c.use_start_time'] = ['<=', $time];
                $map['c.use_end_time'] = ['>=', $time];
            }
            elseif ($curNavIndex == 1){  //已使用
                $map['l.use_time'] = ['>', 0];
            }
            else{                       //已过期
                $map['c.use_end_time'] = ['<=', $time];
            }
            $map['l.uid'] = session('user_id');
            $data['total'] = db('coupon_list')
                ->alias('l')
                ->join('coupon c', 'l.cid = c.id')
                ->where($map)
                ->count();
            //有数据    
            if($data['total']){
                $data['rows'] = db('coupon_list')
                ->alias('l')
                ->join('coupon c', 'l.cid = c.id')
                ->field('l.id, l.cid, l.use_time, c.type, c.condition, c.use_start_time, c.use_end_time, c.money')
                ->where($map)
                ->limit($offset, $pagesize)
                ->select();
                foreach ($data['rows'] as $k => $v) {
                    $data['rows'][$k]['time'] = date('Y-m-d',$v['use_start_time'])."-".date('Y-m-d',$v['use_end_time']);
                    $data['rows'][$k]['type_name'] = config('coupon_type')[$v['type']];
                    $data['rows'][$k]['img'] = SITE_URL."/public/skin/img/icon/envelope.png";
                    $timep = $v['use_end_time'] - time();
                    if($timep < 0){
                        $data['rows'][$k]['pdavailable'] = "已过期";
                    }
                    elseif ($v['use_time'] > 0) {
                        $data['rows'][$k]['pdavailable'] = "已使用";
                    }
                    else{
                        $data['rows'][$k]['pdavailable'] = "可用";
                    }    
                }
            }
           /* dump($data);
            halt($request);*/
            return $data;
        }
    }
}
?>