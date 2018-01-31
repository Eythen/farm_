<?php
/**
 * 快递优惠信息
 * Date: 2018-01-08
 */

namespace app\home\Model;
use think\Model;

class Article extends Model {
	/**
	 * [addExpress 添加快递信息]
	 * @param [type] $url  [查询url]
	 * @param [type] $name [快递名称]
	 * @param [type] $sn   [快递单号]
	 * @param [type] $user_id   [用户id]
	 */
	public function addExpress($url,$name,$sn, $user_id){
		$data['publish_time'] = time();
		$str = "您有一个快递发出，请注意查收！快递名称：" . $name . "快递单号:" . $sn .',<a class="weui-btn weui-btn_plain-primary" href="' . $url . '">点击查看</a>';
        //$data['content'] = htmlspecialchars($str);
        $data['content'] = $str;
        $data['title'] = $name.$sn.'快递信息';
        $data['click'] = mt_rand(1000,1300);
    	$data['add_time'] = time(); 
        $data['admin_id'] = session('uid');
        $data['cat_id'] = 3;

        db()->startTrans();
        try {
	        $article_id = db('article')->strict(false)->insertGetId($data);
	        $message = [
	            'message_id' => $article_id,
	            'user_id' => $user_id,
	            'category' => 3,
	            ];

	        db('user_message')->insert($message);
        	db()->commit();
        }
        catch (Exception $e){
            db()->rollback();
        }
	}


}