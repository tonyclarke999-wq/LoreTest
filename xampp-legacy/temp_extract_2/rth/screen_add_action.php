<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: screen_add_action.php,v $  
# $Revision   $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$s_project_properties	= session_get_project_properties();
$project_id				= $s_project_properties['project_id'];
$redirect_page			= 'screen_page.php';
$screen_id				= session_validate_form_get_field('screen_id');

# Make sure user completed required fields
session_validate_form_set($_POST, $redirect_page);

# Make sure the screen name doesn't already exist for this project
$num = test_screen_name_exists($project_id, session_validate_form_get_field('screen_name_required'));
if( $num > 0 ) {
	
	error_report_show($redirect_page, DUPLICATE_SCREEN_NAME );
	
}

test_add_screen(	$project_id,
					session_validate_form_get_field('screen_name_required'),
					session_validate_form_get_field('screen_desc'),
					session_validate_form_get_field('screen_order') );

session_validate_form_reset();

html_print_operation_successful( 'screen_page', $redirect_page );

# ------------------------------------
# $Log: screen_add_action.php,v $
# Revision 1.1  2006/05/03 20:24:01  gth2
# no message
#
# ------------------------------------

?>