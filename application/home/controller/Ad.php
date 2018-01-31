<?php
/**
 * Author: yangqing      
 * Date: 2016-10-27
 */

namespace app\home\controller;
use think\Request;
use think\Db;
//use \think\Controller;
class Ad extends Base {
    public function ad(){     
        $act = input('act','add');
        $ad_id = input('ad_id');
        $ad_info = array();
        if($ad_id){
            $ad_info = db('ad')->where('ad_id='.$ad_id)->find();
            $ad_info['start_time'] = date('Y-m-d',$ad_info['start_time']);
            $ad_info['end_time'] = date('Y-m-d',$ad_info['end_time']);            
        }
        if($act == 'add'){
            $ad_info['ad_link'] =  '';
            $ad_info['ad_code'] =  '';
            $ad_info['pid'] = input('pid/d');                
        }          
        $position = db('ad_position')->where('1=1')->select();
        $this->assign('info',$ad_info);
        $this->assign('act',$act);
        $this->assign('position',$position);
        return $this->fetch();
    }
    
    public function adList(){
        delFile(RUNTIME_PATH.'Html'); // 先清除缓存, 否则不好预览
            
        $Ad =  db('ad'); 
        $where = "1=1";
        if(input('pid')){
        	$where = "a.pid=".input('pid/d');
        	$this->assign('pid',input('pid/d'));
        }
        $keywords = input('keywords/s');
        if($keywords){
        	$where = "a.ad_name like '%$keywords%'";
        }
        $count = $Ad->alias('a')->where($where)->count();// 查询满足要求的总记录数
        //$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        //$res = $Ad->where($where)->order('pid desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        //        $res = $Ad->where($where)->order('pid desc')->paginate(12);
        $res = $Ad->field('a.ad_id,ap.position_name,a.ad_name,a.ad_code,a.ad_link,a.target,a.enabled,a.orderby')->alias('a')->join('ad_position ap','a.pid=ap.position_id')->where($where)->order('pid desc')->paginate(12);

        $data = array(
            'list' => $res,
            'page' => $res->render()
            );
        $list = array();
        if($res){
        	$media = array('图片','文字','flash');
        	foreach ($data['list'] as $val){
        		$val['media_type'] = $media[$val['media_type']];        		
        		$list[] = $val;
        	}
        }
                          
        //$ad_position_list = db('AdPosition')->column("position_id,position_name,is_open");                        
        $ad_position_list = db('AdPosition')->field("position_id,position_name,is_open")->select();
        $this->assign('ad_position_list',$ad_position_list);//广告位
   

        //
        //$sql="select * from `__PREFIX__goods_category` where `level` = 1"
        //$cat = Db::query($sql);                        
        $cat = Db::query("select * from ".config('database.prefix')."goods_category where level = 1");                        
        //$show = $Page->show();// 分页显示输出
        $this->assign('cat',$cat);// 要类
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$data['page']);// 赋值分页输出
        return $this->fetch();
    }
    
    public function position(){
        $act = input('act/s','add');
        $position_id = input('position_id/d');
        $info = array();
        if($position_id){
            $info = db('ad_position')->where('position_id='.$position_id)->find();
            $this->assign('info',$info);
        }
        else{
            $this->assign('info',null);
        }
        $this->assign('act',$act);
        return $this->fetch();
    }
    
    public function positionList(){
        $Position =  db('ad_position');
        $count = $Position->where('1=1')->count();// 查询满足要求的总记录数
       // $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $res = $Position->order('position_id DESC')->paginate(10);
        $data = array(
            'list' => $res,
            'page' => $res->render()
            );
        $this->assign('list',$data['list']);// 赋值数据集                
       // $show = $Page->show();// 分页显示输出
        $this->assign('page',$data['page']);// 赋值分页输出
        return $this->fetch();
    }
    
    public function adHandle(){
    	$post = input('post.');

        $data['ad_id'] = input('ad_id');
        $data['ad_name'] = $post['ad_name'];
        $data['media_type'] = $post['media_type'];
        $data['pid'] = $post['pid'];
        $data['ad_link'] = $post['ad_link'];
        $data['ad_code'] = $post['ad_code'];
        $data['bgcolor'] = $post['bgcolor'];
        $data['orderby'] = $post['orderby'];
    	$data['start_time'] = strtotime($post['begin']);
    	$data['end_time'] = strtotime($post['end']);
    	if($post['act'] == 'add'){
    		$r = db('ad')->insert($data);
    	}
    	if($post['act'] == 'edit'){
    		$r = db('ad')->where('ad_id='.$data['ad_id'])->update($data);
    	}
    	if($post['act'] == 'del'){
    		$r = db('ad')->where('ad_id='.$post['del_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	$referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('home/Ad/adList');
        // 不管是添加还是修改广告 都清除一下缓存
        delFile(RUNTIME_PATH, 'Html'); // 先清除缓存, 否则不好预览
        
    	if($r){
    		$this->success("操作成功",url('home/Ad/adList'));
    	}else{
    		$this->error("操作失败",$referurl);
    	}
    }
    
    public function positionHandle(){
        $data = input('post.');
        if($data['act'] == 'add'){
            unset($data['act']);
            $r = db('ad_position')->insert($data);
        }
        
        if($data['act'] == 'edit'){
            unset($data['act']);
        	$r = db('ad_position')->where('position_id='.$data['position_id'])->update($data);
        }
        
        if($data['act'] == 'del'){
        	if(db('ad')->where('pid='.$data['position_id'])->count()>0){
        		$this->error("此广告位下还有广告，请先清除",url('home/Ad/positionList'));
        	}else{
        		$r = db('ad_position')->where('position_id='.$data['position_id'])->delete();
        		if($r) exit(json_encode(1));
        	}
        }
        $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('home/Ad/positionList');
        if($r){
        	$this->success("操作成功",url('home/Ad/positionList'));
        }else{
        	$this->error("操作失败",$referurl);
        }
    }
    
    public function changeAdField(){
    	$data[$_REQUEST['field']] = input('GET.value');
    	$data['ad_id'] = input('GET.ad_id');
    	db('ad')->save($data); // 根据条件保存修改的数据
    }
}