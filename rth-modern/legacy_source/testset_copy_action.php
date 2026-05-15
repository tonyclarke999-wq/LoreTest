<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Copy Action Page
#
# $RCSfile: testset_copy_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

global $db;

$copy_add_page			= "testset_copy_add_page.php";
$send_to 				= "";
$redirect_page 			= 'testset_page.php';
$project_id 			= session_get_project_id();
$s_user_properties		= session_get_user_properties();
$s_project_properties	= session_get_project_properties();
$s_properties			= session_get_properties( "release" );

session_records("testset_copy");

/*
$new_testset_id = testset_add(	session_validate_form_get_field('testset_name_required'),
								session_validate_form_get_field('testset_description'),
								$s_properties['build_id'],
								"TESTSET COPY" );
*/
testset_edit_from_session(	$s_properties['testset_id'],
							TEST_TS_ASSOC_STATUS,
							"testset_copy" );

html_print_operation_successful( "copy_testset", $redirect_page );

# ---------------------------------------------------------------------
# $Log: testset_copy_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>