<?php
/**
 * 商城
 */
namespace app\wap\controller;
class Shop extends Base{

    public function index(){
        //轮播图
        $where['pid'] = 2;
        $where['enabled'] = 1;
        $where['end_time'] = ['>',time()];
        $banner = db('ad')->field('ad_link,ad_code')->where($where)->order('start_time desc,orderby')->limit(6)->select();

        //推荐
        $map['pid'] = 3;
        $map['enabled'] = 1;
        $recommends = db('ad')->field('ad_link,ad_code')->where($map)->order('orderby desc')->limit(6)->select();

        //分类隔栏
        $cate = db('ad')->field('ad_code')->where('pid','=','5')->order('ad_id asc')->limit(3)->select();


        $w['is_on_sale'] = 1;
        $w['is_recommend'] = 1;
        //生鲜
        $freshs = db('goods')->field('goods_id,original_img,goods_name,weight,shop_price')->where($w)->where('cat_id','=','2')->order('on_time desc')->limit(5)->select();
        //零食
        $greens = db('goods')->field('goods_id,original_img,goods_name,weight,shop_price')->where($w)->where('cat_id','=','3')->order('on_time desc')->limit(5)->select();
        //套餐
        $package = db('goods')->field('goods_id,original_img,goods_name,weight,shop_price')->where($w)->where('cat_id','=','1')->order('on_time desc')->limit(5)->select();

        //购物车
        $cart = new Cart();
        $carts = $cart->getCart(session('user_id'));

        $this->assign(array(
            'banner'=>$banner,
            'recommends'=>$recommends,
            'cate'=>$cate,
            'freshs'=>$freshs,
            'greens'=>$greens,
            'package'=>$package,
            'carts' => $carts,
        ));
        return view();
	}

}
?>