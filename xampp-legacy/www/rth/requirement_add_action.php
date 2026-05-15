<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Add Action
#
# $RCSfile: requirement_add_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------
include"./api/include_api.php";
auth_authenticate_user();

# if $parent_req==0, then the requirement is not a child of any other requirement
if( isset($_POST["parent_req"]) ) {

	$parent_req = $_POST["parent_req"];
} else {

	$parent_req = 0;
}

$add_req_page	= "requirement_add_page.php?type=".$_POST['req_record_or_file']."&parent_req=$parent_req";

session_validate_form_set($_POST, $add_req_page);

# if requirement is a file
if( $_POST['req_record_or_file'] == "F" ) {

	$uploaded_filename = file_add_requirement( $add_req_page );
	$detail = "";
} else {

	$uploaded_filename = "";
	$detail = session_validate_form_get_field('req_detail_required', "", session_use_FCKeditor());
}

$req_reason_for_change	= session_validate_form_get_field("req_reason_change");
$project_id				= $_POST['project_id'];
$req_name				= session_validate_form_get_field('req_name_required');
$req_area				= session_validate_form_get_field('req_area_covered');
$req_type				= session_validate_form_get_field('req_type');
$req_rec_or_file		= $_POST['req_record_or_file'];
$req_version			= session_validate_form_get_field('req_version');
$req_status				= session_validate_form_get_field('req_status');
$req_priority			= session_validate_form_get_field('req_priority');
$req_assigned_to		= session_validate_form_get_field('req_assigned_to');
$req_assign_release		= session_validate_form_get_field('req_assign_release');
$req_author				= session_validate_form_get_field('req_author');
$req_functionality		= session_validate_form_get_field('req_functionality');


//$req_untestable			= session_validate_form_get_field('chk_untestable');

if( requirement_name_exists($project_id, $req_name) ) {

	error_report_show($add_req_page, DUPLICATE_REQ_NAME);
}


requirement_add(	$project_id,
					$req_name,
					$req_area,
					$req_type,
					$req_rec_or_file,
					$req_version,
					$req_status,
					$req_priority,
					$uploaded_filename,
					$detail,
					$req_reason_for_change,
					$req_assigned_to,
					$req_assign_release,
					$req_author,
					$req_functionality,
					$parent_req );


session_validate_form_reset();

html_print_operation_successful( 'req_add_page', "requirement_page.php" );


# ------------------------------------
# $Log: requirement_add_action.php,v $
# Revision 1.2  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>