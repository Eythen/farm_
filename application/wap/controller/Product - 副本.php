<?php
/**
 * 商品
 */
namespace app\wap\controller;
use app\home\logic\GoodsLogic;
use app\wap\model\Coupon as CouponModel;
class Product extends Base{

    //礼盒列表
    public function package(){
        return view();
    }

    //肉类、蔬菜列表
    public function vegetables(){
        return view();
    }

    //特产列表
    public function specialty(){
        return view();
    }

    //ajax获取商品列表
    public function ajaxlist(){
        if (request()->isAjax()){
            $cat_id = input('cat_id');
            $page = input('page');
            $where['cat_id'] = $cat_id;
            $where['is_on_sale'] = 1;
            $data = db('goods')->where($where)->page($page,10)->select();
            return $data;
        }
    }

    //商品详情页
    public function details(){
        $goodsLogic = new GoodsLogic();
        $goods_id = input('goods_id');
        $user_id = session('user_id');
        $goods = db('goods')->field('goods_id,goods_name,store_count,market_price,shop_price,goods_content,original_img,cat_id,shipping_price')->find($goods_id);
        $images = db('goods_images')->field('image_url')->where('goods_id',$goods_id)->select();

        //获取商品规格
        $filter_spec = $goodsLogic->get_spec($goods_id);
        //根据规格获取对应的价格和库存表
        $spec_goods_price  = db('spec_goods_price')->where('goods_id',$goods_id)->column("key,price,store_count");
        $spec_goods_price = json_encode($spec_goods_price);
        //获取购物车的商品数量
        $cartNum = db('cart')->where('user_id',$user_id)->count();
        //优惠券领取
        $CouponModel = new CouponModel();
        $couponlist = $CouponModel->couponlist();
        //是否收藏了产品
        $collectMap['user_id'] = session('user_id');
        $collectMap['goods_id'] = $goods['goods_id'];
        $collect = db('goods_collect')->where($collectMap)->count();

        $this->assign(array(
            'goods'=>$goods,
            'images'=>$images,
            'filter_spec' => $filter_spec,
            'spec_goods_price' => $spec_goods_price,
            'cartNum'=>$cartNum,
            'user_id'=>$user_id,
            'couponlist'=>$couponlist,
            'collect'=>$collect,
        ));
        return view();
    }

    //商品收藏
    public function collect(){
        if(request()->isAjax()){
            $post = input('post.');
            $collectMap['user_id'] = session('user_id');
            $collectMap['goods_id'] = $post['goods_id'];
            $has = db('goods_collect')->where($collectMap)->find();

            if(!$has){
                $collect['user_id'] = session('user_id');
                $collect['goods_id'] = $post['goods_id'];
                $collect['add_time'] = time();
                $r = db('goods_collect')->insert($collect);
            }
            else{
                $r = db('goods_collect')->where($collectMap)->delete();
            }
            
            if($r){
                $this->success('更新成功！');
            }
            else{
                $this->error('更新失败！');
            }
        }
    } 

    //商品搜索
    public function search(){
        if (request()->isPost()){
            $keywords = input('keywords');
            $data = array(
                'user_id' => session('user_id'),
                'keywords' => $keywords,
                'add_time' => time(),
            );
            $is_search = db('users_search')->where('keywords',$keywords)->where('user_id',$data['user_id'])->value('id');
            if (!$is_search){
                db('users_search')->insert($data);
            }
            $this->searchList($keywords);
            return view('searchList');
        }
        //热门搜索
        $sql = "SELECT keywords FROM yq_users_search AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM yq_users_search)-(SELECT MIN(id) FROM yq_users_search))+(SELECT MIN(id) FROM yq_users_search)) AS id) AS t2 WHERE t1.id >= t2.id ORDER BY t1.id LIMIT 5;";
        $hot = db('users_search')->query($sql);
        //历史搜索
        $log = db('users_search')->field('keywords')->where('user_id',session('user.user_id'))->limit(5)->select();
        $this->assign('hot',$hot);
        $this->assign('log',$log);
        return view();
    }

    //搜索结果列表
    public function searchList($keywords){
        if (request()->isAjax()){
            $page = input('page');
            $keywords = input('keywords');
            $data = db('goods')->field('goods_id,goods_name,shop_price,original_img')->where('is_recommend','1')->where('store_count','>','0')->where('goods_name', 'like','%'.$keywords.'%')->page($page, '6')->select();
            return $data;
        }
        $this->assign('keywords',$keywords);
    }

    //清除搜索历史
    public function clear(){
        if (request()->isAjax()){
            $user_id = session('user.user_id');
            $res = db('users_search')->where('user_id',$user_id)->delete();
            if ($res){
                return 1;
            }else{
                return 0;
            }
        }
    }

}
?>