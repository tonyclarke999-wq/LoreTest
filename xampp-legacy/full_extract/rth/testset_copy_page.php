<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Copy Page
#
# $RCSfile: testset_copy_page.php,v $  $Revision: 1.5 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_button']) ) {

	require_once("testset_copy_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

# Session variables
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$s_copy_properties = array();

$release_properties = session_set_properties("release", $_GET);

session_records("testset_copy");

$s_table_display_options	= session_set_display_options("testset_copy", $_POST);
$order_by					= $s_table_display_options['order_by'];
$order_dir					= $s_table_display_options['order_dir'];
$page_number				= $s_table_display_options['page_number'];

$filter_manual_auto			= $s_table_display_options['filter']['manual_auto'];
$filter_test_type			= $s_table_display_options['filter']['test_type'];
$filter_ba_owner			= $s_table_display_options['filter']['ba_owner'];
$filter_qa_owner			= $s_table_display_options['filter']['qa_owner'];
$filter_tester				= $s_table_display_options['filter']['tester'];
$filter_area_tested			= $s_table_display_options['filter']['area_tested'];
$filter_priority			= $s_table_display_options['filter']['priority'];
$filter_per_page			= $s_table_display_options['filter']['per_page'];
$filter_search				= $s_table_display_options['filter']['test_search'];

# Links to pages
$page                   	= basename(__FILE__);
$test_page					= "test_manual_test.php";
$results_test_run_page  	= "results_test_run_page.php";
$testset_status				= "testset_current_status.php";
$testset_signoff_page		= "testset_signoff_page.php";

$test_name = TEST_TBL.".".TEST_NAME;

$row_style	= '';

$page		= basename(__FILE__);

# These two variables store all the records and select groups in a string.
# The string is passed in the POST when the form is submitted so
# session_set_displayed_testset_records when called can determine what records
# where available for the user to check/uncheck.
$records			= "";
$select_group		= "";

html_window_title();
html_print_body();
html_page_title($project_name . " - " . lang_get("testset_copy_page") );
html_page_header( $db, $project_name );
html_print_menu();

html_testset_menu( $db, $page, $project_id, $s_copy_properties );

if( isset( $_GET['testset_menu_testset_id'] ) && $_GET['testset_menu_testset_id'] != 'all') {

	$release_id 	= $_GET['testset_menu_release_id'];
	$build_id 		= $_GET['testset_menu_build_id'];
	$testset_id 	= $_GET['testset_menu_testset_id'];
	$release_name 	= admin_get_release_name($release_id);
	$build_name 	= admin_get_build_name($build_id);
	$testset_name 	= admin_get_testset_name($testset_id);

	print"<form method='post' action='$page?".$_SERVER['QUERY_STRING']."' name=results>". NEWLINE;
	print"<div align=center>". NEWLINE;
	print"<br>". NEWLINE;


	html_print_tests_filter(	$project_id,
								$filter_manual_auto,
								$filter_test_type,
								$filter_ba_owner,
								$filter_qa_owner,
								$filter_tester,
								$filter_area_tested,
								$filter_test_status=null,
								$filter_priority,
								$filter_per_page,
								$filter_search);


	print"<br>". NEWLINE;


	$rows = test_copy_filter_rows(
							$project_id,
							$release_id,
							$build_id,
							$testset_id,
							$filter_manual_auto,
							$filter_ba_owner,
							$filter_qa_owner,
							$filter_tester,
							$filter_test_type,
							$filter_area_tested,
							$filter_priority,
							$filter_per_page,
							$filter_search,
							$order_by,
							$order_dir,
							$page_number );

	$order_by = $s_table_display_options['order_by'];
	$order_dir = $s_table_display_options['order_dir'];

	print"". NEWLINE;

	if( !empty($rows) ) {

		################################################################################
		# Testset table

		print"<table class=width100 rules=cols>". NEWLINE;

		# Table headers
		print"<tr class=tbl_header>". NEWLINE;

		print"<th>";
		print"</th>". NEWLINE;

		html_tbl_print_header( lang_get('test_id'),		TEST_ID,		$order_by, $order_dir );
		print"<th>&nbsp;</th>". NEWLINE;
		html_tbl_print_header( lang_get('test_name'),	TEST_NAME,		$order_by, $order_dir );
		html_tbl_print_header( lang_get('ba_owner'),	TEST_BA_OWNER,	$order_by, $order_dir );
		html_tbl_print_header( lang_get('qa_owner'),	TEST_QA_OWNER,	$order_by, $order_dir );
		html_tbl_print_header( lang_get('tester'),      TEST_TESTER,	$order_by, $order_dir );
		html_tbl_print_header( lang_get('testtype'),	TEST_TESTTYPE,	$order_by, $order_dir );
		html_tbl_print_header( lang_get('area_tested'),	TEST_AREA_TESTED,$order_by, $order_dir );
		html_tbl_print_header( lang_get('test_status'),	TEST_TS_ASSOC_STATUS,$order_by, $order_dir );
		html_tbl_print_header( lang_get('test_doc') );
		html_tbl_print_header( lang_get('results') );
		html_tbl_print_header( lang_get('tester') );
		html_tbl_print_header( lang_get('info') );
		html_tbl_print_header( lang_get('bug') );
		print"</tr>". NEWLINE;

		foreach( $rows as $row ) {

			$test_id                = $row[TEST_ID];
			$display_test_id		= util_pad_id( $test_id );
			$test_name              = $row[TEST_NAME];
			$manual                 = $row[TEST_MANUAL];
			$automated              = $row[TEST_AUTOMATED];
			$auto_pass              = $row[TEST_AUTO_PASS];
			$ba_owner               = $row[TEST_BA_OWNER];
			$qa_owner               = $row[TEST_QA_OWNER];
			$tester	                = $row[TEST_TESTER];
			$test_type              = $row[TEST_TESTTYPE];
			$area_tested            = $row[TEST_AREA_TESTED];
			$priority               = $row[TEST_PRIORITY];
			$test_ts_assoc_id		= $row[TEST_TS_ASSOC_ID];
			$assigned_to            = $row[TEST_TS_ASSOC_ASSIGNED_TO];
			$comments               = $row[TEST_TS_ASSOC_COMMENTS];
			$testset_assoc_status   = $row[TEST_TS_ASSOC_STATUS];

			# Build list of records
			if( empty($records) ) {
				$records = "$test_id => '$testset_assoc_status'";
			} else {
				$records .= ", $test_id => '$testset_assoc_status'";
			}

			$row_style = html_tbl_alternate_bgcolor( $row_style );
			print"<tr class='$row_style'>". NEWLINE;
			if( session_records_ischecked("testset_copy", $test_id, $testset_assoc_status) ) {
				print"<td><input type='checkbox' name='row_$test_id' value='$testset_assoc_status' checked></td>". NEWLINE;
			} else {
				print"<td><input type='checkbox' name='row_$test_id' value='$testset_assoc_status'></td>". NEWLINE;
			}
			print"<td align='center'>$display_test_id</td>". NEWLINE;
			print"<td class='left' nowrap>".html_print_testtype_icon($manual, $automated)."</td>". NEWLINE;
			print"<td class='left' nowrap>$test_name</td>". NEWLINE;
			print"<td class='left'>$ba_owner</td>". NEWLINE;
			print"<td class='left'>$qa_owner</td>". NEWLINE;
			print"<td class='left'>$tester</td>". NEWLINE;
			print"<td class='left' nowrap>$test_type</td>". NEWLINE;
			print"<td class='left' nowrap>$area_tested</td>". NEWLINE;
			print"<td class='left' nowrap>$testset_assoc_status</td>". NEWLINE;
			print"<td class='center'><a href='$test_page?test_id=$test_id' target='_blank'>". lang_get('docs_link') ."</a></td>". NEWLINE;
			print"<td class='center'><a href='$results_test_run_page?test_id=$test_id&amp;testset_menu_testset_id=$_GET[testset_menu_testset_id]'>". lang_get('results_link') ."</a></td>". NEWLINE;
			print"<td class='left'>$assigned_to</td>". NEWLINE;
			# -------- Comment Icon ----------
			if( !empty($comments) ) {
				print"<td class='center'><img src='images/info.gif' title='$comments'></td>". NEWLINE;
			} else {
				print"<td>&nbsp;</td>". NEWLINE;
			}
			# ------- Test Status Icons ---------
			if( $testset_assoc_status == "Passed" ) {
				print"<td class='center'><img src='images/pass.gif' alt=Pass></td>". NEWLINE;
			} elseif( $testset_assoc_status == "Failed") {
				print"<td class='center'><img src='images/fail.gif' alt=Fail></td>". NEWLINE;
			} else {
				print"<td>&nbsp;</td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}
		print"</table>". NEWLINE;
		print"</div>". NEWLINE;

		if( session_use_javascript() ) {
			//print"<input type=checkbox name=all_tests value=all onClick=checkall('tests_form', this.form.all_tests.checked)\">Select All&nbsp;&nbsp;";
			print"<input id=select_all type=checkbox onClick='checkAll( this )'>". NEWLINE;
			print"<label for=select_all>".lang_get("select_all")."</label>". NEWLINE;
		}

		################################################################################

		print"<br>". NEWLINE;
		print"<div align=center>". NEWLINE;
		print"<input type='submit' name=submit_button value='".lang_get("copy")."'>". NEWLINE;
		print"</div>". NEWLINE;
	} else {
		html_no_records_found_message( lang_get("no_tests") );
	}

	print"<input type=hidden name=records value=\"$records\">". NEWLINE;
	print"<input type='hidden' name='record_groups' value=\"$select_group\">". NEWLINE;
	print"</form>". NEWLINE;

# display all test sets if the user has selected a build id
} elseif( isset( $_GET['build_id'] ) && $_GET['build_id'] != 'all') {

}

html_print_footer();

# ---------------------------------------------------------------------
# $Log: testset_copy_page.php,v $
# Revision 1.5  2008/07/18 07:43:36  peter_thal
# fixed search filter bug in some testset php pages
#
# Revision 1.4  2007/02/03 10:26:19  gth2
# no message
#
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