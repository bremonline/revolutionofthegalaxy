<?php
	require_once('db_fns.php5'); 

	session_start();

	$player_name = $_SESSION['player_name'];

	$parent_id=$_REQUEST['parent_id'];
	$message_id=$_REQUEST['message_id'];
	$topic_id=$_REQUEST['topic_id'];
	$type=$_REQUEST['type'];
	$category=$_REQUEST['category'];

/*	
  $conn = db_connect();
	$query = "select * from conversation_message where name='$name' and type='$type' and category='$category'"; 
	$result = $conn->query($query);
	if ($result->num_rows > 0) {
		$row = $result->fetch_object();
		$text = htmlspecialchars($row->text);
	} else {
		$text="";
	}
*/
	
	do_header();
	
	echo "<body>";
	
	echo "<FORM method='post' action='conversation_complete.php5'>\n";
	echo "     <INPUT type='hidden' name='parent_id' value='$parent_id'/>\n";
	echo "     <INPUT type='hidden' name='message_id' value='$message_id'/>\n";
	echo "     <INPUT type='hidden' name='topic_id' value='$topic_id'/>\n";
	echo "     <INPUT type='hidden' name='type' value='$type'/>\n";
	echo "     <INPUT type='hidden' name='category' value='$category'/>\n";
	echo "Subject: <INPUT type='text' name='subject' size='80' value='$subject' /><br /><br />\n";
	echo "<TEXTAREA name='text' rows='20' cols='60' width='100%'>$text</TEXTAREA>";
	echo "<BR /> <INPUT type='submit' value='Submit'>\n";
	echo "</TD></TR>\n";
	echo "</FORM>\n";
	echo "</html>";

function do_header() {
	echo "
<html>
<head>
  <title>Revolution - Edit Conversation</title>
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