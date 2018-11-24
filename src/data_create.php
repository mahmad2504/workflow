<?php
require_once('common.php');
require_once('generate_tickets.php');

$params = ParseParams();

CreateGenericTask($params['taskname'],$params['assignee'],$params['date'],$params['days']);
SendResponse();


?>