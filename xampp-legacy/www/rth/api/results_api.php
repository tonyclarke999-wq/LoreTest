<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Results API
#
# $RCSfile: results_api.php,v $  $Revision: 1.13 $
# ------------------------------------

# ----------------------------------------------------------------------
# Create where clause for test results and run query to extract data
# OUTPUT:
#   array of test records.
# ----------------------------------------------------------------------
function results_filter_rows( $project_id, $manauto, $baowner, $qaowner, $testtype, $test_area, $status, $per_page, $orderby,
                          $order_dir, $page_number, $release_id, $build_id, $testset_id) {

    $where_clause = results_filter_generate_where_clause ($manauto, $baowner, $qaowner, $testtype, $test_area, $status);
    $row = results_apply_filter ( $project_id, 'results_csv_export.php', $release_id, $build_id, $testset_id, $where_clause, $per_page, $orderby, $order_dir, $page_number);
    return $row;

}

# ----------------------------------------------------------------------
# Create where clause for test results query
# OUTPUT:
#   Where clause string
# ----------------------------------------------------------------------
function results_filter_generate_where_clause($manauto, $baowner, $qaowner, $testtype, $test_area, $status) {

    $test_name          	= TEST_TBL. "." .TEST_NAME;
    $manual_tests           = TEST_TBL. "." .TEST_MANUAL;
    $automated_tests        = TEST_TBL. "." .TEST_AUTOMATED;
    $ba_owner               = TEST_TBL. "." .TEST_BA_OWNER;
    $qa_owner               = TEST_TBL. "." .TEST_QA_OWNER;
    $test_type              = TEST_TBL. "." .TEST_TESTTYPE;
    $area_tested            = TEST_TBL. "." .TEST_AREA_TESTED;
    $ts_assoc_test_status   = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_STATUS;
    $test_results_status	= TEST_RESULTS_TBL .".". TEST_RESULTS_TEST_STATUS;


    $where_clause = '';

    # MANUAL AUTOMATED
    if ( !empty($manauto) && $manauto != 'all') {
        if( $manauto == 'Manual' ) {
            $where_clause = $where_clause. " AND $manual_tests = 'YES' AND $automated_tests = ''";
        }
        elseif( $manauto == 'Automated' ) {
            $where_clause = $where_clause. " AND $manual_tests = '' AND $automated_tests = 'YES'";
        }
        else {
            $where_clause = $where_clause. " AND $manual_tests = 'YES' AND $automated_tests = 'YES'";
        }
    }
    # BA OWNER
    if ( !empty($baowner)  && $baowner != 'all') {

        $where_clause = $where_clause." AND $ba_owner = '$baowner'";
    }
    # QA OWNER
    if ( !empty($qaowner) && $qaowner != 'all') {

        $where_clause = $where_clause." AND $qa_owner = '$qaowner'";
    }
    # TEST TYPE
    if ( !empty( $testtype ) && $testtype != 'all') {

        $where_clause = $where_clause." AND $test_type = '$testtype'";
    }
    # AREA TESTED
    if ( !empty($test_area ) && $test_area != 'all') {

        $where_clause = $where_clause." AND $area_tested = '$test_area'";
    }
  	# TEST STATUS
	if ( !empty($status ) && $status != 'all') {

		$where_clause = $where_clause." AND $ts_assoc_test_status = '$status'";
    }

    return $where_clause;
}

# ----------------------------------------------------------------------
# Create and run query for displaying test result records. Display table header.
# OUTPUT:
#   array of test result records.
# ----------------------------------------------------------------------
function results_apply_filter(	$project_id,
								$csv_export_page,
								$release_id,
								$build_id,
								$testset_id,
								$where_clause=null,
								$per_page,
								$order_by,
								$order_dir,
								$page_number,
								$javascript=null ) {

    global $db;
    $test_tbl          		= TEST_TBL;
	$f_test_id				= TEST_TBL. "." .TEST_ID;
	$f_project_id			= TEST_TBL. "." .PROJECT_ID;
	$f_test_name            = TEST_TBL. "." .TEST_NAME;
	$f_manual_tests         = TEST_TBL. "." .TEST_MANUAL;
	$f_automated_tests      = TEST_TBL. "." .TEST_AUTOMATED;
	$f_ba_owner             = TEST_TBL. "." .TEST_BA_OWNER;
	$f_qa_owner             = TEST_TBL. "." .TEST_QA_OWNER;
	$f_test_assigned_to		= TEST_TBL. "." .TEST_ASSIGNED_TO;
	$f_test_load            = TEST_TBL. "." .TEST_LR;
	$f_test_type            = TEST_TBL. "." .TEST_TESTTYPE;
	$f_area_tested          = TEST_TBL. "." .TEST_AREA_TESTED;
	$f_deleted              = TEST_TBL. "." .TEST_DELETED;
	$f_archived             = TEST_TBL. "." .TEST_ARCHIVED;
	$f_test_priority        = TEST_TBL. "." .TEST_PRIORITY;
	$f_auto_pass            = TEST_TBL. "." .TEST_AUTO_PASS;

	$ts_assoc_tbl           = TEST_TS_ASSOC_TBL;
	$f_ts_assoc_id          = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ID;
	$f_ts_assoc_ts_id       = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TS_ID;
	$f_ts_assoc_test_id     = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TEST_ID;
	$f_ts_assoc_test_status = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_STATUS;
	$f_ts_assoc_assigned_to = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ASSIGNED_TO;
	$f_ts_assoc_comments    = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_COMMENTS;


	$results_tbl			= TEST_RESULTS_TBL;
	$f_results_ts_id		= $results_tbl .".". TEST_RESULTS_TEST_SET_ID;
	$f_status				= $results_tbl .".". TEST_RESULTS_TEST_STATUS;
	$f_assigned_to			= $results_tbl .".". TEST_RESULTS_ASSIGNED_TO;
	$f_comments				= $results_tbl .".". TEST_RESULTS_COMMENTS;

	$limit_clause       	= '';

	$q = "SELECT
			$f_test_id,
			$f_test_name,
			$f_ba_owner,
			$f_qa_owner,
			$f_test_assigned_to,
			$f_test_type,
			$f_area_tested,
			$f_ts_assoc_test_status,
			$f_ts_assoc_assigned_to,
			$f_ts_assoc_comments,
			$f_test_priority,
			$f_ts_assoc_id,
			$f_manual_tests,
			$f_automated_tests,
			$f_test_load,
			$f_auto_pass
		FROM $ts_assoc_tbl
		INNER JOIN $test_tbl ON $f_ts_assoc_test_id = $f_test_id
		WHERE $f_project_id = '$project_id'
			AND $f_ts_assoc_ts_id = '$testset_id'
			AND $f_deleted = 'N'
			AND $f_archived = 'N'
			$where_clause
		GROUP BY $f_test_id
		ORDER BY $order_by $order_dir";


	if( $per_page!=0 && $page_number!=0 ) {

		$row_count = db_num_rows( $db, db_query($db, $q) );

		# Make sure page count is at least 1
		$page_count = ceil($row_count / $per_page );
		if( $page_count < 1 ) {
			$page_count = 1;
		}

		# Make sure page_number isn't past the last page.
		if( $page_number > $page_count ) {
			$page_number = $page_count;
		}


		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * $per_page );
		html_table_offset( 	$row_count,
							$per_page,
							$page_number,
							$order_by,
							$order_dir,
							"results" );

		$q .= " LIMIT $offset, ".$per_page;

	}
	
	/*
	if( $per_page!=0 ) {

		# add a table header that includes the pages showing, export to csv, and links to other result pages

		# Add the limit clause to the query so that we only show n number of records per page

		html_table_offset(	db_num_rows( $db, db_query($db, $q) ),
							$per_page,
							$page_number,
							$order_by,
							$order_dir,
							$csv_export_page );

		$offset = ( ( $page_number - 1 ) * $per_page );

		$q .= " LIMIT $offset, $per_page";
	}
	*/

	//print"$q<br>";

    return db_fetch_array( $db, db_query($db, $q) );
}


