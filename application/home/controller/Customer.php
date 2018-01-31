<?php
/**
 * 客户订单
 * 
 */
namespace app\home\controller;
use think\Controller;

class Customer extends Base {

    private $link_status = array('0' => '新增', '1' => '正在处理', '2' => '处理完结',  '3' => '成交');
    private $customer_type = array('1'=> '实习证明', '2'=>'网站开发');

    /**
     * [index 账号列表]
     */
    public function index(){


        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where = [];
            $where['is_delete'] = 0;
            if($request['url']){
                $where['url'] = ['like', '%'.$request['url'].'%'];
            }
            if($request['site_name']){
                $where['site_name'] = ['like', '%'.$request['site_name'].'%'];
            }
            if($request['userid']){
                $where['userid'] = ['eq', $request['userid']];
            }
            if($request['phone']){
                $where['phone'] = ['eq', $request['phone']];
            }
            if($request['customer']){
                $where['customer'] = ['eq', $request['customer']];
            }
            
            if($request['add_time']){
                $start = strtotime($request['add_time']);
                $end = strtotime($request['add_time'])+3600*24;
                $where['add_time'] = ['between',[$start, $end]];
            }
            if($request['update_time']){
                $start = strtotime($request['update_time']);
                $end = strtotime($request['update_time'])+3600*24;
                $where['update_time'] = ['between',[$start, $end]];
            }

            $data['rows'] = db('customer')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('customer')->where($where)->whereor($whereor)->count();

            $ur = model('Users')->getAll(); //获取全部用户
            $ur = array_column($ur, 'username', 'uid');

            $customer_type = $this->customer_type;
            
            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['update_userid'] = $ur[$v['update_userid']];
                $data['rows'][$k]['userid'] = $ur[$v['userid']];
                $data['rows'][$k]['type'] = $customer_type[$v['type']];
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                if(session('uid')<>$v['userid']){
                    $data['rows'][$k]['phone'] = nophone($v['phone']);
                }
            }
            
            
            return $data;
        }

        $ur = model('Users')->getAll(); //获取全部用户
        $ur = array_column($ur, 'username', 'uid');
        $this->assign('ur', $ur);

        return $this->fetch();
    }

    /**
     * [edit 编辑]
     * @return [type] [description]
     */
    public function edit(){
        $id = input('id',0,'intval');
        $customer_type = $this->customer_type;
        $this->assign('customer_type', $customer_type);

        if( $id ){
            $data = db('customer')->find($id);
            if(session('uid')<>$data['userid']){
                $data['phone'] = nophone($data['phone']);
            }
            $this->assign('data',$data);
        }
        if( request()->isPost() ){
            $request = input('request.');

            $id = $request['id'];
            unset($request['id']);
            if( $id ){
                $where['id'] = $id;
                $data2 = [
                    'customer' => $request['customer'],
                    'phone' => $request['phone'],
                    'address' => $request['address'],
                    'type' => $request['type'],
                    'remark' => $request['remark'],
                    'url' => $request['url'],
                    'site_name' => $request['site_name'],
                    'name' => $request['name'],
                    'password' => $request['password'],
                    'update_userid' => session('uid'),
                    'update_time' => time()
                    ];
                $result = db('customer')->where($where)->update($data2);

                //比较数据更新的内容
                $content = '';    
                $data3 = [
                    'customer' => $data['customer'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'type' => $data['type'],
                    'remark' => $data['remark'],
                    'url' => $data['url'],
                    'site_name' => $data['site_name'],
                    'name' => $data['name'],
                    'password' => $data['password']
                    ];   
                foreach ($data3 as $k => $v) {
                    if(!in_array($v, $data2)){
                        $content .= $k."：".$data3[$k]. "->". $data2[$k]."；";
                    }
                }

                //写入更改日志 
                $log = [
                    'customer_id' => $id,
                    'content' => $content,
                    'update_userid' => session('uid'),
                    'update_time' => time(),

                    ];
                db('customer_log')->insert($log);
                       
                if( $result ){
                    $this->success(config('MSG.UPDATE_SUCCESS'));
                }else{
                    $this->error(config('MSG.UPDATE_ERROR'));
                }
            }else{
                $map['name'] =  $request['name'];
                $map['url'] =  $request['url'];
                $fr = db('customer')->where($map)->find();
                if($fr){
                    $this->error('已经存在这个网站账号名字');
                }

                $data2 = [
                    'customer' => $request['customer'],
                    'phone' => $request['phone'],
                    'address' => $request['address'],
                    'type' => $request['type'],
                    'remark' => $request['remark'],
                    'url' => $request['url'],
                    'site_name' => $request['site_name'],
                    'name' => $request['name'],
                    'password' => $request['password'],
                    'userid' => session('uid'),
                    'add_time' => time()
                    ];

                $result = db('customer')->insert($data2);

                if( $result ){
                    $this->success(config('MSG.ADD_SUCCESS'));
                }else{
                    $this->error(config('MSG.ADD_ERROR'));
                }
            }
            
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
            $ids = $request['id'];
            $id =explode(',', $ids);
            $id = array_filter($id);


            $where['id'] = ['in', $id];
            $data['is_delete'] = 1;
            $data['update_userid'] = session('uid');
            $data['update_time'] = time();
            $result = db('customer')->where($where)->update($data);



            if( $result ){
                //写入更改日志
                foreach ($id as $k => $v) {
                    $log = [
                        'customer_id' => $v,
                        'content' => '删除id='.$v,
                        'update_userid' => session('uid'),
                        'update_time' => time(),

                        ];
                    db('customer_log')->insert($log);
                 } 
                $this->success(config('MSG.DELETE_SUCCESS'));
            }else{
                $this->success(config('MSG.DELETE_ERROR'));
            }
        }
    }


    /**
     * [log 日志列表]
     */
    public function log(){

        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where = [];
            if($request['userid']){
                $where['userid'] = ['eq', $request['userid']];
            }
            if($request['search']){
                $where['content'] = ['like', '%'.$request['search'].'%'];
            }


            $data['rows'] = db('customer_log')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('customer_log')->where($where)->whereor($whereor)->count();

            $ur = model('Users')->getAll(); //获取全部用户
            $ur = array_column($ur, 'username', 'uid');

            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['update_userid'] = $ur[$v['update_userid']];
            }
            return $data;

           // dump($request);

        }
        return $this->fetch();
    }

    /**
     * [logOne 单个日志列表]
     */
    public function logOne($customer_id){
  
        if(request()->isAjax()){
            $request = input('request.');
   
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where = [];
            $where['customer_id'] = ['eq', $customer_id];
            /*if($request['search']){
                $where['sn'] = ['like', '%'.$request['search'].'%'];
              }*/

            $data['rows'] = db('customer_log')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('customer_log')->where($where)->whereor($whereor)->count();

            $ur = model('Users')->getAll(); //获取全部用户
            $ur = array_column($ur, 'username', 'uid');

            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['update_userid'] = $ur[$v['update_userid']];
            }
            return $data;

           // dump($request);

        }
        $this->assign('customer_id', $customer_id);
        return $this->fetch();
    }

    /**
     * [follow 客户跟进列表]
     */
    public function follow(){

        if(request()->isAjax()){
            $request = input('request.');

            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where = [];
            if($request['status']){
                $where['status'] = ['eq', $request['status']];
            }
            if($request['userid']){
                $where['userid'] = ['eq', $request['userid']];
            }
            if($request['update_userid']){
                $where['update_userid'] = ['eq', $request['update_userid']];
            }
            if($request['url']){
                $where['url'] = ['like', '%'.$request['url'].'%'];
            }
            if($request['title']){
                $where['title'] = ['like', $request['title'].'%'];
            }
            if($request['add_time']){
                $start = strtotime($request['add_time']);
                $end = strtotime($request['add_time'])+3600*24;
                $where['add_time'] = ['between',[$start, $end]];
            }
            if($request['update_time']){
                $start = strtotime($request['update_time']);
                $end = strtotime($request['update_time'])+3600*24;
                $where['update_time'] = ['between',[$start, $end]];
            }
            if($request['next_time']){
                $start = strtotime($request['next_time']);
                $end = strtotime($request['next_time'])+3600*24;
                $where['next_time'] = ['between',[$start, $end]];
            }


            $data['rows'] = db('customer_link')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('customer_link')->where($where)->whereor($whereor)->count();

            $link_status = $this->link_status;
            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['update_time'] = formatTime($v['update_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['next_time'] = formatTime($v['next_time'], 'Y-m-d');
                $data['rows'][$k]['update_userid'] = get_admin_name($v['update_userid']);
                $data['rows'][$k]['userid'] = get_admin_name($v['userid']);

                $data['rows'][$k]['status'] = $link_status[$v['status']];
            }
            return $data;

        }
        $ur = model('Users')->getAll(); //获取全部用户
        $ur = array_column($ur, 'username', 'uid');
        $this->assign('ur', $ur);

        //dump($ur);die;


        return $this->fetch();
    }

    /**
     * [followOne 单个客户跟进列表]
     */
    public function followOne(){
        $customer_id = input('customer_id/d');
        $this->assign('customer_id', $customer_id);
        //$this->assign('id', $customer_id);

        $add_url = url('followAdd',['customer_id'=> $customer_id]);
        $this->assign('add_url', $add_url);

        if(request()->isAjax()){
            $request = input('request.');
            $offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where['customer_id'] = $customer_id;
            if($request['userid']){
                $where['userid'] = ['eq', $request['userid']];
            }
            if($request['search']){
                $where['remark'] = ['like', '%'.$request['search'].'%'];
            }


            $data['rows'] = db('customer_link')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('customer_link')->where($where)->whereor($whereor)->count();


            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['next_time'] = formatTime($v['next_time'], 'Y-m-d');
                $data['rows'][$k]['userid'] = get_admin_name($v['userid']);
            }
            return $data;

        }
        return $this->fetch();
    }

    /**
     * [followAdd 单个推广账号外链添加]
     */
    public function followAdd(){

        $customer_id = input('customer_id/d');
        $this->assign('customer_id', $customer_id);
        $id = input('id/d');
        if($id){
            $map['id'] = $id; 
            $data = db('customer_link')->where($map)->find();
            $this->assign('data', $data);
        }

        if(request()->isAjax()){
            $request = input('request.');

            $new_data = [
                'status' => $request['status'],
                'next_time' => strtotime($request['next_time']),
                'title' => $request['title'],
                'url' => $request['url'],
                'remark' => $request['remark'],
                'userid' => session('uid'),
                'add_time' => time(),
                'customer_id' => $request['customer_id']
            ];

            if($id){
                $update_data = [
                    'status' => $request['status'],
                    'next_time' => strtotime($request['next_time']),
                    'title' => $request['title'],
                    'url' => $request['url'],
                    'remark' => $request['remark'],
                    'update_userid' => session('uid'),
                    'update_time' => time(),
                ];
                $where['id'] = $id;
                $result = db('customer_link')->where($where)->update($update_data);
            }
            else{
                $result = db('customer_link')->insert($new_data);
            }

            if($result){
                $this->success('提交成功！');
            }
            else{
                $this->error('提交失败！');
            }
            /*$offset = $request['offset'];
            $limit = $request['limit']? $request['limit']: 20;
            $order = $request['order'];
            $sort = $request['sort'];
            $where = [];
            if($request['userid']){
                $where['userid'] = ['eq', $request['userid']];
            }
            if($request['search']){
                $where['remark'] = ['like', '%'.$request['search'].'%'];
            }


            $data['rows'] = db('customer_link')->where($where)->whereor($whereor)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('customer_link')->where($where)->whereor($whereor)->count();


            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = formatTime($v['add_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['next_time'] = formatTime($v['next_time'], 'Y-m-d H:i:s');
                $data['rows'][$k]['userid'] = get_admin_name($v['userid']);
            }
            return $data;*/

            
        }
        return $this->fetch();
    }
    
    /**
     * [deleteLog 删除日志]
     * @return [type] [description]
     */
    public function deleteLog(){
        if( request()->isPost() ){

            $request = input('request.');
            $ids = $request['id'];
            $id =explode(',', $ids);
            $id = array_filter($id);


            $where['id'] = ['in', $id];
            $data['is_delete'] = 1;
            $result = db('customer_log')->where($where)->update($data);


            if( $result ){
                $this->success(config('MSG.DELETE_SUCCESS'));
            }else{
                $this->success(config('MSG.DELETE_ERROR'));
            }
        }
    }

    /**
     * [moreAdd 批量导入添加]
     */
    public function moreAdd(){
        return $this->fetch();
    } 

    /**
     * [upExcel 上传Excel文件批量导入添加]
     */
    public function upExcel(){
        
        $filename = htmlspecialchars($_POST['Filename'], ENT_QUOTES); 
        $path = 'test';
        $file = request()->file('Filedata');

        $file_dir_path = REAL_PATH.'public' . DS . $path . DS;

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate([ 'size' => 20000000, 'ext' => 'xls,xlsx,txt' ])->move($file_dir_path);
           
        if ($info) { 

            $result = action('PHPExcel/read',array($file_dir_path, $info->getSaveName(), '0', '2') );

            if ($result['status']) {
                $data = $result['info'];
                $ar = array();
                foreach ($data as $key => $value) {
                    $data = array(
                        'sn' => $value['A'],
                        );
                    array_push($ar, $data);
                }
                
            }
        }
        if(!empty($ar)){
             $result = db('customer')->insertAll($ar);
        }
        if($result){
            $return = array('status'=>1, 'info'=>'添加成功');
        }
        else{
            $return = array('status'=>0, 'info'=>'添加失败，请重新上传！');
        }
        
        return json_encode($return);
    } 

    
    
    
}