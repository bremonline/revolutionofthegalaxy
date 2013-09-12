<?php
	require_once('description_data.php5'); 


class DescriptionPanel {
	function show_text_panel_inside($name, $type, $category, $style) {
		$player_name = $_SESSION['player_name'];
		$dd = new DescriptionData();
		if ($dd->does_description_exist($name, $type, $category) ){
			$dd->db_fill($name, $type, $category);
				echo "<TABLE width='100%'>\n";
				echo "<TR><TD class='STD' style='font-size:8;text-align:right;border:0px;padding:0px;'><a style='color:grey;' href=\"javascript:edit_window('$name','$type', '$category', '$player_name')\">EDIT</a></TD></TR>\n";			
				echo "<TR><TD class='STD' style='border:0px;padding:0px;text-align:left'>$dd->text</TD></TR>\n";			
				echo "</TABLE>";
				echo "</TD>";
		
		} else {
			echo "<TABLE width='100%'>\n";
			echo "<TR><TD class='STD' style='font-size:8;text-align:right;border:0px;padding:0px;'><a style='color:grey;' href=\"javascript:edit_window('$name','$type', '$category', '$player_name')\">ADD</a></TD></TR>\n";			
			echo "<TR><TD class='STD' style='border:0px;padding:0px;'><I>No description available</I></TD></TR>\n";			
			echo "</TABLE>";
		}		
	}

	function show_text_panel($name, $type, $category, $style) {
		echo "<TD class='STD' style='vertical-align:top'>";
 		$this->show_text_panel_inside($name, $type, $category, $style);
		echo "</TD>";
	}
	
	function show_text_panel_uneditable_inside($name, $type, $category, $style) {
		$player_name = $_SESSION['player_name'];
		$dd = new DescriptionData();
		if ($dd->does_description_exist($name, $type, $category) ){
			$dd->db_fill($name, $type, $category);
			echo "<TABLE width='100%'>\n";
			echo "<TR><TD class='STD' style='border:0px;padding:0px;text-align:left'>$dd->text</TD></TR>\n";			
			echo "</TABLE>";		
		} else {
			echo "<TABLE width='100%'>\n";
			echo "<TR><TD class='STD' style='border:0px;padding:0px;'><I>No description available</I></TD></TR>\n";			
			echo "</TABLE>";			
		}	
	}
}

?>