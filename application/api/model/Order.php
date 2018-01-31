<?php

namespace app\api\model;


use app\lib\exception\CouponException;
use app\lib\exception\OrderException;
use app\lib\exception\UserAddressException;
use think\Db;
use think\Exception;
use think\Model;
use app\home\logic\Orders as OrderLogic;

class Order extends Model
{
    protected $oGoods;

    protected $goods;

    public static function getByWhere($type,$user_id,$page,$size){
        $where['user_id'] = $user_id;
        $where['order_status'] = ['in','0,1,2,4'];
        if ($type == 1){
            //待付款
            $where['order_status'] = 0;
            $where['pay_status'] = 0;
        }elseif ($type == 3){
            //待收货
            $where['order_status'] = 1;
            $where['pay_status'] = 1;
            $where['shipping_status'] = 1;
        }
        $orders = db('order')->field('order_id,order_status,order_sn,total_amount,pay_status,shipping_status,confirm_time,order_amount,coupon_red,coupon_rebate')->where($where)->order('order_id desc')->page($page,$size)->select();
        $orderLogic = new OrderLogic();
        foreach ($orders as $k => $v){
            $data = $orderLogic->getOrderGoods($v['order_id']);
            foreach ($data as $k2 => $v2){
                $data[$k2]['original_img'] = config('img_prefix').$v2['original_img'];
            }
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

    public static function getOrderCount($type,$user_id){
        $where['user_id'] = $user_id;
        $where['order_status'] = ['in','0,1,2,4'];
        if ($type == 1){
            //待付款
            $where['order_status'] = 0;
            $where['pay_status'] = 0;
        }elseif ($type == 3){
            //待收货
            $where['order_status'] = 1;
            $where['pay_status'] = 1;
            $where['shipping_status'] = 1;
        }
        $count = self::where($where)->count('order_id');
        return [
            'count' => $count,
        ];
    }

    public static function getOrderDetail($order_id){
        $orders = db('order')->field('order_id,order_status,consignee,mobile,address,order_sn,add_time,pay_time,total_amount,goods_price,shipping_price,order_amount,pay_status,shipping_status,coupon_red,coupon_rebate')->find($order_id);
        $order_goods = db('order_goods')->alias('og')->field('og.goods_id,og.goods_name,og.goods_num,og.goods_price,g.original_img,g.goods_remark,og.market_price,og.is_send')->join('goods g','og.goods_id=g.goods_id')->where('order_id',$order_id)->select();
        foreach ($order_goods as $k => $v){
            $order_goods[$k]['original_img'] = config('img_prefix').$v['original_img'];
        }
        $orders['order_goods'] = $order_goods;
        return $orders;
    }

    public static function returns($user_id,$order_id,$goods_id){
        $map['og.order_id'] = $order_id;
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
            $returns[$k]['user_id'] = $user_id;
        }
        $res = db('return')->insertAll($returns);
        if ($res){
            return true;
        }else{
            return false;
        }
    }

    public function place($user_id,$data,$goods){
        $this->oGoods = $goods;
        $this->goods = $this->getGoodsByOrder($this->oGoods);
        $status = $this->getOrderStatus();
        return $this->createOrder($user_id,$data,$status);
    }

    private function createOrder($user_id,$data,$status){
        Db::startTrans();
        try{
            $address = $this->getAddress($data['address_id']);
            $order = [
                'order_sn' => 'OD'.date('YmdHis').rand(0000,9999),
                'user_id' => $user_id,
                'consignee' => $address['consignee'],
                'address' => $address['address'],
                'mobile' => $address['mobile'],
                'goods_price' => $status['goods_price'],
                'coupon_price' => '',
                'order_amount' => $status['order_amount'],
                'total_amount' => $status['total_amount'],
                'add_time' => time(),
                'coupon_red' => '',
                'coupon_rebate' => '',
            ];
            if (array_key_exists('red_id',$data)){
                $red = $this->getCoupon(5,$data['red_id']);
                $order['coupon_red'] = $red['money'];
                $order['order_amount'] = $order['order_amount'] - $red['money'];
            }
            if (array_key_exists('rebate_id',$data)){
                $rebate = $this->getCoupon(7,$data['rebate_id']);
                $order['coupon_rebate'] = $rebate['money'];
                $order['order_amount'] = $order['order_amount'] * $rebate['money']/10;
            }
            $order['coupon_price'] = $status['goods_price'] - $order['order_amount'];
            $this->save($order);
            $order_id = $this->order_id;

            db('coupon_list')->where(['uid'=>$user_id,'id'=>['in',[$data['red_id'],$data['rebate_id']]]])->update(['order_id'=>$order_id,'use_time'=>time()]);

            $order_action = array(
                'order_id' => $order_id,
                'action_note' => '您提交了订单，请等待系统确认',
                'log_time' => time(),
                'status_desc' => '提交订单',
            );
            db('order_action')->insert($order_action);

            foreach ($status['goods'] as $v){
                $order_goods = array(
                    'order_id' => $order_id,
                    'goods_id' => $v['goods_id'],
                    'goods_name' => $v['goods_name'],
                    'goods_sn' => $v['goods_sn'],
                    'goods_num' => $v['goods_num'],
                    'market_price' => $v['market_price'],
                    'goods_price' => $v['shop_price'],
                );
                db('order_goods')->insert($order_goods);
            }
            Db::commit();
            return [
                'status' => true,
                'order_id' => $order_id,
            ];
        }
        catch (Exception $e){
            Db::rollback();
            return [
                'status' => false,
            ];
        }
    }

    private function getOrderStatus(){
        $status = [
            'goods_price' => 0,
            'order_amount' => 0,
            'total_amount' => 0,
            'goods' => []
        ];
        foreach ($this->oGoods as $oGood){
            $gStatus = $this->getGoodsStatus($oGood['goods_id'],$oGood['goods_num'],$this->goods);
            $status['goods_price'] += $gStatus['totalPrice'];
            $status['order_amount'] += $gStatus['totalPrice'];
            $status['total_amount'] += $gStatus['totalPrice'];
            array_push($status['goods'],$gStatus['good']);
        }
        return $status;
    }

    private function getGoodsStatus($oGoods_id,$oGoods_num,$goods){
        $index = -1;
        for ($i = 0;$i < count($goods);$i++){
            if ($oGoods_id == $goods[$i]['goods_id']){
                $index = $i;
            }
        }
        if ($index == -1){
            throw new OrderException([
                'msg' => 'id为'.$oGoods_id.'商品不存在，创建订单失败',
            ]);
        }else{
            $good = $goods[$index];
            if ($good['store_count'] - $oGoods_num < 0){
                throw new OrderException([
                    'msg' => 'id为'.$oGoods_id.'商品库存不足，创建订单失败',
                ]);
            }
            $good['goods_num'] = $oGoods_num;
            unset($good['store_count']);
            $gStatus['totalPrice'] = $good['shop_price'] * $oGoods_num;
            $gStatus['good'] = $good;
            return $gStatus;
        }
    }

    private function getGoodsByOrder($oGoods){
        $oGoods_ids = [];
        foreach ($oGoods as $item){
            array_push($oGoods_ids,$item['goods_id']);
        }
        $goods = db('goods')
            ->field('goods_id,goods_name,store_count,goods_sn,market_price,shop_price,shipping_price')
            ->select($oGoods_ids);
        return $goods;
    }

    private function getAddress($address_id){
        $address = UserAddress::get($address_id);
        if (!$address){
            throw new UserAddressException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 90002
            ]);
        }
        return $address->toArray();
    }

    public function getCoupon($type,$id){
        $map['type'] = $type;
        $map['id'] = $id;
        $map['order_id'] = 0;
        $coupon = CouponList::get($map);
        if (!$coupon){
            throw new CouponException([
                'msg' => '优惠券不存在，下单失败',
            ]);
        }
        $entry = Coupon::get($coupon['cid']);
        $coupon['money'] = $entry['money'];
        return $coupon;
    }

    public function checkOrderStock($order_id){
        $oGoods = db('order_goods')
            ->where('order_id','=',$order_id)
            ->select();
        $this->oGoods = $oGoods;
        $this->goods = $this->getGoodsByOrder($oGoods);
        $status = $this->getOrderStatus();
        return $status;
    }
}