<?php
/**
 * 预订
 */
namespace app\wap\model;
use think\Model;
class Book extends Model{
    /**
     * [totalPrice 计算总价]
     * @param  string $coupon_red      [红包ID]
     * @param  string $coupon_discount [折扣ID]
     * @param  string $is_full         [2为总价的20%，]
     * @return [type]                  [description]
     */
    public function totalPrice($coupon_red = '', $coupon_discount = '', $is_full = ''){
        //算商品总价
        $map['user_id'] = session('user_id');
        $map['selected'] = 1;
        $list = db('book_cart')->where($map)->order('combo_id desc, goods_id desc')->select();
        if(!$list){
            $this->error('您还没有选购商品！请按返回键选择商品！');
        }
        $total_money = 0;
        foreach ($list as $k => $v) {
            if($v['combo_id']>0){
                $combo = db('book_combo')->where('id', $v['combo_id'])->find();
                $total_money += $v['goods_num'] * $combo['shop_price'];

            }
            if($v['goods_id']>0){
                $goods = db('book_goods')->where('goods_id', $v['goods_id'])->find();
                $total_money += $v['goods_num'] * $goods['shop_price'];
            }
        }
        //计算减去红包
        if($coupon_red){
            $coupon = db('coupon')->where('id', $coupon_red)->value('money');
            $total_money = round(($total_money - $coupon), 2);
        }
        if($total_money<0){
            $this->error('系统有误，不能处理！');
        }
        //计算折扣后价
        if($coupon_discount){
            $coupon = db('coupon')->where('id', $coupon_discount)->value('money');
            $total_money = round(($total_money * $coupon)/10, 2) ;
        }
        //选择支付20%
        if($is_full == 2){
            $total_money = round($total_money * 0.2, 2) ;
        }
        return $total_money;
    }

