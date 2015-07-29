<?php
function quicksortPro(&$arr, $min, $max) {
    if ($min >= $max) {
        return ;
    }
    $i = $min;
    $j = $max;
    $temp = $arr[$j];
    while ($i < $j) {
        while ($arr[$i] <= $temp && $i < $j) ++$i;
        $arr[$j] = $arr[$i];
        while ($arr[$j] >= $temp && $i < $j) --$j;
        $arr[$i] = $arr[$j];
    }
    $arr[$j] = $temp;
    quicksortPro($arr, $min, $j-1);
    quicksortPro($arr, $j+1, $max);
    //$arr[$i] = $temp;
    //quicksortPro($arr, $min, $i-1);
    //quicksortPro($arr, $i+1, $max);
}

function qsort(&$arr) {
    $len = count($arr);
    if ($len <= 1) {
        return ;
    }
    quicksortPro($arr, 0, $len - 1);
}

$arr = array(1,3,5,-7,9,2,-4,6,8,6);
qsort($arr);
print_r($arr);
exit;
