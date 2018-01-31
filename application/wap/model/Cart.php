<?php
/**
 * 购物车
 */
namespace app\wap\model;
use think\Model;
class Cart extends Model{

    /*  新增订单
     *  $data       数组
     *  $user_id    用户id
     *  return      str
     * */
    public function addOrder($data,$user_id){
        $code = array();
        if ($data['goods_id'] && $data['goods_num']){
            //直接购买
            $goods = db('goods')
                ->field('goods_id,goods_name,goods_sn,shop_price,store_count,market_price,'.$data['goods_num'].' as goods_num')
                ->where('goods_id','=',$data['goods_id'])
                ->select();
        }else{
            //获取购物车选择结算商品
            $ids = implode(',',$data['cart_id']);
            $goods = $this
                ->alias('c')
                ->field('c.id,c.goods_id,c.goods_name,c.goods_sn,g.shop_price,g.market_price,g.store_count,c.goods_num')
                ->join('goods g','c.goods_id=g.goods_id')
                ->where('c.id','in',$ids)
                ->select();
        }
        //事务开始
        $this->startTrans();

        //新增订单表
        $order = array(
            'order_sn' => 'OD'.date('YmdHis').rand(0000,9999),
            'user_id' => $user_id,
            'consignee' => $data['consignee'],
            'address' => $data['address'],
            'mobile' => $data['mobile'],
            'goods_price' => $data['goods_price'],
            'coupon_price' => $data['goods_price'] - $data['order_amount'],
            'order_amount' => $data['order_amount'],
            'total_amount' => $data['order_amount'],
            'coupon_red' => $data['red_value'],
            'coupon_rebate' => $data['rebate_value'],
            'add_time' => time(),
        );
        $order_id = db('order')->insertGetId($order);
        if (!$order_id){
            $this->rollback();
            $code['code'] = -1;
            $code['msg'] = "提交订单失败，请稍后再试！";
            return $code;
        }

        //红包、折扣券
        db('coupon_list')->where(['uid'=>$user_id,'id'=>['in',[$data['red_id'],$data['rebate_id']]]])->update(['order_id'=>$order_id,'use_time'=>time()]);

        //新增订单动作表
        $order_action = array(
            'order_id' => $order_id,
            'action_note' => '您提交了订单，请等待系统确认',
            'log_time' => time(),
            'status_desc' => '提交订单',
        );
        $order_action = db('order_action')->insert($order_action);
        if ($order_action != 1){
            $this->rollback();
            $code['code'] = -1;
            $code['msg'] = "提交订单失败，请稍后再试！";
            return $code;
        }

        //新增订单商品表
        foreach ($goods as $v){
            if ($v['goods_num'] > $v['store_count']){
                $this->rollback();
                $code['code'] = -2;
                $code['msg'] = $v['goods_name']."库存不足！";
                return $code;
                break;
            }
            $order_goods = array(
                'order_id' => $order_id,
                'goods_id' => $v['goods_id'],
                'goods_name' => $v['goods_name'],
                'goods_sn' => $v['goods_sn'],
                'goods_num' => $v['goods_num'],
                'market_price' => $v['market_price'],
                'goods_price' => $v['shop_price'],
            );
            $order_goods = db('order_goods')->insert($order_goods);
            if ($order_goods != 1){
                $this->rollback();
                $code['code'] = -1;
                $code['msg'] = "提交订单失败，请稍后再试！";
                return $code;
                break;
            }
            if ($v['id']){
                Cart::destroy($v['id']);
            }
        }
        $this->commit();
        $code['code'] = 1;
        $code['msg'] = "提交订单成功！";
        $code['order_id'] = $order_id;
        return $code;
    }

    /*  付款
     *  $order_id       订单id
     *  $user_id        用户id
     *  $total_amount   订单总额
     *  return          bool
     * */
    public function payment($order_id,$user_id,$order_amount){

        //事务开始
        db()->startTrans();
        //更改订单状态
        $isorder = db('order')->where('order_id',$order_id)->update(array('pay_status'=>1,'pay_time'=>time()));
        //新增订单操作记录表
        $data = array(
            'order_id'=>$order_id,
            'pay_status'=>1,
            'action_note'=>'订单付款成功',
            'log_time'=>time(),
            'status_desc'=>'付款成功'
        );
        $order_action = db('order_action')->insert($data);
        //更改用户金额
        $is_user_money = db('users')->where('user_id',$user_id)->setDec('user_money',$order_amount);
        $is_user_money_used = db('users')->where('user_id',$user_id)->setInc('user_money_used',$order_amount);

        //全返信息
        $user_info = db('users')->field('u.mobile,u.nickname,u.username,u.level,ul.level_name,u.rebate_name,ul.rebate')->alias('u')->join('user_level ul','u.level=ul.level_id')->where('u.user_id',$user_id)->find();
        $order_sn = db('order')->where('order_id',$order_id)->value('order_sn');
        $money = $order_amount * $user_info['rebate'] / 100;
        $rebate_data = array(
            'order_id' => $order_id,
            'order_sn' => $order_sn,
            'order_amount' => $order_amount,
            'pay_time' => time(),
            'user_id' => $user_id,
            'mobile' => $user_info['mobile'],
            'nickname' => $user_info['nickname'],
            'username' => $user_info['username'],
            'level' => $user_info['level'],
            'level_name' => $user_info['level_name'],
            'rebate_name' => $user_info['rebate_name'],
            'rebate' => $user_info['rebate'],
            'money' => $money,
        );
        $rebate = db('rebate')->insert($rebate_data);

        //没有全返账号，发送站内信息提示增加全返账号
        if(!$user_info['rebate_name']){
            $article_data = array(
                'cat_id' => 2,
                'title' => '系统提醒',
                'content' => '由于您消费时系统检测到您无填写全返账号信息，请您尽快完善全返账号信息！',
                'add_time' => time(),
                'publish_time' => time(),
            );
            $article_id = db('article')->insertGetId($article_data);
            $message_data = array(
                'user_id' => $user_id,
                'message_id' => $article_id,
            );
            db('user_message')->insert($message_data);
        }

        //写入积分消耗表
        $points_log_data = array(
            'add_time' => time(),
            'type' => 1,
            'user_id' => $user_id,
            'points_money' => $order_amount,
            'order_id' => $order_id,
            'status' => 1,
        );

        $log = db('points_log')->insert($points_log_data); //写入积分变动表

        if ($isorder && $order_action && $is_user_money && $is_user_money_used && $log && $rebate){
            db()->commit();
            $arr['status'] = 1;
        }else{
            db()->rollback();
            $arr['status'] = 2;
            $arr['msg'] = "付款失败！";
        }

        return $arr;

    }
}
?>