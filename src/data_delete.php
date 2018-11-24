<?php 
require_once("common.php");
require_once("session.php");
if($app->admin != 1)
{
	$app->response->error[] = "Unauthorized Access";
	SendResponse();
}
$params = ParseParams();
if(!isset($params['path']))
{
	$app->response->error[] = "Path variable not set";
	SendResponse();
}
unlink ($params['path']);
SendResponse();