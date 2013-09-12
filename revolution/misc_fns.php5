<?php

function do_html_header($title)
{
  // print an HTML header

	echo "<html>\n";
  echo "<head>\n";
  echo "  <title>Revolution v1.0 - $title</title>\n";

  echo "  <link rel='icon' href='./favicon-revo.ico' type='image/x-icon'>\n";

  echo "  <link rel='Stylesheet' href='revolution.css' title='Style' type='text/css'/> \n";
  echo "  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'> \n";
  echo "<script src='revolution.js' type='text/javascript'></script>\n";
  echo "<script src='chat.js' type='text/javascript'></script>\n";

  echo "<script src='scripts/jquery-1.2.1.js' type='text/javascript'></script>\n";
  echo "<script src='effects.js' type='text/javascript'></script>\n";

//	show_rss_links();
	
  echo "</head>\n";
 
}

function do_html_footer() {
	echo "</body>\n";
  echo "</html>\n";
}

function do_html_URL($url, $name) {
  // output URL as link and br
  echo "<br /><a href='$url' > $name </a><br />\n";
}

function check_valid_user()
// see if somebody is logged in and notify them if not
{
  if (isset($_SESSION['player_name']))
  {}  else {
  	 $auth = $_COOKIE["auth"];
//		 require_once("cipher.php5");
//		 $cipher = new Cipher('BOOBOO');
//		 $decryptedtext = $cipher->decrypt($auth);
		 $decryptedtext = $auth;

		 list($player_name, $password) = split(":", $decryptedtext);
		 
     // they are not logged in 
     do_html_header("Main Page");
//     $_SESSION['status_info'] = "Session Expired, but automatically re-initiated";
     
     if ($player_name) {
     		require_once("player_data.php5");
     		$player = new PlayerData();
				$status = $player->check_player($player_name, $password);
				if ($status) {
					$_SESSION['player_name'] = $player_name;
					return true;
				}
			}	

     echo "You are not logged in.<br />";
     do_html_url("login.php5", "Login");
     do_html_footer();
     exit;
  }  
}

function display_link_button($name, $color, $over_color, $href) {
	echo "<TR><TD class='SIDEBAR' onClick=\"location.href='$href'\"
	onMouseOver=\"this.className='SIDEBARHIGHLIGHT'\" onMouseOut=\"this.className='SIDEBAR'\">$name</TD></TR>\n";
}



function convertString ( $string ) {
        $find_array = array (
                "/&quot;/",
                "/&amp;/",
                "/&lt;/",
                "/&gt;/",
                "/&nbsp;/",
                "/&iexcl;/",
                "/&cent;/",
                "/&pound;/",
                "/&curren;/",
                "/&yen;/",
                "/&brvbar;/",
                "/&sect;/",
                "/&uml;/",
                "/&copy;/",
                "/&ordf;/",
                "/&laquo;/",
                "/&not;/",
                "/&shy;/",
                "/&reg;/",
                "/&macr;/",
                "/&deg;/",
                "/&plusmn;/",
                "/&sup2;/",
                "/&sup3;/",
                "/&acute;/",
                "/&micro;/",
                "/&para;/",
                "/&middot;/",
                "/&cedil;/",
                "/&sup1;/",
                "/&ordm;/",
                "/&raquo;/",
                "/&frac14;/",
                "/&frac12;/",
                "/&frac34;/",
                "/&iquest;/",
                "/&Agrave;/",
                "/&Aacute;/",
                "/&Acirc;/",
                "/&Atilde;/",
                "/&Auml;/",
                "/&Aring;/",
                "/&AElig;/",
                "/&Ccedil;/",
                "/&Egrave;/",
                "/&Eacute;/",
                "/&Ecirc;/",
                "/&Euml;/",
                "/&Igrave;/",
                "/&Iacute;/",
                "/&Icirc;/",
                "/&Iuml;/",
                "/&ETH;/",
                "/&Ntilde;/",
                "/&Ograve;/",
                "/&Oacute;/",
                "/&Ocirc;/",
                "/&Otilde;/",
                "/&Ouml;/",
                "/&times;/",
                "/&Oslash;/",
                "/&Ugrave;/",
                "/&Uacute;/",
                "/&Ucirc;/",
                "/&Uuml;/",
                "/&Yacute;/",
                "/&THORN;/",
                "/&szlig;/",
                "/&agrave;/",
                "/&aacute;/",
                "/&acirc;/",
                "/&atilde;/",
                "/&auml;/",
                "/&aring;/",
                "/&aelig;/",
                "/&ccedil;/",
                "/&egrave;/",
                "/&eacute;/",
                "/&ecirc;/",
                "/&euml;/",
                "/&igrave;/",
                "/&iacute;/",
                "/&icirc;/",
                "/&iuml;/",
                "/&eth;/",
                "/&ntilde;/",
                "/&ograve;/",
                "/&oacute;/",
                "/&ocirc;/",
                "/&otilde;/",
                "/&ouml;/",
                "/&divide;/",
                "/&oslash;/",
                "/&ugrave;/",
                "/&uacute;/",
                "/&ucirc;/",
                "/&uuml;/",
                "/&yacute;/",
                "/&thorn;/",
                "/&yuml;/"
        );
        $replace_array = array (
                '&#034;',
                '&#038;',
                '&#060;',
                '&#062;',
                '&#160;',
                '&#161;',
                '&#162;',
                '&#163;',
                '&#164;',
                '&#165;',
                '&#166;',
                '&#167;',
                '&#168;',
                '&#169;',
                '&#170;',
                '&#171;',
                '&#172;',
                '&#173;',
                '&#174;',
                '&#175;',
                '&#176;',
                '&#177;',
                '&#178;',
                '&#179;',
                '&#180;',
                '&#181;',
                '&#182;',
                '&#183;',
                '&#184;',
                '&#185;',
                '&#186;',
                '&#187;',
                '&#188;',
                '&#189;',
                '&#190;',
                '&#191;',
                '&#192;',
                '&#193;',
                '',
                '&#195;',
                '&#196;',
                '&#197;',
                '&#198;',
                '&#199;',
                '&#200;',
                '&#201;',
                '&#202;',
                '&#203;',
                '&#204;',
                '&#205;',
                '&#206;',
                '&#207;',
                '&#208;',
                '&#209;',
                '&#210;',
                '&#211;',
                '&#212;',
                '&#213;',
                '&#214;',
                '&#215;',
                '&#216;',
                '&#217;',
                '&#218;',
                '&#219;',
                '&#220;',
                '&#221;',
                '&#222;',
                '&#223;',
                '&#224;',
                '&#225;',
                '&#226;',
                '&#227;',
                '&#228;',
                '&#229;',
                '&#230;',
                '&#231;',
                '&#232;',
                '&#233;',
                '&#234;',
                '&#235;',
                '&#236;',
                '&#237;',
                '&#238;',
                '&#239;',
                '&#240;',
                '&#241;',
                '&#242;',
                '&#243;',
                '&#244;',
                '&#245;',
                '&#246;',
                '&#247;',
                '&#248;',
                '&#249;',
                '&#250;',
                '&#251;',
                '&#252;',
                '&#253;',
                '&#254;',
                '&#255;'
        );
        $string = htmlentities ( $string, ENT_QUOTES );
        $string = preg_replace ( $find_array, $replace_array, $string );
        return $string;
} 

?>