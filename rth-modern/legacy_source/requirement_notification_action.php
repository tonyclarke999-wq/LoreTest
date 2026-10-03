<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Notification Action Page
#
# $RCSfile: requirement_notification_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();
$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$project_id				= session_get_project_id();

#### Change to correct redirect page ####
$redirect_page	= 'requirement_notification_page.php';
$s_properties	= session_get_properties("release");

session_records("requirements_notification");

user_edit_requirement_notifications( $project_id, $user_id, "requirements_notification" );

html_print_operation_successful( "req_notifications", $redirect_page );

# ---------------------------------------------------------------------
# $Log: requirement_notification_action.php,v $
# Revision 1.2  2005/12/28 23:16:31  gth2
# Minor bug fix.  Calling wrong session function for project_id - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>