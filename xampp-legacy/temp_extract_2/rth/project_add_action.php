<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Add Action
#
# $RCSfile: project_add_action.php,v $  $Revision: 1.5 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_id	= session_get_project_id();

$add_page		= 'project_add_page.php';
$redirect_page	= 'admin_page.php';
$proj_properties	= session_get_properties("project_manage");

session_validate_form_set($_POST, $add_page);

$project_name 			= session_validate_form_get_field('project_name_required');
$project_folder_name	= str_replace(" ", "", $project_name);
$project_description 	= session_validate_form_get_field('project_description');

$req_docs		= FILE_UPLOAD_PATH .$project_folder_name."_req_docs/";
$test_docs		= FILE_UPLOAD_PATH .$project_folder_name."_test_docs/";
$test_run_docs	= FILE_UPLOAD_PATH .$project_folder_name."_test_run_docs/";
$test_plan_docs	= FILE_UPLOAD_PATH .$project_folder_name."_test_plan_docs/";
$defect_docs	= FILE_UPLOAD_PATH .$project_folder_name."_defect_docs/";

# check username unique
if( !is_null( project_get_id($project_name) ) ) {
	error_report_show( $add_page, PROJECT_NOT_UNIQUE );
}

if ( mkdir($req_docs, 0700) && mkdir($test_docs, 0700) && mkdir($test_run_docs, 0700) && mkdir($test_plan_docs, 0700) && mkdir($defect_docs, 0700) ) {

	project_add(	$project_name,
					$project_description,
					$_POST['project_status'],
					isset($_POST['show_custom_1']) ? "Y": "N",
					isset($_POST['show_custom_2']) ? "Y": "N",
					isset($_POST['show_custom_3']) ? "Y": "N",
					isset($_POST['show_custom_4']) ? "Y": "N",
					isset($_POST['show_custom_5']) ? "Y": "N",
					isset($_POST['show_custom_6']) ? "Y": "N",		
					isset($_POST['show_window']) ? "Y": "N",
					isset($_POST['show_object']) ? "Y": "N",
					isset($_POST['show_memory_stats']) ? "Y": "N",
					isset($_POST['show_priority']) ? "Y": "N",
					isset($_POST['show_test_input']) ? "Y": "N",
					$req_docs,
					$test_docs,
					$test_run_docs,
					$test_plan_docs,
					$defect_docs );
		
} else {
	error_report_show( $add_page, UNABLE_TO_CREATE_PROJECT_FOLDERS );
}

// add new project to user settings
array_push( $_SESSION['s_user_projects'], $project_name );

session_validate_form_reset();

html_print_operation_successful( "project_add_page", $redirect_page );


# ------------------------------------
# $Log: project_add_action.php,v $
# Revision 1.5  2007/02/02 03:26:42  gth2
# make new project appear in the project list box when
# a new project is added - gth
#
# Revision 1.4  2006/12/05 04:57:21  gth2
# Allow users to rename project - gth
#
# Revision 1.3  2006/10/05 02:42:19  gth2
# adding file upload to the bug page - gth
#
# Revision 1.2  2006/02/06 13:08:20  gth2
# fixing minor bugs - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>