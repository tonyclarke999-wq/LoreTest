<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Detail Update Action Page
#
# $RCSfile: bug_detail_update_action.php,v $  $Revision   $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();


$s_bug_details			= session_get_properties( "bug" );
$bug_id					= $s_bug_details['bug_id'];
$redirect_on_success	= 'bug_detail_page.php';
$redirect_on_error		= 'bug_detail_update_page.php';
$redirect_on_closed		= 'bug_close_page.php';

session_validate_form_set($_POST, $redirect_on_error);

$value=session_validate_form_get_field('bug_status');

bug_update(	$bug_id,
			session_validate_form_get_field('bug_status'),
			session_validate_form_get_field('bug_category'),
			session_validate_form_get_field('discovery_period'),
			session_validate_form_get_field('bug_component'),
			session_validate_form_get_field('bug_priority'),
			session_validate_form_get_field('bug_severity'),
			session_validate_form_get_field('found_in_release'),
			session_validate_form_get_field('assign_to_release'),
			session_validate_form_get_field('implemented_in_release'),
			session_validate_form_get_field('assigned_to'),
			session_validate_form_get_field('assigned_to_developer'),
			session_validate_form_get_field('summary_required'),
			session_validate_form_get_field( 'bug_description_required', "", session_use_FCKeditor() ) );

if( $value == 'Closed' ) {
	html_redirect( $redirect_on_closed ."?bug_id=$bug_id" );
}

session_validate_form_reset();

html_print_operation_successful( 'update_bug_page', $redirect_on_success );

# ------------------------------------
# $Log: bug_detail_update_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>