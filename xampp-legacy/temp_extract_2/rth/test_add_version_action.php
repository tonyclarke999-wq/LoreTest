<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Add Version Action Page
#
# $RCSfile: test_add_version_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$test_id			= $_POST['test_id'];
$test_version_id	= $_POST['test_version_id'];
$project_id			= session_get_project_id();
$page				= 'test_detail_page.php';
$redirect_page 		= 'test_detail_page.php?test_id='. $test_id .'&project_id='. $project_id;

session_validate_form_set($_POST, $redirect_page);

test_add_new_version( $test_id,
					   $test_version_id,
					   session_validate_form_get_field('test_comments'),
					   session_validate_form_get_field('test_status'),
					   session_validate_form_get_field('test_assigned_to'),
					   $page);


session_validate_form_reset();

html_print_operation_successful( "add_test_version_page", $redirect_page );


# ---------------------------------------------------------------------
# $Log: test_add_version_action.php,v $
# Revision 1.2  2008/07/21 07:42:34  peter_thal
# small bug fixes for test_detail_page linking parameter
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>