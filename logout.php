<?php
session_start();
session_unset();
session_destroy();
//header("location:index.php");
//echo "<h1>Logout successfully</h1>";




?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0"/>
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script>
        $(document).on("mobileinit", function () {
          $.mobile.hashListeningEnabled = false;
          $.mobile.pushStateEnabled = false;
          $.mobile.changePage.defaults.changeHash = false;
		  //$.mobile.autoInitializePage = false;
        });
		$( document ).ready(function() 
		{

		});
    </script> 
    <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="js/index.js"></script>
</head>
<body>
	<h1>Logout successfully</h1>
</body>
</html>