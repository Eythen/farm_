<?php

namespace app\api\model;


use app\lib\exception\UserAddressException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;
use think\Model;

class UserAddress extends Model
{
    protected $hidden = ['email','country','province','city','district','twon','zipcode','is_pickup'];

    public static function createOrUpdateAddress($user_id,$data){
        $user = Users::get($user_id);
        if (!$user){
            throw new UserException();
        }

        if (array_key_exists('address_id',$data)){//update
            $address = self::get(['address_id'=>$data['address_id'],'user_id'=>$user_id]);
            if (!$address){
                throw new UserAddressException();
            }
            Db::startTrans();
            try{
                $address->consignee = $data['consignee'];
                $address->address = $data['address'];
                $address->mobile = $data['mobile'];
                $address->is_default = $data['is_default'];
                $address->user_id = $user_id;
                if ($data['is_default'] == 1){//is_default
                    self::where(['user_id'=>$user_id])->setField('is_default',0);
                    $user->save(['address_id'=>$address->address_id]);
                }
                $address->save();
                Db::commit();
                return true;
            }
            catch (Exception $e){
                Db::rollback();
                return false;
            }

        }else{//insert
            Db::startTrans();
            try{
                $address = self::create([
                    'consignee' => $data['consignee'],
                    'address' => $data['address'],
                    'mobile' => $data['mobile'],
                    'is_default' => $data['is_default'],
                    'user_id' => $user_id,
                ]);
                if ($data['is_default'] == 1){//is_default
                    $where['user_id'] = $user_id;
                    $where['address_id'] = ['<>',$address->address_id];
                    self::where($where)->setField('is_default',0);
                    $user->save(['address_id'=>$address->address_id]);
                }
                Db::commit();
                return true;
            }
            catch (Exception $e){
                Db::rollback();
                return false;
            }
        }
    }
}