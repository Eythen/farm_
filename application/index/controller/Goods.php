<?php
/**
 * Thinkphp整合 淘宝客 https://www.alimama.com
 */
namespace app\index\controller;
use think\Controller;
use think\Loader;

class Goods extends Controller{
	const appkey = '24541969';
	const secretKey = 'fa39702a1656ef42f0fe237f896ee2a7';

    //const yifu = '570538054';


	const adzoneId = ['yifu' => '119080886', 'xue' => '等待回执', 'mao'=>'发送失败', 'chuang'=>'发送成功', 'dizi' => ''];
	protected $tel; //记录不能发送的手机号码

//119080886:1499942263_3k8_324440858
//119080886:1499941443_2k7_570538054
//

	/**
	 * [_simplexml_to_array xml转换成数组]
	 * @param  [type] $obj [对象]
	 * @return [type]      [description]
	 */
	private function _simplexml_to_array($obj)
	{
		if(count($obj) >= 1){
			$result = $keys = [];
			foreach($obj as $key=>$value){
				isset($keys[$key]) ? ($keys[$key] += 1) : ($keys[$key] = 1);
				if( $keys[$key] == 1 ){
					$result[$key] = $this->_simplexml_to_array($value);
				}elseif( $keys[$key] == 2 ){
					$result[$key] = [$result[$key], $this->_simplexml_to_array($value)];
				}else if( $keys[$key] > 2 ){
					$result[$key][] = $this->_simplexml_to_array($value);
				}
			}
			return $result;
		}else if(count($obj) == 0){
			return (string)$obj;
		}
	}

    /**
     * [productexpiringfilter 首页即将过期产品选择]
     * @return [type] [description]
     */
    public function productexpiringfilter(){
        if(request()->isAjax()){
            $cat_id = input('categorygrouptoken/d');

            if($cat_id){
                $cat = model('TaobaoCat')->cat();

                //查找父级id
                $parent_r = array_column($cat, 'parent_id', 'id');
                $parent_id = $parent_r[$cat_id];

                if($parent_id == 0){
                    $ids = [$cat_id];
                    foreach ($cat as $k => $v) {
                        if($cat_id== $v['parent_id']){
                            array_push($ids, $v['id']);
                        }
                    }
                }
                else{
                    $ids = $cat_id ;
                }
                $where['cat_id'] = ['in', $ids];
            }

            $limit = 6;

            $sort = 'id';
            $order = 'desc';


            $where['uland_price'] = ['>', 0];

            //精选
            $data = db('taobao')->where($where)->order($sort.' '.$order)->limit($limit)->select();
            if($data){
                foreach ($data as $k => $v) {
                    $list .= '<li class="productitem"> <a href="'.$v['uland_url_short'].'" class="picture" target="_blank" title="'.$v['title'].'"> <img class="lazyOwl" src="'.$v['pict_url'].'"  alt="'.$v['title'].'" /> </a> <a href="'.$v['uland_url_short'].'" class="title" target="_blank" title="'.$v['title'].'">'.$v['title'].'</a> <p class="coupon-desc"> 券后 ￥'.$v['end_price'].'<del>￥ '.$v['price'].'</del> </p> <div class="couponcover"></div> <a href="'.$v['uland_url_short'].'" target="_blank" class="hoverbtn ">领'.$v['uland_price'].'元券</a> </li>'; 
                }


            }
            return $list;


        }
    }

