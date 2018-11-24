<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="icon" href="assets/css/images/icon.png">
    <link rel="stylesheet" href="assets/css/jquery.mobile.min.css" />
	<link rel="stylesheet" href="assets/css/app.css" />
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.mobile.min.js"></script>
	<script src="assets/js/moment.js"></script> 
	<script src="assets/js/app.js"></script>
	<script>
		var page="logout"
	</script>
</head>
<body>
	<h1>Logout successfully</h1>
</body>
</html>