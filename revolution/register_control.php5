<?php
	require_once('misc_fns.php5'); 
	require_once('news_model.php5'); 
	require_once('player_data.php5'); 
// Add the user to the SMF forum database
	require_once ("/home/content/b/r/e/bremonline/html/revo_smf/SSI.php");
	require("forum/smf_bridge.php5");

	session_start();
	echo "<link rel='Stylesheet' href='revolution.css' title='MapStyle' type='text/css'/>\n";

  //create short variable names
  $player_name=$_POST['player_name'];
  $new_password=$_POST['new_password'];
  $new_password2=$_POST['new_password2'];
  $location=$_POST['location'];
  $email=$_POST['email'];
  $help=$_POST['help'];
  
	// passwords not the same 
	if ($new_password != $new_password2) {
		echo "The passwords you entered do not match - please go back and try again.<BR />";
		echo "To try to login please go to the <A href='login.php5>Login Page</A><BR />";
		echo "To try and register again go to <A href='register.php5>Register Page</A><BR />";
		exit();
	}
	
	$player = new PlayerData();
	if (!$player->check_available_player_name($player_name)) {
		echo "That player name is already taken - please go back and try again.<BR />";
		echo "To try to login please go to the <A href='login.php5'>Login Page</A><BR />";
		echo "To try and register again go to <A href='register.php5'>Register Page</A><BR />";
		exit();	
	}

// Add the user to the SMF forum database
	smf_register_new_member($player_name, $new_password, $new_password, $email);
	
	$player->set_initial_values($player_name, $new_password, $location, $email, $help);
//	$player->debug_display();

	$player->smf_id = smf_get_id_of_player($player_name);
	
	$player->db_insert();




	$_SESSION['player_name'] = $player_name;

	$nm = new NewsModel();
	$nm->add_new_news('judal', 'player', 'alliance', "New Player Created - $player_name", "eom");
	
	echo "Your character '$player_name of $location' has been created <br />";
	echo "Please go to <A href='main_page.php5?view=overview'> Revolution </A> to begin<br />";
?>