     /**
     * [productrankingfilter 首页排行榜产品选择]
     * @return [type] [description]
     */
    public function productrankingfilter(){
        if(request()->isAjax()){
            $cat_id = input('categorygrouptoken/d');

            if($cat_id){
                $cat = model('TaobaoCat')->cat();

                //查找父级id
                $parent_r = array_column($cat, 'parent_id', 'id');
                $parent_id = $parent_r[$cat_id];

                if($parent_id == 0){
                    $ids = [$cat_id];
                    foreach ($cat as $k => $v) {
                        if($cat_id== $v['parent_id']){
                            array_push($ids, $v['id']);
                        }
                    }
                }
                else{
                    $ids = $cat_id ;
                }
                $where['cat_id'] = ['in', $ids];
            }

            $limit = 16;

            $sort = 'month_sell';
            $order = 'desc';




            $where['uland_price'] = ['>', 0];
            $where['end_price'] = ['between', [30,50]];


            //精选
            $data = db('taobao')->where($where)->order($sort.' '.$order)->limit($limit)->select();
            if($data){
                $i = 0;
                foreach ($data as $k => $v) {
                    $list .= '<li class="productitem"> <a href="'.$v['uland_url_short'].'" class="picture" target="_blank" title="'.$v['title'].'"> <img class="lazyOwl" src="'.$v['pict_url'].'"  alt="'.$v['title'].'" /> </a> <i class="icon-rankingbg threeicon"></i> <span class="numlabel">'.$i.'</span>  <a href="'.$v['uland_url_short'].'" class="title" target="_blank" title="'.$v['title'].'">'.$v['title'].'</a> <p class="coupon-desc"> 券后 ￥'.$v['end_price'].'<del>￥ '.$v['price'].'</del> </p> <div class="couponcover"></div> <a href="'.$v['uland_url_short'].'" target="_blank" class="hoverbtn ">领'.$v['uland_price'].'元券</a> </li>'; 
                    $i++;
                }


            }
            return $list;


        }
    }

	/**
	 * [index 首页]
	 * @return [type] [description]
	 */
	public function index(){
        if(request()->isAjax()){
            $request = input('request.');

            $limit = $request['limit']? $request['limit']: 10;
            $page = $request['page']? $request['page']: 1;
            $sort = 'id';
            $order = 'desc';

            $offset = ($page-1)*10;


            $where['uland_price'] = ['>', 0];


            //今天精选
            $today_total = db('taobao')->where($where)->count();
            $today_rows = db('taobao')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            if($today_rows){
                foreach ($today_rows as $k => $v) {
                    $list .= '<li class="li-newest"> <a href="'.$v['uland_url_short'].'" class="picture-warpper" title="'.$v['title'].'" target="_blank"> <img class="lazyload" data-original="'.$v['pict_url'].'" alt="'.$v['title'].'" /> </a> <a target="_blank" href="'.url('show', ['id' => $v['id'] ]).'" class="mallcorner" style="background-color:#dd2727;">详情</a> <div class="desc-wrap"> <p class="product-title-wrap"><a class="product-title" target="_blank" title="'.$v['title'].'" href="'.$v['uland_url_short'].'">'.$v['title'].'</a></p> <div class="salesinfo"> <del class="origin-price">￥'.$v['price'].'</del> <span class="discounttip">（'.$v['discount'].'折）</span> <span class="salescount">月销 '.$v['month_sell'].'</span> </div> <div class="msginfo-row"> <span class="aftercoupon">券后 ￥<span>'.$v['end_price'].'</span>&nbsp;&nbsp; </span> <a class="couponlink" href="'.$v['uland_url_short'].'" target="_blank">领'.$v['uland_price'].'元券</a> </div> </div> </li>'; 
                }

                $url = url('index')."?";
                $data['pagebtnhtml'] = $this->listPage($today_total, $limit, $page, $url);
                $data['productitems'] = $list;
            }
            return $data;
            exit;


        }
        //分类
        $cat = db('taobao_cat')->select();
        $list['yifu'] = get_tree($cat, 2);
        $list['jujia'] = get_tree($cat, 1);
        $list['shouji'] = get_tree($cat, 3);
        $list['diannao'] = get_tree($cat, 3);
        $list['jiajujiancai'] = get_tree($cat, 4);
        $list['shiwu'] = get_tree($cat, 5);
        $list['bangong'] = get_tree($cat, 7);
        $list['maoxue'] = get_tree($cat, 6);
        $list['xiaoyifu'] = get_tree($cat, 8);
        $list['qiche'] = get_tree($cat, 9);

        //取一级分类
        $list['parent'] = [];
        foreach ($cat as $k => $v) {
            if($v['parent_id'] == 0){
                $list['parent'][$k]['id'] = $v['id'];
                $list['parent'][$k]['name'] = $v['name'];
            }
        }


        $this->assign('list', $list);


        //广告大图start
        $ad = db('taobao_ad')->where($map)->order('sort asc, id desc')->select();
        $this->assign('ad', $ad);
        //广告大图end

		$request = input('request.');

            $limit = $request['limit']? $request['limit']: 20;
            $page = $request['page']? $request['page']: 1;
            $sort = 'id';
            $order = 'desc';

            $offset = ($page-1)*10;


            $where['uland_price'] = ['>', 0];


            //今天精选
            $data['today_total'] = db('taobao')->where($where)->count();
            $data['today'] = db('taobao')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $url = url('index')."?";
            $data['today_page'] = $this->listPage($data['today_total'], $limit, $page, $url);

            //活动到期
            $activity['uland_price'] = ['>', 0];
            $activity['uland_end_time'] = ['<=', date('Y-m-d')];

            $data['activity'] = db('taobao')->where($activity)->order('id desc')->limit('0 , 6')->select();

            //排行榜
            $hot['uland_price'] = ['>', 0];
            $data['hot'] = db('taobao')->where($hot)->order('month_sell desc')->limit('0 , 18')->select();
            //$data['today']['total'] = db('taobao')->where($where)->whereor($whereor)->count();

            

            //格式化
            foreach ($data['today'] as $k => $v) {
                //$data['today'][$k]['uland_price'] = ulandFormart($v['uland_price']);
                $data['today'][$k]['now_price'] = $v['price']-$v['uland_price'];
            }

            //格式化
            foreach ($data['hot'] as $k => $v) {
                //$data['today'][$k]['uland_price'] = ulandFormart($v['uland_price']);
                $data['hot'][$k]['now_price'] = $v['price']-$v['uland_price'];
            }

            //格式化
            foreach ($data['activity'] as $k => $v) {
                //$data['today'][$k]['uland_price'] = ulandFormart($v['uland_price']);
                $data['activity'][$k]['now_price'] = $v['price']-$v['uland_price'];
            }
        //}
        

/*dump($data);

die;
*/
        $this->assign('data', $data);
        return $this->fetch();    
	}


