<?php
/**
 * 任务处理
 * 定时运行，处理未支付的订单
 */
namespace app\index\controller;
use think\Controller;
use think\Loader;

class Task extends Controller{
	/**
     * [todo 每五分钟定时处理未支付的订单]
     * @return [type] [description]
     */
    public function todo(){
        //1处理认养的 ，订单设置无效order_status = 3，花猪设置认养user_id = 0;user_name=null adopt_time=0;
        $end_time = strtotime('- 1 hour');

        $map['add_time'] = ['<', $end_time];
        $map['order_status'] = 0;
        $map['pay_status'] = 0;



        $pig_order = db('pig_order')->where($map)->select();
        $pig_ids = [];
        $pig_order_ids = [];
        if(!empty($pig_order)){
            foreach ($pig_order as $k => $v) {
                $pigs = db('pig_order_pigs')->where('order_id', $v['order_id'])->column('pig_id');
                $pig_ids = array_merge($pig_ids, $pigs);
                array_push($pig_order_ids, $v['order_id']);
            }
            $pig_ids = array_unique($pig_ids);
            $pig_update = [
                'adopt_time' => 0,
                'user_id' => 0,
                'user_name' => null,
                ];
            $pig_update_map['pig_id'] = ['in', $pig_ids];
            $pig_order_map['order_id'] = ['in', $pig_order_ids];
            db()->startTrans();
            try {

                db('pig')->where($pig_update_map)->update($pig_update); //更新认养人信息
                db('pig_order')->where($pig_order_map)->setField('order_status',3);
                db()->commit();
            }
            catch (Exception $e){
                db()->rollback();
            }


        }


        //2处理订桌的 ，订单设置无效order_status = 3，;
        try {
            db('book_order')->where($map)->setField('order_status',3);
            db()->commit();
        }
        catch (Exception $e){
            db()->rollback();
        }  



    }





}
?>