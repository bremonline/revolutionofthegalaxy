<?php
	require_once('view_fns.php5'); 
	require_once('development_model.php5'); 

class DevelopmentController {

	function start_development() {
  	$player_name=$_SESSION['player_name'];
  	$development_item=$_REQUEST['development_item'];

		$dm = new DevelopmentModel();
		$proficiency = $dm->get_development_proficiency($development_item);
		$type = $dm->get_development_type($development_item);
		
		// If currently developing something, do not let develop again
		if ($dm->get_currently_developing($player_name) != false) {
			show_error("You cannot develop more than one thing at once.");
			return;
		}
		
		if ($development_item == "") {
			show_error("Invalid Development");
			return;	
		}
		
		if (!$dm->is_developable($development_item) ) {
			show_error("Cannot develop this technology.");
			return;				
		}

		if ($dm->does_player_know_development($player_name, $development_item) ) {
			show_error("Already developed the technology.");
			return;				
		}
		
		$current_developments = $dm->get_current_developments($player_name, $type, $proficiency);
		if ( (strcmp($proficiency,'basic') == 0) and $current_developments >= 8) {
			show_error("You cannot have more than 8 basic developments of any type.");
			return;
		}
		if ( (strcmp($proficiency,'expert') == 0) and $current_developments >= 2) {
			show_error("You cannot have more than 2 expert developments of any type.");
			return;
		}
		if ( (strcmp($proficiency,'master') == 0) and $current_developments >= 2) {
			show_error("You cannot have more than 1 master development and one victory condition of any type.");
			return;
		}

		$dm->add_new_development($player_name, $development_item);
		
		show_info("Development started on $development_item") ;
	}
	
	function cancel_development() {
  	$player_name=$_SESSION['player_name'];
  	$development_item=$_REQUEST['development_item'];
		$dm = new DevelopmentModel();

		if (strcmp($dm->get_currently_developing($player_name), $development_item) != 0) {
			show_error("Cannot Cancel.  Technology is not currenty being developed.");
			return;			
		}	

		$dm->cancel_development($player_name, $development_item);		
	}	

	function remove_development() {
  	$player_name=$_SESSION['player_name'];
  	$development_item=$_REQUEST['development_item'];
		$dm = new DevelopmentModel();

		if (! $dm->does_player_know_development($player_name, $development_item) )  {
			show_error("Cannot Remove.  Technology is not known.");
			return;			
		}	

		$dm->remove_development($player_name, $development_item);		
	}	

}
?>