<?php 

require 'Tmilos/vendor/autoload.php';

use Tmilos\GoogleCharts\DataTable\Column;
use Tmilos\GoogleCharts\DataTable\ColumnType;
use Tmilos\GoogleCharts\DataTable\DataTable;
use Tmilos\GoogleCharts\DataTable\Row;
use Tmilos\Value\AbstractEnum;

$dataTable = new DataTable([
	Column::create(ColumnType::STRING())->setLabel('Assignee'),
	Column::create(ColumnType::STRING())->setLabel('Titles'),
	Column::create(ColumnType::STRING())->setLabel('Type'),
	Column::create(ColumnType::STRING())->setLabel('State'),
	Column::create(ColumnType::STRING())->setLabel('Date'),
	Column::create(ColumnType::NUMBER())->setLabel('Old (Days)'),
	Column::create(ColumnType::NUMBER())->setLabel('Deadline (Days)'),
	Column::create(ColumnType::STRING())->setLabel('Late (Days)')
	//Column::create(ColumnType::NUMBER())->setLabel('Number'),
	]);

$tickets =  array();

$rowdata = array();

$tickets = GetTickets('tickets');
foreach($tickets  as $ticket)
{
	$state = $ticket->states[$ticket->state];
	$row = array();
	
	$row[] = $state->assignee;
	$row[] = $ticket->number." ".$ticket->title;
	$row[] = $ticket->type;
	$row[] = $state->name;
	$row[] = $state->activated;
	
	$date1=date_create($state->activated);
	$date2=date_create(Date('Y-m-d'));
	$interval =date_diff($date1,$date2);
	$days = $interval->format("%a");
	//$days = sprintf("%01d", $days);
	
	//	$row[] = '0';
	//else if($days==1)
	//	$row[] = $days." day";
	//else if($days>1)
	//	$row[] = $days." days";
		
	$row[] = (float)trim($days);
	
	if(isset($state->days))
	{
		$row[] = $state->days;
		$late =  $state->days - $days;
		if($late < 0)
			$row[] = ($late*-1)." days";
		else
			$row[] = '';
	}
	else
	{
		$row[] = '';
		$row[] = '';
	}
	
	
	$rowdata[] = $row;
}
//var_dump($tickets);
//$row[] = 0 ;
//$rowdata[] = $row;
	
	
//}
$dataTable->addRows($rowdata);
echo json_encode($dataTable);


function GetTickets($dir){
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
			GetTickets($dir.'/'.$ff);
		}
		else
		{
			$ticket_file =$dir."/".$ff;
			if(strpos($ticket_file,'.done')!=FALSE)
				continue;
			
			$json_string = file_get_contents($ticket_file);
			$ticket = json_decode($json_string);
			$state = $ticket->states[$ticket->state];
			$tickets[] = $ticket;
		}
    }
	return $tickets;
}
?>