<?php
namespace app\index\controller;
use think\Controller;
use think\Request;

class Base extends Controller{

    /*
     * 初始化操作
     */
    public function _initialize() {  
      session_start();
    	$this->session_id = session_id(); // 当前的 session_id
        define('SESSION_ID',$this->session_id); //将当前的session_id保存为常量，供其它方法调用
        
    }
}
