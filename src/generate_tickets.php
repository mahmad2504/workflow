<?php
require_once('common.php');
require_once('tickets.php');

$workflow = file_get_contents(WORKFLOWDB);

$workflow = str_replace('\r', '', $workflow);
$workflow = str_replace('\n', '', $workflow);

$workflow  = json_decode($workflow);

foreach($workflow as $ticket)
{
	//foreach($ticket->titles as $title)
	//	echo $title.EOL;

	
	if(isset($ticket->schedule->type))
	{
		//echo $ticket->schedule->type;
		if($ticket->schedule->type == 'single')
		{
			 CreateSingleTicket($ticket);
			 continue;
		}
	}
	CreateDateRange($ticket);
	CreateTickets($ticket);
}

function CreateDateRange($ticket)
{
	$begin = new DateTime($ticket->schedule->start);
	$end = new DateTime();
	//$end = $end->modify('+1 day'); 
	$interval = new DateInterval('P'.$ticket->schedule->freq.'D');
	$daterange = new DatePeriod($begin, $interval ,$end);
	$ticket->daterange = array();
	foreach($daterange as $date)
		$ticket->daterange[] = $date->format('Y-m-d');
}
function IsValidTicket($ticket)
{
	global $app;
	
	if(strstr($ticket->type, PHP_EOL))
		echo $ticket->type." has end of line";

	foreach($ticket->state as $i=>$j)
	{
		$state = $ticket->states[$i];
		$found = 0;
		foreach($app->users as $user)
		{
			if($state->assignee == $user->name)
			{
				$found++;
				break;
			}
		}
		if($found == 0)
		{
			echo $ticket->title." Has invalid assignee ".$state->assignee.EOL;
			return false;
		}
	}
	return true;
}
function CreateSingleTicket($ticket)
{
	//var_dump($ticket);
	
	foreach($ticket->titles as $title)
	{
		$donetickets = array();
		$activetickets = array();
	
		$dir = TICKETS_DIR."/".$ticket->type."/".$title;
		//echo $dir.EOL;
		//$ticket_file = "tickets/".$ticket->type."/".$title."/".Date('Y-m-d');
		if(!file_exists($dir))
			mkdir($dir, 0777, true);
		$ffs = scandir($dir);
		unset($ffs[array_search('.', $ffs, true)]);
		unset($ffs[array_search('..', $ffs, true)]);
		// prevent empty ordered elements
		
		
		foreach($ffs as $ff)
		{
			if(is_dir($dir.'/'.$ff))
			{
				// ignore any directry
			}
			else
			{
				$ticket_file =$dir."/".$ff;
				if(strpos($ticket_file,'.done')!=FALSE)
				{
					$donetickets[] = $ticket_file;
					continue;
				}
				$activetickets[] =  $ticket_file;
			}
		}
		rsort($donetickets);
		$ticketcounts = count($donetickets)+count($activetickets);
		if(count($activetickets)==0) /// we should create one ticket atleast
		{
			//Read last closed ticket and find its end date
			//Compute the start but adding schedled day in end date
			// If end date is today or in past create one...
			if(count($donetickets)>0)
			{
				$lastdoneticket = $donetickets[0];
				$json_string = file_get_contents($lastdoneticket);
				$lastdoneticket = json_decode($json_string);
				$lastdone = $lastdoneticket->done;
				$newdate  = date('Y-m-d', strtotime($lastdone. ' + 1 days'));
				if(strtotime($newdate)<=strtotime(Date('Y-m-d')))
				{
					$ticket_file = $dir."/".Date('Y-m-d');
					$nticket = clone $ticket;
					unset($nticket->titles);
					unset($nticket->daterange);
					$nticket->title = $title;
					//$nticket->state = $nticket->states[0];
					$nticket->state = new StdClass();
					$j = 0;
					$nticket->state->$j = 1;
					$nticket->path = $ticket_file;
					$nticket->number = $nticket->number + $ticketcounts;
					$nticket->created = Date('Y-m-d');
					$nticket->createdby = 'admin';
					foreach($nticket->state as $i=>$count)
					{
						$state = $nticket->states[$i];
						$state->activated = Date('Y-m-d');
						$state->activatedby = 'admin';
					}
					if(IsValidTicket($nticket))
					{
						$json_string = json_encode($nticket);
						file_put_contents($ticket_file,$json_string);
						echo "Created ".$ticket_file.EOL;
					}
				}
			}
			//if no closed ticket and ticked scheduled start date <= today
			// create a ticket of 
			else
			{
				if( strtotime($ticket->schedule->start)<= strtotime(Date('Y-m-d')))
				{
					$ticket_file = $dir."/".Date('Y-m-d');
					$nticket = clone $ticket;
					unset($nticket->titles);
					unset($nticket->daterange);
					$nticket->title = $title;
					//$nticket->state = $nticket->states[0];
					$nticket->state = new StdClass();
					$j = 0;
					$nticket->state->$j = 1;
					$nticket->path = $ticket_file;
					$nticket->number = $nticket->number + $ticketcounts;
					$nticket->created = Date('Y-m-d');
					$nticket->createdby = 'admin';
					foreach($nticket->state as $i=>$count)
					{
						$state = $nticket->states[$i];
						$state->activated = Date('Y-m-d');
						$state->activatedby = 'admin';
						echo $state->activatedby.EOL;
					}
					if(IsValidTicket($nticket))
					{
						$json_string = json_encode($nticket);
						file_put_contents($ticket_file,$json_string);
						echo "Created ".$ticket_file.EOL;
					}
				}
			}
		}
	//rsort($donetickets);
	//rsort($activetickets);
	//var_dump($activetickets);
	//var_dump($donetickets);
	/*$json_string = file_get_contents($ticket_file);
			$ticket = json_decode($json_string);
			$state = $ticket->states[$ticket->state];*/
	}
}

