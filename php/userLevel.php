<?php
set_time_limit(0);
define('SYS_PATH', "/mnt/htdocs/farm/");

class UserLevel {

    private $_dbhosts = array();
    private $arrDBs = array('db113', 'db114', 'db115', 'db116', 'db117', 'db118', 'db119', 'db120', 'db121');
    private $file="/tmp/userLevel";

    public function __construct() {
        $file_path = SYS_PATH . "data/config/database.php";
        $databases = include $file_path;
        foreach ($databases AS $key => $database) {
            if (!empty($database) && in_array($key, $this->arrDBs)) {
                $this->_dbhosts[] = $database;
            }
        }
    }

    public function run() {
        $totalNum = 0;
        file_put_contents($this->file, time()."\n");
        foreach ($this->_dbhosts AS $host) {
            echo $host['database']."\n";
            $link = mysql_connect($host['host'], $host['username'], $host['password']) or die("Could not connect: " . mysql_error());
            mysql_select_db($host['database']);
            $sqlCount = 'select count(*) as tol from tbl_user';
            $result  = mysql_query($sqlCount, $link);
            if (!$result) {
                die(mysql_error());
            }
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $tol = intval($row['tol']);
            $step = 1000;
            $i = 0;
            while ($i < $tol) {
                $strSql  = "SELECT uid, snsid, level, addtime, logintime from tbl_user limit $i, $step";
                $result  = mysql_query($strSql, $link);
                if (!$result) {
                    die(mysql_error());
                }
                while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    $row['addtime'] = strtotime($row['addtime']);
                    $str = implode(',', $row);
                    file_put_contents($this->file, $str."\n", FILE_APPEND);
                }
                $i += $step;
                usleep(500000);
            }
            mysql_close($link);
            sleep(1);
        }
    }
}

$userLevel  = new UserLevel();
$userLevel->run();

