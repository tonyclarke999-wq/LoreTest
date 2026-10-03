<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Req Assoc Action Page
#
# $RCSfile: test_req_assoc_action.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_properties     = session_get_project_properties();
$s_project_name			= $project_properties['project_name'];
$s_project_id			= $project_properties['project_id'];
$s_test_details			= session_get_properties("test");
$s_test_id				= $s_test_details['test_id'];
//$test_version_id		= $_POST['test_version_id'];
$page					= 'test_detail_page.php';
$redirect_page 			= "test_detail_page.php?test_id=$s_test_id&project_id=$s_project_id&tab=3";

session_records("test_req_assoc");
session_validate_form_set($_POST);

test_edit_assoc_requirements( $s_test_id, "test_req_assoc", "percent_covered_" );

session_validate_form_reset();

html_print_operation_successful( "test_req_assoc_page", $redirect_page );


# ---------------------------------------------------------------------
# $Log: test_req_assoc_action.php,v $
# Revision 1.3  2008/07/09 07:13:25  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.2  2006/01/06 00:34:53  gth2
# fixed bug with associations - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>