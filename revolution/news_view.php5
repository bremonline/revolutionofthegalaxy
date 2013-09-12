<?php
	require_once('db_fns.php5');
	require_once('game_model.php5');
	require_once('news_model.php5');
	require_once('news_data.php5');

class NewsView {
	function display_news_view($subview) {
		
		$player_name = $_SESSION['player_name'];
		$type = $_REQUEST["type"];
		$category = $_REQUEST["category"];
		$id = $_REQUEST["id"];
		
		if ($subview == '') $subview = 'list';
		if ($category == '') $category = 'player';
		if ($type == '') $type = 'all';
		
		$this->display_all_news_bar($player_name, $category, $type);
		echo "<BR />";

		if ($subview=='list') $this->display_news_list($player_name, $category, $type);
		if ($subview=='individual') $this->display_individual_news_item($player_name, $category, $id);
		if ($subview=='all') $this->display_all_news_of_category($player_name, $category, $type);

	}
	
	function old_news() {
	  $conn = db_connect();
		$query = "select * from news where player_name='$player_name' and category='player' 
			order by ID desc limit 0, 20";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			if (strcmp($row->type,"status") == 0) $class='NEWS-GOOD';
			if (strcmp($row->type,"high") == 0) $class='NEWS-BAD';
			
			echo " <TABLE class='NEWS'>\n";
			echo "  <TR>\n";
			echo "   <TH class='$class'>#{$row->ID} </TH>";
			echo "   <TH class='$class'>$row->subject </TH>";
			echo "   <TH class='$class'>$row->time </TH>";
			echo "  </TR>\n";
			echo "  <TR>\n";
			echo "   <TD class='$class' colspan='3' style='text-align:left' >$row->text </TD>";
			echo "  </TR>\n";
			echo " </TABLE>\n";
		}
	}
	
	function display_universe_news_view() {
		$player_name = $_SESSION['player_name'];
		echo "Universe News <BR /><BR />";

		$gm = new GameModel();
		$old_tick = $gm->get_current_tick() - 100;
		
		
	  $conn = db_connect();
		$query = "select * from news where category='universe' and tick>$old_tick order by ID desc";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			if (strcmp($row->type,"status") == 0) $class='NEWS-GOOD';
			if (strcmp($row->type,"high") == 0) $class='NEWS-BAD';
			
			echo " <TABLE class='NEWS'>\n";
			echo "  <TR>\n";
			echo "   <TH class='$class'>$row->subject </TH>";
			echo "   <TH class='$class'>$row->time </TH>";
			echo "  </TR>\n";
			echo "  <TR>\n";
			echo "   <TD class='$class' colspan='2' style='text-align:left' >$row->text </TD>";
			echo "  </TR>\n";
			echo " </TABLE>\n";
		}
	}
	
	function display_news_bar() {
		$player_name = $_SESSION['player_name'];
		$type = $_REQUEST["type"];
		$view = $_REQUEST["view"];
		$subview = $_REQUEST["subview"];
		$category = $_REQUEST["category"];
		$id = $_REQUEST["id"];

		if ($view == 'news' && $subview == 'individual' && $category == 'player' && id != '') {
			$nm = new NewsModel();
			$nm->mark_news_read($player_name, $id); // Before the news list to get the number right
		}
		
		$nm = new NewsModel();
		$p_all_unread = $nm->get_unread_player_news_by_type($player_name, "all");
		$p_launch_unread = $nm->get_unread_player_news_by_type($player_name, "launch");
		$p_battle_unread = $nm->get_unread_player_news_by_type($player_name, "battle");
		$p_items_unread = $nm->get_unread_player_news_by_type($player_name, "items");
		$p_scans_unread = $nm->get_unread_player_news_by_type($player_name, "scans");
		$p_alliance_unread = $nm->get_unread_player_news_by_type($player_name, "alliance");
		$p_misc_unread = $nm->get_unread_player_news_by_type($player_name, "misc");
		
		$vf = new ViewFunctions();
		echo "<TABLE class='STD' >\n";
		echo "  <TR>";
		echo "    <TH class='STD' style='width:116px;'> Player News: </TH>";
		if ($p_all_unread > 0) $vf->display_id_button("All [$p_all_unread]", "D04040", "F04040", "player_news_all", "102px", "main_page.php5?view=news&subview=list&category=player&type=all");
		else $vf->display_id_button("All [$p_all_unread]", "A0A040", "C0C040", "player_news_all", "102px", "main_page.php5?view=news&subview=list&category=player&type=all");
		
		if ($p_launch_unread > 0) $vf->display_id_button("Launch [$p_launch_unread]", "D04040", "F04040", "player_news_launch", "102px", "main_page.php5?view=news&subview=list&category=player&type=launch");
		else $vf->display_id_button("Launch [$p_launch_unread]", "90A040", "B0C040", "player_news_launch", "102px", "main_page.php5?view=news&subview=list&category=player&type=launch");
		
		if ($p_battle_unread > 0) $vf->display_id_button("Battle [$p_battle_unread]", "D04040", "F04040", "player_news_battle", "102px", "main_page.php5?view=news&subview=list&category=player&type=battle");
		else $vf->display_id_button("Battle [$p_battle_unread]", "80A040", "A0C040", "player_news_battle", "102px", "main_page.php5?view=news&subview=list&category=player&type=battle");
		
		if ($p_items_unread > 0) $vf->display_id_button("Items [$p_items_unread]", "D04040", "F04040", "player_news_items", "102px", "main_page.php5?view=news&subview=list&category=player&type=items");
		else $vf->display_id_button("Items [$p_items_unread]", "809040", "A0B040", "player_news_items", "102px", "main_page.php5?view=news&subview=list&category=player&type=items");
		
		if ($p_scans_unread > 0) $vf->display_id_button("Scans [$p_scans_unread]", "D04040", "F04040", "player_news_scans", "102px", "main_page.php5?view=news&subview=list&category=player&type=scans");
		else $vf->display_id_button("Scans [$p_scans_unread]", "808040", "A0A040", "player_news_scans", "102px", "main_page.php5?view=news&subview=list&category=player&type=scans");
		
		if ($p_alliance_unread > 0) $vf->display_id_button("Alliance [$p_alliance_unread]", "D04040", "F04040", "player_news_alliance", "102px", "main_page.php5?view=news&subview=list&category=player&type=alliance");
		else $vf->display_id_button("Alliance [$p_alliance_unread]", "708040", "90A040", "player_news_alliance", "102px", "main_page.php5?view=news&subview=list&category=player&type=alliance");
		
		if ($p_misc_unread > 0) $vf->display_id_button("Misc [$p_misc_unread]", "D04040", "F04040", "player_news_misc", "102px", "main_page.php5?view=news&subview=list&category=player&type=misc");
		else $vf->display_id_button("Misc [$p_misc_unread]", "707040", "909040", "player_news_misc", "102px", "main_page.php5?view=news&subview=list&category=player&type=misc");
		echo "  </TR>\n";

		$u_all_unread = $nm->count_unread_universe_news_by_type($player_name, "all");
		$u_launch_unread = $nm->count_unread_universe_news_by_type($player_name, "launch");
		$u_battle_unread = $nm->count_unread_universe_news_by_type($player_name, "battle");
		$u_items_unread = $nm->count_unread_universe_news_by_type($player_name, "items");
		$u_scans_unread = $nm->count_unread_universe_news_by_type($player_name, "scans");
		$u_alliance_unread = $nm->count_unread_universe_news_by_type($player_name, "alliance");
		$u_misc_unread = $nm->count_unread_universe_news_by_type($player_name, "misc");
		
		if ($category == 'universe' && $type == 'launch') $u_launch_unread = 0;
		if ($category == 'universe' && $type == 'battle') $u_battle_unread = 0;
		if ($category == 'universe' && $type == 'items') $u_items_unread = 0;
		if ($category == 'universe' && $type == 'scans') $u_scans_unread = 0;
		if ($category == 'universe' && $type == 'alliance') $u_alliance_unread = 0;
		if ($category == 'universe' && $type == 'misc') $u_misc_unread = 0;
		
		echo "  <TR>";
		echo "    <TH class='STD' style='width:116;'> Universe News: </TH>";
		$vf->display_id_button("All", "C0B040", "E0C040", "universe_news_all", "102px", "main_page.php5?view=news&subview=list&category=universe&type=all");
		
		if ($u_launch_unread > 0) $vf->display_id_button("Launch [$u_launch_unread]", "D04040", "F04040", "universe_news_launch", "102px", "main_page.php5?view=news&subview=list&category=universe&type=launch");
		else $vf->display_id_button("Launch [$u_launch_unread]", "C0A040", "E0C040", "universe_news_launch", "102px", "main_page.php5?view=news&subview=list&category=universe&type=launch");

		if ($u_battle_unread > 0) $vf->display_id_button("Battle [$u_battle_unread]", "D04040", "F04040", "universe_news_battle", "102px", "main_page.php5?view=news&subview=list&category=universe&type=battle");
		else $vf->display_id_button("Battle [$u_battle_unread]", "B0A040", "D0C040", "universe_news_battle", "102px", "main_page.php5?view=news&subview=list&category=universe&type=battle");

		if ($u_items_unread > 0) $vf->display_id_button("Items [$u_items_unread]", "D04040", "F04040", "universe_news_items", "102px", "main_page.php5?view=news&subview=list&category=universe&type=items");
		else $vf->display_id_button("Items [$u_items_unread]", "B09040", "D0B040", "universe_news_items", "102px", "main_page.php5?view=news&subview=list&category=universe&type=items");

		if ($u_scans_unread > 0) $vf->display_id_button("Scans [$u_scans_unread]", "D04040", "F04040", "universe_news_scans", "102px", "main_page.php5?view=news&subview=list&category=universe&type=scans"); 
		else $vf->display_id_button("Scans [$u_scans_unread]", "B08040", "D0A040", "universe_news_scans", "102px", "main_page.php5?view=news&subview=list&category=universe&type=scans");

		if ($u_alliance_unread > 0) $vf->display_id_button("Alliance [$u_alliance_unread]", "D04040", "F04040", "universe_news_alliance", "102px", "main_page.php5?view=news&subview=list&category=universe&type=alliance");
		else $vf->display_id_button("Alliance [$u_alliance_unread]", "A08040", "C0A040", "universe_news_alliance", "102px", "main_page.php5?view=news&subview=list&category=universe&type=alliance");

		if ($u_misc_unread > 0) $vf->display_id_button("Misc [$u_misc_unread]", "D04040", "F04040", "universe_news_misc", "102px", "main_page.php5?view=news&subview=list&category=universe&type=misc");
		else $vf->display_id_button("Misc [$u_misc_unread]", "A07040", "C09040", "universe_news_misc", "102px", "main_page.php5?view=news&subview=list&category=universe&type=misc");

		echo "  </TR>\n";
		echo "</TABLE>\n";
	}

	function display_all_news_bar($player_name, $category, $type) {
		$vf = new ViewFunctions();

		echo "<TABLE class='STD' >\n";
		echo "  <TR>";
		$vf->display_id_button("Read all news of $type:$category at once", "C0A040", "E0C040", "all_news_button", "", "main_page.php5?view=news&subview=all&category=$category&type=$type");
		echo "  </TR>\n";
		echo "</TABLE>\n";
		
	}

	function display_all_news_of_category($player_name, $category, $type) {
		$vf = new ViewFunctions();
		$nm = new NewsModel();
		if ($category == 'player' ) $news_list = $nm->get_player_news_by_type($player_name, $type);
		else {
			$news_list = $nm->get_universe_news_by_type($player_name, $type);
			$nm->set_last_seen_news($player_name, $type);
		}
		
		$count_news_list = count($news_list);
		for ($i=0; $i < $count_news_list; $i++) {
			$this->display_individual_news_item($player_name, $category, $news_list[$i]->id);
			echo "<BR />\n";
		}
	}
	
	
	function display_news_list($player_name, $category, $type) {
		$vf = new ViewFunctions();
		$nm = new NewsModel();
		if ($category == 'player' ) $news_list = $nm->get_player_news_by_type($player_name, $type);
		else {
			$news_list = $nm->get_universe_news_by_type($player_name, $type);
			$nm->set_last_seen_news($player_name, $type);
		}
		
		echo " <TABLE class='NEWS'>\n";
		$count_news_list = count($news_list);
		echo "  <TR>\n";
		echo "  <TH class='NEWS'>ID</TH>";
		echo "  <TH class='NEWS'>Subject</TH>";
		echo "  <TH class='NEWS'>Tick</TH>";
		echo "  <TH class='NEWS'>Time</TH>";
		echo "  </TR>\n";

		for ($i=0; $i < $count_news_list; $i++) {
			$class='NEWS-GOOD';
			$news_item = $news_list[$i];
			if ($news_item->has_been_read == false) { $color = "A02000"; $highlight_color="C04020"; }
			else { $color = "705020"; $highlight_color="C09060"; }
			
			if ($category == 'universe') { $color = "705020"; $highlight_color="C09060"; } // no unread universe news
			
			echo "  <TR>\n";
			$vf->display_button("$news_item->id", $color, $highlight_color, "main_page.php5?view=news&subview=individual&category=$category&id={$news_item->id}");
			$vf->display_button("$news_item->subject", $color, $highlight_color, "main_page.php5?view=news&subview=individual&category=$category&id={$news_item->id}");
			$vf->display_button("$news_item->tick", $color, $highlight_color, "main_page.php5?view=news&subview=individual&category=$category&id={$news_item->id}");
			$vf->display_button("$news_item->time", $color, $highlight_color, "main_page.php5?view=news&subview=individual&category=$category&id={$news_item->id}");
			echo "  </TR>\n";
		}
		echo " </TABLE>\n";
		
	}
	
	function display_individual_news_item($player_name, $category, $id) {
		$nm = new NewsModel();
		$news_item = $nm->get_individual_news_item($player_name, $category, $id);
		
		echo " <TABLE class='NEWS'>\n";
		echo "  <TR>\n";
		echo "   <TH class='NEWS-GOOD' style='width:5%' >$news_item->id </TH>";
		echo "   <TH class='NEWS-GOOD' style='width:75%' >$news_item->subject </TH>";
		echo "   <TH class='NEWS-GOOD' style='width:20%' >$news_item->time </TH>";
		echo "  </TR>\n";
		echo "  <TR>\n";
		echo "   <TD class='NEWS-GOOD' colspan='3' style='text-align:left' >$news_item->text </TD>";
		echo "  </TR>\n";
		echo " </TABLE>\n";

		if ($category == 'player') $nm->mark_news_read($player_name, $id);
	}
	
	function display_individual_news($id) {
	  $conn = db_connect();
		$query = "select * from news where ID=$id";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$class='NEWS-GOOD';
			
			echo " <TABLE class='NEWS'>\n";
			echo "  <TR>\n";
			echo "   <TH class='$class'>$row->subject </TH>";
			echo "   <TH class='$class'>$row->time </TH>";
			echo "  </TR>\n";
			echo "  <TR>\n";
			echo "   <TD class='$class' colspan='2' style='text-align:left' >$row->text </TD>";
			echo "  </TR>\n";
			echo " </TABLE>\n";
		}
	}
	
	
	function display_quick_news() {
		$gm = new GameModel();
		$old_tick = $gm->get_current_tick() - 100;
		
		
	  $conn = db_connect();
		$query = "select * from news where tick>$old_tick order by ID desc";
		$result = $conn->query($query);
		echo " <TABLE class='NEWS'>\n";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$class='NEWS-GOOD';
			
			if (strlen($row->player_name)==0 ) $player = "&nbsp;";
			else $player = $row->player_name;
			
			echo "  <TR>\n";
			echo "   <TD class='NEWS-GOOD'>$row->ID </TD>";
			echo "   <TD class='NEWS-GOOD'>$player </TD>";
			echo "   <TD class='NEWS-GOOD'>$row->subject </TD>";
			echo "   <TD class='NEWS-GOOD'>$row->time </TD>";
			echo "  </TR>\n";
		}
		echo " </TABLE>\n";
	}
}

?>