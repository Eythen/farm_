<?php
/**
      
 * Date: 2015-09-09
 */
namespace app\home\controller;
use app\home\Model;
use \think\Config;
class Template extends Base {
    
    
    /**
     *  模板列表
     */
    public function templateList(){     
         $t = input('t','pc'); // pc or  mobile        
         $m = ($t == 'pc') ? 'home' : 'mobile';

         $arr = scandir(REAL_PATH."template/$t/");
         foreach($arr as $key => $val)
         {
                if($val == '.' || $val == '..' ){
                    continue;                 
                }
                 $template_config[$val] = Config::load(REAL_PATH."template/$t/$val/config.php");
         }
        
        $this->assign('t',$t);        
        // $default_theme =  tpCache("hidden.{$t}_default_theme"); // //$default_theme = db('Config')->where("name='{$t}_default_theme'")->column('value');
        //$template_arr = Config::load(REAL_PATH."template/$m/conf/html.php");    
        $template_arr = Config::load(APP_PATH."$m/conf/html.php");    
        //dump($template_arr);

        $this->assign('default_theme',$template_arr['default_theme']);
        $this->assign('template_config',$template_config);
        return $this->fetch();
    }    
    
    /**
     * 魔板选择
     */
    public function changeTemplate(){        
        
        $t = input('t','pc'); // pc or  mobile        
        $m = ($t == 'pc') ? 'home' : 'mobile';
        $theme = input('key');
        $app_path = REAL_PATH;
        $view_path = $app_path."template/$t/";
        $static = $app_path."template/$t/$theme/Static";

        //$default_theme = tpCache("hidden.{$t}_default_theme"); // 获取原来的配置                
        //tpCache("hidden.{$t}_default_theme",$_GET['key']);
        //tpCache('hidden',array("{$t}_default_theme"=>$_GET['key']));                         
        // 修改文件配置  
         if(!is_writeable(APP_PATH."$m/conf/html.php"))
            return "文件/Application/$m/conf/html.php不可写,不能启用魔板,请修改权限!!!";           
         
		$config_html ="<?php
		return array(
			'VIEW_PATH'       => '$view_path', // 改变某个模块的模板文件目录
			'DEFAULT_THEME'    => '$theme', // 模板名称
			'TMPL_PARSE_STRING'  =>array(
		//                '__PUBLIC__' => '/Common', // 更改默认的/Public 替换规则
                    //'__STATIC__'     => $app_path'template/$t/$theme/Static', // 增加新的image  css js  访问路径  后面给 php 改了
					'__STATIC__'     => '$static', // 增加新的image  css js  访问路径  后面给 php 改了
			   ),
			   //'DATA_CACHE_TIME'=>60, // 查询缓存时间
			);
		?>";
         file_put_contents(APP_PATH."$m/conf/html.php", $config_html);       
        $this->success("操作成功!!!",url('home/template/templateList',array('t'=>$t)));      
    }
}