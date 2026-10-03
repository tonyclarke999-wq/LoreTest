<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Continue Manual Test Action Page
#
# $RCSfile: results_continue_manual_test_action.php,v $  $Revision: 1.6 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page						= basename(__FILE__);

$s_results		= session_get_properties("results");
$release_id 	= $s_results['release_id'];
$build_id		= $s_results['build_id'];
$testset_id		= $s_results['testset_id'];
$test_run_id	= $_POST['test_run_id'];
$test_id		= $s_results['test_id'];

$continue_test_page			= "results_continue_manual_test_page.php?test_id=$test_id&testset_id=$testset_id&test_run_id=$test_run_id";
$redirect_page				= "results_view_verifications_page.php?test_id=$test_id&testset_id=$testset_id&test_run_id=$test_run_id";
$redirect_page_file_upload 	= "$page?test_id=$test_id&testset_id=$testset_id";

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$project_id	= session_get_project_id();

session_validate_form_set($_POST, $continue_test_page);

$s_user				= session_get_user_properties();
$username			= $s_user['username'];
$test_name			= test_get_name( $test_id );
//$test_run_id		= $_POST["test_run_id"];
$time_finished		= date("Y-m-d H:i:s");
$time_started		= '';
$comments			= session_validate_form_get_field('test_run_comments');
$test_status		= session_validate_form_get_field('test_run_status_required');
$root_cause			= session_validate_form_get_field('root_cause');
$environment		= session_validate_form_get_field('environment');
$duration			= session_validate_form_get_field('duration');
$os					= session_validate_form_get_field('os');

//print_r($_POST);

if( $duration != '' ) {
	$time_started = results_caculate_time_started( $duration );
}

# ------------------------------------------------
# FILE UPLOAD
# ------------------------------------------------
# NEED TO FIND OUT THE LINK FIELD IS FOR.  SPEAK TO RT
$project_properties	= session_get_project_properties();
$upload_path		= $project_properties['test_run_upload_path'];

if( $_FILES['upload_file']['size'] != '0' && is_uploaded_file($_FILES['upload_file']['tmp_name']) ) {
#if( isset($_FILES['upload_file']['name']) && $_FILES['upload_file']['name'] != '' && is_uploaded_file($_FILES['upload_file']['tmp_name']) ) {

//print"........";

	file_add_test_run_doc(	$_FILES['upload_file']['tmp_name'],
							$_FILES['upload_file']['name'],
							$test_run_id,
							$username,
							$comments,
							$redirect_page_file_upload);

}
else{
	//print"Error uploading file. Either the file size = 0 or the file is not a valid file";
	# WE NEED TO PRINT AN ERROR MESSAGE WHEN THIS DOESN'T WORK.
}

# ------------------------------------------------
# TEST SUITE RESULTS
# ------------------------------------------------
results_edit_testsuite_result( $test_run_id,
								 $testset_id,
								 $test_id,
								 $test_name,
								 $test_status,
								 $username,
								 $time_started,
								 $time_finished,
								 $comments,
								 $root_cause,
								 $environment,
								 $os);

# PREPARE TO UPDATE THE VERIFY_RESULTS TABLE
$vr_tbl				= VERIFY_RESULTS_TBL;
$f_vr_id			= VERIFY_RESULTS_ID;
$f_run_id			= VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
$f_timestamp		= VERIFY_RESULTS_LOG_TIME_STAMP;
$f_actual			= VERIFY_RESULTS_ACTUAL_RESULT;
$f_status			= VERIFY_RESULTS_TEST_STATUS;

# UPDATE VERIFY_RESULTS
foreach(results_get_verify_results_detail( $test_run_id ) as $row_test_step) {

	$verify_results_id  = $row_test_step[VERIFY_RESULTS_ID];

	$q = "	UPDATE $vr_tbl
			SET
				$f_timestamp = '$time_finished',
				$f_actual = '".session_validate_form_get_field("actual_result_$verify_results_id")."',
				$f_status = '".session_validate_form_get_field( "step_status_$verify_results_id" )."'
			WHERE
				$f_run_id = '$test_run_id'
			AND 
				$f_vr_id = '$verify_results_id'";
	#print"$q<br>";
	db_query( $db, $q );
}

# ------------------------------------------------
# UPDATE TEST RUN STATUS
# ------------------------------------------------
if( $test_status == "Passed" ) {
	$finished = '1';
}
else {
	$finished = '0';
}

#results_update_test_run( $test_run_id, $username, session_validate_form_get_field("test_run_status"), $finished, session_validate_form_get_field("comments"), $root_cause );

results_update_test_result( $testset_id, $test_id, $username, $test_status, $root_cause, $finished, $comments );


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

session_validate_form_reset();

html_print_operation_successful( 'run_manual_test_page', $redirect_page );

# ---------------------------------------------------------------------
# $Log: results_continue_manual_test_action.php,v $
# Revision 1.6  2007/02/06 03:27:56  gth2
# correct email problem when updating test results - gth
#
# Revision 1.5  2007/02/03 11:58:12  gth2
# no message
#
# Revision 1.4  2006/02/24 11:33:08  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.3  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.2  2006/01/08 22:00:19  gth2
# bug fixes.  missing some variables - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>