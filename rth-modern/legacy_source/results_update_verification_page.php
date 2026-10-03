<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Update Verification Page
#
# $RCSfile: results_update_verification_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$page                   = basename(__FILE__);
$action_page			= "results_update_verification_action.php";

$s_results = session_set_properties("results", $_GET);

$testset_id 	= $s_results['testset_id'];
$test_id 		= $s_results['test_id'];
$test_run_id 	= $s_results['test_run_id'];
$verify_id 		= $s_results['verify_id'];

$test_name 		= test_get_name( $test_id );
$verification_detail = testset_query_verfication_details( $test_run_id, $verify_id );

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('update_verification') );
html_page_header( $db, $project_name );
html_print_menu();

print"<br>". NEWLINE;

print"<div align=center>\n". NEWLINE;
print"<form method=post action=$action_page>". NEWLINE;

error_report_check( $_GET );

if ( !empty($verification_detail) ) {

	$action				= $verification_detail[VERIFY_RESULTS_ACTION];
	$expected			= $verification_detail[VERIFY_RESULTS_EXPECTED_RESULT];
	$actual				= $verification_detail[VERIFY_RESULTS_ACTUAL_RESULT];
	$status				= $verification_detail[VERIFY_RESULTS_TEST_STATUS];
	$comments			= $verification_detail[VERIFY_RESULTS_COMMENT];
	$current_bug_id		= $verification_detail[VERIFY_RESULTS_DEFECT_ID];
	if( $current_bug_id == 0 ) {
		$padded_bug_id = '';
	}
	else {
		$padded_bug_id = util_pad_id( $current_bug_id );
	}

		
	print"<table class=width70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;

	print"<table class=inner rules=none border=0>". NEWLINE;

    	print"<tr>". NEWLINE;
    	print"<td class=form-header-l colspan=2>". lang_get('update_verification') ."</td>". NEWLINE;
    	print"</tr>". NEWLINE;

		util_add_spacer();

		# TEST NAME
    	print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r>". lang_get('test_name') ."</td>". NEWLINE;
    	print"<td class=left>$test_name</td>". NEWLINE;
    	print"</tr>". NEWLINE;

		util_add_spacer();

		# ACTION
    	print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r nowrap>". lang_get('action') ."</td>". NEWLINE;
    	print"<td class=left>$action</td>". NEWLINE;
    	print"</tr>". NEWLINE;

		util_add_spacer();

		# EXPECTED RESULT
		print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r nowrap>". lang_get('expected_result') ."</td>". NEWLINE;
    	print"<td class=left>$expected</td>". NEWLINE;
    	print"</tr>". NEWLINE;

		util_add_spacer();

		# ACTUAL RESULT
		print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r nowrap>". lang_get('actual_result') ."</td>". NEWLINE;
    	print"<td class=left>$actual</td>". NEWLINE;
    	print"</tr>". NEWLINE;

		util_add_spacer();

		# COMMENTS
		print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r nowrap>". lang_get('comment') ."</td>". NEWLINE;
    	print"<td class=left>". NEWLINE;
            print"<textarea rows='5' cols='50' name='verification_comments'>$comments</textarea>". NEWLINE;
    	print"</td>". NEWLINE;
    	print"</tr>". NEWLINE;

		# Store the current and new bug ID seperately so we can verify if it's changed
		print"<input type='hidden' name='current_bug_id' value='$current_bug_id'>";

		# bug ID
	   	print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('bug_id') ."</td>". NEWLINE;
		print"<td class=left><input type='text' size='20' maxlength='7' name='new_bug_id' value='$padded_bug_id' ></td>". NEWLINE;
    	print"</tr>". NEWLINE;

		# TEST STATUS
		print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r>". lang_get('test_status') ."</td>". NEWLINE;
    	print"<td class=left>". NEWLINE;
        print"<select name='verification_status' size=1>". NEWLINE;
			$verification_status = results_get_verification_status();
			$selected_value = $status;
        	html_print_list_box_from_array( $verification_status, $selected_value);
        print"</select>". NEWLINE;
    	print"</td>". NEWLINE;
    	print"</tr>" ;

	util_add_spacer();

	print"<tr><td class=center colspan=2><input type=submit name='save' value='". lang_get( 'update' ) ."'></td></tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
}

print"</form>". NEWLINE;
print"</div>\n". NEWLINE;

html_print_footer();


# ---------------------------------------------------------------------
# $Log: results_update_verification_page.php,v $
# Revision 1.3  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>