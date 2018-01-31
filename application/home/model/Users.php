<?php
/**
 * 用户模型
 */
namespace app\home\model;
use think\Model;

class Users extends Model {
    //最大错误次数
    const MAX_ERROR_TIMES = 5;          //重试5次
    //缓存时间
    const CACHE_TIME = 1800;            //缓存30分钟
	//错误次数
	private $_errorTimes = 1;

    //上级领导
    //private $parent;
    protected $parent;


    /**
     * [setInfo 设置用户信息]
     */
    public function setInfo(){
        if (session('?uname')) {
            return;
        }
        
        //获取个人基本信息
       /* $userInfo = model('Department')->getUser(session('uid'));
        if (!$userInfo) {
            if ($this->_errorTimes == SELF::MAX_ERROR_TIMES) {
                exit(config('MSG.EHR_ERROR'));
            }
            $this->_errorTimes++;
            $this->setInfo();
        }*/
        //获取所属部门
       /* $depInfo = model('Department')->getTop($userInfo['framework_id']);
        if (!$depInfo) {
            if ($this->_errorTimes == SELF::MAX_ERROR_TIMES) {
                exit(config('MSG.EHR_ERROR'));
            }
            $this->_errorTimes++;
            $this->setInfo();
        }*/
        $userInfo = db('users')->find(session('uid'));
        session('uname', $userInfo['username']);                //保存用户名
        session('phone', $userInfo['phone']);                   //保存手机号
        //session('avatar', format_avatar($userInfo['avatar'])); //保存用户头像
        /*
        session('position_id', $userInfo['framework_id']);      //保存岗位ID
        session('position_name', $userInfo['framework_name']);  //保存岗位名称
        */
    }

    /**
     * [info 获取用户信息]
     */
    public function info(){
        if (!isset($_SESSION['uid'])) {
            return;
        }
        $user = array();
        $user['uname'] = $_SESSION['uname'];                  //用户名
        $user['phone'] = $_SESSION['phone'];                  //手机号
        $user['avatar'] = $_SESSION['avatar'];                //用户头像
        $user['dept_id'] = $_SESSION['dept_id'];              //部门ID
        $user['dept_name'] = $_SESSION['dept_name'];          //部门名称
        $user['position'] = $_SESSION['position'];            //职位名称
        $user['parent_id'] = $_SESSION['parent_id'];          //上级ID
        $user['filiale_id'] = $_SESSION['filiale_id'];        //所属分公司
        $user['position_id'] = $_SESSION['position_id'];      //岗位ID
        $user['position_name'] = $_SESSION['position_name'];  //岗位名称
        return $user;
    }

