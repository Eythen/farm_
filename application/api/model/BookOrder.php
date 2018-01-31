<?php

namespace app\api\model;


use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\wap\model\Book;
use think\Db;
use think\Exception;
use think\Model;

class BookOrder extends Model
{
    protected $Goods=[];
    protected $oGoods=[];
    protected $ComboGoods=[];
    protected $oComboGoods=[];

    public function place($user_id,$data){
        if (array_key_exists('goods',$data)){
            $this->oGoods = $data['goods'];
            $this->Goods = $this->getGoodsByOrder($data['goods']);
        }
        if (array_key_exists('combo_goods',$data)){
            $this->oComboGoods = $data['combo_goods'];
            $this->ComboGoods = $this->getComboGoodsByOrder($data['combo_goods']);
        }
        $status = $this->getOrderStatus();
        return $this->createOrder($user_id,$data,$status);
    }

    private function createOrder($user_id,$data,$status){

        Db::startTrans();
        try{

            $book_order = [
                'order_sn' => 'book'.date('YmdHis').'_'.rand(1000,9999).rand(10,99),
                'order_sn2' => 'book'.date('YmdHis').'_2_'.rand(1000,9999).rand(10,99),
                'user_id' => $user_id,
                'name' => '',
                'at_time_unix' => time(),
                'at_time' => date("Y年m月d日 ha",time()),
                'table' => $data['table'],
                'mobile' => '',
                'order_amount' => $status['order_amount'],
                'total_amount' => $status['total_amount'],
                'add_time' => time(),
                'user_note' => '',
                'coupon_red' => 0,
                'coupon_discount' => 0,
            ];


            $user = Users::get($user_id);
            if (!$user){
                throw new UserException();
            }
            $book_order['name'] = $user->nickname;
            $book_order['mobile'] = $user->mobile?$user->mobile:0;

            if (array_key_exists('user_note',$data)){
                $book_order['user_note'] = $data['user_note'];
            }

            $order = new Order();
            if (array_key_exists('red_id',$data)){
                $red = $order->getCoupon(5,$data['red_id']);
                $book_order['coupon_red'] = $red['money'];
                $book_order['order_amount'] = $book_order['order_amount'] - $red['money'];
            }
            if (array_key_exists('rebate_id',$data)){
                $rebate = $order->getCoupon(7,$data['rebate_id']);
                $book_order['order_amount'] = $book_order['order_amount'] * $rebate['money']/10;
                $book_order['coupon_discount'] = $book_order['total_amount'] - $book_order['order_amount'] - $book_order['coupon_red'];
            }

            $this->save($book_order);
            $order_id = $this->order_id;

            db('coupon_list')->where(['uid'=>$user_id,'id'=>['in',[$data['red_id'],$data['rebate_id']]]])->update(['order_id'=>$order_id,'use_time'=>time(),'is_book'=>2]);

            if (array_key_exists('goods',$status)){
                foreach ($status['goods'] as $v){
                    $goods = [
                        'order_id' => $order_id,
                        'goods_id' => $v['goods_id'],
                        'goods_name' => $v['goods_name'],
                        'goods_num' => $v['goods_num'],
                        'goods_price' => $v['shop_price']
                    ];
                    db('book_order_goods')->insert($goods);
                }
            }

            if (array_key_exists('combo_goods',$status)){
                foreach ($status['combo_goods'] as $v){
                    $combo_goods = [
                        'order_id' => $order_id,
                        'goods_id' => $v['goods_id'],
                        'goods_name' => $v['goods_name'],
                        'goods_num' => $v['goods_num'],
                        'combo_id' => $v['combo_id'],
                        'goods_price' => $v['shop_price']
                    ];
                    db('book_order_combo_goods')->insert($combo_goods);
                }
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
            'order_amount' => 0,
            'total_amount' => 0,
            'combo_goods' => [],
            'goods' => []
        ];
        foreach ($this->oGoods as $oGood){
            $gStatus = $this->checkGood($oGood['goods_id'],$oGood['goods_num'],$this->Goods);
            $status['order_amount'] += $gStatus['totalPrice'];
            $status['total_amount'] += $gStatus['totalPrice'];
            array_push($status['goods'],$gStatus['good']);
        }
        foreach ($this->oComboGoods as $oComboGood){
            $cgStatus = $this->checkComboGood($oComboGood['goods_id'],$oComboGood['goods_num'],$this->ComboGoods);
            $status['order_amount'] += $cgStatus['totalPrice'];
            $status['total_amount'] += $cgStatus['totalPrice'];
            $status['combo_goods'] = array_merge($status['combo_goods'],$cgStatus['combo_goods']);
        }
        return $status;
    }

    private function checkComboGood($goods_id,$goods_num,$goods){
        $index = -1;
        for ($i=0;$i<count($goods);$i++){
            if ($goods_id == $goods[$i]['id']){
                $index = $i;
            }
        }
        if ($index == -1){
            throw new OrderException([
                'msg' => 'id为'.$goods_id.'套餐不存在，创建订单失败',
            ]);
        }else{
            $good = $goods[$index];
            $gStatus['totalPrice'] = $good['shop_price'] * $goods_num;
            foreach ($good['goods'] as $k => $v){
                $good['goods'][$k]['goods_num'] = $goods_num;
                $good['goods'][$k]['combo_id'] = $goods_id;
            }
            $gStatus['combo_goods'] = $good['goods'];
            return $gStatus;
        }
    }

    private function checkGood($goods_id,$goods_num,$goods){
        $index = -1;
        for ($i=0;$i<count($goods);$i++){
            if ($goods_id == $goods[$i]['goods_id']){
                $index = $i;
            }
        }
        if ($index == -1){
            throw new OrderException([
                'msg' => 'id为'.$goods_id.'商品不存在，创建订单失败',
            ]);
        }else{
            $good = $goods[$index];
            $good['goods_num'] = $goods_num;
            $gStatus['totalPrice'] = $good['shop_price'] * $goods_num;
            $gStatus['good'] = $good;
            return $gStatus;
        }
    }

    private function getComboGoodsByOrder($combo_goods){
        $Combo_good_ids = [];
        foreach ($combo_goods as $item){
            array_push($Combo_good_ids,$item['goods_id']);
        }
        $Combo_goods = db('book_combo')
            ->field('id,goods_name,shop_price,goods_content')
            ->select($Combo_good_ids);
        foreach ($Combo_goods as $k => $v){
            $ids = ltrim($v['goods_content'],',');
            $map['goods_id'] = ['in',$ids];
            $Combo_goods[$k]['goods'] = db('book_combo_goods')
                ->field('goods_id,goods_name,shop_price')
                ->where($map)
                ->select();
        }
        return $Combo_goods;
    }

    private function getGoodsByOrder($goods){
        $Good_ids = [];
        foreach ($goods as $item){
            array_push($Good_ids,$item['goods_id']);
        }
        $Goods = db('book_goods')
            ->field('goods_id,goods_name,shop_price')
            ->select($Good_ids);
        return $Goods;
    }

    public static function getCountByType($user_id,$type){
        $map['user_id'] = $user_id;
        if ($type == 2){
            $map['order_status'] = 0;
            $map['pay_status'] = 0;
        }elseif ($type == 3){
            $map['order_status'] = ['in','0,1'];
            $map['pay_status'] = 1;
        }
        $count = self::where($map)->count('order_id');
        return [
            'count' => $count,
        ];
    }

    public static function getByType($user_id,$type,$page,$size){
        $map['user_id'] = $user_id;
        if ($type == 2){
            $map['order_status'] = 0;
            $map['pay_status'] = 0;
        }elseif ($type == 3){
            $map['order_status'] = ['in','0,1'];
            $map['pay_status'] = 1;
        }
        $BookModel = new Book();
        $list = $BookModel->orderList($map,$page-1,$size);
        foreach ($list as $k => $v){
            if ($v['order_status'] == 0 && $v['pay_status'] == 0){
                $list[$k]['status'] = '待付款';
            }elseif (($v['order_status'] == 0 || $v['order_status'] == 1) && $v['pay_status'] == 1){
                $list[$k]['status'] = '已付款';
            }elseif ($v['order_status'] == 1 && $v['pay_status'] == 1){
                $list[$k]['status'] = '已完成';
            }elseif ($v['order_status'] == 2 && $v['pay_status'] == 1){
                $list[$k]['status'] = '退订中';
            }elseif ($v['order_status'] == 3){
                $list[$k]['status'] = '无效';
            }elseif ($v['order_status'] == 4 && $v['pay_status'] == 3){
                $list[$k]['status'] = '已退订';
            }elseif ($v['order_status'] == 4){
                $list[$k]['status'] = '未到店消费';
            }
        }
        return $list;
    }

    public static function getOrderDetail($user_id,$order_id){
        $BookModel = new Book();
        $map['user_id'] = $user_id;
        $map['order_id'] = $order_id;
        $order = $BookModel->orderDetail($map);
        return $order;
    }
}