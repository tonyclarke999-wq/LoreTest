<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Continue Manual Test Page
#
# $RCSfile: results_continue_manual_test_page.php,v $  $Revision: 1.8 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_button']) ) {

	require_once("results_continue_manual_test_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

/*
if( sizeof($_POST) ) {
	session_validate_form_set($_POST);
} else {
	session_validate_form_reset();
}
*/

$page                   = basename(__FILE__);
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$s_results 				= session_set_properties( "results", $_GET );
$testset_id 			= $s_results['testset_id'];
$test_id 				= $s_results['test_id'];
$test_run_id 			= $s_results['test_run_id'];

$redirect_url			= $page ."?test_id=$test_id&amp;testset_id=$testset_id";
$results_page			= 'results_page.php';
$form_name				= 'run_test';
$row_style              = '';
$time					= results_get_time_started();

$order_by		= TEST_STEP_NO;
$order_dir		= "ASC";
$page_number	= 1;

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);

/*
$test_run_options		= session_set_display_options( 'test_run_details', $_POST );
$s_page_number			= $test_run_options['page_number'];
*/

#error_report_check( $_GET );
/*

if( isset($_GET['testset_id']) && isset($_GET['test_id']) ) {
    $testset_id = $_GET['testset_id'];
    $test_id = $_GET['test_id'];
} else {   # coming from redirect etc, get stored testset_id and test_run_id
    $s_results = session_get_properties("results");
    $testset_id = $s_results['testset_id'];
    $test_id = $s_results['test_id'];
}
*/

$test_name = test_get_name( $test_id );

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('run_manual_test_page') );
html_page_header( $db, $project_name );
html_print_menu();
html_test_results_menu( $db, $results_page, $project_id, session_get_properties("results", $_GET) );

error_report_check( $_GET );

$test_results_details = results_get_test_results_detail( $test_run_id );

$row_test_name			= $test_results_details[TEST_RESULTS_TEST_SUITE];
$row_test_id			= $test_results_details[TEST_RESULTS_TEMPEST_TEST_ID];
$row_time_started		= $test_results_details[TEST_RESULTS_TIME_STARTED];
$row_time_finished		= $test_results_details[TEST_RESULTS_TIME_FINISHED];
$row_finished			= $test_results_details[TEST_RESULTS_FINISHED];
$row_test_run_status	= $test_results_details[TEST_RESULTS_TEST_STATUS];
$row_comments			= $test_results_details[TEST_RESULTS_COMMENTS];
$row_os					= $test_results_details[TEST_RESULTS_OS];
$row_env				= $test_results_details[TEST_RESULTS_ENVIRONMENT];
$row_root_cause			= $test_results_details[TEST_RESULTS_ROOT_CAUSE];
$row_cvs				= $test_results_details[TEST_RESULTS_CVS_VERSION];

print"<form method=post enctype='multipart/form-data' name='run_manual_test' action='$page?test_id=$test_id&amp;testset_id=$testset_id&amp;test_run_id=$test_run_id'>". NEWLINE;
print"<input type=hidden name=test_run_id value='$test_run_id'>";

print"<div align=center>". NEWLINE;

print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<table class=inner>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-header-l colspan=2>". lang_get('run_test') ." - $test_name</td>". NEWLINE;
print"</tr>". NEWLINE;

#util_add_spacer();
/*
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('test_name') ."</td>". NEWLINE;
print"<td class=left>$test_name</td>". NEWLINE;
print "</td>". NEWLINE;
*/

print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('test_comments') ."</td>". NEWLINE;
print"<td class='form-data-l'>". NEWLINE;
	print"<textarea rows='5' cols='40' name='test_run_comments'>". session_validate_form_get_field("test_run_comments", $row_comments).	"</textarea>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# Duration
print"<tr>". NEWLINE;
#print"<td class='form-lbl-r'>". lang_get('duration') ."</td>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('time_to_run_test') ."</td>". NEWLINE;
print"<td class='form-data-l'><input type='text' size='3' name='duration' value='".session_validate_form_get_field("duration")."'>&nbsp; ". lang_get('in_minutes') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

