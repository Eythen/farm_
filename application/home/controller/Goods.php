<?php
/**

 * Date: 2015-09-09
 */
namespace app\home\controller;
use app\home\logic\GoodsLogic;
use app\home\model\Goods as GoodsModel;
/*use Think\AjaxPage;
use Think\Page;*/
use Think\Db;

class Goods extends Base {

    /**
     *  商品分类列表
     */
    public function categoryList(){
        $GoodsLogic = new GoodsLogic();
        //$cat_list['have_son']  = '';
        $cat_list = $GoodsLogic->goods_cat_list();
        foreach ($cat_list as $k => $v) {
            if(!isset($v['have_son'])){
                $cat_list[$k]['have_son'] = '';
            }
        }
        //dump($cat_list);
        $this->assign('cat_list',$cat_list);
        return $this->fetch();
    }

    /**
     * 添加修改商品分类
     * 手动拷贝分类正则 ([\u4e00-\u9fa5/\w]+)  ('393','$1'),
     * select * from tp_goods_category where id = 393
        select * from tp_goods_category where parent_id = 393
        update tp_goods_category  set parent_id_path = concat_ws('_','0_76_393',id),`level` = 3 where parent_id = 393
        insert into `tp_goods_category` (`parent_id`,`name`) values
        ('393','时尚饰品'),
     */
    public function addEditCategory(){


            $GoodsLogic = new GoodsLogic();
            if(request()->isGet())
            {
                $goods_category_info = db('GoodsCategory')->where('id='.input('id',0))->find();
                $level_cat = $GoodsLogic->find_parent_cat($goods_category_info['id']); // 获取分类默认选中的下拉框

                $cat_list = db('goods_category')->where("parent_id = 0")->select(); // 已经改成联动菜单
                $this->assign('level_cat',$level_cat);
                $this->assign('cat_list',$cat_list);
                $this->assign('goods_category_info',$goods_category_info);
                return $this->fetch('_category');
                exit;
            }

            $GoodsCategory = db('GoodsCategory'); //

            $type = $_POST['id'] > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新

            //dump($type);
            //ajax提交验证
            if(input('is_ajax') == 1)
            {
                config('TOKEN_ON',false);

                $post = input('post.');

                $data = array(
                    'name' => $post['name'],
                    'mobile_name' => $post['mobile_name'],
                    'parent_id' => $post['parent_id_1'],
                    //'parent_id_2' => $post['parent_id_2'],
                    'is_show' => $post['is_show'],
                    'cat_group' => $post['cat_group'],
                    'image' => $post['image'],
                    'sort_order' => $post['sort_order'],
                    'commission_rate' => $post['commission_rate'],
                    'id' =>  $post['id']
                );


                if(!empty($_POST['id']))// 根据表单提交的POST数据创建数据对象
                {
                    //  编辑
                    $save = $GoodsCategory->where('id = '.$_POST['id'])->update($data); // 写入数据到数据库
                    $GoodsLogic->refresh_cat($_POST['id']);
                    if ($save === false){
                        $return_arr = array(
                            'status' => -1,
                            'msg'   => '操作失败!',
                            'data'  => $GoodsCategory->getError(),
                        );
                    }else{
                        $return_arr = array(
                            'status' => 1,
                            'msg'   => '操作成功',
                            'data'  => array('url'=>url('home/Goods/categoryList')),
                        );
                    }
                    return json_encode($return_arr);
                }else {
                    //  form表单提交
                    config('TOKEN_ON',true);

                    $data['parent_id'] = $_POST['parent_id_1'];
                    $_POST['parent_id_2'] && ($data['parent_id'] = $_POST['parent_id_2']);

                    if($data['id'] > 0 && $data['parent_id'] == $data['id'])
                    {
                        //  编辑
                        $return_arr = array(
                            'status' => -1,
                            'msg'   => '上级分类不能为自己',
                            'data'  => '',
                        );
                        $this->ajaxReturn(json_encode($return_arr));
                    }
                    if($data['commission_rate'] > 100)
                    {
                        //  编辑
                        $return_arr = array(
                            'status' => -1,
                            'msg'   => '分佣比例不得超过100%',
                            'data'  => '',
                        );
                        $this->ajaxReturn(json_encode($return_arr));
                    }

                    $insert_id = $GoodsCategory->insertGetId($data); // 写入数据到数据库
                    $GoodsLogic->refresh_cat($insert_id);

                    $return_arr = array(
                        'status' => 1,
                        'msg'   => '操作成功',
                        'data'  => array('url'=>url('home/Goods/categoryList')),
                    );

                    return json_encode($return_arr);

                }
            }

    }

