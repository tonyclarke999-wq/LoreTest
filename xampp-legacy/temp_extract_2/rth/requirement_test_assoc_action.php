<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Test Assoc Action Page
#
# $RCSfile: requirement_test_assoc_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];
$s_req_version_id	= $s_properties['req_version_id'];

$redirect_page	= 'requirement_tests_assoc_page.php';

session_records("requirement_tests_assoc");
session_validate_form_set($_POST);

requirement_edit_assoc_tests($s_req_id, "requirement_tests_assoc", "percent_covered_");

session_validate_form_reset();

html_print_operation_successful("req_assoc_tests_page", $redirect_page);

# ---------------------------------------------------------------------
# $Log: requirement_test_assoc_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>