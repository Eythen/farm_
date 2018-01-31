<?php
/**
 * 购物车
 */
namespace app\wap\controller;
use app\wap\model\Cart as CartModel;

class Cart extends Base{

    public function index(){
        $carts = $this->getCart(session('user_id'));
        $this->assign(array(
            'carts'=>$carts,
        ));
        return view();
	}

	public function getCart($user_id){
        $carts = db('cart')->alias('c')->field('c.id,c.goods_id,c.goods_name,c.goods_num,c.selected,g.original_img,g.store_count,g.shop_price')->join('goods g','c.goods_id=g.goods_id')->where('user_id',$user_id)->select();
        return $carts;
    }

    public function ajaxAddCart(){
        if (request()->isAjax()){
            $data = array();
            $post = input('post.');
            $user_id = session('user_id');
            if ($user_id){
                $map['user_id'] = $user_id;
                $map['goods_id'] = $post['goods_id'];
//                if ($post['goods_spec']){
//                    $post['spec_key'] = join('_',$post['goods_spec']);
//                    $map['spec_key'] = $post['spec_key'];
//                    foreach ($post['goods_spec'] as $k => $v){
//                        $item = db('spec_item')->where('id',$v)->value('item');
//                        $post['spec_key_name'] .= $k.":".$item." ";
//                    }
//                    unset($post['goods_spec']);
//                }
                //检验该用户的购物车是否已经有此商品
                $isadd = db('cart')->field('id,goods_num')->where($map)->find();
                if ($isadd['id']){
                    //存在
                    $res = db('cart')->where('id',$isadd['id'])->setInc('goods_num',$post['goods_num']);
                }else{
                    //不存在
                    $goods_info = db('goods')->field('goods_sn,goods_name')->find($post['goods_id']);
                    $post['user_id'] = $user_id;
                    $post['goods_sn'] = $goods_info['goods_sn'];
                    $post['goods_name'] = $goods_info['goods_name'];
                    $post['add_time'] = time();
                    $res = db('cart')->insert($post);
                }
                if ($res = 1){
                    $data['status'] = 1;
                    $data['msg'] = '加入购物车成功！';
                }else{
                    $data['status'] = -2;
                    $data['msg'] = '加入购物车失败！';
                }
            }else{
                $data['status'] = -1;
                $data['msg'] = '请登录！';
            }
            return $data;
        }
    }

    //ajax删除商品
    public function ajaxDelCart(){
	    if (request()->isAjax()){
	        $data = array();
            $ids = input('ids/a');
            $res = db('cart')->delete($ids);
            if ($res > 0){
                $data['status'] = 1;
                $data['msg'] = "删除成功！";
            }else{
                $data['status'] = "删除失败！";
            }
            return $data;
        }
    }

    //ajax编辑商品
    public function ajaxEdit(){
        if (request()->isAjax()){
            $id = input('id');
            $type = input('type');
            if ($type == 'inc'){
                db('cart')->where('id',$id)->setInc('goods_num');
            }else{
                db('cart')->where('id',$id)->setDec('goods_num');
            }
        }
    }

    //订单
    public function order(){
        //地址
        $address = db('user_address')->field('address_id,consignee,mobile,address')->where('user_id',session('user_id'))->where('is_default','1')->find();
        if (!$address){
            $this->error('请添加默认地址后再操作',url('Users/addaddress'));
        }

        //物品
        $goods_id = input('goods_id');
        $goods_num = input('num');
        $ids = input('ids/a');
        $money = 0;

        if ($goods_id && $goods_num){
            //直接购买
            $order = db('goods')
                ->field('goods_name,original_img,shop_price,market_price,goods_remark,'.$goods_num.' as goods_num')
                ->where('goods_id','=',$goods_id)
                ->select();
            $money = $order[0]['shop_price'] * $goods_num;
        }elseif (count($ids) > 0){
            $ids = implode(',',$ids);
            //购物车结算
            $order = db('cart')
                ->alias('c')
                ->field('c.id as cart_id,c.goods_id,c.goods_name,c.goods_num,g.original_img,g.shipping_price,g.shop_price,g.market_price')
                ->join('goods g','c.goods_id=g.goods_id')
                ->where('c.user_id',session('user_id'))
                ->where('c.id','in',$ids)
                ->select();
            foreach ($order as $k => $v){
                $money += $v['goods_num'] * $v['shop_price'];
            }
        }

        $time = time();
        $map['cl.uid'] = session('user_id');
        $map['c.use_start_time'] = ['<=', $time];
        $map['c.use_end_time'] = ['>=', $time];
        $map['cl.use_time'] = ['=', 0];
        $map['c.condition'] = ['<=',$money];
        //红包
        $red = db('coupon_list')
            ->alias('cl')
            ->field('c.name as title,c.money as value,cl.id')
            ->join('coupon c','cl.cid=c.id')
            ->where($map)
            ->where('c.type','=','5')
            ->select();
        //折扣券
        $rebate = db('coupon_list')
            ->alias('cl')
            ->field('c.name as title,c.money as value,cl.id')
            ->join('coupon c','cl.cid=c.id')
            ->where($map)
            ->where('c.type','=','7')
            ->select();


        $this->assign(array(
            'order' => $order,
            'money' => $money,
            'address' => $address,
            'red' => json_encode($red),
            'rebate' => json_encode($rebate),
        ));
        return view();
    }

