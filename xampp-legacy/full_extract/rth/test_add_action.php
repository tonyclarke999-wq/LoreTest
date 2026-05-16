<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Add Action Page
#
# $RCSfile: test_add_action.php,v $  $Revision: 1.7 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

session_clear_form_values();
$page		   = basename(__FILE__);
$redirect_page = 'test_detail_page.php';
$test_add_page = "test_add_page.php";

# if $assoc_req==0, then the requirement is not a child of any other requirement
if( isset($_POST["assoc_req"]) ) {

	$assoc_req = $_POST["assoc_req"];
} else {

	$assoc_req = 0;
}

session_validate_form_set($_POST, $test_add_page);

$project_id 	= session_get_project_id();
$testname 		= session_validate_form_get_field('testname_required');
$testpurpose 	= session_validate_form_get_field('testpurpose');
$testcomments 	= session_validate_form_get_field('testcomments');
$testpriority 	= session_validate_form_get_field('testpriority');
$teststatus 	= session_validate_form_get_field('teststatus');
$testareatested = session_validate_form_get_field('testareatested');
$testtype 		= session_validate_form_get_field('testtype');
$ba_owner 		= session_validate_form_get_field('ba_owner');
$qa_owner 		= session_validate_form_get_field('qa_owner');
$tester			= session_validate_form_get_field('tester');
$assigned_to	= session_validate_form_get_field('assigned_to');
$assigned_by	= session_validate_form_get_field('assigned_by');
$dateassigned 	= session_validate_form_get_field('dateassigned');
$dateexpcomplete= session_validate_form_get_field('dateexpcomplete');
$dateactcomplete= session_validate_form_get_field('dateactcomplete');
$datebasignoff 	= session_validate_form_get_field('datebasignoff');
$duration	 	= session_validate_form_get_field('test_duration');
$email_ba 		= session_validate_form_get_field('chk_email_ba_owner');
$email_qa 		= session_validate_form_get_field('chk_email_qa_owner');
$autopass 		= session_validate_form_get_field('chk_autopass');
//$steps 		= session_validate_form_get_field('chk_steps'); 
//$auto 		= session_validate_form_get_field('chk_auto');
//is not needed from now on, because of new field "chk_automanu",which validates if step XOR auto is set to YES
$performance 	= session_validate_form_get_field('chk_performance');

$automanu		= session_validate_form_get_field('chk_automanu');
$steps			='';
$auto			='';

if (!util_date_isvalid($dateassigned)    ||
    !util_date_isvalid($dateexpcomplete) ||
    !util_date_isvalid($dateactcomplete) ||
    !util_date_isvalid($datebasignoff)) {

    error_report_show("test_add_page.php", INVALID_DATE );
}


if (test_name_exists( $project_id, $testname )) {

    error_report_show("test_add_page.php", DUPLICATE_TESTNAME );
}

// Set default values for email_qa_owner and email_ba_owner
if($email_ba == ''){
	$email_ba = 'N';
}
if($email_qa == ''){
	$email_qa = 'N';
}

// set value of $steps XOR $auto to YES
if($automanu == 'man'){
	$steps = 'YES';
}else if($automanu == 'auto'){
	$auto = 'YES';
}

#edited function test_add_test_version to test_add_test_version_return_id to get current id
$testid = test_add_test_version_return_id(
			$project_id,
			$testname,
			$testpurpose,
			$testcomments,
			$testpriority,
			$teststatus,
			$testareatested,
			$testtype,
			$ba_owner,
			$qa_owner,
			$tester,
			$assigned_to,
			$assigned_by,
			$dateassigned,
			$dateexpcomplete,
			$dateactcomplete,
			$datebasignoff,
			$duration,
			$autopass,
			$steps,
			$auto,
			$performance,
			$assoc_req,
			$email_ba,
			$email_qa);

# Add entry into the log table for the project
$page_name = "CREATE TEST";
$deletion = 'N';
$creation = 'Y';
$upload = 'N';
$action = "ADDED TEST $testname";

log_activity_log( $page_name, $deletion, $creation, $upload, $action );

session_validate_form_reset();

# validation succeeded
$redirect_url = $redirect_page ."?test_id=". $testid ."&project_id=".$project_id;
html_print_operation_successful( 'test_detail_page', $redirect_url );


# ---------------------------------------------------------------------
# $Log: test_add_action.php,v $
# Revision 1.7  2008/08/04 08:57:07  peter_thal
# fixed bug with required fields
#
# Revision 1.6  2008/07/09 07:13:26  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.5  2008/07/02 06:51:04  peter_thal
# redirect to test detail page after creating a test
#
# Revision 1.4  2008/07/01 11:44:47  peter_thal
# disabled possibility to select,store and filter both options automated and manual in RTH test category
#
# Revision 1.3  2007/11/19 13:11:53  cryobean
# added test_area, test_type and qa_owner as required fields for adding and updating tests
#
# Revision 1.2  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>