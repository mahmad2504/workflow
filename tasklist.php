<?php
require_once('src/common.php');
require_once('src/session.php');
?>
<!DOCTYPE html> 
<html> 
<head> 
    <title><?php echo ucfirst($app->user);?>  Task List</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="icon" href="assets/css/images/icon.png">
    <link rel="stylesheet" href="assets/css/jquery.mobile.min.css" />
	<link rel="stylesheet" href="assets/css/app.css" />
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.mobile.min.js"></script>
	<script src="assets/js/moment.js"></script> 
	<script src="assets/js/app.js"></script>
	<script>
		//<meta http-equiv="refresh" content="30"/>
		var user="<?php echo $app->user;?>";
		var admin=<?php echo $app->admin;?>;
		var page='tasklist';
	</script>
</head> 
<body> 
 	
<div data-role="page" data-theme="a">
	<div data-role="content">
		
		<ul id="listview" data-role="listview" data-inset="true" data-filter="true" data-input="#filterable-input" >
			
		</ul>
	</div>
	<p>
    <a id="createbtn" href="create.php" data-ajax="false" data-role="button" style="display: none;">Create</a>
	<a id="adminbtn" href="admin.php?details=0" data-ajax="false" data-role="button" style="display: none;">Admin</a>
	<a id="logoutbtn" href="#" data-role="button" >Logout</a>
</p>
   <!-- /content -->
	<div data-role="toolbar" data-type="footer" data-position="fixed">
           <h1 style="font-size:8px;">Workflow 2.0.0<br><a href="http://www.on2sol.com">Custom Software Development</a><br>+92-3008465671</h1>
    </div>

	<div data-role="popup" id="logout_popup">
		<p>Closing Session<p>
	</div>
	
	<div data-role="popup" id="error_popup">
		<p>Network Error<p>
	</div>
	<div data-history="false" data-role="popup" id="reopenerror" data-theme="a">
		<p>Timeout Cannot Reopen<p>
	</div>
	
	<div data-history="false" data-role="popup" id="reverterror" data-theme="a">
		<p>Revert Error<p>
	</div>
	
	
	<div data-history="false" data-role="popup" id="closepopup" data-theme="a">
		&nbsp<a data-mini="true" data-inline="true" data-role="button" href="" data-rel="back" onclick="OnCloseTicket();">Close Ticket?</a>
	</div>
	
	<div data-history="false" data-role="popup" id="revertpopup" data-theme="a">
		&nbsp<a data-mini="true" data-inline="true" data-role="button" href="" data-rel="back" onclick="OnCloseTicket();">Close Ticket</a>
		<a data-mini="true" data-inline="true" data-role="button" href="" data-rel="back" onclick="OnRevertTicket();">Revert Ticket?</a>
	</div>
	
	<div data-history="false" data-role="popup" id="reopenpopup" data-theme="a">
		&nbsp<a data-mini="true" data-inline="true" data-role="button" href="" data-rel="back" onclick="OnReopenTicket();">Reopen Ticket?</a>
	</div>
	
	
	
</div><!-- /page -->


        
 
</body>
</html>