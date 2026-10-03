<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Edit Area Tested Action
#
# $RCSfile: project_edit_user_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page		= 'project_manage_page.php';
$proj_properties	= session_set_properties("project_manage", $_POST);

project_edit_user(	$proj_properties['project_id'],
					$proj_properties['user_id'],
					$_POST['user_rights'],
					isset($_POST['user_delete_rights']) ? "Y": "N",
					isset($_POST['user_email_testset']) ? "Y": "N",
					isset($_POST['user_email_discussions']) ? "Y": "N",
					isset($_POST['user_qa_owner']) ? "Y": "N",
					isset($_POST['user_ba_owner']) ? "Y": "N" );

html_print_operation_successful( "user_edit_page", $redirect_page."#users" );

# ------------------------------------
# $Log: project_edit_user_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>