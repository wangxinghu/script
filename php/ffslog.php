<?php
defined('SYS_PATH') or die('No direct script access.');
include_once(APP_PATH . 'helpers' . DIRECTORY_SEPARATOR . 'functions.php');
/**
 * Log class.
 *
 * Usage:
 * 1.  prepare struct.conf . (Ko::config)
 *     'logPath'   => '/mnt/htdocs/log', //提供绝对路径，日志存放的根目录
 *     'logLevel'   =>  16, # 日志级别
                             #  1：打印FATAL
                             #  2：打印FATAL和WARNING
                             #  4：打印FATAL、WARNING、NOTICE（）
                             #  8：打印FATAL、WARNING、NOTICE、TRACE（）
                             # 16：打印FATAL、WARNING、NOTICE、TRACE、DEBUG（测试环境配置,）
 *     'logUseSubdir'   => yes, //日志文件路径是否增加一个基于app名称的子目录，例如：log/some-app/some-app.log
 *     'logAutoRotate'   => yes, //是否按小时自动分日志，设置为1时，日志被打在some-app.log.2011010101
 *     'logFormat'   => "%L: %t [%f:%N] errno[%E] logId[%l] uri[%U] uid[%u] snsid[%s] refer[%{referer}i] cookie[%{cookie}i]\t%S\t%M",
 *  2.
 *      define('APP', 'ffs');
 *  3.
 *      FfsLog::debug("debug test", array('arr1' => '123', 'arr2' => array('test' => 'val')));
 *      FfsLog::debug("debug2 test");
 *      FfsLog::trace("trace test");
 *      FfsLog::warning("warning test");
 *      FfsLog::fatal("fatal test");
 *
 */
class FfsLog
{
    const LOG_LEVEL_FATAL   = 0x0;
    const LOG_LEVEL_WARNING = 0x02;
    const LOG_LEVEL_NOTICE  = 0x04;
    const LOG_LEVEL_TRACE   = 0x08;
    const LOG_LEVEL_DEBUG   = 0x10;


    public static $arrLogLevels = array(
        self::LOG_LEVEL_FATAL   => 'FATAL',
        self::LOG_LEVEL_WARNING => 'WARNING',
        self::LOG_LEVEL_NOTICE  => 'NOTICE',
        self::LOG_LEVEL_TRACE    => 'TRACE',
        self::LOG_LEVEL_DEBUG   => 'DEBUG',
    );

    protected $cache;

    protected $strFormat;
    protected $strFormatWF;
    protected $logPath;
    protected $intLevel;
    protected $bolAutoRotate;
    protected $isLocalLog;
    protected $isRemoteLog;
    protected $isCacheLog;
    protected $logFormatCachePath;

    protected $addNotice = array();

    private static $arrInstance = array();
    public static $current_instance;

    //const DEFAULT_FORMAT = '%L: %t [%f:%N] errno[%E] logId[%l] uri[%U] uid[%u] snsid[%s] refer[%{referer}i] cookie[%{cookie}i]\t%S\t%M';
    //const DEFAULT_FORMAT_STD = '%L: %{%m-%d %H:%M:%S}t %{app}x * %{pid}x [logid=%l filename=%f lineno=%N errno=%{err_no}x %{encoded_str_array}x errmsg=%{u_err_msg}x]';
    const DEFAULT_FORMAT = "level[%L] date[%t] ts[%d] file[%f] num[%N] host[%V] uri[%U] clientIP[%h] localIP[%A] logId[%l] uid[%u] snsid[%s] lang[%a] scene[%c] errno[%E] errmsg[%M]%S";
    const DEFAULT_LOGPATH = "/mnt/htdocs/logs";
    const DEFAULT_LEVEL = 16;
    const DEFAULT_CACHE_PATH = '/tmp/ffslog/';
    const DEFAULT_CACHE_MAX   = 10;

