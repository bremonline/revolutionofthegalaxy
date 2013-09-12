<?php
	require_once('misc_fns.php5'); 

	session_start();
	unset($_SESSION['player_name']);
	setcookie("auth", "", time());
	session_destroy(); // destroy session
	setcookie("PHPSESSID","",time()-3600,"/"); // delete session cookie 

	
	echo "You are now logged out.  To login again please go to the <A href='login.php5'>Login Page</A><BR />\n";

?>