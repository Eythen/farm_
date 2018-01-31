<?php
/**
 *地区模型
 */
namespace app\home\model;
use think\Model;

class Region extends Model {

	protected $table = 'yq_region';

	/**
	 * [getProvince 查询省份]
	 * @return [type] [description]
	 */
	public function getProvince() {
		return db('region')->field('region_name')->where('region_type=1')->cache(true)->select();
	}

	/**
	 * [getCity 查询市]
	 * @param  [type] $province [省份]
	 */
	public function getCity($province) {
		$region_map['region_name'] = $province;
		$region_map['region_type'] = 1;
		$region_id = db('region')->where($region_map)->getField('region_id');
		$map['parent_id'] = (int)$region_id;
		return db('region')->field('region_name')->where($map)->select();
	}

	/**
	 * [getDistrict 查询县]
	 * @param  [type] $district [县]
	 */
	public function getDistrict($city) {
		$region_map['region_name'] = $city;
		$region_map['region_type'] = 2;
		$region_id = db('region')->where($region_map)->getField('region_id');
		$map['parent_id'] = (int)$region_id;
		return db('region')->field('region_name')->where($map)->select();
	}
}
?>