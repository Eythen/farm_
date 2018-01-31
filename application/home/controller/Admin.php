<?php
namespace app\home\controller;
use think\Controller;

class Admin extends Base {

    /**
     * [index 用户列表]
     */
    public function index(){
        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where ='';
            if($request['search']){
                $where['username'] = ['like', '%'.$request['search'].'%'];
                $whereor['phone'] = ['like', '%'.$request['search'].'%'];
              }

            $data['rows'] = db('admin')->where($where)->whereor($whereor)->field('uid,username,phone,group_id,status')->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('admin')->where($where)->whereor($whereor)->count();

            //获取部门
            $group = db('auth_group')->field('id, title')->select();
            $group = array_column($group, 'title','id');

            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['group'] = $group[$v['group_id']];
            }
            return $data;

           // dump($request);

        }
        return $this->fetch();
    }

    /**
     * [edit 编辑]
     * @return [type] [description]
     */
    public function edit(){
        $id = input('uid',0,'intval');
        if( request()->isPost() ){
            $request = input('request.');

            $uid = $request['uid'];
            unset($request['uid']);
            if( $uid ){
                $where['uid'] = $uid;
                if($request['act']=="status"){
                    $data = [
                        'status' => $request['status']
                    ];
                }
                else{

                    $data = [
                        'username' => $request['username'],
                        'phone' => $request['phone'],
                        'group_id' => $request['group_id'],
                        'status' => $request['status']
                    ];

                    if(!empty($request['password'])){
                        $data['password'] = md5(md5($request['password']).config('MD5_KEY'));
                    }
                }
                

                $result = db('admin')->where($where)->strict(false)->update($data);

                //更新权限
                $access = [
                    'group_id' => $request['group_id']
                ];
                $result2 = db('auth_group_access')->where('uid='.$uid)->update($access);
 
                if(  in_array($result, [0,1])  && in_array($result2, [0,1]) ){
                    $this->success(config('MSG.UPDATE_SUCCESS'));
                }else{
                    $this->error(config('MSG.UPDATE_ERROR'));
                }
            }else{
                $password = md5(md5($request['password']).config('MD5_KEY'));

                $data = [
                    'password' => $password,
                    'username' => $request['username'],
                    'phone' => $request['phone'],
                    'group_id' => $request['group_id'],
                    'status' => $request['status']
                ];
				
                $r = db('admin')->strict(false)->insertGetId($data);
                $access = [
                    'uid' => $r,
                    'group_id' => $request['group_id']
                ];
                $result = db('auth_group_access')->insert($access);
                
                if( $result ){
                    $this->success(config('MSG.ADD_SUCCESS'));
                }else{
                    $this->error(config('MSG.ADD_ERROR'));
                }
            }
            
        }

        //获取部门
        $group = db('auth_group')->field('id, title')->select();
        $group = array_column($group, 'title','id');
        $this->assign('group',$group);

        if( $id ){

            $data = db('admin')->find($id);
            unset($data['password']);
            $this->assign('data',$data);
        }
        return $this->fetch();
    }

    /**
     * [delete 删除]
     * @return [type] [description]
     */
    public function delete(){
        if( request()->isPost() ){

            $request = input('request.');
            $ids = $request['uid'];
            $uid =explode(',', $ids);
            $uid = array_filter($uid);

            $where['uid'] = ['in', $uid];
            $result2 = db('auth_group_access')->where($where)->delete();
            $result = db('admin')->where($where)->delete();

            if( $result && $result2 ){
                $this->success(config('MSG.DELETE_SUCCESS'));
            }else{
                $this->success(config('MSG.DELETE_ERROR'));
            }
        }
    }

    /**
     * [changePassword 本人修改密码]
     * @return [type] [description]
     */
    public function changePassword(){
        if( request()->isPost() ){

            $request = input('request.');


            $map['uid'] = session('uid');
            $data = db('admin')->where($map)->find();

            if(empty($request['password'])){
                $this->error('新密码不能为空');
            }


            $old_pw = md5(md5($request['oldpassword']).config('MD5_KEY'));
            $pw = md5(md5($request['password']).config('MD5_KEY'));
            if($old_pw == $data['password']){
                $updata = [
                    'password' => $pw
                    ];
                $result = db('admin')->where($map)->update($updata);

                if( $result ){
                    $this->success(config('MSG.UPDATE_SUCCESS'));
                }else{
                    $this->success(config('MSG.UPDATE_ERROR'));
                }
            }
            else{
                $this->error('原密码错误');
            }

        }
        return $this->fetch();

    }
    
    /**
     * [form_avatar 显示头像表单页]
     */
    public function form_avatar(){
        $uid = session('uid');
        if (!empty($uid)) {
            $this->assign(uid,$uid);
        }
        return $this->fetch();
    }
    
    
    
    /**
     * [upload 异步上传头像 ]
     */
    public function upload_avatar(){
    
        $upfolder = "public/uploads/";
        if(!empty($_FILES)){
    
            $tempfile = $_FILES['filedata']['tmp_name']; //文件临时目录
            //$filename = date('YmdHis').$_FILES['filedata']['name']; //上传的文件名
             
            $sExtension = substr($_FILES['filedata']['name'], (strrpos($_FILES['filedata']['name'], '.') + 1));//找到扩展名
            $sExtension = strtolower($sExtension);
    
            $filename = date('YmdHis').'avatar'.'.'.$sExtension;// 上传的文件名
             
             
    
             
            $imagepath = $upfolder.$filename;
             
            $array = array();
            if(move_uploaded_file($tempfile,$upfolder.$filename)){  //将文件从临时目录移动到保存目录
                $array = array(
                    'status' => 1,
                    'src' => $imagepath,
                );
                echo json_encode($array);
            }else{
                echo 0;
            }
    
            exit;
        }
    
    
        echo 0;
    }
    
    
    /**
     *  [savecrop 保存剪切后的头像]
     */
    public  function savecrop(){
         
        $res = array('status'=>0,'msg'=>'保存失败!','filename'=>'');
        $crop = input('post.crop');
        if($crop){
            $targ_w = $targ_h = 100;
            $src = ltrim($crop['path'],'/'); //图片全地址
            $pathinfo = pathinfo($src);
            $cutImg= new \Org\Util\CutImages();
            $cutImg->initialize($src,$crop['x'], $crop['y'], $crop['w'], $crop['h']);
            $cutImg->generateShot();
            $filename=$cutImg->getShotName();
            $filename='/'.$filename;
            $affect= model('admin')->where(array('uid'=>session('uid')))->save(array('avatar'=>$filename));
            if($affect){
                $res['status']=1;
                $res['msg']=config('MSG.SAVE_SUCCESS');
                $res['filename']=$filename;
            }else{
                $res['status'] = 0;
                $res['msg']=config('MSG.SAVE_ERROR');
               
    
            }
            echo  json_encode($res);
            exit;
        }
    }
    
    
}