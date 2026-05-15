<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Group Action Page
#
# $RCSfile: test_group_action.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$redirect_page			= 'test_page.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];


$ids					= explode(":", $_POST['test_ids']);
$field					= $_POST['field'];
$value					= $_POST['field_value'];
$update_db				= true;
$test_id_str			= "";

switch( $field ) {

	case 'man_auto':
		$manual	= "";
		$auto	= "";

		switch( $value ) {
			case "Manual":
				$manual = "YES";
				break;
			case "Automated":
				$auto = "YES";
				break;
			# Disabled this option, because its not possible to select Manual AND Automated
			/*case "Man/Auto":
				$manual = "YES";
				$auto 	= "YES";
				break;*/
			default:
				$update_db = false;
		}
		break;
	case 'ba_owner':
		$field_name = TEST_BA_OWNER;
		break;
	case 'qa_owner':
		$field_name = TEST_QA_OWNER;
		break;
	case 'tester':
			$field_name = TEST_TESTER;
		break;
	case 'test_status':
		$field_name = TEST_STATUS;
		break;
	case 'test_priority':
		$field_name = TEST_PRIORITY;
		break;
	case 'auto_pass':
		if( $value == "Enabled" ) {
			$value = 'Y';
		}
		elseif( $value = "Disabled" ) {
			$value = 'N';
		}
		else {
			$update_db = false;
		}
		$field_name = TEST_AUTO_PASS;
		break;
	case 'test_type':
		$field_name = TEST_TESTTYPE;
		break;
	case 'area_tested':
		$field_name = TEST_AREA_TESTED;
		break;
	case 'email_ba_owner':
		if( $value == "Enabled" ) {
			$value = 'Y';
		}
		elseif( $value == "Disabled" ) {
			$value = 'N';
		}
		else {
			$update_db = false;
		}
		$field_name = TEST_EMAIL_BA_OWNER;
		break;
	case 'email_qa_owner':
		if( $value == "Enabled" ) {
			$value = 'Y';
		}
		elseif( $value == "Disabled" ) {
			$value = 'N';
		}
		else {
			$update_db = false;
		}
		$field_name = TEST_EMAIL_QA_OWNER;
		break;

}

if( $update_db ) {

	foreach($ids as $row_test_id) {
		if( $row_test_id != '' ) {
			$test_id_str .= $row_test_id .", ";
		}
		$test_id_str = substr($test_id_str, 0, -1);

	}

	/*
	Changing this because you cannot remove the Man/Auto status from
	a test using this function. -MD

	# Choosing both manual and automated from the list box on the prior page
	# requires that we update two fields, Steps and Script.
	# Else: we just need to update a single field in the db
	if( is_array($field_name) ) {

		foreach( $field_name as $db_field_name ){
			test_update_field( $project_id, $test_id_str, $db_field_name, $value );
		}
	}
	else {
		test_update_field( $project_id, $test_id_str, $field_name, $value );
	}
	*/

	if( $field=="man_auto" ) {


		test_update_field_man_auto( $project_id, $test_id_str, $manual, $auto );
	}
	else {
		test_update_field( $project_id, $test_id_str, $field_name, $value );
	}
}

html_print_operation_successful( "test_page", $redirect_page );

# ---------------------------------------------------------------------
# $Log: test_group_action.php,v $
# Revision 1.3  2008/07/01 11:44:47  peter_thal
# disabled possibility to select,store and filter both options automated and manual in RTH test category
#
# Revision 1.2  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>