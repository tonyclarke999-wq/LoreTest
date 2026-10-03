<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Run Manual Test Page
#
# $RCSfile: results_run_manual_test_page.php,v $  $Revision: 1.10 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_button']) ) {

	require_once("results_run_manual_test_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();


$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$page                   = basename(__FILE__);
$s_results				= session_set_properties("results", $_GET);
$testset_id				= $s_results['testset_id'];
$test_id				= $s_results['test_id'];

$redirect_url			= $page ."?test_id=". $test_id ."&amp;testset_id=". $testset_id;
$results_page			= 'results_page.php';
$form_name				= 'run_test';
$row_style              = '';
$time					= results_get_time_started();

$order_by		= TEST_STEP_NO;
$order_dir		= "ASC";
$page_number	= 1;

$s_user_properties		= session_get_user_properties();
$s_user_id				= $s_user_properties['user_id'];
$s_delete_rights		= $s_user_properties['delete_rights'];

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);



$project_manager		= user_has_rights( $project_id, $s_user_id, MANAGER );
$user_has_delete_rights	= ($s_delete_rights==="Y" || $project_manager);

$test_name = test_get_name( $test_id );

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('run_manual_test_page') );
html_page_header( $db, $project_name );
html_print_menu();
html_test_results_menu( $db, $results_page, $project_id, session_get_properties("results", $_GET) );

error_report_check( $_GET );


# RUN TEST FORM
print"<form method=post enctype='multipart/form-data' name='run_manual_test' action='$page?test_id=$test_id&amp;testset_id=$testset_id'>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<table class=inner>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-header-l colspan=2>". lang_get('run_test') ." - $test_name</td>". NEWLINE;
print"</tr>". NEWLINE;


# TEST COMMENTS
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('test_comments') ."</td>". NEWLINE;
print"<td class='form-data-l'>". NEWLINE;
	print"<textarea rows='5' cols='40' name='test_run_comments'>". session_validate_form_get_field("test_run_comments").	"</textarea>". NEWLINE;
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

	html_print_list_box_from_key_array( $list, session_validate_form_get_field("environment") );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>" ;


# OS
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('os') ."</td>". NEWLINE;
print"<td class='form-data-l'>". NEWLINE;
print"<select name='os' size=1>". NEWLINE;
	$os = results_get_os();
	html_print_list_box_from_array( $os, session_validate_form_get_field("os") );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# Test Run Status
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('test_run_status') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<select name='test_run_status_required' size=1>". NEWLINE;
	$test_run_status = results_get_teststatus_by_project( $project_id, $blank=true );
	html_print_list_box_from_array( $test_run_status, session_validate_form_get_field("test_run_status_required") );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# Root Cause for Failure
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('root_cause') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<select name='root_cause' size=1>". NEWLINE;
	$root_cause = results_get_root_cause_values();
	html_print_list_box_from_array( $root_cause, session_validate_form_get_field("root_cause") );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# EMAIL USER
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

# FILE UPLOAD
print"<tr>\n". NEWLINE;
print"<td><input type='hidden' name=MAX_FILE_SIZE  value='5000000'></td>\n". NEWLINE;
print"</tr>\n". NEWLINE;

print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('upload_file') ."</td>". NEWLINE;
print"<TD class='form-data-l'><input type='file' name='upload_file' value='". session_validate_form_get_field("upload_file")."' size='40'></td>". NEWLINE;
print"</tr>". NEWLINE;

# HIDDEN VARS
print"<tr><td><input type='hidden' name='test_id' value='$test_id'></td></tr>". NEWLINE;
print"<tr><td><input type='hidden' name='testset_id' value='$testset_id'></td></tr>". NEWLINE;

util_add_spacer();

# SUBMIT BUTTON
print"<tr><td class=center colspan=2><input type=submit name='submit_button' value='". lang_get( 'save_results' ) ."'></td>". NEWLINE;

#print"</form>". NEWLINE;
print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"<br>". NEWLINE;




