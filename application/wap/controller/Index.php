<?php
/**
 * 首页
 */
namespace app\wap\controller;
use app\wap\model\PointsLog;
use app\wap\controller\Cart;
class Index extends Base{

    public function index(){
        //轮播图
        $where['pid'] = 1;
        $where['enabled'] = 1;
        $where['end_time'] = ['>',time()];
        $banner = db('ad')->field('ad_link,ad_code')->where($where)->order('start_time desc,orderby')->limit(5)->select();

        //推荐banner
        $ads = db('ad')->field('ad_link,ad_code')->where('pid','=','7')->limit(3)->select();

        //分类隔栏
        $cate = db('ad')->where('ad_id','=','16')->order('ad_id asc')->value('ad_code');

        //推荐商品
        $map['cat_id'] = 2;
        $map['is_on_sale'] = 1;
        $map['is_recommend'] = 1;
        $goods = db('goods')->where($map)->limit(5)->select();

        //热门搜索
//        $sql = "SELECT keywords FROM yq_users_search AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM yq_users_search)-(SELECT MIN(id) FROM yq_users_search))+(SELECT MIN(id) FROM yq_users_search)) AS id) AS t2 WHERE t1.id >= t2.id ORDER BY t1.id LIMIT 5;";
//        $hot = db('users_search')->query($sql);
        $hot = self::getHot(5);
        //历史搜索
        $log = db('users_search')->field('keywords')->where('user_id',session('user.user_id'))->limit(5)->select();

        //购物车
        $cart = new Cart();
        $carts = $cart->getCart(session('user_id'));

        $this->assign(array(
            'banner' => $banner,
            'ads' => $ads,
            'cate' => $cate,
            'goods' => $goods,
            'hot' => $hot,
            'log' => $log,
            'carts' => $carts,
        ));
        return view();
	}

	public static function getHot($num){
        $sql = "SELECT keywords FROM yq_users_search AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM yq_users_search)-(SELECT MIN(id) FROM yq_users_search))+(SELECT MIN(id) FROM yq_users_search)) AS id) AS t2 WHERE t1.id >= t2.id ORDER BY t1.id LIMIT $num;";
        $hot = db('users_search')->query($sql);
        return $hot;
    }

}
?>