<?php
require_once('src/common.php');
?>
<!DOCTYPE html>
<html>
<head> 
    <title>Login</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="icon" href="assets/css/images/icon.png">
    <link rel="stylesheet" href="assets/css/jquery.mobile.min.css" />
	<link rel="stylesheet" href="assets/css/app.css" />
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.mobile.min.js"></script>
	<script src="assets/js/moment.js"></script> 
	<script src="assets/js/app.js"></script>
	<script>
		var page="login"
	</script>
</head> 
<body>
    <div data-role="page" id="login" data-theme="a">
		<div data-role="toolbar" data-type="header" data-position="fixed">    
            <h3>Login</h3>
        </div>
        <div data-role="content">
            <form id="check-user" class="ui-body ui-body-a ui-corner-all" data-ajax="false">
                <fieldset>
					<span id='fieldset'>
					<div data-role="fieldcontain">
                        <label for="username">Username</label>
                        <input type="text" value="" name="username" id="username"/>
                    </div>                                 
                    <div data-role="fieldcontain">                                     
                        <label for="password">Password</label>
                        <input type="password" value="" name="password" id="password"/>
                    </div>
					<a id="changepasswordlink" style="font-size:10px;float:rigt;" href="" onclick="OnChangePasswordClicked();" >Change Password</a>
					<div data-role="fieldcontain">
                        <label for="opassword" id="lopassword" style="display: none;">Old Password</label>
                        <input type="text" value="" name="opassword" id="opassword" style="display: none;"/>
                    </div>
					<div data-role="fieldcontain">
                        <label for="npassword" id="lnpassword" style="display: none;">New Password</label>
                        <input type="text" value="" name="npassword" id="npassword" style="display: none;"/>
                    </div>
					</span>
					<input type="button" data-theme="b" name="submit" id="submit" value="Login" >
                </fieldset>
            </form>                             
        </div>
		<div data-history="false" data-role="popup" id="pwdchange_popup" data-position-to="window" data-transition="turn"><p>Password Changed Successfully<p></div>
        <div data-history="false" data-role="popup" id="invalidcred_popup" data-position-to="window" data-transition="turn"><p>Invalid Credentials<p></div>
        <div data-history="false" data-role="popup" id="error_popup" data-position-to="window" data-transition="turn"><p>Network Error<p></div>
        
		<div data-theme="a" data-role="footer" data-position="fixed">
			
        </div>
    </div>
</body>
</html>