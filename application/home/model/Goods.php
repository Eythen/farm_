<?php
/**
 * tpshop
 * ============================================================================

 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 
 * Date: 2015-09-09
 */

namespace app\home\model;
use think\Model;
class Goods extends Model {
    protected $patchValidate = true; // 系统支持数据的批量验证功能，
    /**
     *     
        self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
        self::MUST_VALIDATE 或者1 必须验证
        self::VALUE_VALIDATE或者2 值不为空的时候验证
     *
     * 
        self::MODEL_INSERT或者1新增数据时候验证
        self::MODEL_UPDATE或者2编辑数据时候验证
        self::MODEL_BOTH或者3全部情况下验证（默认）       
     */
    protected $_validate = array(
        array('goods_name','require','商品名称必须填写！',1 ,'',3),         
        //array('cat_id','require','商品分类必须填写！',1 ,'',3),        
        array('cat_id','0','商品分类必须填写。',1,'notequal',3),
        array('goods_sn','','商品货号重复！',2,'unique',1),        
        array('shop_price','/\d{1,10}(\.\d{1,2})?$/','本店售价格式不对。',2,'regex'),        
        array('member_price','/\d{1,10}(\.\d{1,2})?$/','会员价格式不对。',2,'regex'),        
        array('market_price','/\d{1,10}(\.\d{1,2})?$/','市场价格式不对。',2,'regex'), // currency
        array('weight','/\d{1,10}(\.\d{1,2})?$/','重量格式不对。',2,'regex'),
        array('exchange_integral','checkExchangeIntegral','积分抵扣金额不能超过商品总额',0,'callback'),
     );   
    
    
    
    /**
     * 后置操作方法
     * 自定义的一个函数 用于数据保存后做的相应处理操作, 使用时手动调用
     * @param int $goods_id 商品id
     */
    public function afterSave($goods_id)
    {            
         // 商品货号
         $goods_sn = "sn".str_pad($goods_id,7,"0",STR_PAD_LEFT);   
         $this->where("goods_id = $goods_id and goods_sn = ''")->update(array("goods_sn"=>$goods_sn)); // 根据条件更新记录
         // 商品图片相册  图册
         if(count($_POST['goods_images']) > 1)
         {                          
             array_pop($_POST['goods_images']); // 弹出最后一个             
             $goodsImagesArr = db('GoodsImages')->where("goods_id = $goods_id")->column('img_id,image_url'); // 查出所有已经存在的图片
             
             // 删除图片
             foreach($goodsImagesArr as $key => $val)
             {
                 if(!in_array($val, $_POST['goods_images']))
                     db('GoodsImages')->where("img_id = {$key}")->delete(); // 
             }
             // 添加图片
             foreach($_POST['goods_images'] as $key => $val)
             {
                 if($val == null)  continue;                                  
                 if(!in_array($val, $goodsImagesArr))
                 {                 
                        $data = array(
                            'goods_id' => $goods_id,
                            'image_url' => $val,
                        );
                        db("GoodsImages")->insert($data); // 实例化User对象                     
                 }
             }
         }
         // 查看主图是否已经存在相册中
         $c = db('GoodsImages')->where("goods_id = $goods_id and image_url = '{$_POST['original_img']}'")->count(); 
         if($c == 0 && $_POST['original_img'])
         {
             db("GoodsImages")->insert(array('goods_id'=>$goods_id,'image_url'=>$_POST['original_img'])); 
         }
         
         // 商品规格价钱处理
        $specGoodsPrice = db("SpecGoodsPrice"); // 实例化 商品规格 价格对象
        $specGoodsPrice->where('goods_id = '.$goods_id)->delete(); // 删除原有的价格规格对象         
         if($_POST['item'])
         {
             $spec = db('Spec')->column('id,name'); // 规格表
             $specItem = db('SpecItem')->column('id,item');//规格项
                          
             foreach($_POST['item'] as $k => $v)
             {
                   // 批量添加数据
                   $v['price'] = trim($v['price']);
                   $store_count = $v['store_count'] = trim($v['store_count']); // 记录商品总库存
                   $v['sku'] = trim($v['sku']);
                   $dataList[] = array('goods_id'=>$goods_id,'key'=>$k,'key_name'=>$v['key_name'],'price'=>$v['price'],'store_count'=>$v['store_count'],'sku'=>$v['sku']);                                      
             }             
             $specGoodsPrice->insertAll($dataList);             
             //db('Goods')->where("goods_id = 1")->save(array('store_count'=>10)); // 修改总库存为各种规格的库存相加           
         }   
         
         // 商品规格图片处理
         if($_POST['item_img'])
         {    
             db('SpecImage')->where("goods_id = $goods_id")->delete(); // 把原来是删除再重新插入
             foreach ($_POST['item_img'] as $key => $val)
             {                 
                 db('SpecImage')->insert(array('goods_id'=>$goods_id ,'spec_image_id'=>$key,'src'=>$val));
             }                                                    
         }
         refresh_stock($goods_id); // 刷新商品库存
    }

    /**
     * 检查积分兑换
     * @author dyr
     * @return bool
     */
    protected function checkExchangeIntegral()
    {
        $exchange_integral = input('exchange_integral', 0);
        $shop_price = input('shop_price', 0);
        $point_rate_value = tpCache('shopping.point_rate');
        $point_rate_value = empty($point_rate_value) ? 0 : $point_rate_value;
        if ($exchange_integral > ($shop_price * $point_rate_value)) {
            return false;
        } else {
            return true;
        }
    }
}
