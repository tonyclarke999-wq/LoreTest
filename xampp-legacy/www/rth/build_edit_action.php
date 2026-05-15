<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Page name
#
# $RCSfile: build_edit_action.php,v $  $Revision: 1.2 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

#### Change to correct redirect page ####
$redirect_page = 'build_page.php';
$edit_page = 'build_edit_page.php';
$s_release_properties = session_set_properties( "release" );
session_validate_form_set($_POST, $edit_page);

if( admin_build_name_exists($s_release_properties['release_id'], session_validate_form_get_field('build_edit_name_required') ) ) {
	
	error_report_show($edit_page	, DUPLICATE_BUILD_NAME );
	
}

#### Call api function to add/update database passing in form field values ####
admin_edit_build(	session_validate_form_get_field('build_id'),
					session_validate_form_get_field('build_edit_name_required'),
					session_validate_form_get_field('build_edit_date'),
					session_validate_form_get_field('build_edit_description') );

session_validate_form_reset();

html_print_operation_successful( 'build_page', $redirect_page );

# ------------------------------------
# $Log: build_edit_action.php,v $
# Revision 1.2  2008/08/08 11:22:10  peter_thal
# disabled update buildname to an existing buildname
# test_detail_update_action.php: changed redirect page on error
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------

?>