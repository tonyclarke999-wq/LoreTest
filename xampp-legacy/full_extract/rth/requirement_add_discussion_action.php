<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Requirement Add Discussion Action
#
# $RCSfile: requirement_add_discussion_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

#### Change to correct redirect page ####
$redirect_page = 'build_page.php';
$edit_page = 'build_edit_page.php';

session_validate_form_set($_POST, $edit_page);

#### Call api function to add/update database passing in form field values ####
discussion_add(	$_POST['req_id'],
				session_validate_form_get_field('description'),
				$_POST['status'],
				session_validate_form_get_field('subject_required '),
				$_POST['author'],
				$_POST["assign_to"]);

session_validate_form_reset();

html_print_operation_successful( 'build_page', $redirect_page );

# ---------------------------------------------------------------------
# $Log: requirement_add_discussion_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>