    /**
     * [listPage 列表分页]
     * @param  [type] $total        [总数]
     * @param  [type] $page_num     [每页条数]
     * @param  [type] $current_page [当前分页]
     * @param  [type] $url          [当前链接]
     * @return [type]               [分页字符串]
     */
    public function listPage($total, $page_num, $current_page=1, $url){
        $total_page = ceil($total/$page_num);
        $current_page = $current_page?$current_page:1;



        if($total_page<2){
            $page = '';
            //$page = '<div data-currentpage="'.$current_page.'" data-firstpage="'.$url.'"  data-invalidpageerrmsg="页索引无效" data-outrangeerrmsg="页索引超出范围" data-pagecount="'.$total_page.'" data-pageparameter="page" data-pagerid="Webdiyer.MvcPager" data-urlformat="'.$url.'&amp;page=__page__" style="text-align:center"><a class="current"></a></div>';
        }
        else{
            $page_begin = '<div data-currentpage="'.$current_page.'" data-firstpage="'.$url.'"  data-invalidpageerrmsg="页索引无效" data-outrangeerrmsg="页索引超出范围" data-pagecount="'.$total_page.'" data-pageparameter="page" data-pagerid="Webdiyer.MvcPager" data-urlformat="'.$url.'&amp;page=__page__" style="text-align:center">';

            $p = '';

            $page_end = '</div>';

            if($current_page>=2){
                $prev = $current_page-1;
                $p .= '<a href="'.$url.'&page='.$prev.'"><</a>';
            }

            if($current_page<$total_page){
                $next = $current_page+1;
                $end .= '<a href="'.$url.'&page='.$next.'">></a>';
            }    

            if($current_page==$total_page){
                $next = $current_page;
                $end .= '<a href="'.$url.'&page='.$next.'" disabled="disabled">></a>';
            }    


            if(($total_page < 11 ) || ($current_page<10) ){
                if($total_page < 11){
                    for ($i=1; $i <= $total_page; $i++) { 
                        if($i == $current_page){
                           $p .= '<a class="current">'.$i.'</a>';
                        }
                        
                        else{
                            $p .= '<a href="'.$url.'&page='.$i.'">'.$i.'</a>';
                        }
                    }
                    
                }
                if( ($current_page<10) && ($total_page > 10)){

                    for ($i=1; $i <= 10; $i++) { 
                        if($i == $current_page){
                           $p .= '<a class="current">'.$i.'</a>';
                        }
                        
                        else{
                            $p .= '<a href="'.$url.'&page='.$i.'">'.$i.'</a>';
                        }
                    }
                }
            }
            else{
                $start_page = $current_page-5;
                $end_page = $current_page+5;

                if($end_page>$total_page){
                    for ($i=$start_page; $i <= $total_page; $i++) { 
                        if($i == $current_page){
                            $p .= '<a class="current">'.$i.'</a>';
                        }
                        
                        else{
                            $p .= '<a href="'.$url.'&page='.$i.'">'.$i.'</a>';
                        }

                        if($i == $total_page){
                            $p .= '<a href="'.$url.'&page='.$i.'">'.$i.'</a>';
                        }
                        
                    }
                }
                else{
                    for ($i=$start_page; $i <= $end_page; $i++) { 
                        if($i == $current_page){
                            $p .= '<a class="current">'.$i.'</a>';
                        }
                        
                        else{
                            $p .= '<a href="'.$url.'&page='.$i.'">'.$i.'</a>';
                        }

                        if($i == $total_page){
                            $p .= '<a href="'.$url.'&page='.$i.'">'.$i.'</a>';
                        }
                        
                    }
                }
            }

            $page =$page_begin.$p.$end.$page_end;
        }
        
        $page = str_replace('?&', '?', $page);
        return $page;

    }

