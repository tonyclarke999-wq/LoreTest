<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Requirement Edit Coverage Action Page
#
# $RCSfile: test_req_edit_coverage_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$s_test_details		= session_get_properties("test");
$s_test_id			= $s_test_details['test_id'];
$assoc_id			= $_POST['assoc_id'];
$percent_covered	= $_POST['percent_covered'];
$project_id			= session_get_project_id();
$page				= 'test_detail_page.php';
$redirect_page 		= "test_detail_page.php?test_id=$s_test_id&project_id=$project_id&tab=3";

session_validate_form_set($_POST, $redirect_page);

test_set_percent_req_coverage( $assoc_id, $percent_covered );
							
session_validate_form_reset();

html_print_operation_successful( "test_req_coverage_page", $redirect_page );


# ---------------------------------------------------------------------
# $Log: test_req_edit_coverage_action.php,v $
# Revision 1.2  2008/07/21 07:42:34  peter_thal
# small bug fixes for test_detail_page linking parameter
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>