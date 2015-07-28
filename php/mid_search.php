<?php
function mid_search($arr, $value) {
    $count = count($arr);
    if ($count === 0) {
        return -1;
    }
    $i = 0;
    $j = $count-1;
    while ($i <= $j) {
        $mid = $i + (($j-$i) >> 1);
        if ($arr[$mid] === $value) {
            return $mid;
        } elseif ($arr[$mid] < $value) {
            $i = $mid + 1;
        } else {
            $j = $mid -1;
        }
    }
    return -1;
}
$arr = array(1,3,5,7,7,9,11,13);
$value = 5;
print_r(mid_search($arr, $value));
exit;
