<?php
/**
 * 站点信息设置
 */
namespace app\home\controller;
use think\Controller;
use app\wap\model\PointsLog;
use think\Loader;

class Site extends Base {

	/**
	 * [index 提现平台]
	 * @return [type] [description]
	 */
	public function index(){
        if(request()->isAjax()){
            $request = input('request.');

            db()->startTrans();
            try {
                $data['value'] = $request['order_no_pay'];
                $goods = db('config')->where('name="order_no_pay"')->update($data);
               

                $level = $request['level'];
                foreach ($level as $k => $v) {
                   
                    $levelData = [
                        'amount' => $v['amount'],
                        'discount' => $v['discount'] 
                        ] ;
                    $map['level_id'] = $k;
                    db('user_level')->where($map)->strict(false)->update($levelData);
                }
                
            
                db()->commit();
                return $this->success(config('MSG.UPDATE_SUCCESS'));
            }
            catch (Exception $e){
                db()->rollback();
                return $this->error(config('MSG.UPDATE_ERROR'));
            }
        }

        $level = db('user_level')->select();
        $config = db('config')->where('inc_type="shop_info"')->select();

        foreach ($config as $k => $v) {

            if($v['name'] == 'order_no_pay'){
                $site['order_no_pay'] = $v['value'];
                $site['desc']['order_no_pay'] = $v['desc'];
            }

        }

		
        $this->assign('level', $level);
        $this->assign('site', $site);

		return $this->fetch();    
	}

    //清除缓存
    public function cacheDel(){
        $dir = ROOT_PATH."runtime".DS."log";
        delFile($dir);
        $dir = ROOT_PATH."runtime".DS."cache";
        delFile($dir);
        $dir = ROOT_PATH."runtime".DS."temp";
        delFile($dir);
        $dir = ROOT_PATH."public".DS."public".DS."test";
        delFile($dir);
        $this->success('清理成功');
    }

    

   
}
?>