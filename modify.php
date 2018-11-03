<?php
session_start();
$username = $_POST['user'];
$ticket_path = $_POST['ticket'];
$state = $_POST['state'];
if(isset($_SESSION['token']))
{
	$encstr = $_SESSION['token'];
	$tokenstr = base64_decode($encstr);
    $user = explode('&',$tokenstr)[0];
	if($username != $user)
	{
		echo "Authentication error";
		exit();
	}
}

$uticket = UpdateTicket($ticket_path,$state);

echo $uticket;

function UpdateTicket($path,$state)
{
	$ticket_data = file_get_contents($path);
	$ticket = json_decode($ticket_data);
	if($state == 'done')
	{
		$state = $ticket->states[$ticket->state];
		$ticket->state++;
		if($ticket->state >= count($ticket->states))
		{
			$ticket->state = -1;
			$state->done=Date('Y-m-d');
			$ticket->done=Date('Y-m-d');
		}
		else
		{
			$state->done=Date('Y-m-d');
			$nstate = $ticket->states[$ticket->state];
			$nstate->activated=Date('Y-m-d');
		}
	}
	else if($state == 'revert')
	{
		$state = $ticket->states[$ticket->state];
		if(isset($state->revert))
		{
			if($state->revert >= 0)
			{
				$ticket->state = $state->revert;
			}
		}
	}
	$ticket_data = json_encode($ticket);
	if(isset($ticket->done))
	{
		file_put_contents($path.".done",$ticket_data);
		unlink($path);
	}
	else
	{
		file_put_contents($path,$ticket_data);
	}
	return $ticket_data;
}
?>