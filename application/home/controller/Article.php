<?php
/**
 * Author: 当燃      
 * Date: 2015-09-09
 */
namespace app\home\controller;
use app\home\logic\ArticleCat;

class Article extends Base {

    public function categoryList(){
        $ArticleCat = new ArticleCat(); 
        $cat_list = $ArticleCat->article_cat_list(0, 0, false);
        $type_arr = array('新闻','系统帮助','系统公告');
        $this->assign('type_arr',$type_arr);
        //dump($cat_list);
        $this->assign('cat_list',$cat_list);
        return $this->fetch('categoryList');
    }
    
    public function category(){
        $ArticleCat = new ArticleCat();  
 		$act = input('act')?input('act'):'add';
        $this->assign('act',$act);
        $cat_id = input('cat_id')?input('cat_id'):0;
        $cat_info = array();
        if($cat_id){
            $cat_info = db('article_cat')->where('cat_id='.$cat_id)->find();
            //dump(db()->getLastSql());
            $this->assign('cat_info',$cat_info);
        }
        else{
            $cat_info['parent_id'] = 0;
            $this->assign('cat_info',null);
        }
        $cats = $ArticleCat->article_cat_list(0,$cat_info['parent_id'],true);
        $this->assign('cat_select',$cats);
        return $this->fetch();
    }
    
