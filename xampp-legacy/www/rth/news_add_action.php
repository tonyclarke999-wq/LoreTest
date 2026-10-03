<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# New Add Action Page
#
# $RCSfile: news_add_action.php,v $ $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page 	= 'home_page.php';
$edit_page 		= 'project_manage_page.php';

session_validate_form_set($_POST, $edit_page);

#### Call api function to add/update database passing in form field values ####
news_add(	$_POST['project_id'],
			session_validate_form_get_field('subject'),
			session_validate_form_get_field('body'),
			$_POST['poster'] );

html_print_operation_successful('news_add_page', $redirect_page);

# ------------------------------------
# $Log: news_add_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>