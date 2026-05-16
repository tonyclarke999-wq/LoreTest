<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Archive Tests Page
#
# $RCSfile: project_archive_tests_page.php,v $  $Revision: 1.5 $
# ------------------------------------

if( isset($_POST['submit_button']) ) {

	require_once("project_archive_tests_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$page           		= basename(__FILE__);
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$row_style              = '';

$records				= '';

session_set_properties("project_manage", $_GET);
$selected_project_properties 	= session_get_properties("project_manage");
$selected_project_id 			= $selected_project_properties['project_id'];

$selected_project_properties 	= session_set_display_options("project_archive_tests", $_POST);
$order_by 						= $selected_project_properties['order_by'];
$order_dir 						= $selected_project_properties['order_dir'];
$page_number					= $selected_project_properties['page_number'];

session_records(	"archive_tests",
					admin_get_archived_tests($project_id) );

html_window_title();
html_print_body();
html_page_title(project_get_name($selected_project_id) ." - ". lang_get('archive_tests_page') );
html_page_header( $db, $project_name );
html_print_menu();
admin_menu_print( $page, $project_id, $user_id );



html_project_manage_menu();
html_project_manage_tests_menu();

if( !user_has_rights( $selected_project_id, $user_id, MANAGER ) ) {
	print"<div align=center>";
	error_report_display_msg( NO_RIGHTS_TO_VIEW_PAGE );
	print"</div>";
	exit;
}

error_report_check( $_GET );

print"<div align=center>". NEWLINE;
print"<form action='project_archive_tests_page.php' method=post>". NEWLINE;
print"<br>". NEWLINE;

	print"<table class=hide80>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$row = admin_get_tests(	$selected_project_id, $page_number, $order_by, $order_dir );
	print"<input type=hidden name='order_dir' value='$order_dir'>";
	print"<input type=hidden name='order_by' value='$order_by'>";
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

########################################################################################
#

if( empty($row) ) {

	print lang_get('no_archive_tests');
} else {
	//print"<form action='project_archive_tests_page.php' method=post>". NEWLINE;
	print"<input type=hidden name=project_id value=$selected_project_id>";

	print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
	print"<thead>".NEWLINE;
	print"<tr>". NEWLINE;
	#html_tbl_print_header( lang_get('man_auto') );
	#html_tbl_print_header( lang_get('test_name'), TEST_NAME, 			$order_by, $order_dir );
	#html_tbl_print_header( lang_get('testtype'), TEST_TESTTYPE, 		$order_by, $order_dir );
	#html_tbl_print_header( lang_get('area_tested'), TEST_AREA_TESTED, 	$order_by, $order_dir );
	#html_tbl_print_header( lang_get('status'), TEST_STATUS, 			$order_by, $order_dir );
	#html_tbl_print_header( lang_get('archive') );
	
	html_tbl_print_header_not_sortable( lang_get('man_auto') );
	html_tbl_print_header( lang_get('test_name') );
	html_tbl_print_header( lang_get('testtype') );
	html_tbl_print_header( lang_get('area_tested') );
	html_tbl_print_header( lang_get('status') );
	html_tbl_print_header_not_sortable( lang_get('archive') );
	print"</tr>". NEWLINE;
	print"</thead>".NEWLINE;
	print"<tbody>".NEWLINE;
	foreach($row as $test_row) {
		#$row_style = html_tbl_alternate_bgcolor($row_style);
		#print"<tr class='$row_style'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>".html_print_testtype_icon($test_row[TEST_MANUAL], $test_row[TEST_AUTOMATED])."</td>". NEWLINE;
		print"<td>".$test_row[TEST_NAME]."</td>". NEWLINE;
		print"<td>".$test_row[TEST_TESTTYPE]."</td>". NEWLINE;
		print"<td>".$test_row[TEST_AREA_TESTED]."</td>". NEWLINE;
		print"<td>".$test_row[TEST_STATUS]."</td>". NEWLINE;

		if( session_records_ischecked("archive_tests", $test_row[TEST_ID]) ) {
			$checked = "checked";
		} else {
			$checked = "";
		}

		if( empty($records) ) {
			$records = $test_row[TEST_ID]." => ''";
		} else {
			$records .= ", ".$test_row[TEST_ID]." => ''";
		}

		print"<td><input type=checkbox name=row_".$test_row[TEST_ID]." $checked></td>". NEWLINE;
		print"</tr>". NEWLINE;
	}
	print"</tbody>".NEWLINE;
	print"</table>". NEWLINE;

	print"<br>". NEWLINE;
	print"<input type=hidden name=records value=\"$records\">". NEWLINE;
	print"<input type='hidden' name='record_groups' value=\"\">". NEWLINE;

	print"<input type=submit name=submit_button value='".lang_get("archive")."'>". NEWLINE;
	
}
print"</form>". NEWLINE;
print"</div>". NEWLINE;

html_print_footer();

?>