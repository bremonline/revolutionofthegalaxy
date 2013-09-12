<?php
	require_once('research_model.php5'); 
	require_once('player_data.php5'); 

class ResearchController {

	function start_research() {
  	$player_name=$_SESSION['player_name'];
  	$research_item=$_REQUEST['research_item'];

		$pd = new PlayerData();
		$pd->db_fill($player_name);
		
		$rm = new ResearchModel();
		$mineral_cost = $rm->get_cost("mineral", $research_item);
		$organic_cost = $rm->get_cost("organic", $research_item);

		if ($mineral_cost == 0) {
			show_error("Invalid Research Item: $research_item");
			return;
		}
		
		if ($mineral_cost > $pd->mineral) {
			show_error("Not enough mineral to start research");
			return;
		}

		if ($organic_cost > $pd->organic) {
			show_error("Not enough organic to start research");
			return;
		}
		
		if ($rm->get_currently_researching($player_name) != "") {
			show_error("Already Researching something else.");
			return;
		}
	
		if (! $rm->is_researchable($player_name, $research_item)) {
			show_error("Cannot Research This Technology Yet.");
			return;
		}

		if ($rm->is_researched($player_name, $research_item)) {
			show_error("Technology already researched.");
			return;
		}
		
		$pd->subtract("mineral", $mineral_cost);
		$pd->subtract("organic", $organic_cost);
		$rm->add_new_research($player_name, $research_item);
		
		show_info("Research started on $research_item");
		
	}
}
?>