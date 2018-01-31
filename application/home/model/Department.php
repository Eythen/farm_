<?php
namespace app\home\model;
use think\Model;

class Department extends Model {
    //登陆验证
    /*const checkloginurl ='http://ehr.m.septfarm.com/index.php/Api/User/login';
	//获取部门成员列表
	const getDepUserUrl = 'http://ehr.m.septfarm.com/index.php/Api/User/get_list';
	//获取部门信息
	const getDepInfoUrl = 'http://ehr.m.septfarm.com/index.php/Api/Department/get_info';
	//获取顶级部门id
	const getDepTopUrl = 'http://ehr.m.septfarm.com/index.php/Api/Department/get_top';
	//获取子部门列表
	const getDepListUrl = 'http://ehr.m.septfarm.com/index.php/Api/Department/get_list';
	//获取用户信息
	const getUserInfoUrl = 'http://ehr.m.septfarm.com/index.php/Api/User/get_info';*/

	/**
	 * [appId 获取Appid]
	 */
	private function appId(){
		//return key(config('auth'));
	}

	/**
	 * [AUTH_KEY 获取AUTH_KEY]
	 */
	private function authKey(){
	    //return config('AUTH_KEY');
	}

	/**
	 * [AppConfig 获取APP配置信息]
	 */
	private function appConfig(){
		//return config('auth');
	}

	/**
	 * [checklogin 登陆验证 ]
	 * @param  $username $id          [用户名]
	 * @param  $password $id          [密码]
	 * @return [array]                [返回用户id]
	 */
	public function checklogin($username,$password){
	    $data = array();
	    $data['appid'] = $this->appId();
	    $data['username'] = authcode($username,'ENCODE',$this->authKey());// 加密用户名
	    $data['password'] = authcode($password,'ENCODE',$this->authKey()); //加密密码
	    $data['sign'] = authSign($data, $this->appConfig()); // 生成签名
	    $result = json_decode(cpost(self::checkloginurl, $data), 1);
	    if ($result['code'] == 0) {
	        return $result['data'];
	    }else{
	        return false;
	    }
	}

	/**
	 * [getInfo 获取部门信息]
	 * @param  integer $id          [成员ID]
	 * @return [array]              [返回部门信息]
	 */
	public function getInfo($id){
		if($id){
			$map = ['uid', 'eq', $id];
			$dept_id = db('admin')->where($map)->value('department_id');
			if($dept_id){
				$map2 = ['id', 'eq', $dept_id];
				$result = db('department')->where($map2)->value('name');
			}
		}
        if ($result) {
        	return $result;
        }else{
        	return false;
        }
	}

	/**
	 * [getTop 获取顶级部门信息]
	 * @param  integer $id          [岗位ID]
	 * @return [array]              [返回部门信息]
	 */
	public function getTop($id){
		if($id){
			$map2 = ['id', 'eq', $dept_id];
			$dept_id = db('department')->where($map2)->find();
		}

        if ($result['code'] == 0) {
        	return $this->getInfo($result['data']['id']);
        }else{
        	return false;
        }
	}

	/**
	 * [getList 获取部门列表]
	 * @param  integer $id          [部门ID]
	 * @param  integer $fetch_child [是否递归获取子部门 0：否   1：是]
	 * @return [array]              [返回部门列表]
	 */
	public function getList($id, $fetch_child = 0){
		/*$data = array();
		$data['appid'] = $this->appId();
		$data['id'] = $id;
		$data['fetch_child'] = $fetch_child;
		$data['sign'] = authSign($data, $this->appConfig()); // 生成签名
        $result = json_decode(cpost(self::getDepListUrl, $data), 1);*/
        if($id){
			$map['id'] = ['eq', $id];
		}
		if($fetch_child){
			unset($map);
		}
		$result = db('auth_group')->where($map)->select();
		/*dump($result);
		die;*/
        if ($result) {
        	return $result;
        }else{
        	return false;
        }
	}

	/**
	 * [getUser 获取用户信息]
	 * @param  integer $id          [成员ID]
	 * @return [array]              [返回成员信息]
	 */
	public function getUser($id){
		if($id){
			$map = ['uid', 'eq', $id];
			$result = db('admin')->where($map)->find();
		}
        if ($result) {
        	return $result;
        }else{
        	return false;
        }
	}

	/**
	 * [findName 根据成员名称获取用户信息]
	 * @param  string $name         [成员名称]
	 * @return [array]              [返回成员信息]
	 */
	public function findName($name){
		if($id){
			$map = ['uid', 'eq', $id];
			$result = db('admin')->where($map)->value('name');
		}
        if ($result) {
        	return $result;
        }else{
        	return false;
        }
	}

	/**
	 * [findPhone 根据成员手机称获取用户信息]
	 * @param  integer tel          [成员手机]
	 * @return [array]              [返回成员信息]
	 */
	public function findPhone($tel){
		if($id){
			$map = ['uid', 'eq', $id];
			$result = db('admin')->where($map)->value('phone');
		}
        if ($result) {
        	return $result;
        }else{
        	return false;
        }
	}

	/**
	 * [getUserList 获取部门用户列表]
	 * @param  integer $id          [部门ID]
	 * @param  integer $fetch_child [是否递归获取子部门成员 0：否   1：是]
	 * @param  integer $filiale_id  [分公司id 0：广州总公司 1：成都分公司 2：南京分公司 3：北京分公司]
	 * @return [array]              [返回部门成员列表]
	 */
	public function getUserList($id, $fetch_child = 0 ,$filiale_id = ''){
		if($id){
			$map['group_id'] = ['eq', $id];
		}
		if($fetch_child){
			unset($map);
		}

		$result = db('admin')->where($map)->select();
        if ($result) {
        	return $result;
        }else{
        	return false;
        }
	}
}