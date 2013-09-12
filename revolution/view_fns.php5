<?php

class ViewFunctions {
	function display_link_button($name, $color, $over_color, $href) {
		echo "<TR>";
		$this->display_button($name, $color, $over_color, $href);
		echo "</TR>";
	}

	function display_button($name, $color, $over_color, $href) {
		echo "<TD class='SIDEBAR' style='background-color:$color' onClick=\"location.href='$href'\"
		onMouseOver=\"this.style.backgroundColor='$over_color'\" onMouseOut=\"this.style.backgroundColor='$color'\">
		 $name</TD>";
	}

	function display_left_button($name, $color, $over_color, $href) {
		echo "<TD class='SIDEBAR' style='background-color:$color;text-align:left' onClick=\"location.href='$href'\"
		onMouseOver=\"this.style.backgroundColor='$over_color'\" onMouseOut=\"this.style.backgroundColor='$color'\">
		 $name</TD>";
	}
	
	function display_right_button($name, $color, $over_color, $href) {
		echo "<TD class='SIDEBAR' style='background-color:$color;text-align:right' onClick=\"location.href='$href'\"
		onMouseOver=\"this.style.backgroundColor='$over_color'\" onMouseOut=\"this.style.backgroundColor='$color'\">
		 $name</TD>";
	}

	function display_id_button($name, $color, $over_color, $id, $width, $href) {
		echo "<TD class='SIDEBAR' style='background-color:$color;width:$width' id='$id' onClick=\"location.href='$href'\"
		onMouseOver=\"this.style.backgroundColor='$over_color'\" onMouseOut=\"this.style.backgroundColor='$color'\">
		 $name</TD>";
	}

	function display_confirmable_button($name, $color, $over_color, $href) {
		echo "<TD class='SIDEBAR' style='background-color:$color' onClick=\"javascript:confirm_link('Are you sure', '$href')\"
		onMouseOver=\"this.style.backgroundColor='$over_color'\" onMouseOut=\"this.style.backgroundColor='$color'\">
		 $name</TD>";		
	}
	
	function display_command_button($name, $color, $over_color, $href) {
		echo "<TR>";
		echo "<TD class='COMMANDBAR' style='background-color:$color' onClick=\"location.href='$href'\"
		onMouseOver=\"this.style.backgroundColor='$over_color'\" onMouseOut=\"this.style.backgroundColor='$color'\">
		<A href='$href' style='color:white;text-decoration:none'> $name</A></TD>";
		echo "</TR>";
	}

	function display_colspan_button($name, $color, $colspan, $over_color, $href) {
		echo "<TD class='SIDEBAR' colspan='$colspan' style='background-color:$color' onClick=\"location.href='$href'\"
		onMouseOver=\"this.style.backgroundColor='$over_color'\" onMouseOut=\"this.style.backgroundColor='$color'\">$name</TD>";
		
	}
	

	function display_inactive_link_button($name, $color) {
		echo "<TR>";
		$this->display_inactive_button($name, $color);
		echo "</TR>";
	}

	function display_inactive_button($name, $color) {
		echo "<TD class='SIDEBAR' style='background-color:$color'>$name</TD>";
		
	}
	
	function make_display_button($name, $color, $over_color, $href) {
		return "<TD class='SIDEBAR' style='background-color:$color' onClick=\"location.href='$href'\"
		onMouseOver=\"this.style.backgroundColor='$over_color'\" onMouseOut=\"this.style.backgroundColor='$color'\">
		 $name</TD>";
	}

	function make_rowspan_button($name, $color, $over_color, $rowspan, $href) {
		return "<TD class='SIDEBAR' style='background-color:$color' rowspan='$rowspan' onClick=\"location.href='$href'\"
		onMouseOver=\"this.style.backgroundColor='$over_color'\" onMouseOut=\"this.style.backgroundColor='$color'\">
		 $name</TD>";
	}

}
// Convenience Functions no class required to call them

function show_info($string) {
	if (strlen($_SESSION['status_info']) == 0)
		$_SESSION['status_info'] = $string ;
	else
		$_SESSION['status_info'] = $_SESSION['status_info'] . "<br />" . $string;
}
		
function show_warning($string) {
	if (strlen($_SESSION['warning_info']) == 0)
		$_SESSION['warning_info'] = $string ;
	else
		$_SESSION['warning_info'] = $_SESSION['warning_info'] . "<br />" . $string;
}

function show_error($string) {
	if (strlen($_SESSION['error_info']) == 0)
		$_SESSION['error_info'] = $string ;
	else
		$_SESSION['error_info'] = $_SESSION['error_info'] . "<br />" . $string;
}
?>