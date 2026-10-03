<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Add Action Page
#
# $RCSfile: testset_add_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

global $db;
$send_to = "";
$redirect_page 			= 'testset_page.php';
$testset_page			= 'testset_page.php';
$project_id 			= session_get_project_id();
$s_user_properties		= session_get_user_properties();
$s_project_properties	= session_get_project_properties();

$s_release_properties	= session_set_properties( "release" );
$release_id				= $s_release_properties['release_id'];
$release_name			= admin_get_release_name($release_id);
$build_id				= $s_release_properties['build_id'];
$build_name				= admin_get_build_name($build_id);

session_validate_form_set($_POST, $redirect_page);

if( testset_name_exists($s_release_properties['build_id'], session_validate_form_get_field('testset_name_required') ) ) {
	
	error_report_show($redirect_page	, DUPLICATE_TESTSET_NAME );
	
}

$testset_id = testset_add(	session_validate_form_get_field('testset_name_required'),
							session_validate_form_get_field('testset_description'),
							$s_release_properties['build_id'],
							"ADD TESTSET" );

session_validate_form_reset();

html_print_operation_successful( "add_testset", $redirect_page );

# ---------------------------------------------------------------------
# $Log: testset_add_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>