# ----------------------------------------------------------------------
# is this function needed?
#
# Returns test run records
# ----------------------------------------------------------------------
function results_get_test_run_by_test( $test_id, $testset_id ) {

	global $db;
	$tbl_testsuite_results		= TEST_RESULTS_TBL;
	$f_testsuite_results_id		= $tbl_testsuite_results .".". TEST_RESULTS_ID;
	$f_unique_run_id			= $tbl_testsuite_results .".". TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_testset_id				= $tbl_testsuite_results .".". TEST_RESULTS_TEST_SET_ID;
	$f_test_id					= $tbl_testsuite_results .".". TEST_RESULTS_TEMPEST_TEST_ID;
	$f_test_name				= $tbl_testsuite_results .".". TEST_RESULTS_TEST_SUITE;
	$f_machine_name				= $tbl_testsuite_results .".". TEST_RESULTS_MACHINE_NAME;
	$f_status					= $tbl_testsuite_results .".". TEST_RESULTS_TEST_STATUS;
	$f_env						= $tbl_testsuite_results .".". TEST_RESULTS_ENVIRONMENT;
	$f_run_id					= $tbl_testsuite_results .".". TEST_RESULTS_RUN_ID;
	$f_finished					= $tbl_testsuite_results .".". TEST_RESULTS_RUN_ID;
	$f_os						= $tbl_testsuite_results .".". TEST_RESULTS_OS;
	$f_sp						= $tbl_testsuite_results .".". TEST_RESULTS_SP;
	$f_time_started				= $tbl_testsuite_results .".". TEST_RESULTS_TIME_STARTED;
	$f_time_finished			= $tbl_testsuite_results .".". TEST_RESULTS_TIME_FINISHED;
	$f_status					= $tbl_testsuite_results .".". TEST_RESULTS_TEST_STATUS;
	$f_assigned_to				= $tbl_testsuite_results .".". TEST_RESULTS_ASSIGNED_TO;
	$f_comments					= $tbl_testsuite_results .".". TEST_RESULTS_COMMENTS;
	$f_root_cause				= $tbl_testsuite_results .".". TEST_RESULTS_ROOT_CAUSE;

	$vr_tbl				= VERIFY_RESULTS_TBL;
	$f_vr_id			= $vr_tbl .".". VERIFY_RESULTS_ID;
	$f_vr_ts_id			= $vr_tbl .".". VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
	$f_vr_timestamp		= $vr_tbl .".". VERIFY_RESULTS_TIMESTAMP;
	$f_vr_action		= $vr_tbl .".". VERIFY_RESULTS_ACTION;
	$f_vr_expected 		= $vr_tbl .".". VERIFY_RESULTS_EXPECTED_RESULT;
	$f_vr_actual		= $vr_tbl .".". VERIFY_RESULTS_ACTUAL_RESULT;
	$f_vr_window		= $vr_tbl .".". VERIFY_RESULTS_WINDOW;
	$f_vr_object		= $vr_tbl .".". VERIFY_RESULTS_OBJ;
	$f_vr_custom_1		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_1;
	$f_vr_custom_2		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_2;
	$f_vr_custom_3		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_3;
	$f_vr_custom_4		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_4;
	$f_vr_custom_5		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_5;
	$f_vr_custom_6		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_6;
	$f_vr_validation_id	= $vr_tbl .".". VERIFY_RESULTS_VAL_ID; 		// MAY NOT BE USED
	$f_vr_total_phy_mem	= $vr_tbl .".". VERIFY_RESULTS_TOTAL_PHY_MEM;
	$f_vr_free_phy_mem	= $vr_tbl .".". VERIFY_RESULTS_FREE_PHY_MEM;
	$f_vr_tot_vir_mem	= $vr_tbl .".". VERIFY_RESULTS_TOTAL_VIR_MEM;
	$f_vr_free_vir_mem	= $vr_tbl .".". VERIFY_RESULTS_FREE_VIR_MEM;
	$f_vr_cur_mem_util	= $vr_tbl .".". VERIFY_RESULTS_CUR_MEM_UTIL;
	$f_vr_tot_page_file	= $vr_tbl .".". VERIFY_RESULTS_TOTAL_PAGE_FILE;
	$f_vr_free_page_file= $vr_tbl .".". VERIFY_RESULTS_FREE_PAGE_FILE;
	$f_vr_line_no		= $vr_tbl .".". VERIFY_RESULTS_LINE_NUMBER;
	$f_vr_status		= $vr_tbl .".". VERIFY_RESULTS_TEST_STATUS;
	$f_vr_comment		= $vr_tbl .".". VERIFY_RESULTS_COMMENT;
	$f_defect			= $vr_tbl .".". VERIFY_RESULTS_DEFECT_ID;

	$q = "SELECT
			$f_testsuite_results_id,
			$f_unique_run_id,
			$f_testset_id,
			$f_test_id,
			$f_test_name,
			$f_machine_name,
			$f_status,
			$f_env,
			$f_run_id,
			$f_finished,
			$f_os,
			$f_sp,
			$f_time_started,
			$f_time_finished,
			$f_status,
			$f_assigned_to,
			$f_comments,
			$f_root_cause,
			$f_defect,
			COUNT(IF($f_vr_status='pass', true, null)) as Passed,
			COUNT(IF($f_vr_status='fail', true, null)) as Failed,
			COUNT(IF($f_vr_status='info', true, null)) as Info,
			COUNT(IF($f_vr_status='hold', true, null)) as Hold,
			COUNT(IF($f_vr_status='', true, null)) as Blank,
			COUNT(true) as Total
	     FROM $tbl_testsuite_results
	     INNER JOIN $vr_tbl ON $f_vr_ts_id = $f_unique_run_id
	     WHERE $f_testset_id = '$testset_id'
	     	AND $f_test_id ='$test_id'
	     GROUP BY $f_unique_run_id
	     ORDER BY $f_testsuite_results_id";
	
	//print"$q<br>";
	$rs = db_query( $db, $q );

	$rows = db_fetch_array($db, $rs);

    return $rows;
}

