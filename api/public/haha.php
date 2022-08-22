<?php
$arr = array(1, 2, 3, 4, 5);//可用数字
$arr_len = count($arr);
$maxlength = 3;    //选择几位
 

echo "排列 P({$arr_len}, {$maxlength}){$r}<br>";
 

foreach ($r as $v) {
    echo join('', $v).' ';
}
echo '<hr>';

echo "组合 C({$arr_len}, {$maxlength})={$r}<br>";
 
$r = combination($arr, $maxlength);
foreach ($r as $v) {
    echo join('', $v).' ';
}