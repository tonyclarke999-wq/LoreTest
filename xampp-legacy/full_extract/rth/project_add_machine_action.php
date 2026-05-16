<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Add Users Action
#
# $RCSfile: project_add_machine_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_id	= session_get_project_id();

$redirect_page		= 'project_manage_testmachines_page.php';
$proj_properties	= session_get_properties("project_manage");

session_validate_form_set($_POST, $redirect_page);

if( project_machine_exists( $proj_properties['project_id'], session_validate_form_get_field('machine_name_required') )) {
	
	error_report_show($redirect_page	, DUPLICATE_MACHINE_NAME);
	
}

if( project_ip_address_exists( $proj_properties['project_id'], session_validate_form_get_field('machine_ip_required')  )) {
	
	error_report_show($redirect_page	, DUPLICATE_IP_ADDRESS);
	
}

project_add_machine(	$proj_properties['project_id'],
						session_validate_form_get_field('machine_name_required'),
						session_validate_form_get_field('machine_location_required'),
						session_validate_form_get_field('machine_ip_required') );

session_validate_form_reset();

html_print_operation_successful( "add_machine_page", $redirect_page );

# ------------------------------------
# $Log: project_add_machine_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>