# ----------------------------------------------------------------------
# Return test results
# ----------------------------------------------------------------------
function results_get_test_results_tbl_detail( $test_run_id ) {

	global $db;

	$test_results_tbl		= TEST_RESULTS_TBL;
	$f_test_unique_run_id	= TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_test_id				= TEST_RESULTS_TEMPEST_TEST_ID;
	$f_test_name		= TEST_RESULTS_TEST_SUITE;
	//$f_project_id		= PROJECT_ID;  // MAY BE ABLE TO SPEED UP THE QUERY USING THIS???
	$f_machine_name		= TEST_RESULTS_MACHINE_NAME;
	$f_test_started		= TEST_RESULTS_STARTED;
	$f_test_finished	= TEST_RESULTS_FINISHED;
	$f_time_started		= TEST_RESULTS_TIME_STARTED;
	$f_time_finished	= TEST_RESULTS_TIME_FINISHED;
	$f_test_status		= TEST_RESULTS_TEST_STATUS;
	$f_env				= TEST_RESULTS_ENVIRONMENT;
	$f_os				= TEST_RESULTS_OS;
	$f_cvs_version		= TEST_RESULTS_CVS_VERSION;

	$q = "SELECT
		$f_test_unique_run_id,
		$f_test_name,
		$f_test_id,
		$f_machine_name,
		$f_test_status,
		$f_time_started,
		$f_time_finished,
		$f_test_started,
		$f_test_finished,
		$f_os,
		$f_cvs_version,
		$f_env
	      FROM $test_results_tbl
	      WHERE $f_test_unique_run_id = '$test_run_id'";
	$rs = db_query( $db, $q );
	$row = db_fetch_row( $db, $rs );

	return $row;

}

function results_get_verify_results_detail2( $test_run_id ) {

	global $db;

	$ts_tbl			= TEST_RESULTS_TBL;
	$f_ts_id		= $ts_tbl .".". TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_ts_test_id	= $ts_tbl .".". TEST_RESULTS_TEMPEST_TEST_ID;
	$f_ts_test_name	= $ts_tbl .".". TEST_RESULTS_TEST_SUITE;
	/*
	$f_ts_machine_name	= $ts_tbl .".". TEST_RESULTS_MACHINE_NAME;
	$f_ts_test_started	= $ts_tbl .".". TEST_RESULTS_STARTED;
	$f_ts_test_finished	= $ts_tbl .".". TEST_RESULTS_FINISHED;
	$f_ts_time_started	= $ts_tbl .".". TEST_RESULTS_TIME_STARTED;
	$f_ts_time_finished	= $ts_tbl .".". TEST_RESULTS_TIME_FINISHED;
	$f_ts_test_status	= $ts_tbl .".". TEST_RESULTS_TEST_STATUS;
	$f_ts_env			= $ts_tbl .".". TEST_RESULTS_ENVIRONMENT;
	$f_ts_os			= $ts_tbl .".". TEST_RESULTS_OS;
	$f_ts_cvs_version	= $ts_tbl .".". TEST_RESULTS_CVS_VERSION;
	*/

	$vr_tbl				= VERIFY_RESULTS_TBL;
	$f_vr_id			= $vr_tbl .".". VERIFY_RESULTS_ID;
	$f_vr_ts_id			= $vr_tbl .".". VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
	$f_vr_timestamp		= $vr_tbl .".". VERIFY_RESULTS_TIMESTAMP;
	$f_vr_action		= $vr_tbl .".". VERIFY_RESULTS_ACTION;
	$f_vr_expected 		= $vr_tbl .".". VERIFY_RESULTS_EXPECTED_RESULT;
	$f_vr_actual		= $vr_tbl .".". VERIFY_RESULTS_ACTUAL_RESULT;
	$f_vr_window		= $vr_tbl .".". VERIFY_RESULTS_WINDOW;
	$f_vr_object		= $vr_tbl .".". VERIFY_RESULTS_OBJ;
	$f_vr_custom_1		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_1;
	$f_vr_custom_2		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_2;
	$f_vr_custom_3		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_3;
	$f_vr_custom_4		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_4;
	$f_vr_custom_5		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_5;
	$f_vr_custom_6		= $vr_tbl .".". VERIFY_RESULTS_SHOW_CUSTOM_6;
	$f_vr_validation_id	= $vr_tbl .".". VERIFY_RESULTS_VAL_ID; 		// MAY NOT BE USED
	$f_vr_total_phy_mem	= $vr_tbl .".". VERIFY_RESULTS_TOTAL_PHY_MEM;
	$f_vr_free_phy_mem	= $vr_tbl .".". VERIFY_RESULTS_FREE_PHY_MEM;
	$f_vr_tot_vir_mem	= $vr_tbl .".". VERIFY_RESULTS_TOTAL_VIR_MEM;
	$f_vr_free_vir_mem	= $vr_tbl .".". VERIFY_RESULTS_FREE_VIR_MEM;
	$f_vr_cur_mem_util	= $vr_tbl .".". VERIFY_RESULTS_CUR_MEM_UTIL;
	$f_vr_tot_page_file	= $vr_tbl .".". VERIFY_RESULTS_TOTAL_PAGE_FILE;
	$f_vr_free_page_file= $vr_tbl .".". VERIFY_RESULTS_FREE_PAGE_FILE;
	$f_vr_line_no		= $vr_tbl .".". VERIFY_RESULTS_LINE_NUMBER;
	$f_vr_status		= $vr_tbl .".". VERIFY_RESULTS_TEST_STATUS;
	$f_vr_comment		= $vr_tbl .".". VERIFY_RESULTS_COMMENT;
	$f_defect			= $vr_tbl .".". VERIFY_RESULTS_DEFECT_ID;

	$q = "SELECT STRAIGHT JOIN
		$f_ts_id,
		$f_vr_action,
		$f_vr_expected,
		$f_vr_actual,
		$f_vr_status,
		$f_vr_comment,
		$f_ts_test_id,
		$f_ts_test_name,
		$f_vr_id,
		$f_vr_ts_id,
		$f_vr_timestamp,
		$f_vr_window,
		$f_vr_object,
		$f_vr_custom_1,
		$f_vr_custom_2,
		$f_vr_custom_6,
		$f_vr_custom_4,
		$f_vr_custom_5,
		$f_vr_custom_3,
		$f_vr_validation_id,
		$f_vr_total_phy_mem,
		$f_vr_free_phy_mem,
		$f_vr_tot_vir_mem,
		$f_vr_free_vir_mem,
		$f_vr_cur_mem_util,
		$f_vr_tot_page_file,
		$f_vr_free_page_file,
		$f_vr_line_no,
		$f_defect
	    FROM $ts_tbl, $vr_tbl
	    WHERE $f_vr_ts_id = $f_ts_id
	    AND $f_ts_id = '$test_run_id'
	    ORDER BY '$f_vr_id'";

	$rs = db_query( $db, $q );
    $num = db_num_rows( $db, $rs );
	print"$q<br>";

	$row = array();

	for ( $i=0 ; $i < $num ; $i++ ) {
		array_push( $row, db_fetch_row( $db, $rs ) );
    }

    return $row;
}

