<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('game_model.php5'); 
	require_once('news_model.php5'); 
	require_once('cipher.php5'); 

class PlayerData {
	var $name;
	var $password;
	var $location;
	var $unassigned;
	var $extractor;
	var $genetic_lab;
	var $powerplant;
	var $factory;
	var $crystal;
	var $mineral;
	var $organic;
	var $energy;
	var $mana;
	var $email;
	var $galaxy;
	var $star;
	var $planet;
	var $continent;
	var $admin;
	var $evolution;
	var $last_online;
	var $score;
	var $status;
	var $smf_id;
	
	function db_insert() {
		if ($this->determine_invalid_string($this->name)) {
 			echo "Could not create new player, you used an invalid character.  Go back and try again";
			exit();
		}

		if ($this->determine_invalid_string($this->location)) {
 			echo "Could not create new player, you used an invalid character.  Go back and try again";
			exit();
		}
		
	  $conn = db_connect();
	  $query = "insert into player values (
	  	'$this->name', 
			'$this->location',
			'$this->password',
			'$this->email',
			$this->unassigned,
			$this->extractor,
			$this->genetic_lab,
			$this->powerplant,
			$this->factory,
			$this->crystal,
			$this->mineral,
			$this->organic,
			$this->energy,
			$this->mana,
			$this->galaxy,
			$this->star,
			$this->planet,
			$this->continent,
			NOW(),
			'$this->admin',
			'$this->help',
			'$this->score',
			'active',
			'$this->smf_id'
			)";
 		$result = $conn->query($query);
 		if (!$result) {
 			echo "Could not create new player, please try again: $query";
			$nm = new NewsModel();
			$nm->add_new_news('judal', 'player', 'high', "Could not create player: $this->name", 
			"Query was: <BR /> \n $query" );


 			exit();
 		}
// 		echo "Query: $query<br />\n";
	}
	
	function db_fill($name) {
	  $conn = db_connect();
		$query = "select * from player where name='$name'";	
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$this->name = $row->name;
		$this->location = $row->location;
		$this->password = $row->password;
		$this->email = $row->email;
		$this->unassigned = $row->unassigned;
		$this->extractor = $row->extractor;
		$this->genetic_lab = $row->genetic_lab;
		$this->powerplant = $row->powerplant;
		$this->factory = $row->factory;
		$this->crystal = $row->crystal;
		$this->mineral = $row->mineral;
		$this->organic = $row->organic;
		$this->energy = $row->energy;	
		$this->mana = $row->mana;	
		$this->galaxy = $row->galaxy;	
		$this->star = $row->star;	
		$this->planet = $row->planet;	
		$this->continent = $row->continent;
		$this->last_online = $row->last_online;
		$this->admin = $row->admin;
		$this->help = $row->help;
		$this->score = $row->score;
		$this->status = $row->status;
		$this->smf_id = $row->smf_id;
	}
	
	function debug_display () {
		
		echo "<TABLE class='DEBUG'>\n";		
		echo "<TR><TH class='DEBUG' colspan='2'>Player Information</TH><TR>\n";
		echo "<TR><TD class='DEBUG'>Name:</TD><TD class='DEBUG'>$this->name</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Password:</TD><TD class='DEBUG'> [hidden]</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Location:</TD><TD class='DEBUG'> $this->location</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Email:</TD><TD class='DEBUG'> $this->email</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Unassigned:</TD><TD class='DEBUG'> $this->unassigned</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Extractor:</TD><TD class='DEBUG'> $this->extractor</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Genetic Lab:</TD><TD class='DEBUG'> $this->genetic_lab</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Powerplant:</TD><TD class='DEBUG'> $this->powerplant</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Factory:</TD><TD class='DEBUG'> $this->factory</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Crystal:</TD><TD class='DEBUG'> $this->crystal</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Mineral:</TD><TD class='DEBUG'> $this->mineral</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Organic:</TD><TD class='DEBUG'> $this->organic</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Energy:</TD><TD class='DEBUG'> $this->energy</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Mana:</TD><TD class='DEBUG'> $this->mana</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Score:</TD><TD class='DEBUG'> $this->score</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Galaxy:</TD><TD class='DEBUG'> $this->galaxy</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Star:</TD><TD class='DEBUG'> $this->star</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Planet:</TD><TD class='DEBUG'> $this->planet</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Continent:</TD><TD class='DEBUG'> $this->continent</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Last Online:</TD><TD class='DEBUG'> $this->last_online</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Admin:</TD><TD class='DEBUG'> $this->admin</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Help Level:</TD><TD class='DEBUG'> $this->help</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Score:</TD><TD class='DEBUG'> $this->score</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>Status:</TD><TD class='DEBUG'> $this->status</TD><TR>\n";
		echo "<TR><TD class='DEBUG'>SMF_id:</TD><TD class='DEBUG'> $this->smf_id</TD><TR>\n";
		echo "</TABLE>\n";		
	}
	
	function set_initial_values($name, $password, $location, $email, $help) {
		$gm = new GameModel();
		$this->name = $name;
		$this->password = $password;
		$this->location = $location;
		$this->email = $email;
		
		$this->unassigned=$gm->get_game_parameter("starting_structures");
		$this->extractor=0;
		$this->genetic_lab=0;
		$this->powerplant=0;
		$this->factory=0;
		$this->crystal=0;
		$this->mineral=$gm->get_game_parameter("starting_mineral");
		$this->organic=$gm->get_game_parameter("starting_organic");
		$this->energy=$gm->get_game_parameter("starting_energy");
		$this->mana=0;
		
		// Set initial values for location
		$this->set_initial_location(0);

		if (strcmp($name, "judal") == 0) $this->admin = "superadmin";
		else $this->admin = "player";
		
		$this->help=$help;
		$this->score=0;
		$this->status='active';

	}
	
	function set_initial_location($count) {
		if ($count > 4) {
			echo "Unable to find a planet for you, please send a note to an admin to fix this problem";
			return;
		}

		srand(time());
		$this->galaxy = 1;
		
		$this->star = (rand()%29)+1;
		$this->planet = (rand()%9)+1;
		$this->set_first_available_continent();
		
		// If the continent is > 9, then the planet is full, randomly find another
		if ($this->continent > 9) set_initial_location($count+1);		
		
	}
	
	function set_first_available_continent() {
		$conn = db_connect();
		$query = "select max(continent) as max_continent from player where 
			galaxy=$this->galaxy and star=$this->star and planet=$this->planet";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		if ($row->max_continent == NULL) $this->continent = 1;
		else $this->continent = $row->max_continent + 1;
	}

	function login_player() {
		$start_time = time();
		$player_name = $_POST['player_name'];
		$password = $_POST['password'];
	
		$conn = db_connect();
		$query = "select * from player where name='$player_name' and password = '$password'";
		$query_time = time() - $start_time;

		$result = $conn->query($query);
	  if (!$result || $result->num_rows==0) {
//			echo "The player does not exist or the password does not match. <BR /> ";
//			echo "Please try again here: <A href='login.php5'> Login </A> <BR />";
//			echo "or register here: <A href='register.php5'> Register </A>";
			return false;
		} else {
			$row = $result->fetch_object();
			$fetch_time = time() - $start_time;
		
			$_SESSION['player_name'] = $row->name;
			$c_time = time() - $start_time;
			
//			$cipher = new Cipher('BOOBOO');

//			$encryptedtext = $cipher->encrypt("$player_name:$password");
			$encryptedtext = "$player_name:$password";

			
			setcookie("auth", "$encryptedtext", time()+3600*24*180);
			$enc_time = time() - $start_time;
			echo "(Q: $query_time F: $fetch_time) (C: $c_time, E: $enc_time)";
			return true;
		}
	}		
	
	function check_player($player_name, $password) {
		$conn = db_connect();
		$query = "select * from player where name='$player_name' and password = '$password'";
		$result = $conn->query($query);
	  if (!$result || $result->num_rows==0) return false;
	  else return true;
	}
	
	function check_available_player_name($player_name) {
		$conn = db_connect();
		$query = "select * from player where name='$player_name'";
		$result = $conn->query($query);
	  if ($result->num_rows==0) return true;
	  else return false;
	}
	
	function subtract($type, $amount) {
		$conn = db_connect();

		// Full Anti cheat test
		if ($amount < 0) {
			// First find out how much they have right now
			$query = "select mineral, organic, energy from player where name='$this->name'";
			$result = $conn->query($query);
			$row = $result->fetch_object();		
			
			
			$nm = new NewsModel();
			$nm->add_new_news('judal', 'player', 'misc', "$this->name tried to cheat", "Player tried to get $amount of $type.  " .
				"Player lost: {$row->mineral}m/{$row->organic}o/{$row->energy}e");
			$nm->add_new_news("$this->name", "player", "misc", "You just bought a negative amount of a resource", "Judal has been notified, please explain to him.  " .
				"Penalty is : {$row->mineral}m/{$row->organic}o/{$row->energy}e.  If you think this is in error, please contact Judal" );

//			display_error("You just bought a negative amount of a resource, Judal has been notified, please explain to him.  Further, all your resources have been removed");
			$update_query = "update player set mineral=0, organic=0, energy=0 where name='$this->name'";
			$result = $conn->query($update_query);
			$this->db_fill($this->name);
			return;
		}
		
		$query = "select $type as amount from player where name='$this->name'";
		$result = $conn->query($query);
		$row = $result->fetch_object();		
		$old_amount = $row->amount;
		$new_amount = $old_amount - $amount;
		
		$update_query = "update player set $type=$new_amount where name='$this->name'";
		$result = $conn->query($update_query);
		$this->db_fill($this->name);
	}
	
	function promote($player_name, $admin_type) {
		$conn = db_connect();
		$query = "select * from player where name='$player_name'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return false;

		$update_query = "update player set admin='$admin_type' where name='$player_name'";
		$result = $conn->query($update_query);
		if (!$result) return false;
		else return true;		
	}
	
	function touch($player_name) {
		$conn = db_connect();
		$query = "update player set last_online=NOW() where name='$player_name'";
		$result = $conn->query($query);	
		$query = "update player set status='active' where name='$player_name' and status='inactive'";
		$result = $conn->query($query);	
	}
	
	function add_structures_to_player($player_name, $unassigned, $extractors, $genetic_labs, $powerplants, $factories) {
		if ($unassigned == 0 && $extractors == 0 && $genetic_labs == 0 && $powerplants == 0 && $factories == 0) return; // Nothing to add
		$conn = db_connect();	
		$query = "update player set
		  unassigned = unassigned + $unassigned,
		  extractor = extractor + $extractors,
		  genetic_lab = genetic_lab + $genetic_labs,
		  powerplant = powerplant + $powerplants,
		  factory = factory + $factories
		where name='$player_name'
		";
	  $result = $conn->query($query);
		
	}

	function subtract_structures_from_player($player_name, $unassigned, $extractors, $genetic_labs, $powerplants, $factories) {
		if ($unassigned == 0 && $extractors == 0 && $genetic_labs == 0 && $powerplants == 0 && $factories == 0) return; // Nothing to subtract
		$conn = db_connect();	
		$query = "update player set
		  unassigned = unassigned - $unassigned,
		  extractor = extractor - $extractors,
		  genetic_lab = genetic_lab - $genetic_labs,
		  powerplant = powerplant - $powerplants,
		  factory = factory - $factories
		where name='$player_name'
		";
	  $result = $conn->query($query);
		
	}
	
	function compute_score_for_player($player, $creature_values, $scan_values, $item_values) {
		$conn = db_connect();	
		$query = "select * from player where name='$player'";
	  $result = $conn->query($query);
		$row = $result->fetch_object();
		
		$score = 1000 * ($row->extractor + $row->genetic_lab + $row->powerplant + $row->factory);
		$score += ceil( ($row->mineral + $row->organic + $row->energy) * 0.05 );
		

		$query = "select * from player_creatures where player_name='$player'";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$score += $creature_values["$row->creature"] * $row->number;
		}	  

		$query = "select * from player_scans where player_name='$player'";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$score += $scan_values["$row->scan_type"] * $row->number;
		}	  

		$query = "select * from player_items where player_name='$player'";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$score += $item_values["$row->item_type"] * $row->number;
		}	  


		$query = "update player set score=$score where name='$player'";
	  $result = $conn->query($query);
		
	}
	
	function get_player_name_from_location($galaxy, $star, $planet, $continent) {
		$conn = db_connect();	
		$query = "select name from player
			where galaxy=$galaxy 
			  and star=$star
			  and planet=$planet
			  and continent=$continent";
		$result = $conn->query($query);
		if (!$result || $result->num_rows == 0) {
//			show_error("Invalid Target");
			return false;
		}
		$row = $result->fetch_object();
		return $row->name;
	}

	function get_location_from_player_name($player_name) {
		$conn = db_connect();	
		$query = "select galaxy, star, planet, continent from player where name='$player_name'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$ret = array($row->galaxy, $row->star, $row->planet, $row->continent);
		return $ret;
	}
	
	function is_admin($player_name) {
		$conn = db_connect();	
		$query = "select admin from player where name='$player_name'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		if ( strcmp($row->admin, "admin") == 0 ) return true;
		if ( strcmp($row->admin, "superadmin") == 0 ) return true;
		
		return false;
		
	}
	
	function get_players_on_planet($galaxy, $star, $planet) {
		$conn = db_connect();	
		$query = "select name from player
			where galaxy=$galaxy 
			  and star=$star
			  and planet=$planet
			  ";
		$result = $conn->query($query);
			  
		$player_list = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$player_list[$count] = $row->name;
		}	  

		return $player_list;
		
	}
	
	function determine_invalid_string($string) {
		if (strlen($string) < 3) return false;
		return strpbrk($string, "&*()\"\'<>\\");
	}
	
	function is_player_active($player_name) {
	  $conn = db_connect();
		$query = "select * from player where name='$player_name'";	
		$result = $conn->query($query);
		$row = $result->fetch_object();
		if ($row->status == 'active') return true;
		else return false;		
	}

	function is_player_holiday($player_name) {
	  $conn = db_connect();
		$query = "select * from player where name='$player_name'";	
		$result = $conn->query($query);
		$row = $result->fetch_object();
		if ($row->status != 'active' && $row->status != 'inactive' ) return true;
		else return false;		
	}

	function put_player_on_vacation($player_name, $current_tick) {
	  $conn = db_connect();
		$query = "update player set status='$current_tick' where name='$player_name'";	
		$result = $conn->query($query);
	}

	function reactivate_player($player_name) {
	  $conn = db_connect();
		$query = "update player set status='active' where name='$player_name'";	
		$result = $conn->query($query);
	}
	
	function get_all_player_names() {
		$conn = db_connect();	
		$query = "select name from player";
		$result = $conn->query($query);
			  
		$player_list = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$player_list[$count] = $row->name;
		}	  
		return $player_list;
	}

	function get_all_active_player_names() {
		$conn = db_connect();	
		$query = "select name from player where status='active' order by name";
		$result = $conn->query($query);
			  
		$player_list = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$player_list[$count] = $row->name;
		}	  
		return $player_list;
	}

	function record_login($player_name, $password, $status, $ip) {
		$conn = db_connect();	
		$query = "insert into login_history values ('$player_name', '$password', $status, '$ip', NOW() ) ";
		$result = $conn->query($query);
	}

	function get_pm_link($smf_id) {
		return "<A href='/revo_smf/index.php?action=pm;sa=send;f=inbox;u={$smf_id}' target='_pm'><IMG src='images/pm.gif' border='0'></IMG></A>";
	}
	
	function update_email($player_name, $new_email) {
		$conn = db_connect();	
		$query = "update player set email='$new_email' where name='$player_name' ";
		$result = $conn->query($query);
	}
}