    private function __construct($app)
    {
        $structConf = (array)Ko::config('struct');
        $logConf = isset($structConf['log']) ? $structConf['log'] : array();
        // 生成路径
        $this->logPath = isset($logConf['logPath']) ? $logConf['logPath'] : self::DEFAULT_LOGPATH;
        if(isset($logConf['logUseSubdir']) && $logConf['logUseSubdir'] == "yes") {
            $this->logPath = $this->logPath."/$app";
        }
        //get log format
        $this->strFormat = isset($logConf['logFormat']) ?  $logConf['logFormat']  :  self::DEFAULT_FORMAT;
        $this->strFormatWF =  isset($logConf['logFormatWF']) ? $logConf['logFormatWF'] : $this->strFormat;
        $this->intLevel = isset($logConf['logLevel']) ? intval($logConf['logLevel']) : self::DEFAULT_LEVEL;
        $this->bolAutoRotate = (isset($logConf['logAutoRotate']) && $logConf['logAutoRotate'] == 'yes') ? true : false;
        $this->isLocalLog = (isset($logConf['isLocalLog']) && $logConf['isLocalLog'] == 'yes') ? true : false;
        $this->isRemoteLog = (isset($logConf['isRemoteLog']) && $logConf['isRemoteLog'] == 'yes') ? true : false;
        $this->cacheNum = isset($logConf['cacheNum']) ? intval($logConf['cacheNum']) : self::DEFAULT_CACHE_NUM;
        $this->logFormatCachePath = isset($logConf['logFormatCachePath']) ? $logConf['logFormatCachePath'] : self::DEFAULT_CACHE_PATH;

        if($this->isLocalLog === true && !is_dir($this->logPath)) {
            @mkdir($this->logPath, 0755, true);
        }
        //register_shutdown_function(array($this, 'flushAll'));
    }

    public static function getLogPrefix(){
        if(defined('APP')){
            return APP;
        }else{
            return 'unknow';
        }
    }

    // 获取指定App的log对象，默认为当前App
    public static function getInstance($app = null)
    {
        if(empty($app)) {
            $app = self::getLogPrefix();
        }
        if(empty(self::$arrInstance[$app])) {
            self::$arrInstance[$app] = new FfsLog($app);
        }
        return self::$arrInstance[$app];
    }

    public static function debug($str, $errno = 0, $arrArgs = null,  $depth = 0)
    {
        $ret = self::getInstance()->writeLog(self::LOG_LEVEL_DEBUG, $str, $errno, $arrArgs, $depth + 1);
        return $ret;
    }

    public static function trace($str,  $errno = 0, $arrArgs = null, $depth = 0)
    {
        $ret = self::getInstance()->writeLog(self::LOG_LEVEL_TRACE, $str, $errno, $arrArgs, $depth + 1);
        return $ret;
    }

    public static function notice($str,  $errno = 0, $arrArgs = null, $depth = 0)
    {
        $ret = self::getInstance()->writeLog(self::LOG_LEVEL_NOTICE, $str, $errno, $arrArgs, $depth + 1);
    }

    public static function warning($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        $ret = self::getInstance()->writeLog(self::LOG_LEVEL_WARNING, $str, $errno, $arrArgs, $depth + 1);
    }

    public static function fatal($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        $ret = self::getInstance()->writeLog(self::LOG_LEVEL_FATAL, $str, $errno, $arrArgs, $depth + 1);
    }

    public static function exception($e)
    {
        if ($e  instanceof Exception) {
            $stack_trace = $e->getTrace();
            $class = @$stack_trace[0]['class'];
            $type = @$stack_trace[0]['type'];
            $function = $stack_trace[0]['function'];

            $file = $e->getFile();
            $line = $e->getLine();

            $function = $class != null ?  "$class$type$function" : "";
            $errstr = $e->getMessage();
            $errno = $e->getCode();

            self::getInstance()->writeLog(self::LOG_LEVEL_FATAL, "$errstr at [$function $file:$line] ", $errno, null, 1);
        }
    }

    public static function addNotice($key, $value)
    {
        $log = self::getInstance();
        if ($value === null) {
            $value = '';
        }
        /*
        if(!isset($value)) {
            $value = $key;
            $key = '@';
        }

        $info = is_array($value) ? strtr(strtr(var_export($value, true),
        array("  array (\n"=>'{', "array (\n"=>'{', ' => '=> ':',",\n"=> ',',)),
        array('{  '=> '{', ":\n{"=>':{', '  ),  ' => '},', '),' => '},', ',)'=>'}', ',  '=>',',))
        : $value;
        */
        $log->addNotice[$key] = $value;
    }

