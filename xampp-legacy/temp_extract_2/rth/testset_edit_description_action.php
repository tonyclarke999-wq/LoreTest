<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Edit Description Page
#
# $RCSfile: testset_edit_description_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page	= 'testset_page.php';
$edit_page		= 'testset_edit_description_page.php';

session_validate_form_set($_POST, $edit_page);

#### Call api function to add/update database passing in form field values ####
admin_edit_testset(	util_clean_post_vars('testset_id'),
					session_validate_form_get_field('testset_edit_name_required'),
					session_validate_form_get_field('testset_edit_date'),
					session_validate_form_get_field('testset_edit_description') );

session_validate_form_reset();

html_print_operation_successful( 'release_page', $redirect_page);

# ---------------------------------------------------------------------
# $Log: testset_edit_description_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>