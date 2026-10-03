<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Report Build Status
#
# $RCSfile: report_build_status_page.php,v $  $Revision: 1.6 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_name			= session_get_project_name();
$page                   = basename(__FILE__);

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$row_style = '';

$order_by		= BUILD_DATE_REC;
$order_dir		= "ASC";
$page_number	= 1;

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);

if( isset($_GET['_release_id']) ) {
	$release_id 	= $_GET['_release_id'];;
}

if( isset($_GET['_build_id']) ) {
	$build_id 		= $_GET['_build_id'];
}

html_window_title();



html_print_body();
html_page_title($project_name ." - " . lang_get('report_build_status_page') );
html_page_header( $db, $project_name );
html_print_menu();
html_browse_release_menu($db, $page, $project_id, "", true, false);

error_report_check($_GET);

global $db;

print"<div align=center>";
if( isset( $build_id ) && $build_id != 'all') {

	$build_status 			= report_get_build_status( $build_id );
	$build_date_received 	= $build_status[0];
	$num_of_test_sets 		= $build_status[1];
	$num_of_tests 			= $build_status[2];
	$num_of_passed 			= $build_status[3];
	$num_of_failed 			= $build_status[4];
	$num_awaiting_review	= $build_status[5];
	$num_wip				= $build_status[6];
	$num_not_running		= $build_status[7];
	$num_not_started		= $build_status[8];
	$num_incomplete			= $build_status[9];


	print"<br><br>". NEWLINE;
	print"<form method=post action='$page?_release_id=$release_id&amp;_build_id=$build_id'>";
	print"<table class=width90 rules=cols>". NEWLINE;

	print"<tr>". NEWLINE;
	html_tbl_print_header( lang_get('build_date_received'),	BUILD_DATE_REC, $order_by, $order_dir );
	html_tbl_print_header( lang_get('num_of_test_sets') );
	html_tbl_print_header( lang_get('num_of_tests') );
	html_tbl_print_header( lang_get('num_of_passed') );
	html_tbl_print_header( lang_get('num_of_failed') );
	html_tbl_print_header( lang_get('num_of_not_started') );
	html_tbl_print_header( lang_get('num_of_wip') );
	html_tbl_print_header( lang_get('num_of_incomplete') );
	html_tbl_print_header( lang_get('num_of_finished') );
	print"</tr>". NEWLINE;

	print"<tr>". NEWLINE;
	print"<td>$build_date_received</td>". NEWLINE;
	print"<td>$num_of_test_sets</td>". NEWLINE;
	print"<td>$num_of_tests</td>". NEWLINE;
	print"<td>$num_of_passed</td>". NEWLINE;
	print"<td>$num_of_failed</td>". NEWLINE;
	print"<td>$num_not_started</td>". NEWLINE;
	print"<td>$num_wip</td>". NEWLINE;
	print"<td>$num_incomplete</td>". NEWLINE;
	print"<td>$num_awaiting_review</td>". NEWLINE;
	print"</tr>". NEWLINE;

	print"</table>". NEWLINE;
	print"</form>";
	print"<br><br>". NEWLINE;
	
	if( $num_of_tests != 0 ) {

	
		# Pass pie chart legend, data, title to the pie_chart_image
		print"<img src=\"./images/pie_chart_image.php";
			
		# pie chart title
		print"?graph_title=".lang_get("status_of")." ".admin_get_release_name($release_id).", ".admin_get_build_name($build_id);
		
		# legend
		print"&amp;legend=";
		print"'Passed',";
		print"'Failed',";
		print"'Awaiting Review',";
		print"'WIP',";
		print"'Incomplete',";
		print"'Not Started'";

		# data
		print"&amp;data=";
		print"$num_of_passed,";
		print"$num_of_failed,";
		print"$num_awaiting_review,";
		print"$num_wip,";
		print"$num_incomplete,";
		print"$num_not_started,";
		
		# theme
		print"&amp;theme=test\"";
		print" alt=Build Status Pie Chart>". NEWLINE;

	} else {

	}
}
print"</div>";
html_print_footer();


# ------------------------------------
# $Log: report_build_status_page.php,v $
# Revision 1.6  2009/01/09 08:10:52  cryobean
# implemented feature request 2435387
# included the not finished tests of testsets in calculation
# fix was sent by Bruce Butler
#
# Revision 1.5  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.4  2006/06/10 01:55:06  gth2
# no message
#
# Revision 1.3  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2005/12/08 19:39:51  gth2
# updating reports containing calls to jp-graph - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>