<?php
/**
 * tpshop
 * ============================================================================

 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: 当燃      
 * 专题管理
 * Date: 2015-09-09
 */

namespace app\home\controller;

class Topic extends Base {

    public function index(){
        return $this->fetch();
    }
    
    public function topic(){
    	$act = input('act')?input('act'):'add';
    	$this->assign('act',$act);
    	$topic_id = input('topic_id');
    	$topic_info = array();
    	if($topic_id){
    		$topic_info = db('topic')->where('topic_id='.$topic_id)->find();
    		$this->assign('info',$topic_info);
    	}
        else{
            $this->assign('info',null);
        }
    	
    	$this->assign("URL_upload", url('home/Ueditor/imageUp',array('savepath'=>'topic')));
    	$this->assign("URL_fileUp", url('home/Ueditor/fileUp',array('savepath'=>'topic')));
    	$this->assign("URL_scrawlUp", url('home/Ueditor/scrawlUp',array('savepath'=>'topic')));
    	$this->assign("URL_getRemoteImage", url('home/Ueditor/getRemoteImage',array('savepath'=>'topic')));
    	$this->assign("URL_imageManager", url('home/Ueditor/imageManager',array('savepath'=>'topic')));
    	$this->assign("URL_imageUp", url('home/Ueditor/imageUp',array('savepath'=>'topic')));
    	$this->assign("URL_getMovie", url('home/Ueditor/getMovie',array('savepath'=>'topic')));
    	$this->assign("URL_home", '');
    	return $this->fetch();
    }
    
    public function topicList(){
    	$Ad =  db('topic');
        //$res = $Ad->where('1=1')->order('ctime')->page($_GET['p'].',10')->select();
    	$res = $Ad->where('1=1')->order('ctime')->paginate(10);
    	if($res){
    		foreach ($res as $val){           
    			$val['topic_state'] = $val['topic_state']>1 ? '已发布' : '未发布';
    			$val['ctime'] = date('Y-m-d H:i',$val['ctime']);
    			$list[] = $val;
    		}
    	}

        $page = $res->render();
        $this->assign('page',$page);//分页
    	$this->assign('list',$list);// 赋值数据集
    	return $this->fetch();
    }
    
    public function topicHandle(){
    	$data = input('post.');       
        $data['topic_content'] = $_POST['topic_content']; // 这个内容不做转义        
    	if($data['act'] == 'add'){
    		$data['ctime'] = time();
    		$r = db('topic')->insert($data);
    	}
    	if($data['act'] == 'edit'){
    		$r = db('topic')->where('topic_id='.$data['topic_id'])->update($data);
    	}
    	 
    	if($data['act'] == 'del'){
    		$r = db('topic')->where('topic_id='.$data['topic_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	 
    	if($r){
    		$this->success("操作成功",url('home/Topic/topicList'));
    	}else{
    		$this->error("操作失败",url('home/Topic/topicList'));
    	}
    }
}