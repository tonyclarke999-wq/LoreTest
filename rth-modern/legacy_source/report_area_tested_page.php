<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Report Area Tested
#
# $RCSfile: report_area_tested_page.php,v $  $Revision: 1.3 $
# ------------------------------------

include"./api/include_api.php";

$project_name = session_get_project_name();
$page                   = basename(__FILE__);

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$row_style = '';

$order_by			= AREA_TESTED_NAME;
$order_dir			= "ASC";
$page_number		= 1;

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);

if( isset($_GET['_release_id']) ) {
	$release_id 	= $_GET['_release_id'];;
}

if( isset($_GET['_build_id']) ) {
	$build_id 		= $_GET['_build_id'];
}

if( isset($_GET['_testset_id']) ) {
	$testset_id 	= $_GET['_testset_id'];
}

html_window_title();

auth_authenticate_user();

html_print_body();
html_page_title($project_name ." - " . lang_get('report_area_tested_page') );
html_page_header( $db, $project_name );
html_print_menu();
html_browse_release_menu($db, $page, $project_id);

print"<br>";
error_report_check( $_GET );

global $db;

print"<div align=center>";
if( isset( $testset_id ) && $testset_id != 'all') {

	print"<form method=post action='$page?_release_id=$release_id&amp;_build_id=$build_id&amp;_testset_id=$testset_id'>";
	print"<table class=width80 rules=cols>". NEWLINE;
	print"<tr>". NEWLINE;
	html_tbl_print_header( lang_get('area_tested'),	AREA_TESTED_NAME, $order_by, $order_dir );
	html_tbl_print_header( lang_get('num_of_tests_for_area') );
	html_tbl_print_header( lang_get('num_of_tests_used') );
	html_tbl_print_header( lang_get('percentage_of_area_tests') );
	print"</tr>". NEWLINE;

	foreach( project_get_areas_tested($project_id, $order_by, $order_dir) as $row_area_tested ) {

		$row_style = html_tbl_alternate_bgcolor($row_style);

		$area_tested	= $row_area_tested[AREA_TESTED_NAME];
		$tests_for_area	= report_get_num_tests_in_area($project_id, $area_tested);
		$tests_used 	= report_get_num_area_tested_in_testset($project_id, $_GET['_testset_id'], $area_tested);

		if( $tests_for_area!=0 ) {
			$percentage_tests_used = sprintf( "%01.2f", ($tests_used / $tests_for_area * 100) );
		} else {
			$percentage_tests_used = "0.00";
		}

		print"<tr class='$row_style'>". NEWLINE;
		print"<td>$area_tested</td>". NEWLINE;
		print"<td>$tests_for_area</td>". NEWLINE;
		print"<td>$tests_used</td>". NEWLINE;
		print"<td>$percentage_tests_used%</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	print"</table>". NEWLINE;
	print"</form>";
}
print"</div>";
html_print_footer();


# ------------------------------------
# $Log: report_area_tested_page.php,v $
# Revision 1.3  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>