<?php
require_once('common.php');
$params = ParseParams();
foreach($app->users as $user)
{
	if( ($user->name == $params['username'])&&($user->pass == $params['password']))
	{
		if($params['changepassword'])
		{
			if($user->pass == $params['opassword'])
			{
				$user->pass = $params['npassword'];
				SaveUsers();
				
			}
			else
			{
				$app->response->error[] = "Invalid Password";
				SendResponse();
			}
		}
		$app->user = $user->name;
		$app->admin = 0;
		if(isset($user->admin))
			$app->admin = 1;
		SetSession();
		SendResponse();
	}
}
$app->response->error[] = "Invalid Credentials";
SendResponse();
	
?>