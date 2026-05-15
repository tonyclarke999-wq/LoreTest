<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Update Test Result Action Page
#
# $RCSfile: results_update_test_result_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();


$page               = basename(__FILE__);
$redirect_page		= "results_page.php";

$project_id	= session_get_project_id();

$s_results		= session_get_properties("results");
$release_id 	= $s_results['release_id'];
$build_id		= $s_results['build_id'];
$testset_id		= $s_results['testset_id'];
$test_id		= $s_results['test_id'];

$s_user			= session_get_user_properties();
$username		= $s_user['username'];
$comments		= util_clean_post_vars('test_result_comments');
$root_cause		= util_clean_post_vars('root_cause');
$status			= util_clean_post_vars('test_result_status');

if( $status == "Passed" ) {
	$finished = '1';
}
else {
	$finished = '0';
}

results_update_test_result( $testset_id, $test_id, $username, $status, $root_cause, $finished, $comments );

############################################################################
# EMAIL NOTIFICATION
############################################################################
$send_message = false;
if( !empty($_POST['email_users']) ) {

	$email_to = $_POST['email_users'];
	
	for( $i=0; $i<sizeof($email_to); $i++ ) {
	
		if( $email_to[$i] != "" ) {
			$send_message = true;
		}
	 }
}


if( $send_message ) {
	results_email($project_id, $release_id, $build_id, $testset_id, $test_id, $email_to, "update_test_result");
}

html_print_operation_successful('update_test_result_page', $redirect_page);

# ---------------------------------------------------------------------
# $Log: results_update_test_result_action.php,v $
# Revision 1.2  2007/02/06 03:27:56  gth2
# correct email problem when updating test results - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>