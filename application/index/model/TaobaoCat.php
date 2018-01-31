<?php
/**
 * 权限组模型
 */
namespace app\index\Model;
use think\Model;
use think\Validate;

class TaobaoCat extends Model {

	protected $table = 'yq_taobao_cat';

	/*$validate = new Validate([
			['param','require|array','参数必填|参数必须为数组'],
			['mobile','require|/1[34578]{1}\d{9}$/','手机号错误|手机号错误'],
			['template','require','模板id错误'],
		]);
		if (!$validate->check($data)) {
			return $validate->getError();
		}*/

	/**
	 * [cat 商品分类]
	 * @return [type] [description]
	 */
	public function cat(){
		$data = cache('taobao_cat');
		if(empty($data)){
			$data = db('taobao_cat')->select();
			cache('taobao_cat', $data);
		}
		return $data;

	}

	/**
	 * [getCatName 获取分类名字]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getCatName($id){
		$cat = $this->cat();
		$ar = array_column($cat, 'name', 'id');
		$data = $ar[$id];
		
		return $data;
	}


}