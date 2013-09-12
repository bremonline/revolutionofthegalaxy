<html>
<head>
<title>Revolution - Login</title>

<link rel='Stylesheet' href='revolution.css' title='MapStyle' type='text/css'/>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
<script src='eoe.js' type='text/javascript'></script>

<body>
	Note, If you have an account recently, you can log into it for the next round.<br /><br />
<form method='post' action='login_control.php5'>
	<table class='STD' style='width:300px;' >
		<caption class='STD'><B>Revolution<br /><I>Alpha (v0.30) Gettin Real </I></B></caption>
		<tr><td class='STD' colspan=2 style='align:left;'=>Members Login Here</td>
		<tr> <td class='STD' >Player Name</td> <td class='STD' ><input type='text' name='player_name'></td></tr>
		<tr> <td class='STD' >Player Password</td> <td class='STD' ><input type='password' name='password'></td></tr>
	</table>
	<input type='hidden' name='login' value='yes'><br />
	<input type='submit' value='Login'><br />
</form>
<a href='register.php5'>Need to register?</a><br />
</body>
</html>
