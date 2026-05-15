<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: field_add_action.php,v $  
# $Revision   $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$s_project_properties	= session_get_project_properties();
$project_id				= $s_project_properties['project_id'];
$redirect_page			= 'field_add_page.php';
$redirect_on_error		= 'field_add_page.php';
$screen_id				= session_validate_form_get_field('screen_id');

# Make sure user completed required fields
session_validate_form_set($_POST, $redirect_page);


# Make sure the screen name doesn't already exist for this project
$num = test_field_name_exists( session_validate_form_get_field('field_name_required'),
							   session_validate_form_get_field('screen_id') );
if( $num > 0 ) {
	
	error_report_show($redirect_page, DUPLICATE_FIELD_NAME );
	
}

test_add_field(	session_validate_form_get_field('field_name_required'),
				session_validate_form_get_field('screen_id'),
				session_validate_form_get_field('field_desc'),
				session_validate_form_get_field('field_order'),
				session_validate_form_get_field('text_box'));


session_validate_form_reset();

html_print_operation_successful( 'field_page', $redirect_page );

# ------------------------------------
# $Log: field_add_action.php,v $
# Revision 1.1  2006/05/03 20:18:31  gth2
# no message
#
# ------------------------------------

?>