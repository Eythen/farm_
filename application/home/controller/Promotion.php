<?php
/**
 * Author: 当燃      
 * 专题管理
 * Date: 2016-03-09
 */

namespace app\home\controller;
use think\Db;
use app\home\Logic\GoodsLogic;

class Promotion extends Base {

    public function index(){
        return $this->fetch();
    }
    
        /**
         * 商品活动列表
         */
	public function prom_goods_list()
	{
		$parse_type = array('0'=>'直接打折','1'=>'减价优惠','2'=>'固定金额出售','3'=>'买就赠优惠券');                               
		$level = db('user_level')->select();
		if($level){
			foreach ($level as $v){
				$lv[$v['level_id']] = $v['level_name'];
			}
		}
		$this->assign("parse_type",$parse_type);
                
        /*        $count = db('prom_goods')->count();
                $Page  = new \Think\Page($count,10);    	 
                $show = $Page->show();                      
		$res = db('prom_goods')->limit($Page->firstRow.','.$Page->listRows)->select();*/

        $res = db('prom_goods')->paginate(10); 
        $show = $res->render();

		if($res){
			foreach ($res as $val){
                $val['group_name'] = '';
				if(!empty($val['group']) && !empty($lv)){
					$val['group'] = explode(',', $val['group']);
					foreach ($val['group'] as $v){
						$val['group_name'] .= $lv[$v].',';
					}
				}
				$prom_list[] = $val;
			}
		}
        $this->assign('page',$show);// 赋值分页输出
		$this->assign('prom_list',$prom_list);
		return $this->fetch();
	}
	
	public function prom_goods_info()
	{
		$level = db('user_level')->select();
		$this->assign('level',$level);
		$prom_id = input('id/d');
		$info['start_time'] = date('Y-m-d');
		$info['end_time'] = date('Y-m-d',time()+3600*60*24);
		if($prom_id>0){
			$info = db('prom_goods')->where("id=$prom_id")->find();
			$info['start_time'] = date('Y-m-d',$info['start_time']);
			$info['end_time'] = date('Y-m-d',$info['end_time']);
			$prom_goods = db('goods')->where("prom_id=$prom_id and prom_type=3")->select();
			$this->assign('prom_goods',$prom_goods);
		}
		$this->assign('info',$info);
		$this->assign('min_date',date('Y-m-d'));

        $coupon = db('coupon')->where('type=0')->select();
        $this->assign('coupon',$coupon);

		$this->initEditor();
		return $this->fetch();
	}
	
	public function prom_goods_save()
	{
		$prom_id = input('id');
		$data = input('post.');
		$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);
		$data['group'] = implode(',', $data['group']);
		if($prom_id){
			db('prom_goods')->where("id=$prom_id")->update($data);
			$last_id = $prom_id;
			adminLog("管理员修改了商品促销 ".input('name'));
		}else{
			$last_id = db('prom_goods')->insert($data);
			adminLog("管理员添加了商品促销 ".input('name'));
		}
		
