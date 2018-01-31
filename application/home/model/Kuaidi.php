<?php
/**
 * 物流模型
 */
namespace app\home\Model;
use think\Model;
use think\Validate;

class Kuaidi extends Model {

	protected $table = 'yq_kuaidi';

	/*$validate = new Validate([
			['param','require|array','参数必填|参数必须为数组'],
			['mobile','require|/1[34578]{1}\d{9}$/','手机号错误|手机号错误'],
			['template','require','模板id错误'],
		]);
		if (!$validate->check($data)) {
			return $validate->getError();
		}*/

	/**
	 * [getCat 快递列表]
	 * @param  [array] $map [查询条件]
	 * @return [type] [description]
	 */
	public function getCat($map=[]){
		$cat = cache('kuaidi_cat_name');
		if(empty($cat)){
			$cat = db('kuaidi_cat')->where($map)->select();
			$cat = array_column($cat, 'name', 'code');
			cache('kuaidi_cat_name', $cat, 3600);
		}
		
		return $cat;
	}

	/**
	 * [getName 根据代号获取名字]
	 * @param  [type] $code [代号]
	 * @return [type]       [description]
	 */
	public function getName($code){
		$cat = $this->getCat(['status'=>1]);
		
		$name = $cat[$code];
		return $name;
	}

}