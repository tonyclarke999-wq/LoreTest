<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# --------------------------------------------------
# Release Add Action Page
#
# $RCSfile: release_add_action.php,v $ $Revision: 1.2 $
# --------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page 	= 'release_page.php';
$release_page	= 'release_page.php';
$project_id		= session_get_project_id($release_page);

session_validate_form_set($_POST, $redirect_page);

if( admin_release_name_exists( $project_id, session_validate_form_get_field('rel_name_required') ) ) {
	
	error_report_show($redirect_page, DUPLICATE_RELEASE_NAME );
	
}

admin_add_release(	session_validate_form_get_field('rel_name_required'),
					session_validate_form_get_field('rel_description'),
					$project_id,
					"CREATE RELEASE" );

session_validate_form_reset();

html_print_operation_successful( "add_release", $release_page );

# ------------------------------------
# $Log: release_add_action.php,v $
# Revision 1.2  2007/02/03 11:58:12  gth2
# no message
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>