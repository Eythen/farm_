<?php
namespace app\home\controller;
use think\Controller;


Class  Chart  extends Base{
    
    /**
     * [客户专员工作量统计  replylog]
     */
    public function replylog(){
        
        $date    = input('get.date'); //获取筛选的时间段

        if (is_numeric($date)) {
            switch ($date) {
                //所有
                case 0:
                    $date = '';
                    break;
                //今天
                case 1:
                    $date = date('Y/m/d') . ' - ' . date('Y/m/d');
                    break;
                //昨天
                case 2:
                    $date = date('Y/m/d', strtotime('-1 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //过去7天
                case 3:
                    $date = date('Y/m/d', strtotime('-7 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //过去14天
                case 4:
                    $date = date('Y/m/d', strtotime('-14 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //过去30天
                case 5:
                    $date = date('Y/m/d', strtotime('-30 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //上个月
                case 6:
                    $date = date('Y/m/01', strtotime('-1 Month')) . ' - ' . date('Y/m/t', strtotime('-1 Month'));
                    break;
                default:
                    $this->error('筛选的时间段有误');
                    break;
            }
        }
        
        //校验 get 过来的时间, 并获取 map
        $date_arr = array_filter(explode(' - ', $date));
        if(!empty($date_arr)){
            sort($date_arr);
            list($sDate, $eDate) = $date_arr;
            $date = $sDate . ' - ' . $eDate; //调整开始和结束时间的位置
        }
        
        if(!isset($_GET['date'])){
            $time = time();
            if($sDate == ''){
                $sDate = date('Y-m-d', strtotime('-1 day', $time));
            }
            if($eDate == ''){
                $eDate = date('Y-m-d', strtotime('-1 day', $time));
            }
            $date = "$sDate - $eDate";
        }
        //如果有筛选
        if($date != ''){
            $this->assign('date', $date);
            if(!empty($sDate)){ //如果符合日期格式
                $map['time'][] = array('egt', $sDate);
            }
            else{
                $this->error('开始时间不正确');
            }
            if(!empty($eDate)){ //如果符合日期格式
                $map['time'][] = array('lt', date('Y-m-d', strtotime("+1 day", strtotime($eDate))));
            }
            else{
                $this->error('结束时间不正确');
            }
        }
        
        $map = isset($map) ? $map : '';
        
        $userlist = model('Users')->getDep(config('CORP.DEPT_ID')['KEFU']);
        foreach ($userlist as $k => $v) {
           if ($v['position'] != '客服经理') {
               $users[] = $v['username'];
           }
        }

        $map['zy_name'] = array('in',$users);
        
        //查询客服专员数据
        $data=db('replylog')->where($map)
        ->field("count(id) as num,zy_name as name")
        ->group("zy_name")
        ->order("num desc")
        ->select();
        $res = array();
        foreach ($data as $key => $value) {
            $name.=",'".$value['name']."'";
            $num.=",".$value['num'];
            $res['name'][] = $value['name'];
            $res['num'][] = intval($value['num']);
        }
        $name=substr($name, 1);
        $num=substr($num, 1);
        $this->assign('name', $name);
        $this->assign('num', $num);
        if (input('get.act') == 'index') {
            return $res;
        }else{
            return $this->fetch();
        }
    }
    /**
     * [回访数据分析 chart]
     */
    public function chart(){

        $date    = input('get.date'); //获取筛选的时间段

        if (is_numeric($date)) {
            switch ($date) {
                //所有
                case 0:
                    $date = '';
                    break;
                //今天
                case 1:
                    $date = date('Y/m/d') . ' - ' . date('Y/m/d');
                    break;
                //昨天
                case 2:
                    $date = date('Y/m/d', strtotime('-1 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //过去7天
                case 3:
                    $date = date('Y/m/d', strtotime('-7 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //过去14天
                case 4:
                    $date = date('Y/m/d', strtotime('-14 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //过去30天
                case 5:
                    $date = date('Y/m/d', strtotime('-30 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //上个月
                case 6:
                    $date = date('Y/m/01', strtotime('-1 Month')) . ' - ' . date('Y/m/t', strtotime('-1 Month'));
                    break;
                default:
                    $this->error('筛选的时间段有误');
                    break;
            }
        }
        
        //校验 get 过来的时间, 并获取 map
        $date_arr = array_filter(explode(' - ', $date));
        if(!empty($date_arr)){
            sort($date_arr);
            list($sDate, $eDate) = $date_arr;
            $date = $sDate . ' - ' . $eDate; //调整开始和结束时间的位置
        }
        //如果没有筛选任何东西
        if(!isset($_GET['date'])){
            $time = time();
            if($sDate == ''){
                $sDate = date('Y-m-01', $time);
            }
            if($eDate == ''){
                $eDate = date('Y-m-t', $time);
            }
            $date = "$sDate - $eDate";
        }
        //如果有筛选
        if($date != ''){
            if(!empty($sDate)){ //如果符合日期格式
                $map['add_time'][] = array('egt', $sDate);
            }
            else{
                $this->error('开始时间不正确');
            }
            if(!empty($eDate)){ //如果符合日期格式
                $map['add_time'][] = array('lt', date('Y-m-d', strtotime("+1 day", strtotime($eDate))));
            }
            else{
                $this->error('结束时间不正确');
            }
        }
        /**
                                    因数据量少，现先设置默认统计所有,删除下面if判断，即显示数据当月到当前
         */
        if(!isset($_GET['date'])){
            unset($map);
        }
        $map = isset($map) ? $map : '';
        
        //查询五个招商经理
        $data=db('fawu_consult')->where($map)
        ->field("count(id) as num,zhaoshang_manager")
        ->group("zhaoshang_manager")
        ->order("num desc")
        ->limit('5')
        ->select();
        
        
        
        
        foreach ($data as $key => $value) {
            $name.=",'".$value['zhaoshang_manager']."'";
            $num.=",".$value['num'];
        }
        $name=substr($name, 1);
        $num=substr($num, 1);
        $this->assign('name', $name);
        $this->assign('num', $num);
        
        
        //主题分类
        $data2=db('fawu_consult')->where($map)
        ->field("count(id) as num,title")
        ->group("title")
        ->order("num desc")
        ->select();
        foreach ($data2 as $key => $value) {
            $name2.=",'".$value['title']."'";
            $num2.=",".$value['num'];
        }
        $name2=substr($name2, 1);
        $num2=substr($num2, 1);
        $this->assign('name2', $name2);
        $this->assign('num2', $num2);
        
        //主题分类图表
        $category=db('fawu_consult')->field("count(*) as count,title")->group("title")->select();
        
        $month = "[";
        foreach ($category as $key => $value) {
            $month .= "{name:'" . $value['title'] . "',";
            $month .= "data:[";
            $month .= $value['count'] . ",";
            $month .= "]},";
        }
        $month .= "]";
        $this->assign('month', $month);
        
        return $this->fetch();
    }
    
    
    
    /**
     * [短信满意度统计 ]
     */
    public function chartMsg(){
        
		$date    = input('get.date'); //获取筛选的时间段

        if (is_numeric($date)) {
            switch ($date) {
                //所有
                case 0:
                    $date = '';
                    break;
                //今天
                case 1:
                    $date = date('Y/m/d') . ' - ' . date('Y/m/d');
                    break;
                //昨天
                case 2:
                    $date = date('Y/m/d', strtotime('-1 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //过去7天
                case 3:
                    $date = date('Y/m/d', strtotime('-7 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //过去14天
                case 4:
                    $date = date('Y/m/d', strtotime('-14 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //过去30天
                case 5:
                    $date = date('Y/m/d', strtotime('-30 day')) . ' - ' . date('Y/m/d', strtotime('-1 day'));
                    break;
                //上个月
                case 6:
                    $date = date('Y/m/01', strtotime('-1 Month')) . ' - ' . date('Y/m/t', strtotime('-1 Month'));
                    break;
                default:
                    $this->error('筛选的时间段有误');
                    break;
            }
        }
        
		
		//校验 get 过来的时间, 并获取 map
		$date_arr = array_filter(explode(' - ', $date));
		if(!empty($date_arr)){
		    sort($date_arr);
		    list($sDate, $eDate) = $date_arr;
		    $date = $sDate . ' - ' . $eDate; //调整开始和结束时间的位置
		}
		//如果没有筛选任何东西
		if(!isset($_GET['date'])){
		    $time = time();
		    if($sDate == ''){
		        $sDate = date('Y-m-01', $time);
		    }
		    if($eDate == ''){
		        $eDate = date('Y-m-t', $time);
		    }
		    $date = "$sDate - $eDate";
		}
		//如果有筛选
		if($date != ''){
		    if(!empty($sDate)){ //如果符合日期格式
		        $map['add_time'][] = array('egt', $sDate);
		    }
		    else{
		        $this->error('开始时间不正确');
		    }
		    if(!empty($eDate)){ //如果符合日期格式
		        $map['add_time'][] = array('lt', date('Y-m-d', strtotime("+1 day", strtotime($eDate))));
		    }
		    else{
		        $this->error('结束时间不正确');
		    }
		}
		$map['type'] = array('eq',2);
		
		$num=array();
		$name="'满意','一般','不满'";
		for($i=1;$i<4;$i++){
			$map['content'] = array('exp',"regexp $i");
			//短信三种状态的数量
			$count=db('sms')-> where($map)->count();
			$num[$i]=$count;
		}
		$num=implode(',', $num);	
		$this->assign('name', $name);
		$this->assign('num', $num);
		return $this->fetch();
    }
    
}