# Environment
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('environment') ."</td>". NEWLINE;
print"<td class='form-data-l'>". NEWLINE;
print"<select name='environment' size=1>". NEWLINE;
	$rows_environments = project_get_environments( $project_id );

	foreach($rows_environments as $row_environment) {

		$list[$row_environment[ENVIRONMENT_NAME]] = $row_environment[ENVIRONMENT_NAME];
	}
	$list[""] = "";

	html_print_list_box_from_key_array( $list, session_validate_form_get_field("environment", $row_env) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>" ;


# OS
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('os') ."</td>". NEWLINE;
print"<td class='form-data-l'>". NEWLINE;
print"<select name='os' size=1>". NEWLINE;
	$os = results_get_os();
	html_print_list_box_from_array( $os, session_validate_form_get_field("os", $row_os) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# Test Run Status
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('test_run_status') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<select name='test_run_status_required' size=1>". NEWLINE;
	$test_run_status = results_get_teststatus_by_project( $project_id, $blank=true );
	html_print_list_box_from_array( $test_run_status, session_validate_form_get_field("test_run_status_required", $row_test_run_status) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# Root Cause for Failure
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('root_cause') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<select name='root_cause' size=1>". NEWLINE;
	$root_cause = results_get_root_cause_values();
	html_print_list_box_from_array( $root_cause, session_validate_form_get_field("root_cause", $row_root_cause) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# E-mail user
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('email_test_run_status') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<select name='email_users[]' multiple size='3'>". NEWLINE;
	$users 			= user_get_details_all($project_id);
	$email_users	= array();
	foreach($users as $user) {

		$email_users[$user[USER_EMAIL]] = $user[USER_UNAME];
	}
	//$email_users[] = "";

	html_print_list_box_from_key_array( $email_users, session_validate_form_get_field("email_users") );
print"</select>". NEWLINE;
print "</td>". NEWLINE;
print"</tr>". NEWLINE;

# File Upload
print"<tr>\n". NEWLINE;
print"<td><input type='hidden' name=MAX_FILE_SIZE  value='5000000'></td>\n". NEWLINE;
print"</tr>\n". NEWLINE;

print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('upload_file') ."</td>". NEWLINE;
print"<TD class='form-data-l'><input type='file' name='upload_file' value='". session_validate_form_get_field("upload_file")."' size='40'></td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr><td><input type='hidden' name='test_id' value='$test_id'></td></tr>". NEWLINE;
print"<tr><td><input type='hidden' name='testset_id' value='$testset_id'></td></tr>". NEWLINE;

util_add_spacer();

print"<tr><td class=center colspan=2><input type=submit name='submit_button' value='". lang_get( 'save_results' ) ."'></td>". NEWLINE;

#print"</form>". NEWLINE;
print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"<br>". NEWLINE;


# Test Steps
/*print"<form method=post name='test_run' action='$redirect_url'>". NEWLINE;
print"<table class=hide100>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
$rows_test_steps = test_get_test_steps( $test_id, $s_page_number );
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
//print"</form>". NEWLINE; */

$rows_test_steps = results_get_verify_results_detail( $test_run_id );

$num_test_steps = sizeof($rows_test_steps);

if( $num_test_steps != '0' ) {  # Display test steps if they exist


	print"<table class='width100' rules='cols'>". NEWLINE;
	print"<tr class='tbl_header'>". NEWLINE;
	html_tbl_print_header( lang_get('step_no') );
	html_tbl_print_header( lang_get('step_action') );
	html_tbl_print_header( lang_get('step_expected') );
	html_tbl_print_header( lang_get('actual_result') );
	html_tbl_print_header( lang_get('pass_fail') );
	print"</tr>". NEWLINE;

	$i = 0;
	$row_style = '';
	foreach($rows_test_steps as $row_test_step ) {

		$verify_results_id  = $row_test_step[VERIFY_RESULTS_ID];
		$step_number     	= $row_test_step[VERIFY_RESULTS_VAL_ID];
		$step_action     	= $row_test_step[VERIFY_RESULTS_ACTION];
		//$step_input			= $row_test_step[VERIFY_RESULT_TEST_INPUTS];
		$step_expected   	= $row_test_step[VERIFY_RESULTS_EXPECTED_RESULT];
		$step_actual	   	= $row_test_step[VERIFY_RESULTS_ACTUAL_RESULT];
		$test_status		= $row_test_step[VERIFY_RESULTS_TEST_STATUS];

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		print"<td class='tbl-c' valign=top>$step_number</TD>". NEWLINE;
		print"<td class='tbl-l' valign=top>$step_action</TD>". NEWLINE;
		//print"<td class='tbl-l' valign=top>$step_input</TD>". NEWLINE;
		print"<td class='tbl-l' valign=top>$step_expected</TD>". NEWLINE;
		print"<td><textarea name='actual_result_$verify_results_id' rows='4' cols='30'>".session_validate_form_get_field("actual_result_$verify_results_id", $step_actual)."</textarea></td>". NEWLINE;
		print"<td>". NEWLINE;
		print"<select name='step_status_$verify_results_id'>";

			$list_box = array("Pass", "Fail", "Info", "");

			$list_box_selected 	= session_validate_form_get_field( "step_status_$verify_results_id", $test_status );
			html_print_list_box_from_array(	$list_box,
											$list_box_selected );

		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		print"</tr>". NEWLINE;

//		print"<td><input type='hidden' name='step_number[{$i}]' value='$step_number'></td>". NEWLINE;
//		print"<td><input type='hidden' name='step_action[{$i}]' value='$step_action'></td>". NEWLINE;
//		print"<td><input type='hidden' name='step_expected[{$i}]' value='$step_expected'></td>". NEWLINE;

		$i++;

	}
	print"</table>". NEWLINE;
}

print"</div>". NEWLINE;


print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: results_continue_manual_test_page.php,v $
# Revision 1.8  2007/03/14 17:45:53  gth2
# removing code that passes varables by reference - gth
#
# Revision 1.7  2007/02/25 23:17:39  gth2
# fixing bugs for release 1.6.1 - gth
#
# Revision 1.6  2007/02/06 03:27:56  gth2
# correct email problem when updating test results - gth
#
# Revision 1.5  2007/02/03 11:58:12  gth2
# no message
#
# Revision 1.4  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2006/01/08 22:00:19  gth2
# bug fixes.  missing some variables - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>