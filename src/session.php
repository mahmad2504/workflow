<?php
if(isset($_SESSION['token']))
{
	$encstr = $_SESSION['token'];
	$tokenstr = base64_decode($encstr);
	$username = explode('&',$tokenstr)[0];
	$admin = explode('&',$tokenstr)[2];
	if(!IsUserValid($username))
	{
		include('login.php');
		exit();
	}
	$app->user = $username;
	$app->admin =  $admin;
}
else 
{
	include('login.php');
	exit();
}
?>