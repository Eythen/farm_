<?php
/**
      
 * Date: 2015-09-09
 */
namespace app\home\controller;
use app\wap\model\Adopt as AdoptModel;
use Think\Db;

class Adopt extends Base { 
    /**
     * [piglist 花猪列表]
     * @return [type] [description]
     */
    public function piglist(){
        if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('is_on_sale')) ;             
            if($str){
                $where['is_on_sale'] = input('is_on_sale') ;
            }
              
            // 关键词搜索               
            $goods_name = input('key_word') ? trim(input('key_word')) : '';
            if($goods_name)
            {
                $where['goods_name'] = ['like', "%$goods_name%" ] ;
            }
            //认养状态
            $str = strlen(input('user_id')) ;             
            if($str){
                if(input('user_id') == 1){
                    $where['user_id'] = ['>', 0];
                }
                else{
                    $where['user_id'] = 0;
                }
            }
            //认养人
            $user_name = input('user_name') ? trim(input('user_name')) : '';
            if($user_name)
            {
                $where['user_name'] = ['like', "%$user_name%" ] ;
            }

            //出栏时间
            $out_time = input('out_time') ? trim(input('out_time')) : '';
            if($out_time)
            {
                $out_time = explode('-', $out_time);
                $start_time = strtotime(trim($out_time[0]));
                $end_time = strtotime('+1 day', strtotime(trim($out_time[1])));
                $where['out_time'] = ['between', [$start_time, $end_time] ] ;
            }
            

            $page = input('page')?input('page'):1;
            $limit = input('limit')?input('limit'):10;
            $offset = input('offset')?input('offset'):0;

