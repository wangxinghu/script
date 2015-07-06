<?php
$a = '{aaa":"bbb"}';
var_dump(json_decode($a, true));exit;
$arrParam = array(
    'map_move' => array(
        array(
            'oid' => '12',
            'x' => '12',
            'y' => '12',
            'f' => '0',
        ),
        array(
            'oid' => '13',
            'x' => '13',
            'y' => '13',
            'f' => '0',
        ),
    ),
    'hub' => array(
        'in' => array(
            '12',
            '13',
        ),
        'out' => array(
            '12',
            '13',
        ),
    ),
    'warehouse' => array(
        'in' => array(
            '12',
            '13',
        ),
        'out' => array(
            '12',
            '13',
        ),
    ),
    'decoration_warehouse' => array(
        'in' => array(
            '12',
            '13',
        ),
        'out' => array(
            '12',
            '13',
        ),
    ),
);

$str = json_encode($arrParam);
print_r($str);
print_r("\n");
print_r(urldecode($str));
exit;



function escape ($value)
{
    if (is_array($value)) {
        foreach ($value as &$val) {
            $val = $this->escape($val);
        }
        return implode(', ', $value);
    } elseif (is_bool($value)) {
        return (int) $value;
    } elseif (is_int($value)) {
        return $value;
    } elseif (is_float($value)) {
        return sprintf('%F', $value);
    }
    return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
}

$a = array('a'=>'b', 'c'=>2);
var_dump(serialize($a));

    $str = serialize($a);
    var_dump(escape($str));
    exit;
$a = array('g' => 'a1', 'b' => 'b1', 'c' => 'c1');
var_dump(implode(',', $a));exit;
var_dump($a);
exit;
$b = array('c' => 2, );
var_dump(array_merge($a, $b));
exit;
exit;
$a = array('a' => 'b', 'e' => 'f');
$b = array('g' => 'd');
var_dump(array_intersect_key($a, $b));
exit;
$str = '{"snsid":"ibdjzawuhoiggbbyogwlnwt","login_session":"65C7161B-EA28-48D4-8A7F-A846319DE9FF","udid":"","farm_uuid":"65C7161B-EA28-48D4-8A7F-A846319DE9FF","lang":"en_US","resource_type":"high","version":"3.9.1.0","platform":"iOS","transport":"http://127.0.0.1:8080/","scene":1,"session_id":"65C7161B-EA28-48D4-8A7F-A846319DE9FF_1434093358","product":"ffs.dev.iOS","time_zone_offset":28800,"tm_isdst":"0","tm_gmtoff":28800,"tm_zone":"CST","id":"allfamilyfarmfriend","uid":"ibdjzawuhoiggbbyogwlnwt","app_type":"native","scene":2}';
var_dump(json_decode($str, true));
exit;
$a = array('a' => 'b');
foreach ($a as $key => &$value) {
    $value = 'c';
}
$b = array('aa' => 'bb');
foreach ($b as $key => &$value) {
    $value = 'cc';
}
$value = 'e';
var_dump($a);
var_dump($b);
exit;
var_dump(0 == '');exit;
$b = null;
$a = unserialize($b) ? unserialize($b) : array();
var_dump($a);exit;
$a = new stdClass();
$a->b = 'c';
$key = 'b';
var_dump(isset($a->{$ked}));exit;
$a = array('a' => array('a1' => 'a2','a3' => 'a4'),'b' => array('b1' => 'b2','b3' => 'b4'));

foreach ($a as $key => &$value) {
    $value = (object)$value;
}
var_dump($a['a']->a1);exit;
$a = new stdClass();
$a->a->a1 = 'a2';
$a->a->a3 = 'a3';
$a->b->b1 = 'b2';
$a->b->b3 = 'b3';

