<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Step Import CSV Action Page
#
# $RCSfile: test_step_import_csv_action.php,v $  $Revision: 1.4 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$s_user_properties		= session_get_user_properties();
$s_project_properties   = session_get_project_properties();
$s_show_options 		= session_get_show_options();
$s_test_details			= session_set_properties("test", $_GET);

$s_user_id				= $s_user_properties['user_id'];
$s_username				= $s_user_properties['username'];
$s_tempest_admin		= $s_user_properties['tempest_admin'];
$s_project_rights		= $s_user_properties['project_rights'];
$s_delete_rights		= $s_user_properties['delete_rights'];
$s_email				= $s_user_properties['email'];

$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

$project_details		= project_get_details($project_id);
$s_show_test_input		= $project_details[PROJ_SHOW_TEST_INPUT];

$test_id				= util_pad_id( $s_test_details['test_id'] );
$test_version_id		= $s_test_details['test_version_id'];
$redirect_page 			= "test_detail_page.php?test_id=$test_id&project_id=$project_id&tab=1";

# Upload function
if( IMPORT_EXPORT_TO_EXCEL ) {
	test_import_excel( $test_id, 'upload_file');
}
else {
	test_import_csv($test_id, 'upload_file');
}


html_print_operation_successful( 'test_import_csv_page', $redirect_page );

# ---------------------------------------------------------------------
# $Log: test_step_import_csv_action.php,v $
# Revision 1.4  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.3  2006/04/11 12:11:03  gth2
# create a test version when uploading test steps - gth
#
# Revision 1.2  2006/01/16 13:27:45  gth2
# adding excel integration - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>