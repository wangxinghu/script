<?php
    $content = file_get_contents('./seed.txt', 'r');
    $arr_c = explode("\n",$content);
    $arrRes = array();
    foreach ($arr_c as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $data = explode("\t", $line);
        $id = intval($data[0]);
        $level = intval($data[2]);
        $scene = intval($data[3]);
        if ($scene == 10) {
            $scene = 2;
        }
        $difficulty = intval($data[4]);
        $dp = intval($data[5]);
        if ($id <= 0) {
            continue;
        }
        $arrRes[$id] = array(
            'level' => $level,
            'dp' => $dp,
            'scene' => $scene,
            'difficulty' => $difficulty,
        );
    }
    var_dump($arrRes);
    $strContent = "<?php\n";
    $strContent .= "return  ";
    $strContent .= var_export($arrRes, true).";\n";
    file_put_contents('./order_seed.php', $strContent);
    exit;
