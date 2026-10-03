<?php
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# testset lock action
#
# $RCSfile: testset_lock_action.php,v $ $Revision: 1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();


$redirect_page		= 'results_page.php';
$testset_id 		= util_clean_post_vars('testset_id');
$build_id 			= util_clean_post_vars('build_id');
$comments 			= util_clean_post_vars('lock_comment');

$user_name 			= session_get_username();
$date 				= date_get_short_dt();


testset_update_testset_lock($testset_id, $build_id, $date, $user_name, $comments);

html_print_operation_successful( "testset_lock_page", $redirect_page );


# ------------------------------------
# $Log: testset_lock_action.php,v $
# Revision 1.1  2008/07/25 09:50:01  peter_thal
# added lock testset feature
# disabled detail column in test result, because functionality is not implemented yet
#
# ------------------------------------
?>
