<?php

$dir = str_replace('\\', '/', __DIR__);

define('TICKETS_DIR',$dir .'/../data/tickets');
define('USERDB',$dir .'/../data/users.json');
define('WORKFLOWDB',$dir .'/../data/workflow.json');

define('EOL','<BR>');
define('ACTION_DONE','Done');
define('ACTION_REVERT_WAIT','Revertwait');
define('ACTION_REVERT','Revert');
define('ACTION_DONE_WAIT','Wait');
define('CANCEL_ACTION_DONE_WAIT','Cancel_Wait');



$app =  new StdClass();
$app->response = new StdClass();
$app->response->error = array();
$app->user = null;
$app->admin = 0;
$app->users = null;
$app->userdb=USERDB;

session_start();
$app->users = LoadUsers();
if($app->users == null)
{
	SendResponse();
}
function ParseParams()
{	
	global $_POST;
	global $_GET;
	$data = array();
	if(count($_GET)>0)
		$params = $_GET;
	else
		$params = $_POST;
	
	return $params;
}
function SetSession()
{
	global $app;
	$user=$app->user;
	global $_SESSION;
	$encstr = base64_encode($user.'&'.time().'&'.$app->admin);
    $_SESSION['token'] = $encstr; 
}
function SendResponse()
{
	global $app;
	$response=$app->response;
	if(count($response->error)>0)
		$response->status = 'success';
	else
		$response->status = 'success';
	echo json_encode($response);
	die;
}
function IsUserValid($username)
{
	global $app;
	$users=$app->users;
	foreach($users as $user)
	{
		if($user->name==$username)
			return true;
	}
	return false;
}
function LoadUsers()
{
	global $app;
	$users=$app->users;
	$response=$app->response;
	
	if(file_exists($app->userdb))
	{
		$users = json_decode(file_get_contents($app->userdb));
	}
	else
	{
		$response->error[] = "User Data Not Found";
	}
	return $users;
}
function SaveUsers()
{
	global $app;
	$users=$app->users;
	file_put_contents($app->userdb,json_encode($users));
	return $users;
}
?>