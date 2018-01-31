<?php
/**
 * 评论管理控制器
 * Date: 2015-10-20
 */

namespace app\home\controller;


use think\Model;
/*use Think\AjaxPage;
use Think\Page;*/

class Comment extends Base {


    public function index(){
        $comment_list = '';
        $page = '';
        $this->assign('comment_list', $comment_list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function detail(){
        $id = input('id/d');
        $res = db('comment')->where(array('comment_id'=>$id))->find();
        if(!$res){
            exit($this->error('不存在该评论'));
        }
        if(request()->isPost()){
            $add['parent_id'] = $id;
            $add['content'] = input('content');
            $add['goods_id'] = $res['goods_id'];
            $add['add_time'] = time();
            $add['username'] = 'admin';

            $add['is_show'] = 1;

            $row =  db('comment')->insert($add);
            if($row){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
            exit;

        }
        $reply = db('comment')->where(array('parent_id'=>$id))->select(); // 评论回复列表
         
        $this->assign('comment',$res);
        $this->assign('reply',$reply);
        return $this->fetch();
    }


    public function del(){
        $id = input('get.id');
        $row = db('comment')->where(array('comment_id'=>$id))->delete();
        if($row){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function op(){
        $post = input('post.');
        $type = $post['type'];

        $selected_id = $post['selected'];
        if(!in_array($type,array('del','show','hide')) || !$selected_id)
            $this->error('非法操作');
        $where = "comment_id IN ({$selected_id})";
        if($type == 'del'){
            //删除回复
            $where .= " OR parent_id IN ({$selected_id})";
            $row = db('comment')->where($where)->delete();
//            exit(M()->getLastSql());
        }
        if($type == 'show'){
            $row = db('comment')->where($where)->update(array('is_show'=>1));
        }
        if($type == 'hide'){
            $row = db('comment')->where($where)->update(array('is_show'=>0));
        }
        if(!$row)
            $this->error('操作失败');
        $this->success('操作成功');

    }

    public function ajaxindex(){
        $model = db('comment');
        /*$username = input('nickname','','trim');
        $content = input('content','','trim');*/

        $username = input('nickname')?trim(input('nickname')):'';
        $content = input('content')?trim(input('content')):'';

        $where=' parent_id = 0';
        if($username){
            $where .= " AND username='$username'";
        }
        if($content){
            $where .= " AND content like '%{$content}%'";
        } 
                
        $comment_list = $model->where($where)->order('add_time DESC')->paginate(16);
        $page = $comment_list->render();
        if(!empty($comment_list))
        {
            $goods_id_arr = get_arr_column($comment_list, 'goods_id');
            $goods_list = db('Goods')->where("goods_id in (".  implode(',', $goods_id_arr).")")->column("goods_id,goods_name");
        }
        $this->assign('goods_list',$goods_list);
        $this->assign('comment_list',$comment_list);
        $this->assign('page',$page);// 赋值分页输出
        return $this->fetch();
    }
    
    public function ask_list(){
        $comment_list = '';
        $page = '';
        $this->assign('comment_list', $comment_list);
        $this->assign('page', $page);
    	return $this->fetch();
    }
    
    public function ajax_ask_list(){
    	$model = db('goods_consult');
    	$username = input('nickname')?trim(input('nickname')):'';
    	$content = input('content')?trim(input('content')):'';
    	$where=' parent_id = 0';
    	if($username){
    		$where .= " AND username='$username'";
    	}
    	if($content){
    		$where .= " AND content like '%{$content}%'";
    	}      	
    	
        $comment_list = $model->where($where)->order('add_time DESC')->paginate(16); 
        $page = $comment_list->render();//分页
        $goods_list = '';
    	if(!empty($comment_list))
    	{
    		$goods_id_arr = get_arr_column($comment_list, 'goods_id');
    		$goods_list = db('Goods')->where("goods_id in (".  implode(',', $goods_id_arr).")")->column("goods_id,goods_name");
    	}
    	$consult_type = array(0=>'默认咨询',1=>'商品咨询',2=>'支付咨询',3=>'配送',4=>'售后');
    	$this->assign('consult_type',$consult_type);
    	$this->assign('goods_list',$goods_list);
    	$this->assign('comment_list',$comment_list);
    	$this->assign('page',$page);// 赋值分页输出
    	return $this->fetch();
    }
    
    public function consult_info(){
    	$id = input('id/d');
    	$res = db('goods_consult')->where(array('id'=>$id))->find();
    	if(!$res){
    		exit($this->error('不存在该咨询'));
    	}
    	if(request()->isPost()){
    		$add['parent_id'] = $id;
    		$add['content'] = input('content');
    		$add['goods_id'] = $res['goods_id'];
                $add['consult_type'] = $res['consult_type'];
    		$add['add_time'] = time();    		
    		$add['is_show'] = 1;   	
    		$row =  db('goods_consult')->insert($add);
    		if($row){
    			$this->success('添加成功');
    		}else{
    			$this->error('添加失败');
    		}
    		exit;    	
    	}
    	$reply = db('goods_consult')->where(array('parent_id'=>$id))->select(); // 咨询回复列表   	 
    	$this->assign('comment',$res);
    	$this->assign('reply',$reply);
    	return $this->fetch();
    }
    public function ask_handle(){
    	$post = input('post.');
        $type = $post['type'];

        $selected_id = $post['selected'];      
    	if(!in_array($type,array('del','show','hide')) || !$selected_id)
    		$this->error('操作完成');
    
        $selected_id = implode(',',$selected_id);
    	if($type == 'del'){
    		//删除咨询
    		$where .= "( id IN ({$selected_id}) OR parent_id IN ({$selected_id})) ";
    		$row = db('goods_consult')->where($where)->delete();
    	}
    	if($type == 'show'){
    		$row = db('goods_consult')->where("id IN ({$selected_id})")->update(array('is_show'=>1));
    	}
    	if($type == 'hide'){
    		$row = db('goods_consult')->where("id IN ({$selected_id})")->update(array('is_show'=>0));
    	}    		
    	$this->success('操作完成');
    }
}