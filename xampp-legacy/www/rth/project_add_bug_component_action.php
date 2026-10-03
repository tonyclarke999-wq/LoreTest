<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Add Bug Component Action
#
# $RCSfile: project_add_bug_component_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_id	= session_get_project_id();

$redirect_on_error		= 'project_manage_bug_component_page.php';
$redirect_on_success	= 'project_manage_bug_component_page.php';
$s_project_properties	= session_get_properties("project_manage");
$project_id				= $s_project_properties['project_id'];

session_validate_form_set($_POST, $redirect_on_error);

if( project_bug_component_exists( $project_id, session_validate_form_get_field('bug_component_required') ) ) {
	
	error_report_show($redirect_on_error	, DUPLICATE_BUG_COMPONENT );
	
}


project_add_bug_component( $project_id, 
						   session_validate_form_get_field('bug_component_required') );

session_validate_form_reset();

html_print_operation_successful( "add_bug_component_page", $redirect_on_success );

# ------------------------------------
# $Log: project_add_bug_component_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>