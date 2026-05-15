<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Step Edit Page
#
# $RCSfile: test_step_edit_page.php,v $  $Revision: 1.5 $
# ---------------------------------------------------------------------

include"./api/include_api.php";

$page						= basename(__FILE__);
$update_action_page			= 'test_detail_page.php';
$test_step_edit_page		= 'test_step_edit_action.php';

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id 			= $project_properties['project_id'];
//$test_id				= $_GET['test_id'];
//$test_version_id		= $_GET['test_version_id'];
$test_step_id			= $_GET['test_step_id'];

$s_test_detail			= session_get_properties('test');
$test_id			    = $s_test_detail['test_id'];
//$test_version_id		= $s_test_detail['test_version_id'];




if (isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
}
else
    $is_validation_failure = false;

html_window_title();
auth_authenticate_user();

html_page_title($project_name ." - ". lang_get('test_step_edit_page') );
html_page_header( $db, $project_name );

html_print_menu();
test_menu_print ($page);
html_print_body();
print"<br><br>";

$ts_detail 	= test_get_test_step_detail( $test_step_id );

$step 		= $ts_detail[TEST_STEP_NO];
$action 	= $ts_detail[TEST_STEP_ACTION];
$expected 	= $ts_detail[TEST_STEP_EXPECTED];
$inputs		= $ts_detail[TEST_STEP_TEST_INPUTS];
$info_step 	= $ts_detail[TEST_STEP_INFO_STEP];
$checked	= "";

error_report_check( $_GET );

print"<div align='center'>";
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class='width100'>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;


		print"<form method=post name='test_step_add' action=$test_step_edit_page>". NEWLINE;
		print"<input type='hidden' name='test_step_id' value='$test_step_id'>";
		//print"<td><input type='hidden' name='test_version_id' value='$test_version_id'></td>";

		print"<table class='inner'>". NEWLINE;

		print"<tr>". NEWLINE;
			print"<td colspan=2 align=left class='form-lbl-l'>". lang_get('step') ." $step</td>". NEWLINE;
		print"</tr>";

		$test_step_tbl  = TEST_STEP_TBL;
		$f_test_id		= TEST_STEP_TEST_ID;
		$f_test_step_no	= TEST_STEP_NO;

		# MOVE STEP
		print"<tr>". NEWLINE;
			print"<td class='form-lbl-r'>". lang_get('move_step') ."</td>". NEWLINE;
			print"<td align=left>". NEWLINE;
			print"<select name='location'>". NEWLINE;
			print"<option value='none'></option>". NEWLINE;
			print"<option value='end'>At end of table</option>". NEWLINE;
				$q = "SELECT * FROM $test_step_tbl WHERE $f_test_id = '$test_id' ORDER BY $f_test_step_no";
				$rs = db_query( $db, $q );
				while($rw = db_fetch_row( $db, $rs ) ){
					print"<option value=$rw[$f_test_step_no]>After Step $rw[$f_test_step_no]</option>". NEWLINE;
				}
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>";

		# INFO STEP
		print"<tr>". NEWLINE;
			print"<td class='form-lbl-r'>". lang_get('info_step') ."</td>". NEWLINE;
			if( $info_step=="Y" ) { 
				$checked = "checked"; 
			}
			print"<td align=left><input type=checkbox name=info_step $checked></td>";
		print"</tr>";

		# ACTION
		$action = session_validate_form_get_field("step_action_required", $action, session_use_FCKeditor());
		print"<tr>";
			print"<td class='form-lbl-r'>". lang_get('step_action') ." <span class='required'>*</span></td>". NEWLINE;
			print"<td align=left>";
			html_FCKeditor("step_action_required", 600, 200, $action);
			print"</td>". NEWLINE;
		print"</tr>";

		# INPUTS
		$inputs = session_validate_form_get_field("step_inputs_required", $inputs, session_use_FCKeditor());
		print"<tr>";
			print"<td class='form-lbl-r'>". lang_get('test_inputs') ."</td>". NEWLINE;
			print"<td align=left>";
			html_FCKeditor("step_input", 600, 200, $inputs);
			print"</td>". NEWLINE;
		print"</tr>";

		# EXPTECTED RESULT
		$expected_result = session_validate_form_get_field("step_expected_required", $expected, session_use_FCKeditor());
		print"<tr>";
			print"<td class='form-lbl-r'>". lang_get('step_expected') ." <span class='required'>*</span></td>". NEWLINE;
			print"<td align=left>";
			html_FCKeditor("step_expected_required", 600, 200, $expected_result);
			print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		print"<tr>". NEWLINE;
			print"<td><input type='hidden' name='test_id' value='$test_id'></td>". NEWLINE;
		print"</tr>". NEWLINE;

		util_add_spacer();

		# SUBMIT BUTTON
		print"<tr>". NEWLINE;
		print"<td colspan='3' class=center><input type='submit' value='". lang_get('edit_step') ."'></td>". NEWLINE;
		print"</tr>". NEWLINE;

		util_add_spacer();

		print"</table>". NEWLINE;
		print"</form>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</div>";

# ---------------------------------------------------------------------
# $Log: test_step_edit_page.php,v $
# Revision 1.5  2007/03/14 17:23:44  gth2
# removing Test Input as a required field so that it's consistent witth the
# test detail page. - gth
#
# Revision 1.4  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/06/24 14:34:14  gth2
# updating changes lost with cvs problem.
#
# Revision 1.2  2005/12/05 15:35:29  gth2
# Info Step check box was not checked when editing a step - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>