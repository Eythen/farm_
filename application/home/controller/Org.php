<?php
/**
 * 机构
 */
namespace app\home\controller;

class Org extends Base {

    public function orgList(){
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

            $data['rows'] = db('org')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('org')->where($where)->count();

            //时间格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['name'] = getSubstr($v['name'], '0', '33');
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
            }
            return $data;

        }
        return view();
    }

    public function org(){
 		$act = input('act')?input('act'):'add';
        $info = array();
        if(input('org_id')){
           $org_id = input('org_id');
           $info = db('org')->where('org_id='.$org_id)->find();
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
        $this->assign("URL_upload", url('home/Ueditor/imageUp',array('savepath'=>'org')));
        $this->assign("URL_fileUp", url('home/Ueditor/fileUp',array('savepath'=>'org')));
        $this->assign("URL_scrawlUp", url('home/Ueditor/scrawlUp',array('savepath'=>'org')));
        $this->assign("URL_getRemoteImage", url('home/Ueditor/getRemoteImage',array('savepath'=>'org')));
        $this->assign("URL_imageManager", url('home/Ueditor/imageManager',array('savepath'=>'org')));
        $this->assign("URL_imageUp", url('home/Ueditor/imageUp',array('savepath'=>'org')));
        $this->assign("URL_getMovie", url('home/Ueditor/getMovie',array('savepath'=>'org')));
        $this->assign("URL_home", "");
    }
    
    public function orgHandle(){
        $data = input('post.');
        if($data['act'] == 'add'){
            unset($data['act']);
        	$data['add_time'] = time(); 
            $r = db('org')->insert($data);
        }
        
        if($data['act'] == 'edit'){
            unset($data['act']);
            $r = db('org')->update($data);
        }
        
        if($data['act'] == 'del'){
            $data['org_id'] = trim($data['org_id'], ',');
            $map['org_id'] = ['in', $data['org_id']];
        	$r = db('org')->where($map)->delete();
        }

        if($r !== false){
            $this->success("操作成功");
        }else{
            $this->error("操作失败");
        }
    }

}