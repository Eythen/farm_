<?php
/**
 * 权限组模型
 */
namespace app\home\Model;
use think\Model;
use think\Validate;

class Alidayu extends Model {

	protected $table = 'yq_alidayu';

	/*$validate = new Validate([
			['param','require|array','参数必填|参数必须为数组'],
			['mobile','require|/1[34578]{1}\d{9}$/','手机号错误|手机号错误'],
			['template','require','模板id错误'],
		]);
		if (!$validate->check($data)) {
			return $validate->getError();
		}*/
}