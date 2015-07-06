<?php
    $content = file_get_contents('./lock.txt', 'r');
    $arr_c = explode("\n",$content);
    $arr_t = array_shift($arr_c);
    $arr_t = trim($arr_t);
    $arr_t = explode("\t",$arr_t);
    $productOrigin = 'product1: original';
    $productOriginIndex = -1;
    $arrProductRelationIndex = array();
    $arrTypeIndex = -1;
    foreach ($arr_t as $key => $title) {
        $title = trim($title);
        if ($title === $productOrigin) {
            $productOriginIndex = $key;
            continue;
        }
        if (strpos($title, 'product') !== false) {
            $arrProductRelationIndex[] = $key;
            continue;
        }
        if (strpos($title, 'array') !== false) {
            $arrTypeIndex = $key;
            continue;
        }
    }
    if ($productOriginIndex === -1) die('no product1: original');
    if ($arrTypeIndex === -1) die('no array type');
    $arrRes = array();
    foreach ($arr_c as $key => $line) {
        $lineNo = $key + 2;
        $arrProduct = array();
        $line = trim($line);
        if (empty($line)) continue;
        $data = explode("\t", $line);
        $type = intval($data[$arrTypeIndex]);
        if ($type <= 0) die("line: ".$lineNo." array data error");
        $productId = intval($data[$productOriginIndex]);
        if ($productId <= 0) die("line: ".$lineNo." product1:original data error");
        $arrProduct[] = $productId;
        foreach ($arrProductRelationIndex as $index) {
            $str = trim($data[$index]);
            $str = str_replace("；", ";", $str);
            if (empty($str)) continue;
            $arrProductRelation = explode(";", $str);
            foreach ($arrProductRelation as $id) {
                $id = intval($id);
                if ($id <= 0) continue;
                $arrProduct[] = $id;
            }
        }
        $arrProduct = array_unique($arrProduct);
        $arrRes[$type][$productId] = $arrProduct;
    }
    ksort($arrRes);
    var_dump($arrRes);
    $strContent = "<?php\n";
    $strContent .= "return  ";
    $strContent .= var_export($arrRes, true).";\n";
    file_put_contents('./order_need_unlock.php', $strContent);
    exit;
