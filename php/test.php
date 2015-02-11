<?php
#$sql="UPDATE `patent`.`patent_foreign` SET `f_abstract`=\"$a['abstract']\" WHERE `f_applicationid` LIKE '%".$applicationid."%'";
$a['a'] = 'test';
$sql = "UPDATE `patent`.`patent_foreign` SET `f_abstract`=\"$a['a']\" WHERE `f_applicationid` LIKE '%";
echo 'etstate';
exit;
$goog['a'] = 'asd';
unset($good['a']['b']);
var_dump($good);exit;
$x = &$good;
foreach ($a as $key) {
    if (isset($x[$key])) {
        $x = &$x[$key];
    }else{
        $x[$key] = array();
        $x = &$x[$key];
    }
}
if (!is_array($x)) {
    $x = array();
}
$x[$lastNode] = $value;
var_dump($good);exit;
$a = array('a','b','c');
$x = array('a'=>array('b'=>array('c'=>'item')));
$last_node=array_pop($a);
$y=&$x;
foreach($a as $node){
    $y=&$y[$node];
    var_dump($y);
}
unset($y[$last_node]);
var_dump($x);exit;
$arr = array('a' => array('b'=>array('c'=>'d', 'e'=>'f')));
$key = array('a', 'b', 'c');
$str = 'unset($arr[$key[0]][$key[1]][$key[2]]);';
var_dump($str);
var_dump(eval($str));
var_dump($arr);exit;
print_r($arr);
if ($arr['aaa'] === true) {
    var_dump('good');
}
exit;
$str = "level[%L] date[%t] ts[%d] file[%f] num[%N] host[%V] uri[%U] clientIP[%h] localIP[%A] logId[%l] uid[%u]%S";
$regex = "/(\w+)[\w+]/";
preg_match_all($regex, $str, $matches);
var_dump($matches);exit;
$arr = 1;
var_dump(in_array('s', array(0)));exit;
//error_reporting(E_ALL^E_NOTICE);
$arr = array( 
'2' => 23,
'3' => 45
);
$arr = array_keys($arr);
var_dump($arr);exit;
var_dump(var_export($arr,true));
exit;

class ff {
    public static function init() {
        set_error_handler(array('ff', 'error_handler'));
    }
    public static function error_handler($errno, $errstr, $errfile, $errline) {
        $de->fe = 34;
    }
    public static function wt() {
        include_once('./tt.php');
        tt();
        print_r(error_get_last());
    }
}

ff::init();
ff::wt();



exit;
error_reporting(0);
if ($arr['c'] == 'd') {
    echo 'ccccc';
}
var_dump('aaaa');exit;
    function test1($a, $b) {
        $aa = debug_backtrace();
        print_r($aa);
    }
    function test2($a, $b) {
        test1($a, $b);
    }
    
    $a=1;
    $b=2;
    $c = test2($a, $b);
    print_r($c);
    exit;
    //$str = "\"\u2764\ufe0f\u7c89\u7ea2\u2606\u5c0f\u5154\u2764\ufe0f\"";
    $str = "\"\u2764\ufe0f\u7c89\u7ea2\u2606\u5c0f\u5154\u2764\ufe0f\"";
    $de = json_decode($str,true);
    var_dump(mb_strlen($de));
    var_dump($de);exit;
    //$en = json_encode($str, JSON_UNESCAPED_UNICODE);
    var_dump($en);
    $de = json_decode($en,true);
    var_dump($de);