# ----------------------------------------------------------------------
# Return test results by test run id
# ----------------------------------------------------------------------
function results_get_test_results_detail( $test_run_id ) {

	global $db;
	$ts_tbl			= TEST_RESULTS_TBL;
	$f_id			= TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_test_id		= TEST_RESULTS_TEMPEST_TEST_ID;
	$f_test_name	= TEST_RESULTS_TEST_SUITE;
	$f_time_started	= TEST_RESULTS_TIME_STARTED;
	$f_time_finished= TEST_RESULTS_TIME_FINISHED;
	$f_finished		= TEST_RESULTS_FINISHED;
	$f_machine_name	= TEST_RESULTS_MACHINE_NAME;
	$f_os			= TEST_RESULTS_OS;
	$f_sp			= TEST_RESULTS_SP;
	$f_env			= TEST_RESULTS_ENVIRONMENT;
	$f_cvs_version	= TEST_RESULTS_CVS_VERSION;
	$f_test_status	= TEST_RESULTS_TEST_STATUS;
	$f_comments		= TEST_RESULTS_COMMENTS;
	$f_root_cause	= TEST_RESULTS_ROOT_CAUSE;
	

	$q = "SELECT
		$f_id,
		$f_test_id,
		$f_test_name,
		$f_time_started,
		$f_time_finished,
		$f_finished,
		$f_machine_name,
		$f_os,
		$f_sp,
		$f_env,
		$f_test_status,
		$f_comments,
		$f_root_cause,
		$f_cvs_version
	     FROM $ts_tbl
	     WHERE $f_id = '$test_run_id'";

	$rs = db_query( $db, $q );
	$row = db_fetch_row( $db, $rs );

	return $row;

}

# ----------------------------------------------------------------------
# Return verify results
# ----------------------------------------------------------------------
function results_get_verify_results_detail( $test_run_id ) {

	global $db;

	$vr_tbl				= VERIFY_RESULTS_TBL;
	$f_vr_id			= VERIFY_RESULTS_ID;
	$f_vr_ts_id			= VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
	$f_vr_timestamp		= VERIFY_RESULTS_TIMESTAMP;
	$f_vr_action		= VERIFY_RESULTS_ACTION;
	$f_vr_expected 		= VERIFY_RESULTS_EXPECTED_RESULT;
	$f_vr_actual		= VERIFY_RESULTS_ACTUAL_RESULT;
	$f_vr_window		= VERIFY_RESULTS_WINDOW;
	$f_vr_object		= VERIFY_RESULTS_OBJ;
	$f_vr_custom_1		= VERIFY_RESULTS_SHOW_CUSTOM_1;
	$f_vr_custom_2		= VERIFY_RESULTS_SHOW_CUSTOM_2;
	$f_vr_custom_3		= VERIFY_RESULTS_SHOW_CUSTOM_3;
	$f_vr_custom_4		= VERIFY_RESULTS_SHOW_CUSTOM_4;
	$f_vr_custom_5		= VERIFY_RESULTS_SHOW_CUSTOM_5;
	$f_vr_custom_6		= VERIFY_RESULTS_SHOW_CUSTOM_6;
	$f_vr_validation_id	= VERIFY_RESULTS_VAL_ID; 		// MAY NOT BE USED
	$f_vr_total_phy_mem	= VERIFY_RESULTS_TOTAL_PHY_MEM;
	$f_vr_free_phy_mem	= VERIFY_RESULTS_FREE_PHY_MEM;
	$f_vr_tot_vir_mem	= VERIFY_RESULTS_TOTAL_VIR_MEM;
	$f_vr_free_vir_mem	= VERIFY_RESULTS_FREE_VIR_MEM;
	$f_vr_cur_mem_util	= VERIFY_RESULTS_CUR_MEM_UTIL;
	$f_vr_tot_page_file	= VERIFY_RESULTS_TOTAL_PAGE_FILE;
	$f_vr_free_page_file= VERIFY_RESULTS_FREE_PAGE_FILE;
	$f_vr_line_no		= VERIFY_RESULTS_LINE_NUMBER;
	$f_vr_status		= VERIFY_RESULTS_TEST_STATUS;
	$f_vr_comment		= VERIFY_RESULTS_COMMENT;
	$f_defect			= VERIFY_RESULTS_DEFECT_ID;


	$q = "SELECT
		$f_vr_id,
		$f_vr_action,
		$f_vr_expected,
		$f_vr_actual,
		$f_vr_status,
		$f_vr_comment,
		$f_defect,
		$f_vr_custom_1,
		$f_vr_custom_2,
		$f_vr_custom_3,
		$f_vr_custom_4,
		$f_vr_custom_5,
		$f_vr_custom_6,
		$f_vr_timestamp,
		$f_vr_window,
		$f_vr_object,
		$f_vr_total_phy_mem,
		$f_vr_free_phy_mem,
		$f_vr_tot_vir_mem,
		$f_vr_free_vir_mem,
		$f_vr_cur_mem_util,
		$f_vr_tot_page_file,
		$f_vr_free_page_file,
		$f_vr_line_no,
		$f_vr_validation_id
	    FROM $vr_tbl
	    WHERE $f_vr_ts_id = '$test_run_id'
	    ORDER BY $f_vr_id";

	$rs = db_query( $db, $q );
    $num = db_num_rows( $db, $rs );
	#print"$q<br>";

	$row = array();

	for ( $i=0 ; $i < $num ; $i++ ) {
		array_push( $row, db_fetch_row( $db, $rs ) );
    }

    return $row;
}

/*
function results_get_test_case_detail( $test_run_id ) {

	global $db;

	$tc_tbl			= TEST_CASE_RESULTS_TBL;
	$f_tc_id		= $tc_tbl .".". TEST_CASE_RESULTS_TC_UNIQUE_RUN_ID;
	$f_tc_ts_id		= $tc_tbl .".". TEST_CASE_RESULTS_TS_UNIQUE_RUN_ID;
	$f_tc_name		= $tc_tbl .".". TEST_CASE_RESULTS_TEST_CASE;
	$f_tc_cvs_version	= $tc_tbl .".". TEST_CASE_RESULTS_CVS_VERSION;


	$q = "SELECT
		$f_tc_id,
		$f_tc_name,
		$f_tc_cvs_version
		FROM $tc_tbl
	    WHERE $f_tc_ts_id = '$test_run_id'
	    ORDER BY $f_tc_ts_id";

	$rs = db_query( $db, $q );
    $num = db_num_rows( $db, $rs );
	//print"$q<br>";

	if( $num > 1 ) {
		$row = array();

		for ( $i=0 ; $i < $num ; $i++ ) {
			array_push( $row, db_fetch_row( $db, $rs ) );
    	}

    	return $row;
    }
    else {

    	$row = db_fetch_row( $db, $rs );

		return $row;

    }

}
*/

# ----------------------------------------------------------------------
# Delete a test run and redirect browser
# ----------------------------------------------------------------------
function results_delete_test_run( $id ) {

	global $db;
	$ts_tbl			= TEST_RESULTS_TBL;
	$vr_tbl			= VERIFY_RESULTS_TBL;

	$f_ts_test_run_id	= TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_vr_test_run_id	= VERIFY_RESULTS_TS_UNIQUE_RUN_ID;

	$redirect_page = "results_test_run_page.php";

	$q1 = "DELETE FROM $ts_tbl WHERE $f_ts_test_run_id ='$id'";
	db_query( $db, $q1 );

	$q3 = "DELETE FROM $vr_tbl WHERE $f_vr_test_run_id ='$id'";
	db_query( $db, $q3 );

	html_redirect( $redirect_page );
}