    /**
     * 获取商品分类 的帅选规格 复选框
     */
    public function ajaxGetSpecList(){
        $GoodsLogic = model('GoodsLogic', 'logic');
        $category_id = input('category_id') ? input('category_id') : 0;
        $filter_spec = db('GoodsCategory')->where("id = ".$category_id)->column('filter_spec');
        $filter_spec_arr = explode(',',$filter_spec);
        //dump($filter_spec_arr);
        $str = $GoodsLogic->GetSpecCheckboxList(input('type_id/s'),$filter_spec_arr);
        $str = $str ? $str : '没有可帅选的商品规格';
        exit($str);
    }

    /**
     * 获取商品分类 的帅选属性 复选框
     */
    public function ajaxGetAttrList(){
        $GoodsLogic = new GoodsLogic();
        $_REQUEST['category_id'] = $_REQUEST['category_id'] ? $_REQUEST['category_id'] : 0;
        $filter_attr = db('GoodsCategory')->where("id = ".$_REQUEST['category_id'])->column('filter_attr');
        $filter_attr_arr = explode(',',$filter_attr);
        $str = $GoodsLogic->GetAttrCheckboxList($_REQUEST['type_id'],$filter_attr_arr);
        $str = $str ? $str : '没有可帅选的商品属性';
        exit($str);
    }

    /**
     * 删除分类
     */
    public function delGoodsCategory(){
        // 判断子分类
        $GoodsCategory = db("GoodsCategory");
        $count = $GoodsCategory->where("parent_id = {$_GET['id']}")->count("id");
        $count > 0 && $this->error('该分类下还有分类不得删除!',url('home/Goods/categoryList'));
        // 判断是否存在商品
        $goods_count = db('Goods')->where("cat_id = {$_GET['id']}")->count('1');
        $goods_count > 0 && $this->error('该分类下有商品不得删除!',url('home/Goods/categoryList'));
        // 删除分类
        $GoodsCategory->where("id = {$_GET['id']}")->delete();
        $this->success("操作成功!!!",url('home/Goods/categoryList'));
    }


    /**
     *  商品列表
     */
    public function goodsList(){
    	if(request()->isAjax()){

    		$where = ' 1 = 1 '; // 搜索条件
	        if(input('intro')){
	            $where = "$where and ".input('intro')." = 1" ;
	        }
	        if(input('brand_id')){
	            $where = "$where and brand_id = ".input('brand_id') ;
	        }
	        if(!empty(input('is_on_sale'))){
	            $where = "$where and is_on_sale = ".input('is_on_sale') ;
	        }
            // 关键词搜索
            $key_word = input('key_word') ? trim(input('key_word')) : '';
            if($key_word)
            {
                $where = "$where and (goods_name like '%$key_word%' )" ;
                //$where = "$where and (goods_name like '%$key_word%' or goods_sn like '%$key_word%')" ;
            }
            $goods_sn = input('goods_sn') ? trim(input('goods_sn')) : '';
            if($goods_sn)
            {
                $where = "$where and (goods_sn like '%$goods_sn%' )" ;
            }

	        $cat_id = input('cat_id');
	        if($cat_id > 0)
	        {
	            $grandson_ids = getCatGrandson($cat_id);
	            $where .= " and cat_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
	        }


	        $model = db('Goods');

	        $order = input('orderby1')?input('orderby1'):'goods_id';
	        $sort = input('orderby2')?input('orderby2'):'desc';
	        $page = input('page')?input('page'):1;
	        $limit = input('limit')?input('limit'):10;
	        $offset = input('offset')?input('offset'):0;
	        $order_str = $order." ".$sort;
	        $data['total'] = $model->where($where)->order($order_str)->limit($offset, $limit)->count();

	        $data['rows'] = $model->where($where)->order($order_str)->limit($offset, $limit)->select();
	        //$page = $goodsList->render(); //分布
	        return $data;

    	}
        $GoodsLogic = new GoodsLogic();
        $brandList = $GoodsLogic->getSortBrands();
        $categoryList = $GoodsLogic->getSortCategory();
        $this->assign('categoryList',$categoryList);
        $this->assign('brandList',$brandList);
        return $this->fetch();
    }

