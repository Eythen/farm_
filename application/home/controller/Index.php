<?php
/**
 * tpshop
 * ============================================================================

 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: 当燃      
 * Date: 2015-09-09
 */
namespace app\home\controller;


class Index extends Base {

    public function index(){
        if (!session('?umenu')) {
            //die;
            $umenu = model('Menu')->getUserMenu(session('gid'));    //获取所属组菜单
            //dump($umenu);
            session('umenu', $umenu);
        }
        /*  dump(session('gid'));
            dump(session('umenu'));
            die;*/
        //判断是否为推广组
        /*if (in_array(session('uid'),array_keys(model('Users')->getTg()))) {
            //判断推广组成员有没有设置值班
            if (!session('?work_time')) {
                $this->assign('is_it_zy',1);
            }
        }*/
        

        $this->assign('sideMenu',session('umenu'));
        //$this->assign('lists',$list);
        //echo "vvv";
        return $this->fetch();
        /*$this->pushVersion();
        $act_list = session('act_list');
        $menu_list = getMenuList($act_list);
        $this->assign('menu_list',$menu_list);
        $admin_info = getAdminInfo(session('admin_id'));
		$order_amount = db('order')->where("order_status=0 and (pay_status=1 or pay_code='cod')")->count();
		$this->assign('order_amount',$order_amount);
		$this->assign('admin_info',$admin_info);
        $this->display();*/
    }
   
    //public function welcome(){
    public function main(){
        
    	$this->assign('sys_info',$this->get_sys_info());
    	$today = strtotime("-1 day");
    	$count['handle_order'] = db('order')->where("order_status=0 and (pay_status=1 or pay_code='cod')")->count();//待处理订单
    	$count['new_order'] = db('order')->where("add_time>$today")->count();//今天新增订单
    	$count['goods'] =  db('goods')->where("1=1")->count();//商品总数
    	$count['article'] =  db('article')->where("1=1")->count();//文章总数
    	$count['users'] = db('users')->where("1=1")->count();//会员总数
    	$count['today_login'] = db('users')->where("last_login>$today")->count();//今日访问
    	$count['new_users'] = db('users')->where("reg_time>$today")->count();//新增会员
    	$count['comment'] = db('comment')->where("is_show=0")->count();//最新评论
    	$this->assign('count',$count);
       return $this->fetch();
    }
    
    public function get_sys_info(){
		$sys_info['os']             = PHP_OS;
		$sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO';//zlib
		$sys_info['safe_mode']      = (boolean) ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off		
		$sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
		$sys_info['curl']			= function_exists('curl_init') ? 'YES' : 'NO';	
		$sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
		$sys_info['phpv']           = phpversion();
		$sys_info['ip'] 			= GetHostByName($_SERVER['SERVER_NAME']);
		$sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
		$sys_info['max_ex_time'] 	= @ini_get("max_execution_time").'s'; //脚本最大执行时间
		$sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
		$sys_info['domain'] 		= $_SERVER['HTTP_HOST'];
		$sys_info['memory_limit']   = ini_get('memory_limit');		
        $sys_info['version']   	    = file_get_contents(APP_PATH.'version.txt');
		$mysqlinfo = db()->query("SELECT VERSION() as version");
		$sys_info['mysql_version']  = $mysqlinfo['version'];
		if(function_exists("gd_info")){
			$gd = gd_info();
			$sys_info['gdinfo'] 	= $gd['GD Version'];
		}else {
			$sys_info['gdinfo'] 	= "未知";
		}
		return $sys_info;
    }
    
    
    public function pushVersion()
    {            
        if(!empty($_SESSION['isset_push']))
            return false;    
        $_SESSION['isset_push'] = 1;    
        error_reporting(0);//关闭所有错误报告
        $app_path = dirname($_SERVER['SCRIPT_FILENAME']).'/';
        $version_txt_path = $app_path.'/application/version.txt';
        $curent_version = file_get_contents($version_txt_path);

        $vaules = array(            
                'domain'=>$_SERVER['SERVER_NAME'], 
                'last_domain'=>$_SERVER['SERVER_NAME'], 
                'key_num'=>$curent_version, 
                'install_time'=>INSTALL_DATE,
                'serial_number'=>SERIALNUMBER,
         );     
         /*$url = "http://service.tp-shop.cn/index.php?m=Home&c=Index&a=user_push&".http_build_query($vaules);
         stream_context_set_default(array('http' => array('timeout' => 3)));
         file_get_contents($url); */        
    }
    
    /**
     * ajax 修改指定表数据字段  一般修改状态 比如 是否推荐 是否开启 等 图标切换的
     * table,id_name,id_value,field,value
     */
    public function changeTableVal(){  
            $table = input('table'); // 表名
            $id_name = input('id_name'); // 表主键id名
            $id_value = input('id_value'); // 表主键id值
            $field  = input('field'); // 修改哪个字段
            $value  = input('value'); // 修改字段值                        
            db($table)->where("$id_name = $id_value")->update(array($field=>$value)); // 根据条件保存修改的数据
    }	    

}