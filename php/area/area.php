<?php
/*
houseinfo
    93 : 18*26
    100249 : 26*30
    65020 : 14*20
map_size
    120*120
地块和树只考虑单点坐标.
//TODO 还得判断greenhouse是不是在map上。
//TODO 还得判断itemid是否可以放在greenhouse中。
*/
class GreenHouse {
    const GID= 93;
    const SGID = 100249;
    const TGID = 65020;
    public static $greenhouseSize = array(
            self::GID => array(
                'x' => 18,
                'y' => 26,
            ),
            self::SGID => array(
                'x' => 26,
                'y' => 30,
            ),
            self::TGID => array(
                'x' => 14,
                'y' => 20,
            ),
    );
    private $mapData = array();
    public function __construct() {
        $this->mapData = require './map.php';
    }
    public function getSize($minX, $minY, $f, $sizeX, $sizeY) {
        $maxX = $f == 1 ? $minX + $sizeY : $minX + $sizeX;
        $maxY = $f == 1 ? $minY + $sizeX : $minY + $sizeY;
        return array('minX' => $minX, 'minY' => $minY, 'maxX' => $maxX, 'maxY' => $maxY);
    }
    public function checkSize($line, $data) {
        if ($line['x'] >= $data['minX'] && $line['x'] < $data['maxX'] && $line['y'] >= $data['minY'] && $line['y'] < $data['maxY']) {
            return true;
        }
        return false;
    }
    public function normal() {
        //echo 'start:'.microtime(true)."\n";
        $start = microtime(true);
        $greenHouseData = array();
        foreach ($this->mapData as $oid => $line) {
            if ($line['itemid'] == self::GID || $line['itemid'] == self::SGID || $line['itemid'] == self::TGID) {
                $size = $this->getSize($line['x'], $line['y'], $line['f'], self::$greenhouseSize[$line['itemid']]['x'], self::$greenhouseSize[$line['itemid']]['y']);
                $size['itemid'] = $line['itemid'];
                $greenHouseData[$oid] = $size;
            }
        }
        //echo 'list'.microtime(true)."\n";
        foreach ($this->mapData as $oid => $line) {
            if ($line['itemid'] == self::GID || $line['itemid'] == self::SGID || $line['itemid'] == self::TGID) {
                continue;
            }
            foreach ($greenHouseData as $goid => $data) {
                $ret = $this->checkSize($line, $data);
                if ($ret === true) {
                    $greenHouseData[$goid]['oids'][$oid] = array('x' => $line['x'], 'y' => $line['y']);
                    break;
                }
            }
        }
        //echo 'end:'.microtime(true)."\n";
        $end = microtime(true);
        $cost = number_format((($end - $start)*1000), 2, '.', '');
        //echo 'normal_cost:'.$cost."\n";
        file_put_contents('normal', var_export($greenHouseData, true));
        return $cost;
    }

    public function dot() {
        //echo 'start:'.microtime(true)."\n";
        $start = microtime(true);
        $greenHouseData = array();
        $itemGrid = array();
        foreach ($this->mapData as $oid => $line) {
            if ($line['itemid'] == self::GID || $line['itemid'] == self::SGID || $line['itemid'] == self::TGID) {
                $size = $this->getSize($line['x'], $line['y'], $line['f'], self::$greenhouseSize[$line['itemid']]['x'], self::$greenhouseSize[$line['itemid']]['y']);
                $size['itemid'] = $line['itemid'];
                $greenHouseData[$oid] = $size;
            } else {
                $itemGrid[$line['x'].'_'.$line['y']] = $oid;
            }
        }
        //echo 'list:'.microtime(true)."\n";
        foreach ($greenHouseData as $oid => $value) {
            $oids = array();
            for ($i = $value['minX']; $i < $value['maxX']; ++$i) {
                for ($j = $value['minY']; $j < $value['maxY']; ++$j) {
                    if (isset($itemGrid[$i.'_'.$j])) {
                        $oids[] = $itemGrid[$i.'_'.$j];
                    }
                }
            }
            sort($oids);
            $greenHouseData[$oid]['oids'] = $oids;
        }
        //echo 'end:'.microtime(true)."\n";
        $end = microtime(true);
        $cost = number_format((($end - $start)*1000), 2, '.', '');
        //echo 'dot_cost:'.$cost."\n";
        file_put_contents('dot', var_export($greenHouseData, true));
        return $cost;
    }

