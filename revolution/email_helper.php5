<?php 
	require_once('db_fns.php5'); 
	require_once('email_preferences_data.php5'); 

class EmailHelper {

	function send_launch_email($player_name, $launcher_name, $target_name, $fleet, $mission, $launch_tick, $arrival_tick, $depart_tick, $return_tick) {
		$epd = new EmailPreferencesData();
		$should_email = $epd->get_email_preference($player_name, 'email_on_launch');
		if (! $should_email) return;  // No use sending anything if they do not want an email
		
		$to = $this->get_email_address($player_name);
		$subject = "REVO: $launcher_name has launched $fleet to $target_name for $mission, arrival is $arrival_tick";
		$message = "Launcher: $launcher_name\n"
			. "Target: $target_name\n"
			. "Fleet: $fleet\n"
			. "Mission: $mission\n"
			. "Launch Tick: $launch_tick\n"
			. "Arrival Tick: $arrival_tick\n"
			. "Depart Tick: $depart_tick\n"
			. "Return Tick: $return_tick\n\n";
			
		$this->send_email($to, $subject, $message);
	}

	function send_recall_email($player_name, $launcher_name, $target_name, $fleet) {
		$epd = new EmailPreferencesData();
		$should_email = $epd->get_email_preference($player_name, 'email_on_recall');
		if (! $should_email) return;  // No use sending anything if they do not want an email
		
		$to = $this->get_email_address($player_name);
		$subject = "REVO: $launcher_name has recalled $fleet from $target_name";
		$message = "Launcher: $launcher_name\n"
			. "Target: $target_name\n"
			. "Fleet: $fleet\n\n";
			
		$this->send_email($to, $subject, $message);
	}

	function send_complete_email($player_name, $completed_build, $preference, $number, $tick) {
		$epd = new EmailPreferencesData();
		$should_email = $epd->get_email_preference($player_name, $preference);
		if (! $should_email) return;  // No use sending anything if they do not want an email
		
		$to = $this->get_email_address($player_name);
		$subject = "REVO: You ($player_name) have completed $completed_build";
		if (number == 0) {
			$message = "Build: $completed_build\n"
			. "Tick: $tick\n";
		} else {
			$message = "Build: $completed_build\n"
			. "Number: $number\n"
			. "Tick: $tick\n";
			
		}
			
		$this->send_email($to, $subject, $message);
	}





	function send_battle_email($player, $target, 
					$att_att, $att_def, $att_foc, $att_int, $att_dis,
					$def_att, $def_def, $def_foc, $def_int, $def_dis,
					$att_captured, $att_damage, $def_captured, $def_damage,
					$structures_captured, $unassigned, $extractors, $genetic_labs, $powerplants, $factories,
					$forts_destroyed) {
		$epd = new EmailPreferencesData();
		$should_email = $epd->get_email_preference($player, 'email_on_battle');
		if (! $should_email) return;  // No use sending anything if they do not want an email
		$to = $this->get_email_address($player);
		$subject = "REVO: You ($player) are involved in a battle at {$target}'s continent";
		$message = "Total Attacker Stats: {$att_att}A/{$att_def}D/{$att_foc}F/{$att_int}i/{$att_dis}d\n"
						.  "Total Defender Stats: {$def_att}A/{$def_def}D/{$def_foc}F/{$def_int}i/{$def_dis}d\n"
						.  "Attacker Damage: {$att_captured}% captured, {$att_damage}% destroyed\n"
						.  "Defender Damage: {$def_captured}% captured, {$def_damage}% destroyed, {$forts_destroyed} forts destroyed\n"
						.  "Total Captured: $structures_captured - {$unassigned}u {$extractors}e {$genetic_labs}g {$powerplants}p {$factories}f\n\n";
		$this->send_email($to, $subject, $message);		
	}

	function get_email_address($player_name) {
	  $conn = db_connect();
		$query = "select email from player where name='$player_name'";	
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->email;		
	}

	function send_email($to, $subject, $message) {
		$headers = "From: GameMaster@RevolutionOfTheGalaxy.com\r\nReply-To: gamemaster@revolutionofthegalaxy.com\r\nCC: archive@revolutionofthegalaxy.com\r\n";
		$message .= "\nto goto the mobile site click on: http://revolutionofthegalaxy.com/revolution/blackberry.php5\n\n";
		$mail_sent = @mail( $to, $subject, $message, $headers );
		// Eat the result whether or not it actually sends it.
	}
}

?>