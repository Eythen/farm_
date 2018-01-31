<?php
namespace app\home\model;
use think\Model;


class Visitor extends Model{
    
       
   /**
    * [getpagedata 获取分页查询数据]
    * @param number $offset  [分页查询开始的条数]
    * @param number $limit   [分页长度]
    * @param string $sort    [排序的字段]
    * @param string $order   [排序方式]
    * @param array $map      [查询条件]
    * @param string $keyword [搜索关键字]
    */
    public function getpagedata($offset=0,$limit=10,$sort="id",$order="desc",$map=array(),$keyword=""){
          if($keyword !=""){
              $map["id|name|tel|date_format(add_time,'%Y-%m-%d %h:%i:%s')|address|content|zy"] = array('like',"%$keyword%");
          }
          
          $data['total']= db('visitor')->where($map)->count();
          if($data['total']){
            $rows=db('visitor')->where($map)->order("$sort $order")->limit("$offset,$limit")->select();
            $data['rows']=$rows;
            return $data;
          }
    
    }
    
    /**
     * [add 添加]
     * @param [array] $data [添加的数据]
     */
    public function add($data){
      $result = db('visitor')->insert($data);
      return $result;
    }
    
    /**
     * [addMore 多条数据添加]
     * @param  [array] $data [添加数据数组]
     */
    public function addMore($data){
      $result = db('visitor')->insertAll($data);
      return $result;
    }
    
    
    
    
    
}