    /**
     *  商品列表
     */
    public function ajaxGoodsList(){

        $where = ' 1 = 1 '; // 搜索条件
        if(input('intro')){
            $where = "$where and ".input('intro')." = 1" ;
        }
        if(input('brand_id')){
            $where = "$where and brand_id = ".input('brand_id') ;
        }
        if(!empty(input('is_on_sale'))){
            $where = "$where and is_on_sale = ".input('is_on_sale') ;
        }
        $cat_id = input('cat_id');
        // 关键词搜索
        $key_word = input('key_word') ? trim(input('key_word')) : '';
        if($key_word)
        {
            $where = "$where and (goods_name like '%$key_word%' or goods_sn like '%$key_word%')" ;
        }

        if($cat_id > 0)
        {
            $grandson_ids = getCatGrandson($cat_id);
            $where .= " and cat_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
        }


        $model = db('Goods');

        $order = input('orderby1')?input('orderby1'):'goods_id';
        $sort = input('orderby2')?input('orderby2'):'desc';
        $page = input('page')?input('page'):1;
        $order_str = $order." ".$sort;

        $goodsList = $model->where($where)->order($order_str)->paginate(10);
        $page = $goodsList->render(); //分布

        $catList = db('goods_category')->select();
        $catList = convert_arr_key($catList, 'id');
        $this->assign('catList',$catList);
        $this->assign('goodsList',$goodsList);
        $this->assign('page',$page);// 赋值分页输出
        return $this->fetch();
    }


