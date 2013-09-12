<?php

class HelpController {
	function create_new_concept() {
 		$player_name=$_SESSION["player_name"];
		$concept = $_REQUEST["concept"];

		$hm = new HelpModel();
		$hm->insert_new_description($player_name, $concept, "concept");
	}
	
	function create_new_question() {
 		$player_name=$_SESSION["player_name"];
		$question = $_REQUEST["question"];
		$hm = new HelpModel();
		$hm->insert_new_description($player_name, $concept, "concept");
		
		
	}
}

?>