$upload_rows = test_get_uploaded_documents($test_id);
print"<br>". NEWLINE;
print"<h4>Supporting Docs</h4>". NEWLINE;

		if( !empty($upload_rows) ) {
		
			print"<table class=width95>";
			print"<tr>";
			print"<td>";
				print"<table class=inner>";
				print"<tr>";
					print"<td class=grid-header-c>". lang_get('file_type') ."</td>";
					print"<td class=grid-header-c>". lang_get('file_name') ."</td>";
					print"<td class=grid-header-c>". lang_get('version') ."</td>";
					print"<td class=grid-header-c>". lang_get('view') ."</td>";
					print"<td class=grid-header-c>". lang_get('download') ."</td>";
					print"<td class=grid-header-c>". lang_get('show_history') ."</td>";
					print"<td class=grid-header-c>". lang_get('add_version') ."</td>";
					if($user_has_delete_rights){
						print"<td class=grid-header-c>". lang_get('delete') ."</td>";	
					}
					//print"<td class=grid-header-c>". lang_get('edit') ."</td>";
					print"<td class=grid-header-c>". lang_get('uploaded_by') ."</td>";
					print"<td class=grid-header-c>". lang_get('date_added') ."</td>";
					print"<td class=grid-header-c>". lang_get('info') ."</td>";
					//print"<td class=grid-header-c>". lang_get('delete_latest') ."</td>";
				print"</tr>";

				foreach($upload_rows as $upload_row) {

					$display_name  = $upload_row[MAN_TD_DISPLAY_NAME];
					$man_test_id   = $upload_row[MAN_TD_MANUAL_TEST_ID];
					$doc_detail_row= test_get_uploaded_document_detail($man_test_id);
					$filename      = $doc_detail_row[MAN_TD_VER_FILENAME];
					$comments      = $doc_detail_row[MAN_TEST_DOCS_VERS_COMMENTS];
					$time_stamp    = $doc_detail_row[MAN_TD_VER_TIME_STAMP];
					$uploaded_by   = $doc_detail_row[MAN_TD_VER_UPLOADED_BY];
					$version       = $doc_detail_row[MAN_TD_VER_VERSION];
					$doc_type      = $doc_detail_row[MAN_TEST_DOCS_VERS_MANUAL_DOC_TYPE_NAME];

					$fname = $project_properties['test_upload_path'] . $filename;

					print"<tr>";
						print"<td class=grid-data-c>".html_file_type( $filename )."</td>";
						print"<td class=grid-data-c>$display_name</td>";
						print"<td class=grid-data-c>$version</td>";
						print"<td class=grid-data-c>";
						print"<a href='$fname' target='new'>" . lang_get('view') . "</a>";
						print"</td>";
						print"<td class=grid-data-c>";
						print"<a href='download.php?upload_filename=$fname'>" . lang_get('download') . "</a>";
						print"</td>";
						print"<td class=grid-data-c>";
						print"<a href='test_doc_history_page.php?test_id=$test_id&mantestid=$man_test_id'>" . lang_get('show') . "</a>";
						print"</td>";
						print"<td class=grid-data-c>";
						print"<a href='test_add_doc_version_page.php?test_id=$test_id&manual_test_id=$man_test_id'>" . lang_get('add') . "</a>";
						print"</td>";
						if($user_has_delete_rights){
							print"<td class=grid-data-c>";
							print"<a href='test_delete_doc_action.php?test_id=$test_id&manual_test_id=$man_test_id'>" . lang_get('delete') . "</a>";
							print"</td>";
						}
						//print"<td class=grid-data-c>";
						//print"<a href='test_doc_edit_page.php?mantestid=$man_test_id'>" . lang_get('edit') . "</a>";
						//print"</td>";
						print"<td class=grid-data-c>$uploaded_by</td>";
						print"<td class=grid-data-c>$time_stamp</td>";
						print"<td class=grid-data-c>";

						  if($comments) {
							  print"<img src='". IMG_SRC . "/info.gif' title='$comments'>";
						  }
						  else {
							  print"&nbsp;";
						  }

						print"</td>";
						//print"<td class=grid-data-c>";
						//print"<a href='test_doc_delete_page.php?mantestid=$man_test_id'>" . lang_get('delete') . "</a>";
						//print"</td>";
					print"</tr>";
				}

				print"</table>";
			print"</td>";
			print"</tr>";
			print"</table>";
			print"<br>".NEWLINE;

		}
		else {
			print"<span class='print' <table class=width95><tr><td class=grid-header-c>" . lang_get('no_documentation') . "</td></tr></table><br>";
		}