$b = array('a' => array('aa' => 'bb', 'cc' => 'dd'));
var_dump(array_merge_recursive($a, $b));
exit;
$a = array(12,23,34);
$b = array_fill(0, count($a), 1);
$a = array_combine($a, $b);
var_dump($a);
exit;
function testt(&$value, &$key) {
    $key = $value;
    $value = 1;
    var_dump($key, $value);
}
array_walk($a,'testt');
var_dump($a);
exit;
$a = new stdClass();
var_dump(empty($a));exit;
$a = array('12' => 'a', '23' => 'b');
$b = array('23' => array('hh'), '45' => array('gg'));
var_dump(array_diff_key($b, $a));exit;
$a = new stdClass();
$a->b = array('ee' => 'ff');
$a->c = $a->b;
$a->b = 1;
var_dump($a->c);
exit;
$a = 'a';
$b = 'b';
$a = $b = 'c';
var_dump($a, $b);exit;
function strs($str1, $str2) {
    $num1 = strlen($str1);
    $num2 = strlen($str2);
    if ($num1 ==0 || $num2 ==0) {
        return false;
    }
    for ($i = 0; $i <= $num1-$num2; $i++) {
        if (substr($str1, $i, $num2) == $str2) {
            return $i;
        }
    }
    return false;
}
$str1 = 'bdacdeabc';
$str2 = 'abc';
var_dump(strs($str1, $str2));
exit;



$p = array(
    array('a', 'b', 'c', 'd'),
    array('e', 'f', 'g', 'h'),
    array('i', 'j', 'k', 'l'),
);
function arrConvert($p) {
    $res = array();
    $lineNum = count($p);
    foreach ($p as $lNo => $arrLine) {
        foreach ($arrLine as $vNo => $strV) {
            $res[$vNo][$lineNum-$lNo-1] = $strV;
        }
    }
    foreach ($res as $key => &$data) {
        ksort($res[$key]);
    }
    return $res;
}
$pC = arrConvert($p);
var_dump($pC);
exit;

$a = 1.13;
$b = 100;
$c = intval(($a*$b));
$c = intval(round($a*$b-0.5));
var_dump($c);
exit;


$arr = array(10, 20, 30);
$b = 20;
$arrb = array($b);
var_dump(array_diff($arr, $arrb));
exit;
$id = array_rand($arr, 1);
var_dump($arr[$id]);
echo 'test';
exit;
$arr = array_merge($arr, array());
var_dump($arr);
exit;
$b = array();
$str = 'npc_'.$arr['a'];
$b['npc_'.$arr['a']] = 3;
var_dump($b);exit;
function getUniqueToken() {
    $arrChars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        '0','1','2','3','4','5','6','7','8','9');
    shuffle($arrChars);
    $arrKey = array_rand($arrChars, 8);
    $str = '';
    for ($i = 0; $i < 8; $i++) {
        $str .= $arrChars[$arrKey[$i]];
    }
    return $str;
}
var_dump(getUniqueToken());
var_dump(getUniqueToken());
var_dump(getUniqueToken());
var_dump(getUniqueToken());
exit;
function test(&$a) {
    $a[1]['a'] = 2;
}
$a = array(
    1 => array(
        'a' => 'a',
        'b' => 'b',
    ),
    2 => array(
        'c' => 'c',
        'd' => 'd',
    ),
);
test($a);
var_dump($a);
exit;
unset($a[1]['a']);
unset($a[1]['b']);
unset($a[1]);
unset($a[2]['c']);
unset($a[2]['d']);
unset($a[2]);
var_dump($a);
exit;
if ($tt['sdf'] === null) {
    echo 'succ';
}
exit;
$total=10;//红包总额
$num=8;// 分成8个红包，支持8人随机领取
$min=0.01;//每个人最少能收到0.01元

