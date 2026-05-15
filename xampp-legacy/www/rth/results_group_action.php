<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Group Action Page
#
# $RCSfile: results_group_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$redirect_page			= 'results_page.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];
$s_properties			= session_get_properties("results");
$testset_id				= $s_properties['testset_id'];
$s_user					= session_get_user_properties();
$s_username				= $s_user['username'];

$ids					= explode(":", $_POST['ids']);
$comments				= $_POST['test_result_comments'];
$status					= $_POST['action'];
$update_db				= true;
$test_ids				= "";
$finished				= '1';


foreach( $ids as $row_test_id ) {

	if( $row_test_id != '' ) {
		$test_ids .= $row_test_id .", ";
	}
	$test_ids = substr($test_ids, 0, -1);
}

results_mass_update_test_result($testset_id, $test_ids, $s_username, $status, $comments, $finished );

html_print_operation_successful( "test_results_page", $redirect_page );

# ---------------------------------------------------------------------
# $Log: results_group_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>