<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Group Action
#
# $RCSfile: bug_group_action.php,v $    $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$redirect_page			= 'bug_page.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];


$ids					= explode(":", $_POST['bug_ids']);
$field					= $_POST['field'];
$value					= $_POST['field_value'];
$update_db				= true;
$test_id_str			= "";

switch( $field ) {

	case 'bug_status':
		$field_name = BUG_STATUS;
		break;
	case 'assign_to':
		$field_name = BUG_ASSIGNED_TO;
		break;
	case 'assign_to_dev':
		$field_name = BUG_ASSIGNED_TO_DEVELOPER;
		break;
	
	case 'test_type':

}

if( $update_db ) {

	# This could become too expensive as we update each bug individually
	# I'm doing this because we wan't to update the history table for each bug
	# I can probably find a better way of doing this later bug for now this'll do
	# UPDATE bug_table WHERE bug_id IN ( 1, 2, 3, 4 )
	# Or maybe use a temp table to join the bug_table and history table and run 
	# one update
	foreach($ids as $bug_id) {

		if( $bug_id != '' ) {
			bug_update_field( $bug_id, $field_name, $value );
		}
	}


}

html_print_operation_successful( "bug_page", $redirect_page );

# ------------------------------------
# $Log: bug_group_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>