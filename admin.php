<?php
require_once('src/common.php');
require_once('src/session.php');
if($app->admin != 1)
{
	$app->response->error[] = "Unauthorized Access";
	SendResponse();
}
$params = ParseParams();
?>
<!doctype html>
<html lang="en-au">
    <head>
        <title><?php echo ucfirst($app->user);?>  Admin Panel</title> 
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<link rel="icon" href="assets/css/images/icon.png">
		<link rel="stylesheet" href="assets/css/jquery.mobile.min.css" />
		<link rel="stylesheet" href="assets/css/app.css" />
		<script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/jquery.mobile.min.js"></script>
		<script src="assets/js/moment.js"></script> 
		<script type = "text/javascript" src = "assets/js/loader.js"></script>
		<script type = "text/javascript">
         google.charts.load('current', {packages: ['corechart','line']});
		</script>
		<script src="assets/js/datatable.js"></script>
		<script>
			//<meta http-equiv="refresh" content="30"/>
			var user="<?php echo $app->user;?>";
			var admin=<?php echo $app->admin;?>;
			var page='tasklist';
			var details=0;
			<?php 
				if(isset($params['details']))
					echo 'details = 1;';
			
			?>;
		</script>
	<style>
	.center {
		position: fixed; /* or absolute */
		top: 10%;
		left: 50%;
		}
	</style>
    </head>
    <body>
		<div data-role="page" id="admin" data-theme="a">
			<div data-role="toolbar" data-type="header" data-position="fixed">    
				<h3>Admin Panel</h3>
			</div>
			<div data-role="content">
				<div id="chart_div"></div>
				<form method="get" action="index.php">
					<button type="submit">Go Back</button><br>
				</form>
			</div>
			<div data-role="popup" id="error_popup"><p>Network Error<p></div>
			<div data-role="popup" id="deletsuccess_popup"><p>Ticket Deleted<p></div>
			<div data-history="false" data-role="popup" id="deletepopup" data-theme="a">
				&nbsp<a data-mini="true" data-inline="true" data-role="button" href="" data-rel="back" onclick="OnDeleteTicket();">Delete Ticket?</a>
			</div>
		</div>
    </body>
</html>