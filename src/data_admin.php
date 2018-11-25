<?php 
require_once("common.php");
require_once("session.php");
$params = ParseParams();
if($app->admin != 1)
{
	$app->response->error[] = "Unauthorized Access";
	SendResponse();
}
require_once('tickets.php');
require_once('generate_tickets.php');

require 'Tmilos/src/DataTable/DataTable.php';
require 'Tmilos/src/DataTable/Column.php';
require 'Tmilos/src/Value/Value.php';
require 'Tmilos/src/Value/Enum.php';
require 'Tmilos/src/Value/AbstractValue.php';
require 'Tmilos/src/Value/AbstractEnum.php';
require 'Tmilos/src/DataTable/ColumnType.php';
require 'Tmilos/src/DataTable/Row.php';
require 'Tmilos/src/DataTable/Cell.php';

use Tmilos\GoogleCharts\DataTable\Column;
use Tmilos\GoogleCharts\DataTable\ColumnType;
use Tmilos\GoogleCharts\DataTable\DataTable;
use Tmilos\GoogleCharts\DataTable\Row;
use Tmilos\Value\AbstractEnum;

$dataTable = new DataTable([
	Column::create(ColumnType::STRING())->setLabel('Assignee'),
	Column::create(ColumnType::NUMBER())->setLabel('Number'),
	Column::create(ColumnType::STRING())->setLabel('Titles'),
	Column::create(ColumnType::STRING())->setLabel('Type'),
	Column::create(ColumnType::STRING())->setLabel('State'),
	Column::create(ColumnType::STRING())->setLabel('Date'),
	Column::create(ColumnType::NUMBER())->setLabel('Old (Days)'),
	Column::create(ColumnType::NUMBER())->setLabel('Deadline (Days)'),
	Column::create(ColumnType::STRING())->setLabel('Late (Days)'),
	Column::create(ColumnType::STRING())->setLabel('Link'),
	Column::create(ColumnType::STRING())->setLabel('Path'),
	//Column::create(ColumnType::NUMBER())->setLabel('Number'),
	]);

$tickets =  array();

$rowdata = array();


if(!file_exists(TICKETS_DIR))
{
	SendResponse();
	return ;
}

$tickets = new Tickets();
$tickets->DoTimeWaitStates();
$tkts = $tickets->GetAllTickets();
foreach($tkts as $ticket)
{
	$row = array();
	$row[] = $ticket->owner;
	$row[] = $ticket->number;
	$row[] = $ticket->title;
	$row[] = $ticket->type;
	$row[] = $ticket->statename;
	$row[] = $ticket->activated;
	
	$date1=date_create($ticket->activated);
	$date2=date_create(Date('Y-m-d'));
	$interval =date_diff($date1,$date2);
	$days = $interval->format("%R%a");
	$row[] = (float)trim($days);

	if ($ticket->days != null)
	{
		$row[] = $ticket->days;
		$late =  $ticket->days - $days;
		if($late < 0)
			$row[] = ($late*-1)." days";
		else
			$row[] = '';
	}
	else
	{
		$row[] = '';
		$row[] = 'NA';
	}
	$row[] = '';
	$row[] = $ticket->path;
	$rowdata[] = $row;
}
$dataTable->addRows($rowdata);
echo json_encode($dataTable);
return;

?>