    /**
     * [order 预订下单]
     * @param  string $coupon_red      [红包ID]
     * @param  string $coupon_discount [折扣ID]
     * @param  string $is_full         [2为总价的20%，]
     * @return [type]                  [description]
     */
    public function order($coupon_red = '', $coupon_discount = '', $is_full = ''){
        //算商品总价
        $map['user_id'] = session('user_id');
        $map['selected'] = 1;
        $list = db('book_cart')->where($map)->order('combo_id desc, goods_id desc')->select();
        if(!$list){
            $this->error('您还没有选购商品！请按返回键选择商品！');
        }
        $total_money = 0; 
        $combo_goods_r = [];  
        $goods_r = [];  
        foreach ($list as $k => $v) {
            if($v['combo_id']>0){
                $combo = db('book_combo')->where('id', $v['combo_id'])->find();
                $combo_goods_map['goods_id'] = ['in', $combo['goods_content']];
                $combo_goods = db('book_combo_goods')->where($combo_goods_map)->select();
                foreach ($combo_goods as $kk => $vv) {
                    $combo_goods_r[$v['combo_id']][$kk]['combo_id'] = $v['combo_id'];
                    $combo_goods_r[$v['combo_id']][$kk]['goods_id'] = $vv['goods_id'];
                    $combo_goods_r[$v['combo_id']][$kk]['goods_name'] = $vv['goods_name'];
                    $combo_goods_r[$v['combo_id']][$kk]['goods_price'] = $vv['shop_price'];
                    $combo_goods_r[$v['combo_id']][$kk]['goods_num'] = $v['goods_num'];
                }
                $total_money += $v['goods_num'] * $combo['shop_price'];

            }
            if($v['goods_id']>0){
                $goods = db('book_goods')->where('goods_id', $v['goods_id'])->find();
                $goods_r[$v['goods_id']]['goods_id'] = $goods['goods_id'];
                $goods_r[$v['goods_id']]['goods_name'] = $goods['goods_name'];
                $goods_r[$v['goods_id']]['goods_price'] = $goods['shop_price'];
                $goods_r[$v['goods_id']]['goods_num'] = $v['goods_num'];
                $total_money += $v['goods_num'] * $goods['shop_price'];
            }
        }

        $total_amount = $total_money; //订单总价
        //计算减去红包
        if($coupon_red){
            $coupon_red_money = db('coupon')->where('id', $coupon_red)->value('money');
            $total_money = round(($total_money - $coupon_red_money), 2);
        }
        if($total_money<0){
            $this->error('系统有误，不能处理！');
        }
        //计算折扣后价
        if($coupon_discount){
            $coupon = db('coupon')->where('id', $coupon_discount)->value('money');
            $discount_money = round(($total_money * $coupon)/10, 2);
            $coupon_discount_money = $total_money - $discount_money; //折扣后省的钱
            $total_money = $discount_money;
        }
        $order_amount = $total_money; //应付款总金额
        //支付20%
        $amount20 = 0;
        $amount20 = round($total_money * 0.2, 2) ;
        $amount80 = $order_amount - $amount20;
        //订桌信息
        $table = db('book_temp')->where('user_id', session('user_id'))->order('id desc')->find();
        //转化下单时间为unix_timestamp
        $at_time_unix = $this->atTimeUnix($table['at_time']);
        
        $order_sn ='book'.date('YmdHis').'_'.rand(1000,9999).rand(10,99);
        $order_sn2 ='book'.date('YmdHis').'_2_'.rand(1000,9999).rand(10,99);
        $data = [
            'coupon_discount' => $coupon_discount_money?$coupon_discount_money:0,
            'coupon_red' => $coupon_red_money?$coupon_red_money:0,
            'total_amount' => $total_amount,
            'order_amount' => $order_amount,
            'amount20' => $amount20,
            'amount80' => $amount80,
            'add_time' => time(),
            'is_full' => $is_full,
            'order_sn' => $order_sn,
            'order_sn2' => $order_sn2,
            'user_id' => session('user_id'),
            'at_time' => $table['at_time'],
            'at_time_unix' => $at_time_unix,
            'table' => $table['table'],
            'mobile' => $table['mobile'],
            'name' => $table['name'],
            'people_num' => $table['people_num'],
            'user_note' => $table['remark'],

            ];


        //提交前再次查桌子是否被订
        $table_r = explode(',', $table['table']);
        $table_r = array_filter($table_r);
        $booked = $this->booked($table['at_time']); //已订了的桌子
        $table_booked = '';
        foreach ($table_r as $k => $v) {
            if(in_array($v, $booked)){
                $table_booked .= config('seat')[$v].",";
            }
        }
        if($table_booked){
            $table_booked = trim($table_booked, ',');
            return $table_booked;//中断退出
        }
        //提交前再次查桌子是否被订end
        
        db()->startTrans();
        try{
            
            $order_id = db('book_order')->insertGetId($data);

            $combo_goods_r2 = [];
            $i = 0;
            foreach ($combo_goods_r as $k => $v) {
                foreach ($v as $kk => $vv) {
                    $combo_goods_r2[$i]['combo_id'] = $vv['combo_id'];
                    $combo_goods_r2[$i]['goods_id'] = $vv['goods_id'];
                    $combo_goods_r2[$i]['goods_name'] = $vv['goods_name'];
                    $combo_goods_r2[$i]['goods_price'] = $vv['goods_price'];
                    $combo_goods_r2[$i]['goods_num'] = $vv['goods_num'];
                    $combo_goods_r2[$i]['order_id'] = $order_id;
                    $i++;
                }
            }
            db('book_order_combo_goods')->insertAll($combo_goods_r2);  //添加套餐

            //处理单品
            if($goods_r){
                foreach ($goods_r as $k => $v) {
                   $goods_r[$k]['order_id'] = $order_id;
                }
                db('book_order_goods')->insertAll($goods_r);  //添加单品
            }
            //处理红包
            if($coupon_red){
                $coupon_red_data['order_id'] = $order_id;
                $coupon_red_data['is_book'] = 2;
                $coupon_red_data['use_time'] = time();

                $coupon_red_map['uid'] = session('user_id');
                $coupon_red_map['cid'] = $coupon_red;
                db('coupon_list')->where($coupon_red_map)->update($coupon_red_data);
            }
            //处理折扣券
            if($coupon_discount){
                $coupon_discount_data['order_id'] = $order_id;
                $coupon_discount_data['is_book'] = 2;
                $coupon_discount_data['use_time'] = time();

                $coupon_discount_map['uid'] = session('user_id');
                $coupon_discount_map['cid'] = $coupon_discount;
                db('coupon_list')->where($coupon_discount_map)->update($coupon_discount_data);
            }
            //清空已下单购物车
            $cartMap['user_id'] = session('user_id');
            $cartMap['selected'] = 1;
            db('book_cart')->where($cartMap)->delete();
            db()->commit();
            return $order_id;
        } catch (Exception $e){
            db()->rollback();
            return 0;
        }


    }

