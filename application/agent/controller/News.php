<?php
/**
 * 新闻模块
 */
namespace app\agent\controller;
use \think\Controller;

class News extends Base {
	
	/*
     * 初始化操作
     */
    public function _initialize() {
        if(session('user_id') && session('user')['manager_mobile']){
            //$this->redirect('Users/index');
        }
        else{
            if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
                $this->redirect('Loginweixin/index');
            }
            else{
                $this->redirect('Login/index');
            }
        }
    }
	
    //新闻列表
    public function index(){
        if(request()->isAjax()){
            $get = input('get.');
            $page = $get['num']?$get['num']:1;
            $pagesize = $get['size']?$get['size']:10;
            $offset = ($page-1)*$pagesize;

            $data['lists'] = db('article')->where(['is_open' =>1, 'cat_id' => 4 ])->field('article_id, title, publish_time')->order('publish_time desc')->limit($offset, $pagesize)->select();
            $data['length'] = db('article')->where(['is_open' =>1, 'cat_id' => 4 ])->field('article_id, title, publish_time')->order('publish_time desc')->count();
            if($data['length']){
                foreach ($data['lists'] as $k => $v) {
                    $map['user_id'] = session('user_id');
                    $map['message_id'] = $v['article_id'];
                    $has_message = db('user_message')->where($map)->find();
                    if(!$has_message){
                        $data['lists'][$k]['status'] = 0;
                    }
                    else{
                        $data['lists'][$k]['status'] = 1;
                    }
                    $data['lists'][$k]['pddate'] = formatTime($v['publish_time'], 'Y-m-d H:i:s');
                    $data['lists'][$k]['page']=$page;
                    $data['lists'][$k]['pdurl'] = url('detail', ['article_id' => $v['article_id']]);
                    $data['lists'][$k]['pdName'] = $v['title'];

                }
            }

            
            return $data;
        }

        return view();
	}

	//详情
    public function detail(){
    	$map['article_id'] = input('article_id/d');
    	$map['cat_id'] = 4;
        $article = db('article')->where($map)->find();

        if(!$article){
            $this->error('信息页面不存在！');
        }
        if($article['is_open'] <>1){
            $this->error('信息页面设置了前台不显示！');
        }
        //内部处理 未读取就不记录，已读取记录到数据表
        $r = db('user_message')->where(['message_id'=> $article['article_id'], 'user_id' => session('user_id')])->find();
        if(!$r){
            $data = [
                'message_id' => $map['article_id'],
                'user_id' => session('user_id'),
                'category' => 4,
                'status' => 1,
            ];
            db('user_message')->insert($data);
        }
            
        $this->assign('article', $article);

        return view();
	}





}
?>