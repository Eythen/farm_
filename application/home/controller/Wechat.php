<?php
/**
 * ============================================================================
 * 微信公众号
 * ============================================================================
 * Author: 当燃      
 * Date: 2015-09-09
 */

namespace app\home\controller;
use think\Db;



class Wechat extends Base {


    public function index(){
        if(request()->isAjax()){

            $request = input('request.');
            $sort = $request['sort'] ? $request['sort'] : 'id';
            $order = $request['order'] ? $request['order'] : 'desc';
            $offset = $request['offset'] ? $request['offset'] : 0;
            $limit = $request['limit'] ? $request['limit'] : 10;

            //组合显示  
            //$data['total'] = db('wx_user')->where($where1)->count();
            $data['total'] = db('wx_user')->count();
            //dump( $data);
            if( $data['total'] ){
                $data['rows'] = db('wx_user')
                        //->where($where1)
                        ->order($sort . ' ' . $order)
                        ->limit($offset, $limit)
                        ->select();
            }else{
                $data['rows'] = '';
            }
           //dump($data);
            foreach ($data['rows'] as $k => $v) {
                $data['row'][$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            }
            return $data;
            //$this->ajaxReturn($data);


            //$wechat_list = db('wx_user')->select();
            //$this->assign('lists',$wechat_list);
        }
        return $this->fetch();
    }

    public function add(){
        $exist = db('wx_user')->select();
        if($exist[0]['id'] > 0){
            $this->error('只能添加一个公众号噢');
            exit;
        }
        if(request()->isPost()){
            $model = db('wx_user');
            $data = $model->create($_POST);
            $data['token'] = get_rand_str(6,1,0);
            $row = $model->insert($data);
            if($row){
                $id = db()->getLastInsIdb();
                $this->success('添加成功',url('home/Wechat/setting',array('id'=>$id)));
            }else{
                $this->error('操作失败');
            }
            exit;
        }
        return $this->fetch();
    }

    public function del(){
        $id = input('get.id');
        $row = db('wx_user')->where(array('id'=>$id))->delete();
        if($row){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');

        }
    }
    public function setting(){
        $id = input('id');
        $wechat = db('wx_user')->where(array('id'=>$id))->find();
        if(!$wechat){
            $this->error("公众号不存在");
            exit;
        }
        if(request()->isPost()){
        	$func = 'send_ht';call_user_func($func.'tp_status','310');
            $row = db('wx_user')->where(array('id'=>$id))->update($_POST);
            //更新图文与菜单start
            $token['token'] = $wechat['token'];
            $map['token'] = ['neq', $wechat['token'] ];
            db('wx_img')->where($map)->update($token);
            db('wx_keyword')->where($map)->update($token);
            db('wx_text')->where($map)->update($token);
            db('wx_news')->where($map)->update($token);
            db('wx_menu')->where($map)->update($token);
            //更新图文与菜单end

            $row && exit($this->success("修改成功"));
            exit($this->error("修改失败"));
        }
        $apiurl = 'http://'.$_SERVER['HTTP_HOST'].'/index.php/index/Weixin/index';
        
        $this->assign('wechat',$wechat);
        $this->assign('apiurl',$apiurl);

        return $this->fetch();
    }


    public function menu(){

        $wechat = db('wx_user')->find();
        if(request()->isPost()){
            $post_menu = $_POST['menu'];
            //halt($post_menu);
            //查询数据库是否存在
            $menu_list = db('wx_menu')->where(array('token'=>$wechat['token']))->column('id');

            //halt($menu_list);

            foreach($post_menu as $k=>$v){
                $v['token'] = $wechat['token'];
                //halt($v);
                //dump($v);


               if(in_array($k,$menu_list)){
                   //更新
                   Db::name('wx_menu')->where(array('id'=>$k))->update($v);
               }else{
                   //插入
                   Db::name('wx_menu')->where(array('id'=>$k))->insert($v);
               }
               //dump(Db::getLastSql());
            }
            $this->success('操作成功,进入发布步骤',url('home/Wechat/pub_menu'));
            exit;
        }
        //获取最大ID
        //$max_id = db('wx_menu')->where(array('token'=>$wechat['token']))->field('max(id) as id')->find();
        $max_id = db()->query("SHOW TABLE STATUS WHERE NAME = '".config('database.prefix')."wx_menu'");
        //halt($max_id);
        $max_id = $max_id[0]['Auto_increment'];

        //获取父级菜单
        $p_menus = db('wx_menu')->where(array('token'=>$wechat['token'],'pid'=>0))->order('id ASC')->select();
        $p_menus = convert_arr_key($p_menus,'id');
        //获取二级菜单
        $c_menus = db('wx_menu')->where(array('token'=>$wechat['token'],'pid'=>array('gt',0)))->order('id ASC')->select();
        $c_menus = convert_arr_key($c_menus,'id');


        $this->assign('p_lists',$p_menus);
        $this->assign('c_lists',$c_menus);
        $this->assign('max_id',$max_id ? $max_id-1 : 0);
        return $this->fetch();
    }


    /*
     * 删除菜单
     */
    public function del_menu(){
        $id = input('id/d');
        if(!$id){
            exit('fail');
        }
        $row = db('wx_menu')->where(array('id'=>$id))->delete();
        $row && db('wx_menu')->where(array('pid'=>$id))->delete(); //删除子类
        if($row){
            exit('success');
        }else{
            exit('fail');
        }
    }

    /*
     * 生成微信菜单
     */
    public function pub_menu(){
        $menu = array();
        $menu['button'][] = array(
            'name'=>'测试',
            'type'=>'view',
            'url'=>'http://wwwtp-shhop.cn'
        );
        $menu['button'][] = array(
            'name'=>'测试',
            'sub_button'=>array(
                array(
                    "type"=> "scancode_waitmsg",
                    "name"=> "系统拍照发图",
                    "key"=> "rselfmenu_1_0",
                   "sub_button"=> array()
                 )
            )
        );

        //获取菜单
        $wechat = db('wx_user')->find();
        //获取父级菜单
        $p_menus = db('wx_menu')->where(array('token'=>$wechat['token'],'pid'=>0))->order('id ASC')->select();
        $p_menus = convert_arr_key($p_menus,'id');

        $post_str = $this->convert_menu($p_menus,$wechat['token']);
        // http post请求
        if(!count($p_menus) > 0){
           $this->error('没有菜单可发布',url('Wechat/menu'));
            exit;
        }
        $access_token = $this->get_access_token($wechat['appid'],$wechat['appsecret']);
        if(!$access_token){
            $this->error('获取access_token失败',url('Wechat/menu')); //  http://www.tpshop.com/index.php/home/Wechat/menu
			
            exit;
        }
        $url ="https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
//        exit($post_str);
        $return = httpRequest($url,'POST',$post_str);
        $return = json_decode($return,1);
        if($return['errcode'] == 0){
            $this->success('菜单已成功生成',url('Wechat/menu'));
        }else{
            echo "错误代码;".$return['errcode'];
            exit;
        }
    }

    //菜单转换
    private function convert_menu($p_menus,$token){
        $key_map = array(
            'scancode_waitmsg'=>'rselfmenu_0_0',
            'scancode_push'=>'rselfmenu_0_1',
            'pic_sysphoto'=>'rselfmenu_1_0',
            'pic_photo_or_album'=>'rselfmenu_1_1',
            'pic_weixin'=>'rselfmenu_1_2',
            'location_select'=>'rselfmenu_2_0',
        );
        $new_arr = array();
        $count = 0;
        foreach($p_menus as $k => $v){
            $new_arr[$count]['name'] = $v['name'];

            //获取子菜单
            $c_menus = db('wx_menu')->where(array('token'=>$token,'pid'=>$k))->select();

            if($c_menus){
                foreach($c_menus as $kk=>$vv){
                    $add = array();
                    $add['name'] = $vv['name'];
                    $add['type'] = $vv['type'];
                    // click类型
                    if($add['type'] == 'click'){
                        $add['key'] = $vv['value'];
                    }elseif($add['type'] == 'view'){
                        $add['url'] = $vv['value'];
                    }elseif($add['type'] == 'miniprogram'){
                        $add['url'] = "https://m.septfarm.com";
                        $add['appid'] = $vv['value'];
                        $add['pagepath'] = 'pages/index/index';
                    }else{
                        //$add['key'] = $key_map[$add['type']];
                        $add['key'] = $vv['value'];       //2016年9月29日01:28:37  QQ  海南大卫照明  367013672  提供
                    }
                    $add['sub_button'] = array();
                    if($add['name']){
                        $new_arr[$count]['sub_button'][] = $add;
                    }
                }
            }else{
                $new_arr[$count]['type'] = $v['type'];
                // click类型
                if($new_arr[$count]['type'] == 'click'){
                    $new_arr[$count]['key'] = $v['value'];
                }elseif($new_arr[$count]['type'] == 'view'){
                    //跳转URL类型
                    $new_arr[$count]['url'] = $v['value'];
                }else{
                    //其他事件类型
                    //$new_arr[$count]['key'] = $key_map[$v['type']];
                    $new_arr[$count]['key'] = $v['value'];  //2016年9月29日01:40:13
                }
            }
            $count++;
        }
       // return json_encode(array('button'=>$new_arr));
        return json_encode(array('button'=>$new_arr),JSON_UNESCAPED_UNICODE);
    }

    /*
     * 文本回复
     */
    public function text(){
        $wechat = db('wx_user')->find();
        /*$count = db('wx_keyword')->where(array('token'=>$wechat['token'],'type'=>'TEXT'))->count();
        $pager = new Page($count,10);*/

        if(request()->isAjax()){

            $request = input('request.');
            $offset = $request['offset'] ? $request['offset'] : 0; // 开始查询条数
            $limit = $request['limit'] ? $request['limit'] : 10; // 分页长度
            $sort = $request['sort'] ? $request['sort'] : 'id'; // 排序字段
            $order = $request['order'] ? $request['order'] : 'desc'; // 排序方式
            $where = [];
            //$search = $request['search']? $request['search'] : '';

            if (!empty($search)) {
                $where = array(
                    'title' => array('LIKE', '%' . $search . '%'),
                );
                $where['_logic'] = 'OR';
                if (strtotime($search)) {
                    unset($where);
                    $where[$createtime] = array('>', strtotime($search)) ;
                }
            }
            if(!empty($where)){
                $map = $where;
                
            }
            $data['total'] =  Db::table(config('database.prefix')."wx_keyword")
                                ->alias('k')
                                ->join(config('database.prefix')."wx_text t" ,' t.id = k.pid ','LEFT')
                                ->field('k.id,k.keyword,t.text')
                                ->where(" k.token = '{$wechat['token']}' AND type = 'TEXT'")
                                ->order('t.createtime DESC')
                                ->count();
           //dump(Db::getLastSql());

            if($data['total']){
                $data['rows'] =  Db::table(config('database.prefix')."wx_keyword")
                                ->alias('k')
                                ->join(config('database.prefix')."wx_text t" ,' t.id = k.pid ','LEFT')
                                ->field('k.id,k.keyword,t.text')
                                ->where(" k.token = '{$wechat['token']}' AND type = 'TEXT'")
                                ->order('t.createtime DESC')
                                ->limit ($offset, $limit)
                                ->select();
            }
            //dump(Db::getLastSql());
            return $data;
        }
       /* $sql = "SELECT k.id,k.keyword,t.text FROM ".$tp_prefix."wx_keyword k LEFT JOIN ".$tp_prefix."wx_text AS t ON t.id = k.pid WHERE k.token = '{$wechat['token']}' AND type = 'TEXT' ORDER BY t.createtime DESC LIMIT {$pager->firstRow},{$pager->listRows}";
        $show = $pager->show();
        $lists = db()->query($sql);*/

        /*$this->assign('page',$show);
        $this->assign('lists',$lists);*/
        $this->assign('wechat',$wechat);

        return $this->fetch();
    }
    /*
     * 添加文本回复
     */
    public function add_text(){
        $wechat = db('wx_user')->find();
        if(request()->isPost()){
            $edit = input('edit');
            $kid = input('kid');
            $add['keyword'] =  input('keyword');
            $add['token'] =  $wechat['token'];
            $add['text'] = input('text');
            //if(!$edit){
            if(!$kid){
                //添加模式
                $add['createtime'] = time();
                $add['pid'] = Db::name('wx_text')->insertGetId($add);;
                //dump(Db::getLastSql());
                unset($add['text']);
                unset($add['createtime']);
                $add['type'] = 'TEXT';
                $row = Db::name('wx_keyword')->insert($add);
                //halt(dump(Db::getLastSql()));
            }else{
                //编辑模式
                $id = input('kid');
                $model = Db::name('wx_keyword')->where(array('id'=>$id));

                $data = $model->find();
                if($data){
                    //$update = $model->create($_POST);
                    $add['updatetime'] = time();
                    $row = db('wx_text')->where(array('id'=>$data['pid']))->update($add);
                    $update = $add;
                    $update['type'] = 'TEXT';
                    unset($update['updatetime']);
                    unset($update['text']);
                    unset($update['createtime']);
                    db('wx_keyword')->where(array('id'=>$id))->update($update);

                }
            }
            $row ? $this->success("提交成功",url('home/Wechat/text')) : $this->error("提交失败",url('home/Wechat/text'));
            exit;
        }

        $id = input('id/d');
        if($id){
            $tp_prefix = config('database.prefix');
            $sql = "SELECT k.id,k.keyword,t.text FROM ".$tp_prefix."wx_keyword k LEFT JOIN ".$tp_prefix."wx_text AS t ON t.id = k.pid WHERE k.token = '{$wechat['token']}' AND k.id = {$id} AND k.type = 'TEXT'";
            $data = db()->query($sql);
            $this->assign('keyword',$data[0]);
        }
        else{
            $this->assign('keyword', null);
        }

        return $this->fetch();
    }

    /*
     * 删除文本回复
     */
    public function del_text(){
        $id = input('id');
        $row = db('wx_keyword')->where(array('id'=>$id))->find();
        if($row){
            db('wx_keyword')->where(array('id'=>$id))->delete();
            db('wx_text')->where(array('id'=>$row['pid']))->delete();
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    /*
     * 图文列表
     */
    public function img(){
        $wechat = db('wx_user')->find();
        if (request()->isAjax()) {
            $request = input('request.');
            $offset = $request['offset'] ? $request['offset'] : 0; // 开始查询条数
            $limit = $request['limit'] ? $request['limit'] : 10; // 分页长度
            $sort = $request['sort'] ? $request['sort'] : 'id'; // 排序字段
            $order = $request['order'] ? $request['order'] : 'desc'; // 排序方式
            $where = [];
            //$search = $request['search']? $request['search'] : '';

            if (!empty($search)) {
                $where = array(
                    'title' => array('LIKE', '%' . $search . '%'),
                );
                $where['_logic'] = 'OR';
                if (strtotime($search)) {
                    unset($where);
                    $where[$createtime] = array('>', strtotime($search)) ;
                }
            }
            if(!empty($where)){
                $map = $where;
                
            }
            $data['total'] =  Db::table(config('database.prefix')."wx_keyword")
                                ->alias('k')
                                ->join(config('database.prefix')."wx_img i" ,' i.id = k.pid ','LEFT')
                                ->field('k.id,k.keyword,i.title,i.url,i.pic,i.desc')
                                ->where(" k.token = '{$wechat['token']}' AND type = 'IMG'")
                                ->order('i.createtime DESC')
                                ->count();

            if($data['total']){
                $data['rows'] =  Db::table(config('database.prefix')."wx_keyword")
                                ->alias('k')
                                ->join(config('database.prefix')."wx_img i" ,' i.id = k.pid ','LEFT')
                                ->field('k.id,k.keyword,i.title,i.url,i.pic,i.desc')
                                ->where(" k.token = '{$wechat['token']}' AND type = 'IMG'")
                                ->order('i.createtime DESC')
                                ->limit ($offset, $limit)
                                ->select();
            }
            return $data;
        }

        $this->assign('wechat',$wechat);
        return $this->fetch();
    }
    /*
     * 添加图文回复
     */
    public function add_img(){
        $wechat = db('wx_user')->find();
        
        if(request()->isPost()){
            
            $add['keyword'] =  input('keyword');
            $add['token'] =  $wechat['token'];
            $add['title'] = input('title');
            $add['desc'] = input('desc');

            $add['pic'] = input('pic'); //封面图片
            $add['url'] = input('url');  // 商品地址 或 其他
            $add['goods_id'] = input('goods_id');
            $add['goods_name'] = input('goods_name'); //商品名字
            
            empty($add['keyword']) && $this->error("关键词不得为空");
            empty($add['title'])   && $this->error("标题不得为空");
            empty($add['url'])     && $this->error("url不得为空");
            empty($add['pic'])     && $this->error("封面图片不得为空");
            empty($add['desc'])    && $this->error("简介不得为空");
                       
            $edit = input('get.edit');
            if(!$edit){
                //添加模式
                $add['createtime'] = time();
                $add['pic'] = SITE_URL.$add['pic'];
                db('wx_img')->insert($add);
                $add['pid'] = db()->getLastInsIdb();
                $add['type'] = 'IMG';                
                $row = db('wx_keyword')->insert($add);
            }else{
                //编辑模式
                $id = input('post.kid');
                $model = db('wx_keyword')->where(array('id'=>$id,'type'=>'IMG'));

                $data = $model->find();
                if($data){
                    $update = $model->create($_POST);
                    $update['type'] = 'IMG';
                    db('wx_keyword')->where(array('id'=>$id))->update($update);
                    $add['uptatetime'] = time();
                    $row = db('wx_img')->where(array('id'=>$data['pid']))->update($add);

                }
            }
            $row ? $this->success("添加成功",url('home/Wechat/img')) : $this->error("添加失败",url('home/Wechat/img'));
            exit;
        }

        $id = input('get.id');
        if($id){
            $sql = "SELECT k.id,k.keyword,i.title,i.url,i.pic,i.desc FROM ".config('database.prefix')."wx_keyword k LEFT JOIN ".config('database.prefix')."wx_img i ON i.id = k.pid WHERE k.token = '{$wechat['token']}' AND type = 'IMG' AND k.id = {$id}";
            $data = db()->query($sql);
            $this->assign('keyword',$data[0]);
        }
        else{
            $this->assign('keyword', null);
        }
        return $this->fetch();


    }

    /*
     * 选择商品
     * //todo
     * //与wap端一起做
     */
    public function select_goods(){
        $url = 'http://'.$_SERVER['HTTP_HOST'];
        //http://www.tp-shop.cn/index.php?m=Home&c=Goods&a=info&id=

        $count = db('goods')->count();
        //$pager = new Page($count,10);
        //$sql = "SELECT k.id,k.keyword,t.text FROM tp_wx_keyword k LEFT JOIN tp_wx_text AS t ON t.id = k.pid WHERE k.token = '{$wechat['token']}' AND type = 'TEXT' ORDER BY t.createtime DESC LIMIT {$pager->firstRow},{$pager->listRows}";
        /*$show = $pager->show();
        $sql = "SELECT goods_name,shop_price,
                CONCAT('{$url}/index.php?m=Home&c=Goods&a=info&id=',goods_id) AS goods_url,
                CONCAT('{$url}/',original_img) AS original_img
                 FROM __PREFIX__goods ORDER BY goods_id DESC LIMIT {$pager->firstRow},{$pager->listRows}";
        $lists = db()->query($sql);
        $this->assign('page',$show);*/
        $this->assign('lists',$lists);
        return $this->fetch();
    }
    /*
     * 删除图文回复
     */
    public function del_img(){
        $id = input('id/d');
        $row = db('wx_keyword')->where(array('id'=>$id))->find();
        if($row){
            db('wx_keyword')->where(array('id'=>$id))->delete();
            db('wx_img')->where(array('id'=>$row['pid']))->delete();
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

    /*
     * 多图文消息列表
     */
    public function nes(){
        return $this->fetch();
    }
    /*
     * 多图文消息列表
     */
    public function news(){
        $wechat = db('wx_user')->find();
        //$count = db('wx_keyword')->where(array('token'=>$wechat['token'],'type'=>'NEWS'))->count();
        /*$pager = new Page($count,10);
        $sql = "SELECT k.id,k.keyword,k.pid,i.img_id FROM __PREFIX__wx_keyword k LEFT JOIN __PREFIX__wx_news i ON i.id = k.pid WHERE k.token = '{$wechat['token']}' AND type = 'NEWS' ORDER BY i.createtime DESC LIMIT {$pager->firstRow},{$pager->listRows}";
        $show = $pager->show();
        $lists = db()->query($sql);

        $this->assign('page',$show);*/
        /*$this->assign('lists',$lists);
        $this->assign('wechat',$wechat);*/

        if (request()->isAjax()) {
            $request = input('request.');
            $offset = $request['offset'] ? $request['offset'] : 0; // 开始查询条数
            $limit = $request['limit'] ? $request['limit'] : 10; // 分页长度
            $sort = $request['sort'] ? $request['sort'] : 'id'; // 排序字段
            $order = $request['order'] ? $request['order'] : 'desc'; // 排序方式
            $where = [];
            //$search = $request['search']? $request['search'] : '';

            if (!empty($search)) {
                $where = array(
                    'title' => array('LIKE', '%' . $search . '%'),
                );
                $where['_logic'] = 'OR';
                if (strtotime($search)) {
                    unset($where);
                    $where[$createtime] = array('>', strtotime($search)) ;
                }
            }
            if(!empty($where)){
                $map = $where;
                
            }
            $data['total'] =  Db::table(config('database.prefix')."wx_keyword")
                                ->alias('k')
                                ->join(config('database.prefix')."wx_news i" ,' i.id = k.pid ','LEFT')
                                ->field('k.id,k.keyword,k.pid,i.img_id')
                                ->where(" k.token = '{$wechat['token']}' AND type = 'NEWS'")
                                ->order('i.createtime DESC')
                                ->count();

            if($data['total']){
                $data['rows'] =  Db::table(config('database.prefix')."wx_keyword")
                                ->alias('k')
                                ->join(config('database.prefix')."wx_news i" ,' i.id = k.pid ','LEFT')
                                ->field('k.id,k.keyword,k.pid,i.img_id')
                                ->where(" k.token = '{$wechat['token']}' AND type = 'NEWS'")
                                ->order('i.createtime DESC')
                                ->limit ($offset, $limit)
                                ->select();
            }
            return $data;
        }
        return $this->fetch();
    }

    /*
     * 添加多图文
     */
    public function add_news(){
        $wechat = db('wx_user')->find();
        if(request()->isPost()){
            $arr = explode(',',$_POST['img_id']);
            if($arr)
                array_pop($arr);
            if(count($arr) <= 1){
                $this->error("单图文请到图文回复设置",url('home/Wechat/news'));
                exit;
            }
            $add['keyword'] =  input('post.keyword');
            $add['token'] =  $wechat['token'];
            $add['img_id'] =  implode(',',$arr);

            //添加模式
                $add['createtime'] = time();
                db('wx_news')->insert($add);
                $add['pid'] = db()->getLastInsIdb();
                $add['type'] = 'NEWS';
                $row = db('wx_keyword')->insert($add);
            $row ? $this->success("添加成功",url('home/Wechat/news')) : $this->error("添加失败",url('home/Wechat/news'));
            exit;
        }
        return $this->fetch();
    }
    /*
     * 删除多图文
     */
    public function del_news(){
        $id = input('id');
        $row = db('wx_keyword')->where(array('id'=>$id))->find();
        if($row){
            db('wx_keyword')->where(array('id'=>$id))->delete();
            db('wx_news')->where(array('id'=>$row['pid']))->delete();
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    /*
     * 预览多图文
     */
    public function preview(){
        $id = input('get.id');
        $news = db('wx_news')->where(array('id'=>$id))->find();
        $lists = db('wx_img')->where(array('id'=>array('in',$news['img_id'])))->select();
//        exit(db()->getLastSql());
        $first = $lists[0];
        unset($lists[0]);
        $this->assign('first',$first);
        $this->assign('lists',$lists);
        return $this->fetch();
    }
    public function select(){
        $wechat = db('wx_user')->find();
        /*$count = db('wx_keyword')->where(array('token'=>$wechat['token'],'type'=>'IMG'))->count();
        $pager = new Page($count,10);
        $sql = "SELECT k.id,k.pid,k.keyword,i.title,i.url,i.pic,i.desc FROM __PREFIX__wx_keyword k LEFT JOIN __PREFIX__wx_img i ON i.id = k.pid WHERE k.token = '{$wechat['token']}' AND type = 'IMG' ORDER BY i.createtime DESC LIMIT {$pager->firstRow},{$pager->listRows}";
        $show = $pager->show();
        $lists = db()->query($sql);

        $this->assign('page',$show);*/
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    public function get_access_token($appid,$appsecret){
        //判断是否过了缓存期
        $wechat = db('wx_user')->find();
        $expire_time = $wechat['web_expires'];
        if($expire_time > time()){
           return $wechat['web_access_token'];
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $return = httpRequest($url,'GET');
        $return = json_decode($return,1);
        $web_expires = time() + 7000; // 提前200秒过期
        db('wx_user')->where(array('id'=>$wechat['id']))->update(array('web_access_token'=>$return['access_token'],'web_expires'=>$web_expires));
        return $return['access_token'];
    }



}