    /**
     * [list 列表]
     * @return [type] [description]
     */
    public function list(){


        /*$data = db('taobao')->select();

        foreach ($data as $k => $v) {*/
       //if(request()->isAjax()){

            $request = input('request.');

            $limit = $request['limit']? $request['limit']: 20;
            $page = $request['page']? $request['page']: 1;
            $offset = ($page-1)*10;


            $where['uland_price'] = ['>', 0];
            if($request['lowprice']){
                $where['price'] = ['>=', $request['lowprice']];
            }

            $request['cat_id'] = input('cat_id/d');
            $data['title'] = '优惠商品列表';

            if($request['cat_id']){
                $cat = model('TaobaoCat')->cat();

                //查找父级id
                $parent_r = array_column($cat, 'parent_id', 'id');
                $parent_id = $parent_r[$request['cat_id']];

                if($parent_id == 0){
                    //$get_son_id = get_son_id($cat, $request['cat_id']);
                    $ids = [$request['cat_id']];
                    foreach ($cat as $k => $v) {
                        if($request['cat_id']== $v['parent_id']){
                            array_push($ids, $v['id']);
                        }
                    }
                    $data['title'] = getCatName($request['cat_id']);
                    $nav = '<ul class="bread-nav"> <li><a href="/">首页</a></li> <li><span class="arrow-right">></span><a href="'.url('list', ['cat_id' => $request['cat_id']]).'">'.getCatName($request['cat_id']).'</a></li> </ul>';
                }
                else{
                    $data['title'] = getCatName($request['cat_id'])."-".getCatName($parent_id);
                    $ids = $request['cat_id'] ;
                    $nav = '<ul class="bread-nav"> <li><a href="/">首页</a></li> <li><span class="arrow-right">></span><a href="'.url('list', ['cat_id' => $parent_id ]).'">'.getCatName($parent_id).'</a></li> <li><span class="arrow-right">></span><a href="'.url('list', ['cat_id' => $request['cat_id']]).'">'.getCatName($request['cat_id']).'</a></li> </ul>';
                }
                $where['cat_id'] = ['in', $ids];
            }
            if($request['highprice']){
                $where['price'] = ['<=', $request['highprice']];
            }


            $request['act'] = input('act/s');

            if($request['act'] == '9'){
                $data['title'] = '9元包邮';
                $where['end_price'] = ['<=', 9];
                $nav = '<ul class="bread-nav"> <li><a href="/">首页</a></li> <li><span class="arrow-right">></span><a href="'.url('list', ['act' => '9']).'">9元包邮</a></li> </ul>';
            }
            elseif($request['act'] == 'priceasc'){
                $sort = 'end_price';
                $order = 'asc';
            }
            elseif($request['act'] == 'baicaijia'){
                $data['title'] = '白菜价';
                $where['end_price'] = ['between', [10,50] ];
                $nav = '<ul class="bread-nav"> <li><a href="/">首页</a></li> <li><span class="arrow-right">></span><a href="'.url('list', ['act' => 'baicaijia']).'">白菜价</a></li> </ul>';
            }
            elseif($request['act'] == 'baiyuanquan'){
                $data['title'] = '百元券';
                $where['uland_price'] = 100;
                $nav = '<ul class="bread-nav"> <li><a href="/">首页</a></li> <li><span class="arrow-right">></span><a href="'.url('list', ['act' => 'baicaijia']).'">百元券</a></li> </ul>';
            }


            $request['type'] = input('type');
            if($request['type'] == 'xl'){
                $sort = 'month_sell';
                $order = 'desc';
            }
            elseif($request['type'] == 'zhk'){
                $sort = 'discount';
                $order = 'asc';
            }
            elseif($request['type'] == 'priceasc'){
                $sort = 'end_price';
                $order = 'asc';
            }
            elseif($request['type'] == 'pricedesc'){
                $sort = 'end_price';
                $order = 'desc';
            }
            else{
                $sort = 'id';
                $order = 'desc';
                
            }
            
            

           if(request()->isAjax()){
                $request = input('request.');

                $limit = $request['limit']? $request['limit']: 20;
                $page = $request['page']? $request['page']: 1;
                $sort = 'id';
                $order = 'desc';

                $offset = ($page-1)*10;


                $where['uland_price'] = ['>', 0];


                //今天精选
                $total = db('taobao')->where($where)->count();
                $rows = db('taobao')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
                if($rows){
                    foreach ($rows as $k => $v) {
                        $list .= '<li class="li-newest"> <a href="'.$v['uland_url_short'].'" class="picture-warpper" title="'.$v['title'].'" target="_blank"> <img class="lazyload" data-original="'.$v['pict_url'].'" alt="'.$v['title'].'" /> </a> <a target="_blank" href="'.url('show', ['id' => $v['id'] ]).'" class="mallcorner" style="background-color:#dd2727;">详情</a> <div class="desc-wrap"> <p class="product-title-wrap"><a class="product-title" target="_blank" title="'.$v['title'].'" href="'.$v['uland_url_short'].'">'.$v['title'].'</a></p> <div class="salesinfo"> <del class="origin-price">￥'.$v['price'].'</del> <span class="discounttip">（'.$v['discount'].'折）</span> <span class="salescount">月销 '.$v['month_sell'].'</span> </div> <div class="msginfo-row"> <span class="aftercoupon">券后 ￥<span>'.$v['end_price'].'</span>&nbsp;&nbsp; </span> <a class="couponlink" href="'.$v['uland_url_short'].'" target="_blank">领'.$v['uland_price'].'元券</a> </div> </div> </li>'; 
                    }

                    $url = url('list')."?cat_id=".$request['cat_id']."&type=".$request['type'];
                    $data['pagebtnhtml'] = $this->listPage($total, $limit, $page, $url);
                    $data['productitems'] = $list;
                }
                return $data;
                exit;


            }


            $data['rows'] = db('taobao')->where($where)->order($sort.' '.$order)->limit($offset.','.$limit)->select();
            $data['total'] = db('taobao')->where($where)->count();

            $url = url('list')."?cat_id=".$request['cat_id']."&type=".$request['type'];
            $data['page'] = $this->listPage($data['total'], $limit, $page, $url);


            //格式化
            foreach ($data['rows'] as $k => $v) {
                $data['rows'][$k]['now_price'] = $v['price']-$v['uland_price'];
            }

        $navcat = $request['act'];

        $this->assign('data', $data);

        $this->assign('navcat', $navcat);
        $this->assign('nav', $nav);
        return $this->fetch();


        /*Loader::import("taobaoke.TopSdk");
        date_default_timezone_set('Asia/Shanghai'); 
        $c = new \TopClient;
        $c->appkey = self::appkey;
        $c->secretKey = self::secretKey;*/
   /*      $req = new \TbkItemGetRequest;
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
        $req->setQ("女装");
        $req->setCat("16,18");
        $req->setItemloc("杭州");
        $req->setSort("tk_rate_des");
        $req->setIsTmall("false");
        $req->setIsOverseas("false");
        $req->setStartPrice("2");
        $req->setEndPrice("200");
        $req->setStartTkRate("100");
        $req->setEndTkRate("1023");
        $req->setPlatform("1");
        $req->setPageNo("123");
        $req->setPageSize("20");
        $resp = $c->execute($req);*/


/*$req = new \TbkItemInfoGetRequest;
$req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url");
$req->setPlatform("1");
$req->setNumIids("553564376542,553683272157");
$resp = $c->execute($req);*/
//dump($resp);

        /*$resp = $this->_simplexml_to_array($resp);
        dump($resp);*/

}
    
