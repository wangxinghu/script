<?php
date_default_timezone_set('UTC');
function getBeginTimeOfToday($timezone_offset)
{
	$time=time();
	$today=mktime(0, 0, 0, date('m'), date('j'), date('Y'));
	if($timezone_offset>0)//utc以东
	{
		$tomorrow=$today+86400;
		if(($tomorrow-$time)<$timezone_offset)//当前时区与utc已不再同一天
		{
			$today=$tomorrow-$timezone_offset;
		}
		else//在同一天
		{
			$today=$today-$timezone_offset;
		}
	}
	else//utc以西 
	{
		$timezone_offset=0-$timezone_offset;
		if(($time-$today)<$timezone_offset)//不再同一天
		{
			$today=$today+$timezone_offset-86400;
		}
		else
		{
			$today=$today+$timezone_offset;
		}
	}
	return $today;
}
var_dump(getBeginTimeOfToday(3600));