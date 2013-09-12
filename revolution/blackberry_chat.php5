<?php
	require_once('db_fns.php5'); 
	require_once('misc_fns.php5'); 
	require_once('player_data.php5'); 
	
	session_start();
	check_valid_user();
	if ($_REQUEST["action"] == 'new_message') send_message($_REQUEST["text"]);
	display_stylesheet();
	if ($_REQUEST["action"] == 'big_refresh') display_messages(50);
	else display_messages(10);
	display_send_form();
// -- End Main Page

function display_stylesheet() {
	echo "
<style type='text/css'>

.w{
color:white;
font-size:x-small;
}

.g{
color:#AAA;
font-size:x-small;}

.b{
color:black;
font-size:x-small;
}
</style>
";	
}

function display_messages($number) {
	$messages = get_chat_messages($number);
	
	foreach(array_reverse($messages) as $message) {
		$player = $message["player_name"];
		$time = $message["post_time"];
		$text = $message["text"];
		
		echo "<SPAN class='g'>$time</SPAN> <SPAN class='g'>$player</SPAN> <SPAN class='b'><B>$text</B></SPAN><BR />";
	}

}

function display_send_form() {
  $player_name = $_SESSION["player_name"];
	echo "<FORM method='get' action='blackberry_chat.php5'>\n";
	echo "     <INPUT type='hidden' name='action' value='new_message'/>\n";
	echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
	echo "     <INPUT type='hidden' name='player_name' value='$player_name'/>\n";
	echo "<INPUT name='text' size='30' />";
	echo "  <INPUT type='submit' value='Send' />";
	echo "</FORM><BR />\n";

	echo "<hr style='height:2px;'/>\n";

	echo "<FORM method='get' action='blackberry_chat.php5'>\n";
	echo "     <INPUT type='hidden' name='action' value='refresh'/>\n";
	echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
	echo "  <INPUT type='submit' value='Refresh' />";
	echo "</FORM>\n";

	echo "<FORM method='get' action='blackberry_chat.php5'>\n";
	echo "     <INPUT type='hidden' name='action' value='big_refresh'/>\n";
	echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
	echo "  <INPUT type='submit' value='Show Last 50 messages' />";
	echo "</FORM><BR />\n";

}
	
function send_message($text) {
	if (strlen($text) == 0) return;
	
  $player_name = $_SESSION["player_name"];

	$encoded_message = convertString($text);

	$conn = db_connect();
	$query = "INSERT INTO chat_message VALUES (0, 'General', 'General', 'Revolution', '$player_name', NOW(), '$encoded_message')";
	$result = $conn->query($query);
}

function get_chat_messages($number) {
	$conn = db_connect();
	$query = "SELECT id, player_name, date_format(post_time, '%H:%i') as post_time, text 
		FROM chat_message  
		WHERE chat_type = 'General' 
		  AND chat_channel = 'Revolution'
		  ORDER BY id DESC
		  LIMIT 0, $number"; 
  $result = $conn->query($query);
	$chat_messages = array();
	for ($count=0; $row = $result->fetch_object(); $count++) {
		$chat_messages[$count]["player_name"] = $row->player_name;
		$chat_messages[$count]["post_time"] = $row->post_time;
		$chat_messages[$count]["text"] = $row->text;
	}
	
	return $chat_messages;
}
	
?>