# ----------------------------------------------------------------------
# Print verfication status icon
# ----------------------------------------------------------------------
function results_verfication_status_icon( $status ) {

	$status = strtoupper($status);

	switch( $status ) {
		case 'PASS':
		case 'PASSED':
			return"<td class='tbl-c'><img src='". IMG_SRC ."pass.gif' alt='pass'></td>". NEWLINE;
			break;
		case 'FAIL':
			return"<td class='tbl-c'><img src='". IMG_SRC ."fail.gif' alt='fail'></td>". NEWLINE;
			break;
		case 'INFO':
			return"<td class='tbl-c'><img src='". IMG_SRC ."info.gif' alt='info'></td>". NEWLINE;
			break;
		case 'HOLD':
			return"<td class='tbl-c'>". lang_get( 'hold' ) ."</td>". NEWLINE;  // we may want to create an icon for this
			break;
		default:
			return"<td class='tbl-c'></td>". NEWLINE;
			break;
	}

}

# ----------------------------------------------------------------------
# Format memory status
#
# INPUT:
#	kB
# OUTPUT:
#	MB
# ----------------------------------------------------------------------
function results_format_memory_stats( $stat ) {

	$stat = $stat/1024;
	$stat = $stat/1024;
	settype($stat, 'string');
	$var = strpos($stat, ".");
	$stat = substr($stat, 0, $var + 2 );
	$stat = $stat . " MB";
	return $stat;

}

# ----------------------------------------------------------------------
# Return an array with all test statuses
# OUTPUT:
#   array of test result records.
# ----------------------------------------------------------------------
function results_get_teststatus_by_project( $project_id, $blank=false ) {

	$arr = array("Passed", "Failed", "WIP", "Finished : Awaiting Review", "Incomplete");

    if( $blank == true ) {
		$arr[] = "";
	}

    return $arr;
}


# ----------------------------------------------------------------------
# Run a query and update test results
# This will update the test in the testset_testsuite_assoc table
# OUTPUT:
#   none
# ----------------------------------------------------------------------
function results_update_test_result( $testset_id, $test_id, $tester, $status, $root_cause, $finished, $comments ) {

	global $db;

	$test_ts_tbl	= TEST_TS_ASSOC_TBL;
	$f_testset_id	= TEST_TS_ASSOC_TS_ID;
	$f_test_id		= TEST_TS_ASSOC_TEST_ID;
	$f_status		= TEST_TS_ASSOC_STATUS;
	$f_root_cause	= TEST_TS_ASSOC_ROOT_CAUSE;
	$f_tester		= TEST_TS_ASSOC_ASSIGNED_TO;
	$f_timestamp	= TEST_TS_ASSOC_TIMESTAMP;
	$f_finished		= TEST_TS_ASSOC_FINISHED;
	$f_comments		= TEST_TS_ASSOC_COMMENTS;
	$date			= date_get_short_dt();

	$q = "UPDATE $test_ts_tbl
		SET
		$f_tester = '$tester',
		$f_status = '$status',
		$f_root_cause = '$root_cause',
		$f_comments = '$comments',
		$f_finished = '$finished',
		$f_timestamp = '$date'
		WHERE $f_testset_id = '$testset_id'
		AND $f_test_id = '$test_id'";

	db_query( $db, $q );
}

function results_update_test_status( $testset_id, $test_id, $tester, $status, $finished=1 ) {

	global $db;
	$test_ts_tbl	= TEST_TS_ASSOC_TBL;
	$f_testset_id	= TEST_TS_ASSOC_TS_ID;
	$f_test_id		= TEST_TS_ASSOC_TEST_ID;
	$f_status		= TEST_TS_ASSOC_STATUS;
	$f_tester		= TEST_TS_ASSOC_ASSIGNED_TO;
	$f_timestamp	= TEST_TS_ASSOC_TIMESTAMP;
	$f_finished		= TEST_TS_ASSOC_FINISHED;
	$f_comments		= TEST_TS_ASSOC_COMMENTS;
	$date			= date_get_short_dt();

	$q = "UPDATE $test_ts_tbl
		SET
		$f_tester = '$tester',
		$f_status = '$status',
		$f_finished = '$finished',
		$f_timestamp = '$date'
		WHERE $f_testset_id = '$testset_id'
		AND $f_test_id = '$test_id'";

	db_query( $db, $q );
}

function results_mass_update_test_result( $testset_id, $test_ids, $assigned_to, $status, $comments, $finished=0 ) {


	global $db;
	$test_ts_tbl	= TEST_TS_ASSOC_TBL;
	$f_testset_id	= TEST_TS_ASSOC_TS_ID;
	$f_test_id		= TEST_TS_ASSOC_TEST_ID;
	$f_status		= TEST_TS_ASSOC_STATUS;
	$f_assigned_to	= TEST_TS_ASSOC_ASSIGNED_TO;
	$f_timestamp	= TEST_TS_ASSOC_TIMESTAMP;
	$f_finished		= TEST_TS_ASSOC_FINISHED;
	$f_comments		= TEST_TS_ASSOC_COMMENTS;
	$date			= date_get_short_dt();

	$q = "UPDATE $test_ts_tbl
		SET
		$f_assigned_to = '$assigned_to',
		$f_status = '$status',
		$f_comments = '$comments',
		$f_finished = '$finished',
		$f_timestamp = '$date'
		WHERE $f_testset_id = '$testset_id'
		AND $f_test_id IN ($test_ids)";
	//print"$q<br>";
	db_query( $db, $q );
}


# ----------------------------------------------------------------------
# Run a query and update a specific test run
# OUTPUT:
#   none
# ----------------------------------------------------------------------
function results_update_test_run( $test_run_id, $assigned_to, $status, $finished, $comments, $root_cause ) {

	global $db;
	$results_tbl	= TEST_RESULTS_TBL;
	$f_results_id	= TEST_RESULTS_ID;
	$f_test_run_id	= TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_assigned_to	= TEST_RESULTS_ASSIGNED_TO;
	$f_status		= TEST_RESULTS_TEST_STATUS;
	$f_comments		= TEST_RESULTS_COMMENTS;
	$f_root_cause	= TEST_RESULTS_ROOT_CAUSE;
	$f_finished		= TEST_RESULTS_FINISHED;
	$f_timestamp	= TEST_RESULTS_LOG_TIME_STAMP;
	$date			= date_get_short_dt();

	$q = "	UPDATE $results_tbl
			SET
				$f_assigned_to 	= '$assigned_to',
				$f_status 		= '$status',
				$f_comments 	= '$comments',
				$f_root_cause 	= '$root_cause',
				$f_finished 	= '$finished',
				$f_timestamp 	= '$date'
			WHERE $f_test_run_id= '$test_run_id'";
//print$q;exit;
	db_query( $db, $q );
}

# ----------------------------------------------------------------------
# Pass a test run with minimal details.  Used when a user wants to pass a test
# without entering a comment
# OUTPUT:
#   none
# ----------------------------------------------------------------------
function results_pass_test_run( $test_run_id, $assigned_to, $status, $finished ) {

	global $db;
	$results_tbl	= TEST_RESULTS_TBL;
	$f_results_id	= TEST_RESULTS_ID;
	$f_test_run_id	= TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_assigned_to	= TEST_RESULTS_ASSIGNED_TO;
	$f_status		= TEST_RESULTS_TEST_STATUS;
	$f_finished		= TEST_RESULTS_FINISHED;
	$f_timestamp	= TEST_RESULTS_LOG_TIME_STAMP;
	$date		= date_get_short_dt();

	$q = "UPDATE $results_tbl
		 SET
		 $f_assigned_to = '$assigned_to',
		 $f_status = '$status',
		 $f_finished = '$finished',
		 $f_timestamp = '$date'
		 WHERE $f_test_run_id = '$test_run_id'";
	db_query( $db, $q );
}

