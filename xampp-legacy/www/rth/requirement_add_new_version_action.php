<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Requirement Add New Version Action
#
# $RCSfile: requirement_add_new_version_action.php,v $  $Revision: 1.7 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

//print_r($_POST);exit;

session_validate_form_set($_POST, "requirement_add_new_version_page.php");

# if requirement is a file
if( $_POST['req_record_or_file'] == "F" ) {

	$uploaded_filename = file_add_requirement("requirement_add_new_version_page.php"); //"requirement_detail_page.php");
	$detail = "";
} else {

	$uploaded_filename = "";
	$detail = session_validate_form_get_field('req_detail_required', "", session_use_FCKeditor());
}

$redirect_on_error	= "requirement_add_new_version_page.php?failed=true&error=280";

$project_id			= $_POST["project_id"];
$req_id				= $_POST["req_id"];
$req_name			= session_validate_form_get_field('req_name_required');
$req_area			= session_validate_form_get_field('req_area');
$req_type			= session_validate_form_get_field('req_type');
$req_record_or_file	= $_POST['req_record_or_file'];
$req_version		= session_validate_form_get_field('req_version');
$req_priority		= session_validate_form_get_field('req_priority');
$req_status			= session_validate_form_get_field('req_status');
$req_functionality	= session_validate_form_get_field("req_functionality");
$reason_for_change	= session_validate_form_get_field("req_reason_change");
$req_assigned_to	= session_validate_form_get_field('req_assigned_to');
$req_author			= session_validate_form_get_field('req_author');
$req_functionality	= session_validate_form_get_field("req_functionality");
$assigned_release	= session_validate_form_get_field("assigned_release");
$req_defect_id 		= session_validate_form_get_field("defect_id");
if( $req_defect_id == '' ) {
	$req_defect_id = 0;
}


# return the user to the previous page if the new_bug_id doesn't exist in the bug table
if( !bug_exists($req_defect_id) && $req_defect_id != 0 ) {
		html_redirect($redirect_on_error);
}

requirement_add_version(	$project_id,
							$req_id,
							$req_defect_id,
							$req_area,
							$req_type,
							$req_record_or_file,
							$req_version,
							$req_status,
							$uploaded_filename,
							$detail,
							$reason_for_change,
							$req_assigned_to,
							$req_author,
							$req_functionality,
							$req_priority,
							$assigned_release );

session_validate_form_reset();


# GET THE NEW REQUIREMENT VERSION NUMBER
$req_version_id = requirement_get_latest_version( $req_id );


############################################################################
# EMAIL NOTIFICATION
############################################################################

$recipients		= requirement_get_notify_users($project_id, $req_id);

requirement_email($project_id, $req_id, $recipients, $action="new_version");

############################################################################
############################################################################

html_print_operation_successful( 'req_edit_page', "requirement_detail_page.php?req_version_id=$req_version_id" );


# ------------------------------------
# $Log: requirement_add_new_version_action.php,v $
# Revision 1.7  2006/12/05 05:24:18  gth2
# fixing bug 1608519 - gth
#
# Revision 1.6  2006/09/27 23:58:33  gth2
# Adding functionality to link a change request (defect_id) to a requirement - gth
#
# Revision 1.5  2006/01/09 04:15:23  gth2
# cleaning up error checking for file upload - gth
#
# Revision 1.4  2006/01/09 04:11:23  gth2
# fixing problem with file download for req history - gth
#
# Revision 1.3  2005/12/13 13:59:56  gth2
# Completed the addition of requirement priority - gth
#
# Revision 1.2  2005/12/06 13:56:50  gth2
# Adding requirement priority and last updated - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>