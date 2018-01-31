<?php
/**
 * 动态
 */
namespace app\wap\controller;

class News extends Base{

    public function index(){
        //轮播图
        $where['pid'] = 3;
        $where['enabled'] = 1;
        $where['end_time'] = ['>',time()];
        $banner = db('ad')->field('ad_link,ad_code')->where($where)->order('start_time desc,orderby')->limit(5)->select();

        //直播
        $lives = db('live')->where('is_open',1)->limit(3)->order('add_time desc')->select();

        $this->assign(array(
            'banner'=>$banner,
            'lives'=>$lives,
        ));
        return view();
	}

	//直播
    public function ajaxLive(){
        if (request()->isAjax()){
            $page = input('page');
            $lives = db('live')->where('is_open',1)->page($page,'3')->select();
            return $lives;
        }
        return view('Live');
    }

    //新闻列表
    public function lst(){
        $cat_ids = db('article_cat')->where('cat_type','0')->column('cat_id');
        $cat_ids = implode(',',$cat_ids);
        //热门
        $hot = db('article')->field('article_id,title,thumb')->where('cat_id','in',$cat_ids)->order('click desc')->find();
        //文章
        $this->assign('hot',$hot);
        $this->assign('cat_ids',$cat_ids);
        return view();
    }

    //ajax获取新闻列表
    public function ajaxList(){
        if (request()->isAjax()){
            $page = input('page');
            $cat_ids = input('cat_ids');
            $news = db('article')->field('article_id,title,description,thumb,content')->where('cat_id','in',$cat_ids)->page($page,'4')->select();
            return $news;
        }
    }

    //新闻详情
    public function details(){
        $article_id = input("article_id");
        $new = db('article')->field('article_id,title,content,add_time,thumb')->find($article_id);
        $this->assign('new',$new);
        $this->assign('tguid', session('tguid'));
        $this->assign('user_id', session('user_id'));
        $this->assign('host', request()->domain());
        return view();
    }

    //机构
    public function organize(){
        $res = db('org')->field('name,pic,content')->select();
        $this->assign('res',$res);
        return view();
    }
}
?>