function results_get_verification_status() {

	$status = array( "Pass", "Fail", "Hold", "Info" );
	return $status;
}

# ----------------------------------------------------------------------
# Run a query and update a specific verification record
# INPUT:
#	TS_UNIQUE_RUN_ID, VERIFY_RESULTS_ID, STATUS, AND COMMENTS
# OUTPUT:
#   none
# ----------------------------------------------------------------------
function results_update_verification( $test_run_id, $verify_id, $status, $comments, $defect_id ) {

	global $db;
	$vr_tbl			= VERIFY_RESULTS_TBL;
	$f_verify_id	= VERIFY_RESULTS_ID;
	$f_test_run_id	= VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
	$f_status		= VERIFY_RESULTS_TEST_STATUS;
	$f_comments		= VERIFY_RESULTS_COMMENT;
	$f_defect_id	= VERIFY_RESULTS_DEFECT_ID;
	//$f_timestamp		= VERIFY_RESULTS_LOG_TIME_STAMP;

	$q = "UPDATE $vr_tbl
	      SET
	      $f_status = '$status',
	      $f_comments = '$comments',
	      $f_defect_id = '$defect_id'
	      WHERE $f_test_run_id = '$test_run_id'
	      AND $f_verify_id = '$verify_id'";
	db_query( $db, $q );

}

# ----------------------------------------------------------------------
# Run a query and update a field of an individual verification
# This will update any field in the VerifyResults table
# INPUT:
#	TS_UNIQUE_RUN_ID, FIELD TO UPDATE, VALUE
# OUTPUT:
#
# ----------------------------------------------------------------------
function results_update_verfication_record( $verify_id, $field, $value ) {

	global $db;
	$vr_tbl			= VERIFY_RESULTS_TBL;
	$f_verify_id	= VERIFY_RESULTS_ID;

	$q = "UPDATE
			$vr_tbl
	      SET
			$field = '$value'
	      WHERE
			$f_verify_id = '$verify_id'";
	//print"$q<br>";

	db_query( $db, $q );

}

/*
function results_print_test_detail_table( $test_detail ) {

	print"<table class=width100 rules=cols>";
	print"<tr class='tbl_header'>";
	html_tbl_print_header( lang_get('test_id') );
	html_tbl_print_header( lang_get('test_name') );
	html_tbl_print_header( lang_get('ba_owner') );
	html_tbl_print_header( lang_get('qa_owner') );
	html_tbl_print_header( lang_get('area_tested') );
	print"</tr>";


	extract( $test_detail, EXTR_PREFIX_ALL, 'v' );

	$test_id              = ${'v_' . TEST_ID};
	$test_name              = ${'v_' . TEST_NAME};
	$ba_owner               = ${'v_' . TEST_BA_OWNER};
	$qa_owner               = ${'v_' . TEST_QA_OWNER};
	$area_tested            = ${'v_' . TEST_AREA_TESTED};

	print"<tr>";
	print"<td class='tbl-c'>$test_id</td>";
	print"<td class='tbl-c'>$test_name</td>";
	print"<td class='tbl-c'>$ba_owner</td>";
	print"<td class='tbl-c'>$qa_owner</td>";
	print"<td class='tbl-c'>$area_tested</td>";
	print"</tr>";
	print"</table>";
	print"<br><br>";

}
*/

# ----------------------------------------------------------------------
# Return values for manaul and automated tests
# OUTPUT:
#   array containing array manual / auto values
# ----------------------------------------------------------------------
function results_get_root_cause_values() {

    $arr = array("Application Defect",
				 "Environmental Issue",
				 "Test Case Issue",
				 "Test Data Issue",
				 "");

    return $arr;
}

# ----------------------------------------------------------------------
# Get test details for a test in a testset
# INPUT:
#   TestSetID and TestID
# OUTPUT:
#   Corresponding test and testset information
# ----------------------------------------------------------------------
function results_query_test_run_details( $test_run_id ) {

	global $db;
	$results_tbl	= TEST_RESULTS_TBL;
	$f_results_id	= TEST_RESULTS_ID;
	$f_test_run_id	= TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_test_id		= TEST_RESULTS_TEMPEST_TEST_ID;
	//$f_testset_id	= TEST_RESULTS_TEST_SET_ID;
	$f_test_name	= TEST_RESULTS_TEST_SUITE;
	$f_status		= TEST_RESULTS_TEST_STATUS;
	//$f_finished		= TEST_RESULTS_FINISHED;
	$f_assigned_to	= TEST_RESULTS_ASSIGNED_TO;
	$f_comments		= TEST_RESULTS_COMMENTS;
	$f_root_cause	= TEST_RESULTS_ROOT_CAUSE;


	$q = "SELECT
		$f_results_id,
		$f_test_id,
		$f_test_name,
		$f_status,
		$f_assigned_to,
		$f_comments,
		$f_root_cause
	    FROM $results_tbl
	    WHERE $f_test_run_id = '$test_run_id'";

	//print"$q<BR>";
	$rs = &db_query( $db, $q );
	$row = db_fetch_row( $db, $rs ) ;

    return $row;

}


function results_get_time_started() {

	$time = array();

    $time['hour']	= array('','12','11','10','09','08','07','06','05','04','03','02','01');
	$time['minute'] = array('','00','10','20','30','40','50');
	$time['am_pm']	= array('','AM','PM');

    return $time;
}

function results_get_os() {

	$os = array('Win95',
				'Win98',
				'NT',
				'Win2000',
				'XP',
				'RedHat Linux',
				'Mandrake',
				'AIX',
				'Solaris',
				'Linux',
				'zLinux',
				'');
    return $os;
}


# ----------------------------------------------------------------------
# Create a test result for a manual test run.
# This function only inputs data into the TestSuite table.
# We will need another function to input test setps into the verify_results table
# INPUT:
#   Users test input
# ----------------------------------------------------------------------
function results_create_testsuite_result( $test_run_id, $testset_id, $test_id, $test_name,
										  $test_run_status, $tested_by_user, $time_started,
										  $time_finished, $comments, $root_cause,
										  $environment, $os ) {

	global $db;

	$results_tbl		= TEST_RESULTS_TBL;
	$f_test_run_id		= TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_test_id			= TEST_RESULTS_TEMPEST_TEST_ID;
	$f_testset_id		= TEST_RESULTS_TEST_SET_ID;
	$f_test_name		= TEST_RESULTS_TEST_SUITE;
	$f_status			= TEST_RESULTS_TEST_STATUS;
	$f_assigned_to		= TEST_RESULTS_ASSIGNED_TO;
	$f_started			= TEST_RESULTS_STARTED;
	$f_finished			= TEST_RESULTS_FINISHED;
	$f_time_started		= TEST_RESULTS_TIME_STARTED;
	$f_time_finished	= TEST_RESULTS_TIME_FINISHED;
	$f_comments			= TEST_RESULTS_COMMENTS;
	$f_root_cause		= TEST_RESULTS_ROOT_CAUSE;
	$f_environment		= TEST_RESULTS_ENVIRONMENT;
	$f_os				= TEST_RESULTS_OS;



	$q = "INSERT INTO $results_tbl ( $f_test_run_id, $f_test_id,
					 $f_testset_id, $f_test_name, $f_status, $f_assigned_to,
					 $f_started, $f_finished, $f_time_started, $f_time_finished,
					 $f_comments, $f_root_cause, $f_environment, $f_os )
					 VALUES('$test_run_id', '$test_id', '$testset_id',
					 '$test_name', '$test_run_status', '$tested_by_user',
					 '1', '1', '$time_started', '$time_finished', '$comments',
					 '$root_cause', '$environment', '$os')";
	#print"$q<br>";
	db_query( $db, $q );


}

