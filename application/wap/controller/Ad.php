<?php
/**
 * 跑马灯
 */
namespace app\wap\controller;
use app\wap\model\PointsLog;

class Ad extends Base{

    public function index(){
        if(request()->isAjax()){
            $get = input('get.');

            $page = $get['page']?$get['page']:1;
            $pagesize = $get['pagesize']?$get['pagesize']:12;

            $log = new PointsLog();
            $data['lists'] = $log->ad($pagesize, $page);
            foreach ($data['lists'] as $k => $v) {
                $data['lists'][$k]['add_time'] = formatTime($v['add_time']);
            }


            return $data;

        }
        return view();
	}

}
?>