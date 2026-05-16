<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Release Edit Action
#
# $RCSfile: release_edit_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page = 'release_page.php';
$edit_page = 'release_edit_page.php';

session_validate_form_set($_POST, $edit_page);

#### Call api function to add/update database passing in form field values ####
admin_edit_release(	session_validate_form_get_field('release_id'),
					session_validate_form_get_field('release_edit_name_required'),
					session_validate_form_get_field('release_edit_date'),
					session_validate_form_get_field('release_edit_description') );

html_print_operation_successful( 'release_page', $redirect_page);

# ------------------------------------
# $Log: release_edit_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------

?>