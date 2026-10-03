<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# CSV Export Page
#
# $RCSfile: csv_export.php,v $ $Revision: 1.5 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$csv = "";

switch ( $_GET['table'] ) {
	case "requirements":

		# columns to be included from the recordset and the
		# name to use in the header row
		$headers = 	array(	REQ_ID 					=> lang_get('req_id'),
							REQ_FILENAME 			=> lang_get('req_name'),
							REQ_VERS_FILENAME		=> lang_get('file_name'),
							REQ_VERS_DETAIL			=> lang_get('req_detail'),
							REQ_DOC_TYPE_NAME 		=> lang_get('req_type'),
							REQ_AREA_COVERAGE 		=> lang_get('req_area'),
							REQ_PRIORITY			=> lang_get('req_priority'),
							REQ_VERS_STATUS			=> lang_get('status'),
							REQ_VERS_VERSION		=> lang_get('version'),
							REQ_LOCKED_BY 			=> lang_get('req_locked_by'),
							REQ_LOCKED_DATE 		=> lang_get('req_locked_date'),
							RELEASE_NAME			=> lang_get('release_name'),
							REQ_FUNCT_NAME			=> lang_get('functionality')
			             );


		$display_options = session_get_display_options("requirements");

		$rows = requirement_get_for_export( 	$project_id,
												$page_number=0,
												$display_options['order_by'],
												$display_options['order_dir'],
												$display_options['filter']['doc_type'],
												$display_options['filter']['status'],
												$display_options['filter']['area_covered'],
												$display_options['filter']['functionality'],
												$display_options['filter']['assign_release'],
												$display_options['filter']['show_versions'],
												$per_page="",
												$display_options['filter']['requirement_search'],
												$display_options['filter']['priority']
										  );

		# add functionality information of each requirement
		foreach($rows as $row => $fields) {
			
			$rows_functions = requirement_get_functionality($project_id, $fields[REQ_ID]);
			$functionality = "";

			foreach($rows_functions as $key => $value) {

				$functionality .= $value .": ";
			}
			
			$rows[$row][REQ_FUNCT_NAME] = $functionality;
		}

		break;
	case "tests":

		$headers = array(	TEST_ID			=> lang_get('test_id'),
							TEST_NAME		=> lang_get('test_name'),
							TEST_MANUAL		=> lang_get('manual'),
							TEST_AUTOMATED	=> lang_get('automated'),
							TEST_BA_OWNER	=> lang_get('ba_owner'),
							TEST_QA_OWNER	=> lang_get('qa_owner'),
							TEST_TESTER     => lang_get('tester'),
							TEST_TESTTYPE	=> lang_get('testtype'),
							TEST_AREA_TESTED=> lang_get('area_tested'),
							TEST_PRIORITY	=> lang_get('priority'),
							TEST_STATUS		=> lang_get('test_status'),
							TEST_AUTO_PASS	=> lang_get('autopass')
						 );


		$display_options = session_get_display_options( "test" );


		$rows = test_filter_rows(	$project_id,
									$display_options['filter']['manual_auto'],
									$display_options['filter']['ba_owner'],
									$display_options['filter']['qa_owner'],
									$display_options['filter']['tester'],
									$display_options['filter']['test_type'],
									$display_options['filter']['area_tested'],
									$display_options['filter']['test_status'],
									$display_options['filter']['priority'],
									$display_options['filter']['per_page'],
									$display_options['filter']['test_search'],
									$display_options['order_by'],
									$display_options['order_dir'],
									$page_number=0 );
		break;
	case "test_workflow":

		$headers = array(	TEST_ID			=> lang_get('test_id'),
							TEST_NAME		=> lang_get('test_name'),
							TEST_MANUAL		=> lang_get('manual'),
							TEST_AUTOMATED	=> lang_get('automated'),
							TEST_BA_OWNER	=> lang_get('ba_owner'),
							TEST_QA_OWNER	=> lang_get('qa_owner'),
							TEST_TESTER     => lang_get('tester'),
							TEST_TESTTYPE	=> lang_get('testtype'),
							TEST_AREA_TESTED=> lang_get('area_tested'),
							TEST_PRIORITY	=> lang_get('priority'),
							TEST_STATUS		=> lang_get('test_status'),
							TEST_AUTO_PASS	=> lang_get('autopass'),
							TEST_DATE_ASSIGNED	=> lang_get('date_assigned'),
							TEST_DATE_EXPECTED	=> lang_get('date_expected'),
							TEST_DATE_COMPLETE	=> lang_get('date_complete'),
							TEST_BA_SIGNOFF		=> lang_get('ba_signoff_date'),
							TEST_COMMENTS		=> lang_get('info')
						);

		$display_options = session_get_display_options( "test_workflow" );

		$rows = test_workflow_filter_rows(	$project_id,
											$display_options['filter']['manual_auto'],
											$display_options['filter']['test_type'],
											$display_options['filter']['ba_owner'],
											$display_options['filter']['qa_owner'],
											$display_options['filter']['tester'],
											$display_options['filter']['area_tested'],
											$display_options['filter']['test_status'],
											$display_options['filter']['priority'],
											$display_options['filter']['per_page'],
											$display_options['filter']['test_search'],
											$display_options['order_by'],
											$display_options['order_dir'],
											$page_number=0 );


		break;
	case "results":

		$headers = array(	TEST_ID						=> lang_get('test_id'),
							TEST_NAME					=> lang_get('test_name'),
							TEST_BA_OWNER				=> lang_get('ba_owner'),
							TEST_QA_OWNER				=> lang_get('qa_owner'),
							TEST_ASSIGNED_TO			=> lang_get('test_assigned_to'),
							TEST_TESTTYPE				=> lang_get('testtype'),
							TEST_AREA_TESTED			=> lang_get('area_tested'),
							TEST_TS_ASSOC_STATUS		=> lang_get('test_run_status'),
							TEST_TS_ASSOC_ASSIGNED_TO	=> lang_get('test_status'),
							TEST_TS_ASSOC_COMMENTS		=> lang_get('info'),
							TEST_PRIORITY				=> lang_get('priority')
						);


		$table_options = session_get_display_options("results");
		$s_properties = session_get_properties("results");

		$rows = results_filter_rows(	$project_id,
										$table_options['filter']['manual_auto'],
										$table_options['filter']['ba_owner'],
										$table_options['filter']['qa_owner'],
										$table_options['filter']['test_type'],
										$table_options['filter']['area_tested'],
										$table_options['filter']['test_status'],
										$table_options['filter']['per_page'],
										$table_options['order_by'],
										$table_options['order_dir'],
										$page_number=0,
										$s_properties['release_id'],
										$s_properties['build_id'],
										$s_properties['testset_id'] );
		break;
	case "test_run":

		$s_results 		= session_get_properties( "results" );
		$test_run_id 	= $s_results['test_run_id'];

		$rows_verify_details 	= results_get_verify_results_detail( $test_run_id );
		$s_show_options 		= session_get_show_options();

		# get project preferences that determine what fields we do and don't show for each project
		$show_custom_1		= $s_show_options['show_custom_1'];
		$show_custom_2		= $s_show_options['show_custom_2'];
		$show_custom_3		= $s_show_options['show_custom_3'];
		$show_custom_4		= $s_show_options['show_custom_4'];
		$show_custom_5		= $s_show_options['show_custom_5'];
		$show_custom_6		= $s_show_options['show_custom_6'];
		$show_window		= $s_show_options['show_window'];
		$show_object		= $s_show_options['show_object'];
		$show_memory_stats	= $s_show_options['show_memory_stats'];

		$headers = array(	VERIFY_RESULTS_VAL_ID			=> lang_get('step_no'),
							VERIFY_RESULTS_ACTION			=> lang_get('action_'),
							VERIFY_RESULTS_EXPECTED_RESULT	=> lang_get('expected_'),
							VERIFY_RESULTS_ACTUAL_RESULT	=> lang_get('actual_'),
							VERIFY_RESULTS_TEST_STATUS		=> lang_get('status'),
							VERIFY_RESULTS_COMMENT			=> lang_get('comment'),
							VERIFY_RESULTS_DEFECT_ID		=> lang_get('defect') );

		if( $show_custom_1 == 'Y' ) {
			$headers[VERIFY_RESULTS_SHOW_CUSTOM_1] = lang_get('show_custom_1');
		}
		if( $show_custom_2 == 'Y' ) {
			$headers[VERIFY_RESULTS_SHOW_CUSTOM_2] = lang_get('show_custom_2');
		}
		if( $show_custom_3 == 'Y' ) {
			$headers[VERIFY_RESULTS_SHOW_CUSTOM_3] = lang_get('show_custom_3');
		}
		if( $show_custom_4 == 'Y' ) {
			$headers[VERIFY_RESULTS_SHOW_CUSTOM_4] = lang_get('show_custom_4');
		}
		if( $show_custom_5 == 'Y' ) {
			$headers[VERIFY_RESULTS_SHOW_CUSTOM_5] = lang_get('show_custom_5');
		}
		if( $show_custom_6 == 'Y' ) {
			$headers[VERIFY_RESULTS_SHOW_CUSTOM_6] = lang_get('show_custom_6');
		}

		$headers[VERIFY_RESULTS_TIMESTAMP] 		= lang_get('time_tested');
		$headers[VERIFY_RESULTS_WINDOW] 		= lang_get('window');
		$headers[VERIFY_RESULTS_OBJ] 			= lang_get('object');
		$headers[VERIFY_RESULTS_LINE_NUMBER]	= lang_get('line_no');

		if( $show_memory_stats == 'Y' ) {

			$headers[VERIFY_RESULTS_TOTAL_PHY_MEM] = lang_get('tot_phy_mem');
			$headers[VERIFY_RESULTS_FREE_PHY_MEM] = lang_get('free_phy_mem');
			$headers[VERIFY_RESULTS_TOTAL_VIR_MEM] = lang_get('tot_vir_mem');
			$headers[VERIFY_RESULTS_FREE_VIR_MEM] = lang_get('free_vir_mem');
			$headers[VERIFY_RESULTS_CUR_MEM_UTIL] = lang_get('cur_mem_util');
			$headers[VERIFY_RESULTS_TOTAL_PAGE_FILE] = lang_get('tot_page_file');
			$headers[VERIFY_RESULTS_FREE_PAGE_FILE] = lang_get('free_page_file');

		}

		$rows = results_get_verify_results_detail( $test_run_id );

		/*
		foreach($rows as $key => $val) {
			print"$key = $val<br>";
		}
		*/
		break;
	case "bugs":

		$headers = array(	BUG_ID						=> lang_get('bug_id'),
							BUG_PRIORITY				=> lang_get('bug_priority'),
							BUG_SEVERITY				=> lang_get('bug_severity'),
							BUG_STATUS					=> lang_get('bug_status'),
							BUG_CATEGORY				=> lang_get('bug_category'),
							BUG_REPORTER				=> lang_get('reported_by'),
							BUG_ASSIGNED_TO				=> lang_get('assigned_to'),
							BUG_ASSIGNED_TO_DEVELOPER	=> lang_get('assigned_to_developer'),
							BUG_FOUND_IN_RELEASE		=> lang_get('found_in_release'),
							BUG_ASSIGN_TO_RELEASE		=> lang_get('assigned_to_release'),
							BUG_DESCRIPTION				=> lang_get('bug_description'),
							BUG_SUMMARY					=> lang_get('bug_summary') );


		$display_options = session_get_display_options( "bug" );

		# We really should create a seperate function for csv/excel export
		$rows = bug_get(	$project_id,
							$page_number=0,
							$display_options['order_by'],
							$display_options['order_dir'],
							$display_options['filter']['status'],
							$display_options['filter']['category'],
							$display_options['filter']['component'],
							$display_options['filter']['reported_by'],
							$display_options['filter']['assigned_to'],
							$display_options['filter']['assigned_to_developer'],
							$display_options['filter']['found_in_release'],
							$display_options['filter']['assigned_to_release'] );
		
		break;
	case "test_steps":

		$headers = array(	TEST_STEP_NO				=> lang_get('step_no'),
							TEST_STEP_ACTION			=> lang_get('step_action'),
							TEST_STEP_TEST_INPUTS		=> lang_get('test_inputs'),
							TEST_STEP_EXPECTED			=> lang_get('step_expected'),
							TEST_STEP_INFO_STEP			=> lang_get('info_step') );

		$s_test_details			= session_get_properties("test");
		$test_id				= $s_test_details['test_id'];

		$rows = test_get_test_steps_for_export( $test_id );

		break;
	default:
		exit;
}


