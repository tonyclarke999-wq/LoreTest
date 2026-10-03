<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Update Test Run Page
#
# $RCSfile: results_update_test_run_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$page                   = basename(__FILE__);
$action_page			= "results_update_test_run_action.php". NEWLINE;

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('update_test_result_page') );
html_page_header( $db, $project_name );
html_print_menu();

print"<br>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<form method=post action=$action_page>". NEWLINE;

error_report_check( $_GET );

$s_results = session_set_properties("results", $_GET);
$test_run_id = $s_results['test_run_id'];

$row_test_detail = results_query_test_run_details( $test_run_id );

if ( !empty($row_test_detail) ) {

	$test_id        = $row_test_detail[TEST_RESULTS_TEMPEST_TEST_ID];
	//$testset_id	= $row_test_detail[TEST_RESULTS_TEST_SET_ID];
	$test_name      = $row_test_detail[TEST_RESULTS_TEST_SUITE];
	$status			= $row_test_detail[TEST_RESULTS_TEST_STATUS];
	$root_cause		= $row_test_detail[TEST_RESULTS_ROOT_CAUSE];
	$assigned_to	= $row_test_detail[TEST_RESULTS_ASSIGNED_TO];
	$comments		= $row_test_detail[TEST_RESULTS_COMMENTS];

	print"<table class=width60>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;

	print"<table class=inner rules=none border=0>". NEWLINE;

    	print"<tr>". NEWLINE;
    	print"<td class=form-header-l colspan=2>". lang_get('update_test_run') ."</td>". NEWLINE;
    	print"</tr>". NEWLINE;

		util_add_spacer();

    	print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r>". lang_get('test_name') ."</td>". NEWLINE;
    	print"<td class=left>$test_name</td>". NEWLINE;

    	print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r>". lang_get('test_comments') ."</td>". NEWLINE;
    	print"<td class=left>". NEWLINE;
            print"<textarea rows='5' cols='40' name='test_run_comments'>$comments</textarea>". NEWLINE;
    	print"</td>". NEWLINE;
    	print"</tr>". NEWLINE;

		# Test Passed By
		# Shouldn't we just get this from the session on the action page?
		# This wouild allow a user to enter another persons name.  Probably not good
		/*
		print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r>". lang_get('test_run_passed_by') ."</td>". NEWLINE;
    	print"<td class=left>". NEWLINE;
        print"<select name='test_run_assigned_to' size=1>". NEWLINE;
		$selected_value = $assigned_to;
        	$assign_to_users = user_get_usernames_by_project($project_id, $blank=true);
        	html_print_list_box_from_array( $assign_to_users, $selected_value);
        print"</select>". NEWLINE;
    	print "</td>". NEWLINE;
    	print"</tr>" ;
		*/

		# Test Run Status
		print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r>". lang_get('test_run_status') ."</td>". NEWLINE;
    	print"<td class=left>". NEWLINE;
        print"<select name='test_run_status' size=1>". NEWLINE;
		$selected_value = $status;
        	$test_run_status = results_get_teststatus_by_project( $project_id );
        	html_print_list_box_from_array( $test_run_status, $selected_value);
        print"</select>". NEWLINE;
    	print"</td>". NEWLINE;
    	print"</tr>" ;

		# Root Cause for Failure
    	print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('root_cause') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
		print"<select name='root_cause' size=1>". NEWLINE;
		$selected_value = $root_cause;
			$root_causes = results_get_root_cause_values();
			html_print_list_box_from_array( $root_causes, $selected_value);
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
    	print"</tr>". NEWLINE;

		# E-mail user
		print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r>". lang_get('email_test_run_status') ."</td>". NEWLINE;
    	print"<td class=left>". NEWLINE;
        print"<select name='email_users[]' multiple size='5'>". NEWLINE;
			$selected_value = user_get_email_by_username($assigned_to);

			$users 			= user_get_details_all($project_id);
			$email_users	= array();
			foreach($users as $user) {

				$email_users[$user[USER_EMAIL]] = $user[USER_UNAME];
			}
			$email_users[""] = "";

        	html_print_list_box_from_key_array( $email_users, $selected_value);
        print"</select>". NEWLINE;
    	print "</td>". NEWLINE;
    	print"</tr>" ;

		#print"<tr><td><input type='hidden' name='test_id' value='$test_id'></td></tr>". NEWLINE;

		util_add_spacer();

		print"<tr><td class=center colspan=2><input type=submit name='save' value='". lang_get( 'update' ) ."'><br/><br/></td>". NEWLINE;

    	print"</table>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

}

print"</form>". NEWLINE;

print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: results_update_test_run_page.php,v $
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