<?php
/**
 * 优惠券功能
 */
namespace app\wap\controller;
use app\wap\model\Coupon as CouponModel;
class Coupon extends Base{

    public function index(){
        die();
        return view();
	}

    //领取
    public function draw(){
        if(request()->isAjax()){
            if(!session('user_id')){
                $this->error('请先登陆，再来领取！');
            }
            $post = input('post.');
            $map['id'] = $post['cid'];
            $coupon = db('coupon')->where($map)->find();
            if($coupon){
                $time = time();
                //在领取时间内
                if( ($time >= $coupon['send_start_time']) && ($time <= $coupon['send_end_time'])){
                    //在数量内
                    $num = $coupon['createnum'] - $coupon['send_num'];
                    if($coupon['createnum'] == 0){  //设置无限制
                        $num =1;
                    }
                    if($num>0){
                        $update['send_num'] = $coupon['send_num'] + 1;
                        db()->startTrans();
                        try{
                            //更新数据
                            $r = db('coupon')->where($map)->update($update);
                            //加入数据
                            $data = [
                                'uid' => session('user_id'),
                                'cid' => $post['cid'],
                                'type' => $coupon['type'],
                                'send_time' => time(),
                                ];
                            db('coupon_list')->insert($data);
                            db()->commit();
                            return $this->success('领取成功');
                        }
                        catch (Exception $e){
                            db()->rollback();
                            return $this->error('领取失败');
                        }
                    }
                    else{
                        $this->error('已被领取完！');
                    }
                }
                else{
                   $this->error('券已失效，不能领取！');
                }
            }
            else{
                $this->error('领取的券不存在！');
            }
        }
    }
    

}
?>