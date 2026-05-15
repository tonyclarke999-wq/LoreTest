<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Edit Bug Category Action
#
# $RCSfile: project_edit_bug_category_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_on_success	= 'project_manage_bug_category_page.php';
$redirect_on_error		= 'project_edit_bug_category_page.php';

$proj_properties	= session_set_properties("project_manage", $_POST);

session_validate_form_set($_POST, $redirect_on_error);

project_edit_bug_category(	$_POST['project_id'],
							$_POST['bug_category_id'],
							session_validate_form_get_field('bug_category_required') );

session_validate_form_reset();


html_print_operation_successful( "edit_bug_category_page", $redirect_on_success );

# ------------------------------------
# $Log: project_edit_bug_category_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>