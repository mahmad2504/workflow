<?php
define('EOL','<BR>');
session_start();
if(!isset($_SESSION['token']))
{
	echo 'Authorization Failure';
	return;
}
$token = $_SESSION['token'];
$user = '';
if(isset($_POST['user']))
{
	$user = $_POST['user'];
}
if(isset($_GET['user']))
{
	$user = $_GET['user'];
}

if($user == '')
{
	echo "Username/Password Invalid".EOL;
	return;
}

$tokenstr = base64_decode($token);
$username = explode('&',$tokenstr)[0];
if($username  != $user)
{
	echo 'Authorization Failure';
	return;
}
$users = file_get_contents('users.json');
$users  = json_decode($users);

$tickets = array();
$tickets = GetMyTickets('tickets',$user);
echo json_encode($tickets);
exit();

function GetMyTickets($dir,$user){
	global $tickets;
    $ffs = scandir($dir);

    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);

    // prevent empty ordered elements
    if (count($ffs) < 1)
        return;

    foreach($ffs as $ff)
	{
        if(is_dir($dir.'/'.$ff))
		{
			GetMyTickets($dir.'/'.$ff,$user);
		}
		else
		{
			$ticket_file =$dir."/".$ff;
			if(strpos($ticket_file,'.done')!=FALSE)
				continue;
			//echo $ticket_file.EOL;
			$json_string = file_get_contents($ticket_file);
			$ticket = json_decode($json_string);
			//var_dump($ticket);
			$state = $ticket->states[$ticket->state];
			if($state->assignee == $user)
				$tickets[] = $ticket;
		}
    }
	return $tickets;
}
?>