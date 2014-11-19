<?php
function abc(){
         $s = str_repeat('1', 1024);
}
xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
sleep(1);
abc();

$data = xhprof_disable();
//print_r($data);
define ('XHLIB', '/usr/local/Cellar/php56-xhprof/254eb24/xhprof_lib/utils/');
include_once XHLIB.'xhprof_lib.php';
include_once XHLIB.'xhprof_runs.php';
$xhprof_runs= new XHProfRuns_Default();
$xhprof_source = 'xhprof_test';
$run_id=$xhprof_runs->save_run($data, $xhprof_source);
$report_url = 'http://127.0.0.1:8080/xhprof_html/index.php?run='.$run_id.'&source='.$xhprof_source;
echo 'view the performance report:<A href="'.$report_url.'" target=_blank>'.$report_url.'</A>';