if( IMPORT_EXPORT_TO_EXCEL ) {
	if( isset($headers) ) {

		# HEADER ROW
		$row = $rows[0];
		$header = "";
		$data = "";
		
		foreach($headers as $key => $value) {

			# Replace double quotes with two double quotes
			$value = str_replace('"', '""', $value);
			$header .= '"'. $value .'"'. "\t";
		}
		
		# DATA ROWS
		//print_r($rows);
		foreach($rows as $row) {

			$data_row = "";

			foreach($row as $key => $value) {

				//print"$key = $value<br>";
				$data_field = str_replace( '"', '""', util_unhtmlentities( util_strip_html_tags($row[$key]) ) );

				$data_row .= '"'. $data_field .'"'. "\t";
			}

			$data .= trim($data_row)."". NEWLINE;
			//$csv .= substr($csv_row, 0, -1)."". NEWLINE;
		}
	} 
	else {

		# HEADER ROW
		$row = $rows[0];
		$data_row = "";

		foreach($row as $key => $value) {

			# Replace double quotes with two double quotes
			$data_field = str_replace('"', '""', $key);

			$data_row .= '"'.$data_field.'"\t';
		}

		$data .= substr($data_row, 0, -1)."". NEWLINE;

		# DATA ROWS
		foreach($rows as $row) {

			$data_row = "";

			foreach($row as $key => $value) {

				$data_field = str_replace('"', '""', util_unhtmlentities( util_strip_html_tags($value) ) );

				$data_row .= '"'.$data_field.'"\t';
			}

			$data .= substr($data_row, 0, -1)."". NEWLINE;

		}
	}


	$headers = str_replace("\r","",$header);
	$data = str_replace("\r","",$data);

	# OUTPUT	
	header("Content-type: application/x-msdownload"); 
	header("Content-Disposition: attachment; filename=\"$_GET[table].xls\"");
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	//print "$csv";
	print "$header\n$data";

}
else {
	if( isset($headers) ) {

		# HEADER ROW
		$row = $rows[0];

		$csv_row = "";

		foreach($headers as $key => $value) {

			# Replace double quotes with two double quotes
			$csv_field = str_replace('"', '""', $value);

			$csv_row .= '"'.$csv_field.'",';
		}

		$csv .= substr($csv_row, 0, -1)."". NEWLINE;

		# DATA ROWS
		foreach($rows as $row) {

			$csv_row = "";

			foreach($headers as $key => $value) {

				$csv_field = str_replace( '"', '""', util_unhtmlentities( util_strip_html_tags($row[$key]) ) );

				$csv_row .= '"'.$csv_field.'",';
			}

			$csv .= substr($csv_row, 0, -1)."". NEWLINE;
		}
	} else {

		# HEADER ROW
		$row = $rows[0];

		$csv_row = "";

		foreach($row as $key => $value) {

			# Replace double quotes with two double quotes
			$csv_field = str_replace('"', '""', $key);

			$csv_row .= '"'.$csv_field.'",';
		}

		$csv .= substr($csv_row, 0, -1)."". NEWLINE;

		# DATA ROWS
		foreach($rows as $row) {

			$csv_row = "";

			foreach($row as $key => $value) {

				$csv_field = str_replace('"', '""', util_unhtmlentities( util_strip_html_tags($value) ) );

				$csv_row .= '"'.$csv_field.'",';
			}

			$csv .= substr($csv_row, 0, -1)."". NEWLINE;

		}
	}

	# OUTPUT	
	header("Content-Type: text/csv");
	header("Content-Length: " . strlen($csv));
	header("Content-Disposition: attachment; filename=\"$_GET[table].csv\"");
	echo$csv;
}
# ------------------------------------
# $Log: csv_export.php,v $
# Revision 1.5  2007/03/14 17:22:23  gth2
# adding additional varibable to filter function now that we've added priority
# to the filters - gth
#
# Revision 1.4  2006/08/05 22:07:59  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/01/20 02:36:05  gth2
# enable export to excel functionaltiy - gth
#
# Revision 1.2  2006/01/16 13:27:45  gth2
# adding excel integration - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------

?>