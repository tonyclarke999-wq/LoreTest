<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: bug_add_action.php,v $  $Revision: 1.2 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

# TO DO
# check for test_run_id
# check for req_version_id
# add bug relationships
# create bug history

$project_properties     = session_get_project_properties();
$project_id				= $project_properties['project_id'];
$page                   = basename(__FILE__);
$redirect_on_error		= 'bug_add_page.php';

session_validate_form_set($_POST, $redirect_on_error);

$bug_id = bug_add( $project_id,
					session_validate_form_get_field('bug_category'),
					session_validate_form_get_field('bug_component'),
					session_validate_form_get_field('discovery_period'),
					session_validate_form_get_field('bug_priority'),
					session_validate_form_get_field('bug_severity'),
					session_validate_form_get_field('found_in_release'),
					session_validate_form_get_field('assign_to_release'),
					//session_validate_form_get_field('implemented_in_release'),
					session_validate_form_get_field('assigned_to'),
					session_validate_form_get_field('assigned_to_developer'),
					session_validate_form_get_field('summary_required'),
					session_validate_form_get_field('description_required', "", session_use_FCKeditor() ),
					session_validate_form_get_field('req_version_id'),
					session_validate_form_get_field('verify_id') );

session_validate_form_reset();

# REDIRECT THE USER BACK TO THE TEST RUN PAGE IF THAT'S WHERE THEY CAME FROM
if( !empty( $_POST['test_run_id'] ) ) {

	$test_run_id =  $_POST['test_run_id'];
	$redirect_on_success = "results_view_verifications_page.php?test_run_id=$test_run_id";

}
else {
	$redirect_on_success = "bug_detail_page.php?bug_id=$bug_id";
}
html_print_operation_successful( 'add_bug_page', $redirect_on_success );

# ------------------------------------
# $Log: bug_add_action.php,v $
# Revision 1.2  2006/02/27 17:51:34  gth2
# added bug email functionality - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>