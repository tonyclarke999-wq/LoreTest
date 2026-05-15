<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Plan Upload New Version Action Page
#
# $RCSfile: testset_upload_new_test_plan_version_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();
$project_id	= session_get_project_id();

#### Change to correct redirect page ####
$redirect_page	= 'testset_page.php';
$s_properties	= session_get_properties("release");

file_add_test_plan_version( $_FILES['upload_file']['tmp_name'],
							$_POST['test_plan_name'],
							$_POST['build_id'],
							$_POST['comments'],
							$_POST['old_test_plan_id'],
							$_POST['version'],
							$redirect_page );

html_print_operation_successful( lang_get('test_plan_add'), $redirect_page );

# ---------------------------------------------------------------------
# $Log: testset_upload_new_test_plan_version_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>