    /**
     * [orderDetail 订餐详情]
     * @param  [type] $map    [ $map['order_id']一定要有  ]
     * @return [array]           [description]
     */
    public function orderDetail($map){
        $order = db('book_order')->where($map)->find();
        if(!$order){
            $this->error('订单不存在！');
        }
        //订桌处理
        $tables = explode(',', $order['table']);
        $tables = array_filter($tables);
        $order['seat'] = "";
        foreach ($tables as $k => $v) {
            $order['seat'] .= config('seat')[$v].",";
        }
        //处理套餐内容
        $order_combo_goods = db('book_order_combo_goods')->where('order_id', $map['order_id'])->select();
        $order_combo = [];
        foreach ($order_combo_goods as $k => $v) {
            $combo = db('book_combo')
                ->field('id, goods_name, original_img, shop_price, goods_content')
                ->where('id', $v['combo_id'])
                ->find();
            $order_combo[$combo['id']]['goods_name'] = $combo['goods_name'];
            $order_combo[$combo['id']]['goods_price'] = $combo['shop_price'];
            $order_combo[$combo['id']]['goods_num'] = $v['goods_num'];
            $order_combo[$combo['id']]['original_img'] = config('img_prefix').$combo['original_img'];
            $order_combo[$combo['id']]['goods_content'] .= $v['goods_name']. ',';
        }

        foreach ($order_combo as $k => $v) {
            $order_combo[$k]['goods_content'] = trim($v['goods_content'], ',');
        }
        //处理单品内容
        $order_goods = db('book_order_goods')
            ->alias('og')
            ->field('og.*,bg.original_img')
            ->join('book_goods bg','og.goods_id=bg.goods_id')
            ->where('og.order_id', $map['order_id'])
            ->select();

        foreach ($order_goods as $k2 => $v2){
            $order_goods[$k2]['original_img'] = config('img_prefix').$v2['original_img'];
        }

        $order['order_goods'] = $order_goods;
        $order['order_combo'] = $order_combo;

        return $order;
    }


    /**
     * [orderList 订餐列表]
     * @param  [type] $map       [  查询条件 ]
     * @return [array]           [description]
     */
    public function orderList($map, $offset, $limit){
        
        $orderlist = db('book_order')
                    ->field('table, order_id, order_sn, at_time, order_status, people_num, order_amount, is_full, amount20, amount80, pay_status, add_time')
                    ->where($map)
                    ->limit($offset, $limit)
                    ->order('order_id desc')
                    ->select();

        //订桌处理
        foreach ($orderlist as $k => $v) {
            $tables = explode(',', $v['table']);
            $tables = array_filter($tables);
            $orderlist[$k]['seat'] = "";
            foreach ($tables as $kk => $vv) {
                $orderlist[$k]['seat'] .= config('seat')[$vv].",";
            }
        }
        foreach ($orderlist as $k => $v) {
            $orderlist[$k]['seat'] = trim($v['seat'], ',');
        }

        return $orderlist;
    }

    /**
     * [tableStatus 位置状态]
     * @param  [type] $table [位置]
     * @param  [string] $time  [预定时间 ]
     * @return [type]        [description]
     */
    public function tableStatus($table, $time){
        $map['at_time_unix'] = $this->atTimeUnix($time);      
        $map['order_status'] = 0;   //   
        $tables = db('book_order')->where($map)->column('table');
        $tables = ','.implode(',', $tables).',';
        $table = ','.$table.',';

        if(stripos($tables, $table) !== false){
            return 1;
        }
        else{
            return 0;
        }
    }

    /**
     * [atTimeUnix 订餐时间转unix_timestamp]
     * @param  [type] $at_time [ 2017年12月28日 上午8点]
     * @return [type]          [description]
     */
    public function atTimeUnix($at_time){
        $time = $at_time;
        $at_time_unix = 0;
        $at_time = explode('日', $time);
        $at_time = str_replace(['年', '月'], '-', $at_time[0]);

        if(stripos($time, '上午8点')){
            $at_time_unix = $at_time." 8:00:00";
        }
        if(stripos($time, '下午2点')){
            $at_time_unix = $at_time." 14:00:00";
        }
        if(stripos($time, '晚上8点')){
            $at_time_unix = $at_time." 20:00:00";
        }

        $at_time_unix = strtotime($at_time_unix);
        if(!$at_time_unix){
            $this->error('请正确选择用餐时间');
        }

        return $at_time_unix;
    }

    /**
     * [booked 已订了的桌子]
     * @param  [string] $at_time [例2017年12月21日 晚上8点]
     * @return [type]          [description]
     */
    public function booked($at_time){
        $map['at_time_unix'] = $this->atTimeUnix($at_time);
        $map['order_status'] = 0;
        $booked = db('book_order')->where($map)->column('table');
        $booked = implode(',', $booked);
        $booked = trim($booked, ',');
        $booked = explode(',', $booked);

        return $booked;

    }

}
?>