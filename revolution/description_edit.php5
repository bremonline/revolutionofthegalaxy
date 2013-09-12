<?php
	require_once('db_fns.php5'); 

	session_start();

	$name=$_REQUEST['name'];
	$type=$_REQUEST['type'];
	$category=$_REQUEST['category'];
	$author=$_REQUEST['author'];
	$player_name = $_SESSION['player_name'];
	
  $conn = db_connect();
	$query = "select * from description where name='$name' and type='$type' and category='$category'"; 
	$result = $conn->query($query);
	if ($result->num_rows > 0) {
		$row = $result->fetch_object();
		$text = htmlspecialchars($row->text);
	} else {
		$text="";
	}
	
	do_header();
	
	echo "<body>";
	
	echo "<FORM method='post' action='description_complete.php5'>\n";
	echo "     <INPUT type='hidden' name='name' value='$name'/>\n";
	echo "     <INPUT type='hidden' name='type' value='$type'/>\n";
	echo "     <INPUT type='hidden' name='author' value='$author'/>\n";
	echo "     <INPUT type='hidden' name='category' value='$category'/>\n";
	echo "<TEXTAREA name='text' rows='20' cols='60' width='100%'>$text</TEXTAREA>";
	echo "<BR /> <INPUT type='submit' value='Edit Text'>\n";
	echo "</TD></TR>\n";
	echo "</FORM>\n";
	
	echo "</html>";

function do_header() {
	echo "
<html>
<head>
  <title>Revolution - Edit Description</title>
  <link rel='Stylesheet' href='revolution.css' title='Style' type='text/css'/> 
  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'> 
<script src='revolution.js' type='text/javascript'></script>
";
	do_tiny_mce_header();
echo "</head>";
}

function do_tiny_mce_header() {
	echo "
<!-- TinyMCE -->
<script type=\"text/javascript\" src=\"/tinymce/jscripts/tiny_mce/tiny_mce.js\"></script>
<script type=\"text/javascript\">
	tinyMCE.init({
		mode : \"textareas\",
		theme : \"advanced\",
	
		plugins : \"safari,emotions,preview,searchreplace,contextmenu,paste,noneditable,visualchars,nonbreaking\",
	
		// Theme options
		theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,emotions,hr,removeformat,preview,help\",
		theme_advanced_buttons2 : \"cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,image,cleanup,code\",
		theme_advanced_buttons3 : \"\",
		theme_advanced_toolbar_location : \"top\",
		theme_advanced_toolbar_align : \"left\",
		theme_advanced_statusbar_location : \"bottom\",
		theme_advanced_resizing : true,
		theme_advanced_resizing_use_cookie : false
	});
</script>
<!-- /TinyMCE -->	";
		
}
?>