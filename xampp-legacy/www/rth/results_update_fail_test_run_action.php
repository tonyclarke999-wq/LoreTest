<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Update Fail Test Run Action Page
#
# $RCSfile: results_update_fail_test_run_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();


$page           = basename(__FILE__);
$redirect_page	= "results_page.php";

$s_results 		= session_get_properties("results");
$s_testset_id 	= $s_results['testset_id'];
$s_test_id		= $s_results['test_id'];

$s_user 		= session_get_user_properties();
$tester		 	= $s_user['username'];

$status 		= "Failed";

results_update_test_status( $s_testset_id, $s_test_id, $tester, $status );

//html_redirect($redirect_page);
html_print_operation_successful('update_test_result_page', $redirect_page);

# ---------------------------------------------------------------------
# $Log: results_update_fail_test_run_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>