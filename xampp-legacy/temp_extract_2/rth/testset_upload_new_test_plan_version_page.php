<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Plan Upload New Version Page
#
# $RCSfile: testset_upload_new_test_plan_version_page.php,v $  $Revision: 1.3 $
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

$s_table_display_options	= session_set_display_options( "testset", $_POST );
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
							lang_get('upload_new_test_plan') ) );

$test_plan_details = testset_get_test_plan_details( $_GET['test_plan_id'] );

$test_plan_name			= $test_plan_details[TEST_PLAN_NAME];
$test_plan_version 		= $test_plan_details[TEST_PLAN_VERSION_VERSION];
$test_plan_version_id	= $test_plan_details[TEST_PLAN_VERSION_ID];

print"<br><br>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<form enctype=\"multipart/form-data\" name=\"upload\" action=\"testset_upload_new_test_plan_version_action.php\" method=\"post\">". NEWLINE;
print"<input type=hidden name=MAX_FILE_SIZE  value=25000000>". NEWLINE;
print"<input type=hidden name=build_id  value=$build_id>". NEWLINE;
print"<input type=hidden name=old_test_plan_id  value=$test_plan_version_id>". NEWLINE;
print"<input type=hidden name=test_plan_name  value=$test_plan_name>". NEWLINE;
print"<table class=width70>". NEWLINE;
print"<tr>". NEWLINE;
print"	<td>". NEWLINE;
print"	<table class=inner>". NEWLINE;
print"	<tr>". NEWLINE;
print"		<td class=left><h4>". lang_get("uploading_new_version") ." $test_plan_name</h4></td>". NEWLINE;
print"	</tr>". NEWLINE;
print"	<tr>". NEWLINE;
print"		<td>". NEWLINE;
print"		<table>". NEWLINE;
print"		<tr>". NEWLINE;
print"			<td class=right>". lang_get("file_name") ." <span class='required'>*</span></td>". NEWLINE;
print"			<td class=left><input type=file name=upload_file size=90></td>". NEWLINE;
print"		</tr>". NEWLINE;
print"		<tr>". NEWLINE;
print"			<td class=right>". lang_get("comments") ."</td>". NEWLINE;
print"			<td class=left><textarea name=comments rows=6 cols=80></textarea></td>". NEWLINE;
print"		</tr>". NEWLINE;
print"		<tr>". NEWLINE;
print"			<td class=right>". lang_get("version") ." <span class='required'>*</span></td>". NEWLINE;
print"			<td class=left><input type=text name=version value='" . util_increment_version($test_plan_version) . "' size=10></td>". NEWLINE;
print"		</tr>". NEWLINE;
print"		<tr>". NEWLINE;
print"			<td></td>". NEWLINE;
print"			<td><input type=submit value=". lang_get("upload") ."></td>". NEWLINE;
print"		</tr>". NEWLINE;
print"		</table>". NEWLINE;
print"		</td>". NEWLINE;
print"	</tr>". NEWLINE;
print"	</table>". NEWLINE;
print"	</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>";

print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: testset_upload_new_test_plan_version_page.php,v $
# Revision 1.3  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>