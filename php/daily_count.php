<?php
set_time_limit(600);
date_default_timezone_set('UTC');
error_reporting(7);
define("ALSLOG_PATH","/mnt/funplus/logs/fp_ffseaside/history/");
class alsLog
{
    protected $analyzedData;
    protected $total                = array();
    protected $hostMap                  = array(
        'farm-mobile.socialgamenet.com'                     => 'global' ,
        'ffs-amazon.socialgamenet.com'                          => 'amazon' ,
        'ffs-cn.ifunplus.cn'                                    => 'ffs-cn' ,
        'ffs-tango.socialgamenet.com'                           => 'ffs-tango' ,
        'farm-mobile-androidtest.socialgamenet.com' => 'androidtest',
        'farm-mobile-testflight.socialgamenet.com'      => 'testflight',
        'farm-mobile-test.socialgamenet.com'        => 'test',
        'ffs-dev-scene2.socialgamenet.com'              => 'scene2',
        'farm-mobile-test-1.socialgamenet.com'          => 'test-1',
    );
    public function __construct(){}
    public function run()
    {
        $tr = FALSE;
        $yesterdayDate = date( "Ymd", strtotime( "-1 day" ) );
        $filePattern   = array( "erverError_ios_{$yesterdayDate}","erverError_android_{$yesterdayDate}" );

        $files         = $this->getFilesList( $filePattern, ALSLOG_PATH );
        foreach( $files AS $file )
        {
            $this->dealWithOneFile( ALSLOG_PATH . $file );
        }

        foreach( $this->analyzedData AS $key=>$value )
        {
            foreach( $value AS $inKey=>$inValue )
            {
                $this->analyzedData[$key][$inKey]['snsids']     = array_unique( $this->analyzedData[$key][$inKey]['snsids'] );
                $this->analyzedData[$key][$inKey]['player_num'] = count( $this->analyzedData[$key][$inKey]['snsids'] );
                unset( $this->analyzedData[$key][$inKey]['snsids'] );

                if( !isset( $this->total[$key] ) )
                {
                    $this->total[$key] = array();
                    $this->total[$key]['error_player_num'] = 0;
                    $this->total[$key]['error_num']  = 0;
                }

                $this->total[$key]['error_player_num'] += $this->analyzedData[$key][$inKey]['player_num'];
                $this->total[$key]['error_num']            += $this->analyzedData[$key][$inKey]['num'];
            }
        }

        $this->doSort();
        $logFileName = "/mnt/funplus/ffs_parse/log/ServerInfo_Error_{$yesterdayDate}.html";
        $htmlStr = $this->createHtmlPage();

        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>FFS Report</title></head><body>_content_</body></html>';
        $html = str_replace( "_content_", $htmlStr, $html );
        file_put_contents( $logFileName, $html );
        system("python /mnt/funplus/ffs_script/python/sendmail.py");
    }

    protected function doSort()
    {
        foreach( $this->total as $key => $row )
        {
            $error_player_num[$key]  = $row['error_player_num'];
            $error_num[$key]             = $row['error_num'];
        }
        array_multisort( $error_player_num, SORT_DESC, $error_num, SORT_DESC, $this->total );

        foreach( $this->analyzedData AS $key=>$value )
        {
            $error_player_num = array();
            $error_num                = array();
            foreach( $value AS $inKey=>$inValue )
            {
                $error_player_num[$inKey]  = $inValue['num'];
                $error_num[$inKey]             = $inValue['player_num'];
            }
            array_multisort( $error_player_num, SORT_DESC, $error_num, SORT_DESC, $this->analyzedData[$key] );
        }
    }

    protected function createHtmlPage()
    {
        $htmlStr  = "<style>td,th{border-bottom:1px dashed #EEEEEE;font-family:arial;font-size:14px;color:#333333;height:20px;line-height:20px;}caption{color:blue;font-weight:bold;}</style>";
        $htmlStr .= $this->createTotalHtml();
        $htmlStr .= "<br />";
        $htmlStr .= $this->createDetailHtml();
        return $htmlStr;
    }

    protected function createTotalHtml()
    {
        $htmlStr = "<table>";
        $htmlStr .= "<tr><th>Platform</th><th>Sync Error Num</th><th>Sync Error Players Num</th></tr>";
        foreach ($this->total as $key => $value)
        {
            $htmlStr .= "<tr><td style='text-align:left;'>" . $key . "</td><td style='text-align:left;'> ". $value['error_num'] ." </td><td style='text-align:left;'> ". $value['error_player_num'] . "</td></tr>";
        }

        $htmlStr .= "</table>";
        return $htmlStr;
    }