    public function articleList(){
        $type = input('type')?input('type'):1;
        $type = str_replace('?v=4.0', '', $type);
        $this->assign('type',$type);

        $ArticleCat = model('ArticleCat', 'logic');
        $cats = $ArticleCat->article_cat_list(0,0,false);
        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            if($type){

                if($type==2){
                    $where['cat_id'] = ['in',[2,3]];
                }
                else{
                    $where['cat_id'] = $type;
                }
            }
            if($request['cat_id']){
                $where['cat_id'] = ['eq', $request['cat_id'] ];
            }
            if($request['search']){
                $where['title'] = ['like', '%'.$request['search'].'%'];
              }
            //$data['rows'] = db('article')->where($where)->whereor($whereor)->field('id, sn, num, update_time, ip')->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['rows'] = db('article')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('article')->where($where)->whereor($whereor)->count();

            //时间格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['title'] = getSubstr($v['title'], '0', '33');
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['category'] =  $cats[$v['cat_id']]['cat_name'];
            }
            return $data;

        }
        

        
        $this->assign('cats',$cats);
        $this->assign('cat_id',$cat_id);
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出
        return $this->fetch('articleList');
    }
    
    public function article(){
        //分类
        $type = input('type')?input('type'):1;
        $type = str_replace('?v=4.0', '', $type);
        $this->assign('type',$type);
        $ArticleCat = new ArticleCat();
        $act = input('act')?input('act'):'add';
        $info = array();
        $info['publish_time'] = time()+3600*24;
        $level = [];
        $user_ids = [];
        $$users = [];
        if($type==2){  //消息时读取用户等级
            $level = db('user_level')->column('level_name, level_id','level_id');  
        }

        if(input('article_id')){
           $article_id = input('article_id');
           $info = db('article')->where('article_id='.$article_id)->find();
           if($info['cat_id'] ==2){  //用户消息
            
            $user_ids = db('user_message')->where('message_id', $article_id)->column('user_id');

            $map['user_id'] = ['in', $user_ids];
            $users = db('users')->where($map)->field('user_id,nickname,mobile')->select();
           }
        }

        if(empty($info['cat_id'])){
            $info['cat_id'] = $type;
        }
        $cats = $ArticleCat->article_cat_list(0,$info['cat_id']);
        $this->assign('cat_select',$cats);
        $this->assign('act',$act);
        $this->assign('info',$info);
        $this->assign('level',$level);
        $this->assign('user_ids',$user_ids);
        $this->assign('users', $users);
        $this->initEditor();
        return $this->fetch();
    }
    
    
    /**
     * 初始化编辑器链接
     * @param $post_id post_id
     */
    private function initEditor()
    {
        $this->assign("URL_upload", url('home/Ueditor/imageUp',array('savepath'=>'article')));
        $this->assign("URL_fileUp", url('home/Ueditor/fileUp',array('savepath'=>'article')));
        $this->assign("URL_scrawlUp", url('home/Ueditor/scrawlUp',array('savepath'=>'article')));
        $this->assign("URL_getRemoteImage", url('home/Ueditor/getRemoteImage',array('savepath'=>'article')));
        $this->assign("URL_imageManager", url('home/Ueditor/imageManager',array('savepath'=>'article')));
        $this->assign("URL_imageUp", url('home/Ueditor/imageUp',array('savepath'=>'article')));
        $this->assign("URL_getMovie", url('home/Ueditor/getMovie',array('savepath'=>'article')));
        $this->assign("URL_home", "");
    }
    
    
    public function categoryHandle(){
    	$data = input('post.');
        
        if($data['act'] == 'add'){ 
            unset($data['act']);          
            $d = db('article_cat')->insert($data);
        }
        
        if($data['act'] == 'edit')
        {
        	unset($data['act']);
            if ($data['cat_id'] == $data['parent_id']) 
			{
        		$this->error("所选分类的上级分类不能是当前分类",url('home/Article/category',array('cat_id'=>$data['cat_id'])));
        	}
        	$ArticleCat = new ArticleCat();
        	$children = array_keys($ArticleCat->article_cat_list($data['cat_id'], 0, false)); // 获得当前分类的所有下级分类
        	if (in_array($data['parent_id'], $children))
        	{
        		$this->error("所选分类的上级分类不能是当前分类的子分类",url('home/Article/category',array('cat_id'=>$data['cat_id'])));
        	}
        	$d = db('article_cat')->where("cat_id=$data[cat_id]")->update($data);
            if($d){
                $this->success("操作成功", url('home/Article/category',array('cat_id'=>$data['cat_id'],'act'=>'edit')));
            }else{
                $this->error("操作失败", url('home/Article/category',array('cat_id'=>$data['cat_id'],'act'=>'edit')));
            }
        }
        
        if($data['act'] == 'del'){      	
        	$res = db('article_cat')->where('parent_id ='.$data['cat_id'])->select(); 
        	if ($res)
        	{
        		exit(json_encode('还有子分类，不能删除'));
        	}
        	$res = db('article')->where('cat_id ='.$data['cat_id'])->select();       	      	
        	if ($res)
        	{
        		exit(json_encode('非空的分类不允许删除'));
        	}      	
        	$r = db('article_cat')->where('cat_id='.$data['cat_id'])->delete();
        	if($r) exit(json_encode(1));
        }
        if($d){
        	$this->success("操作成功",url('home/Article/category'));
        }else{
        	$this->error("操作失败",url('home/Article/category'));
        }
    }
    
    public function aticleHandle(){
        $data = input('post.');

        db()->startTrans();
        try {
            $data['publish_time'] = strtotime($data['publish_time']);
            //$data['content'] = htmlspecialchars(stripslashes($_POST['content']));
            if($data['act'] == 'add'){
                unset($data['act']);
                $data['click'] = mt_rand(1000,1300);
            	$data['add_time'] = time(); 
                $data['admin_id'] = session('uid');
                $article_id = db('article')->strict(false)->insertGetId($data);
                if($data['cat_id'] == 2){
                    $ids = trim($data['user_ids'], ',');
                    if($data['level_id'] && empty($ids)){  //选择了用户分类，就发给分类所有用户
                        $all_ur = get_all_users('user_id,level');
                        foreach ($all_ur as $k => $v) {
                            if($v['level'] == $data['level_id']){
                                $ids .= ','.$v['user_id'];
                            }
                        }
                    }
                    if($ids){
                        $ids = trim($data['user_ids'], ',');
                        $id_r = explode(',', $ids);
                        $message=[];
                        foreach ($id_r as $k => $v) {
                            $message[] = [
                                'message_id' => $article_id,
                                'user_id' => $v,
                                ];
                        }
                    }
                    db('user_message')->insertAll($message);
                }
                
            }
            
            if($data['act'] == 'edit'){
                unset($data['act']);
                $data['update_time'] = time();
                $data['update_admin_id'] = session('uid');
                $r = db('article')->where('article_id='.$data['article_id'])->strict(false)->update($data);
                if($data['cat_id'] == 2){
                    //清空原来的
                    db('user_message')->where('message_id='.$data['article_id'])->delete();
                    //添加现在的用户
                    $ids = trim($data['user_ids'], ',');
                    if($data['level_id'] && empty($ids)){  //选择了用户分类，就发给分类所有用户
                        $all_ur = get_all_users('user_id,level');
                        $ids = '';
                        foreach ($all_ur as $k => $v) {
                            if($v == $data['level_id']){
                                $ids .= ",".$k;
                            }
                        }
                    }
                    if($ids){
                        $ids = trim($ids, ',');
                        $id_r = explode(',', $ids);
                        $message=[];
                        foreach ($id_r as $k => $v) {
                            $message[] = [
                                'message_id' => $data['article_id'],
                                'user_id' => $v,
                                ];
                        }
                        
                    }
                    db('user_message')->insertAll($message);
                }
            }
            
            if($data['act'] == 'del'){
                $data['article_id'] = trim($data['article_id'], ',');
                $map['article_id'] = ['in', $data['article_id']];
            	$r = db('article')->where($map)->delete();

            	      	
            }
            //$referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('home/Article/articleList');
            $referurl = url('home/Article/articleList', ['type' => $data['cat_id']]);
            
            db()->commit();
            return $this->success("操作成功",$referurl);
        }
        catch (Exception $e){
            db()->rollback();
            return $this->errorerror("操作失败",$referurl);
        }
    }
    
    
    public function link(){
    	$act = input('act')?input('act'):'add';
    	$this->assign('act',$act);
    	$link_id = input('link_id');
    	$link_info = array();
    	if($link_id){
    		$link_info = db('friend_link')->where('link_id='.$link_id)->find();
    		$this->assign('info',$link_info);
    	}
        else{
            $this->assign('info',null);
        }
    	return $this->fetch();
    }
    
    public function linkList(){
    	$Ad =  db('friend_link');
    	$res = $Ad->where('1=1')->order('orderby')->paginate(12);
        $data = [
                'list' =>$res,
                'page' =>$res->render(),
        ];
    	if($data['list']){
    		foreach ($data['list'] as $val){
    			$val['target'] = $val['target']>0 ? '开启' : '关闭';
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);// 赋值数据集
    	$count = $Ad->where('1=1')->count();// 查询满足要求的总记录数
    	//$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
    	//$show = $Page->show();// 分页显示输出
    	$this->assign('page',$data['page']);// 赋值分页输出
    	return $this->fetch();
    }
    
    public function linkHandle(){
        $data = input('post.');
        //处理字段$data2['act']
        $data2 = $data;
        unset($data2['act']);

        if($data['act'] == 'add'){
    		//stream_context_set_default(array('http'=>array('timeout' =>2)));send_http_status('311');
    		$r = db('friend_link')->insert($data2);
    	}
    	if($data['act'] == 'edit'){
    		$r = db('friend_link')->where('link_id='.$data['link_id'])->update($data2);
    	}
    	
    	if($data['act'] == 'del'){
    		$r = db('friend_link')->where('link_id='.$data['link_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	
    	if($r){
    		$this->success("操作成功",url('home/article/linkList'));
    	}else{
    		$this->error("操作失败",url('home/article/linkList'));
    	}
    }
    
    /**
     * 文章内容页
     */
    public function detail(){
        $article_id = (int)input('article_id',20);
        $article = db('article')->where("article_id=$article_id")->find();

        if($article){
            $cat_id = $article['cat_id'];
            $parent = db('article_cat')->where("cat_id=$cat_id")->find();
            $this->assign('cat_name',$parent['cat_name']);
            $this->assign('article',$article);
        }
        return $this->fetch();
    } 


}