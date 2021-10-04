<?php
    require_once 'config.php';
    //document is only called when user is logged in

    //if user is logged in but account got locked, sign out (update the active values too)
    //attempt to connect to database
	$connection = mysqli_connect($server, $connectUser, $connectPass);
	if (!$connection)
	{
		die("Database connection failed: " . mysqli_error($connection));
	}

	//attempt to select the database
	$db = mysqli_select_db($connection, $database);
	if(!$db)
	{
		die("Database selection failed: " . mysqli_error($connection));
	}
?>