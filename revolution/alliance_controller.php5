<?php
	require_once("alliance_model.php5");
	require_once("news_model.php5");
	require_once("forum/smf_bridge.php5");

class AllianceController {

	function create_alliance() {
 		$player_name=$_SESSION["player_name"];
		$alliance_name = $_REQUEST["alliance_name"];
		$shorthand = $_REQUEST["shorthand"];
		$description = $_REQUEST["description"];

		$am = new AllianceModel();
		
		$status = $am->create_new_alliance($player_name, $alliance_name, $shorthand, $description);
		if ($status) {
			$subject = "You have created a new alliance";
			$text = "You have created a new alliance named $alliance_name.  Its tag is: $shorthand";
			$nm = new NewsModel();
			$nm->add_new_news($player_name, 'player', 'alliance', $subject, $text);
		}
		
		// Create the alliance forum and membergroup
		smf_create_alliance_board($player_name, $alliance_name);
	}

	function apply_to_alliance() {
 		$player_name=$_SESSION["player_name"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();

		if (!is_null($am->get_alliance_of_player($player_name)) ) {
			show_error("You can't apply to an alliance if you are already in an alliance");
			return;
		}

		if ($am->check_for_application($player_name) ){
			show_error("You can't apply to more than one alliance at a time");
			return;
		}

		$alliance_leader = $am->get_alliance_leader($alliance);
		
		$am->add_application($player_name, $alliance);
		$subject = "$player_name is applying to your alliance";
		$text = "$player_name is applying to your alliance.  Please go to the alliance page and accept or reject the application";
		$nm = new NewsModel();
		$nm->add_new_news($alliance_leader, 'player', 'alliance', $subject, $text);
		
		show_info("You have applied to $alliance");
	}

	function withdraw_application() {
 		$player_name=$_SESSION["player_name"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();

		$am->withdraw_application($player_name);
		
		$alliance_leader = $am->get_alliance_leader($alliance);
		$subject = "$player_name withdrew the application to your alliance";
		$text = "$player_name withdrew the application to your alliance.  No further action is necessary";
		$nm = new NewsModel();
		$nm->add_new_news($alliance_leader, 'player', 'alliance', $subject, $text);
		
		show_info("You have withdrawn your application to $alliance");
	}

	function reject_application() {
 		$player_name=$_SESSION["player_name"];
		$applicant = $_REQUEST["applicant"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();

		$am->withdraw_application($applicant);

		$subject = "Application Rejected";
		$text = "Your application to $alliance has been rejected";
		$nm = new NewsModel();
		$nm->add_new_news($applicant, 'player', 'alliance', $subject, $text);
		
		show_info("You have rejected $applicant");
	}

	function accept_application() {
 		$player_name=$_SESSION["player_name"];
		$applicant = $_REQUEST["applicant"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();

		$am->withdraw_application($applicant);
		$am->accept_applicant($applicant, $alliance);

		$subject = "Application Accepted";
		$text = "Your application to $alliance has been Accepted";
		$nm = new NewsModel();
		$nm->add_new_news($applicant, 'player', 'alliance', $subject, $text);

		// Add player to the forum membergroup
		smf_add_player_to_group($applicant, $alliance);
		
		
		show_info("You have accepted $applicant");
	}

	function promote_to_senior() {
 		$player_name=$_SESSION["player_name"];
		$member = $_REQUEST["member"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();

		$am->promote($member, "Senior");

		$subject = "You have been promoted";
		$text = "You have been promoted to Senior in $alliance";
		$nm = new NewsModel();
		$nm->add_new_news($member, 'player', 'alliance', $subject, $text);


		show_info("You have promoted $member to Senior");
	}

	function demote_to_member() {
 		$player_name=$_SESSION["player_name"];
		$member = $_REQUEST["member"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();

		$am->promote($member, "Member");

		$subject = "You have been demoted";
		$text = "You have been demoted back to a Member of $alliance";
		$nm = new NewsModel();
		$nm->add_new_news($member, 'player', 'alliance', $subject, $text);


		show_info("You have demoted $member back to a Member");
	}

	function kick_member() {
 		$player_name=$_SESSION["player_name"];
		$member = $_REQUEST["member"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();

		$am->kick_member($member);
		
		$subject = "You have been kicked";
		$text = "You have been kicked out of $alliance";
		$nm = new NewsModel();
		$nm->add_new_news($member, 'player', 'alliance', $subject, $text);
		
		// Remove member from membergroup
		smf_delete_player_from_group($member, $alliance);
	
		show_info("You have kicked $member out of your alliance");
	}

	function leave_alliance() {
 		$player_name=$_SESSION["player_name"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();
		$alliance_leader = $am->get_alliance_leader($alliance);

		if (strcmp($player_name, $alliance_leader) == 0) {
			show_error("You cannot leave your own alliance, you must kick all members then disband it");
			return;
		}
		
		$am->kick_member($player_name);
		
		$subject = "You have left your alliance";
		$text = "You have left the alliance: $alliance";
		$nm = new NewsModel();
		$nm->add_new_news($player_name, 'player', 'alliance', $subject, $text);
	
		$subject = "A player has left your alliance";
		$text = "$player_name has left $alliance";
		$nm = new NewsModel();
		$nm->add_new_news($alliance_leader, 'player', 'alliance', $subject, $text);


		// Remove member from membergroup
		smf_delete_player_from_group($player_name, $alliance);
	
		show_info("You have left your alliance");
	}

	function disband_alliance() {
 		$player_name=$_SESSION["player_name"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();
		$alliance_leader = $am->get_alliance_leader($alliance);

		$member_count = $am->get_realtime_member_count($alliance);
		$applicant_count = $am->get_realtime_applicant_count($alliance);

		if ($member_count > 1) {
			show_error("You cannot disband an alliance with any outstanding seniors or members");
			return;
		}
		if ($applicant_count > 0) {
			show_error("You cannot disband an alliance with any outstanding applicants");
			return;
		}
		
		$am->disband_alliance($alliance);
		
		$subject = "You have disbanded your alliance";
		$text = "You have disbanded the alliance: $alliance";
		$nm = new NewsModel();
		$nm->add_new_news($player_name, 'player', 'alliance', $subject, $text);

		// Remove member from membergroup
		smf_delete_player_from_group($player_name, $alliance);
		// Send a note to judal to destory the alliance boards
		$nm->add_new_news('judal', 'player', 'alliance', "$alliance has been destroyed", "by player: $player_name");

			
		show_info("You have disbanded your alliance");
	}

	function edit_alliance_description() {
 		$player_name=$_SESSION["player_name"];
		$description = $_REQUEST["description"];
		$am = new AllianceModel();
		$alliance = $am->get_alliance_of_player($player_name);
		$am->edit_description($alliance, $description);
	}

	function create_declaration() {
 		$player_name=$_SESSION["player_name"];
		$target_alliance = $_REQUEST["target_alliance"];
		$type = $_REQUEST["type"];
		$ticks = $_REQUEST["ticks"];
		$description = $_REQUEST["description"];
		$am = new AllianceModel();
		$alliance = $am->get_alliance_of_player($player_name);
		$am->create_new_declaration($alliance, $target_alliance, $type, $ticks, $description);

		if (strlen($ticks) == 0) $ticks = 0;
		
		if (strcmp($type, "War") == 0) {
			$subject = "$alliance has just declared war on $target_alliance";
			if ($ticks < 1) {
				$text = "$alliance has just declared war on $target_alliance.<BR/><BR/>$description";
			} else {
				$text = "$alliance has just declared war on $target_alliance.  This war is due to end on tick $ticks.<BR/><BR/>$description";
			}
			$nm = new NewsModel();
			$nm->add_new_news($player_name, 'universe', 'alliance', $subject, $text);
		} else if (strcmp($type, "NAP") == 0) {
			$subject = "$alliance has just declared a NAP with $target_alliance";
			if ($ticks < 1) {
				$text = "$alliance has just declared a NAP with $target_alliance.<BR/><BR/>$description";
			} else {
				$text = "$alliance has just declared a NAP with $target_alliance until tick $ticks.<BR/><BR/>$description";
			} 
			$nm = new NewsModel();
			$nm->add_new_news($player_name, 'universe', 'alliance', $subject, $text);
		} else {
			$subject = "$alliance has just declared an allegence with $target_alliance";
			if ($ticks < 1) {
				$text = "$alliance has just declared an allegence with $target_alliance.<BR/><BR/>$description";
			} else {
				$text = "$alliance has just declared an allegence with $target_alliance until tick $ticks. <BR/><BR/>$description";
			} 
			$nm = new NewsModel();
			$nm->add_new_news($player_name, 'universe', 'alliance', $subject, $text);
		}
	}

	function remove_declaration() {
 		$player_name=$_SESSION["player_name"];
		$target_alliance = $_REQUEST["target_alliance"];

		$am = new AllianceModel();
		$alliance = $am->get_alliance_of_player($player_name);
		$am->remove_declaration($alliance, $target_alliance);

		$subject = "$alliance has just removed its declaration on $target_alliance";
		$text = "$alliance has just removed its declaration on $target_alliance";
		$nm = new NewsModel();
		$nm->add_new_news($player_name, 'universe', 'alliance', $subject, $text);
	}
	
	function promote_to_leader() {
 		$player_name=$_SESSION["player_name"];
		$member = $_REQUEST["member"];
		$alliance = $_REQUEST["alliance"];
		$am = new AllianceModel();

		$am->promote($member, "Leader");
		$am->promote($player_name, "Senior");  // Lower the rank of the former leader

		$subject = "You have been promoted to Leader";
		$text = "You have been promoted to Leader of $alliance";
		$nm = new NewsModel();
		$nm->add_new_news($member, 'player', 'alliance', $subject, $text);

		$subject = "You have abdicated";
		$text = "You have abdicated your position as Leader of $alliance.  You are now a Senior.";
		$nm = new NewsModel();
		$nm->add_new_news($player_name, 'player', 'alliance', $subject, $text);

		show_info("You have abdicated your leadership to $member");
	}

}
?>