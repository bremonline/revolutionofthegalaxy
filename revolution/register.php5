<html>
<head>
<title>Revolution - Login</title>

<link rel='Stylesheet' href='revolution.css' title='MapStyle' type='text/css'/>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
<script src='eoe.js' type='text/javascript'></script>
<body>
<form method='post' action='register_control.php5'>
<table class='STD' style='width:400px;' >
		<caption class='STD'><B>Revolution<br /><I>Alpha (v0.10)</I></B></caption>
		<tr><td class='STD' colspan=2 style='align:left;'=>Please Enter Registration Information </td>
		<tr><td class='STD' >Player Name </td><td  class='STD' valign='top'><input type='text' name='player_name' size=30 maxlength=16 /></td></tr>
		<tr><td class='STD' >of </td><td  class='STD' valign='top'><input type='text' name='location' size=30 maxlength=16 /></td></tr>
		<tr><td class='STD' >Player Password</td><td  class='STD' ><input type='password' name='new_password' size=30 maxlength=16 /></td></tr>
		<tr><td class='STD' >Confirm Password</td><td  class='STD' ><input type='password' name='new_password2' size=30 maxlength=16 /></td></tr>
		<tr><td class='STD' >Email Address</td><td  class='STD' ><input type='text' name='email' size=30 maxlength=100 /></td></tr>
		<tr><td class='STD' >Ever play Evolution?</td><td  class='STD' >
			<select name='help'>
				<option value='evolution'>Yes</option>
				<option value='novice'>No</option>
			</select>
		</td></tr>
</table>
<input type='submit' value='Register'>
</form>

</body>
</html>
