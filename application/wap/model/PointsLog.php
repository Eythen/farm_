<?php
/**
 * 积分消耗记录
 */
namespace app\wap\model;
use think\Model;

class PointsLog extends Model{
    
    protected $table = 'yq_points_log';

    /**
     * status 0未处理，1已完成，2未完成
     * type 0未分类，1商城消费，2捐助，3提现 ,4工资
     */


    /**
     * [log 填写积分消记录]
     * @param  [array]  $data   [积分处理内容]
     * @param  integer $return [1要返回参数，0不返回]
     * @return [bool]          [description]
     */
    public function log($data, $return=1){
        // 实例化模型
        $pointModel = new PointsLog($data);
        $result = $pointModel->allowField(true)->save();
        if($return){
            return $result;
        }
    }

    /**
     * [updatePoint 状态更新]
     * @param  [array] $where [更新条件]
     * @param  [int] $return [1要返回参数，0不返回]
     * @return [bool]        [description]
     */
    public function updatePoint($data, $where, $return=1){
        // 实例化模型
        $pointModel = new PointsLog($data);
        $result = $pointModel->allowField(true)->save();
        if($return){
            return $result;
        }
    }

    /**
     * [deletePoints 删除]
     * @param  [array] $where [更新条件]
     * @param  [int] $return [1要返回参数，0不返回]
     * @return [bool]        [description]
     */
    public function deletePoints($where, $return=1){
        // 实例化模型
        $pointModel = new PointsLog();
        $result = $pointModel->destroy($where);
        if($return){
            return $result;
        }
    }

    /**
     * [listPoints 积分列表]
     * @param  [array] $where [查询积分条件]
     * @return [array]        [查询结果]
     */
    public function listPoints($where){
        // 实例化模型
        $pointModel = new PointsLog();
        $result = $pointModel->where($where)->select();
        
        return $result;
    }

    /**
     * [ad 跑马灯信息]
     * @param  [type] $limit [选取条数]
     * @return [type]        [description]
     * '0未分类，1商城消费，2升级，3提现 ,4充值,5购物邮费，6报单币换购物7返利币换报单币8返利币换购物币9分红',
     * 用户昵称】+刚刚充值了【充值金额】+元！
     * 厉害了，小黎哥刚刚充值了10000元！
恭喜小黎哥成为钻石会员！
剁手！小黎哥又买了5000元东西！
    *
     */
    public function ad($limit='20', $page='1'){
        // 实例化模型
        $pointModel = new PointsLog();
        $where['type'] = ['in', [1, 2, 4]];
        $offset = ($page-1)*$limit;
        $result = $pointModel->where($where)->order('add_time desc')->limit($offset, $limit)->select();

        //获取用户
        $users = get_all_users('nickname', 'user_id');

        foreach ($result as $k => $v) {
            if($v['type']==4){ //

                $result[$k]['title'] = '厉害了，刚刚'.$users[$v['user_id']].'充值了'.$v['points_recharge'].'元！';
            }
            if($v['type']==2){
                $v['des'] = str_replace('用于会员等级升级到', '', $v['des']);
                $result[$k]['title'] = '恭喜，'.$users[$v['user_id']].'成为'.$v['des'] .'会员！';
            }
            if($v['type']==1){
                $result[$k]['title'] = '剁手！'.$users[$v['user_id']].'又买了'.$v['points_money'].'元东西！';
            }
            

        }
        
        return $result;
    }


}
?>