# ----------------------------------------------------------------------
# Create a test result for a manual test run.
# This function only inputs data into the TestSuite table.
# We will need another function to input test setps into the verify_results table
# INPUT:
#   Users test input
# ----------------------------------------------------------------------
function results_edit_testsuite_result( $test_run_id, $testset_id, $test_id, $test_name,
										  $test_run_status, $tested_by_user, $time_started,
										  $time_finished, $comments, $root_cause,
										  $environment, $os ) {

	global $db;

	$results_tbl		= TEST_RESULTS_TBL;
	$f_test_run_id		= TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_test_id			= TEST_RESULTS_TEMPEST_TEST_ID;
	$f_testset_id		= TEST_RESULTS_TEST_SET_ID;
	$f_test_name		= TEST_RESULTS_TEST_SUITE;
	$f_status			= TEST_RESULTS_TEST_STATUS;
	$f_assigned_to		= TEST_RESULTS_ASSIGNED_TO;
	$f_started			= TEST_RESULTS_STARTED;
	$f_finished			= TEST_RESULTS_FINISHED;
	$f_time_started		= TEST_RESULTS_TIME_STARTED;
	$f_time_finished	= TEST_RESULTS_TIME_FINISHED;
	$f_comments			= TEST_RESULTS_COMMENTS;
	$f_root_cause		= TEST_RESULTS_ROOT_CAUSE;
	$f_environment		= TEST_RESULTS_ENVIRONMENT;
	$f_os				= TEST_RESULTS_OS;



	$q = "UPDATE $results_tbl
			SET
				$f_test_name = '$test_name',
				$f_status = '$test_run_status',
				$f_assigned_to = '$tested_by_user',
				$f_started = '1',
				$f_finished = '1',
				$f_time_started = '$time_started',
				$f_time_finished = '$time_finished',
				$f_comments = '$comments',
				$f_root_cause = '$root_cause',
				$f_environment = '$environment',
				$f_os = '$os'
			WHERE
				$f_test_run_id = '$test_run_id'
					AND $f_test_id = '$test_id'
					AND $f_testset_id = '$testset_id'";

	//print"$q<br>";
	db_query( $db, $q );


}

# --------------------------------------------------------------------------
# Calcute the time a test was started based on the current time and
# the duration of the test run.  The duration is entered by the user
# when running a manual test.  The current limit is 999 minutes.
# INPUT
# $duration (minutes to run a test)
# OUTPUT
# The time the user started the test in this format yyyy-mm-dd hh:mm:ss
# --------------------------------------------------------------------------
function results_caculate_time_started( $duration ) {

	$seconds = $duration * 60;
	$unix_time_started = time() - $seconds;
	$start_time = date("Y-m-d H:i:s", $unix_time_started);

	return $start_time;

}

# --------------------------------------------------------------------------
#
#
# --------------------------------------------------------------------------
function results_does_test_run_file_exist( $testrunID ) {

	global $db;

	$run_doc_tbl		= INDIV_RUN_DOCS_TBL;
	$f_ts_unique_id		= INDIV_RUN_DOCS_TS_UNIQUE_RUN_ID;
	$f_display_name		= INDIV_RUN_DOCS_DISPLAY_NAME;


	$query_individualRunDocs = "SELECT $f_display_name FROM $run_doc_tbl WHERE $f_ts_unique_id = '$testrunID'";
	#print"$query_individualRunDocs <BR>";
	$recordSet_individualRunDocs = $db->Execute($query_individualRunDocs);
	$num_individualRunDocs = $recordSet_individualRunDocs->NumRows();

	if($num_individualRunDocs==0)
	{
		$test_run_file_exists = "No";
	}
	else
	{
		$test_run_file_exists = "Yes";
	}

	return $test_run_file_exists;

}

function results_get_num_tests_by_status( $testset_id, $status ) {

	global $db;

	$tsa_tbl		= TEST_TS_ASSOC_TBL;
	$f_ts_id		= TEST_TS_ASSOC_TS_ID;
	$f_status		= TEST_TS_ASSOC_STATUS;

	$q = "SELECT COUNT($f_status)
		  FROM $tsa_tbl
		  WHERE $f_ts_id = $testset_id
		  AND $f_status = '$status'";

	$num_status = db_get_one( $db, $q );

	return $num_status;

}


function results_email($project_id, $release_id, $build_id, $testset_id, $test_id, $recipients, $action) {

	$display_generic_info 	= true;
	$display_generic_url	= true;

	$generic_url = RTH_URL."login.php?project_id=$project_id&page=results_test_run_page.php&release_id=$release_id&build_id=$build_id&testset_id=$testset_id&test_id=$test_id";

	$username				= session_get_username();
	$project_name			= session_get_project_name();
	$release_name			= admin_get_release_name($release_id);
	$build_name				= admin_get_build_name($build_id);
	$testset_name			= admin_get_testset_name($testset_id);

	$user_details			= user_get_name_by_username($username);
	$first_name				= $user_details[USER_FNAME];
	$last_name				= $user_details[USER_LNAME];

	$row_test_detail	= testset_query_test_details( $testset_id, $test_id );

	$test_name      	= $row_test_detail[TEST_NAME];
	$status				= $row_test_detail[TEST_TS_ASSOC_STATUS];
	$finished			= $row_test_detail[TEST_TS_ASSOC_FINISHED];
	$assigned_to		= $row_test_detail[TEST_TS_ASSOC_ASSIGNED_TO];
	$comments			= $row_test_detail[TEST_TS_ASSOC_COMMENTS];
	$root_cause			= $row_test_detail[TEST_RESULTS_ROOT_CAUSE];

	# CREATE EMAIL SUBJECT AND MESSAGE
	switch($action) {
	case"test_run":

		$subject = "RTH: Test Run Notification - $test_name";
		$message = "Test $test_name has been run by $first_name $last_name\n". NEWLINE;
		break;

	case"update_test_result":

		$subject = "RTH: Test Result has been Updated";
		$message = "The test result for $test_name has been updated by $first_name $last_name\n". NEWLINE;
		break;
	}

	# Generic link to results page if the $generic_url variable has been set
	if( $display_generic_url ) {
		$message .= "Click the following link to view the Test Results:". NEWLINE;
		$message .= "$generic_url\n". NEWLINE;
	}

	if( $display_generic_info ) {

		$message .= "Project Name: $project_name\r". NEWLINE;
		$message .= "Release Name: $release_name\r". NEWLINE;
		$message .= "Build Name: $build_name\r". NEWLINE;
		$message .= "TestSet Name: $testset_name\r\n\r". NEWLINE;
		$message .= "Test Name: $test_name\r". NEWLINE;
		$message .= "Status: $status\r". NEWLINE;
		if( !empty($root_cause) ) {
			$message		.= "Root Cause: $root_cause\r". NEWLINE;
		}
		$message		.= "Comments: $comments\r\n\r". NEWLINE;
	}

	email_send($recipients, $subject, $message);
}


