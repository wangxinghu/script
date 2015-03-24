<?php
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
