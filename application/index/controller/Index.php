<?php
/**
 * $Author: yang 2017-08-15 $
 */ 
namespace app\index\controller;
use think\Loader;
use think\captcha;

class Index extends Base {
    
    
    public function index(){
        header("Location: ".url('wap/Index/index'));               
        // 如果是手机跳转到 手机模块
        if(true == isMobile()){
            header("Location: ".url('wap/Index/index'));
        }
        
        /*$hot_goods = $hot_cate = $cateList = array();
        $sql = "select a.goods_name,a.goods_id,a.shop_price,a.market_price,a.cat_id,b.parent_id_path,b.name from ".config('database.prefix')."goods as a left join ";
        $sql .= config('database.prefix')."goods_category as b on a.cat_id=b.id where a.is_hot=1 and a.is_on_sale=1 order by a.sort";//二级分类下热卖商品       
        $index_hot_goods = db()->query($sql);//首页热卖商品

       // dump($index_hot_goods);

		if($index_hot_goods){
			foreach($index_hot_goods as $val){
				$cat_path = explode('_', $val['parent_id_path']);
				$hot_goods[$cat_path[1]][] = $val;
			}
		}

        $hot_category = db('goods_category')->where("is_hot=1 and level=3 and is_show=1")->select();//热门三级分类
        foreach ($hot_category as $v){
        	$cat_path = explode('_', $v['parent_id_path']);
        	$hot_cate[$cat_path[1]][] = $v;
        }
        
        foreach ($this->cateTrre as $k=>$v){
            if($v['is_hot']==1){
        		$v['hot_goods'] = empty($hot_goods[$k]) ? '' : $hot_goods[$k];
        		$v['hot_cate'] = empty($hot_cate[$k]) ? '' : $hot_cate[$k];
        		$cateList[] = $v;
        	}
        }
        
        $this->assign('cateList',$cateList); 
        $this->assign('cartList','null'); 

        $cart_total_price['anum'] = '0';
        $cart_total_price['total_fee'] = '0';
        $this->assign('cart_total_price',$cart_total_price); 
        return $this->fetch();*/
    }
 
    /**
     *  公告详情页
     */
    public function notice(){
        return $this->fetch();
    }
    
    // 二维码
    public function qr_code(){        
        // 导入Vendor类库包 Library/Vendor/Zend/Server.class.php
        Loader::import('phpqrcode.phpqrcode');
          //import('Vendor.phpqrcode.phpqrcode');
            error_reporting(E_ERROR);            
            $url = urldecode(input('data'));

            //new \QRcode::png($url);
            $img = new \QRcode();
            $img->png($url);        
    }
    
    // 验证码
    public function verify()
    {
        //验证码类型
        $type = input('get.type') ? input('get.type') : '';
        $fontSize = input('get.fontSize') ? input('get.fontSize') : '40';
        $length = input('get.length') ? input('get.length') : '4';
        
        /*$config = array(
            'fontSize' => $fontSize,
            'length' => $length,
            'useCurve' => true,
            'useNoise' => false,
        );
        $Verify = new Verify($config);
        $Verify->entry($type);*/
        $captcha_config = [
            // 验证码字符集合
            'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY', 
            // 验证码字体大小(px)
            'fontSize' => 20, 
            // 是否画混淆曲线
            'useCurve' => true, 
             // 验证码图片高度
            'imageH'   => 40,
            // 验证码图片宽度
            'imageW'   => 81, 
            // 验证码位数
            'length'   => 2, 
            // 验证成功后是否重置        
            'reset'    => true
            ];
        //captcha("", $captcha_config); 
       // $Verify = new \think\captcha\Captcha($captcha_config);

       /* captcha($id, (array)Config::get('captcha'));
        $captcha = new Captcha((array)Config::get('captcha'));*/

        //captcha($id, $captcha_config);
        $captcha = new \think\captcha\Captcha($captcha_config);
        return $captcha->entry();

        //dump($verify);

//captcha_img();
//captcha_img();
        //dump($config);
        //$Verify->entry($type);
        //return \think\Url::build('/captcha' . ($id ? "/{$id}" : ''));       
    }
    
    // 促销活动页面
    public function promoteList()
    {                          
        $Model = db();
        $goodsList = $Model->query("select * from ".config('database.prefix')."goods as g inner join ".config('database.prefix')."flash_sale as f on g.goods_id = f.goods_id   where ".time()." > start_time  and ".time()." < end_time");                        
        $brandList = db('brand')->column("id,name,logo");
        $this->assign('brandList',$brandList);
        $this->assign('goodsList',$goodsList);
        return $this->fetch();
    }
    
    function truncate_tables (){
        $model = db(); // 实例化一个model对象 没有对应任何数据表
        $tables = $model->query("show tables");
        $table = array('tp_admin','tp_config','tp_region','tp_system_module','tp_admin_role','tp_system_menu');
        foreach($tables as $key => $val)
        {                                    
            if(!in_array($val['tables_in_tpshop'], $table))                             
                echo "truncate table ".$val['tables_in_tpshop'].' ; ';
                echo "<br/>";         
        }                
    }

}