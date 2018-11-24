<?php
require_once('src/common.php');
?>
<?php
require_once('src/common.php');
require_once('src/session.php');
if($app->admin == 0)
{
	$app->response->error[] = "Unathorized access";
	SendResponse();
}
?>
<!DOCTYPE html>
<html>
<head> 
	<title>Create Task</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="icon" href="assets/css/images/icon.png">
    <link rel="stylesheet" href="assets/css/jquery.mobile.min.css" />
	<link rel="stylesheet" href="assets/css/app.css" />
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.mobile.min.js"></script>
	<script src="assets/js/moment.js"></script> 
	<script src="assets/js/app.js"></script>
	<script>
		var user="<?php echo $app->user;?>";
		var page="create"
	</script>
</head> 
<body>
    <div data-role="page" id="login" data-theme="b">
        <div data-role="toolbar" data-type="header" data-position="fixed">       
            <h3>Create</h3>
        </div>
        <div data-role="content">
            <form id="check-user" class="ui-body ui-body-a ui-corner-all" data-ajax="false" autocomplete="on">
                <fieldset>
					<div data-role="fieldcontain">
						<label for="select-choice-min" class="select">Assigned To</label>
						<select name="select-choice-min" id="assignee" data-mini="true">
						  <?php
							$i=1;
							foreach($app->users as $user)
							{
								echo '<option value="'.$i.'">'.$user->name.'</option>';
								$i++;
							}
							?>
						</select>
					</div>			
					<div data-role="fieldcontain">
                        <label for="taskname">Task Name</label>
                        <input type="text" value="" name="taskname" id="taskname" placeholder="Task Name"/>
                    </div>
					
					
                    <div data-role="fieldcontain">
                        <label for="date">Date</label>
						<input type="date" name="date" id="date" value="" placeholder="Assigned to">
                    </div>
					<div data-role="fieldcontain">
						<label for="number-1">Days</label>
						<input type="number" data-clear-btn="false" name="number-1" id="days" value="2">
					</div>
					
					
					<input type="button" data-theme="b" name="submit" id="createtask" value="Create" >
					<input type="button" data-theme="b" name="back" id="back" value="Back" >
                </fieldset>
            </form>                             
        </div>
		<div data-history="false" data-role="popup" id="taskcreated_popup" data-position-to="window" data-transition="turn"><p>Task Created<p></div>
        <div data-history="false" data-role="popup" id="taskcreaterror_popup" data-position-to="window" data-transition="turn"><p>Task Already Exist<p></div>
		<div data-role="popup" id="error_popup"><p>Network Error<p></div>
		<div data-theme="a" data-role="footer" data-position="fixed">	
        </div>
    </div>
</body>
</html>