##########################################################################
# Build the session data used on the view_verfications page
# This function is used when navigating from the bug to a test result
# The bug page contains only the verification_id so we must
# get the session data needed in the results section in order for the
# results sub-menu to appear properly
# INPUT: Verification ID
# OUTPUT: An array containing the session data needed for the results pages
############################################################################
function results_build_session_data_from_verification_id( $verify_id ) {

	global $db;
	$vr_tbl				= VERIFY_RESULTS_TBL;
	$f_verify_id		= $vr_tbl .".". VERIFY_RESULTS_ID;
	$f_vr_run_id		= $vr_tbl .".". VERIFY_RESULTS_TS_UNIQUE_RUN_ID;

	$results_tbl		= TEST_RESULTS_TBL;
	$f_results_id		= $results_tbl .".". TEST_RESULTS_ID;
	$f_test_run_id		= $results_tbl .".". TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_testset_id		= $results_tbl .".". TEST_RESULTS_TEST_SET_ID;
	$f_test_id			= $results_tbl .".". TEST_RESULTS_TEMPEST_TEST_ID;

	# Get the unique_test_run_id from the verify results table
	$q = "SELECT $f_vr_run_id
		  FROM $vr_tbl
		  WHERE $f_verify_id = '$verify_id'";

	$test_run_id = db_get_one( $db, $q );

	# Get the testset_id and test_id from the test_run_id supplied above
	$q2 = "SELECT DISTINCT $f_testset_id, $f_test_id
		   FROM $results_tbl
		   WHERE $f_test_run_id = '$test_run_id'";

	$rs			= db_query( $db, $q2 );
	$row		= db_fetch_row( $db, $rs );
	$test_id	= $row[TEST_RESULTS_TEMPEST_TEST_ID];
	$testset_id	= $row[TEST_RESULTS_TEST_SET_ID];

	# Get the build_id from the test set table
	$build_id = admin_get_build_id_from_testset_id( $testset_id );

	# Get the release_id from the build table
	$release_id = admin_get_release_id_from_build_id( $build_id );

	# pass the array into $_GET.  The results_view_verification_page uses these variables
	$array = array( $_GET['release_id']=$release_id,
					$_GET['build_id']=$build_id,
					$_GET['testset_id']=$testset_id,
					$_GET['test_id']=$test_id,
					$_GET['test_run_id']=$test_run_id);

	# We might want to pass the array into session_set_properties directly and set
	# the session variable in this function
	#session_set_properties( "results", $_GET );
	return $array;

}

#---------------------------------------------------------------------------
# Calculate the total duration of all the tests in a testset
# This function is used to estimate how much test time is remaining
# to complete all the testing in a testset
#---------------------------------------------------------------------------
function results_calculate_total_duration( $testset_id ) {

	global $db;
	$test_set_duration = 0;
	$total_duration = 0;

	$test_ts_tbl	= TEST_TS_ASSOC_TBL;
	$f_testset_id	= TEST_TS_ASSOC_TS_ID;
	$f_ts_test_id	= TEST_TS_ASSOC_TEST_ID;
	$f_status		= TEST_TS_ASSOC_STATUS;

	$test_tbl       = TEST_TBL;
	$f_test_id		= TEST_ID;
	$f_duration		= TEST_DURATION;

	$q = "SELECT $f_ts_test_id, $f_status
		  FROM $test_ts_tbl
		  WHERE $f_testset_id = '$testset_id'";
	$rs = db_query( $db, $q );

	while( $row = db_fetch_row( $db, $rs ) ) {

		$test_id 	 = $row[TEST_TS_ASSOC_TEST_ID];
		$test_status = $row[TEST_TS_ASSOC_STATUS];

		$q_duration = "SELECT $f_duration
					   FROM $test_tbl
					   WHERE $f_test_id = '$test_id'";

		$test_duration = db_get_one( $db, $q_duration );

		$total_duration += $test_duration; # store total of all test. If = 0 then duration wasn't specified.


	}

	 if( $total_duration == 0 ) { # exit if none of the tests have specified a duration
	 	return "Duration Not Specified";
	 }

	 $time = split('\.', ($total_duration / 60) );
	 $hours = $time[0];
	 $minutes = ( $total_duration % 60 );

	 $duration = $hours . " Hours - " . $minutes . " minutes";

	 return $duration;

}

#---------------------------------------------------------------------------
# Calculate the remaining duration of all the tests in a testset
# This function is used to estimate how much test time is remaining
# to complete all the testing in a testset
#---------------------------------------------------------------------------
function results_calculate_remaining_duration( $testset_id, $total_duration ) {

	global $db;
	$test_duration = 0;
	$remaining_duration = 0;

	$test_ts_tbl	= TEST_TS_ASSOC_TBL;
	$f_testset_id	= TEST_TS_ASSOC_TS_ID;
	$f_ts_test_id	= TEST_TS_ASSOC_TEST_ID;
	$f_status		= TEST_TS_ASSOC_STATUS;

	$test_tbl       = TEST_TBL;
	$f_test_id		= TEST_ID;
	$f_duration		= TEST_DURATION;

	$q = "SELECT $f_ts_test_id, $f_status
		  FROM $test_ts_tbl
		  WHERE $f_testset_id = '$testset_id'";
	$rs = db_query( $db, $q );

	while( $row = db_fetch_row( $db, $rs ) ) {

		$test_id 	 = $row[TEST_TS_ASSOC_TEST_ID];
		$test_status = $row[TEST_TS_ASSOC_STATUS];

		$q_duration = "SELECT $f_duration
					   FROM $test_tbl
					   WHERE $f_test_id = '$test_id'";


		$test_duration = db_get_one( $db, $q_duration );

		if( $test_status == 'WIP' || $test_status == 'Not Started' ) {
			$remaining_duration += $test_duration;
		}

	}

	 $time = split('\.', ($remaining_duration / 60) );
	 $hours = $time[0];
	 $minutes = ( $remaining_duration % 60 );

	 $remaining_duration = $hours . " Hours - " . $minutes . " minutes";

	 if($total_duration == 'Duration Not Specified'){
	 	$remaining_duration = "Duration Not Specified";
	 }
	 return $remaining_duration;

}


# ------------------------------------
# $Log: results_api.php,v $
# Revision 1.13  2008/01/22 09:49:06  cryobean
# added some OS entries
#
# Revision 1.12  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.11  2006/09/27 05:34:46  gth2
# adding Mantis integration - gth
#
# Revision 1.10  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.9  2006/06/30 00:55:43  gth2
# removing &$db from api files - gth
#
# Revision 1.8  2006/06/24 14:34:15  gth2
# updating changes lost with cvs problem.
#
# Revision 1.7  2006/02/27 17:24:13  gth2
# added autopass and testset duration functionality - gth
#
# Revision 1.6  2006/02/24 11:33:08  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.5  2006/02/09 12:34:26  gth2
# changing db field names for consistency - gth
#
# Revision 1.4  2006/01/20 02:36:03  gth2
# enable export to excel functionaltiy - gth
#
# Revision 1.3  2006/01/16 13:27:48  gth2
# adding excel integration - gth
#
# Revision 1.2  2006/01/08 22:02:08  gth2
# changing Test Set statuses - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ------------------------------------
?>