    /**
     * [get_parent 获取上级领导]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_parent($uid){
        $parent = array();
        $userlist = $this->getAll();
        //dump($userlist);
        die;
        /*if ($uid != 1) {
            $this->parent[$uid] = $userlist[$uid];
            if ($userlist[$uid]['parent_id'] != 1) {
               $this->get_parent($userlist[$uid]['parent_id']);
            }
        }*/
        return ;//$this->parent;
    }

    /**
     * [getInfo 获取用户信息]
     * @param  integer $id    [用户ID]
     * @param  integer $type  [是否获取实时数据 0-不使用 1-使用]
     * @return [array]        [返回部门用户列表]
     */
    public function getInfo($id,$type = 0){
        $cacheText = 'USER_'.$id;       //缓存名称
        if ($type) {
            //$userInfo = model('Department')->getUser($id);
            $userInfo = db('users')->find(session('uid'));
            if (!$userInfo) {
                if ($this->_errorTimes == self::MAX_ERROR_TIMES) {
                    return false;
                }
                $this->_errorTimes++;
                return $this->getInfo($id,$type);
            }
            cache($cacheText, $userInfo, self::CACHE_TIME);
            return $userInfo;
        }else{
            if (cache($cacheText)) {
                return cache($cacheText);
            }else{
                return $this->getInfo($id,1);
            }
        }
    }

    /**
     * [getPhone 根据ID获取用户手机]
     * @param  [int] $id [用户ID]
     * @return [string]     [返回用户手机号码]
     */
    public function getPhone($id){
        if (empty($id)) {
            return;
        }
        $userlist = $this->getAll();
        $user = $userlist[$id];
        if (!$user) {
            return;
        }
        return $user['phone'];
    }

    /**
     * [getName 根据ID获取用户姓名]
     * @param  [int] $id    [用户ID]
     * @return [string]     [返回用户姓名]
     */
    public function getName($id){
        if (empty($id)) {
            return;
        }
        $userlist = $this->getAll();
        $user = $userlist[$id];
        if (!user) {
            return;
        }
        return $user['username'];
    }

    /**
     * [findName 根据用户名获取用户信息]
     * @param  string $name  [用户名]
     * @param  integer $type  [是否获取实时数据 0-不使用 1-使用]
     * @return [array]        [返回部门用户列表]
     */
    public function findName($name,$type = 0){
        $cacheText = 'USER_'.urlencode($name);       //缓存名称
        if ($type) {
            $userInfo = model('Department')->findName($name);
            if (!$userInfo) {
                if ($this->_errorTimes == self::MAX_ERROR_TIMES) {
                    return false;
                }
                $this->_errorTimes++;
                return $this->findName($name,$type);
            }
            cache($cacheText, $userInfo, self::CACHE_TIME);
            return $userInfo;
        }else{
            if (cache($cacheText)) {
                return cache($cacheText);
            }else{
                return $this->findName($name,1);
            }
        }
    }

    /**
     * [findPhone 根据用户名获取用户信息]
     * @param  integer $phone [用户手机]
     * @param  integer $type  [是否获取实时数据 0-不使用 1-使用]
     * @return [array]        [返回部门用户列表]
     */
    public function findPhone($phone,$type = 0){
        $cacheText = 'USER_'.$phone;       //缓存名称
        if ($type) {
            $userInfo = model('Department')->findPhone($phone);
            if (!$userInfo) {
                if ($this->_errorTimes == self::MAX_ERROR_TIMES) {
                    return false;
                }
                $this->_errorTimes++;
                return $this->findPhone($phone,$type);
            }
            cache($cacheText, $userInfo, self::CACHE_TIME);
            return $userInfo;
        }else{
            if (cache($cacheText)) {
                return cache($cacheText);
            }else{
                return $this->findPhone($phone,1);
            }
        }
    }

    /**
     * [getDep 获取部门用户]
     * @param  integer $id          [部门ID]
     * @param  integer $child       [是否递归 0-不递归 1-递归]
     * @param  integer $type        [是否获取实时数据 0-不使用 1-使用]
     * @param  integer $filiale_id  [分公司id 0：广州总公司 1：成都分公司 2：南京分公司 3：北京分公司]
     * @return [array]              [返回部门用户列表]
     */
    public function getDep($id, $child = 1, $type = 0, $filiale_id = ''){
        $cacheText = 'DEP_LIST_'.$id.'_'.$child.'_'.$filiale_id;            //缓存名称
        if ($type) {
            $userInfo = model('Department')->getUserList($id,$child,$filiale_id);
            if (!$userInfo) {
                if ($this->_errorTimes == self::MAX_ERROR_TIMES) {
                    return false;
                }
                $this->_errorTimes++;
                return $this->getDep($id,$child,$type,$filiale_id);
            }
            $result = array();
            foreach ($userInfo as $key => $value) {
                $result[$value['uid']] = $value;
            }
            cache($cacheText, $result, self::CACHE_TIME);
            return $result;
        }else{
            if (cache($cacheText)) {
                return cache($cacheText);
            }else{
                return $this->getDep($id,$child,1,$filiale_id);
            }
        }
    }

    /**
     * [getGroup 获取部门列表]
     * @param  integer $id          [部门ID]
     * @param  integer $child       [是否递归 0-不递归 1-递归]
     * @param  integer $type        [是否获取实时数据 0-不使用 1-使用]
     * @return [array]              [返回部门列表]
     */
    public function getGroup($id, $child = 1, $type = 0){
        $cacheText = 'GROUP_LIST_'.$id.'_'.$child;            //缓存名称
        if ($type) {
            $groupList = model('Department')->getList($id,$child);
            if (!$groupList) {
                if ($this->_errorTimes == self::MAX_ERROR_TIMES) {
                    return false;
                }
                $this->_errorTimes++;
                return $this->getGroup($id,$child,$type);
            }
            $result = array();
            foreach ($groupList as $key => $value) {
                $result[$value['id']] = $value;
            }
            cache($cacheText, $result, self::CACHE_TIME);
            return $result;
        }else{
            if (cache($cacheText)) {
                return cache($cacheText);
            }else{
                return $this->getGroup($id,$child,1);
            }
        }
    }

    /**
     * [getAll 获取全部用户]
     * @param  integer $type        [是否获取实时数据 0-不使用 1-使用]
     * @param  integer $filiale_id  [分公司id]
     * @return [array]              [返回全部用户列表]
     */
    public function getAll($type = 0, $filiale_id = ''){
        return $this->getDep(1,1,$type, $filiale_id);
    }

    /**
     * [getTz 获取全部投资中心员工]
     * @param  integer $type        [是否获取实时数据 0-不使用 1-使用]
     * @param  integer $filiale_id  [分公司id]
     * @return [array]              [返回全部用户列表]
     */
    public function getTz($type = 0, $filiale_id = ''){
        $res = $this->getDep($this->_getTzId(),1,$type,$filiale_id);
        if (empty($filiale_id)) {
            $leader = $this->getInfo(5);            //徐总
            if ($leader) {
                $res[5] = $leader;
            }
        }
        return $res;
    }

    /**
     * [getYy 获取全部运营部员工]
     * @param  integer $type        [是否获取实时数据 0-不使用 1-使用]
     * @param  integer $filiale_id  [分公司id]
     * @return [array]              [返回全部用户列表]
     */
    public function getYy($type = 0, $filiale_id = ''){
        return $this->getDep($this->_getYyId(),1,$type,$filiale_id);
    }

    /**
     * [getCw 获取全部财务部员工]
     * @param  integer $type        [是否获取实时数据 0-不使用 1-使用]
     * @return [array]              [返回全部用户列表]
     */
    public function getCw($type = 0){
        return $this->getDep($this->_getCwId(),1,$type);;
    }

    /**
     * [getTg 获取全部推广部员工]
     * @param  integer $type        [是否获取实时数据 0-不使用 1-使用]
     * @param  integer $format      [是否只输出姓名   0-不输出 1-输出]
     * @return [array]              [返回全部用户列表]
     */
    public function getTg($type = 0,$format = 0){
        $res = $this->getDep($this->_getTgId(),1,$type);
        if ($res && $format == 1) {
            $list = array();
            foreach ($res as $k => $v) {
                $list[] = $v['username'];
            }
            $res = $list;
        }
        return $res;
    }

    /**
     * [login_log 登陆日志]
     */
    public function login_log(){
        $d['admin_id'] = session('uid');
        $d['username'] = session('uname');
        $d['time'] = date('Y-m-d H:i:s');
        $d['ip'] = request()->ip();
        $d['platform'] = $this-> getOcache();
        $d['browser'] = $this->getBrowser();
        $d['browserversion'] = $this->getBrowserVer();
        db('login_log')->insert($d);
    }

    /**
     * [getBrowser 获取浏览器]
     * @return [string] [浏览器名]
     */
    public function getBrowser(){
    $agent=$_SERVER["HTTP_USER_AGENT"];
    if(strstr($agent,'MSIE')!==false || strstr($agent,'rv:11.0')){ //ie11判断
        return "ie";
    }
    else if(strstr($agent,'Firefox')!==false){
        return "firefox";
    }
    else if(strstr($agent,'Chrome')!==false){
        return "chrome";
    }
    else if(strstr($agent,'Opera')!==false){
        return 'opera';
    }
    else if((strstr($agent,'Chrome')==false)&&strstr($agent,'Safari')!==false){
        return 'safari';
    }
    else{
            return 'unknown';
        }
    }

    /**
     * [getBrowserVer 获取浏览器版本]
     * @return [string] [版本]
     */
    public function getBrowserVer(){
        if (empty($_SERVER['HTTP_USER_AGENT'])){ //当浏览器没有发送访问者的信息的时候
            return 'unknow';
        }
        $agent= $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs)){
            return $regs[1];
        }
        elseif (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs)){
            return $regs[1];
        }
        elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs)){
            return $regs[1];
        }
        elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs)){
            return $regs[1];
        }
        elseif ((strstr($agent,'Chrome')==false)&&preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs)){
            return $regs[1];
        }
        else{
            return 'unknow';
        }
    }
    /**
     * [getOS 获取操作系统]
     * @return [type] [操作系统]
     */
    public function getOcache(){
        $os='';
        $Agent=$_SERVER['HTTP_USER_AGENT'];

        if (stristr($Agent, 'win')&&strstr($Agent, '95')){
            $os='Windows 95';
        }elseif(stristr($Agent, 'win 9x')&&strstr($Agent,'4.90')){
            $os='Windows ME';
        }elseif(stristr($Agent, 'win')&&strstr($Agent, '98')){
            $os='Windows 98';
        }elseif(stristr($Agent, 'win')&&stristr($Agent, 'nt 5.0')){
            $os='Windows 2000';
        }elseif(stristr($Agent, 'win')&&stristr($Agent, 'nt 6.0')){
            $os='Windows Vista';
        }elseif(stristr($Agent, 'win')&&stristr($Agent, 'nt 6.1')){
            $os='Windows 7';
        }elseif(stristr($Agent, 'win')&&stristr($Agent, 'nt 6.2')){
            $os='Windows 8';
        }elseif(stristr($Agent, 'win')&&stristr($Agent, 'nt 6.3')){
            $os='Windows 8.1';
        }elseif(stristr($Agent, 'win')&&stristr($Agent, 'nt 10.0')){
            $os='Windows 10';
        }elseif(stristr($Agent, 'win')&&stristr($Agent, 'nt 5.1')){
            $os='Windows XP';
        }elseif(stristr($Agent, 'win')&&stristr($Agent, 'nt')){
            $os='Windows NT';
        }elseif(stristr($Agent, 'win')&&strstr($Agent, '32')){
            $os='Windows 32';
        }elseif(stristr($Agent, 'linux')){
            $os='Linux';
        }elseif(stristr($Agent, 'unix')){
            $os='Unix';
        }else if(stristr($Agent, 'sun')&&stristr($Agent, 'os')){
            $os='SunOS';
        }elseif(stristr($Agent, 'ibm')&&stristr($Agent, 'os')){
            $os='IBM OS/2';
        }elseif(stristr($Agent, 'Mac')&&stristr($Agent, 'PC')){
            $os='Macintosh';
        }elseif(stristr($Agent, 'PowerPC')){
            $os='PowerPC';
        }elseif(stristr($Agent, 'AIX')){
            $os='AIX';
        }elseif(stristr($Agent, 'HPUX')){
            $os='HPUX';
        }elseif(stristr($Agent, 'NetBSD')){
            $os='NetBSD';
        }elseif(stristr($Agent, 'BSD')){
            $os='BSD';
        }elseif(strstr($Agent, 'OSF1')){
            $os='OSF1';
        }elseif(strstr($Agent, 'IRIX')){
            $os='IRIX';
        }elseif(stristr($Agent, 'FreeBSD')){
            $os='FreeBSD';
        }elseif($os==''){
            $os='Unknown';
        }
        return $os;
    }
}