<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Plan Show History Page
#
# $RCSfile: testset_show_test_plan_history_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   		= basename(__FILE__);
$form_name						= 'add_testset';
$action_page					= 'testset_add_action.php';
$testset_edit_page				= 'testset_edit_page.php';
$testset_add_tests_page			= 'testset_add_tests_page.php';
$delete_page 					= 'delete_page.php';
$testset_copy_page				= 'testset_copy_page.php';
$testset_edit_description_page	= 'testset_edit_description_page.php';
$s_project_properties   		= session_get_project_properties();
$project_name           		= $s_project_properties['project_name'];
$project_id 					= $s_project_properties['project_id'];
$row_style              		= '';

$s_release_properties	= session_set_properties( "release", $_GET );
$release_id				= $s_release_properties['release_id'];
$release_name			= admin_get_release_name($release_id);
$build_id				= $s_release_properties['build_id'];
$build_name				= admin_get_build_name($build_id);

$s_table_display_options	= session_set_display_options( "testset", $_GET );
$order_by					= $s_table_display_options['order_by'];
$order_dir					= $s_table_display_options['order_dir'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('release_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_release_map(	Array(	"release_link",
							"build_link",
							"<a href=testset_page.php>". lang_get("testsets") ."</a>",
							lang_get('test_plan_history') ) );

$rows = testset_get_test_plan_log( $_GET['test_plan_id'] );

if( !empty($rows) ) {

	print"<br><br>";
	print"<table class=width100 rules=cols>". NEWLINE;
	print"<tr>". NEWLINE;
	html_tbl_print_header( lang_get("file_name") );
	html_tbl_print_header( lang_get("view") );
	html_tbl_print_header( lang_get("download") );
	html_tbl_print_header( lang_get("uploaded_by") );
	html_tbl_print_header( lang_get("date_added") );
	html_tbl_print_header( lang_get("version") );
	html_tbl_print_header( lang_get("info") );
	print"</tr>". NEWLINE;

	foreach($rows as $row) {

		$file_name = $s_project_properties['test_plan_upload_path'] . $row[TEST_PLAN_VERSION_FILENAME];
		$row_style = html_tbl_alternate_bgcolor( $row_style );

		print"<tr class=$row_style>". NEWLINE;
		print"<td>". $row[TEST_PLAN_NAME]. "</td>". NEWLINE;
		print"<td><a href='$file_name' target='_blank'>". lang_get('view') ."</a></td>". NEWLINE;
		print"<td><a href='download.php?upload_filename=$file_name'>". lang_get('download') ."</a></td>". NEWLINE;
		print"<td>". $row[TEST_PLAN_VERSION_UPLOADEDBY]. "</td>". NEWLINE;
		print"<td>". $row[TEST_PLAN_VERSION_UPLOADEDDATE]. "</td>". NEWLINE;
		print"<td>". $row[TEST_PLAN_VERSION_VERSION]. "</td>". NEWLINE;
		print"<td>". html_info_icon( $row[TEST_PLAN_VERSION_COMMMENTS] ). "</td>". NEWLINE;
		print"</tr>". NEWLINE;

	}

	print"</table>". NEWLINE;

	print"<br><br>". NEWLINE;

}

html_print_footer();

# ---------------------------------------------------------------------
# $Log: testset_show_test_plan_history_page.php,v $
# Revision 1.3  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>