<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Update Action Page
#
# $RCSfile: bug_update_action.php,v $  $Revision   $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();


$s_bug_details			= session_get_properties( "bug" );
$bug_id					= $s_bug_details['bug_id'];
$redirect_on_success	= 'bug_detail_page.php';
$redirect_on_error		= 'bug_detail_page.php';
$redirect_on_closed		= 'bug_close_page.php';
$action					= $_POST['action'];

$s_project_properties   = session_get_project_properties();

$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

//print_r($_POST);
switch( $action ) {

	case 'update_assign_to':

		$field = BUG_ASSIGNED_TO;
		$value = $_POST['update_assign_to'];
		bug_update_field( $bug_id, $field, $value );
		break;

	case 'update_assign_to_developer':

		$field = BUG_ASSIGNED_TO_DEVELOPER;
		$value = $_POST['assign_to_developer'];
		bug_update_field( $bug_id, $field, $value );
		break;

	case 'update_status':

		$field = BUG_STATUS;
		$value = $_POST['update_status'];
		bug_update_field( $bug_id, $field, $value );
		
		if( $value == 'Closed' ) {
			html_redirect( $redirect_on_closed ."?bug_id=$bug_id" );
		}
		break;

	case 'add_bugnote':

		session_validate_form_set($_POST, $redirect_on_error);
		bug_add_bugnote( $bug_id, $_POST['bugnote_required'] );
		break;

	case 'add_relationship':

		session_validate_form_set($_POST, $redirect_on_error);
		$added_relationship	= bug_add_relationship(	$bug_id,
													$_POST['related_bug_id_required'],
													$_POST['relationship_type'] );

		if( !$added_relationship ) {
			error_report_show($redirect_on_error, COULD_NOT_CREATE_RELATIONSHIP);
		}
		break;

}

# Attach current user to this bug
//bug_monitor_attach_user($bug_id);

html_print_operation_successful( 'update_bug_page', $redirect_on_success );

# ------------------------------------
# $Log: bug_update_action.php,v $
# Revision 1.2  2006/02/27 17:24:55  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>