    public function addOrder(){
        $post = input('post.');
        $user_id = session('user_id');
        if ($user_id){
            $cart = new CartModel();
            $res = $cart->addOrder($post,$user_id);
            if ($res['code'] == 1){
                $this->redirect('Wap/Cart/payment', ['order_id' => $res['order_id']]);
            }else{
                $this->error($res['msg']);
            }
        }else{
            $this->error('请登陆',url('Login/index'));
        }
    }

    public function ajaxOrder(){
        if (request()->isAjax()){
            $data = array();
            $post = input('post.');
            $user_id = session('user_id');
            if ($user_id){
                $cart = new CartModel();
                $res = $cart->addOrder($post,$user_id);
                if ($res !== flase){
                    $data['status'] = 1;
                    $data['id'] = $res;
                }else{
                    $data['status'] = 2;
                    $data['msg'] = "添加订单失败！";
                }
            }else{
                $data['status'] = 3;
                $data['msg'] = "请登录！";
            }
            return $data;
        }
    }

    //付款详情
    public function payment(){
//        $users = db('users')->field('user_id,pay_code,user_money,rebate_money,pay_money,idno')->find(session('user_id'));
//        if (request()->isAjax()){
//            $data = array();
//            $password = input('password');
//            $order_id = input('order_id');
//            $order_amount = input('order_amount');
//            if ($users['pay_code'] == md5(md5($password).config('MD5_KEY'))){
//                $cart = new CartModel();
//                $data = $cart->payment($order_id,$users['user_id'],$order_amount);
//            }
//            return $data;
//        }
//        $this->assign('users',$users);
        $order_id = input('order_id');
        $order = db('order')->field('order_id,order_sn,order_amount')->find($order_id);
        $this->assign('order',$order);
        return view();
    }

    /**
     * [findPayStatus 查看支付状态]
     * @return [type] [如果支付成功，返回订单链接]
     */
    public function findPayStatus(){
        $order_id = input('order_id');
        $map['order_id'] = ['eq', $order_id];
        $status = db('order')->where($map)->value('pay_status');
        $msg = url('details', ['order_id'=> $order_id]);

        //支付成功
        if($status == 1){
            $this->success($msg);
        }
        else{
            echo $status."|||".$msg;
        }
    }

    //付款结果
    public function res(){
        $order_id = input('order_id');
        $orders = db('order')->field('order_id,order_sn,mobile,total_amount')->find($order_id);
        $n = db('order_goods')->where('order_id',$order_id)->sum('goods_num');
        $this->assign(array(
            'orders'=>$orders,
            'n'=>$n,
        ));
        return view();
    }

    //订单详情
    public function details(){
        $order_id = input('order_id');
        $orders = db('order')->field('order_id,order_status,consignee,mobile,address,order_sn,add_time,pay_time,total_amount,goods_price,shipping_price,order_amount,pay_status,shipping_status,coupon_red,coupon_rebate')->find($order_id);
        $order_goods = db('order_goods')->alias('og')->field('og.goods_id,og.goods_name,og.goods_num,og.goods_price,g.original_img,g.goods_remark,og.market_price,og.is_send')->join('goods g','og.goods_id=g.goods_id')->where('order_id',$order_id)->select();
        $n = db('order_goods')->where('order_id',$order_id)->sum('goods_num');

        $this->assign('orders',$orders);
        $this->assign('order_goods',$order_goods);
        $this->assign('n',$n);
        return view();
    }

}
?>