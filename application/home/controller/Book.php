<?php
/**
      
 * Date: 2015-09-09
 */
namespace app\home\controller;
use app\wap\model\Book as BookModel;
use Think\Db;

class Book extends Base { 
    /**
     * [combolist 套餐列表]
     * @return [type] [description]
     */
    public function combolist(){
        if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('is_on_sale')) ;             
            if($str){
                $where['is_on_sale'] = input('is_on_sale') ;
            }
              
            // 关键词搜索               
            $goods_name = input('key_word') ? trim(input('key_word')) : '';
            if($goods_name)
            {
                $where['goods_name'] = ['like', "%$goods_name%" ] ;
            }
            $shop_price = input('shop_price') ? trim(input('shop_price')) : '';
            if($shop_price)
            {
                $where['shop_price'] = $shop_price;
            }


            $model = db('book_combo');
           
            $order = input('orderby1')?input('orderby1'):'id';
            $sort = input('orderby2')?input('orderby2'):'desc';
            $page = input('page')?input('page'):1;
            $limit = input('limit')?input('limit'):10;
            $offset = input('offset')?input('offset'):0;
            $order_str = $order." ".$sort;
            $data['total'] = $model->where($where)->order($order_str)->count();
            //halt(db()->getLastSql());
            if($data['total']){
               $data['rows'] = $model->where($where)->order($order_str)->limit($offset, $limit)->select();
               foreach ($data['rows'] as $k => $v) {
                   $data['rows'][$k]['last_update'] = date('Y-m-d H:i:s', $v['last_update']);
                   $data['rows'][$k]['last_update_uid'] = get_admin_name($v['last_update_uid']);

               }
            }
            else{
                $data['rows'] = [];
            }
            //$page = $goodsList->render(); //分布
            return $data;

        }     
     
        
        return $this->fetch();
    }

    /**
     * 添加修改套餐
     */
    public function addEditCombo(){
        if(input('id')){
            $goods = db('book_combo')->where('id', input('id'))->find(); 
            $this->assign('goodsInfo', $goods);
            if($goods['goods_content']){
                $map['goods_id'] = ['in', $goods['goods_content']];
                $combo_goods = db('book_goods')->field('goods_id, goods_name, shop_price')->where($map)->select();
                $this->assign('combo_goods', $combo_goods);
            }
        }
        else{
            $time = time();
            $data = [
                    'uid' => session('uid'),
                    'add_time' => $time,
                    'last_update_uid' => session('uid'),
                    'last_update' => $time
                ];
            $id = db('book_combo')->insertGetId($data);
            $this->redirect(url('addEditCombo', ['id' => $id]));
        }
        //ajax提交
        if (request()->isPost()) {
            $request = input('request.');
            if($request['id']){
                $data = [
                    'goods_content' => $request['goods_content'],
                    'goods_name' => $request['goods_name'],
                    'goods_remark' => $request['goods_remark'],
                    'shop_price' => $request['shop_price'],
                    'original_img' => $request['original_img'],
                    'id' => $request['id'],
                    'last_update_uid' => session('uid'),
                    'last_update' => time()
                ];
                $result = db('book_combo')->where('id', $request['id'])->update($data);
            }
            else{
                $time = time();
                $data = [
                    'goods_content' => $request['goods_content'],
                    'goods_name' => $request['goods_name'],
                    'goods_remark' => $request['goods_remark'],
                    'shop_price' => $request['shop_price'],
                    'original_img' => $request['original_img'],
                    'id' => $request['id'],
                    'uid' => session('uid'),
                    'add_time' => $time,
                    'last_update_uid' => session('uid'),
                    'last_update' => $time
                ];
                $result = db('book_combo')->insert($data);
            }
            if($result){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');

            }
            
        }
        return $this->fetch('_combo');
    }

    /**
     * 修改套餐商品
     */
    public function updatecombocontent(){
        //ajax提交
        if (request()->isPost()) {
            $request = input('request.');
            if($request['id']){
                $data = [
                    'goods_content' => $request['goods_content'],
                    'last_update_uid' => session('uid'),
                    'last_update' => time()
                ];
                $result = db('book_combo')->where('id', $request['id'])->update($data);
            }
            $return = '';
            if($request['return']){
                $map['goods_id'] = ['in', $request['goods_content']];
                $return = db('book_goods')->field('goods_id, goods_name, shop_price')->where($map)->select();
            }
            if($result){
                $this->success('操作成功','', $return);
            }
            else{
                $this->error('操作失败');

            }
            
        }
    }

    /**
     *  搜索商品列表
     */
    public function search_goods(){ 
        $id = input('id');
        $this->assign('id', $id);
        if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('is_on_sale')) ;             
            if($str){
                $where['is_on_sale'] = input('is_on_sale') ;
            }
              
            // 关键词搜索               
            $goods_name = input('key_word') ? trim(input('key_word')) : '';
            if($goods_name)
            {
                $where['goods_name'] = ['like', "%$goods_name%" ] ;
            }
            $shop_price = input('shop_price') ? trim(input('shop_price')) : '';
            if($shop_price)
            {
                $where['shop_price'] = $shop_price;
            }

            $model = db('book_combo_goods');
           
            $order = input('orderby1')?input('orderby1'):'goods_id';
            $sort = input('orderby2')?input('orderby2'):'desc';
            $page = input('page')?input('page'):1;
            $limit = input('limit')?input('limit'):10;
            $offset = input('offset')?input('offset'):0;
            $order_str = $order." ".$sort;
            $data['total'] = $model->where($where)->order($order_str)->count();
            //halt(db()->getLastSql());
            if($data['total']){
               $data['rows'] = $model->where($where)->order($order_str)->limit($offset, $limit)->select();
               foreach ($data['rows'] as $k => $v) {
                   $data['rows'][$k]['last_update'] = date('Y-m-d H:i:s', $v['last_update']);
                   $data['rows'][$k]['last_update_uid'] = get_admin_name($v['last_update_uid']);

               }
            }
            else{
                $data['rows'] = [];
            }
            //$page = $goodsList->render(); //分布
            return $data;

        }     
     
        
        return $this->fetch();                                           
    }

    /**
     *  商品列表
     */
    public function goodsList(){ 
        if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('is_on_sale')) ;             
            if($str){
                $where['is_on_sale'] = input('is_on_sale') ;
            }
              
            // 关键词搜索               
            $goods_name = input('key_word') ? trim(input('key_word')) : '';
            if($goods_name)
            {
                $where['goods_name'] = ['like', "%$goods_name%" ] ;
            }
            $shop_price = input('shop_price') ? trim(input('shop_price')) : '';
            if($shop_price)
            {
                $where['shop_price'] = $shop_price;
            }

            $model = db('book_goods');
           
            $order = input('orderby1')?input('orderby1'):'goods_id';
            $sort = input('orderby2')?input('orderby2'):'desc';
            $page = input('page')?input('page'):1;
            $limit = input('limit')?input('limit'):10;
            $offset = input('offset')?input('offset'):0;
            $order_str = $order." ".$sort;
            $data['total'] = $model->where($where)->order($order_str)->count();
            //halt(db()->getLastSql());
            if($data['total']){
               $data['rows'] = $model->where($where)->order($order_str)->limit($offset, $limit)->select();
               foreach ($data['rows'] as $k => $v) {
                   $data['rows'][$k]['last_update'] = date('Y-m-d H:i:s', $v['last_update']);
                   $data['rows'][$k]['last_update_uid'] = get_admin_name($v['last_update_uid']);

               }
            }
            else{
                $data['rows'] = [];
            }
            //$page = $goodsList->render(); //分布
            return $data;

        }     
     
        
        return $this->fetch();                                           
    }

    /**
     *  套餐商品列表
     */
    public function comboGoodsList(){ 
    	if(request()->isAjax()){

    		$where = []; // 搜索条件  
            $str = strlen(input('is_on_sale')) ;             
	        if($str){
	            $where['is_on_sale'] = input('is_on_sale') ;
	        }
              
            // 关键词搜索               
            $goods_name = input('key_word') ? trim(input('key_word')) : '';
            if($goods_name)
            {
                $where['goods_name'] = ['like', "%$goods_name%" ] ;
            }
            $shop_price = input('shop_price') ? trim(input('shop_price')) : '';
            if($shop_price)
            {
                $where['shop_price'] = $shop_price;
            }

	        $model = db('book_combo_goods');
	       
	        $order = input('orderby1')?input('orderby1'):'goods_id';
	        $sort = input('orderby2')?input('orderby2'):'desc';
	        $page = input('page')?input('page'):1;
	        $limit = input('limit')?input('limit'):10;
	        $offset = input('offset')?input('offset'):0;
	        $order_str = $order." ".$sort;
	        $data['total'] = $model->where($where)->order($order_str)->count();
            //halt(db()->getLastSql());
            if($data['total']){
	           $data['rows'] = $model->where($where)->order($order_str)->limit($offset, $limit)->select();
               foreach ($data['rows'] as $k => $v) {
                   $data['rows'][$k]['last_update'] = date('Y-m-d H:i:s', $v['last_update']);
                   $data['rows'][$k]['last_update_uid'] = get_admin_name($v['last_update_uid']);

               }
            }
            else{
                $data['rows'] = [];
            }
	        //$page = $goodsList->render(); //分布
	        return $data;

    	}     
     
        
        return $this->fetch();                                           
    }

    /**
     * 添加修改套餐商品
     */
    public function addEditComboGoods(){
        if(input('goods_id')){
            $goods = db('book_combo_goods')->where('goods_id', input('goods_id'))->find(); 
            $this->assign('goodsInfo', $goods);
        }
        //ajax提交
        if (request()->isPost()) {
            $request = input('request.');
            if($request['goods_id']){
                $data = [
                    'goods_name' => $request['goods_name'],
                    'goods_remark' => $request['goods_remark'],
                    'shop_price' => $request['shop_price'],
                    'original_img' => $request['original_img'],
                    'goods_id' => $request['goods_id'],
                    'last_update_uid' => session('uid'),
                    'last_update' => time()
                ];
                $result = db('book_combo_goods')->where('goods_id', $request['goods_id'])->update($data);
            }
            else{
                $time = time();
                $data = [
                    'goods_name' => $request['goods_name'],
                    'goods_remark' => $request['goods_remark'],
                    'shop_price' => $request['shop_price'],
                    'original_img' => $request['original_img'],
                    'goods_id' => $request['goods_id'],
                    'uid' => session('uid'),
                    'add_time' => $time,
                    'last_update_uid' => session('uid'),
                    'last_update' => $time
                ];
                $result = db('book_combo_goods')->insert($data);
            }
            if($result){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');

            }
            
        }
        return $this->fetch('_combogoods');
    }

    /**
     * 添加修改单品
     */
    public function addEditGoods(){
        if(input('goods_id')){
            $goods = db('book_goods')->where('goods_id', input('goods_id'))->find(); 
            $this->assign('goodsInfo', $goods);
        }
        //ajax提交
        if (request()->isPost()) {
            $request = input('request.');
            if($request['goods_id']){
                $data = [
                    'goods_name' => $request['goods_name'],
                    'goods_remark' => $request['goods_remark'],
                    'shop_price' => $request['shop_price'],
                    'original_img' => $request['original_img'],
                    'goods_id' => $request['goods_id'],
                    'last_update_uid' => session('uid'),
                    'last_update' => time()
                ];
                $result = db('book_goods')->where('goods_id', $request['goods_id'])->update($data);
            }
            else{
                $time = time();
                $data = [
                    'goods_name' => $request['goods_name'],
                    'goods_remark' => $request['goods_remark'],
                    'shop_price' => $request['shop_price'],
                    'original_img' => $request['original_img'],
                    'goods_id' => $request['goods_id'],
                    'uid' => session('uid'),
                    'add_time' => $time,
                    'last_update_uid' => session('uid'),
                    'last_update' => $time
                ];
                $result = db('book_goods')->insert($data);
            }
            if($result){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');

            }
            
        }
        return $this->fetch('_goods');
    }

    //商品参数套餐组合编辑新增
    public function saveSizeFrom($type,$goods_id,$data){
        if ($data){
            foreach ($data as $k => $v){
                if (empty($v['name'])){
                    continue;
                }
                $v['pid'] = $goods_id;
                if ($v['id']){//编辑
                    db($type)->update($v);
                }else{
                    db($type)->insert($v);
                }
            }
        }
    }
   
    
    /**
     * 添加修改编辑  商品属性类型
     */
    public  function addEditGoodsType(){        
            $id = input('id/d') ? input('id/d') : 0;            
            $model = db("GoodsType");           
            if(request()->isPost())
            {
                    $data['name'] = input('name/s');
                    if($id)
                        $model->where("id=".$id)->update($data);
                    else
                        $model->insert($data);
                    
                    $this->success("操作成功!!!",url('home/Goods/goodsTypeList'));               
                    exit;
            }           
           $goodsType = $model->find($id);
           $this->assign('goodsType',$goodsType);
           return $this->fetch('_goodsType');           
    }
    
    /**
     * 商品属性列表
     */
    public function goodsAttributeList(){       
        $goodsTypeList = db("GoodsType")->select();
        $this->assign('goodsTypeList',$goodsTypeList);
        return $this->fetch();
    }   
    
    /**
     *  商品属性列表
     */
    public function ajaxGoodsAttributeList(){            
        //ob_start('ob_gzhandler'); // 页面压缩输出
        $_GET['type_id'] = '';
        $where = ' 1 = 1 '; // 搜索条件                        
        input('type_id')   && $where = "$where and type_id = ".input('type_id') ;                
        // 关键词搜索               
        $model = db('GoodsAttribute');
        /*$count = $model->where($where)->count();
        $Page       = new AjaxPage($count,13);
        $show = $Page->show();
        $goodsAttributeList = $model->where($where)->order('`order` desc,attr_id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();*/

        $goodsAttributeList = $model->where($where)->order('`order` desc,attr_id DESC')->paginate(13);
        $page = $goodsAttributeList->render();

        $goodsTypeList = db("GoodsType")->column('id,name');

        $attr_input_type = array(0=>'手工录入',1=>' 从列表中选择',2=>' 多行文本框');
        $this->assign('attr_input_type',$attr_input_type);
        $this->assign('goodsTypeList',$goodsTypeList);        
        $this->assign('goodsAttributeList',$goodsAttributeList);
        $this->assign('page',$page);// 赋值分页输出
        return $this->fetch();         
    }   
    
    /**
     * 添加修改编辑  商品属性
     */
    public  function addEditGoodsAttribute(){
                        
            $model = db("GoodsAttribute");                      
            $type = input('attr_id') > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新         
            $_POST['attr_values'] = str_replace('_', '', input('attr_values')); // 替换特殊字符
            $_POST['attr_values'] = str_replace('@', '', input('attr_values')); // 替换特殊字符            
            $_POST['attr_values'] = trim($_POST['attr_values']);

            if((input('is_ajax') == 1) && request()->isPost())//ajax提交验证
            {                
                config('TOKEN_ON',false);
                if(!$model->create(NULL,$type))// 根据表单提交的POST数据创建数据对象                 
                {
                    //  编辑
                    $return_arr = array(
                        'status' => -1,
                        'msg'   => '提交不成功!',
                        'data'  => $model->getError(),
                    );
                    $this->ajaxReturn(json_encode($return_arr));
                }else {                   
                   // config('TOKEN_ON',true); //  form表单提交
                    if ($type == 2)
                    {
                        $model->save(); // 写入数据到数据库                        
                    }
                    else
                    {
                        $insert_id = $model->add(); // 写入数据到数据库                        
                    }
                    $return_arr = array(
                        'status' => 1,
                        'msg'   => '操作成功',                        
                        'data'  => array('url'=>url('home/Goods/goodsAttributeList')),
                    );
                    $this->ajaxReturn(json_encode($return_arr));
                }  
            }                
           // 点击过来编辑时                 
           $attr_id['attr_id'] = input('attr_id') ? input('attr_id') : 0;       
           $goodsTypeList = db("GoodsType")->select();           
           $goodsAttribute = $model->where($attr_id)->find(); 
                     //dump($goodsAttribute);
           $this->assign('goodsTypeList',$goodsTypeList);                   
           $this->assign('goodsAttribute',$goodsAttribute);
           return $this->fetch('_goodsAttribute');           
    }  
    
    /**
     * 更改指定表的指定字段
     */
    public function updateField(){
        $primary = array(
                'goods' => 'goods_id',
                'goods_category' => 'id',
                'brand' => 'id',            
                'goods_attribute' => 'attr_id',
        		'ad' =>'ad_id',            
        );
		
		$post = input('post.');
        $model = db($post['table']);
        $data[$post['field']] = $post['value']; 

        $map[$primary[$post['table']]] = $post['id'];     
        $r = $model->where($map)->update($data); 

        $return_arr = array(
            'status' => 1,
            'msg'   => '操作成功',                        
            'data'  => array('url'=>url('home/Goods/goodsAttributeList')),
        );
        return $return_arr;
    }
    /**
     * 动态获取商品属性输入框 根据不同的数据返回不同的输入框类型
     */
    public function ajaxGetAttrInput(){
        $str = '';
        $GoodsLogic = model('GoodsLogic', 'logic');
        //dump($_REQUEST['goods_id']."_____".$_REQUEST['type_id']);
        $str = $GoodsLogic->getAttrInput(input('goods_id/d'),input('type_id/d'));
        exit($str);
    }
        
    /**
     * 删除单品
     */
    public function delGoods()
    {
		$goods_id = input('param.id');
        $ids = explode(',', $goods_id);
        $ids = array_filter($ids);

        foreach ($ids as $k => $goods_id) {
    
            $error = '';
            
            // 判断此商品是否有订单
            $c1 = db('book_order_goods')->where("goods_id = $goods_id")->count('1');
            $c1 && $error .= $goods_id.'此商品有订单,不得删除! <br/>';
          
            
            if($error)
            {
                $return_arr = array('status' => -1,'msg' =>$error,'data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
                return $return_arr;           
            }
            // 删除此商品        
            db("book_goods")->where('goods_id ='.$goods_id)->delete();  //商品表
            db("book_cart")->where('goods_id ='.$goods_id)->delete();  // 购物车
                     
                     
        }

        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
        return $return_arr;
    }

    /**
     * 删除套餐
     */
    public function delCombo()
    {
        $id = input('param.id');
        $ids = explode(',', $id);
        $ids = array_filter($ids);

        foreach ($ids as $k => $id) {
    
            $error = '';
            
            // 判断此商品是否有订单
            $c1 = db('book_order_combo_goods')->where("combo_id = $id")->count('1');
            $c1 && $error .= $id.'此套餐有订单,不得删除! <br/>';
          
            
            if($error)
            {
                $return_arr = array('status' => -1,'msg' =>$error,'data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
                return $return_arr;           
            }
            // 删除此套餐        
            db("book_combo")->where('id ='.$id)->delete();  //套餐表
            db("book_cart")->where('combo_id ='.$id)->delete();  // 购物车
                     
                     
        }

        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
        return $return_arr;
    }

    /**
     * 删除套餐商品
     */
    public function delComboGoods()
    {
        $goods_id = input('param.id');
        $ids = explode(',', $goods_id);
        $ids = array_filter($ids);

        foreach ($ids as $k => $goods_id) {
    
            $error = '';
            
            // 判断此商品是否有订单
            $c1 = db('book_order_combo_goods')->where("goods_id = $goods_id")->count('1');
            $c1 && $error .= $goods_id.'此商品有订单,不得删除! <br/>';
          
            
            if($error)
            {
                $return_arr = array('status' => -1,'msg' =>$error,'data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
                return $return_arr;           
            }
            // 删除此商品        
            db("book_combo_goods")->where('goods_id ='.$goods_id)->delete();  //商品表
        }

        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
        return $return_arr;
    }

    
    
    
    /**
     * 初始化编辑器链接     
     * 本编辑器参考 地址 http://fex.baidu.com/ueditor/
     */
    private function initEditor()
    {
        $this->assign("URL_upload", url('home/Ueditor/imageUp',array('savepath'=>'goods'))); // 图片上传目录
        $this->assign("URL_imageUp", url('home/Ueditor/imageUp',array('savepath'=>'article'))); //  不知道啥图片
        $this->assign("URL_fileUp", url('home/Ueditor/fileUp',array('savepath'=>'article'))); // 文件上传s
        $this->assign("URL_scrawlUp", url('home/Ueditor/scrawlUp',array('savepath'=>'article')));  //  图片流
        $this->assign("URL_getRemoteImage", url('home/Ueditor/getRemoteImage',array('savepath'=>'article'))); // 远程图片管理
        $this->assign("URL_imageManager", url('home/Ueditor/imageManager',array('savepath'=>'article'))); // 图片管理        
        $this->assign("URL_getMovie", url('home/Ueditor/getMovie',array('savepath'=>'article'))); // 视频上传
        $this->assign("URL_home", "");
    }    
    
    
    
    /**
     * [orderlist 订单列表]
     * @return [type] [description]
     */
    public function orderlist(){
        if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('mobile')) ;             
            if($str){
                $where['mobile'] = input('mobile') ;
            }
              
            // 关键词搜索               
            $name = input('key_word') ? trim(input('key_word')) : '';
            if($name)
            {
                $where['name'] = ['like', "%$name%" ] ;
            }
            $table = input('table') ? trim(input('table')) : '';
            if($table)
            {
                $where['table'] = ['like', "%$table%" ] ;
            }
            //订单状态
            $str = strlen(input('order_status')) ; 
            if($str)
            {
                $where['order_status'] = ['eq', input('order_status/d') ] ;
            }
            $date = input('date');
            $hours = input('hours');
            if($date && !$hours){
                $start_time = strtotime($date);
                $end_time = strtotime('+ 1 day', strtotime($date));
                //dump(date('Y-m-d H:i:s',$end_time));
                $where['at_time_unix'] = ['between', [$start_time, $end_time]];
            }
            if($date && $hours){
                $at_time_unix = strtotime($date . " " . $hours . ":00:00");
                $where['at_time_unix'] = $at_time_unix;
            }
            if(!$date && $hours){
                $at_time_unix = strtotime(date('Y-m-d') . " " . $hours . ":00:00");
                $where['at_time_unix'] = $at_time_unix;
            }
            //dump($where);


            $model = db('book_order');
           
            $order = input('orderby1')?input('orderby1'):'order_id';
            $sort = input('orderby2')?input('orderby2'):'desc';
            $page = input('page')?input('page'):1;
            $limit = input('limit')?input('limit'):10;
            $offset = input('offset')?input('offset'):0;
            $order_str = $order." ".$sort;


            $data['total'] = $model->where($where)->order($order_str)->count();
            //halt(db()->getLastSql());
            if($data['total']){
               $data['rows'] = $model->where($where)->order($order_str)->limit($offset, $limit)->select();
               foreach ($data['rows'] as $k => $v) {
                   $data['rows'][$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                   $data['rows'][$k]['user_id'] = find_username($v['user_id']);
                   $data['rows'][$k]['pay_status'] = config('book_order_pay_status')[$v['pay_status']];
                   $data['rows'][$k]['order_status'] = config('book_order_status')[$v['order_status']];

               }
            }
            else{
                $data['rows'] = [];
            }
            return $data;
        }  

        $seat = config('seat');
        $order_status = config('book_order_status');
        $this->assign([
            'seat' => $seat,
            'order_status' => $order_status,
            ]);
        return $this->fetch();
    }

    //订单详情
    public function editOrder(){
        $act = input('act');
        if($act == 'edit'){
            $template = 'detail';
        }
        if($act == 'view'){
            $template = 'detail';
        }
        if($act == 'success'){
            $template = 'detail';
        }
        if($act == 'refund'){
            $template = 'detail';
        }
        if($act == 'print'){
            $template = 'print';
        }
        $order_id = input('order_id/d');
        if(!$order_id){
            $this->error('请选择订单');
        }
        $map['order_id'] = $order_id;
        $model = new BookModel();
        $order = $model->orderDetail($map);
        if($order['pay_status'] ==1){
            $order['pay_money'] = $order['order_amount'];
            $order['pay_money_spare'] = 0;
        }
        elseif($order['pay_status'] ==2){
            $order['pay_money'] = $order['amount20'];
            $order['pay_money_spare'] = $order['amount80'];
        }
        else{
            $order['pay_money'] = 0;
            $order['pay_money_spare'] = $order['order_amount'];
        }

        //取操作日志
        $action_log = db('book_order_action')->where('order_id', $order_id)->order('action_id desc')->select();
        foreach ($action_log as $k => $v) {
            $action_log[$k]['action_user'] = get_admin_name($v['action_user']);
        }

        $order_status = config('book_order_status');
        $pay_status = config('book_pay_status');

        $this->assign([
            'order' => $order,
            'order_status' => $order_status,
            'pay_status' => $pay_status,
            'act' => $act,
            'action_log' => $action_log,
            ]);

        return $this->fetch($template);
    }

    //订餐桌状态
    public function tableStatus(){
        $at_time = input('at_time/s');
        if(!$at_time){
            $hours = date('H');

            if($hours < 8){
                $at_time = date('Y年m月d日').' 上午8点';
            }
            if(($hours >= 8) && ($hours< 14)){
                $at_time = date('Y年m月d日').' 下午2点';
            }
            if(($hours < 20) && ($hours>= 14)){
                $at_time = date('Y年m月d日').' 晚上8点';
            }
        }

        $model = new BookModel();
        $booked = $model->booked($at_time);

        $this->assign([
            'booked'=> $booked,
            'at_time'=> $at_time,
            ]);

        return view();
    }

    /**
     * [orderActionLog 操作日志]
     * @param  [type] $order_id     [订单ID]
     * @param  string $order_status [订单状态]
     * @param  string $pay_status   [订单支付状态]
     * @param  string $action_note  [操作留言]
     * @return [type]               [description]
     */
    public function orderActionLog($order_id , $order_status='', $pay_status='', $action_note=''){
        if($order_status){
             $data['order_status'] = $order_status;
        }
        if($pay_status){
             $data['pay_status'] = $pay_status;
        }
        if($action_note){
             $data['action_note'] = $action_note;
        }
        $data['order_id'] = $order_id;
        $data['log_time'] = time();
        $data['action_user'] = session('uid');

        db('book_order_action')->insert($data);    

    }


    public function orderAction(){
        $act = input('act');
        $order_id = input('order_id/d');
        if($act == 'edit'){
            $template = 'detail';
        }
        if($act == 'view'){
            $template = 'detail';
        }
        if($act == 'success'){
            $order_status = input('order_status');
            if(input('note/s')){
                $action_note = input('note/s');
            }
            $data = [
                'order_status' => $order_status,
                ];
            if($order_status == 3){
                $pay_status = db('book_order')->where('order_id', $order_id)->value('pay_status');
                if($pay_status){
                    $this->error('用户已经支付，不能设置为无效');
                }
            }
            $r = db('book_order')->where('order_id', $order_id)->update($data);
            //dump(db()->getLastSql());
            //记录日志
            $this->orderActionLog($order_id, $order_status, '', "$action_note");
            if($r !== false){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');
            }
        }
        if($act == 'refund'){
            $order_status = 4;
            $action_note = input('note/s');
            $data = [
                'order_id' => $order_id,
                'order_status' => $order_status,
                ];
            $r = db('book_order')->where('order_id', $order_id)->update($data);

            $this->orderActionLog($order_id, $order_status, '', "$action_note");
            if($r){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');
            }

        }
        
    }
    
    
}