<?php
namespace app\home\controller;
use think\Controller;
use think\File;
use think\Validate;
use think\Loader;

class Upload extends Base{
    
    /**
     *  [uploadfile 批量导入用户] 
     *  
     */
    public  function uploadfile(){
        // 上传图片框中的描述表单名称，
        $filename = htmlspecialchars($_POST['Filename'], ENT_QUOTES); 
        $path = 'test';
        $file = request()->file('file');

        $file_dir_path = REAL_PATH.'public' . "/" . $path . "/";

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate([ 'size' => 20000000, 'ext' => 'zip,rar,doc,docx,zip,pdf,txt,ppt,pptx,xls,xlsx' ])->move($file_dir_path);
 
        if ($info) {        
            $result = action('PHPExcel/read',array($file_dir_path, $info->getSaveName(), '0', '2') );



            if ($result['status']) {              
                $data = $result['info'];
                //处理重复
                $exists = '';
                $tel = [];

                foreach ($data as $k => $v) {
                    $has = db('users')->where('mobile',$v['A'])->value('mobile');
                    if($has){
                        $exists .= $v['A']."<br>";
                    }
                    else{

                        $users[$k]['mobile'] = $v['A'];
                        $users[$k]['password'] = md5(md5('123456').config('md5_key'));
                        $users[$k]['pay_code'] = md5(md5('789').config('md5_key'));
                        $users[$k]['nickname'] = $v['A'];
                        array_push($tel, $v['A']);
                    }

                }
                if(!empty($tel)){

                    // 获取去掉重复数据的数组 
                    $unique_arr = array_unique ( $tel ); 
                    // 获取重复数据的数组 
                    $repeat_arr = array_diff_assoc ( $tel, $unique_arr ); 
                    if(!empty($repeat_arr)){
                        $info = '不能导入！excel表中存在以下手机号重复，请先处理好！<br>'.implode('<br>', $repeat_arr);
                        $return = array('status' => 1, 'info' => $info );
                        return json_encode($return);
                    }
                }

                //获取手机号码后删除上传表格
                //unlink('E:\work\duxu\public\public/test/20171114\1b6823dff135821cd686acc304e917a9.xls');
                //$link = "'".$file_dir_path.$info->getSaveName()."'";
                //$fp = fopen($file_dir_path.$info->getSaveName(), 'r');
                //fclose($fp);//添加关闭文件的操作，任何时候都不要忘记
                //unlink($file_dir_path.$info->getSaveName());

                if(empty($exists)){
                    db()->startTrans();
                    try{

                        db('users')->insertAll($users);
                        db()->commit();
                        $info = '导入成功！<br>'.implode('<br>', $tel);
                    }
                    catch (Exception $e){
                        db()->rollback();
                        $info = '导入失败！<br>'.implode('<br>', $tel);
                    }
                }
                else{
                    $info = '不能导入！已经网站上存在以下手机号，请先处理好！<br>'.$exists;
                }

                $return = array('status' => 1, 'info' => $info );

            } else {
                $return = array('status' => 0, 'info' => $result['info']);
            }
        } 
        else {
            $return = array('status' => 0, 'info' => $file->getError());
        }

        return json_encode($return);

    }
    