            $model = new AdoptModel();
            $data = $model->piglist($where, $offset, $limit, 1);
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['last_update_uid'] = get_admin_name($v['last_update_uid']);
                $data['rows'][$k]['last_update'] = date('Y-m-d H:i:s', $v['last_update']);
                $data['rows'][$k]['out_time'] = date('Y-m-d', $v['out_time']);
                $data['rows'][$k]['adopt_time'] = formatTime($v['adopt_time'], 'Y-m-d');
            }
            return $data;

        }     
      
        return $this->fetch();
    }

     /**
     * [loglist 花猪成长信息列表]
     * @return [type] [description]
     */
    public function loglist(){
        if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('is_on_sale')) ;             
            if($str){
                $where['is_on_sale'] = input('is_on_sale') ;
            }
              
            // 关键词搜索               
            $goods_name = input('key_word') ? trim(input('key_word')) : '';
            if($goods_name)
            {
                $where['goods_name'] = ['like', "%$goods_name%" ] ;
            }
            
            // 花猪搜索               
            $pig_id = input('pig_id') ? trim(input('pig_id/d')) : '';
            if($pig_id){
                $where['pig_id'] = $pig_id ;
            }
            else{
                $this->error('请选择相应的花猪');
            }
            

            $page = input('page')?input('page'):1;
            $limit = input('limit')?input('limit'):10;
            $offset = input('offset')?input('offset'):0;

            $model = new AdoptModel();
            $data = $model->loglist($where, $offset, $limit, 1);
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['uid'] = get_admin_name($v['uid']);
                $data['rows'][$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            }
            return $data;

        }     
      
        return $this->fetch();
    }


    /**
     * 添加修改花猪
     */
    public function addEditPig(){
        $info['out_time'] = strtotime('+ 90 days');
        if(input('pig_id')){
            $pig = db('pig')->where('pig_id', input('pig_id'))->find(); 
            if($pig['out_time']){
                $info['out_time'] = $pig['out_time'];
            }
        }
        $this->assign(['pigInfo'=> $pig, 'info' => $info]);
        //ajax提交
        if (request()->isPost()) {
            $request = input('request.');
            //halt($request);
            if($request['pig_id']){
                $data = [
                    'pig_name' => $request['pig_name'],
                    'pig_remark' => $request['pig_remark'],
                    'pig_price' => $request['pig_price'],
                    'original_img' => $request['original_img'],
                    'out_time' => strtotime($request['out_time']),
                    'pig_id' => $request['pig_id'],
                    'last_update_uid' => session('uid'),
                    'last_update' => time()
                ];
                $result = db('pig')->where('pig_id', $request['pig_id'])->update($data);
            }
            else{
                $time = time();
                $data = [
                    'pig_name' => $request['pig_name'],
                    'pig_remark' => $request['pig_remark'],
                    'pig_price' => $request['pig_price'],
                    'original_img' => $request['original_img'],
                    'out_time' => strtotime($request['out_time']),
                    'pig_id' => $request['pig_id'],
                    'uid' => session('uid'),
                    'add_time' => $time,
                    'last_update_uid' => session('uid'),
                    'last_update' => $time
                ];
                $result = db('pig')->insert($data);
            }
            if($result){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');

            }
            
        }
        return $this->fetch('_pig');
    }  
        
    /**
     * 删除花猪
     */
    public function delPig()
    {
		$pig_id = input('param.id');
        $ids = explode(',', $pig_id);
        $ids = array_filter($ids);

        foreach ($ids as $k => $pig_id) {
    
            $error = '';
            
            // 判断此花猪是否有订单
            $c1 = db('pig_order_pigs')->where("pig_id = $pig_id")->count('1');
            $c1 && $error .= $pig_id.'此花猪有订单,不得删除! <br/>';
          
            
            if($error)
            {
                $return_arr = array('status' => -1,'msg' =>$error,'data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
                return $return_arr;           
            }
            // 删除此商品        
            db("pig")->where('pig_id ='.$pig_id)->delete();  //商品表
            //db("pig_cart")->where('pig_id ='.$pig_id)->delete();  // 购物车
                     
                     
        }

        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
        return $return_arr;
    }

    /**
     * 添加修改成长记录
     */
    public function addEditLog(){
        $info['pig_id'] = input('pig_id/d');
        if(!$info['pig_id']){
            $this->error('请先选择花猪');
        }
        if(input('log_id')){
            $log = db('pig_log')->where('log_id', input('log_id'))->find(); 
            if($log['pig_id']){
                $info['pig_id'] = $log['pig_id'];
            }
        }
        //健康分类
        $health = db('pig_health')->column('name');
        $vaccine = db('pig_vaccine')->column('name');
        //疫苗分类
        $this->assign([
            'data'=> $log, 
            'info' => $info,
            'health' => $health,
            'vaccine' => $vaccine,
            ]);
        //ajax提交
        if (request()->isPost()) {
            $request = input('request.');
            //halt($request);
            if($request['log_id']){
                $data = [
                    'pig_vaccine' => $request['pig_vaccine'],
                    'pig_health' => $request['pig_health'],
                    'pig_long' => $request['pig_long'],
                    'pig_round' => $request['pig_round'],
                    'pig_id' => $request['pig_id'],
                    'pig_weight' => $request['pig_weight'],
                ];
                $r = db('pig')->where('pig_id', $request['pig_id'])->update($data);
                $result = db('pig_log')->where('log_id', $request['log_id'])->update($data);
            }
            else{
                $time = time();
                $data2 = [
                    'pig_vaccine' => $request['pig_vaccine'],
                    'pig_health' => $request['pig_health'],
                    'pig_long' => $request['pig_long'],
                    'pig_round' => $request['pig_round'],
                    'pig_id' => $request['pig_id'],
                    'pig_weight' => $request['pig_weight'],
                ];
                $r = db('pig')->where('pig_id', $request['pig_id'])->update($data2);
                $data = [
                    'pig_vaccine' => $request['pig_vaccine'],
                    'pig_health' => $request['pig_health'],
                    'pig_long' => $request['pig_long'],
                    'pig_round' => $request['pig_round'],
                    'pig_id' => $request['pig_id'],
                    'pig_weight' => $request['pig_weight'],
                    'at_time' => strtotime($request['at_time']),
                    'uid' => session('uid'),
                    'add_time' => $time,
                ];
                $result = db('pig_log')->insert($data);


            }
            if($result){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');

            }
            
        }
        return $this->fetch('_log');
    }  

    
    /**
     * 初始化编辑器链接     
     * 本编辑器参考 地址 http://fex.baidu.com/ueditor/
     */
    private function initEditor()
    {
        $this->assign("URL_upload", url('home/Ueditor/imageUp',array('savepath'=>'goods'))); // 图片上传目录
        $this->assign("URL_imageUp", url('home/Ueditor/imageUp',array('savepath'=>'article'))); //  不知道啥图片
        $this->assign("URL_fileUp", url('home/Ueditor/fileUp',array('savepath'=>'article'))); // 文件上传s
        $this->assign("URL_scrawlUp", url('home/Ueditor/scrawlUp',array('savepath'=>'article')));  //  图片流
        $this->assign("URL_getRemoteImage", url('home/Ueditor/getRemoteImage',array('savepath'=>'article'))); // 远程图片管理
        $this->assign("URL_imageManager", url('home/Ueditor/imageManager',array('savepath'=>'article'))); // 图片管理        
        $this->assign("URL_getMovie", url('home/Ueditor/getMovie',array('savepath'=>'article'))); // 视频上传
        $this->assign("URL_home", "");
    }    
    
    
    
    /**
     * [orderlist 订单列表]
     * @return [type] [description]
     */
    public function orderlist(){
        if(request()->isAjax()){

            $where = []; // 搜索条件  
            $str = strlen(input('mobile')) ;             
            if($str){
                $where['mobile'] = input('mobile') ;
            }
              
            // 关键词搜索               
            $name = input('key_word') ? trim(input('key_word')) : '';
            if($name)
            {
                $where['name'] = ['like', "%$name%" ] ;
            }

            $pay_status = input('pay_status') ? trim(input('pay_status')) : '';
            if(is_numeric($pay_status)){
                $where['pay_status'] = $pay_status;
            }
            //订单状态
            $order_status = input('order_status') ? trim(input('order_status')) : '';
            if(is_numeric($order_status)){
                $where['order_status'] = $order_status;
            }
            //订单提取方式
            $order_consign = input('order_consign') ? trim(input('order_consign')) : '';
            if(is_numeric($order_consign)){
                $where['consign'] = $order_consign;
            }
            //下单时间
            $add_time = input('add_time') ? trim(input('add_time')) : '';
            if($add_time)
            {
                $add_time = explode('-', $add_time);
                $start_time = strtotime(trim($add_time[0]));
                $end_time = strtotime('+1 day', strtotime(trim($add_time[1])));
                $where['add_time'] = ['between', [$start_time, $end_time] ] ;
            }
  
           

            $page = input('num')?input('num'):1;
            $limit = input('limit')?input('limit'):10;
            $offset = ($page-1) * $limit;
  

            $model = new AdoptModel();
            $data = $model->orderlist($where, $offset, $limit, 1);
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $data['rows'][$k]['order_status'] =  config('pig_order_status')[$v['order_status']];
                $data['rows'][$k]['pay_status'] =  config('pig_order_pay_status')[$v['pay_status']];
            }

            return $data;
        }  

        $pay_status = config('pig_order_pay_status');
        $order_status = config('pig_order_status');
        $order_consign = config('pig_order_consign');
        $this->assign([
            'pay_status' => $pay_status,
            'order_status' => $order_status,
            'order_consign' => $order_consign,
            ]);
        return $this->fetch();
    }

    //订单详情
    public function editOrder(){
        $act = input('act');
        if($act == 'edit'){
            $template = 'detail';
        }
        if($act == 'view'){
            $template = 'detail';
        }
        if($act == 'success'){
            $template = 'detail';
        }
        if($act == 'refund'){
            $template = 'detail';
        }
        if($act == 'print'){
            $template = 'print';
        }
        $order_id = input('order_id/d');
        if(!$order_id){
            $this->error('请选择订单');
        }
        $map['order_id'] = $order_id;
        $model = new AdoptModel();
        $order = $model->orderDetail($map);
        if($order['pay_status'] ==1){
            $order['pay_money'] = $order['order_amount'];
            $order['pay_money_spare'] = 0;
        }
        elseif($order['pay_status'] ==2){
            $order['pay_money'] = $order['amount20'];
            $order['pay_money_spare'] = $order['amount80'];
        }
        else{
            $order['pay_money'] = 0;
            $order['pay_money_spare'] = $order['order_amount'];
        }

        //取操作日志
        $action_log = db('pig_order_action')->where('order_id', $order_id)->order('action_id desc')->select();
        foreach ($action_log as $k => $v) {
            $action_log[$k]['action_user'] = get_admin_name($v['action_user']);
        }

        //取快递信息
        $logistics = model('wap/Adopt')->logistics($order_id);

        $order_status = config('pig_order_status');
        $pay_status = config('pig_order_pay_status');
        $order_consign = config('pig_order_consign');

        $this->assign([
            'order' => $order,
            'order_status' => $order_status,
            'pay_status' => $pay_status,
            'order_consign' => $order_consign,
            'act' => $act,
            'action_log' => $action_log,
            'logistics' => $logistics,
            ]);

        return $this->fetch($template);
    }

    /**
     * [orderActionLog 操作日志]
     * @param  [type] $order_id     [订单ID]
     * @param  string $order_status [订单状态]
     * @param  string $pay_status   [订单支付状态]
     * @param  string $action_note  [操作留言]
     * @return [type]               [description]
     */
    public function orderActionLog($order_id , $order_status='', $pay_status='', $action_note=''){
        if($order_status){
             $data['order_status'] = $order_status;
        }
        if($pay_status){
             $data['pay_status'] = $pay_status;
        }
        if($action_note){
             $data['action_note'] = $action_note;
        }
        $data['order_id'] = $order_id;
        $data['log_time'] = time();
        $data['action_user'] = session('uid');

        db('pig_order_action')->insert($data);    

    }

    //操作
    public function orderAction(){
        $act = input('act');
        $order_id = input('order_id/d');
        if($act == 'edit'){
            $template = 'detail';
        }
        if($act == 'view'){
            $template = 'detail';
        }
        if($act == 'success'){
            $order_status = input('order_status');
            if(input('note/s')){
                $action_note = input('note/s');
            }
            $data = [
                'order_status' => $order_status,
                ];
            //订单无效时要设置花猪上线
            if($order_status == 3){
                $ids = db('pig_order_pigs')->where('order_id', $order_id)->column('pig_id');
                $idsmap['pig_id'] = ['in', $ids];
                db('pig')->where($idsmap)->setField('user_id', 0);
            }    
            $r = db('pig_order')->where('order_id', $order_id)->update($data);
            //dump(db()->getLastSql());
            //记录日志
            $this->orderActionLog($order_id, $order_status, '', "$action_note");
            if($r !== false){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');
            }
        }
        if($act == 'refund'){
            $order_status = 5;//退款完成
            $action_note = input('note/s');
            $data = [
                'order_id' => $order_id,
                'order_status' => $order_status,
                ];
            $r = db('pig_order')->where('order_id', $order_id)->update($data);

            $this->orderActionLog($order_id, $order_status, '', "$action_note");
            if($r){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');
            }

        }
        
    }
    
    //发货
    public function orderShipping(){
        $map['order_id'] = input('order_id/d');
        $model = new AdoptModel();
        $order = $model->orderDetail($map);
        if(request()->isAjax()){
            $post = input('post.');
            $act = input('act');
            if(($act == 'del') && input('logistics_id/d')){
                $r = db('pig_order_logistics')->where('logistics_id', input('logistics_id/d'))->delete();
            }
            else{
                $shipping_sn = $post['shipping_sn'];
                $shipping_sn = str_replace('，', ',', $shipping_sn); //中文逗号转成英文

                $shipping_sn = explode(',', $shipping_sn);
                $shipping_sn = array_filter($shipping_sn);//过滤空的

                foreach ($shipping_sn as $k => $v) {
                    $kuaidi_name = model('Kuaidi')->getName($post['shipping_name']);
                    $data[$k] = [
                        'code' => $post['shipping_name'],
                        'sn' => $v,
                        'name' => $kuaidi_name,
                        'user_id' => session('uid'),
                        'user_name' => get_admin_name(session('uid')),
                        'add_time' => time(),
                        'order_id' => input('order_id/d'),
                        ];
                }
                //发送消息
                $url = url('wap/Adopt/logistics',['order_id' => input('order_id/d')]);
                model('home/Article')->addExpress($url, $kuaidi_name, $post['shipping_sn'], $order['user_id']);
                //更新订单状态
                db('pig_order')->where('order_id', input('order_id/d'))->setField('order_status', 4);
                $r = db('pig_order_logistics')->insertAll($data);
                //写日志
                $this->orderActionLog(input('order_id/d'), '4', '', "发货".$kuaidi_name."：".$post['shipping_sn']);

                
            }

            if($r){
                $this->success('操作成功');
            }
            else{
                $this->error('操作失败');
            }
        }

        $kuaidi = model('Kuaidi');
        $kuaidi_cat = $kuaidi->getCat(['status'=>1]);



        $this->assign([
            'order' => $order,
            'kuaidi_cat' => $kuaidi_cat,
            ]);
        return view();
    }
    
    //健康分类
    public function addTypeHealth(){
        $health_id = input('health_id/d');
        if($health_id){
            $data = db('pig_health')->where('health_id', $health_id)->find();
            $this->assign('data', $data);
        }

        if(request()->isAjax()){
            $data['name'] = input('pig_health');
            $data['remark'] = input('remark');
            $data['add_time'] = time();
            $data['user_id'] = session('user_id');
            if($health_id){
                $r = db('pig_health')->where('health_id', $health_id)->update($data);
            }
            else{
                $r = db('pig_health')->insert($data);
            }
            if($r){
                $this->success('提交成功');
            }
            else{
                $this->success('提交失败');
            }
        }

        return view();
    }
    
    //疫苗分类
    public function addTypeVaccine(){
        $vaccine_id = input('vaccine_id/d');
        if($vaccine_id){
            $data = db('pig_vaccine')->where('vaccine_id', $vaccine_id)->find();
            $this->assign('data', $data);
        }

        if(request()->isAjax()){
            $data['name'] = input('pig_vaccine');
            $data['remark'] = input('remark');
            $data['add_time'] = time();
            $data['user_id'] = session('user_id');
            if($vaccine_id){
                $r = db('pig_vaccine')->where('vaccine_id', $vaccine_id)->update($data);
            }
            else{
                $r = db('pig_vaccine')->insert($data);
            }
            if($r){
                $this->success('提交成功');
            }
            else{
                $this->success('提交失败');
            }
        }
        return view();
    }
}