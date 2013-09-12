<?php
	require_once('admin_controller.php5'); 
	require_once('research_controller.php5'); 
	require_once('development_controller.php5'); 
	require_once('creatures_controller.php5'); 
	require_once('structures_controller.php5'); 
	require_once('conversations_controller.php5'); 
	require_once('fleets_controller.php5'); 
	require_once('move_controller.php5'); 
	require_once('scans_controller.php5'); 
	require_once('alliance_controller.php5'); 
	require_once('forts_controller.php5'); 
	require_once('bombs_controller.php5'); 
	require_once('pulses_controller.php5'); 
	require_once('help_controller.php5'); 
	require_once('chat_controller.php5'); 
	require_once('vacation_controller.php5'); 
	require_once('profile_controller.php5'); 

class PageController {
	function perform_action() {
  	$action=$_REQUEST['action'];
  	if ( strlen($action) == 0) return; // No action to take
  	
		$ac = new AdminController();
		$ac->check_actions();
		
		if (strcmp ( $action, 'research') == 0) {
			$rc = new ResearchController();
			$rc->start_research();
		} else if (strcmp ( $action, 'develop') == 0) {
			$dc = new DevelopmentController();
			$dc->start_development();
		} else if (strcmp ( $action, 'create_creatures') == 0) {
			$cc = new CreaturesController();
			$cc->create_creatures();
		} else if (strcmp ( $action, 'allocate') == 0) {
			$sc = new StructuresController();
			$sc->allocate_new_structure();
		} else if (strcmp($action, "move_fleets") == 0) {
			$fc = new FleetsController();
			$fc->move_fleets();
		} else if (strcmp($action, "launch_fleet") == 0) {
				$fc = new FleetsController();
			$fc->launch_fleet();
		} else if (strcmp($action, "set_invite_key") == 0) {
			$mc = new MoveController();
			$mc->set_invite_key();
		} else if (strcmp($action, "move_player") == 0) {
			$mc = new MoveController();
			$mc->move_player();
		} else if (strcmp($action, "create_scans") == 0) {
			$sc = new ScansController();
			$sc->create_scans();
		} else if (strcmp($action, "site_scan") == 0) {
			$sc = new ScansController();
			$sc->use_site_scan();
		} else if (strcmp($action, "use_scan") == 0) {
			$sc = new ScansController();
			$sc->use_scan();
		} else if (strcmp($action, "scan_history") == 0) {
			$sc = new ScansController();
			$sc->show_scan_history();
		} else if (strcmp($action, "create_alliance") == 0) {
			$ac = new AllianceController();
			$ac->create_alliance();
		} else if (strcmp($action, "apply_to_alliance") == 0) {
			$ac = new AllianceController();
			$ac->apply_to_alliance();
		} else if (strcmp($action, "withdraw_application") == 0) {
			$ac = new AllianceController();
			$ac->withdraw_application();
		} else if (strcmp($action, "reject_applicant") == 0) {
			$ac = new AllianceController();
			$ac->reject_application();
		} else if (strcmp($action, "accept_applicant") == 0) {
			$ac = new AllianceController();
			$ac->accept_application();
		} else if (strcmp($action, "promote_to_senior") == 0) {
			$ac = new AllianceController();
			$ac->promote_to_senior();
		} else if (strcmp($action, "demote_to_member") == 0) {
			$ac = new AllianceController();
			$ac->demote_to_member();
		} else if (strcmp($action, "promote_to_leader") == 0) {
			require_once('alliance_controller.php5'); 
			$ac = new AllianceController();
			$ac->promote_to_leader();
		} else if (strcmp($action, "kick_member") == 0) {
			$ac = new AllianceController();
			$ac->kick_member();
		} else if (strcmp($action, "leave_alliance") == 0) {
			$ac = new AllianceController();
			$ac->leave_alliance();
		} else if (strcmp($action, "disband_alliance") == 0) {
			$ac = new AllianceController();
			$ac->disband_alliance();
		} else if (strcmp($action, "edit_alliance_description") == 0) {
			$ac = new AllianceController();
			$ac->edit_alliance_description();
		} else if (strcmp($action, "create_declaration") == 0) {
			$ac = new AllianceController();
			$ac->create_declaration();
		} else if (strcmp($action, "remove_declaration") == 0) {
			$ac = new AllianceController();
			$ac->remove_declaration();
		} else if (strcmp($action, "create_forts") == 0) {
			$fc = new FortsController();
			$fc->create_forts();
		} else if (strcmp($action, "create_bombs") == 0) {
			$bc = new BombsController();
			$bc->create_bombs();
		} else if (strcmp($action, "create_pulses") == 0) {
			$pc = new PulsesController();
			$pc->create_pulses();
		} else if (strcmp($action, "move_bombs") == 0) {
			$fc = new FleetsController();
			$fc->move_bombs();
		} else if (strcmp($action, "change_trap_status") == 0) {
			$bc = new BombsController();
			$bc->change_trap_status();
		} else if (strcmp($action, "fire_pulse") == 0) {
			$pc = new PulsesController();
			$pc->fire_pulse();
		} else if (strcmp($action, "recall_fleet") == 0) {
			$fc = new FleetsController();
			$fc->recall_fleet();
		} else if (strcmp($action, "cancel_development") == 0) {
			$dc = new DevelopmentController();
			$dc->cancel_development();
		} else if (strcmp($action, "remove_development") == 0) {
			$dc = new DevelopmentController();
			$dc->remove_development();
		} else if (strcmp($action, "new_concept") == 0) {
			$hc = new HelpController();
			$hc->create_new_concept();
		} else if (strcmp($action, "create_new_chat") == 0) {
			$cc = new ChatController();
			$cc->create_new_chat();
		} else if (strcmp($action, "go_on_vacation") == 0) {
			$vc = new VacationController();
			$vc->go_on_vacation();
		} else if (strcmp($action, "reactivate_player") == 0) {
			$vc = new VacationController();
			$vc->reactivate_player();
		} else if (strcmp($action, "create_new_conversation_category") == 0) {
			$cc = new ConversationsController();
			$cc->create_new_conversation_category();
		} else if (strcmp($action, "create_new_conversation_topic") == 0) {
			$cc = new ConversationsController();
			$cc->create_new_conversation_topic();
		} else if (strcmp($action, "create_new_personal_shout") == 0) {
			$cc = new ChatController();
			$cc->create_new_personal_shout();
		} else if (strcmp($action, "clear_shout") == 0) {
			$cc = new ChatController();
			$cc->clear_shout();
		} else if (strcmp($action, "modify_email") == 0) {
			$pc = new ProfileController();
			$pc->modify_email();
		} else if (strcmp($action, "update_email_preferences") == 0) {
			$pc = new ProfileController();
			$pc->update_email_preferences();
		} 
	}

}
?>