    public function getGridIds($data, $gridData) {
        $ids = array();
        foreach ($gridData as $oid) {
            $ret = $this->checkSize($this->mapData[$oid], $data);
            if ($ret === true) {
                $ids[] = $oid;
            }
        }
        return $ids;
    }
    //没有考虑边界问题
    public function grid($gridValue) {
        //echo 'start:'.microtime(true)."\n";
        $start = microtime(true);
        $greenHouseData = array();
        $gridData = array();
        foreach ($this->mapData as $oid => $line) {
            if ($line['itemid'] == self::GID || $line['itemid'] == self::SGID || $line['itemid'] == self::TGID) {
                $size = $this->getSize($line['x'], $line['y'], $line['f'], self::$greenhouseSize[$line['itemid']]['x'], self::$greenhouseSize[$line['itemid']]['y']);
                $size['itemid'] = $line['itemid'];
                $greenHouseData[$oid] = $size;
                continue;
            }
            $x = $line['x']>>$gridValue;
            $y = $line['y']>>$gridValue;
            $gridData[$x.'_'.$y][] = $oid;
        }
        //echo 'list:'.microtime(true)."\n";
        foreach ($greenHouseData as $oid => $data) {
            $arrOids = array();
            $minX = $data['minX']>>$gridValue;
            $minY = $data['minY']>>$gridValue;
            $maxX = $data['maxX']>>$gridValue;
            $maxY = $data['maxY']>>$gridValue;
            for ($i = $minX; $i <= $maxX; ++$i) {
                for ($j = $minY; $j <= $maxY; ++$j) {
                    if (!isset($gridData[$i.'_'.$j])) {
                        continue;
                    }
                    $oids = array();
                    if ($i == $minX || $i == $maxX || $j == $minY || $j == $maxY) {
                        $oids = $this->getGridIds($data, $gridData[$i.'_'.$j]);
                    } else {
                        $oids = $gridData[$i.'_'.$j];
                    }
                    if (!empty($oids) && is_array($oids)) {
                        //$arrOids = array_merge($arrOids, $oids);
                        //$arrOids = $arrOids + $oids;
                        foreach ($oids as $oidTemp) {
                            $arrOids[] = $oidTemp;
                        }
                    }
                }
            }
            ksort($arrOids);
            $greenHouseData[$oid]['oids'] = $arrOids;
        }
        //echo 'end:'.microtime(true)."\n";
        $end = microtime(true);
        $cost = number_format((($end - $start)*1000), 2, '.', '');
        //echo $gridValue.':'.$cost."\n";
        file_put_contents('grid'.$gridValue, var_export($greenHouseData, true));
        return $cost;
    }

