<?php
	require_once('db_fns.php5'); 
	require_once('news_model.php5'); 
	
	session_start();
	
	$name=$_REQUEST['name'];
	$type=$_REQUEST['type'];
	$category=$_REQUEST['category'];
	$author=$_REQUEST['author'];
	$text=$_REQUEST['text'];

	do_header();
	
	update_description($name, $type, $category, $author, $text);

	// Tell me when someone updated any description
	$nm = new NewsModel();
	if ($author != 'judal')
		$nm->add_new_news('judal', 'player', 'misc', "$author updated a description for $type:$category:$name", "New Description follows: <BR /><BR />$text" );

	echo "<body onload='javascript:close_edit_window()'>";

	echo "</body>";
	echo "</html>";

function do_header() {
	echo "
<html>
<head>
  <title>Revolution - Edit Description</title>
  <link rel='Stylesheet' href='revolution.css' title='Style' type='text/css'/> 
  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'> 
<script src='revolution.js' type='text/javascript'></script>
</head>
";
}

function update_description($name, $type, $category, $author, $text) {
  $conn = db_connect();
	$query = "select * from description where name='$name' and type='$type' and category='$category'"; 
	$result = $conn->query($query);
	
	if ($result->num_rows > 0) {
		$query = "update description 
			set category='$category', author='$author', text='$text', last_edited=NOW()
			where name='$name' and type='$type' and category='$category'"; 
		$result = $conn->query($query);
		
	} else {
		$query = "select max(ordinality) as max from description where category='$category' and type='$type'";
		$result = $conn->query($query);
		$row = $result->fetch_object(); 
		$max = $row->max;
		$newmax = $max + 1.0;
		
		
		$query = "insert into description values ('$name', '$category', '$type', '$author', '$text', NOW(), $newmax)"; 
		$result = $conn->query($query);
	}
}

?>