<?php
/**
 * 项目模型
 */
namespace app\home\Model;
use think\Model;

class Help extends Model {
    protected $field = true;

    //保存相册
    public function setImgs($help_id){
        $help_images = $_POST['goods_images'];
        if(count($help_images) >= 1){
            $ImagesArr = db('help_images')->where("help_id",$help_id)->column('img_id,image_url'); // 查出所有已经存在的图片
            // 删除图片
            foreach($ImagesArr as $key => $val){
                if(!in_array($val, $help_images))
                    db('help_images')->where("img_id = {$key}")->delete(); //
            }
            // 添加图片
            foreach($help_images as $key => $val){
                if($val == null)  continue;
                if(!in_array($val, $ImagesArr))
                {
                    $data = array(
                        'help_id' => $help_id,
                        'image_url' => $val,
                    );
                    db("help_images")->insert($data); // 实例化User对象
                }
            }
        }

    }

}