<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Delete Assoc Action Page
#
# $RCSfile: requirement_delete_assoc_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------


include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'requirement_assoc_action.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];
$username				= session_get_username();
$row_style				= '';

$display_options 	= session_set_display_options( "requirements", array_merge($_POST, $_GET) );
$s_tab 				= $display_options['tab'];

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];

if( !empty($_GET['parent_id']) ) {
	$parent_id			= $_GET['parent_id'];
}
$assoc_id			= $_GET['assoc_id'];

switch( $_GET['assoc'] ) {
case "req":
	requirement_delete_req_assoc($parent_id, $assoc_id);
	############################################################################
	# EMAIL NOTIFICATION
	############################################################################
	$recipients = requirement_get_notify_users($project_id, $s_req_id);

	requirement_email($project_id, $s_req_id, $recipients, $action="edit_children");
	############################################################################
	break;
case "test":
	requirement_delete_test_assoc($assoc_id);
	############################################################################
	# EMAIL NOTIFICATION
	############################################################################
	$recipients = requirement_get_notify_users($project_id, $s_req_id);

	requirement_email($project_id, $s_req_id, $recipients, $action="edit_test_assoc");
	############################################################################
	break;
case "release":
	requirement_delete_release_assoc($assoc_id);
	############################################################################
	# EMAIL NOTIFICATION
	############################################################################
	$recipients = requirement_get_notify_users($project_id, $s_req_id);

	requirement_email($project_id, $s_req_id, $recipients, $action="edit_release_assoc");
	############################################################################
	break;
}


html_print_operation_successful( 'req_assoc_page', "requirement_detail_page.php" );

# ---------------------------------------------------------------------
# $Log: requirement_delete_assoc_action.php,v $
# Revision 1.2  2006/01/06 00:34:53  gth2
# fixed bug with associations - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>