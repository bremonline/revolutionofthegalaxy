<?php
	require_once('page_view.php5'); 
	require_once('misc_fns.php5'); 

  $view=$_REQUEST['view'];
  $subview=$_REQUEST['subview'];

	do_html_header('Main Page');

	$pageview = new PageView();
	$pageview->display_main_view($view, $subview);

	do_html_footer();

?>