<?php
	require_once('misc_fns.php5'); 
	require_once('player_data.php5'); 
	$st = time();
	session_start();
	$sess_time = time() - $st;
  //create short variable names
  $player_name=$_POST['player_name'];
  $password=$_POST['password'];

	
	if (strlen($player_name) == 0) {
     echo "You entered invalid information.  Please try again<br />";
     do_html_url("login.php5", "Login");
     exit();
	}

	$rst_time = time() - $st;
	
	$player = new PlayerData();
	$pd_time = time() - $st;
	$player->db_fill($player_name);
	$pf_time = time() - $st;
	$status = $player->login_player();	
	$lp_time = time() - $st;

	$ip = $_SERVER['REMOTE_ADDR'];
	$player->record_login($player_name, $password, $status, $ip);
	$rl_time = time() - $st;

	echo "<link rel='Stylesheet' href='revolution.css' title='MapStyle' type='text/css'/>\n";
	
	if ($status) {
			echo "Your are now logged in as $player->name of $player->location. <br />";	
			echo "For main access, please go to <A href='main_page.php5?view=overview'> Revolution of the Galaxy </A> to begin<br />";
			echo "For mobile access, please go to <A href='blackberry.php5'> Revo Mobile </A> to begin<br />";
	} else {
    echo "You entered invalid information.  Please try again<br />";
     do_html_url("login.php5", "Login");
	}	
	echo "(Times: S: $sess_time, RST: $rst_time, PD: $pd_time, PF: $pf_time, LP: $lp_time, RL: $rl_time)";
?>

