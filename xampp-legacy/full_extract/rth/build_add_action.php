<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: build_add_action.php,v $  $Revision   $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page	= 'build_page.php';
$build_page		= 'build_page.php';
$release_id		= session_validate_form_get_field('release_id');


$s_release_properties = session_set_properties( "release" );
$release_name = admin_get_release_name($s_release_properties['release_id']);

session_validate_form_set($_POST, $redirect_page);

if( admin_build_name_exists($s_release_properties['release_id'], session_validate_form_get_field('build_name_required') ) ) {
	
	error_report_show($redirect_page	, DUPLICATE_BUILD_NAME );
	
}

admin_add_build(	$s_release_properties['release_id'],
					session_validate_form_get_field('build_name_required'),
					session_validate_form_get_field('build_description'),
					"ADD BUILD" );

session_validate_form_reset();

html_print_operation_successful( 'release_page', $redirect_page );

# ------------------------------------
# $Log: build_add_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------

?>