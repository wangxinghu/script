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
        echo microtime(true)."\n";
        $greenHouseData = array();
        foreach ($this->mapData as $oid => $line) {
            if ($line['itemid'] == self::GID || $line['itemid'] == self::SGID || $line['itemid'] == self::TGID) {
                $size = $this->getSize($line['x'], $line['y'], $line['f'], self::$greenhouseSize[$line['itemid']]['x'], self::$greenhouseSize[$line['itemid']]['y']);
                $size['itemid'] = $line['itemid'];
                $greenHouseData[$oid] = $size;
            }
        }
        echo microtime(true)."\n";
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
        echo microtime(true)."\n";
        return $greenHouseData;
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
    public function grid($gridValue) {
        //echo microtime(true)."\n";
        $start = microtime(true);
        //$gridValue = 1;
        $greenHouseData = array();
        $gridData = array();
        foreach ($this->mapData as $oid => $line) {
            if ($line['itemid'] == self::GID || $line['itemid'] == self::SGID || $line['itemid'] == self::TGID) {
                $size = $this->getSize($line['x'], $line['y'], $line['f'], self::$greenhouseSize[$line['itemid']]['x'], self::$greenhouseSize[$line['itemid']]['y']);
                $size['itemid'] = $line['itemid'];
                $greenHouseData[$oid] = $size;
                continue;
            }
            $x = intval($line['x']/$gridValue);
            $y = intval($line['y']/$gridValue);
            $gridData[$x.'_'.$y][] = $oid;
        }
        foreach ($greenHouseData as $oid => $data) {
            $arrOids = array();
            $minX = intval($data['minX']/$gridValue);
            $minY = intval($data['minY']/$gridValue);
            $maxX = intval($data['maxX']/$gridValue);
            $maxY = intval($data['maxY']/$gridValue);
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
                        $arrOids = array_merge($arrOids, $oids);
                    }
                }
            }
            sort($arrOids);
            $greenHouseData[$oid]['oids'] = $arrOids;
        }
        //echo microtime(true)."\n";
        $end = microtime(true);
        $cost = number_format((($end - $start)*1000), 2, '.', '');
        echo $gridValue.':'.$cost."\n";
        return $greenHouseData;
    }
}

$obj = new GreenHouse();
//$res = $obj->normal();
//$res = $obj->grid(10);
//file_put_contents('grid'.$i, var_export($res, true));
//print_r($res);
exit;
