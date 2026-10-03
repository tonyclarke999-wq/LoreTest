<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Add Tests Action Page
#
# $RCSfile: testset_add_tests_action.php,v $  $Revision: 1.1.1.1 $
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

testset_add_tests_from_session(	$s_properties,
								TEST_TESTTYPE,
								"testset_edit" );

############################################################################
# EMAIL NOTIFICATION
############################################################################
$recipients	= user_mail_by_pref(PROJ_USER_EMAIL_TESTSET);

testset_email($project_id, $s_release_id, $s_build_id, $s_testset_id, $recipients, "new_testset");
############################################################################
############################################################################

html_print_operation_successful( "edit_testset", $redirect_page );

# ---------------------------------------------------------------------
# $Log: testset_add_tests_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>