		if(is_array($data['goods_id'])){
			$goods_id = implode(',', $data['goods_id']);
			if($prom_id>0){
				db("goods")->where("prom_id=$prom_id and prom_type=3")->update(array('prom_id'=>0,'prom_type'=>0));
			}
			db("goods")->where("goods_id in($goods_id)")->update(array('prom_id'=>$last_id,'prom_type'=>3));
		}
		$this->success('编辑促销活动成功',url('Promotion/prom_goods_list'));
	}
	
	public function prom_goods_del()
	{
		$prom_id = input('id');                
                $order_goods = db('order_goods')->where("prom_type = 3 and prom_id = $prom_id")->find();
                if(!empty($order_goods))
                {
                    $this->error("该活动有订单参与不能删除!");    
                }                
		db("goods")->where("prom_id=$prom_id and prom_type=3")->update(array('prom_id'=>0,'prom_type'=>0));
		db('prom_goods')->where("id=$prom_id")->delete();
		$this->success('删除活动成功',url('Promotion/prom_goods_list'));
	}
    

    
        /**
         * 活动列表
         */
	public function prom_order_list()
	{
		$parse_type = array('0'=>'满额打折','1'=>'满额优惠金额','2'=>'满额送积分','3'=>'满额送优惠券');		
		$level = db('user_level')->select();
		if($level){
			foreach ($level as $v){
				$lv[$v['level_id']] = $v['level_name'];
			}
		}

        $res = db('prom_order')->paginate(10);
        $show = $res->render();
		if($res){
			foreach ($res as $val){
                $val['group_name'] = '';
				if(!empty($val['group']) && !empty($lv)){
					$val['group'] = explode(',', $val['group']);
					foreach ($val['group'] as $v){
						$val['group_name'] .= $lv[$v].',';
					}
				}
				$prom_list[] = $val;
			}
		}
        $this->assign('page',$show);// 赋值分页输出                  
        $this->assign("parse_type",$parse_type);
		$this->assign('prom_list',$prom_list);

		return $this->fetch();
	}
	
	public function prom_order_info(){
		$this->assign('min_date',date('Y-m-d'));
		$level = db('user_level')->select();
		$this->assign('level',$level);
		$prom_id = input('id');
		$info['start_time'] = date('Y-m-d');
		$info['end_time'] = date('Y-m-d',time()+3600*24*60);
		if($prom_id>0){
			$info = db('prom_order')->where("id=$prom_id")->find();
			$info['start_time'] = date('Y-m-d',$info['start_time']);
			$info['end_time'] = date('Y-m-d',$info['end_time']);
		}
		$this->assign('info',$info);
		$this->assign('min_date',date('Y-m-d'));

		$this->initEditor();
        $coupon = db('coupon')->where('type=0')->select();
        $this->assign('coupon',$coupon);
		return $this->fetch();
	}
	
	public function prom_order_save(){
		$prom_id = input('id');
		$data = input('post.');
		$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);
		$data['group'] = implode(',', $data['group']);
		if($prom_id){
			db('prom_order')->where("id=$prom_id")->update($data);
			adminLog("管理员修改了商品促销 ".input('name'));
		}else{
			db('prom_order')->insert($data);
			adminLog("管理员添加了商品促销 ".input('name'));
		}
		$this->success('编辑促销活动成功',url('Promotion/prom_order_list'));
	}
	
	public function prom_order_del()
	{
		$prom_id = input('id');                                
                $order = db('order')->where("order_prom_id = $prom_id")->find();
                if(!empty($order))
                {
                    $this->error("该活动有订单参与不能删除!");    
                }
                                
		db('prom_order')->where("id=$prom_id")->delete();
		$this->success('删除活动成功',url('Promotion/prom_order_list'));
	}
	
    public function group_buy_list(){
    	$Ad =  db('group_buy');
    	/*$count = $Ad->count();
    	$Page = new \Think\Page($count,10);        
    	$res = $Ad->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();*/

        $res = $Ad->order('id desc')->paginate(10);
    	if($res){
    		foreach ($res as $val){
    			$val['start_time'] = date('Y-m-d H:i',$val['start_time']);
    			$val['end_time'] = date('Y-m-d H:i',$val['end_time']);
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);
    	$show = $res->render();
    	$this->assign('page',$show);
    	return $this->fetch();
    }
    
    public function group_buy(){
    	$act = input('act','add');
    	$groupbuy_id = input('id');
    	$group_info = array();
    	$group_info['start_time'] = date('Y-m-d');
    	$group_info['end_time'] = date('Y-m-d',time()+3600*365);

    	if($groupbuy_id){
    		$group_info = db('group_buy')->where('id='.$groupbuy_id)->find();
    		$group_info['start_time'] = date('Y-m-d H:i',$group_info['start_time']);
    		$group_info['end_time'] = date('Y-m-d H:i',$group_info['end_time']);
    		$act = 'edit';
    	}
    	$this->assign('min_date',date('Y-m-d'));
    	$this->assign('info',$group_info);
    	$this->assign('act',$act);
    	return $this->fetch();
    }
    
    public function groupbuyHandle(){
    	$data = input('post.');
    	$data['intro'] = htmlspecialchars(stripslashes($_POST['intro']));
    	$data['start_time'] = strtotime($data['start_time']);
    	$data['end_time'] = strtotime($data['end_time']);
    	if($data['act'] == 'del'){
            unset($data['act']);
    		$r = db('group_buy')->where('id='.$data['id'])->delete();
    		db('goods')->where("prom_type=2 and prom_id=".$data['id'])->update(array('prom_id'=>0,'prom_type'=>0));
    		if($r) exit(json_encode(1));
    	}
    	if($data['act'] == 'add'){
            unset($data['act']);
    		$r = db('group_buy')->insert($data);
    		db('goods')->where("goods_id=".$data['goods_id'])->update(array('prom_id'=>$r,'prom_type'=>2));
    	}
    	if($data['act'] == 'edit'){
            unset($data['act']);
    		$r = db('group_buy')->where('id='.$data['id'])->update($data);
    		db('goods')->where("prom_type=2 and prom_id=".$data['id'])->update(array('prom_id'=>0,'prom_type'=>0));
    		db('goods')->where("goods_id=".$data['goods_id'])->update(array('prom_id'=>$data['id'],'prom_type'=>2));
    	}
    	if($r){
    		$this->success("操作成功",url('home/Promotion/group_buy_list'));
    	}else{
    		$this->error("操作失败",url('home/Promotion/group_buy_list'));
    	}
    }
    
    public function get_goods(){
    	$prom_id = input('id');
    	/*$count = db('goods')->where("prom_id=$prom_id and prom_type=3")->count(); 
    	$Page  = new \Think\Page($count,10);
    	$goodsList = db('goods')->where("prom_id=$prom_id and prom_type=3")->order('goods_id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $show = $Page->show();*/

        $goodsList = db('goods')->where("prom_id=$prom_id and prom_type=3")->order('goods_id DESC')->paginate(10);
    	$page = $goodsList->render();
    	$this->assign('page',$page);
    	$this->assign('goodsList',$goodsList);
    	return $this->fetch(); 
    }   
    
    public function search_goods(){
    	$GoodsLogic = model('GoodsLogic','logic');
    	$brandList = $GoodsLogic->getSortBrands();
    	$this->assign('brandList',$brandList);
    	$categoryList = $GoodsLogic->getSortCategory();
    	$this->assign('categoryList',$categoryList);
    	
    	$goods_id = input('goods_id');
    	$where = ' is_on_sale = 1 and prom_type=0 and store_count>0 ';//搜索条件
    	if(!empty($goods_id)){
    		$where .= " and goods_id not in ($goods_id) ";
    	}
    	input('intro')  && $where = "$where and ".input('intro')." = 1";

        $cat_id = input('cat_id')?input('cat_id'):'';
        $brand_id = input('brand_id')?input('brand_id'):'';

    	if($cat_id){
            $grandson_ids = getCatGrandson($cat_id);
            $where = " $where  and cat_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
        }
        if($brand_id){
            $where = "$where and brand_id = ".$brand_id;
        }

    	$this->assign('cat_id',$cat_id);
    	$this->assign('brand_id',$brand_id);


    	if(!empty($_REQUEST['keywords']))
    	{  
            $keywords = input('keywords');
    		$this->assign('keywords',$keywords);
    		$where = "$where and (goods_name like '%".input('keywords')."%' or keywords like '%".input('keywords')."%')" ;
    	}
        else{
            $this->assign('keywords',null);
        }

        $goodsList = Db::name('goods')->where($where)->order('goods_id DESC')->paginate(10);
    	$page = $goodsList->render();//分页显示输出
    	$this->assign('page',$page);//赋值分页输出
    	$this->assign('goodsList',$goodsList);
    	$tpl = input('get.tpl','search_goods');
    	return $this->fetch($tpl);
    }
    
    //限时抢购
    public function flash_sale(){
    	$condition = array();
    	$model = db('flash_sale');
    	/*$count = $model->where($condition)->count();
    	$Page  = new \Think\Page($count,10);    	 
    	$show = $Page->show();
    	$prom_list = $model->where($condition)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();*/

        $prom_list = $model->where($condition)->order("id desc")->paginate(10);
        $show = $prom_list->render();
    	$this->assign('prom_list',$prom_list);
    	$this->assign('page',$show);// 赋值分页输出
    	return $this->fetch();
    }
    
    public function flash_sale_info(){
    	if(request()->isPost()){
    		$data = input('post.');
    		$data['start_time'] = strtotime($data['start_time']);
    		$data['end_time'] = strtotime($data['end_time']);
    		if(empty($data['id'])){
    			$r = db('flash_sale')->insert($data);
    			db('goods')->where("goods_id=".$data['goods_id'])->update(array('prom_id'=>$r,'prom_type'=>1));
    			adminLog("管理员添加抢购活动 ".$data['name']);
    		}else{
    			$r = db('flash_sale')->where("id=".$data['id'])->update($data);
    			db('goods')->where("prom_type=1 and prom_id=".$data['id'])->update(array('prom_id'=>0,'prom_type'=>0));
    			db('goods')->where("goods_id=".$data['goods_id'])->update(array('prom_id'=>$data['id'],'prom_type'=>1));
    		}
    		if($r){
    			$this->success('编辑抢购活动成功',url('Promotion/flash_sale'));
    			exit;
    		}else{
    			$this->error('编辑抢购活动失败',url('Promotion/flash_sale'));
    		}
    	}
    	$id = input('id');
        $info['start_time'] = date('Y-m-d H:i:s');
    	$info['end_time'] = date('Y-m-d 23:59:59',time()+3600*24*60);
    	if($id>0){
    		$info = db('flash_sale')->where("id=$id")->find();
    		$info['start_time'] = date('Y-m-d H:i',$info['start_time']);
    		$info['end_time'] = date('Y-m-d H:i',$info['end_time']);
    	}
    	$this->assign('info',$info);
    	$this->assign('min_date',date('Y-m-d'));
    	return $this->fetch();
    }
    
    public function flash_sale_del(){
    	$id = input('del_id');
    	if($id){
    		db('flash_sale')->where("id=$id")->delete();
    		db('goods')->where("prom_type=1 and prom_id=$id")->update(array('prom_id'=>0,'prom_type'=>0));
    		 exit(json_encode(1));
    	}else{
    		 exit(json_encode(0));
    	}
    }
    
    private function initEditor()
    {
    	$this->assign("URL_upload", url('home/Ueditor/imageUp',array('savepath'=>'promotion')));
    	$this->assign("URL_fileUp", url('home/Ueditor/fileUp',array('savepath'=>'promotion')));
    	$this->assign("URL_scrawlUp", url('home/Ueditor/scrawlUp',array('savepath'=>'promotion')));
    	$this->assign("URL_getRemoteImage", url('home/Ueditor/getRemoteImage',array('savepath'=>'promotion')));
    	$this->assign("URL_imageManager", url('home/Ueditor/imageManager',array('savepath'=>'promotion')));
    	$this->assign("URL_imageUp", url('home/Ueditor/imageUp',array('savepath'=>'promotion')));
    	$this->assign("URL_getMovie", url('home/Ueditor/getMovie',array('savepath'=>'promotion')));
    	$this->assign("URL_home", "");
    }
    
}