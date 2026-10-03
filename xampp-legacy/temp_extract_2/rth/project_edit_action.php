<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Edit Action
#
# $RCSfile: project_edit_action.php,v $  $Revision: 1.2 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page			= 'project_manage_page.php';
$project_id				= $_POST['project_id'];
$update_project			= true;

session_validate_form_set($_POST, $redirect_page ); 
$project_details		= project_get_details( $project_id );
$current_project_name	= $project_details['project_name'];
$new_project_name		=  session_validate_form_get_field('project_name_required');


// change the project name in session and the file upload directories if the user has changed the project name
if( $current_project_name != $new_project_name  ) {

	
	// rename the file upload directories
	$old_req_docs_dir		= $project_details['req_upload_path'];
	$old_test_docs_dir		= $project_details['test_upload_path'];
	$old_test_run_docs_dir	= $project_details['test_run_upload_path'];
	$old_test_plan_docs_dir	= $project_details['test_plan_upload_path'];
	$old_defect_docs_dir	= $project_details['defect_upload_path'];

	$project_folder_name	= str_replace(" ", "", $new_project_name);
	$req_docs_dir			= FILE_UPLOAD_PATH .$project_folder_name."_req_docs/";
	$test_docs_dir			= FILE_UPLOAD_PATH .$project_folder_name."_test_docs/";
	$test_run_docs_dir		= FILE_UPLOAD_PATH .$project_folder_name."_test_run_docs/";
	$test_plan_docs_dir		= FILE_UPLOAD_PATH .$project_folder_name."_test_plan_docs/";
	$defect_docs_dir		= FILE_UPLOAD_PATH .$project_folder_name."_defect_docs/";

	if ( rename( $old_req_docs_dir, $req_docs_dir ) 
		 && rename($old_test_docs_dir, $test_docs_dir) 
		 && rename($old_test_run_docs_dir, $test_run_docs_dir) 
		 && rename($old_test_plan_docs_dir, $test_plan_docs_dir) 
		 && rename($old_defect_docs_dir, $defect_docs_dir) ) {

		// change the project name in the "change project" list box.  For projects other than the "current project"
		foreach( $_SESSION['s_user_projects'] as $key => $val ) {

			if( $val == $current_project_name ) {
				$_SESSION['s_user_projects'][$key] = $new_project_name; 
			}
		}

		// update session data if the user changes the name of the project that they're currently logged into (the "current project")
		if( $_SESSION['project_properties']['project_name'] == $current_project_name ) {

			$_SESSION['project_properties']['project_name'] = $new_project_name;
			$_SESSION['project_properties']['req_upload_path'] = $req_docs_dir;
			$_SESSION['project_properties']['test_upload_path'] = $test_docs_dir;
			$_SESSION['project_properties']['test_run_upload_path'] = $test_run_docs_dir;
			$_SESSION['project_properties']['test_plan_upload_path'] = $test_plan_docs_dir;
			$_SESSION['project_properties']['defect_upload_path'] = $defect_docs_dir;
		}

		// change file upload path
		project_edit_file_upload_path(	$project_id, 
										$req_docs_dir,
										$test_docs_dir,
										$test_run_docs_dir,
										$test_plan_docs_dir,
										$defect_docs_dir );
	}
	else {
		
		$update_project = false;
		error_report_show( $redirect_page, UNABLE_TO_CREATE_PROJECT_FOLDERS );

	}


}

// only update the project if changing the name of the project was successful
if( $update_project ) {

	project_edit(	$project_id, 
					session_validate_form_get_field('project_name_required'),
					session_validate_form_get_field('project_description'),
					$_POST['project_status'],
					isset($_POST[PROJ_SHOW_CUSTOM_1]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_CUSTOM_2]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_CUSTOM_3]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_CUSTOM_4]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_CUSTOM_5]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_CUSTOM_6]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_WINDOW]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_OBJECT]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_MEM_STATS]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_PRIORITY]) ? "Y": "N",
					isset($_POST[PROJ_SHOW_TEST_INPUT]) ? "Y": "N");

}



session_validate_form_reset();
html_print_operation_successful( "edit_project_page", $redirect_page );

# ------------------------------------
# $Log: project_edit_action.php,v $
# Revision 1.2  2006/12/05 04:57:21  gth2
# Allow users to rename project - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>