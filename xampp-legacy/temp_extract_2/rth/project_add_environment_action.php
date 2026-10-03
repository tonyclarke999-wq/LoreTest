<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Add Environment Action
#
# $RCSfile: project_add_environment_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page	= 'project_manage_testenvironment_page.php';
$add_page		= 'project_manage_page.php';

$selected_project_properties 	= session_set_properties("project_manage", $_POST);
$selected_project_id 			= $selected_project_properties['project_id'];

session_validate_form_set($_POST, $add_page);

if( project_environment_exists( $selected_project_id, session_validate_form_get_field('environment_name_required') ) ) {
	
	error_report_show($redirect_page	, DUPLICATE_ENVIRONMENT_NAME);
	
}

project_add_environment(	$selected_project_id,
							session_validate_form_get_field('environment_name_required') );

session_validate_form_reset();


html_print_operation_successful( "add_environment_page", $redirect_page );

# ------------------------------------
# $Log: project_add_environment_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>