$rows_test_steps = test_get_test_steps( $test_id, $page_number );

$num_test_steps = sizeof($rows_test_steps);

if( $num_test_steps != '0' ) {  # Display test steps if they exist


	print"<table class='width100' rules='cols'>". NEWLINE;
	print"<tr class='tbl_header'>". NEWLINE;
	html_tbl_print_header( lang_get('step_no') );
	html_tbl_print_header( lang_get('step_action') );
	html_tbl_print_header( lang_get('step_inputs') );
	html_tbl_print_header( lang_get('step_expected') );
	html_tbl_print_header( lang_get('actual_result') );
	html_tbl_print_header( lang_get('pass_fail') );
	print"</tr>". NEWLINE;

	$i = 0;
	$row_style = '';
	foreach($rows_test_steps as $row_test_step ) {

		$row_test_step_id   = $row_test_step[TEST_STEP_ID];
		$step_number     	= $row_test_step[TEST_STEP_NO];
		$step_action     	= $row_test_step[TEST_STEP_ACTION];
		$step_inputs		= $row_test_step[TEST_STEP_TEST_INPUTS];
		$step_expected   	= $row_test_step[TEST_STEP_EXPECTED];
		$info_step 			= $row_test_step[TEST_STEP_INFO_STEP];

		$info_step_class = "";
		if ($info_step=="Y") {
			$info_step_class = "class='test-step-info'";
		}

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		print"<td align=center><div $info_step_class>$step_number</div></TD>". NEWLINE;
		print"<td align=left><div $info_step_class>$step_action</div></TD>". NEWLINE;
		print"<td align=left><div $info_step_class>$step_inputs</div></TD>". NEWLINE;
		print"<td align=left><div $info_step_class>$step_expected</div></TD>". NEWLINE;
		
		# Only display the Actual Result field if this is not an Info step
		print"<td>". NEWLINE;
		if ($info_step != "Y") {
			$actual_result = "actual_result_". $row_test_step_id;
			print"<textarea name='$actual_result' rows='4' cols='30'>".session_validate_form_get_field($actual_result) ."</textarea>";
		}
		print"</td>".NEWLINE;
		
		# Only display the Pass/Fail status field if this is not an Info step
		print"<td>" . NEWLINE;
		if ($info_step != "Y") {
			$status_list = "step_status_". $row_test_step_id;
			print"<select name='$status_list'>";

			$list_box = array( "Pass", "Fail", "Info", "" );
			if($info_step=="Y") {
				$info_step="Info";
			} else {
				$info_step="";
			}

			$list_box_selected = session_validate_form_get_field( "$status_list", $info_step );
			html_print_list_box_from_array(	$list_box, $list_box_selected );

			print"</select>". NEWLINE;
		}
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		$i++;

	}
	print"</table>". NEWLINE;
}

print"</div>". NEWLINE;


print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: results_run_manual_test_page.php,v $
# Revision 1.10  2008/07/24 07:41:46  peter_thal
# added supporting docs table in test run page
#
# Revision 1.9  2008/04/29 04:55:09  cryobean
# ui bugfixes from bruce
#
# Revision 1.8  2007/02/25 23:17:39  gth2
# fixing bugs for release 1.6.1 - gth
#
# Revision 1.7  2007/02/06 03:27:56  gth2
# correct email problem when updating test results - gth
#
# Revision 1.6  2007/02/05 03:57:47  gth2
# no message
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