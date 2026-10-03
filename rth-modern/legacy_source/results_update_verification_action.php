<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Update Verification Action Page
#
# $RCSfile: results_update_verification_action.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

# When a bug and verification are related, the data is not normalized.
# The bug_id exists in the Bug Table and the Verification table
# The verification_id exists in both the verification table and the bug table
# This is done for performance reasons but requires some extra cleanup when data is 
# updated or deleted

$page               = basename(__FILE__);
$redirect_page		= "results_view_verifications_page.php";
$redirect_on_error	= "results_update_verification_page.php?failed=true&error=280";

$s_results = session_get_properties("results");

$testset_id 	= $s_results['testset_id'];
$test_id 		= $s_results['test_id'];
$test_run_id 	= $s_results['test_run_id'];
$verify_id 		= $s_results['verify_id'];

$comments			= util_clean_post_vars('verification_comments');
$status				= util_clean_post_vars('verification_status');
$current_bug_id		= util_clean_post_vars('current_bug_id');
$new_bug_id			= util_clean_post_vars('new_bug_id');
if( $new_bug_id == '' ) {
	$new_bug_id = 0;
}



# Need to verify the user entered a valid id and
# update the bug table if the user has changed this value
if( $current_bug_id != $new_bug_id ) {

	# return the user to the previous page if the new_bug_id doesn't exist in the bug table
	if( !bug_exists($new_bug_id) && $new_bug_id != 0 ) {
		html_redirect($redirect_on_error);
	}

	
	# see if the verify_id exists anywhere in the bug table
	$related_bug_id = bug_get_bug_id_from_verification_id( $verify_id );

	# remove the old verify_id from the bug table if it exists
	if( !empty($related_bug_id) ) {

		bug_update_field( $related_bug_id, BUG_TEST_VERIFY_ID, $value="" );
	}

	# set the new verify_id in the bug table
	bug_update_field( $new_bug_id, BUG_TEST_VERIFY_ID, $verify_id );

}

# Update the verify results table 
results_update_verification( $test_run_id, $verify_id, $status, $comments, $new_bug_id );

html_print_operation_successful( 'update_verification', $redirect_page );

# ---------------------------------------------------------------------
# $Log: results_update_verification_action.php,v $
# Revision 1.3  2006/09/25 12:46:39  gth2
# Working on linking rth and other bugtrackers - gth
#
# Revision 1.2  2006/01/20 02:36:05  gth2
# enable export to excel functionaltiy - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>