<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Discussion Action Page
#
# $RCSfile: requirement_discussion_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$s_properties			= session_set_properties("requirements", $_GET);
$s_req_id				= $s_properties['req_id'];
$s_req_version_id		= $s_properties['req_version_id'];

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

# ADD POST
if( isset($_POST['submit_add_post']) ) {
	$redirect_page = "requirement_discussion_page.php";

	session_validate_form_set($_POST, $redirect_page);

	$discussion_id	= $_POST['discussion_id'];
	$post			= session_validate_form_get_field( 'new_post_required', "", session_use_FCKeditor() );
	$author			= $_POST['author'];

	discussion_add_post(	$discussion_id,
							$post,
							$author,
							"");

	session_validate_form_reset();

	############################################################################
	# EMAIL NOTIFICATION
	############################################################################
	$notify_recipients		= requirement_get_notify_users($project_id, $s_req_id);
	$discussion_recipients	= requirement_get_discussion_users($project_id);

	# merge arrays and remove duplicates
	$recipients				= array_merge($discussion_recipients, $notify_recipients);

	requirement_email($project_id, $s_req_id, $recipients, $action="new_post", $discussion_id);
	############################################################################
	############################################################################

	html_print_operation_successful( "discussion_post_add_page", $redirect_page );
}

# ADD DISCUSSION
if( isset($_POST['submit_add_discussion']) ) {

	$redirect_page = "requirement_detail_page.php";

	session_validate_form_set($_POST, $redirect_page);

	$req_id		= $_POST["req_id"];
	$subject	= session_validate_form_get_field("subject_required");
	$discussion	= session_validate_form_get_field( "discussion", "", session_use_FCKeditor() );
	$status		= $_POST["status"];
	$author		= $_POST["author"];
	$assigned_to= $_POST["assign_to"];

	discussion_add(	$req_id,
					$subject,
					$discussion,
					$status,
					$author,
					$assigned_to );

	session_validate_form_reset();

	############################################################################
	# EMAIL NOTIFICATION
	############################################################################
	$notify_recipients		= requirement_get_notify_users($project_id, $s_req_id);
	$discussion_recipients	= requirement_get_discussion_users($project_id);

	# merge arrays and remove duplicates
	$recipients				= array_merge($discussion_recipients, $notify_recipients);

	requirement_email($project_id, $s_req_id, $recipients, $action="new_discussion");
	############################################################################
	############################################################################

	html_print_operation_successful( "discussion_add_page", $redirect_page );
}

# ---------------------------------------------------------------------
# $Log: requirement_discussion_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>