    protected function createDetailHtml()
    {
        $htmlStr = "";
        foreach( $this->analyzedData AS $key=>$value )
        {
            $captionKey = strtoupper( $key );
            $htmlStr .= "<table style='width:800px;'><caption>" . $captionKey . "</caption>";
            $htmlStr .= "<tr><th>Error Type</th><th>Sync Error Num</th><th>Sync Error Players Num</th></tr>";
            foreach( $value AS $inKey=>$inValue )
            {
                $errorType        = $this->analyzedData[$key][$inKey]['errorType'];
                $error_num            = $this->analyzedData[$key][$inKey]['num'];
                $error_player_num = $this->analyzedData[$key][$inKey]['player_num'];
                $htmlStr             .= "<tr><td style='text-align:left;'> ". $errorType ." </td><td style='text-align:left;'>" . $error_num . "</td><td style='text-align:left;'> ". $error_player_num . "</td></tr>";
            }
            $htmlStr .= "</table><br />";
        }
        return $htmlStr;
    }

    protected function getHost( $domain )
    {
        if( !isset( $this->hostMap[$domain] ) ){
            echo $domain." can not find.\r\n";
            return false;
        }

        return $this->hostMap[$domain];
    }

    protected function parseOneLineData( $line , $filePath )
    {
        $line = trim( $line );
        if( empty( $line ) ){
            return false;
        }

        $version = "";
        if( stripos( $filePath , "android" ) ){
            $version = "android";
        }
        else if( stripos( $filePath , "ios" ) ){
            $version = "ios";
        }
        else{
            return false;
        }

        $dataArr = explode("\t", $line);

        $host  = $this->getHost( $dataArr[0] );
        $snsid = $dataArr[3];

        $arrayKey = $host.".".$version;

        if( !isset( $analyzedData[ $arrayKey ] ) ){
            $analyzedData[ $arrayKey ] = array();
        }

        $analyzedData[ $arrayKey ]['snsids'][] = $snsid;

        $tmpRs                     = $this->getErrorType( $dataArr[4] );

        $errorTypeArrayKey = $tmpRs['errorTypeArrayKey'];
        $errorType                 = $tmpRs['errorType'];

        if( !isset( $this->analyzedData[ $arrayKey ][ $errorTypeArrayKey ] ) )
        {
            $this->analyzedData[ $arrayKey ][ $errorTypeArrayKey ] = array();
            $this->analyzedData[ $arrayKey ][ $errorTypeArrayKey ]['errorType'] = $errorType;
            $this->analyzedData[ $arrayKey ][ $errorTypeArrayKey ]['snsids']    = array();
            $this->analyzedData[ $arrayKey ][ $errorTypeArrayKey ]['num']         = 0;
        }

        $this->analyzedData[ $arrayKey ][ $errorTypeArrayKey ]['snsids'][] = $snsid;
        $this->analyzedData[ $arrayKey ][ $errorTypeArrayKey ]['num']++;
    }

    protected function dealWithOneFile( $filePath )
    {
        if( !file_exists( $filePath ) ){
            return false;
        }

        $handle = fopen( $filePath, "r" );

        $loopNum = 0;

        while( !feof( $handle ) )
        {
            $line = fgets( $handle );
            $line = trim($line);
            if( strlen($line) <= 0 ){
                continue;
            }
            $this->parseOneLineData( $line , $filePath );
        }

        fclose($handle);
    }

    protected function getErrorType( $data )
    {
        $errorType = "";
        $data      = substr( $data, 1);
        $tmpData   = $data;

        if(strpos($tmpData, 'eginFish')){
            $tmpData = str_replace(",", "(-", $tmpData);
        }

        $regex1 = "|^(.*)\(|i";
        $regex2 = "|^(.*):|i";

        preg_match( $regex1, $tmpData, $match1 );

        if( !empty( $match1 ) )
        {
            $errorType = $match1[1];
        }
        else
        {
            preg_match( $regex2, $tmpData, $match2 );
            if( !empty( $match2 ) )
            {
                $errorType = $match2[1];
            }
        }

        if( empty( $match1 ) && empty( $match2 ) )
        {
            $errorType = $tmpData;
        }

        if( empty($errorType) )
        {
            return false;
        }

        $errorType = str_replace( array("#"), " ", $errorType );
        $errorType = str_replace( array("$"), "", $errorType );

        $errorTypeArrayKey = str_replace( array(" "), "_", $errorType );

        $rs = array();
        $rs['errorType']                 = $errorType;
        $rs['errorTypeArrayKey'] = $errorTypeArrayKey;

        return $rs;
    }

    protected function getFilesList( $pattern = array(),$fileDir )
    {
        $rs = array();
        $handle  = opendir( $fileDir );
        while( $fileName = readdir($handle) )
        {
            if($fileName == "." || $fileName == ".."){
                continue;
            }

            if( !empty( $pattern ) )
            {
                foreach( $pattern AS $inPattern )
                {
                    if( strpos( $fileName, $inPattern ) )
                    {
                        $rs[] = $fileName;
                        continue;
                    }
                }
            }
            else
            {
                $rs[] = $fileName;
            }
        }
        return $rs;
    }
}

$alsLogObj = new alsLog();
$alsLogObj->run();
