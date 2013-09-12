<?php
	require_once('view_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('description_panel.php5'); 
	require_once('game_model.php5'); 

class VacationView {
	function display_vacation_view($subview) {
		$player_name = $_SESSION["player_name"];
		$pd = new PlayerData();
		$dp = new DescriptionPanel();
		$vf = new ViewFunctions();
		echo "<TABLE class='STD'> <TR>\n";
		$dp = new DescriptionPanel();
		echo "<TD class='STD' colspan='2'>";
		$dp->show_text_panel_inside("Vacation Mode", "page", "description", ""); 
		echo "</TD>";
		echo "</TR>";
		if ($pd->is_player_active($player_name)) {
			echo "<TR><TD class='STD'>Go on vacation <I> (Note minimum of 36 ticks before you can return)</I></TD>";
			$vf->display_confirmable_button("Go on Vacation", "008000", "800000", "main_page.php5?view=vacation&action=go_on_vacation");
			echo "</TR>";
		} else {
			$gm = new GameModel();
			$current_tick = $gm->get_current_tick();
			$pd->db_fill($player_name);

			echo "<TR><TD class='STD'>Come off Vacation</I></TD>";
			if ($pd->status > $current_tick - 36) {
				$vf->display_inactive_button("Cannot reactivate until tick: " . ($pd->status + 36), "606060");
			} else {
				$vf->display_confirmable_button("Reactivate", "800000", "008000", "main_page.php5?view=vacation&action=reactivate_player");
			}
			echo "</TR>";
		}		
		echo "</TABLE>\n";
	}
}
?>