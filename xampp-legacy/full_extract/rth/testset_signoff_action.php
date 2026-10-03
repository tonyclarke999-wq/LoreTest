<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Signoff Action Page
#
# $RCSfile: testset_signoff_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();


$redirect_page		= 'results_page.php';
$testset_id 		= util_clean_post_vars('testset_id');
$build_id 			= util_clean_post_vars('build_id');
$status 			= util_clean_post_vars('signoff_status');
$comments 			= util_clean_post_vars('signoff_comments');

$user_name 			= session_get_username();
$date 				= date_get_short_dt();

testset_update_testset_signoff($testset_id, $build_id, $status, $date, $user_name, $comments);

html_print_operation_successful( "testset_signoff_page", $redirect_page );

# ---------------------------------------------------------------------
# $Log: testset_signoff_action.php,v $
# Revision 1.2  2006/01/08 22:00:19  gth2
# bug fixes.  missing some variables - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>