    public function lines() {
        //echo 'start:'.microtime(true)."\n";
        $start = microtime(true);
        $greenHouseData = array();
        $greenHouseX = array();
        $greenHouseY = array();
        $xItem = array();
        $yItem = array();
        foreach ($this->mapData as $oid => $line) {
            if ($line['itemid'] == self::GID || $line['itemid'] == self::SGID || $line['itemid'] == self::TGID) {
                $size = $this->getSize($line['x'], $line['y'], $line['f'], self::$greenhouseSize[$line['itemid']]['x'], self::$greenhouseSize[$line['itemid']]['y']);
                $greenHouseX[$line['oid'].'_min'] = $size['minX'];
                $greenHouseX[$line['oid'].'_max'] = $size['maxX'];
                $greenHouseY[$line['oid'].'_min'] = $size['minY'];
                $greenHouseY[$line['oid'].'_max'] = $size['maxY'];
                $size['itemid'] = $line['itemid'];
                $greenHouseData[$oid] = $size;
            } else {
                $xItem[$line['x']][$oid] = 1;
                $yItem[$line['y']][$oid] = 1;
            }
        }
        asort($greenHouseX);
        asort($greenHouseY);
        //echo 'list:'.microtime(true)."\n";
        $xoids = array();
        $begin = null;
        $end = null;
        $open = 0;
        foreach ($greenHouseX as $key => $value) {
            $end = $value;
            if ($begin !== null && $open !== 0) {
                $oidTemp = array();
                for ($i = $begin; $i < $end; ++$i) {
                    if (isset($xItem[$i])) {
                        foreach ($xItem[$i] as $oid => $valueTemp) {
                             $oidTemp[$oid] = 1;
                        }
                        //$oidTemp = array_merge($oidTemp, $xItem[$i]);
                    }
                }
                foreach ($xoids as $itemid => &$xoidsData) {
                    if ($xoidsData['begin'] === 1) {
                        //foreach ($oidTemp as $oid => $valueTemp) {
                             //$xoidsData['oids'][$oid] = 1;
                        //}
                        //$xoidsData['oids'] = array_merge($xoidsData['oids'], $oidTemp);
                        $xoidsData['oids'] = $xoidsData['oids'] + $oidTemp;
                    }
                }
            }
            $arrData = explode('_', $key);
            if ($arrData[1] == 'min') {
                $xoids[$arrData[0]] = array('begin' => 1, 'oids' => array());
                ++$open;
            } else {
                $xoids[$arrData[0]]['begin'] = 0;
                --$open;
            }
            $begin = $value;
        }
        //echo 'x_list:'.microtime(true)."\n";
        $yoids = array();
        $begin = null;
        $end = null;
        $open = 0;
        foreach ($greenHouseY as $key => $value) {
            $end = $value;
            if ($begin !== null && $open !== 0) {
                $oidTemp = array();
                for ($i = $begin; $i < $end; $i++) {
                    if (isset($yItem[$i])) {
                        foreach ($yItem[$i] as $oid => $valueTemp) {
                             $oidTemp[$oid] = 1;
                        }
                        //$oidTemp = array_merge($oidTemp, $yItem[$i]);
                    }
                }
                foreach ($yoids as $itemid => &$yoidsData) {
                    if ($yoidsData['begin'] === 1) {
                        foreach ($oidTemp as $oid => $valueTemp) {
                             $yoidsData['oids'][$oid] = 1;
                        }
                        //$yoidsData['oids'] = array_merge($yoidsData['oids'], $oidTemp);
                        //$yoidsData['oids'] = $yoidsData['oids'] + $oidTemp;
                    }
                }
            }
            $arrData = explode('_', $key);
            if ($arrData[1] == 'min') {
                $yoids[$arrData[0]] = array('begin' => 1, 'oids' => array());
                ++$open;
            } else {
                $yoids[$arrData[0]]['begin'] = 0;
                --$open;
            }
            $begin = $value;
        }
        //echo 'y_list:'.microtime(true)."\n";
        foreach ($greenHouseData as $oid => $data) {
            $arrOid = array_intersect_key($xoids[$oid]['oids'], $yoids[$oid]['oids']);
            $oids = array_keys($arrOid);
            sort($oids);
            $greenHouseData[$oid]['oids'] = $oids;
        }
        //echo 'end:'.microtime(true)."\n";
        $end = microtime(true);
        $cost = number_format((($end - $start)*1000), 2, '.', '');
        //echo 'line_cost:'.$cost."\n";
        file_put_contents('lines', var_export($greenHouseData, true));
        return $cost;
    }
}

$obj = new GreenHouse();
$i = $j = 100;
$normal = $dot = $grid = $lines = 0.0;
while($i--) {
    $res = $obj->normal();
    $normal += $res;
    $res = $obj->dot();
    $dot += $res;
    $res = $obj->grid(3);
    $grid += $res;
    $res = $obj->lines();
    $lines += $res;
}
echo 'avg_normal:'.$normal/$j."\n";
echo 'avg_dot:'.$dot/$j."\n";
echo 'avg_grid:'.$grid/$j."\n";
echo 'avg_lines:'.$lines/$j."\n";
//print_r($res);
exit;
