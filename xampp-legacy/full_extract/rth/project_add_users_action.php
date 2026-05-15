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
# $RCSfile: project_add_users_action.php,v $  $Revision: 1.2 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_id	= session_get_project_id();

$redirect_page		= 'project_manage_page.php';
$proj_properties	= session_get_properties("project_manage");

session_validate_form_set($_POST, $redirect_page);


if( !isset($_POST['add_users']) ) {

	error_report_show( $redirect_page, REQUIRED_FIELD_MISSING );
}


project_add_users(	$proj_properties['project_id'], 
					$_POST['add_users'],
					$_POST['add_users_rights'],
					isset($_POST['add_user_delete_rights']) ? "Y": "N",
					isset($_POST['add_user_email_testset']) ? "Y": "N",
					isset($_POST['add_user_email_discussions']) ? "Y": "N",
					isset($_POST['add_user_qa_tester']) ? "Y": "N",
					isset($_POST['add_user_ba_tester']) ? "Y": "N" );

session_validate_form_reset();

html_print_operation_successful( "add_users_page", $redirect_page."#users" );

# ------------------------------------
# $Log: project_add_users_action.php,v $
# Revision 1.2  2006/02/27 17:26:16  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>