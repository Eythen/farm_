<?php
/**
 * 登陆
 */
namespace app\wap\controller;
use app\wap\model\Users;

class Login extends Base{

    public function index(){
        if (input('isurl') == 1){
            $referurl = url("Users/index");
        }else{
            $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url("Users/index");
        }
        $this->assign('referurl', $referurl);
        return view();
	}

	public function ajaxLogin(){
        if (request()->isAjax()){
            $data = input('post.');
            $users = new Users();
            $ur = $users->getByMobile($data['mobile']);
            $arr = array();
            $arr['status'] = $users->login($data);
            if ($arr['status'] == '1') {
                $arr['msg'] = "用户不存在！";
            }
            if ($arr['status'] == '2') {
                $arr['msg'] = "登陆成功！";
            }
            if ($arr['status'] == '3') {
                $arr['msg'] = "密码错误！";
            }
            if ($arr['status'] == '4') {
                $arr['msg'] = "用户冻结！";
            }
            return $arr;
        }
    }

    public function forgetpsw(){
        return view();
    }
}
?>