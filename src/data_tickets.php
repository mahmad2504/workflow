<?php
require_once('common.php');
require_once('tickets.php');

$params = ParseParams();
$tickets = new Tickets();
$tickets->DoTimeWaitStates();

switch($params['cmd'])
{
	case 'gettickets':
		GetTickets($params['user']);
		break;
	case 'changestate_close_timewait':
		$path = $params['path'];
		$id = $params['id'];
		$user = $params['user'];
		CloseTicket($id,$user,$path);
		GetTickets($params['user']);
		//ChangeState($user,$path,$id,ACTION_DONE_WAIT);
		//ChangeState($user,$path,$id,ACTION_DONE_WAIT);
		break;
	case 'changestate_canceltimewait':
		$path = $params['path'];
		$id = $params['id'];
		$user = $params['user'];
		ReopenTicket($id,$user,$path);
		GetTickets($params['user']);
		break;
	case 'changestate_revert_timewait':
		$path = $params['path'];
		$id = $params['id'];
		$user = $params['user'];
		ReopenTicket($id,$user,$path);// Reopen if in timewait state
		RevertTicket($id,$user,$path);
		GetTickets($params['user']);
		break;
	default:
		$app->response->error[] = "Command ".$params['cmd']." Not supported";
		SendResponse();
		break;
	
}
function ChangeState($user,$path,$id)
{
	global $app;
	$app->response->path = $path;
	$app->response->is = $id;
	SendResponse();
}
function RevertTicket($id,$user,$path)
{
	global $app;
	global $tickets;
	$usertickets = $tickets->GetActiveTickets($user);
	foreach($usertickets as $ticket)
	{
		if($ticket->path == $path)
		{
			if(($ticket->owner == $user)&&($ticket->id == $id))
			{
				$ticket->ChangeState(ACTION_REVERT_WAIT);
				return;
			}
		}
	}
	$app->response->error[]  = "Ticket Not Found";
}
function ReopenTicket($id,$user,$path)
{
	global $app;
	global $tickets;
	$usertickets = $tickets->GetActiveTickets($user);
	foreach($usertickets as $ticket)
	{
		if($ticket->path == $path)
		{
			if(($ticket->owner == $user)&&($ticket->id == $id))
			{
				$ticket->ChangeState(CANCEL_ACTION_DONE_WAIT);
				return;
			}
		}
	}
	$app->response->error[]  = "Ticket Not Found";
}
function CloseTicket($id,$user,$path)
{
	global $app;
	global $tickets;
	$usertickets = $tickets->GetActiveTickets($user);
	foreach($usertickets as $ticket)
	{
		if($ticket->path == $path)
		{
			if(($ticket->owner == $user)&&($ticket->id == $id))
			{
				$ticket->ChangeState(ACTION_DONE_WAIT);
				return;
			}
		}
	}
	$app->response->error[]  = "Ticket Not Found";
}
function GetTickets($user)
{
	global $app;
	$retval =  array();
	global $tickets;
	$usertickets = $tickets->GetActiveTickets($user);
	foreach($usertickets as $ticket)
	{
		$obj = new StdClass();
		$obj->id = $ticket->id;
		$obj->title = $ticket->title;
		$obj->type=$ticket->type;
		$obj->owner = $ticket->owner;
		$obj->activated = $ticket->activated;
		$obj->activatedby = $ticket->activatedby;
		$obj->days = $ticket->days;
		$obj->path = $ticket->path;	
		$obj->waitstate= $ticket->waitstatetime;
		$obj->instancecount = $ticket->instancecount;
		$obj->statename = $ticket->statename;
		$obj->revertable = $ticket->revertable;
		$obj->nextaction = $ticket->nextaction;
		$obj->number = $ticket->number;
		$retval[] = $obj;
	}
	$app->response->data = $retval;
	SendResponse();
}
?>