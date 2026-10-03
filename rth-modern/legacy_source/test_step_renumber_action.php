<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Step Renumber Action Page
#
# $RCSfile: test_step_renumber_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$test_id			= $_POST['test_id'];
//$test_version_id	= $_POST['test_version_id'];
$project_id			= session_get_project_id();
$page				= 'test_detail_page.php';
$redirect_page 		= 'test_detail_page.php?test_id='. $test_id .'&project_id='. $project_id;

test_renumber_test_steps( $test_id );

html_print_operation_successful( "renumber_test_step_page", $redirect_page );

# ---------------------------------------------------------------------
# $Log: test_step_renumber_action.php,v $
# Revision 1.2  2008/07/09 07:13:26  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>