    /**
     * 添加修改商品
     */
    public function addEditGoods()
    {
        $GoodsLogic = model('GoodsLogic','logic');


        $type = input('goods_id') > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新

        //ajax提交验证
        if (request()->isPost()) {
            config('TOKEN_ON', false);
            $request = input('request.');

            if($type==2){
                $Goods = GoodsModel::get(input('goods_id/d')); //
            }
            else{
                $Goods = new GoodsModel();
            }
            if (!$Goods)// 根据表单提交的POST数据创建数据对象
            {
                //  编辑
                $return_arr = array(
                    'status' => -1,
                    'msg' => $error_msg[0],
                    //'data' => $error,
                );
                return $return_arr;
            } else {
                //  form表单提交
                // config('TOKEN_ON',true);
                $Goods->on_time = time(); // 上架时间
                //$Goods->cat_id = $_POST['cat_id_1'];
                foreach ($request as $k => $v) {
                    $Goods->$k = $v;
                }
                $_POST['cat_id_2'] && ($Goods->cat_id = $_POST['cat_id_2']);
                $_POST['cat_id_3'] && ($Goods->cat_id = $_POST['cat_id_3']);

                $_POST['extend_cat_id_2'] && ($Goods->extend_cat_id = $_POST['extend_cat_id_2']);
                $_POST['extend_cat_id_3'] && ($Goods->extend_cat_id = $_POST['extend_cat_id_3']);

                if ($type == 2) {

                    $goods_id = $_POST['goods_id'];

                    $Goods->allowField(true)->isUpdate(true)->save(); // 写入数据到数据库
                    $Goods->afterSave($goods_id);
                } else {
                    $Goods->allowField(true)->isUpdate(false)->save(); // 写入数据到数据库
                    $goods_id = $insert_id = $Goods->goods_id; // 写入数据到数据库

                    $Goods->afterSave($goods_id);
                }

//                $GoodsLogic->saveGoodsAttr($goods_id, $_POST['goods_type']); // 处理商品 属性

                //处理商品参数和套餐组合
                $this->saveSizeFrom('goods_size',$goods_id,$_POST['size']);
                $this->saveSizeFrom('goods_entry',$goods_id,$_POST['entry']);

                $return_arr = array(
                    'status' => 1,
                    'msg' => '操作成功',
                    'data' => array('url' => url('home/Goods/goodsList')),
                );
                return $return_arr;
            }
        }

        $goodsInfo = db('Goods')->where('goods_id=' . input('goods_id', 0))->find();
        //$cat_list = $GoodsLogic->goods_cat_list(); // 已经改成联动菜单
        $level_cat = $GoodsLogic->find_parent_cat($goodsInfo['cat_id']); // 获取分类默认选中的下拉框

        $level_cat2 = $GoodsLogic->find_parent_cat($goodsInfo['extend_cat_id']); // 获取分类默认选中的下拉框
        if(empty($level_cat2['1'])){
            $level_cat2['1'] = '';
        }
        if(empty($level_cat2['2'])){
            $level_cat2['2'] = '';
        }
        if(empty($level_cat2['3'])){
            $level_cat2['3'] = '';
        }
        if(empty($level_cat['3'])){
            $level_cat['3'] = '';
        }

        $cat_list = db('goods_category')->where("parent_id = 0")->select(); // 已经改成联动菜单
        $brandList = $GoodsLogic->getSortBrands();
        $goodsType = db("GoodsType")->select();
        $suppliersList = db("suppliers")->select();
        $this->assign('suppliersList',$suppliersList);
        $this->assign('level_cat', $level_cat);
        $this->assign('level_cat2', $level_cat2);
        $this->assign('cat_list', $cat_list);
        $this->assign('brandList', $brandList);
        $this->assign('goodsType', $goodsType);
        $this->assign('goodsInfo', $goodsInfo);  // 商品详情
        $goodsImages = db("GoodsImages")->where('goods_id =' . input('goods_id', 0))->select();
        $this->assign('goodsImages', $goodsImages);  // 商品相册
        $this->initEditor(); // 编辑器

        $size_list = db('goods_size')->where('pid','=',input('goods_id', 0))->select();
        $size_list_count = db('goods_size')->where('pid','=',input('goods_id', 0))->count();
        $this->assign('size_list', $size_list);
        $this->assign('size_list_count', $size_list_count);

        $size_list = db('goods_entry')->where('pid','=',input('goods_id', 0))->select();
        $size_list_count = db('goods_entry')->where('pid','=',input('goods_id', 0))->count();
        $this->assign('entry_list', $size_list);
        $this->assign('entry_list_count', $size_list_count);

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

    //删除商品的参数或者套餐组合
    public function delSizeFrom(){
        $data = array();
        $id = input('id');
        $type = input('type');
        $res = db($type)->delete($id);
        if ($res){
            $data['code'] = 1;
            $data['msg'] = 'ok';
        }else{
            $data['code'] = 0;
            $data['msg'] = '删除失败！';
        }
        return $data;
    }

    /**
     * 商品类型  用于设置商品的属性
     */
    public function goodsTypeList(){
        $model = db("GoodsType");
        $goodsTypeList = $model->order("id desc")->paginate(100);
        $show = $goodsTypeList->render();
        $this->assign('show',$show);
        $this->assign('goodsTypeList',$goodsTypeList);
        return $this->fetch('goodsTypeList');
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
     * 删除商品
     */
    public function delGoods()
    {
		$goods_id = input('param.id');
        $ids = explode(',', $goods_id);
        $ids = array_filter($ids);

        foreach ($ids as $k => $goods_id) {

            $error = '';

            // 判断此商品是否有订单
            $c1 = db('OrderGoods')->where("goods_id = $goods_id")->count('1');
            $c1 && $error .= $goods_id.'此商品有订单,不得删除! <br/>';


             // 商品团购
            $c1 = db('group_buy')->where("goods_id = $goods_id")->count('1');
            $c1 && $error .= $goods_id.'此商品有团购,不得删除! <br/>';

             // 商品退货记录
            $c1 = db('return_goods')->where("goods_id = $goods_id")->count('1');
            $c1 && $error .= $goods_id.'此商品有退货记录,不得删除! <br/>';

            if($error)
            {
                $return_arr = array('status' => -1,'msg' =>$error,'data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
                return $return_arr;
            }
            // 删除此商品
            db("Goods")->where('goods_id ='.$goods_id)->delete();  //商品表
            db("cart")->where('goods_id ='.$goods_id)->delete();  // 购物车
            db("comment")->where('goods_id ='.$goods_id)->delete();  //商品评论
            db("goods_consult")->where('goods_id ='.$goods_id)->delete();  //商品咨询
            db("goods_images")->where('goods_id ='.$goods_id)->delete();  //商品相册
            db("spec_goods_price")->where('goods_id ='.$goods_id)->delete();  //商品规格
            db("spec_image")->where('goods_id ='.$goods_id)->delete();  //商品规格图片
            db("goods_attr")->where('goods_id ='.$goods_id)->delete();  //商品属性
            db("goods_collect")->where('goods_id ='.$goods_id)->delete();  //商品收藏

        }

        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
        return $return_arr;
    }

    /**
     * 删除商品类型
     */
    public function delGoodsType()
    {
        $_GET['id'] = input('param.id');
		// 判断 商品规格
        $count = db("Spec")->where("type_id = {$_GET['id']}")->count("1");
        $count > 0 && $this->error('该类型下有商品规格不得删除!',url('home/Goods/goodsTypeList'));
        // 判断 商品属性
        $count = db("GoodsAttribute")->where("type_id = {$_GET['id']}")->count("1");
        $count > 0 && $this->error('该类型下有商品属性不得删除!',url('home/Goods/goodsTypeList'));
        // 删除分类
        db('GoodsType')->where("id = {$_GET['id']}")->delete();
        $this->success("操作成功!!!",url('home/Goods/goodsTypeList'));
    }

    /**
     * 删除商品属性
     */
    public function delGoodsAttribute()
    {
        $_GET['id'] = input('param.id');
		// 判断 有无商品使用该属性
        $count = db("GoodsAttr")->where("attr_id = {$_GET['id']}")->count("1");
        $count > 0 && $this->error('有商品使用该属性,不得删除!',url('home/Goods/goodsAttributeList'));
        // 删除 属性
        db('GoodsAttribute')->where("attr_id = {$_GET['id']}")->delete();
        $this->success("操作成功!!!",url('home/Goods/goodsAttributeList'));
    }

    /**
     * 删除商品规格
     */
    public function delGoodsSpec()
    {
        $_GET['id'] = input('param.id');
		// 判断 商品规格项
        $count = db("SpecItem")->where("spec_id = {$_GET['id']}")->count("1");
        $count > 0 && $this->error('清空规格项后才可以删除!',url('home/Goods/specList'));
        // 删除分类
        db('Spec')->where("id = {$_GET['id']}")->delete();
        $this->success("操作成功!!!",url('home/Goods/specList'));
    }

    /**
     * 品牌列表
     */
    public function brandList(){
        $model = db("Brand");
        $where = "";
        $keyword = input('keyword');
        $_POST['keyword'] = $keyword?$keyword:'';
        $_GET['p'] = input('p')?input('p'):'';
        $where = $keyword ? " name like '%$keyword%' " : "";

        $brandList = $model->where($where)->order("`sort` asc")->paginate(10);
        $show  = $brandList->render();
        //$cat_list = db('goods_category')->where("parent_id = 0")->column('id,name'); // 已经改成联动菜单
        $cat_list = db('goods_category')->column('id,name'); // 已经改成联动菜单
        $cat_list[0] = "";
        //dump($cat_list);
        $this->assign('cat_list',$cat_list);
        $this->assign('show',$show);

        $this->assign('brandList',$brandList);
        return $this->fetch('brandList');
    }

    /**
     * 添加修改编辑  商品品牌
     */
    public  function addEditBrand(){
            $id = input('id');
            $_GET['p'] = input('p', '');
            $model = db("Brand");
            if(request()->isPost())
            {
                    $request = input('post.');

                    if($id)
                        $model->update($data);
                    else
                        $id = $model->insert($data);

                    $this->success("操作成功!!!",url('home/Goods/brandList',array('p'=>$_GET['p'])));
                    exit;
            }
           $cat_list = db('goods_category')->where("parent_id = 0")->select(); // 已经改成联动菜单
           $this->assign('cat_list',$cat_list);
           $brand = $model->find($id);
           $this->assign('brand',$brand);
           return $this->fetch('_brand');
    }

    /**
     * 删除品牌
     */
    public function delBrand()
    {
        // 判断此品牌是否有商品在使用
        $goods_count = db('Goods')->where("brand_id = {$_GET['id']}")->count('1');
        if($goods_count)
        {
            $return_arr = array('status' => -1,'msg' => '此品牌有商品在用不得删除!','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
            $this->ajaxReturn(json_encode($return_arr));
        }

        $model = db("Brand");
        $model->where('id ='.$_GET['id'])->delete();
        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
        $this->ajaxReturn(json_encode($return_arr));
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
     * 商品规格列表
     */
    public function specList(){
        $_GET['type_id'] =  input('type_id')? input('type_id'):'';
        $goodsTypeList = db("GoodsType")->select();
        $this->assign('goodsTypeList',$goodsTypeList);
        return $this->fetch();
    }


    /**
     *  商品规格列表
     */
    public function ajaxSpecList(){
        //ob_start('ob_gzhandler'); // 页面压缩输出
        $where = ' 1 = 1 '; // 搜索条件
        //$_GET['type_id'] =  input('type_id')? input('type_id'):'';

        if(input('type_id')){
            $where = "$where and type_id = ".input('type_id') ;
        }
        // 关键词搜索
        $model = db('spec');

        $specList = $model->where($where)->order('`type_id` desc')->paginate(13);
        $data  = [
                'list' =>$specList,
                'page' =>$specList->render()
            ];

        $GoodsLogic = model('GoodsLogic' ,'logic');
        foreach($specList as $k => $v)
        {       // 获取规格项
                $arr = $GoodsLogic->getSpecItem($v['id']);
                foreach ($v as $key => $vv) {
                    $list[$k][$key] = $v[$key];
                    $list[$k]['spec_item'] = implode(' , ', $arr);
                }
        }
        $this->assign('specList',$list);
        $this->assign('page',$data['page']);// 赋值分页输出
        $goodsTypeList = db("GoodsType")->select(); // 规格分类
        $goodsTypeList = convert_arr_key($goodsTypeList, 'id');
        $this->assign('goodsTypeList',$goodsTypeList);
        return $this->fetch();
    }

    /**
     * 添加修改编辑  商品规格
     */
    public  function addEditSpec(){
        $model = db("spec");

        $id = input('id/d') ? input('id/d') : 0;
        $spec = $model->find($id);
        $GoodsLogic = new GoodsLogic();
        $items = $GoodsLogic->getSpecItem($id);
        $spec[items] = implode(PHP_EOL, $items);
        $this->assign('spec',$spec);
        $goodsTypeList = db("GoodsType")->select();
        $this->assign('goodsTypeList',$goodsTypeList);
        if (request()->isPost()){
            $items = input('items');
            $id = input('id');
            $data = array(
                'type_id' => input('type_id'),
                'name' => input('name'),
                'order' => input('order'),
            );
            if ($id){
                //更新
                $data['id'] = $id;
                $res = $model->update($data);
            }else{
                //新增
                $res = $model->insertGetId($data);
                $id = $res;
            }
            $this->saveSpec($id,$items);
            if ($res !== false){
                $this->success("操作成功！",url('specList'));
            }else{
                $this->error('操作失败');
            }
        }
        return $this->fetch('_spec');
    }

    public function saveSpec($spec_id,$items=array()){
        $model = db('spec_item');
        $items = explode(PHP_EOL,$items);
        foreach ($items as $key => $val)  // 去除空格
        {
            $val = str_replace('_', '', $val); // 替换特殊字符
            $val = str_replace('@', '', $val); // 替换特殊字符

            $val = trim($val);
            if(empty($val))
                unset($items[$key]);
            else
                $items[$key] = $val;
        }
        $db_items = $model->where("spec_id = $spec_id")->column('id,item');
        /* 提交过来的 跟数据库中比较 不存在 插入*/
        foreach($items as $key => $val)
        {
            if(!in_array($val, $db_items))
                $dataList[] = array('spec_id'=>$spec_id,'item'=>$val);
        }
        // 批量添加数据
        $dataList && $model->insertAll($dataList);
        /* 数据库中的 跟提交过来的比较 不存在删除*/
        foreach($db_items as $key => $val)
        {
            if(!in_array($val, $items))
            {
                //  SELECT * FROM `tp_spec_goods_price` WHERE `key` REGEXP '^11_' OR `key` REGEXP '_13_' OR `key` REGEXP '_21$'
                db("spec_goods_price")->where("`key` REGEXP '^{$key}_' OR `key` REGEXP '_{$key}_' OR `key` REGEXP '_{$key}$' or `key` = '{$key}'")->delete(); // 删除规格项价格表
                $model->where('id='.$key)->delete(); // 删除规格项
            }
        }
    }


//    /**
//     * 添加修改编辑  商品规格
//     */
//    public  function addEditSpec(){
//
//            $model = db("spec");
//            $type = input('id') > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新
//            if((input('is_ajax') == 1) && request()->isPost())//ajax提交验证
//            {
//                config('TOKEN_ON',false);
//                if(!$model->create(NULL,$type))// 根据表单提交的POST数据创建数据对象
//                {
//                    //  编辑
//                    $return_arr = array(
//                        'status' => -1,
//                        'msg'   => '',
//                        'data'  => $model->getError(),
//                    );
//                    $this->ajaxReturn(json_encode($return_arr));
//                }else {
//                   // config('TOKEN_ON',true); //  form表单提交
//                    if ($type == 2)
//                    {
//                        $model->save(); // 写入数据到数据库
//                        $model->afterSave($_POST['id']);
//                    }
//                    else
//                    {
//                        $insert_id = $model->add(); // 写入数据到数据库
//                        $model->afterSave($insert_id);
//                    }
//                    $return_arr = array(
//                        'status' => 1,
//                        'msg'   => '操作成功',
//                        'data'  => array('url'=>url('home/Goods/specList')),
//                    );
//                    $this->ajaxReturn(json_encode($return_arr));
//                }
//            }
//           // 点击过来编辑时
//           $id = input('id/d') ? input('id/d') : 0;
//           $spec = $model->find($id);
//           $GoodsLogic = new GoodsLogic();
//           $items = $GoodsLogic->getSpecItem($id);
//           $spec[items] = implode(PHP_EOL, $items);
//           $this->assign('spec',$spec);
//
//           $goodsTypeList = db("GoodsType")->select();
//           $this->assign('goodsTypeList',$goodsTypeList);
//           return $this->fetch('_spec');
//    }


    /**
     * 动态获取商品规格选择框 根据不同的数据返回不同的选择框
     */
    public function ajaxGetSpecSelect(){
        $goods_id = input('goods_id') ? input('goods_id') : 0;
        $GoodsLogic = new GoodsLogic();
        //$_GET['spec_type'] =  13;
        $specList = Db::name('spec')->where("type_id = ".input('spec_type'))->order('`order` desc')->select();
//echo Db::getLastSql();


        foreach($specList as $k => $v){
            $specList[$k]['spec_item'] = db('spec_item')->where("spec_id = ".$v['id'])->order('id')->column('id,item'); // 获取规格项
       }
/*dump($specList);
dump('specList');   */
            //$items_id = db('spec_goods_price')->where('goods_id = '.$goods_id)->field("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id")->group('goods_id')->fetchSql(true)->select();
            //规格商品价格
            //$items_id = db('spec_goods_price')->where('goods_id = '.$goods_id)->field("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id")->group('goods_id')->select();
            //$items_id = db('spec_goods_price')->where('goods_id = '.$goods_id)->field('key')->select();
            $items_id = db('spec_goods_price')->where('goods_id = '.$goods_id)->field("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id")->group('goods_id')->select();

/*dump($items_id);
dump('items_id');   */
            if(!empty($items_id)){
                $items_ids = explode('_',$items_id[0]['items_id']);
            }
            else{
                $items_ids = null;
            }
/*dump($items_ids);*/

            //$items_id = $items_ids
//dump($items_id);

        // 获取商品规格图片
        if($goods_id)
        {
           //$specImageList = db('spec_image')->where("goods_id = $goods_id")->field('spec_image_id,src')->fetchSql()->select();
           $specImageList = db('spec_image')->where("goods_id = $goods_id")->column('spec_image_id,src');
        }
        else{
            $specImageList = null;
        }
/* dump($specImageList);
 dump('specImageList'); */
  //dump($specList);
        $this->assign('specImageList',$specImageList);
        $this->assign('items_ids',$items_ids);
        $this->assign('specList',$specList);
        return $this->fetch('ajax_spec_select');
    }

    /**
     * 动态获取商品规格输入框 根据不同的数据返回不同的输入框
     */
    public function ajaxGetSpecInput(){
         $GoodsLogic = new GoodsLogic();
         $goods_id = input('goods_id') ? input('goods_id') : 0;
         $spec_arr = input('spec_arr/a');
         //dump($spec_arr);
         $str = $GoodsLogic->getSpecInput($goods_id , $spec_arr);
         exit($str);
    }

    /**
     * 删除商品相册图
     */
    public function del_goods_images()
    {
        $path = input('filename','');
        db('goods_images')->where("image_url = '$path'")->delete();
    }
}