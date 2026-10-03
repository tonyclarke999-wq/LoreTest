<?php
session_start();
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Session API
#
# $RCSfile: session_api.php,v $ 
# $Revision: 1.22 $
# ------------------------------------


# ---------------------------------------------------------------------
# Initialize User Session Data
# ----------------------------------------------------------------------
function session_initialize() {

	# Session identifier
	$_SESSION['sessionID'] = time();

	# Javascript Enabled
	$_SESSION['javascript_enabled_browser'] = true;

	# Results page session variables
	$_SESSION['properties']['results']['build_id'] = null;
	$_SESSION['properties']['results']['release_id'] = null;
	$_SESSION['properties']['results']['testset_id'] = null;
	$_SESSION['properties']['results']['test_id'] = null;
	$_SESSION['properties']['results']['test_run_id'] = null;
	$_SESSION['properties']['results']['verify_id'] = null;
	# Release page session variables
	$_SESSION['properties']['release']['build_id'] = null;
	$_SESSION['properties']['release']['release_id'] = null;
	$_SESSION['properties']['release']['testset_id'] = null;
	$_SESSION['properties']['release']['test_id'] = null;
	$_SESSION['properties']['release']['test_run_id'] = null;
	$_SESSION['properties']['release']['verify_id'] = null;

	# Requirement
	$_SESSION['properties']['requirements']['discussion_id'] = null;
	$_SESSION['properties']['requirements']['req_id'] = null;
	$_SESSION['properties']['requirements']['req_version_id'] = null;

	# Results
	$_SESSION['properties']['results']['build_id'] = null;
	$_SESSION['properties']['results']['release_id'] = null;
	$_SESSION['properties']['results']['testset_id'] = null;
	$_SESSION['properties']['results']['test_id'] = null;
	$_SESSION['properties']['results']['test_run_id'] = null;
	$_SESSION['properties']['results']['verify_id'] = null;

	# Test Page
	$_SESSION['properties']['test']['test_id'] = null;
	$_SESSION['properties']['test']['test_version_id'] = null;

	# Bug Detail Page
	$_SESSION['properties']['bug']['bug_id'] = null;


	# Testset Copy Page
	$_SESSION['properties']['testset_copy']['build_id'] = null;
	$_SESSION['properties']['testset_copy']['release_id'] = null;
	$_SESSION['properties']['testset_copy']['testset_id'] = null;
	$_SESSION['properties']['testset_copy']['test_id'] = null;
	$_SESSION['properties']['testset_copy']['test_run_id'] = null;
	$_SESSION['properties']['testset_copy']['verify_id'] = null;
	$_SESSION['properties']['testset_copy']['testset_name'] = null;
	$_SESSION['properties']['testset_copy']['records'] = Array();
	$_SESSION['properties']['testset_copy']['select_group'] = Array();

	# Testset Edit Page
	$_SESSION['properties']['testset_copy_copy_testset']['build_id'] = null;
	$_SESSION['properties']['testset_copy_copy_testset']['release_id'] = null;
	$_SESSION['properties']['testset_copy_copy_testset']['testset_id'] = null;
	$_SESSION['properties']['testset_copy_copy_testset']['test_id'] = null;
	$_SESSION['properties']['testset_copy_copy_testset']['test_run_id'] = null;
	$_SESSION['properties']['testset_copy_copy_testset']['verify_id'] = null;
	$_SESSION['properties']['testset_copy_copy_testset']['testset_name'] = null;
	$_SESSION['properties']['testset_copy_copy_testset']['records'] = Array();
	$_SESSION['properties']['testset_copy_copy_testset']['select_group'] = Array();

	# Project Manage Page
	$_SESSION['properties']['project_manage']['area_id'] = null;
	$_SESSION['properties']['project_manage']['environment_id'] = null;
	$_SESSION['properties']['project_manage']['machine_id'] = null;
	$_SESSION['properties']['project_manage']['project_id'] = null;
	$_SESSION['properties']['project_manage']['req_doc_type_id'] = null;
	$_SESSION['properties']['project_manage']['test_doc_type_id'] = null;
	$_SESSION['properties']['project_manage']['user_id'] = null;
	$_SESSION['properties']['project_manage']['req_area_covered_id'] = null;
	$_SESSION['properties']['project_manage']['req_functionality_id'] = null;
	$_SESSION['properties']['project_manage']['test_type_id'] = null;
	$_SESSION['properties']['project_manage']['bug_category_id'] = null;
	$_SESSION['properties']['project_manage']['bug_component_id'] = null;

	# File Upload properties
	$_SESSION['properties']['project_properties']['test_upload_path'] = null;
	$_SESSION['project_properties']['req_upload_path']             = null;
    $_SESSION['project_properties']['test_upload_path']         = null;
    $_SESSION['project_properties']['test_run_upload_path'] = null;
	$_SESSION['project_properties']['test_plan_upload_path']	   = null;
	$_SESSION['project_properties']['defect_upload_path']	   = null;

	# User Edit Page
	$_SESSION['properties']['user_edit']['user_id'] = null;


	# Table display options
	#
	# NOTE: disp1ay_0ptions is spelt with NUMBERS, this is to make sure the
	#       session variable does not get overwritten by another variable
	#       with the same name.
	$_SESSION['disp1ay_opti0ns']['admin']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['admin']['order_by'] = PROJ_NAME;
	$_SESSION['disp1ay_opti0ns']['admin']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['build']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['build']['order_by'] = BUILD_DATE_REC;
	$_SESSION['disp1ay_opti0ns']['build']['order_dir'] = 'DESC';
	$_SESSION['disp1ay_opti0ns']['project_manage_users']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_manage_users']['order_by'] = USER_UNAME;
	$_SESSION['disp1ay_opti0ns']['project_manage_users']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['project_manage_areas']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_manage_areas']['order_by'] = AREA_TESTED_NAME;
	$_SESSION['disp1ay_opti0ns']['project_manage_areas']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['project_manage_environment']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_manage_environment']['order_by'] = ENVIRONMENT_NAME;
	$_SESSION['disp1ay_opti0ns']['project_manage_environment']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['project_manage_machines']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_manage_machines']['order_by'] = MACH_NAME;
	$_SESSION['disp1ay_opti0ns']['project_manage_machines']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['project_manage_req_doc_type']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_manage_req_doc_type']['order_by'] = REQ_DOC_TYPE_NAME;
	$_SESSION['disp1ay_opti0ns']['project_manage_req_doc_type']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['project_manage_req_functionality']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_manage_req_functionality']['order_by'] = REQ_FUNCT_NAME;
	$_SESSION['disp1ay_opti0ns']['project_manage_req_functionality']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['project_manage_req_area_covered']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_manage_req_area_covered']['order_by'] = REQ_AREA_COVERAGE;
	$_SESSION['disp1ay_opti0ns']['project_manage_req_area_covered']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['project_manage_testtype']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_manage_testtype']['order_by'] = TEST_TYPE_TYPE;
	$_SESSION['disp1ay_opti0ns']['project_manage_testtype']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['project_manage_test_doc_type']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_manage_test_doc_type']['order_by'] = MAN_DOC_TYPE_NAME;
	$_SESSION['disp1ay_opti0ns']['project_manage_test_doc_type']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['project_archive_tests']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['project_archive_tests']['order_by'] = TEST_NAME;
	$_SESSION['disp1ay_opti0ns']['project_archive_tests']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['release']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['release']['order_by'] = RELEASE_DATE_RECEIVED;
	$_SESSION['disp1ay_opti0ns']['release']['order_dir'] = 'DESC';
	$_SESSION['disp1ay_opti0ns']['report_area_tested']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['report_area_tested']['order_by'] = AREA_TESTED_NAME;
	$_SESSION['disp1ay_opti0ns']['report_area_tested']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['report_build_status']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['report_build_status']['order_by'] = BUILD_DATE_REC;
	$_SESSION['disp1ay_opti0ns']['report_build_status']['order_dir'] = 'ASC';
	# Requirements
	$_SESSION['disp1ay_opti0ns']['requirements']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['requirements']['order_by'] = REQ_FILENAME;
	$_SESSION['disp1ay_opti0ns']['requirements']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['requirements']['tab'] = 1;
	# Results
	$_SESSION['disp1ay_opti0ns']['results']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['results']['order_by'] = TEST_NAME;
	$_SESSION['disp1ay_opti0ns']['results']['order_dir'] = 'ASC';
	# Bug
	$_SESSION['disp1ay_opti0ns']['bug']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['bug']['order_by'] = BUG_ID;
	$_SESSION['disp1ay_opti0ns']['bug']['order_dir'] = 'DESC';
	# Screen
	$_SESSION['disp1ay_opti0ns']['screen']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['screen']['order_by'] = SCREEN_ORDER;
	$_SESSION['disp1ay_opti0ns']['screen']['order_dir'] = 'ASC';
	# Field
	$_SESSION['disp1ay_opti0ns']['field']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['field']['order_by'] = FIELD_NAME;
	$_SESSION['disp1ay_opti0ns']['field']['order_dir'] = 'ASC';
	$_SESSION['disp1ay_opti0ns']['field']['filter']['filter_screen'] = "";
	$_SESSION['disp1ay_opti0ns']['field']['filter']['filter_search'] = "";
	# Test
	$_SESSION['disp1ay_opti0ns']['test']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['test']['order_by'] = TEST_NAME;
	$_SESSION['disp1ay_opti0ns']['test']['order_dir'] = 'ASC';
	# Test Detail
	$_SESSION['disp1ay_opti0ns']['test_detail']['tab'] = 1;
	$_SESSION['disp1ay_opti0ns']['test_detail']['page_number'] = 1;
	# Test Req Assoc
	$_SESSION['disp1ay_opti0ns']['test_req_assoc']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['test_req_assoc']['order_by'] = REQ_FILENAME;
	$_SESSION['disp1ay_opti0ns']['test_req_assoc']['order_dir'] = 'ASC';
	# Test Run Results
	$_SESSION['disp1ay_opti0ns']['test_run_results']['page_number'] = '1';
	# Testset
	$_SESSION['disp1ay_opti0ns']['testset']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['testset']['order_by'] = TS_TBL.".".TS_DATE_CREATED;
	$_SESSION['disp1ay_opti0ns']['testset']['order_dir'] = 'DESC';
	# Testset Copy
	$_SESSION['disp1ay_opti0ns']['testset_copy']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['testset_copy']['order_by'] = TEST_NAME;
	$_SESSION['disp1ay_opti0ns']['testset_copy']['order_dir'] = 'ASC';
	# Testset Copy Copy???
	$_SESSION['disp1ay_opti0ns']['testset_copy_copy_testset']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['testset_copy_copy_testset']['order_by'] = TEST_NAME;
	$_SESSION['disp1ay_opti0ns']['testset_copy_copy_testset']['order_dir'] = 'ASC';
	# Testset Add Tests
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['order_by'] = TEST_NAME;
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['order_dir'] = 'ASC';
	# Testset Edit
	$_SESSION['disp1ay_opti0ns']['testset_edit']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['testset_edit']['order_by'] = TEST_NAME;
	$_SESSION['disp1ay_opti0ns']['testset_edit']['order_dir'] = 'ASC';
	# TestSuite (NEEDED???)
	$_SESSION['disp1ay_opti0ns']['testsuite']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['testsuite']['order_by'] = TEST_TESTTYPE;
	$_SESSION['disp1ay_opti0ns']['testsuite']['order_dir'] = 'ASC';
	# Test Workflow
	$_SESSION['disp1ay_opti0ns']['test_workflow']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['test_workflow']['order_by'] = TEST_NAME;
	$_SESSION['disp1ay_opti0ns']['test_workflow']['order_dir'] = 'ASC';
	# User Edit
	$_SESSION['disp1ay_opti0ns']['user_edit']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['user_edit']['order_by'] = PROJ_NAME;
	$_SESSION['disp1ay_opti0ns']['user_edit']['order_dir'] = 'ASC';
	# User Manage
	$_SESSION['disp1ay_opti0ns']['user_manage']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['user_manage']['order_by'] = USER_UNAME;
	$_SESSION['disp1ay_opti0ns']['user_manage']['order_dir'] = 'ASC';
	# Test Steps
	$_SESSION['disp1ay_opti0ns']['test_steps']['order_by'] = TEST_STEP_NO;
	$_SESSION['disp1ay_opti0ns']['test_steps']['order_dir'] = 'ASC';
	# requirement assoc
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['page_number'] = 1;
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['order_by'] = REQ_TBL.".".REQ_FILENAME;
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['order_dir'] = 'ASC';
	#requirement assoc filter
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['filter']['per_page'] = 25;
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['filter']['doc_type'] = "";
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['filter']['status']= "";
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['filter']['area_covered']= "";
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['filter']['functionality']= "";
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['filter']['assign_release']= "";
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['filter']['requirement_search']= "";
	$_SESSION['disp1ay_opti0ns']['requirement_assoc']['filter']['priority'] = "";

	# Filter options
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['per_page'] = RECORDS_PER_PAGE_REQUIREMENTS;
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['manual_auto'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['status'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['area_covered'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['functionality'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['assign_release'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['doc_type'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['priority'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['show_versions'] = "latest";
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['requirement_search'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements']['filter']['tree'] = array();
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['per_page'] = 0;
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['manual_auto'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['status'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['area_covered'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['functionality'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['assign_release'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['doc_type'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['priority'] = "";
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['show_versions'] = "latest";
	$_SESSION['disp1ay_opti0ns']['requirements_folder_view']['filter']['requirement_search'] = "";
	# Results
	$_SESSION['disp1ay_opti0ns']['results']['filter']['per_page']= 25;
	$_SESSION['disp1ay_opti0ns']['results']['filter']['manual_auto'] = "";
	$_SESSION['disp1ay_opti0ns']['results']['filter']['ba_owner'] = "";
	$_SESSION['disp1ay_opti0ns']['results']['filter']['qa_owner']= "";
	$_SESSION['disp1ay_opti0ns']['results']['filter']['tester']= "";
	$_SESSION['disp1ay_opti0ns']['results']['filter']['test_status']= "";
	$_SESSION['disp1ay_opti0ns']['results']['filter']['test_type']= "";
	$_SESSION['disp1ay_opti0ns']['results']['filter']['area_tested']= "";

	# Bugs
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['per_page']= 25;
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['status'] = "";
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['category'] = "";
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['component'] = "";
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['reported_by'] = "";
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['assigned_to'] = "";
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['assigned_to_developer'] = "";
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['found_in_release'] = "";
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['assigned_to_release'] = "";
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['view_closed'] = "No";
	$_SESSION['disp1ay_opti0ns']['bug']['filter']['bug_search'] = "";
	
	#Testsetfilter
	$_SESSION['disp1ay_opti0ns']['testset']['filter']['build_name'] = "";
	$_SESSION['disp1ay_opti0ns']['testset']['filter']['release_name'] = "";
	$_SESSION['disp1ay_opti0ns']['testset']['filter']['per_page'] = 25;

	# Test
	$_SESSION['disp1ay_opti0ns']['test']['filter']['per_page']= 25;
	$_SESSION['disp1ay_opti0ns']['test']['filter']['manual_auto'] = "";
	$_SESSION['disp1ay_opti0ns']['test']['filter']['ba_owner'] = "";
	$_SESSION['disp1ay_opti0ns']['test']['filter']['qa_owner']= "";
	$_SESSION['disp1ay_opti0ns']['test']['filter']['tester']= "";
	$_SESSION['disp1ay_opti0ns']['test']['filter']['test_type']= "";
	$_SESSION['disp1ay_opti0ns']['test']['filter']['area_tested']= "";
	$_SESSION['disp1ay_opti0ns']['test']['filter']['test_status']= "";
	$_SESSION['disp1ay_opti0ns']['test']['filter']['priority']= "";
	$_SESSION['disp1ay_opti0ns']['test']['filter']['test_search']= "";
	# Test Workflow
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['per_page']= 25;
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['manual_auto'] = "";
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['ba_owner'] = "";
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['qa_owner']= "";
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['tester']= "";
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['area_tested']= "";
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['test_status']= "";
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['test_type']= "";
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['priority']= "";
	$_SESSION['disp1ay_opti0ns']['test_workflow']['filter']['test_search']= "";
	# Testset Add
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['filter']['manual_auto'] = "";
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['filter']['ba_owner'] = "";
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['filter']['qa_owner']= "";
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['filter']['tester']= "";
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['filter']['test_type']= "";
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['filter']['area_tested']= "";
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['filter']['priority']= "";
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['filter']['test_search']= "";
	$_SESSION['disp1ay_opti0ns']['testset_add_tests']['filter']['per_page']= RECORDS_PER_PAGE_TESTSET_ADD;
	# Testset Copy
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['manual_auto'] = "";
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['ba_owner'] = "";
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['qa_owner']= "";
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['tester']= "";
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['test_type']= "";
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['area_tested']= "";
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['test_status']= "";
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['priority']= "";
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['test_search']= "";
	$_SESSION['disp1ay_opti0ns']['testset_copy']['filter']['per_page']= RECORDS_PER_PAGE_TESTSET_EDIT;
	# Testset Edit
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['manual_auto'] = "";
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['ba_owner'] = "";
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['qa_owner']= "";
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['tester']= "";
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['test_type']= "";
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['area_tested']= "";
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['test_status']= "";
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['priority']= "";
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['test_search']= "";
	$_SESSION['disp1ay_opti0ns']['testset_edit']['filter']['per_page']= RECORDS_PER_PAGE_TESTSET_EDIT;

	# Validate Form
	$_SESSION['validate_form'] = null;

	# Upload Paths - these are already stored in the project_properties
	#$_SESSION['upload_paths']['test_plan_upload_path'] = '';

}

# ----------------------------------------------------------------------------------
# -------------------------- SESSION RECORDS FUNCTIONS -----------------------------
# ----------------------------------------------------------------------------------

# ----------------------------------------------------------------------------------
# Function to store checked records across multiple pages and filters.
# When initially called, the function needs to be supplied with a variable name to
# store information in the session ($property_set).
#
# Optional parameters are records initially selected and groups of records initially
# selected when the page first loads.
#
# ----------------------------------------------------------------------------------
function session_records(	$property_set,
							$initial_records_selected=array(),
							$initial_record_groups_selected=array() ) {
	# If data is posted, then the user has moved to a different page
	# or has applied a filter. The function will now get the list of
	# records from the previous page and the list of records with
	# a check beside them.
	if( sizeof($_POST) ) {

		# initialise records string
		$records = $_POST['records'];

		# initialise record_groups string
		if( isset($_POST['record_groups']) ) {

			$record_groups = $_POST['record_groups'];
		} else {

			$record_groups = "";
		}

		# save the records displayed on the previous page
		session_records_set_displayed(	$property_set,
										$records,
										$record_groups );

		# save the records selected on the previous page
		session_records_set_selected($property_set);

	# Else if data is not posted, then the user has loaded the page for the
	# first time. The function will store any records that are initially
	# selected.
	} else {

		# save the records which are selected when the page first loads
		session_records_set_initial(	$property_set,
										$initial_records_selected );
	}
}

# ----------------------------------------------------------------------------------
# Saves the records which are initially selected when the page first loads.
# ----------------------------------------------------------------------------------
function session_records_set_initial( 	$property_set,
										$initial_records_selected=array(),
										$initial_record_groups_selected=array() ) {

	# Initialise the displayed records array
	$_SESSION['properties'][$property_set]['displayed_records']		= array();
	$_SESSION['properties'][$property_set]['displayed_select_group']= array();

	# Initialise the selected records array
	$_SESSION['properties'][$property_set]['records']		= $initial_records_selected;
	$_SESSION['properties'][$property_set]['select_group']	= $initial_record_groups_selected;

	# Uncomment to debug
	/*
	print"session_records_set_initial<br>";
	print"<textarea cols=100 rows=30>";
	print"property_set = $_SESSION[properties][$property_set]";
	print"". NEWLINE;
	print"". NEWLINE;
	print"initial_records_selected = ";
	print_r($initial_records_selected);
	print"". NEWLINE;
	print"initial_groups_selected = ";
	print_r($initial_record_groups_selected);
	print"</textarea>";
	*/
}

# ----------------------------------------------------------------------------------
# This function sets the records which were displayed on the previous page.
#
# INPUT:
#   property_set, session variable name
#   records_displayed, records displayed on previous page
#   record_groups_displayed, record groups on the previous page
# ----------------------------------------------------------------------------------
function session_records_set_displayed( $property_set,
										$records_displayed,
										$record_groups_displayed ) {

	# Create arrays from strings
	$displayed_records_array		= "return Array(". stripslashes($records_displayed)	   .");";
	$displayed_record_groups_array	= "return Array(". stripslashes($record_groups_displayed).");";

	$_SESSION['properties'][$property_set]['displayed_records'] =
		eval($displayed_records_array);

	$_SESSION['properties'][$property_set]['displayed_select_group'] =
		eval($displayed_record_groups_array);

	# Uncomment to debug
	/*
	print"session_records_set_displayed<br>";
	print "<br>";
	print"<textarea cols=100 rows=30>";
	print_r($_SESSION['properties'][$property_set]);
	print"\n\$_POST:";
	print_r($_POST);
	print"</textarea>";
	*/

}

# ----------------------------------------------------------------------------------
# Determines which records a user selected on previous page.
#
# The variables are stored in a way as to reduce the amount of memory
# needed from the server and also speed up the process of determining if a record
# is selected.
#
# Variables used as follows:
#
# Two arrays: [records]= Array(record_id1 => group_name, record_id2 => group_name, ...) and
#             [groups] = Array(group_name1, group_name2, ...)
#
# If a record is set and its group is not set --> checked
# e.g. [records]= Array(1 => WIP, 2 => WIP)
#      [groups] = Array(Finished)
#      Records 1 and 2 are checked
#
# If a record is set and its group is also set --> not checked
# e.g. [records]= Array(1 => WIP, 2 => WIP)
#      [groups] = Array(WIP)
#      All of group WIP are checked, except records 1 and 2
#
# If a record is not set and its group is also not set --> not checked
# e.g. [records]= Array(1 => WIP, 2 => WIP)
#      [groups] = Array(Finished)
#      Any records of group Not Started, would not be checked
#
# If a record is not set and its group is set --> checked
# e.g. [records]= Array(1 => WIP, 2 => WIP)
#      [groups] = Array(Finished)
#      Any records of group Finished would be checked
#
#
# INPUT:
#	$property_set: session variable name used to save the data
#	rest of the input variables are taken from $_POST
#
# ----------------------------------------------------------------------------------
function session_records_set_selected( $property_set ) {

	//print"session_records_set_selected<br>";

	//print_r($_SESSION['properties'][$property_set]['records']);

	####################################################################################################
	# get all the records displayed on the page

	$displayed_records	= $_SESSION['properties'][$property_set]['displayed_records'];
	$displayed_groups	= $_SESSION['properties'][$property_set]['displayed_select_group'];

	####################################################################################################

	####################################################################################################
	# set the posted variables

	# define posted variables
	$posted_records = array();
	$posted_groups = array();

	foreach($_POST as $key => $value) {

		# $posted_records[record_id] = record_group
		$exploded_post = explode( "_", $key, 3 );

		if( $exploded_post[0]=="row" ) {
			$posted_records[$exploded_post[1]] = $value;
		}

		# $posted_groups = (group_name1, group_name2, ...)
		$exploded_post = explode( "_", $key, 2 );

		if( $exploded_post[0]=="allpages" ) {

			$posted_groups[] = $value;
		}

	}
	//print_r($posted_groups);exit;
	####################################################################################################

	//print_r($posted_records);
	//print_r($posted_groups);
	//print_r($displayed_groups);

	####################################################################################################
	# set the select_group variables

	# loop through all groups displayed on the page
	foreach($displayed_groups as $displayed_group_name) {

		# if $displayed_group_name checked on page
		$posted_group_name_match = util_array_value_search($displayed_group_name, $posted_groups);

		//print $posted_group_name_match.":".$displayed_group_name."<br>";

		if( $posted_group_name_match ) {
/*
			# add $displayed_group_name to group_name
			$_SESSION['properties'][$property_set]['select_group'][] = $displayed_group_name;

			//print_r($_SESSION['properties'][$property_set]['select_group']);

*/
			# check if group was selected on previous page, and if not
			if( !util_array_value_search($displayed_group_name, $_SESSION['properties'][$property_set]['select_group']) ) {
				# add $displayed_group_name to group_name
				$_SESSION['properties'][$property_set]['select_group'][] = $displayed_group_name;

				# remove any tests in session of that group
				foreach( $_SESSION['properties'][$property_set]['records'] as $record_id => $group_name ) {
					if( $group_name==$displayed_group_name ) {
						//print"unset $record_id<br>";
						unset( $_SESSION['properties'][$property_set]['records'][$record_id] );
					}
				}
			}


			# remove all records of that group but only if they have been posted
			foreach( $posted_records as $record_id => $group_name ) {
				if( $group_name==$displayed_group_name ) {
					//print"unset $record_id<br>";
					unset( $_SESSION['properties'][$property_set]['records'][$record_id] );
				}
			}
		} else {

			# remove $displayed_group_name from select_group
			$_SESSION['properties'][$property_set]['select_group'] =
				array_diff($_SESSION['properties'][$property_set]['select_group'], Array("$displayed_group_name"));

		}

	}
	####################################################################################################

//print_r($_SESSION['properties'][$property_set]['select_group']);
//print">select group<br><br>";

	####################################################################################################
	# set the records variables

	# loop through displayed records
	foreach( $displayed_records as $displayed_record_id => $displayed_record_group ) {

		$displayed_record_group_match
			= util_array_value_search($displayed_record_group, $_SESSION['properties'][$property_set]['select_group']);

			//print"<br>search_value: $displayed_record_group, restult: $displayed_record_group_match<br>";print_r($_SESSION['properties'][$property_set]['select_group']);print"<br>";

		$posted_records_id_match = util_array_key_search($displayed_record_id, $posted_records);

		if( $displayed_record_group_match ) {
			//print"displayed_record_group_match<BR>";

			# if posted_record id matches $displayed_record id
			if( $posted_records_id_match ) {
				# remove id from records
				unset( $_SESSION['properties'][$property_set]['records'][$displayed_record_id] );
			} else {
				# add id
				$_SESSION['properties'][$property_set]['records'][$displayed_record_id] = "$displayed_record_group";
			}

		} else {
			//print"false displayed_record_group_match<BR>";

			if( $posted_records_id_match ) {

				$_SESSION['properties'][$property_set]['records'][$displayed_record_id] = "$displayed_record_group";
			} else {
				unset( $_SESSION['properties'][$property_set]['records'][$displayed_record_id] );
			}
		}
	}

	####################################################################################################
/*
	print "<br>";
	print"<textarea cols=100 rows=30>";
	print_r($_SESSION['properties'][$property_set]);
	print"". NEWLINE;
	print"\n\$_POST:";
	print_r($_POST);
	print"</textarea>";
*/
}

# ----------------------------------------------------------------------------------
# Returns whether a record in the session is checked or not
#
# INPUT:
#	property set
#	test id
#	group the record belongs to
# OUTPUT:
#	true or false
# ----------------------------------------------------------------------------------
function session_records_ischecked( $property_set, $test_id, $group=null ) {

	$test_is_set = isset( $_SESSION['properties'][$property_set]['records'][$test_id] );

	if( is_null($group) ) {
		return	$test_is_set;
	} else {
		print($group_is_set = session_records_ischecked_group( $property_set, $group ));

		return	( ( $test_is_set && !$group_is_set ) ||
				  (!$test_is_set &&  $group_is_set ) );
	}
}

# ----------------------------------------------------------------------------------
# Returns whether a record group in the session is selected or not
#
# INPUT:
#	property set
#	group name
# OUTPUT:
#	true or false
# ----------------------------------------------------------------------------------
function session_records_ischecked_group( $property_set, $group ) {

	$is_checked = util_array_value_search($group, $_SESSION['properties'][$property_set]['select_group']);

	return $is_checked;
}

# ----------------------------------------------------------------------------------
# ---------------------------- END SESSION RECORDS ---------------------------------
# ----------------------------------------------------------------------------------

# ----------------------------------------------------------------------------------
# Sets the properties for a given property set, e.g. $_SESSION['properties'][$property_set].
# The function checks the $properties array (typically $_GET) for each property set
# variable.
#
# All $_SESSION['properties'][$property_set] variables, should be defined in
# session_initialize().
#
# The release_id, build_id and testset_id properties are set slightly different because of
# html_test_results_menu. These variables have to be unset depending on which of these
# variables are in the $properties array.
#
# If you what to store a new session property, define it after release id, build id and
# testset id.
#
# INPUT:
#   release_id
#   build_id
#   testset_id
#   test_run_id
#   verify_id
# OUTPUT:
#   $_SESSION['properties'][$property_set]
# ----------------------------------------------------------------------------------
function session_set_properties( $property_set, $properties=null ) {

	# Set the properties.
	foreach($_SESSION['properties'][$property_set] as $key => $value) {

		if( !empty($properties[$key]) ) {
			$_SESSION['properties'][$property_set][$key] = $properties[$key];
		}
	}

	# Set/Unset release_id, build_id, and testset_id
    if( !empty($properties['release_id']) ) {
		//print"setting release_id<br>";
        $_SESSION['properties'][$property_set]['release_id'] = $properties['release_id'];
        $_SESSION['properties'][$property_set]['build_id'] = "";
        $_SESSION['properties'][$property_set]['testset_id'] = "";
        if( $_SESSION['properties'][$property_set]['release_id'] == 'all' ) {
            $_SESSION['properties'][$property_set]['release_id'] = "";
        }
    }
    if( !empty($properties['build_id']) ) {
        $_SESSION['properties'][$property_set]['build_id'] = $properties['build_id'];
        $_SESSION['properties'][$property_set]['testset_id'] = "";
        if( $_SESSION['properties'][$property_set]['build_id'] == 'all' ) {
            $_SESSION['properties'][$property_set]['build_id'] = "";
        }
    }
    if( !empty($properties['testset_id']) ) {
        $_SESSION['properties'][$property_set]['testset_id'] = $properties['testset_id'];
        if( $_SESSION['properties'][$property_set]['testset_id'] == 'all' ) {
	    	$_SESSION['properties'][$property_set]['testset_id'] = "";
        }
    }

	# adding to fix problem with results page
	if( !empty($properties['test_id']) ) {
        $_SESSION['properties'][$property_set]['test_id'] = $properties['test_id'];

        if( $_SESSION['properties'][$property_set]['test_id'] == 'none' ) {
	    	$_SESSION['properties'][$property_set]['test_id'] = "";
        }
    }

    return $_SESSION['properties'][$property_set];
}

function session_reset_properties( $property_set ) {

	$_SESSION['properties'][$property_set] = null;
}

# ----------------------------------------------------------------------------------
# Sets table and filter display options for a given element and returns the the values
# as an array. Display options are only set if the key is set in $options.
#
# All $_SESSION[$page] variables, should be defined in
# session_initialize().
#
# INPUT:
#   page_number
#   order_by
#   manual_auto
#   ba_owner
#   scripter
#   test_type
#   area_tested
#   test_status
#   per_page
#
# OUTPUT:
#   Array of element display options
# ----------------------------------------------------------------------------------
function session_set_display_options( $element, $options ) {

    #
    # Filter Options
    #

    $s_filter =& $_SESSION['disp1ay_opti0ns'][$element]['filter'];

	if( isset($s_filter) ) {
		foreach($s_filter as $key => $value) {

			if( isset($options[$key]) ){
				$s_filter[$key] = $options[$key];
			}
		}
	}

	# per page
	if( isset($options['per_page']) ){
		$s_filter['per_page'] = util_per_page( $options['per_page'] );
	}

	#
	# Display Options
	#

	#$s_display_options = $_SESSION['disp1ay_opti0ns'][$element];

	# set page number
    util_set_page_number(&$_SESSION['disp1ay_opti0ns'][$element]['page_number'], $options);
    if(!empty($options['order_by'])){
    	$_SESSION['disp1ay_opti0ns'][$element]['order_by'] = $options['order_by'];
    }
    if(!empty($options['order_dir'])){
    	$_SESSION['disp1ay_opti0ns'][$element]['order_dir'] = $options['order_dir'];
    }
    #util_set_order_by(@&$_SESSION['disp1ay_opti0ns'][$element]['order_by'], $options);
    #util_set_order_dir(@&$_SESSION['disp1ay_opti0ns'][$element]['order_dir'], $options);

	# tabs
	if( isset($options['tab']) && $options['tab'] != '' ) {
		$_SESSION['disp1ay_opti0ns'][$element]['tab'] = $options['tab'];
	}

    return $_SESSION['disp1ay_opti0ns'][$element];
}


# ----------------------------------------------------------------------------------
# Accessor functions
# ----------------------------------------------------------------------------------

function session_get_properties( $property_set ) {

	$var = $_SESSION['properties'][$property_set];

	return $var;
}

function session_get_display_options( $table_set ) {
	return $_SESSION['disp1ay_opti0ns'][$table_set];
}

function session_get_filter_options( $filter_set ) {
	return $_SESSION['disp1ay_opti0ns'][$filter_set]['filter'];
}

# ----------------------------------------------------------------------------------
# --------------------------------- END ADMIN --------------------------------------
# ----------------------------------------------------------------------------------

# ----------------------------------------------------------------------------------
# ------------------------------- VALIDATE FORMS -----------------------------------
# ----------------------------------------------------------------------------------
# How to Validate a Form
# ----------------------
#
# 1. In the form you want to validate, set the field value property equal to
#    session_validate_form_get_field( $form_field_name, $origional_value ),
#
#    $form_field_name is the name of the form field, required fields have the
#    suffix "_required"
#    $origional_value is optional, it is specified where data is preloaded into a
#    form as the page loads. e.g. release_edit_page.php
#
# 2. In the file to which the form will be submitted, insert function
#    session_validate_form_set( $form_vars, $redirect_page ) before any code which will use the
#    submitted form values.
#
#    $redirect_page is the page where the data is submitted from.
#
# 3. Any fields needed in this file should be accessed using the function
#    session_validate_form_get_field( $field_name )
#
# 4. After you are finished using these fields, add the function
#    session_validate_form_reset()
#
# 5. If the page on which the form lies, preloads data into the form,
#    put the function session_validate_form_reset() at the bottom of the
#    previous/next page in the flow, this will reset any saved input overwriting,
#    origional values.

# -------------------------------------------------------------------------------
# This function basically cleans and stores POST variables in the session.
# It calls error_report_show() if a required field is empty or POST is empty.
#
# A required field has the suffix "_required"
# -------------------------------------------------------------------------------
function session_validate_form_set( $form_vars, $redirect_page=null ) {

	$required_field_empty = false;

	if( empty($form_vars) && $redirect_page ) {

		error_report_show( $redirect_page, REQUIRED_FIELD_MISSING );
	} else {

		//$_SESSION['validate_form'] = array();

		foreach($form_vars as $key => $value) {

			# for multiple select
			if( is_array($value) ) {
				$_SESSION['validate_form'][$key] = $value;

			# when not a multiple select
			} else {
				//$value = htmlspecialchars($value, ENT_QUOTES);
				$value = util_strip_slashes($value);
				
				$_SESSION['validate_form'][$key] = $value;

				$trimmed_value = trim($value);
				
				if( strpos($key, '_required') && empty($trimmed_value) ) {

					$required_field_empty = true;
				}
			}
		}

		if($required_field_empty && $redirect_page) {

			error_report_show( $redirect_page, REQUIRED_FIELD_MISSING );
		}
	}
}

# -------------------------------------------------------------------------------
# This function sets the session validation variable to null
# -------------------------------------------------------------------------------
function session_validate_form_reset() {
	$_SESSION['validate_form'] = null;
}

# ----------------------------------------------------------------------------------
# Returns field key from session validation variable if it has been set, otherwise
# it returns $origional_value
#
# $origional_value should be passed on pages that preload data from the database.
#
# ----------------------------------------------------------------------------------
function session_validate_form_get_field( $field, $origional_value="", $using_FCKeditor=false ) {

	# If $field is not empty
	if( !empty($_SESSION['validate_form'][$field]) ) {

		# If $field is a string, convert special characters to html
		if( is_string($_SESSION['validate_form'][$field]) && !$using_FCKeditor ) {
			#added mysql_real_escape_string() to enable writing backslashes in all fields
			$return_value = mysql_real_escape_string(htmlspecialchars($_SESSION['validate_form'][$field], ENT_QUOTES));

		# Else just return $field
		} else {

			$return_value = $_SESSION['validate_form'][$field];

			# replace ' with &#039; because ' will break a query string
			$return_value = preg_replace("/'/", "&#039;", $return_value );
		}

	# If $field is empty, return $origional_value
	} else {

		$return_value = $origional_value;
	}

	return $return_value;
}

# ----------------------------------------------------------------------------------
# ------------------------------- END VALIDATE FORMS -------------------------------
# ----------------------------------------------------------------------------------


function session_get_upload_path( $upload_category ) {

	return $_SESSION['upload_paths'][$upload_category];
}

# ----------------------------------------------------------------------------------
# -------------------------- CHECK JAVASCRIPT ENABLED ------------------------------
# ----------------------------------------------------------------------------------
function session_set_javascript_enabled( $enabled ) {

	$_SESSION['javascript_enabled_browser'] = $enabled;
}

function session_use_javascript() {

	return USE_JAVASCRIPT && $_SESSION['javascript_enabled_browser'];
}
# ----------------------------------------------------------------------------------
# ------------------------- END CHECK JAVASCRIPT ENABLED ---------------------------
# ----------------------------------------------------------------------------------

# Check if FCKeditor can be used
# Returns true if the user is able to run JavaScript and USE_FCK_EDITOR is true
function session_use_FCKeditor() {

	return session_use_javascript() && USE_FCK_EDITOR;
}

# ----------------------------------------------------------------------
# Set session variable to indicate user has successfully logged in
# ----------------------------------------------------------------------
function session_setLogged_in( $logged_in_indicator ) {
    $_SESSION['logged_in'] = $logged_in_indicator;
}


# ----------------------------------------------------------------------
# Get session variable to indicate whether user has successfully
# logged in
# ----------------------------------------------------------------------
function session_getLogged_in() {

    if (isset($_SESSION['logged_in'])) {
        return true;
    } else {
        return false;
    }
}

# ----------------------------------------------------------------------
# Set Session user properties
# ----------------------------------------------------------------------
function session_set_user_properties( $user_id, $username, $tempest_admin, $project_rights, $delete_rights, $email ) {
    $_SESSION['user_properties']['user_id']			= $user_id;
    $_SESSION['user_properties']['username']		= $username;
    $_SESSION['user_properties']['tempest_admin']	= $tempest_admin;
    $_SESSION['user_properties']['project_rights']	= $project_rights;
    $_SESSION['user_properties']['delete_rights']	= $delete_rights;
	$_SESSION['user_properties']['email']			= $email;
}


# ----------------------------------------------------------------------
# Get Session user properties
# ----------------------------------------------------------------------
function session_get_user_properties() {
    return $_SESSION['user_properties'];
}


# ----------------------------------------------------------------------
# Get Session user name
# ----------------------------------------------------------------------
function session_get_username() {

    return $_SESSION['user_properties']['username'];
}

function session_set_user_projects() {

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;
	$f_proj_deleted			= $tbl_project .".". PROJ_DELETED;

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_username				= $tbl_user .".". USER_UNAME;
	$f_tempest_admin		= $tbl_user .".". USER_ADMIN;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;

	$username = session_get_username();

	global $db;

	$q = "	SELECT	$f_tempest_admin
			FROM	$tbl_user
			WHERE	$f_username = '$username'";

	# tempest administrator
	if( db_get_one($db, $q)=="Y" ) {

		$q = "	SELECT	$f_proj_name
				FROM	$tbl_project
				WHERE $f_proj_deleted = 'N'
				ORDER BY $f_proj_name ASC";
	} else {

		# tempest user
		$q = "	SELECT	$f_proj_name
				FROM	$tbl_project
				INNER JOIN $tbl_proj_user_assoc ON $f_proj_id = $f_proj_user_proj_id
				INNER JOIN $tbl_user ON $f_proj_user_user_id = $f_user_id
				WHERE $f_username = '$username'
				AND $f_proj_deleted = 'N'
				ORDER BY $f_proj_name ASC";
	}

	$rows = db_fetch_array($db, db_query($db, $q));

	$project_array = array();

	foreach($rows as $row) {
		$project_array[] = $row[PROJ_NAME];
	}

	$_SESSION['s_user_projects'] = $project_array;
}

function session_get_user_projects() {

    return $_SESSION['s_user_projects'];
}

function session_get_user_projects_excluding_current_project($current_project) {

    $projects = $_SESSION['s_user_projects'];
    $user_projects = array();
    foreach($projects as $project)
    {
    	if($project != $current_project)
    	{
    		array_push( $user_projects, $project );
    	}
    }
    return $user_projects; 
}

# ----------------------------------------------------------------------
# Set user properties on Session
# ----------------------------------------------------------------------
function session_set_new_project_name($project_name) {
    $_SESSION['project_properties']['project_name'] = $project_name;
}


# ----------------------------------------------------------------------
# Set project properties on Session
# ----------------------------------------------------------------------
function session_set_project_properties($project_name,
										$project_id,
										$req_upload_path,
										$test_upload_path,
										$test_run_upload_path,
										$test_plan_upload_path,
										$defect_upload_path,
										$bug_url ) {

    $_SESSION['project_properties']['project_name']					= $project_name;
    $_SESSION['project_properties']['project_id']					= $project_id;
    $_SESSION['project_properties']['req_upload_path']				= $req_upload_path;
    $_SESSION['project_properties']['test_upload_path']				= $test_upload_path;
    $_SESSION['project_properties']['test_run_upload_path']			= $test_run_upload_path;
	$_SESSION['project_properties']['test_plan_upload_path']		= $test_plan_upload_path;
	$_SESSION['project_properties']['defect_upload_path']			= $defect_upload_path;
    $_SESSION['project_properties']['bug_url']						= $bug_url;
}


# ----------------------------------------------------------------------
# Get Session project properties
# ----------------------------------------------------------------------
function session_get_project_properties() {
    return $_SESSION['project_properties'];
}

# ----------------------------------------------------------------------
# Get project name from Session
# ----------------------------------------------------------------------
function session_get_project_name() {
    $project_properties     = $_SESSION['project_properties'];
    $project_name           = $project_properties['project_name'];
    return $project_name;
}

# ----------------------------------------------------------------------
# Get project id from Session
# ----------------------------------------------------------------------
function session_get_project_id() {

	return $_SESSION['project_properties']['project_id'];

}

# ----------------------------------------------------------------------
# Blank Session project properties
# ----------------------------------------------------------------------
function session_reset_project() {
    $_SESSION['project_properties'] = '';
    $_SESSION['project_properties'] = '';
    $_SESSION['show_options'] = '';
}

# ----------------------------------------------------------------------
# ------------------- USER PROPERTIES ----------------------------------
# ----------------------------------------------------------------------

# ----------------------------------------------------------------------
# Set project and user properties on Session for user
# ----------------------------------------------------------------------
function session_set_application_details($project_name, $username) {

    $project_details = project_get_application_details($project_name, $username);

    session_set_user_properties($project_details[USER_ID],
    							$username,
    							$project_details[USER_ADMIN],
    							$project_details[PROJ_USER_PROJECT_RIGHTS],
    							$project_details[PROJ_USER_DELETE_RIGHTS],
								$project_details[USER_EMAIL]);

    session_set_project_properties(	$project_details[PROJ_NAME],
    								$project_details[PROJ_ID],
    								$project_details[PROJ_REQ_UPLOAD_PATH],
    								$project_details[PROJ_TEST_UPLOAD_PATH],
    								$project_details[PROJ_TEST_RUN_UPLOAD_PATH],
    								$project_details[PROJ_TEST_PLAN_UPLOAD_PATH],
									$project_details[PROJ_DEFECT_UPLOAD_PATH],
    								$project_details[PROJ_BUG_URL_UPLOAD_PATH]);

    session_set_show_options(	$project_details[PROJ_SHOW_CUSTOM_1],
    							$project_details[PROJ_SHOW_CUSTOM_2],
    							$project_details[PROJ_SHOW_CUSTOM_3],
								$project_details[PROJ_SHOW_CUSTOM_4],
								$project_details[PROJ_SHOW_CUSTOM_5],
    							$project_details[PROJ_SHOW_CUSTOM_6],
    							$project_details[PROJ_SHOW_WINDOW],
    							$project_details[PROJ_SHOW_OBJECT],
								$project_details[PROJ_SHOW_MEM_STATS],
								$project_details[PROJ_SHOW_PRIORITY] );

	session_set_user_projects();
}


# ----------------------------------------------------------------------
# Set show options on Session
# ----------------------------------------------------------------------
function session_set_show_options(
									$show_custom_1,
									$show_custom_2,
									$show_custom_3,
									$show_custom_4,
									$show_custom_5,
									$show_custom_6,
									$show_window,
									$show_object,
									$show_memory_stats,
									$show_priority ) {

    $_SESSION['show_options']['show_custom_1']      = $show_custom_1;
    $_SESSION['show_options']['show_custom_2']      = $show_custom_2;
    $_SESSION['show_options']['show_custom_3']      = $show_custom_3;
	$_SESSION['show_options']['show_custom_4']      = $show_custom_4;
	$_SESSION['show_options']['show_custom_5']      = $show_custom_5;
    $_SESSION['show_options']['show_custom_6']		= $show_custom_6;
    $_SESSION['show_options']['show_window']        = $show_window;
    $_SESSION['show_options']['show_object']        = $show_object;
    $_SESSION['show_options']['show_memory_stats']  = $show_memory_stats;
    $_SESSION['show_options']['show_priority']      = $show_priority;
}


# ----------------------------------------------------------------------
# Set show options on Session
# ----------------------------------------------------------------------
function session_get_show_options() {
    return $_SESSION['show_options'];
}


# ----------------------------------------------------------------------
# clear form values from session
# ----------------------------------------------------------------------
function session_clear_form_values() {
    $_SESSION['form_values'] = array();
}


# ----------------------------------------------------------------------
# Set form values using passed array
# ----------------------------------------------------------------------
function session_set_form_values ($form_values) {
    $_SESSION['form_values'] = $form_values;
}


# ----------------------------------------------------------------------
# Get form values
# ----------------------------------------------------------------------
function session_get_form_values () {
    return $_SESSION['form_values'];
}

# ----------------------------------------------------------------------
# Get form values
# ----------------------------------------------------------------------
function session_get_ID() {
    return $_SESSION['sessionID'];
}


# ----------------------------------------------------------------------
# End Session
# ----------------------------------------------------------------------
function session_end() {
    session_unset();
    session_destroy();
}


# ------------------------------------
# $Log: session_api.php,v $
# Revision 1.22  2008/08/04 06:55:01  peter_thal
# added sorting function to several tables
#
# Revision 1.21  2008/07/18 07:43:36  peter_thal
# fixed search filter bug in some testset php pages
#
# Revision 1.20  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.19  2008/07/11 07:05:53  peter_thal
# fixed order_by and order_dir bug and added a missing parameter to test_detail_page
#
# Revision 1.18  2008/07/03 09:30:27  peter_thal
# enabled writing and saving backslashes in all fields
#
# Revision 1.17  2008/01/22 09:46:12  cryobean
# added function for copy test feature
#
# Revision 1.16  2007/11/13 07:47:30  cryobean
# *** empty log message ***
#
# Revision 1.15  2007/03/17 02:11:13  gth2
# adding page_number to project page - gth
#
# Revision 1.14  2007/03/14 17:45:52  gth2
# removing code that passes varables by reference - gth
#
# Revision 1.13  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.12  2007/02/03 10:26:19  gth2
# no message
#
# Revision 1.11  2007/02/02 04:27:31  gth2
# correcting error with records per page when adding tests to a test set - gth
#
# Revision 1.10  2006/12/05 05:29:19  gth2
# updates for 1.6.1 release
#
# Revision 1.9  2006/10/05 02:42:18  gth2
# adding file upload to the bug page - gth
#
# Revision 1.8  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.7  2006/06/10 02:09:43  gth2
# no message
#
# Revision 1.6  2006/05/03 21:54:30  gth2
# adding screen and field to session - gth
#
# Revision 1.5  2006/02/06 13:08:21  gth2
# fixing minor bugs - gth
#
# Revision 1.4  2006/01/16 13:27:48  gth2
# adding excel integration - gth
#
# Revision 1.3  2006/01/09 02:02:24  gth2
# fixing some defects found while writing help file
#
# Revision 1.2  2005/12/13 13:59:54  gth2
# Completed the addition of requirement priority - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ------------------------------------
?>