    public static function unsetNotice($key) {
        if (empty($key)) {
            return;
        }
        $log = self::getInstance();
        if (isset($log->addNotice[$key])) {
            unset ($log->addNotice[$key]);
        }
    }
    // 生成logid
    public static function genLogID()
    {
        if(defined('LOG_ID')){
            return LOG_ID;
        }
        if(getenv('HTTP_X_BD_LOGID')){
            define('LOG_ID', trim(getenv('HTTP_X_BD_LOGID')));
        }elseif(isset($_REQUEST['logid'])){
            define('LOG_ID', intval($_REQUEST['logid']));
        }else{
            $arr = gettimeofday();
            $logId = ((($arr['sec']*100000 + $arr['usec']/10) & 0x7FFFFFFF) | 0x80000000);
            define('LOG_ID', $logId);
        }
        return LOG_ID;
    }

    // 获取客户端ip
    public static function getClientIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))  {
            $ip = getenv("HTTP_CLIENT_IP"); 
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR"); 
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))  {
            $ip = getenv("REMOTE_ADDR"); 
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR']; 
        } else {
            $ip = "unknown"; 
        }
        return $ip; 
    }

    private function writeLog($intLevel, $str, $errno = 0, $arrArgs = null, $depth = 0, $log_format = null)
    {
        if( $intLevel > $this->intLevel || !isset(self::$arrLogLevels[$intLevel]) ) {
            return;
        }
        $this->time = strftime('%y-%m-%d %H:%M:%S');
        $this->ts = time();

        //assign data required
        $this->current_log_level = self::$arrLogLevels[$intLevel];

        //build array for use as strargs
        $_arr_args = false;
        $_add_notice = false;
        //if (is_array($arrArgs) && count($arrArgs) > 0) {
        if ($arrArgs) {
            $_arr_args = true;
        }
        if (!empty($this->addNotice)) {
            $_add_notice = true;
        }

        if ($_arr_args && $_add_notice) { //both are defined, merge
            $this->current_args = $arrArgs + $this->addNotice;
        } else if (!$_arr_args && $_add_notice) { //only add notice
            $this->current_args = $this->addNotice;
        } else if ($_arr_args && !$_add_notice) { //only arr args
            $this->current_args = $arrArgs;
        } else { //empty
            $this->current_args = array();
        }

        $this->current_err_no = $errno;
        $this->current_err_msg = $str;
        $trace = debug_backtrace();
        $depth2 = $depth + 1;
        if( $depth >= count($trace) ) {
            $depth = count($trace) - 1;
            $depth2 = $depth;
        }
        $this->current_file = isset( $trace[$depth]['file'] ) 
            ? $trace[$depth]['file'] : "" ;
        $this->current_line = isset( $trace[$depth]['line'] ) 
            ? $trace[$depth]['line'] : "";
        $this->current_function = isset( $trace[$depth2]['function'] ) 
            ? $trace[$depth2]['function'] : "";
        $this->current_class = isset( $trace[$depth2]['class'] ) 
            ? $trace[$depth2]['class'] : "" ; 
        $this->current_function_param = isset( $trace[$depth2]['args'] ) 
            ? $trace[$depth2]['args'] : "";

        self::$current_instance = $this;

        //get the format
        $format = $log_format ? $log_format :  $this->getFormat($intLevel);
        $arrLog = $this->getLogString($format);
        $arrLog['arr'] = array_merge($arrLog['arr'], $this->current_args);

        // write to cache
        $prefix = self::getLogPrefix();
        if ( ($intLevel & self::LOG_LEVEL_WARNING) || ($intLevel & self::LOG_LEVEL_FATAL) ) {
            $key = $prefix . 'wf' ;
        } else {
            $key = $prefix;
        }
        $this->cache[$key][] = $arrLog;

        if (count($this->cache[$key]) >= $this->cacheNum) {
            $this->flush($key);
        }

    }

    // added support for self define format
    private function getFormat($level) {
        if ($level == self::LOG_LEVEL_FATAL || $level == self::LOG_LEVEL_WARNING) {
            $fmtstr = $this->strFormatWF;
        } else {
            $fmtstr = $this->strFormat;
        }
        return $fmtstr;
    }

    public function getLogString($format) {
        $md5val = md5($format);
        $func = "_log_$md5val";
        $arrfunc = "arr_log_$md5val";
        if (function_exists($func) && function_exists($arrfunc)) {
            return array('str'=>$func(), 'arr'=>$arrfunc());
        }
        $dataPath = $this->logFormatCachePath;
        $filename = $dataPath . $md5val.'.php';
        if (!file_exists($filename)) {
            $tmp_filename = $filename . '.' . posix_getpid() . '.' . rand();
            if (!is_dir($dataPath)) {
                mkdir($dataPath, 0755, true);
            }
            file_put_contents($tmp_filename, $this->parseFormat($format));
            rename($tmp_filename, $filename);
        }
        include_once($filename);
        return array('str'=>$func(), 'arr'=>$arrfunc());
    }
    // parse format and generate code
    public function parseFormat($format) {
        $matches = array();
        $regex = '/%(?:{([^}]*)})?(.)/';
        preg_match_all($regex, $format, $matches);
        $prelim = array();
        $action = array();
        $prelim_done = array();

        $len = count($matches[0]);
        for($i = 0; $i < $len; $i++) {
            $code = $matches[2][$i];
            $param = $matches[1][$i];
            switch($code) {
            case 'h':
                $action[] = "(defined('CLIENT_IP')? CLIENT_IP : FfsLog::getClientIp())";
                break;
            case 't':
                //$action[] = ($param == '')? "strftime('%y-%m-%d %H:%M:%S')" : "strftime(" . var_export($param, true) . ")";
                $action[] = 'FfsLog::$current_instance->time';
                break;
            case 'd':
                //$action[] = ($param == '')? "strftime('%y-%m-%d %H:%M:%S')" : "strftime(" . var_export($param, true) . ")";
                $action[] = 'FfsLog::$current_instance->ts';
                break;
            case 'i':
                $key = 'HTTP_' . str_replace('-', '_', strtoupper($param));
                $key = var_export($key, true);
                $action[] = "(isset(\$_SERVER[$key])? \$_SERVER[$key] : '')";
                break;
            case 'A':
                $action[] = "(isset(\$_SERVER['SERVER_ADDR'])? \$_SERVER['SERVER_ADDR'] : '')";
                break;
            case 'C':
                if ($param == '') {
                    $action[] = "(isset(\$_SERVER['HTTP_COOKIE'])? \$_SERVER['HTTP_COOKIE'] : '')";
                } else {
                    $param = var_export($param, true);
                    $action[] = "(isset(\$_COOKIE[$param])? \$_COOKIE[$param] : '')";
                }
                break;
            case 'D':
                $action[] = "(defined('REQUEST_TIME_US')? (microtime(true) * 1000 - REQUEST_TIME_US/1000) : '')";
                break;
            case 'e':
                $param = var_export($param, true);
                $action[] = "((getenv($param) !== false)? getenv($param) : '')";
                break;
            case 'f':
                $action[] = 'FfsLog::$current_instance->current_file';
                break;
            case 'H':
                $action[] = "(isset(\$_SERVER['SERVER_PROTOCOL'])? \$_SERVER['SERVER_PROTOCOL'] : '')";
                break;
            case 'm':
                $action[] = "(isset(\$_SERVER['REQUEST_METHOD'])? \$_SERVER['REQUEST_METHOD'] : '')";
                break;
            case 'p':
                $action[] = "(isset(\$_SERVER['SERVER_PORT'])? \$_SERVER['SERVER_PORT'] : '')";
                break;
            case 'q':
                $action[] = "(isset(\$_SERVER['QUERY_STRING'])? \$_SERVER['QUERY_STRING'] : '')";
                break;
            case 'T':
                switch($param) {
                case 'ms':
                    $action[] = "(defined('REQUEST_TIME_US')? (microtime(true) * 1000 - REQUEST_TIME_US/1000) : '')";
                    break;
                case 'us':
                    $action[] = "(defined('REQUEST_TIME_US')? (microtime(true) * 1000000 - REQUEST_TIME_US) : '')";
                    break;
                default:
                    $action[] = "(defined('REQUEST_TIME_US')? (microtime(true) - REQUEST_TIME_US/1000000) : '')";
                }
                break;
            case 'U':
                $action[] = "(isset(\$_SERVER['REQUEST_URI'])? \$_SERVER['REQUEST_URI'] : '')";
                break;
            case 'v':
                $action[] = "(isset(\$_SERVER['HOSTNAME'])? \$_SERVER['HOSTNAME'] : '')";
                break;
            case 'V':
                $action[] = "(isset(\$_SERVER['HTTP_HOST'])? \$_SERVER['HTTP_HOST'] : '')";
                break;

            case 'L':
                $action[] = 'FfsLog::$current_instance->current_log_level';
                break;
            case 'N':
                $action[] = 'FfsLog::$current_instance->current_line';
                break;
            case 'E':
                $action[] = 'FfsLog::$current_instance->current_err_no';
                break;
            case 'l':
                $action[] = "FfsLog::genLogID()";
                break;
            case 's':
                $action[] = "(isset(ConfigModel::\$CURRENT_SNSID) ? ConfigModel::\$CURRENT_SNSID : '')";
                break;
            case 'u':
                $action[] = "(isset(ConfigModel::\$CURRENT_UID) ? ConfigModel::\$CURRENT_UID : '')";
                break;
            case 'a':
                $action[] = "(isset(ConfigModel::\$CURRENT_LANG) ? ConfigModel::\$CURRENT_LANG : '')";
                break;
            case 'c':
                $action[] = "(isset(ConfigModel::\$CURRENT_SCENE) ? ConfigModel::\$CURRENT_SCENE : '')";
                break;
            case 'S':
                if ($param == '') {
                    $action[] = 'FfsLog::$current_instance->getStrArgs()';
                } else {
                    $param_name = var_export($param, true);
                    if (!isset($prelim_done['S_'.$param_name])) {
                        $prelim[] = 
                            "if (isset(FfsLog::\$current_instance->current_args[$param_name])) {
                            \$____curargs____[$param_name] = FfsLog::\$current_instance->current_args[$param_name];
                            unset(FfsLog::\$current_instance->current_args[$param_name]);
                    } else \$____curargs____[$param_name] = '';";
                    $prelim_done['S_'.$param_name] = true;
                    }
                    $action[] = "\$____curargs____[$param_name]";
                }
                break;
            case 'M':
                $action[] = 'FfsLog::$current_instance->current_err_msg';
                break;
            case 'x':
                $need_urlencode = false;
                if (substr($param, 0, 2) == 'u_') {
                    $need_urlencode = true;
                    $param = substr($param, 2);
                }
                switch($param) {
                case 'log_level':
                case 'line':
                case 'class':
                case 'function':
                case 'err_no':
                case 'err_msg':
                    $action[] = 'FfsLog::$current_instance->current_'.$param;
                    break;
                case 'log_id':
                    $action[] = "FfsLog::genLogID()";
                    break;
                case 'app':
                    $action[] = "FfsLog::getLogPrefix()";
                    break;
                case 'function_param':
                    $action[] = 'FfsLog::flattenArgs(FfsLog::$current_instance->current_function_param)';
                    break;
                case 'argv':
                    $action[] = '(isset($GLOBALS["argv"])? FfsLog::flattenArgs($GLOBALS["argv"]) : \'\')';
                    break;
                case 'pid':
                    $action[] = 'posix_getpid()';
                    break;
                case 'encoded_str_array':
                    $action[] = 'FfsLog::$current_instance->getStrArgsStd()';
                    break;
                default:
                    $action[] = "''";
                }
                if ($need_urlencode) {
                    $action_len = count($action);
                    $action[$action_len-1] = 'rawurlencode(' . $action[$action_len-1] . ')';
                }
                break;
            case '%':
                $action[] =  "'%'";
                break;
            default:
                $action[] = "''";
            }
        }

        $strformat = preg_split($regex, $format);
        $code = var_export($strformat[0], true);
        for($i = 1; $i < count($strformat); $i++) {
            //$code = $code . ' . ' . $action[$i-1] . ' . ' . var_export($strformat[$i], true);
            $code = $code . ' . ' . $action[$i-1] . ' . "' . $strformat[$i] . '"';
        }
        $code .=  ' . "\n"';
        $pre = implode("\n", $prelim);

        $cmt = "Used for app " . self::getLogPrefix() . "\n";
        $cmt .= "Original format string: " . str_replace('*/', '* /', $format);

        $md5val = md5($format);
        $func = "_log_$md5val";
        $str = "<?php \n/*\n$cmt\n*/\nfunction $func() {\n$pre\nreturn $code;\n}\n\n";

        $arrcode = '';
        $arrRegex = "/(\w+)[\w+]/";
        preg_match_all($arrRegex, $format, $arrMatches);
        $arrFormat = $arrMatches[0];
        $num = count($arrFormat);
        for($i = 1; $i <= $num; $i++) {
            $arrcode = $arrcode . '"'.$arrFormat[$i-1].'"=>'.$action[$i-1].",\n";
        }
        $arrfunc = "arr_log_$md5val";
        $str = $str . "function $arrfunc() {\n$pre\nreturn array(\n$arrcode);\n}";
        return $str;
    }

    //helper functions for use in generated code
    public static function flattenArgs($args) {
        if (!is_array($args)) return '';
        $str = array();
        foreach($args as $a) {
            $str[] = preg_replace('/[ \n\t]+/', " ", $a);
        }
        return implode(', ', $str);
    }

    public function getStrArgs() {
        $strArgs = '';
        foreach($this->current_args as $k=>$v){
            if (is_array($v)) {
                $v = json_encode($v);
            } 
            $strArgs .= ' '.$k.'['.$v.']';
        }
        return $strArgs;
    }

    public function getStrArgsStd() {
        $args = array();
        foreach($this->current_args as $k=>$v){
            $args[] = rawurlencode($k).'='.rawurlencode($v);
        }
        return implode(' ', $args);
    }

    public function flush($key) {
        if (empty($this->cache[$key])) {
            return;
        }

        $localFp = null;

        if ($this->isLocalLog === true && !empty($this->logPath)) {
            $file =  $this->logPath . "/$key.log";
            if($this->bolAutoRotate) {
                $file .= '.'.date('YmdH');
            }
            $localFp = fopen($file , "a" );
        }

        foreach ($this->cache[$key] as $_k => $arrLog) {
            if (!isset($arrLog['arr']) || empty($arrLog['arr']) || !is_array($arrLog['arr'])) {
                continue;
            }
            $content = json_encode($arrLog['arr']);
            if ($this->isRemoteLog === true) {
                $this->flushRemoteLog($key, $content);
            }
            if ($this->isLocalLog === true && $localFp) {
                fwrite( $localFp , $content."\n");
            }
            unset($this->cache[$key][$_k]);
        }

        unset($this->cache[$key]);

        if($localFp) {
            fclose($localFp);
        }
    }

    public function flushRemoteLog($key, $content) {
        if (empty($key) || empty($content) ) {
            return;
        }
        $configStruct = (array) Ko::config('struct');
        if (isset($configStruct['log']['als']) && $configStruct['log']['als'] == 'yes') {
            include_once (SYS_PATH . DIRECTORY_SEPARATOR . 'alsLog.php');
            $als_model=new alsLog();
            $als_model->writeAls($key, $content);
        }
        if (isset($configStruct['log']['fluentd']) && $configStruct['log']['fluentd'] == 'yes') {
            include_once (SYS_PATH . DIRECTORY_SEPARATOR . 'fluentdLog.php');
            $fluentd=new fluentdLog();
            $metric_name = isset($configStruct['fluentd']['metric_name_pre']) ? $configStruct['fluentd']['metric_name_pre'] : '';
            $res=$fluentd->writeString($metric_name.$name, $content);
        }
    }

    public static function setUid($uid) {
        self::$uid = $uid;
    }

    public static function setSession($sid) {
        self::$sid = $sid;
    }

    public function flushAll() {
        FfsLog::addNotice('aaa', array('bbb'=>array('ccc'=>2, 'ddd'=>3)));
        FfsLog::addNotice('aaa', 'ddd');
        FfsLog::unsetNotice('aaa');
        FfsLog::notice('test', 10, array('bbb'=>array('ccc'=>2, 'ddd'=>3)));
        if ($this->cache) foreach ($this->cache as $_k => $_v) {
            $this->flush($_k);
        }
    }
}
