<?php

namespace app\api\service;


use app\api\model\BookOrder;
use app\api\model\Goods;
use app\api\model\Order as OrderModel;
use think\Db;
use think\Exception;
use think\Loader;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($data,&$msg){
        if ($data['result_code'] == 'SUCCESS'){
            $order_sn = $data['out_trade_no'];
            $res = strpos($order_sn,'OD');
            if($res === false){
                $table = 'order';
            }else{
                $table = 'book_order';
            }
            Db::startTrans();
            try{
                if ($table == 'order'){
                    $order = OrderModel::get(['order_sn'=>$order_sn]);
                }elseif ($table == 'book_order'){
                    $order = BookOrder::get(['order_sn'=>$order_sn]);
                }

                if ($order->pay_status == 0){
                    if ($table == 'order'){
                        $OrderModel = new OrderModel();
                        $status = $OrderModel->checkOrderStock($order->order_id);
                        $this->reduceStock($status);
                    }
                    $this->updateOrderStatus($order->order_id,$table);
                }

                Db::commit();
                return true;
            }
            catch (Exception $e){
                Db::rollback();
                return false;
            }
        }else{
            return true;
        }
    }

    private function reduceStock($status){
        foreach ($status['goods'] as $good){
            Goods::where('goods_id','=',$good['goods_id'])
                ->setDec('store_count',$good['goods_num']);
        }
    }

    private function updateOrderStatus($order_id,$table){
        db($table)->where('order_id','=',$order_id)
            ->update(['pay_status'=>1,'pay_code'=>'wx','pay_code'=>'微信','pay_time'=>time()]);
    }
}