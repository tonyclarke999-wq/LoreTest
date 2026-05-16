<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Page
#
# $RCSfile: testset_page.php,v $  $Revision: 1.5 $
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

$redirect_url			= $page ."?build_id=". $build_id;

$display_options	= session_set_display_options( "testset", $_POST );
$order_by			= $display_options['order_by'];
$order_dir			= $display_options['order_dir'];

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];

html_window_title();
html_print_body( $form_name, 'testset_name_required');
html_page_title($project_name ." - ". lang_get('testset_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_release_map(	Array(	"release_link",
							"build_link",
							lang_get("testsets") ) );


error_report_check( $_GET );

print"<div align=center>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<form method=post name=$form_name action=$action_page>". NEWLINE;
print"<table class=inner>". NEWLINE;

print"<tr>". NEWLINE;
print"<td colspan='2'><h4>". lang_get('add_testset') ." - $build_name</h4></td>". NEWLINE;
print"</tr>". NEWLINE;


# TESTSET NAME
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('testset_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class='form-data-l'><input type='text' maxlength='20' name='testset_name_required' size=30 value='". session_validate_form_get_field("testset_name_required"). "'></td>". NEWLINE;
print"</tr>". NEWLINE;

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('description') ."</td>". NEWLINE;
print"<td class='form-data-l'><textarea name='testset_description' rows=5 cols=30 >". session_validate_form_get_field("testset_description").	"</textarea></td>". NEWLINE;
print"</tr>". NEWLINE;

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td colspan='2' class='form-data-c'><input type='submit' value='". lang_get('add') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;
print"</form>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br><br>". NEWLINE;

################################################################################
# TESTSET TABLE
$testset_details = testset_get_details_by_build( $build_id, null, $order_by, $order_dir );

if( !empty( $testset_details ) ) {

	print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
	print"<thead>".NEWLINE;
	print"<tr>". NEWLINE;
	html_tbl_print_header( lang_get('testset_name') );
	html_tbl_print_header_not_sortable( lang_get('edit_add_tests') );
	#html_tbl_print_header( lang_get('testset_date_received'), TS_DATE_CREATED, $order_by, $order_dir, $page );
	html_tbl_print_header( lang_get('testset_date_received') );
	html_tbl_print_header( lang_get('description') );
	if( user_has_rights( $project_id, $user_id, ADMIN ) )
	{
		html_tbl_print_header_not_sortable( lang_get('copy') );
	}
	html_tbl_print_header_not_sortable( lang_get('edit') );
	html_tbl_print_header_not_sortable( lang_get('delete') );
	print"</tr>". NEWLINE;
	print"</thead>".NEWLINE;
	print"<tbody>".NEWLINE;
	for( $i=0; $i < sizeof( $testset_details ); $i++ ) {

		extract( $testset_details[$i], EXTR_PREFIX_ALL, 'v' );

		$testset_id				= ${'v_' . TS_ID};
		$testset_name			= ${'v_' . TS_NAME};
		$testset_date_created	= ${'v_' . TS_DATE_CREATED};
		$testset_description	= ${'v_' . TS_DESCRIPTION};

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		print"<td class='tbl-l'>$testset_name</td>". NEWLINE;

		if( testset_number_of_tests($testset_id) ) { # edit tests in testset
			$edit_add_link_name = lang_get('edit_testset');
			$edit_add_link_href = $testset_edit_page;
		}
		else { # add tests to testset
			$edit_add_link_name = lang_get('add_tests');
			$edit_add_link_href = $testset_add_tests_page;
		}

		print"<td class='tbl-c'><a href='$edit_add_link_href?testset_id=$testset_id'>$edit_add_link_name</a></td>". NEWLINE;
		print"<td class='tbl-c'>$testset_date_created</td>". NEWLINE;
		print"<td class='tbl-c'>$testset_description</td>". NEWLINE;
		if( user_has_rights( $project_id, $user_id, ADMIN ) )
		{
			print"<td class='tbl-c'><a href='$testset_copy_page?testset_id=$testset_id'>". lang_get('copy') ."</a></td>". NEWLINE;
		}
		print"<td class='tbl-c'><a href='$testset_edit_description_page?testset_id=$testset_id'>". lang_get('edit') ."</a></td>". NEWLINE;
		print"<td class='tbl-c'>";
		print "<form method='post' action='$delete_page'>". NEWLINE;
			print"<input type='submit' name='delete' value='". lang_get( 'delete' ) ."' class='page-numbers'>";
			print"<input type='hidden' name='r_page' value='$redirect_url'>". NEWLINE;
			print"<input type='hidden' name='f' value='delete_testset'>". NEWLINE;
			print"<input type='hidden' name='id' value=$testset_id>". NEWLINE;
			print"<input type='hidden' name='msg' value='40'>". NEWLINE;
			print"</form>";
			print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		/*
		print"<td class='tbl-c'><a href='$delete_page?r_page=$page&f=delete_testset&id=$testset_id&msg=40'>". lang_get('delete') ."</a></td>". NEWLINE;
		*/

	}
	print"</tbody>".NEWLINE;
	print"</table>". NEWLINE;
} else {
	html_no_records_found_message( lang_get('no_testsets') );
}

print"<br><br>". NEWLINE;

################################################################################
# Test Plan File Upload

print"<h2>";
print lang_get("test_plan_file_upload");
print"</h2>". NEWLINE;

print"<hr>". NEWLINE;
print"<br>". NEWLINE;

$rows = testset_get_test_plans($build_id);

if( !empty($rows) ) {
	print"<table class=width100 rules=cols>". NEWLINE;
	print"<tr>". NEWLINE;
					html_tbl_print_header( lang_get("file_type") );
					html_tbl_print_header( lang_get("file_name") );
					html_tbl_print_header( lang_get("version") );
					html_tbl_print_header( lang_get("view") );
					html_tbl_print_header( lang_get("download") );
					html_tbl_print_header( lang_get("show_log") );
					html_tbl_print_header( lang_get("add_version") );
					html_tbl_print_header( lang_get("uploaded_by") );
					html_tbl_print_header( lang_get("date_added") );
					html_tbl_print_header( lang_get("info") );
					html_tbl_print_header( lang_get("delete") );
	print"</tr>". NEWLINE;

	foreach($rows as $row) {

		$test_plan_id 	= $row[TEST_PLAN_ID];
		$upload_path	= $s_project_properties['test_plan_upload_path'];
		$file_name 		= $upload_path . $row[TEST_PLAN_VERSION_FILENAME];
		$row_style 		= html_tbl_alternate_bgcolor( $row_style );

		print"<tr class=$row_style>". NEWLINE;
		print"<td>".html_file_type( $row[TEST_PLAN_NAME] )."</td>". NEWLINE;
		print"<td>". $row[TEST_PLAN_NAME]. "</td>". NEWLINE;
		print"<td>". $row[TEST_PLAN_VERSION_VERSION]. "</td>". NEWLINE;
		print"<td><a href='$file_name' target='_blank'>". lang_get('view') ."</a></td>". NEWLINE;
		print"<td><a href='download.php?upload_filename=$file_name'>". lang_get('download') ."</a></td>". NEWLINE;
		print"<td><a href=\"testset_show_test_plan_history_page.php?test_plan_id=$test_plan_id\">". lang_get('show') ."</a></td>". NEWLINE;
		print"<td><a href=\"testset_upload_new_test_plan_version_page.php?test_plan_id=$test_plan_id\">". lang_get('add') ."</a></td>". NEWLINE;
		print"<td>". $row[TEST_PLAN_VERSION_UPLOADEDBY]. "</td>". NEWLINE;
		print"<td>". $row[TEST_PLAN_VERSION_UPLOADEDDATE]. "</td>". NEWLINE;
		print"<td>". html_info_icon( $row[TEST_PLAN_VERSION_COMMMENTS] ). "</td>". NEWLINE;
		print"<td>";
				print"<form method='post' action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete' value='". lang_get( 'delete' ) ."' class='page-numbers'>";
				print"<input type='hidden' name='r_page' value='$redirect_url'>". NEWLINE;
				print"<input type='hidden' name='f' value='delete_testplan'>". NEWLINE;
				print"<input type='hidden' name='id' value=$test_plan_id>". NEWLINE;
				print"<input type='hidden' name='msg' value=".DEL_TEST_PLAN.">". NEWLINE;
				print"</form>";
		print"</td>". NEWLINE;
				
		print"</tr>". NEWLINE;

	}

	print"</table>". NEWLINE;
	print"<br><br>". NEWLINE;

}

################################################################################
# File Upload Form
print"<form enctype=\"multipart/form-data\" name=\"upload\" action=\"testset_plan_file_upload_action.php\" method=\"post\">". NEWLINE;
print"<input type=hidden name=MAX_FILE_SIZE  value=25000000>". NEWLINE;
print"<input type=hidden name=build_id  value=$build_id>". NEWLINE;
print"<table class=width70>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=left><h4>". lang_get("upload_test_plan") ."</h4></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>";
print"<table>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=right>". lang_get("file_name") ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=left><input type=file name=upload_file size=90></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=right>". lang_get("comments") ."</td>". NEWLINE;
print"<td class=left><textarea name=comments rows=2 cols=80></textarea></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td></td>". NEWLINE;
print"<td><input type=submit value=". lang_get("upload") ."></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</td>";
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>";
print"</div>". NEWLINE;

html_print_footer();

session_validate_form_reset();

# ---------------------------------------------------------------------
# $Log: testset_page.php,v $
# Revision 1.5  2008/01/22 10:01:22  cryobean
# made the table sortable
# changed permission for copy testset feature, only ADMIN is now allowed to do this because this is a dangerous feature where many testsets would be lost if it isn't used correcly
#
# Revision 1.4  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>