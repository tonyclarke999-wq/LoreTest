<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Edit Action Page
#
# $RCSfile: requirement_edit_action.php,v $  $Revision: 1.6 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

session_validate_form_set($_POST, "requirement_edit_page.php");

if($_POST['record_or_file']=="R") {

	$detail = session_validate_form_get_field('req_detail_required', "", session_use_FCKeditor());
} else {

	$detail = "";
}

$redirect_on_error	= "requirement_edit_page.php?failed=true&error=280";

$project_id			= $_POST["project_id"];
$req_id				= $_POST["req_id"];
$req_version_id		= $_POST["req_ver_id"];

$reason_for_change	= session_validate_form_get_field("req_reason_change", "", session_use_FCKeditor());
$req_name			= session_validate_form_get_field('req_name_required');
$req_area			= session_validate_form_get_field('req_area');
$req_type			= session_validate_form_get_field('req_type');
$req_status			= session_validate_form_get_field('req_status');
$req_functionality	= session_validate_form_get_field("req_functionality");
$req_priority		= session_validate_form_get_field("req_priority");
$req_release		= session_validate_form_get_field("assigned_release");
$req_defect_id 		= session_validate_form_get_field("defect_id");
if( $req_defect_id == '' ) {
	$req_defect_id = 0;
}


# return the user to the previous page if the new_bug_id doesn't exist in the bug table
if( !bug_exists($req_defect_id) && $req_defect_id != 0 ) {
		html_redirect($redirect_on_error);
}
	
requirement_edit(	$project_id,
					$req_id,
					$req_version_id,
					$req_defect_id,
					$req_name,
					$req_area,
					$req_type,
					$req_status,
					$detail,
					$reason_for_change,
					$req_functionality,
					$req_priority,
					$req_release );


session_validate_form_reset();

############################################################################
# EMAIL NOTIFICATION
############################################################################

$recipients		= requirement_get_notify_users($project_id, $req_id);

requirement_email($project_id, $req_id, $recipients, $action="updated");

############################################################################
############################################################################

html_print_operation_successful( 'req_edit_page', "requirement_detail_page.php" );


# ---------------------------------------------------------------------
# $Log: requirement_edit_action.php,v $
# Revision 1.6  2006/09/27 23:58:33  gth2
# Adding functionality to link a change request (defect_id) to a requirement - gth
#
# Revision 1.5  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.4  2005/12/08 22:13:40  gth2
# adding Assign To Release to requirment edit page - gth
#
# Revision 1.3  2005/12/06 13:56:50  gth2
# Adding requirement priority and last updated - gth
#
# Revision 1.2  2005/12/05 19:41:33  gth2
# Adding fields: priority and untestable - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>