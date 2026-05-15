<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Update Test Run Action Page
#
# $RCSfile: results_update_test_run_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                = basename(__FILE__);

$s_results			= session_get_properties('results', $_POST);
$s_user				= session_get_user_properties();
$email_to 			= user_get_email_by_username( $email_users );

session_validate_form_set($_POST);

$test_run_id		= $s_results['test_run_id'];
$testset_id			= $s_results['testset_id'];
$test_id			= $s_results['test_id'];

$redirect_page		= "results_test_run_page.php?test_id=$test_id&testset_id=$testset_id";

$username			= $s_user['username'];

$comments		= session_validate_form_get_field('test_run_comments');
$email_comments = $_POST['test_run_comments'];  # don't escape characters when sending e-mail
$status			= $_POST['test_run_status'];
$root_cause		= $_POST['root_cause'];
$email_users	= $_POST['email_users'];

/*
print"comments = $comments<br>";
print"status = $status<br>";
print"root_cause = $root_cause<br>";
print"email to = $email_to<br>";
*/


if( $status == "Passed" ) {
	$finished = '1';
}
else {
	$finished = '0';
}

# update record
results_update_test_run( $test_run_id, $username, $status, $finished, $comments, $root_cause );


# send e-mail if user has selected users
if( isset( $email_to ) && $email_to != '' ) {

	# get release, build, and testset names
	#$s_results			= session_get_properties("results");
	$project_properties		= session_get_project_properties();
	$project_name			= $project_properties['project_name'];
	$project_id				= $project_properties['project_id'];

	$release_id = $s_results['release_id'];
	$build_id	= $s_results['build_id'];


	$release_name = admin_get_release_name( $release_id );
	$build_name	  = admin_get_build_name( $build_id );
	$testset_name = admin_get_testset_name( $testset_id );
	$test_name	  = test_get_name( $test_id );

	$email_from		= $s_user['email'];
	$email_to		= $email_to;
	$subject		= "Test Run Notification - $test_name";
	$message		= "Project Name: $project_name\r". NEWLINE;
	$message		.= "Release Name: $release_name\r". NEWLINE;
	$message		.= "Build Name: $build_name\r". NEWLINE;
	$message		.= "TestSet Name: $testset_name\r\n\r". NEWLINE;
	$message		.= "Test Name: $test_name\r". NEWLINE;
	$message		.= "Status: $status\r". NEWLINE;
	if( isset( $root_cause ) && $root_cause != '' ) {
		$message		.= "Root Cause: $root_cause\r". NEWLINE;
	}
	$message		.= "Comments: $email_comments\r". NEWLINE;

	$message		.= "\r\n\r\nPlease log into Tempest, select the appropriate project, click here to view the test results: \r". NEWLINE;
	$message		.= $tempest_url ."login.php?project_id=$project_id&page=results_test_run_page.php&release_id=$release_id&build_id=$build_id&testset_id=$testset_id&test_id=$test_id";

	#print"message = $message<br>";

	$headers = "From: ". $email_from ."\r". NEWLINE;

	email_send( $email_to, $subject, $message, $headers='' );

}

html_print_operation_successful('update_test_result', $redirect_page);

# ---------------------------------------------------------------------
# $Log: results_update_test_run_action.php,v $
# Revision 1.2  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>