function CreateTickets($ticket)
{	
	foreach($ticket->titles as $title)
	{
		$i=0;
		foreach($ticket->daterange as $date)
		{
			$ticket_folder = TICKETS_DIR."/".$ticket->type."/".$title;
			$ticket_file = TICKETS_DIR."/".$ticket->type."/".$title."/".$date;
			if(!file_exists($ticket_folder))
				mkdir($ticket_folder, 0777, true);
			
			if(!file_exists($ticket_file))
			{
				if(!file_exists($ticket_file.".done"))
				{
					$nticket = clone $ticket;
					unset($nticket->titles);
					unset($nticket->daterange);
					$nticket->title = $title;
					//$nticket->state = $nticket->states[0];
					$nticket->state = new StdClass();
					$j = 0;
					$nticket->state->$j = 1;
					$nticket->path = $ticket_file;
					$nticket->number = $nticket->number + $i;
					$nticket->created = $date;
					foreach($nticket->state as $i=>$count)
					{
					  $state = $nticket->states[$i];
					  $state->activated= $date;
					  $state->activatedby = 'admin';
					}
					if(IsValidTicket($nticket))
					{
						$json_string = json_encode($nticket);
						file_put_contents($ticket_file,$json_string);
						//echo "Created ".$ticket_file.EOL;
					}
				}
			}
			$i++;
			//if(file_exists(
		}
	}
}

function CreateGenericTask($taskname,$assignee,$date,$days)
{
	global $app;
	if(strlen($date)>0)
	{
		/*if(strtotime($date) < strtotime(Date('Y-m-d')))
		{
			$app->response->error[] = 'Invalid Date';
				return null;	
		}*/
	}
	else
		$date = Date('Y-m-d');
	
	$task =  new StdClass();
	$task->type = 'Task';
	$task->titles = array();
	$task->titles[0] = $taskname;
	$task->schedule = new StdClass();
	$task->schedule->start = $date;
	$task->schedule->freq = 0;
	$task->number = 0;
	$task->states = array();
	$task->states[0] = new StdClass();
	$task->states[0]->name = 'Task';
	$task->states[0]->assignee = $assignee;
	if($days >= 0)
		$task->states[0]->days = $days;
	
	$task->states[1] = new StdClass();
	$task->states[1]->name = 'Approval';
	$task->states[1]->assignee = 'fouzia';
	$task->states[1]->days = 1;
	$task->states[1]->revert = 0;
	
	CreateSingleTimeTicket($task,$assignee);
	
}
function CreateSingleTimeTicket($ticket,$assignee)
{
	global $app;
	$title = $ticket->titles[0];
	$date = $ticket->schedule->start;
	$ticket_path = TICKETS_DIR.'/Tasks/'.$title."-".$assignee;
	$ticket_file = $ticket_path."/".$date;
	if(file_exists($ticket_file))
	{
		$app->response->error[] = 'Ticket Exists';
		return null;;
	}
	if(!file_exists($ticket_path))
		mkdir($ticket_path, 0777, true);
	
	/*file_put_contents($ticket_path."/".$date,json_encode($task));*/

	$nticket = clone $ticket;
	unset($nticket->titles);
	unset($nticket->daterange);
	$nticket->title = $title;
	$nticket->state = new StdClass();
	$j = 0;
	$nticket->state->$j = 1;
	$nticket->path = $ticket_file;
	$nticket->number = $nticket->number;
	$nticket->created = $date;
	$nticket->createdby = 'admin';
	foreach($nticket->state as $i=>$count)
	{
		$state = $nticket->states[$i];
		$state->activated = $date;
		$state->activatedby = 'admin';
	}
	if(IsValidTicket($nticket))
	{
		$json_string = json_encode($nticket);
		file_put_contents($ticket_file,$json_string);
	}	
	else
	{
		$app->response->error[] = 'Invalid User';
		return null;
	}
	
	
}
?>