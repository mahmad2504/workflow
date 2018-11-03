<?php
session_start();
$username = '';
if(isset($_SESSION['token']))
{
	$encstr = $_SESSION['token'];
	$tokenstr = base64_decode($encstr);
    $username = explode('&',$tokenstr)[0];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>jQM Complex Demo</title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0"/>
    <link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css" />
    <script src="js/jquery-1.9.1.min.js"></script>
    <script>
        $(document).on("mobileinit", function () {
          $.mobile.hashListeningEnabled = false;
          $.mobile.pushStateEnabled = false;
          $.mobile.changePage.defaults.changeHash = false;
		  $.mobile.autoInitializePage = false;
        });
		$( document ).ready(function() 
		{
			<?php 
			echo 'var user="'.$username.'";';
			?>
			 //window.location.hash = 'second';
			 $.mobile.initializePage();
			 if(user.length > 0)
			 {
				//$.mobile.changePage("#second");
				LoadTickets(user);
				//$('#page-2-title').text(user+" Tasks");
				//LoadTickets(user);
			 }
			//console.log("Page loaded");
			//$.mobile.changePage("#second");
		});
		 
    </script> 
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="js/index.js"></script>
	<script src="js/moment.js"></script>
</head>
<body>
    <div data-role="page" id="login" data-theme="b">
        <div data-role="header" data-theme="a">       
            <h3>Login</h3>
        </div>
  
        <div data-role="content">
            <form id="check-user" class="ui-body ui-body-a ui-corner-all" data-ajax="false">
                <fieldset>
					<span id='fieldset'>
                    <div data-role="fieldcontain">
                        <label for="username">Enter your username:</label>
                        <input type="text" value="" name="username" id="username"/>
                    </div>                                 
                    <div data-role="fieldcontain">                                     
                        <label for="password">Enter your password:</label>
                        <input type="password" value="" name="password" id="password"/>
                    </div>
					</span>
					<a id="changepasswordlink" style="font-size:10px;float:rigt;" href="" onclick="OnChangePasswordClicked();" >Change Password</a>
                    <input type="button" data-theme="b" name="submit" id="submit" value="Submit" onclick="OnPasswordChange();" >
                </fieldset>
            </form>                             
        </div>
  
        <div data-theme="a" data-role="footer" data-position="fixed">
			
        </div>
    </div>
	
    <div data-role="page" id="second">
        <div data-theme="a" data-role="header">
            <a href="logout.php" class="ui-btn-left ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete" id="back-btn">Logout</a>      
            <h3 id='page-2-title'>My Tasks</h3>
        </div>
  
        <div data-role="content">
			  <ul id="listview" data-role="listview" data-split-icon="gear" data-split-theme="a" data-inset="true">
				
				
			</ul>
			<div data-role="popup" id="purchase" data-theme="a" data-overlay-theme="b" class="ui-content" style="max-width:340px; padding-bottom:2em;">
				<h3>Close this ticket?</h3>
				<a href="" data-rel="back" class="ui-shadow ui-btn ui-corner-all ui-btn-b ui-icon-check ui-btn-icon-left ui-btn-inline ui-mini" onclick="OnDoneClicked();">Done</a>
			</div>
        </div>
        <div data-theme="a" data-role="footer" data-position="fixed">
            <h1 style="font-size:8px;">Workflow 1.0.0<br><a href="http://www.on2sol.com">www.on2sol.com</a></h1>
        </div>
    </div>
</body>
</html>