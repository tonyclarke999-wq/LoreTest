<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Step Add Action Page
#
# $RCSfile: test_step_add_action.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page				= basename(__FILE__);
$test_id			= $_POST['test_id'];
$project_id			= session_get_project_id();
//$test_version_id	= $_POST['test_version_id'];
//$page				= 'test_detail_page.php';
$redirect_page 		= "test_detail_page.php?test_id=$test_id&project_id=$project_id";

session_validate_form_set($_POST, $redirect_page);

$info_step = "N";
if( isset($_POST['info_step']) ) {

	$info_step = "Y";
}

#added to every session_validate... mysql_real_escape_string(), because it doesnt work for session_validate...(fck-textfield)
test_add_test_step( $test_id,
					#session_validate_form_get_field('location', "", session_use_FCKeditor()), deleted, makes no sense with FCKeditor
					session_validate_form_get_field('location', ""),
					mysql_real_escape_string(session_validate_form_get_field('step_action_required', "", session_use_FCKeditor())),
					mysql_real_escape_string(session_validate_form_get_field('step_expected_required', "", session_use_FCKeditor())),
					mysql_real_escape_string(session_validate_form_get_field('step_test_inputs', "", session_use_FCKeditor())),
					$info_step,
					$page );


session_validate_form_reset();

html_print_operation_successful( "add_test_step_page", $redirect_page );


# ---------------------------------------------------------------------
# $Log: test_step_add_action.php,v $
# Revision 1.3  2008/07/09 07:13:26  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.2  2008/07/03 09:30:27  peter_thal
# enabled writing and saving backslashes in all fields
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>