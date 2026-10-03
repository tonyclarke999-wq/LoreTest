<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Step Edit Action Page
#
# $RCSfile: test_step_edit_action.php,v $  $Revision: 1.6 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$test_id			= $_POST['test_id'];
$test_step_id		= $_POST['test_step_id'];
//$test_version_id	= $_POST['test_version_id'];
$project_id			= session_get_project_id();
$page				= 'test_step_edit_page.php';
$error_page			= 'test_step_edit_page.php?test_step_id='. $test_step_id .'&test_id='. $test_id;
$redirect_page 		= 'test_detail_page.php?test_id='. $test_id .'&project_id='. $project_id;


session_validate_form_set($_POST, $error_page);

$info_step = "N";
if( isset($_POST['info_step']) ) {

	$info_step = "Y";
}

#added to every session_validate... mysql_real_escape_string(), because it doesnt work for session_validate...(fck-textfield) 
test_update_test_step(  $test_id,
						$test_step_id,
						session_validate_form_get_field('location'),
						mysql_real_escape_string(session_validate_form_get_field('step_action_required', "", session_use_FCKeditor())),
						mysql_real_escape_string(session_validate_form_get_field('step_input', "", session_use_FCKeditor())),
						mysql_real_escape_string(session_validate_form_get_field('step_expected_required', "", session_use_FCKeditor())),
						$info_step );

session_validate_form_reset();

html_print_operation_successful( "edit_test_step_page", $redirect_page );


# ---------------------------------------------------------------------
# $Log: test_step_edit_action.php,v $
# Revision 1.6  2009/03/26 08:04:05  sca_gs
# fixed problem with wrong caption
#
# Revision 1.5  2008/07/21 07:42:34  peter_thal
# small bug fixes for test_detail_page linking parameter
#
# Revision 1.4  2008/07/03 09:30:27  peter_thal
# enabled writing and saving backslashes in all fields
#
# Revision 1.3  2007/03/14 17:23:44  gth2
# removing Test Input as a required field so that it's consistent witth the
# test detail page. - gth
#
# Revision 1.2  2006/12/05 05:29:20  gth2
# updates for 1.6.1 release
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>