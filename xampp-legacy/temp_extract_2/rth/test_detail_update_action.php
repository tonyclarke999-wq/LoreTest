<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Detail Update Action Page
#
# $RCSfile: test_detail_update_action.php,v $  $Revision: 1.10 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_properties = session_get_project_properties();
$project_id			= $project_properties['project_id'];
$username			= session_get_username();

$s_test_details		= session_get_properties("test");
$test_id			= $s_test_details['test_id'];
$test_version_id	= $s_test_details['test_version_id'];
$redirect_page 		= "test_detail_page.php?test_id=$test_id&project_id=$project_id";
$current_test_name 	= test_get_name($test_id);

session_validate_form_set($_POST, "test_detail_update_page.php");

$testname 			= session_validate_form_get_field('testname_required');
$testpurpose 		= session_validate_form_get_field('testpurpose');
$testcomments 		= session_validate_form_get_field('testcomments');
$testpriority 		= session_validate_form_get_field('testpriority');
$teststatus 		= session_validate_form_get_field('teststatus');
$testareatested 	= session_validate_form_get_field('testareatested');
$testtype 			= session_validate_form_get_field('testtype');
$ba_owner 			= session_validate_form_get_field('ba_owner');
$qa_owner 			= session_validate_form_get_field('qa_owner');
$tester				= session_validate_form_get_field('tester');		
$assigned_to 		= session_validate_form_get_field('assigned_to');
$assigned_by 		= session_validate_form_get_field('assigned_by');
$dateassigned 		= session_validate_form_get_field('dateassigned');
$dateexpcomplete 	= session_validate_form_get_field('dateexpcomplete');
$dateactcomplete 	= session_validate_form_get_field('dateactcomplete');
//$datebasignoff 	= session_validate_form_get_field('datebasignoff');
//$signoff_by		= session_validate_form_get_field('signoff_by');
$autopass 			= session_validate_form_get_field('chk_autopass');
#is not needed from now on, because of new field "chk_automanu",which validates if step XOR auto is set to YES
//$steps 				= session_validate_form_get_field('chk_steps');
//$auto 				= session_validate_form_get_field('chk_auto');
$performance 		= session_validate_form_get_field('chk_performance');
$email 				= session_validate_form_get_field('email');
$duration 			= session_validate_form_get_field('test_duration');
$email_ba_owner		= session_validate_form_get_field('chk_email_ba_owner');
$email_qa_owner		= session_validate_form_get_field('chk_email_qa_owner');
$current_status		= session_validate_form_get_field('current_test_status');
$send_email			= false;

$automanu			= session_validate_form_get_field('chk_automanu');
$steps				='';
$auto				='';


if (!util_date_isvalid($dateassigned)    ||
    !util_date_isvalid($dateexpcomplete) ||
    !util_date_isvalid($dateactcomplete)) {

    error_report_show("test_detail_update_page.php", INVALID_DATE );
}
if (test_name_exists_with_id( $project_id, $testname, $test_id )) {

    error_report_show("test_detail_update_page.php", DUPLICATE_TESTNAME );
}

// set value of $steps XOR $auto to YES
if($automanu == 'man'){
	$steps = 'YES';
}else if($automanu == 'auto'){
	$auto = 'YES';
}


// UPDATE TEST
test_update_test($test_id, $test_version_id, $testname, $testpurpose, $testcomments, $testpriority, $teststatus,
                 $testareatested, $testtype,  $ba_owner, $qa_owner, $tester, $assigned_to, $assigned_by,
                 $dateassigned, $dateexpcomplete, $dateactcomplete, $duration, $autopass, $steps, $auto, $performance,
				 $email_ba_owner, $email_qa_owner);


# Email the ba owner or qa owner on status change
if( $current_status != $teststatus ) {

	
	$test_detail = test_get_detail( $test_id );
	
	if( ($email_ba_owner == 'Y') && ($test_detail[TEST_BA_OWNER] != '') && ($username != $test_detail[TEST_BA_OWNER]) ) {
		
		$send_email = true;	
		$owner_array = array($test_detail[TEST_BA_OWNER]);
	}

	if( ($email_qa_owner == 'Y') && ($test_detail[TEST_QA_OWNER] != '') && ($username != $test_detail[TEST_QA_OWNER]) ) {
		
		$send_email = true;	
		if( is_array($owner_array) ) {
			array_push($owner_array, $test_detail[TEST_QA_OWNER]);
		}
		else {
			$owner_array = array($test_detail[TEST_QA_OWNER]);
		}
	}
}


// Compose message and send email
if( $send_email ) {

	$recipients = user_get_email_by_username( $owner_array );
	test_compose_email($project_id, $test_id, $recipients, "status_change");
	
}


/*
if ($teststatus == 'Not Running' AND $assigned_to != '' AND $email=='YES') {

    $current_user_row = user_get_current_user_name();
    $assigned_to_row = user_get_name_by_username($assigned_to);

    $f_email     = USER_EMAIL;
    $f_firstname = USER_FNAME;
    $f_lastname  = USER_LNAME;

    $current_user_email = $current_user_row[$f_email];
    $current_user_firstname = $current_user_row[$f_firstname];
    $current_user_lastname = $current_user_row[$f_lastname];

    $assigned_user_email = $assigned_to_row[$f_email];
    $assigned_user_firstname = $assigned_to_row[$f_firstname];
    $assigned_user_lastname = $assigned_to_row[$f_lastname];

    $send_to = $current_user_email;

    if ($current_user_email != $assigned_user_email[$f_email]) {
        $send_to .= ', ' . $assigned_user_email;
    }

    #Build up the message and format to be sent
    $subject = $testname." is currently Not Running. ";

    $message = "$assigned_user_firstname $assigned_user_lastname, you have been assigned to fix ".
                $testname.". $current_user_firstname $current_user_lastname has stated \"$testcomments\".
                When you have resolved this issue, please inform $current_user_firstname. Thank you.";

    $headers = "From: support@bhtconsulting\r\n"."To: ".$assigned_user_email."\r\n"."Reply-To: ".$current_user_email."\r". NEWLINE;
    mail($send_to, $subject, $message, $headers);
}
*/


# validation succeeded
#html_redirect($redirect_page);

session_validate_form_reset();

html_print_operation_successful( "update_test_page", $redirect_page );

# ---------------------------------------------------------------------
# $Log: test_detail_update_action.php,v $
# Revision 1.10  2009/01/27 12:44:41  cryobean
# fixed problem during update of tests
#
# Revision 1.9  2008/08/08 11:22:08  peter_thal
# disabled update buildname to an existing buildname
# test_detail_update_action.php: changed redirect page on error
#
# Revision 1.8  2008/08/08 09:44:09  peter_thal
# test name validation added on test update page
#
# Revision 1.7  2008/08/04 08:57:07  peter_thal
# fixed bug with required fields
#
# Revision 1.6  2008/07/09 07:13:24  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.5  2008/07/01 11:44:47  peter_thal
# disabled possibility to select,store and filter both options automated and manual in RTH test category
#
# Revision 1.4  2007/11/19 13:11:53  cryobean
# added test_area, test_type and qa_owner as required fields for adding and updating tests
#
# Revision 1.3  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.2  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>
