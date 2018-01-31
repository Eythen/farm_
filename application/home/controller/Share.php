<?php
/**
 * 推广
 */
namespace app\home\controller;

class Share extends Base {

    public function shareList(){
        $water = array('全部','左下角','下居中','居中','右下角');
        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where = '';
            if($request['search']){
                $where['name'] = ['like', '%'.$request['search'].'%'];
            }

            $data['rows'] = db('share')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('share')->where($where)->count();

            //时间格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['name'] = getSubstr($v['name'], '0', '33');
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['water'] = $water[$v['water']];
            }
            return $data;

        }
        return view();
    }

    public function share(){
 		$act = input('act')?input('act'):'add';
        $info = array();
        if(input('share_id')){
           $share_id = input('share_id');
           $info = db('share')->where('share_id='.$share_id)->find();
        }
        $this->assign('act',$act);
        $this->assign('info',$info);
        $this->initEditor();
        return $this->fetch();
    }

    /**
     * 初始化编辑器链接
     * @param $post_id post_id
     */
    private function initEditor()
    {
        $this->assign("URL_upload", url('home/Ueditor/imageUp',array('savepath'=>'share')));
        $this->assign("URL_fileUp", url('home/Ueditor/fileUp',array('savepath'=>'share')));
        $this->assign("URL_scrawlUp", url('home/Ueditor/scrawlUp',array('savepath'=>'share')));
        $this->assign("URL_getRemoteImage", url('home/Ueditor/getRemoteImage',array('savepath'=>'share')));
        $this->assign("URL_imageManager", url('home/Ueditor/imageManager',array('savepath'=>'share')));
        $this->assign("URL_imageUp", url('home/Ueditor/imageUp',array('savepath'=>'share')));
        $this->assign("URL_getMovie", url('home/Ueditor/getMovie',array('savepath'=>'share')));
        $this->assign("URL_home", "");
    }
    
    public function shareHandle(){
        $data = input('post.');
        if($data['act'] == 'add'){
            unset($data['act']);
            $data['add_time'] = time(); 
            $data['update_time'] = time();
        	$data['user_id'] = session('admin_id'); 
            $r = db('share')->insert($data);
        }
        
        if($data['act'] == 'edit'){
            unset($data['act']);
            $data['update_time'] = time(); 
            $r = db('share')->update($data);
        }
        
        if($data['act'] == 'del'){
            $data['share_id'] = trim($data['share_id'], ',');
            $map['share_id'] = ['in', $data['share_id']];
        	$r = db('share')->where($map)->delete();
        }

        if($r !== false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }

}