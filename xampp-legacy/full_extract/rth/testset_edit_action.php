<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Edit Action Page
#
# $RCSfile: testset_edit_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();
$project_id	= session_get_project_id();

#### Change to correct redirect page ####
$redirect_page	= 'testset_page.php';

$s_properties	= session_get_properties("release");
$s_release_id	= $s_properties['release_id'];
$s_build_id		= $s_properties['build_id'];
$s_testset_id	= $s_properties['testset_id'];

session_records("testset_edit");

testset_edit_from_session(	$s_testset_id,
							TEST_TESTTYPE,
							"testset_edit" );

html_print_operation_successful( "edit_testset", $redirect_page );

# ---------------------------------------------------------------------
# $Log: testset_edit_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>