<?php
require_once('common.php');
require_once('generate_tickets.php');

$params = ParseParams();

if(strlen($params['date'])==0)
	$params['date'] =  Date('Y-m-d');
if(strlen($params['edate'])==0)
{
	$days = -1;
}
else
{
	$date1=date_create($params['date']);
	$date2=date_create($params['edate']);
	$interval =date_diff($date1,$date2);
	$days = $interval->format("%R%a");
	if($days < 0)
		$days = -1;
}
CreateGenericTask($params['taskname'],$params['assignee'],$params['date'],$days);
SendResponse();


?>