<?php
define('EOL','<BR>');
$users = file_get_contents('users.json');
$users  = json_decode($users);

$workflow = file_get_contents('workflow.json');
$workflow  = json_decode($workflow);
foreach($workflow as $ticket)
{
	//foreach($ticket->name as $name)
	//	var_dump($ticket);
	CreateDateRange($ticket);
	CreateTickets($ticket);
}
echo "Done".EOL;

function CreateDateRange($ticket)
{
	$begin = new DateTime($ticket->schedule->start);
	$end = new DateTime();
	$end = $end->modify('+1 day'); 
	$interval = new DateInterval('P'.$ticket->schedule->freq);
	$daterange = new DatePeriod($begin, $interval ,$end);
	$ticket->daterange = array();
	foreach($daterange as $date)
		$ticket->daterange[] = $date->format('Y-m-d');
}
function IsValidTicket($ticket)
{
	global $users;
	$state = $ticket->states[$ticket->state];
	
	if(!array_key_exists($state->assignee,$users))
	{
		echo $ticket->title." Has invalid assignee ".$state->assignee.EOL;
		return false;
	}
	return true;
}
function CreateTickets($ticket)
{
	foreach($ticket->titles as $title)
	{
		$i=0;
		foreach($ticket->daterange as $date)
		{
			$ticket_folder = "tickets/".$ticket->type."/".$title;
			$ticket_file = "tickets/".$ticket->type."/".$title."/".$date;
			if(!file_exists($ticket_folder))
				mkdir($ticket_folder, 0777, true);
			
			if(!file_exists($ticket_file))
			{
				if(!file_exists($ticket_file.".done"))
				{
					$nticket = clone $ticket;
					unset($nticket->titles);
					$nticket->title = $title;
					//$nticket->state = $nticket->states[0];
					$nticket->state = 0;
					$nticket->path = $ticket_file;
					$nticket->number = $nticket->number + $i;
					$nticket->created = $date;
					$state = $nticket->states[$nticket->state];
					$state->activated= $date;
					if(IsValidTicket($nticket))
					{
						$json_string = json_encode($nticket);
						file_put_contents($ticket_file,$json_string);
						echo "Created ".$ticket_file.EOL;
					}
				}
			}
			$i++;
			//if(file_exists(
		}
	}
}
?>