for ($i=1;$i<$num;$i++)
{
    $safe_total=($total-($num-$i)*$min)/($num-$i);//随机安全上限
    echo $safe_total . "\t";
    $money=mt_rand($min*100,$safe_total*100)/100;
    $money = 0.01;
    $total=$total-$money;
    echo '第'.$i.'个红包：'.$money.' 元，余额：'.$total.' 元 '."\n";
}
echo '第'.$num.'个红包：'.$total.' 元，余额：0 元'."\n";
exit;
unset($arr['0']);
var_dump($arr);
var_dump('aaa');
exit;
$req = '{"Target":"MobileDataHandler.handle","Response":"\/1","data":["execute_batch",{"uid":"7fe6b696342cbaff5be91c9d3d294169","login_session":"83ffaecc3a60bd609cfb4e83aaa42efb","udid":"83ffaecc3a60b d609cfb4e83aaa42efb","farm_uuid":"","lang":"en_US","transport":"http:\/\/ffs-global-upgrade.funplusgame.com\/","product":"ffs.global.android","call_id":"call1419110129 ","is_delayed":"0","scene":1,"time_zone_offset":"0.000000","user_info":{"coins":"5825","reward_points":"0","experience":"1805","op":"0","level":"9"},"queue":[{"data":{ "is_all":"1","plants":[{"x":"40","y":"36","id":"4"},{"x":"40","y":"32","id":"4"},{"x":"40","y":"28","id":"4"},{"x":"44","y":"44","id":"4"},{"x":"36","y":"36","id":"4"} ,{"x":"36","y":"32","id":"4"},{"x":"48","y":"28","id":"4"},{"x":"44","y":"48","id":"4"},{"x":"44","y":"28","id":"4"},{"x":"48","y":"32","id":"4"},{"x":"44","y":"40","i d":"4"},{"x":"44","y":"36","id":"4"},{"x":"44","y":"32","id":"4"},{"x":"48","y":"36","id":"4"},{"x":"48","y":"40","id":"4"},{"x":"48","y":"44","id":"4"},{"x":"48","y": "48","id":"4"},{"x":"52","y":"52","id":"4"},{"x":"52","y":"48","id":"4"},{"x":"52","y":"44","id":"4"},{"x":"52","y":"40","id":"4"},{"x":"52","y":"36","id":"4"},{"x":"5 2","y":"32","id":"4"},{"x":"52","y":"28","id":"4"},{"x":"48","y":"52","id":"4"},{"x":"40","y":"40","id":"4"}],"op_type":"harvest crop"},"method":"harvest_plants","time stamp":"1419110527","step":"-1","queue_id":"1","method_id":"1419110099-152","scene":1},{"data":{"duration":"376"},"method":"session_end","timestamp":"1419110531","step ":"-1","queue_id":"2","method_id":"1419110099-153","scene":1},{"data":{"stepid":"8","seconds":"391"},"method":"onlinegiftrecord","timestamp":"1419110531","step":"-1"," queue_id":"3","method_id":"1419110099-154","scene":1},{"data":{"duration":"1473"},"method":"session_end","timestamp":"1419111628","step":"-1","queue_id":"4","method_id ":"1419110099-155","scene":1},{"data":{"stepid":"8","seconds":"391"},"method":"onlinegiftrecord","timestamp":"1419111628","step":"-1","queue_id":"5","method_id":"14191 10099-156","scene":1}]},"save_data"]}';
$req = '{"data":{ "is_all":"1","plants":[{"x":"40","y":"36","id":"4"},{"x":"40","y":"32","id":"4"},{"x":"40","y":"28","id":"4"},{"x":"44","y":"44","id":"4"},{"x":"36","y":"36","id":"4"} ,{"x":"36","y":"32","id":"4"},{"x":"48","y":"28","id":"4"},{"x":"44","y":"48","id":"4"},{"x":"44","y":"28","id":"4"},{"x":"48","y":"32","id":"4"},{"x":"44","y":"40","i d":"4"},{"x":"44","y":"36","id":"4"},{"x":"44","y":"32","id":"4"},{"x":"48","y":"36","id":"4"},{"x":"48","y":"40","id":"4"},{"x":"48","y":"44","id":"4"},{"x":"48","y": "48","id":"4"},{"x":"52","y":"52","id":"4"},{"x":"52","y":"48","id":"4"},{"x":"52","y":"44","id":"4"},{"x":"52","y":"40","id":"4"},{"x":"52","y":"36","id":"4"},{"x":"5 2","y":"32","id":"4"},{"x":"52","y":"28","id":"4"},{"x":"48","y":"52","id":"4"},{"x":"40","y":"40","id":"4"}]id":"5","method_id":"14191 10099-156","scene":1}]}';
$rq = json_decode($req);
var_dump($rq->data);
exit;
