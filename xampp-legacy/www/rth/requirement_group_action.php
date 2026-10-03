<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Group Action Page
#
# $RCSfile: requirement_group_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$redirect_page			= 'requirement_page.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$req_ids				= explode("|", $_POST['ids']);
$rows_version_ids		= explode("|", $_POST['version_ids']);
$field_name				= $_POST['field'];
$field_value			= $_POST['field_value'];

switch( $field_name ) {

	case 'assigned_release':
		requirement_group_assoc_release( $rows_version_ids, $field_value );
		break;
	case 'status':
		
		foreach( $rows_version_ids as $row_version_id ) {
			requirement_update_req_version_field( $row_version_id, $field_name, $field_value );
			//print"need to update req_id $req_id and version $row_version_id<br>";
		}	
		break;

}

/*
if( $field_name == 'assigned_release' ) {

	requirement_group_assoc_release( $rows_version_ids, $field_value );
} else {

	foreach( $rows_version_ids as $row_version_id ) {

		requirement_update_req_version_field( $row_version_id, $field_name, $field_value );
		//print"need to update req_id $req_id and version $row_version_id<br>";
	}
}
*/

html_print_operation_successful( "req_page", $redirect_page );

# ---------------------------------------------------------------------
# $Log: requirement_group_action.php,v $
# Revision 1.2  2005/12/08 22:13:40  gth2
# adding Assign To Release to requirment edit page - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>