    /**
     * [show 商品详情页面]
     * @return [type] [description]
     */
    function show(){
        $map['id'] = input('id/d');
        $data = db('taobao')->where($map)->find();
        $data['now_price'] = $data['price']-$data['uland_price'];
        $cat = model('TaobaoCat')->cat();

        //查找父级id
        $parent_r = array_column($cat, 'parent_id', 'id');
        $parent_id = $parent_r[$data['cat_id']];
        if($parent_id == 0){
            
            $nav = '<ul class="bread-nav"> <li><a href="/">首页</a></li> <li><span class="arrow-right">></span><a href="'.url('list', ['cat_id' => $data['cat_id']]).'">'.getCatName($data['cat_id']).'</a></li> </ul>';
        }
        else{
            $nav = '<ul class="bread-nav"> <li><a href="/">首页</a></li> <li><span class="arrow-right">></span><a href="'.url('list', ['cat_id' => $parent_id ]).'">'.getCatName($parent_id).'</a></li> <li><span class="arrow-right">></span><a href="'.url('list', ['cat_id' => $data['cat_id']]).'">'.getCatName($data['cat_id']).'</a></li> </ul>';
        }
        $data['nav'] = $nav;

        if($data['update_time']){
            $data['update_userid'] = get_admin_name($data['update_userid']);
            $data['update_time'] = formatTime($data['update_time'], 'Y-m-d H:i:s');
        }
        else{
            $data['update_userid'] = 'vv';
            $data['update_time'] = date("Y-m-d 09:10:12");
        }

        
        $hot['uland_num_not'] = ['>', 0];
        $hot['cat_id'] = ['eq', $data['cat_id']];
        $hot_data = db('taobao')->where($hot)->order('commision desc')->limit(6)->select();

        $activity['uland_num_not'] = ['>', 10];
        $activity['uland_price'] = ['>', 20];
        $activity['cat_id'] = ['eq', $data['cat_id']];
        $activity_data = db('taobao')->where($activity)->order('month_sell desc')->limit(6)->select();

        //$love['cat_id'] = ['eq', $data['cat_id']];
        $love['uland_num_not'] = ['>', 0];
        $love_data = db('taobao')->where($love)->limit(5)->select();
        foreach ($love_data as $k => $v) {
            $love_data[$k]['now_price'] = $v['price']-$v['uland_price'];
        }


        $this->assign('data', $data);
        $this->assign('hot_data', $hot_data);
        $this->assign('activity_data', $activity_data);
        $this->assign('love_data', $love_data);


        return $this->fetch();

    }

    
    





}
?>