    /***
     *   [readexeldata  读取exel文档的数据]
     * @param string $filename 文件名（包含完整的路径）
     * @param string $exts     文件扩展名 （xls 或者 xlsx）   
     * @param string $timecolum  需要时间转换的列号 (ABCDEFGHIJK)
     * @param int $startRow  从第几行开始读取数据
     * @return array  返回以data[1][A]形式的二位数组
     */
    public function readexeldata($filename,$exts="xls",$timecolum="",$startRow=1){

        Loader::import('PHPExcel.PHPExcel');
                //$auth = new \Auth();
        //import("Org.Util.PHPExcel");
        $PHPExcel=new \PHPExcel();
        
        
        //如果excel文件后缀名为.xls，导入这个类
        if($exts == 'xls'){
            import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader=new \PHPExcel_Reader_Excel5();
        }else if($exts == 'xlsx'){
            import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader=new \PHPExcel_Reader_Excel2007();
        }
        
        
        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=$startRow;$currentRow<=$allRow;$currentRow++){
             
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
              
                if($currentColumn==$timecolum && $currentRow >1){
        
                    $cell =$this->excelTime($currentSheet->getCell($address)->getValue());
        
        
        
                }else{
        
        
                    $cell =$currentSheet->getCell($address)->getValue();
                    if($cell instanceof \PHPExcel_RichText){
                        $cell  = $cell->__toString();
                    }
                     
                }
                 
                $data[$currentRow][$currentColumn] = $cell;
            }
        
        }
        
      return $data;
        
    }
    
    
    
    /**
     * [excelTime  excel中的时间格式转化为 YY-mm-dd格式]
     * @param string $date
     * @param string $time
     * @return string
     */
  public  function excelTime($date, $time = false) {
    
        if(function_exists('GregorianToJD')){
    
            if (is_numeric( $date )) {
    
                $jd = GregorianToJD( 1, 1, 1970 );
    
                $gregorian = JDToGregorian( $jd + intval ( $date ) - 25569 );
    
                $date = explode( '/', $gregorian );
    
                $date_str = str_pad( $date [2], 4, '0', STR_PAD_LEFT )
    
                ."-". str_pad( $date [0], 2, '0', STR_PAD_LEFT )
    
                ."-". str_pad( $date [1], 2, '0', STR_PAD_LEFT )
    
                . ($time ? " 00:00:00" : '');
    
                return $date_str;
    
            }
    
        }else{
    
            $date=$date>25568?$date+1:25569;
            $ofs=(70 * 365 + 17+2) * 86400;
            $date = date("Y-m-d",($date * 86400) - $ofs).($time ? " 00:00:00" : '');
    
        }
    
        return $date;
    
    }
    
    
    
    /**
     *  [uploadfile 上传文件]
     * @param array $files 传过来的$_FILES
     *
     */
    public  function uploadimg($files){
        $path = 'test';
        $file = request()->file('file');

        $file_dir_path = REAL_PATH.'public' . "/" . $path . "/";

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate([ 'size' => 20000000, 'ext' => 'jpg, png, gif, jpeg' ])->move($file_dir_path);


        if ($info) {
            $state = "SUCCESS";         
            $return_data['url'] = '/public' . "/" . $path . "/" .   date('Ymd') .'/'.  $info->getFilename();
            $return_data['title'] = $filename;
            $return_data['original'] = $filename;
            $return_data['state'] = $state;
            return $return_data['url'];
        } else {
            $state = "ERROR" . $file->getError();
            return 0;
        }
        /*$data = array();
        //图片上传设置
        $config = array(
            'maxSize'    =>    3145728,
            'rootPath'   =>    'Public/Uploads/',
            'savePath'   =>    date('Y').'/'.date('m').'/'.date('d').'/',
            'saveName'   =>    array('date','YmdHis'),
            'exts'       =>    array('jpg', 'png','gif'),
            'autoSub'    =>    false,
            'subName'    =>    array('date','Ymd'),
        );
      
    
        $upload = new \Think\Upload($config,'Local'); // 实例化上传类
        $file_info = $upload->upload();
    
        if($file_info){
            //$filename = "Public/uploads/2016/04/22/123.xls";
            $filename = $upload->rootPath.$upload ->savePath.$file_info['filedata']['savename'];
            //$exts = $file_info['filedata']['ext'];
            return $filename;
        }else{
    
            return 0;
        }*/
    
    }

    /**
     *  [file 上传文件]
     * @param int $type 上传文件类型
     * @return [array] $data  [上传结果]
     */
    public  function file($type = 0){
        if(!empty($_FILES)){
            switch ($type) {
                case 1:
                    $filetype = array('jpg', 'jpeg', 'png', 'gif');
                    break;
                case 2:
                    $filetype = array('xls', 'xlsx', 'doc', 'docx');
                    break;
                case 3:
                    $filetype = array('avi', 'mp4', 'rmvb', '3gp');
                    break;
                default:
                    $filetype = array('xls', 'xlsx', 'doc', 'docx','jpg', 'jpeg', 'png', 'gif','pdf','ppt', 'pptx','avi', 'mp4', 'rmvb', '3gp');
                    break;
            }

            $filetype = "'".implode($filetype, ',')."'";
            
            $filename = htmlspecialchars($_POST['Filename'], ENT_QUOTES); 
            $path = 'test';
            $file = request()->file('Filedata');

            $file_dir_path = REAL_PATH.'public' . "/" . $path . "/";

            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate([ 'size' => 20000000, 'ext' => $filetype ])->move($file_dir_path);


           /* if ($info) {
                $state = "SUCCESS";         
            } else {
                $state = "ERROR" . $file->getError();
            }

            $return_data['url'] = '/public' . "/" . $path . "/" .   date('Ymd') .'/'.  $info->getFilename();
            $return_data['title'] = $filename;
            $return_data['original'] = $filename;
            $return_data['state'] = $state;
*/
            $data = array();
            if($info){
                //压缩图片
                //resizeImg(WEB_ROOT.$upload->rootPath.$upload ->savePath.strtolower($file_info['Filedata']['savename']));
                $data['status'] = 1;
                $data['path']   = '/public' . "/" . $path . "/" .   date('Ymd') .'/'.  $info->getFilename();
                $data['name']   = $filename;
                $data['ext']    = $info->getExtension();
                $data['time']   = date('Y-m-d H:i:s');
                $data['info']   = config('MSG.UPLOAD_SUCCESS');
            }else{
                $data['status'] = 0;
                if (!in_array(strtolower(pathinfo($_FILES['Filedata']['name'])['extension']),$filetype)) {
                    $data['info'] = '上传格式不对，请重新上传！';
                }
                if ($_FILES['Filedata']['size'] > 20000000) {
                    $data['info'] = '上传文件太大，请压缩后重新上传';
                }
                if (!isset($data['info'])) {
                    $data['info'] = config('MSG.UPLOAD_ERROR');
                }
            }
            return $data;
        }
    }

    /**
     * [delete 文件删除]
     */
    public function fileDelete() {
        if (request()->isPost()) {
            $file_url = '.'.input('file_url');
            if (empty($file_url)) {
                $this->error(config('MSG.DELETE_ERROR').'1');
            }
            $file_url2=mb_convert_encoding($file_url, 'GB2312', 'UTF-8');
            if (!file_exists($file_url)) {
                $this->error(config('MSG.DELETE_ERROR').'2');
            }
            if (!unlink($file_url)) {
                $this->error(config('MSG.DELETE_ERROR').'3');
            }
            $this->success(config('MSG.DELETE_SUCCESS'));
        }
    }

    /**
     * [uploadTel 上传电话号码]
     */
    public function uploadTel() {
        
        $filename = htmlspecialchars($_POST['Filename'], ENT_QUOTES); 
        $path = 'test';
        $file = request()->file('file');

        $file_dir_path = REAL_PATH.'public' . "/" . $path . "/";

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate([ 'size' => 20000000, 'ext' => 'zip,rar,doc,docx,zip,pdf,txt,ppt,pptx,xls,xlsx' ])->move($file_dir_path);

        if ($info) {        
            $result = action('PHPExcel/read',array($file_dir_path, $info->getSaveName(), '0', '2') );

            if ($result['status']) {              
                $data = $result['info'];
                foreach ($data as $key => $value) {
                    $tel[] = $value['A'];
                }

                $return = array('status' => 1, 'info' => implode(',', $tel));
                //获取手机号码后删除上传表格
                //$link = "'".$file_dir_path.$info->getSaveName()."'";
                //unlink($link);
            } else {
                $return = array('status' => 0, 'info' => $result['info']);
            }
        } 
        else {
            $return = array('status' => 0, 'info' => $file->getError());
        }


        return json_encode($return);

    }
}