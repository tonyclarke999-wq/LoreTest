<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Report API
#
# $RCSfile: report_api.php,v $  $Revision: 1.10 $
# ------------------------------------

# ----------------------------------------------------------------------
# Returns the number of tests for an area in a testset
# ----------------------------------------------------------------------
function report_get_num_area_tested_in_testset( $project_id, $testset_id, $area_tested ) {

	global $db;
	$count = 0;

	foreach( testset_get_tests($testset_id) as $row_test ) {

		if( $row_test[TEST_AREA_TESTED] == "$area_tested" ) {

			$count++;
		}
	}

	return $count;
}

# ----------------------------------------------------------------------
# Returns the number of tests in an area
# ----------------------------------------------------------------------
function report_get_num_tests_in_area( $project_id, $area_tested ) {

	global $db;

	$tbl_test		= TEST_TBL;
	$f_name 		= TEST_NAME;
	$f_id 			= TEST_ID;
	$f_area 		= TEST_AREA_TESTED;
	$f_deleted 		= TEST_DELETED;
	$f_archive 		= TEST_ARCHIVED;
	$f_project_id 	= TEST_PROJ_ID;

	$q = "	SELECT	$f_id,
					$f_name,
					$f_area,
					$f_archive
			FROM 	$tbl_test
			WHERE	$f_project_id = $project_id
					AND $f_area = '$area_tested'
					AND	$f_deleted = 'N'";

	global $db;

	$num = db_num_rows($db, db_query($db, $q));

	return $num;
}

# ----------------------------------------------------------------------
# Returns the status report of a testset
# OUTPUT:
# 	number of tests in testset,
#	number of tests passed in testset,
#	number of tests failed in testset,
#	number of tests awaiting review in testset,
#	number of tests wip in testset,
#	number of tests not running in testset,
#	number of tests not started in testset,
#	number of tests finished qa in testset,
#	number of tests finished ba in testset,
#	number of tests winrunner issue in testset
# ----------------------------------------------------------------------
function report_get_testset_status( $testset_id ) {

	global $db;

	$test_count = 0;
	$passed_count = 0;
	$failed_count = 0;
	$awaiting_review_count = 0;
	$wip_count = 0;
	$not_running_count = 0;
	$not_started_count = 0;

	# get associated tests
	$assoc_tbl			= TEST_TS_ASSOC_TBL;
	$f_assoc_id			= $assoc_tbl .".". TEST_TS_ASSOC_ID;
	$f_assoc_ts_id		= $assoc_tbl .".". TEST_TS_ASSOC_TS_ID;
	$f_assoc_test_id	= $assoc_tbl .".". TEST_TS_ASSOC_TEST_ID;
	$f_status			= $assoc_tbl .".". TEST_TS_ASSOC_STATUS;
	$f_finished			= $assoc_tbl .".". TEST_TS_ASSOC_FINISHED;
	$f_assigned_to		= $assoc_tbl .".". TEST_TS_ASSOC_ASSIGNED_TO;
	$f_comments			= $assoc_tbl .".". TEST_TS_ASSOC_COMMENTS;

	$q = "	SELECT
				$f_status,
				COUNT($f_status) AS CountStatus
			FROM
				$assoc_tbl
			WHERE
				$f_assoc_ts_id = $testset_id
			GROUP BY $f_status";


	$row_count_statuses = db_fetch_array( $db, db_query($db, $q) );

	foreach($row_count_statuses as $row_count_status) {

		switch( $row_count_status[TEST_TS_ASSOC_STATUS] ) {
		case "Passed":
			$passed_count+=$row_count_status["CountStatus"];
			break;
		case "Failed":
			$failed_count+=$row_count_status["CountStatus"];
			break;
		case "Finished: Awaiting Review":
			$awaiting_review_count+=$row_count_status["CountStatus"];
			break;
		case "WIP":
			$wip_count+=$row_count_status["CountStatus"];
			break;
		case "Not Running":
			$not_running_count+=$row_count_status["CountStatus"];
			break;
		case "Not Started":
			$not_started_count+=$row_count_status["CountStatus"];
			break;
		}
	}

	$test_count += 	$test_count
					+ $passed_count
					+ $failed_count
					+ $awaiting_review_count
					+ $wip_count
					+ $not_running_count
					+ $not_started_count;

	return array(	$test_count,
					$passed_count,
					$failed_count,
					$awaiting_review_count,
					$wip_count,
					$not_running_count,
					$not_started_count );
}

# ----------------------------------------------------------------------
# Returns the status report of a build
# OUTPUT:
# 	number of tests in build,
#	number of tests passed in build,
#	number of tests failed in build,
#	number of tests awaiting review in build,
#	number of tests wip in build,
#	number of tests not running in build,
#	number of tests not started in build,
#	number of tests finished qa in build,
#	number of tests finished ba in build,
#	number of tests winrunner issue in build
#	number of tests incomplete in build
# ----------------------------------------------------------------------
function report_get_build_status( $build_id ) {

	global $db;

	# build date
	$build_tbl 		= BUILD_TBL;
	$f_build_id		= BUILD_ID;
	$f_release_id	= BUILD_REL_ID;
	$f_build_name	= BUILD_NAME;
	$f_date			= BUILD_DATE_REC;
	$f_description	= BUILD_DESCRIPTION;
	$f_archive		= BUILD_ARCHIVE;

	$q = "	SELECT
				$f_date
			FROM $build_tbl
			WHERE $f_build_id = $build_id";

	$build_date = db_get_one($db, $q);

	# get testsets
	$ts_tbl				= TS_TBL;
	$ts_id				= TS_ID;
	$ts_name 			= TS_NAME;
	$ts_status			= TS_STATUS;
	$ts_desc			= TS_DESCRIPTION;
	$ts_build_id		= TS_BUILD_ID;
	$ts_orderby			= TS_ORDERBY;
	$ts_archive			= TS_ARCHIVE;
	$ts_date_created	= TS_DATE_CREATED;

	$q = "	SELECT
				$ts_id
			FROM $ts_tbl
			WHERE $ts_build_id = $build_id";

	$testsets = db_fetch_array( $db, db_query($db, $q) );

	$test_count = 0;
	$passed_count = 0;
	$failed_count = 0;
	$awaiting_review_count = 0;
	$wip_count = 0;
	$not_running_count = 0;
	$not_started_count = 0;
	$incomplete_count = 0;

	foreach($testsets as $row_testset) {

		# get associated tests
		$assoc_tbl			= TEST_TS_ASSOC_TBL;
		$f_assoc_id			= $assoc_tbl .".". TEST_TS_ASSOC_ID;
		$f_assoc_ts_id		= $assoc_tbl .".". TEST_TS_ASSOC_TS_ID;
		$f_assoc_test_id	= $assoc_tbl .".". TEST_TS_ASSOC_TEST_ID;
		$f_status			= $assoc_tbl .".". TEST_TS_ASSOC_STATUS;
		$f_finished			= $assoc_tbl .".". TEST_TS_ASSOC_FINISHED;
		$f_assigned_to		= $assoc_tbl .".". TEST_TS_ASSOC_ASSIGNED_TO;
		$f_comments			= $assoc_tbl .".". TEST_TS_ASSOC_COMMENTS;

		$q = "	SELECT
					$f_status,
					COUNT($f_status) AS CountStatus
				FROM
					$assoc_tbl
				WHERE
					$f_assoc_ts_id = ".$row_testset[TS_ID]."
				GROUP BY $f_status";


		$row_count_statuses = db_fetch_array( $db, db_query($db, $q) );

		foreach($row_count_statuses as $row_count_status) {

			switch( $row_count_status[TEST_TS_ASSOC_STATUS] ) {
			case "Passed":
				$passed_count+=$row_count_status["CountStatus"];
				break;
			case "Failed":
				$failed_count+=$row_count_status["CountStatus"];
				break;
			case "Finished: Awaiting Review":
				$awaiting_review_count+=$row_count_status["CountStatus"];
				break;
			case "WIP":
				$wip_count+=$row_count_status["CountStatus"];
				break;
			case "Not Running":
				$not_running_count+=$row_count_status["CountStatus"];
				break;
			case "Not Started":
				$not_started_count+=$row_count_status["CountStatus"];
				break;
			case "Incomplete":
				$incomplete_count+=$row_count_status["CountStatus"];
				break;
			}
		}
	}

	$test_count += 	$test_count
					+ $passed_count
					+ $failed_count
					+ $awaiting_review_count
					+ $wip_count
					+ $not_running_count
					+ $not_started_count
					+ $incomplete_count;

	return array(	$build_date,
					sizeof($testsets),
					$test_count,
					$passed_count,
					$failed_count,
					$awaiting_review_count,
					$wip_count,
					$not_running_count,
					$not_started_count,
					$incomplete_count );
}

# ----------------------------------------------------------------------
# Returns the status report of a project
# OUTPUT:
# 	number of tests in project,
#	number of tests passed in project,
#	number of tests failed in project,
#	number of tests awaiting review in project,
#	number of tests wip in project,
#	number of tests not running in project,
#	number of tests not started in project,
#	number of tests finished qa in project,
#	number of tests finished ba in project,
#	number of tests winrunner issue in project
# ----------------------------------------------------------------------
function report_get_project_status( $project_id, $release_id=null, $build_id=null, $testset_id=null ) {

	global $db;
	$tbl_release 		= RELEASE_TBL;
	$f_rel_id			= $tbl_release .".". RELEASE_ID;
	$f_rel_name			= $tbl_release .".". RELEASE_NAME;
	$f_project_id		= $tbl_release .".". RELEASE_PROJECT_ID;

	$tbl_build			= BUILD_TBL;
	$f_build_id			= $tbl_build .".". BUILD_ID;
	$f_build_release_id	= $tbl_build .".". BUILD_REL_ID;

	# testsets
	$ts_tbl				= TS_TBL;
	$ts_id				= $ts_tbl .".". TS_ID;
	$ts_name 			= $ts_tbl .".". TS_NAME;
	$ts_status			= $ts_tbl .".". TS_STATUS;
	$ts_desc			= $ts_tbl .".". TS_DESCRIPTION;
	$ts_build_id		= $ts_tbl .".". TS_BUILD_ID;
	$ts_orderby			= $ts_tbl .".". TS_ORDERBY;
	$ts_archive			= $ts_tbl .".". TS_ARCHIVE;
	$ts_date_created	= $ts_tbl .".". TS_DATE_CREATED;

	$q = "	SELECT
				$ts_id
			FROM $ts_tbl
			INNER JOIN $tbl_build
				ON $f_build_id = $ts_build_id
			INNER JOIN $tbl_release
				ON $f_rel_id = $f_build_release_id
			WHERE $f_project_id = $project_id";

	$testsets = db_fetch_array($db, db_query($db, $q));

	$test_count = 0;
	$passed_count = 0;
	$failed_count = 0;
	$awaiting_review_count = 0;
	$wip_count = 0;
	$not_running_count = 0;
	$not_started_count = 0;

	foreach($testsets as $row_testset) {

		# number of tests
		$assoc_tbl			= TEST_TS_ASSOC_TBL;
		$f_assoc_id			= $assoc_tbl .".". TEST_TS_ASSOC_ID;
		$f_assoc_ts_id		= $assoc_tbl .".". TEST_TS_ASSOC_TS_ID;
		$f_assoc_test_id	= $assoc_tbl .".". TEST_TS_ASSOC_TEST_ID;
		$f_status			= $assoc_tbl .".". TEST_TS_ASSOC_STATUS;
		$f_finished			= $assoc_tbl .".". TEST_TS_ASSOC_FINISHED;
		$f_assigned_to		= $assoc_tbl .".". TEST_TS_ASSOC_ASSIGNED_TO;
		$f_comments			= $assoc_tbl .".". TEST_TS_ASSOC_COMMENTS;

		$q = "	SELECT
					$f_status,
					COUNT($f_status) AS CountStatus
				FROM
					$assoc_tbl
				WHERE
					$f_assoc_ts_id = ".$row_testset[TS_ID]."
				GROUP BY $f_status";


		$row_count_statuses = db_fetch_array( $db, db_query($db, $q) );

		foreach($row_count_statuses as $row_count_status) {

			switch( $row_count_status[TEST_TS_ASSOC_STATUS] ) {
			case "Passed":
				$passed_count+=$row_count_status["CountStatus"];
				break;
			case "Failed":
				$failed_count+=$row_count_status["CountStatus"];
				break;
			case "Finished: Awaiting Review":
				$awaiting_review_count+=$row_count_status["CountStatus"];
				break;
			case "WIP":
				$wip_count+=$row_count_status["CountStatus"];
				break;
			case "Not Running":
				$not_running_count+=$row_count_status["CountStatus"];
				break;
			case "Not Started":
				$not_started_count+=$row_count_status["CountStatus"];
				break;
			}
		}

		$test_count = sizeof( test_get_all_ids( $project_id ) );
	}
	

	return array(	sizeof($testsets),
					$test_count,
					$passed_count,
					$failed_count,
					$awaiting_review_count,
					$wip_count,
					$not_running_count,
					$not_started_count );
}

# ----------------------------------------------------------------------
# Returns verifications summary for testset
# OUTPUT:
#	number verifications passed
#	number verifications failed
#	number verifications with bugs
# ----------------------------------------------------------------------
function report_get_verifs_summary( $testset_id ) {

	$tbl_verify_results			= VERIFY_RESULTS_TBL;
	$f_verify_test_status		= $tbl_verify_results .".". VERIFY_RESULTS_TEST_STATUS;
	$f_verify_unique_run		= $tbl_verify_results .".". VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
	$f_verify_defect_id			= $tbl_verify_results .".". VERIFY_RESULTS_DEFECT_ID;

	$tbl_ts_results				= TEST_RESULTS_TBL;
	$f_ts_results_testset_id	= $tbl_ts_results .".". TEST_RESULTS_TEST_SET_ID;
	$f_ts_results_unique_run_id	= $tbl_ts_results .".". TEST_RESULTS_TS_UNIQUE_RUN_ID;

	$tbl_testset		= TS_TBL;
	$f_testset_id		= $tbl_testset .".". TS_ID;
	$f_testset_archive	= $tbl_testset .".". TS_ARCHIVE;

	$q = "	SELECT
				COUNT(IF($f_verify_test_status='Pass', true, null)) as Passed,
				COUNT(IF($f_verify_test_status='Fail', true, null)) as Failed,
				COUNT(IF($f_verify_test_status='Info', true, null)) as Info,
				COUNT(IF($f_verify_defect_id != 0, true, null)) as Bugs
			FROM $tbl_verify_results
			INNER JOIN $tbl_ts_results
				ON $f_verify_unique_run = $f_ts_results_unique_run_id
			INNER JOIN $tbl_testset
				ON $f_testset_id = $f_ts_results_testset_id
			WHERE
				$f_testset_id = $testset_id
				AND $f_testset_archive = 'N'";

	global $db;

	$rs = db_query($db, $q);
	$row = db_fetch_row($db, $rs);

	return $row;
}

# ----------------------------------------------------------------------
# Returns failed verifications for testset
# ----------------------------------------------------------------------
function report_get_failed_verifs( $testset_id, $order_by=VERIFY_RESULTS_ID, $order_dir="ASC", $page_number=1, $bugs_only=false ) {

	$tbl_ts_results				= TEST_RESULTS_TBL;
	$f_ts_results_testset_id	= $tbl_ts_results .".". TEST_RESULTS_TEST_SET_ID;
	$f_ts_results_unique_run_id	= $tbl_ts_results .".". TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_ts_results_ts			= $tbl_ts_results .".". TEST_RESULTS_TEST_SUITE;
	$f_ts_results_os			= $tbl_ts_results .".". TEST_RESULTS_OS;

	$tbl_testset		= TS_TBL;
	$f_testset_id		= $tbl_testset .".". TS_ID;
	$f_testset_archive	= $tbl_testset .".". TS_ARCHIVE;

	$tbl_verify_results			= VERIFY_RESULTS_TBL;
	$f_verify_results_id		= $tbl_verify_results .".". VERIFY_RESULTS_ID;
	$f_verify_test_status		= $tbl_verify_results .".". VERIFY_RESULTS_TEST_STATUS;
	$f_verify_unique_run		= $tbl_verify_results .".". VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
	$f_verify_log_time			= $tbl_verify_results .".". VERIFY_RESULTS_LOG_TIME_STAMP;
	$f_verify_test_status		= $tbl_verify_results .".". VERIFY_RESULTS_TEST_STATUS;
	$f_verify_line_num			= $tbl_verify_results .".". VERIFY_RESULTS_LINE_NUMBER;
	$f_verify_tot_phy_mem		= $tbl_verify_results .".". VERIFY_RESULTS_TOTAL_PHY_MEM;
	$f_verify_free_phy_mem		= $tbl_verify_results .".". VERIFY_RESULTS_FREE_PHY_MEM;
	$f_verify_tot_vir_mem		= $tbl_verify_results .".". VERIFY_RESULTS_TOTAL_VIR_MEM;
	$f_verify_free_vir_mem		= $tbl_verify_results .".". VERIFY_RESULTS_FREE_VIR_MEM;
	$f_verify_cur_mem_util		= $tbl_verify_results .".". VERIFY_RESULTS_CUR_MEM_UTIL;
	$f_verify_tot_page_file		= $tbl_verify_results .".". VERIFY_RESULTS_TOTAL_PAGE_FILE;
	$f_verify_free_page_file	= $tbl_verify_results .".". VERIFY_RESULTS_FREE_PAGE_FILE;
	$f_verify_custom_1			= $tbl_verify_results .".". VERIFY_RESULTS_SHOW_CUSTOM_1;
	$f_verify_custom_2			= $tbl_verify_results .".". VERIFY_RESULTS_SHOW_CUSTOM_2;
	$f_verify_custom_3			= $tbl_verify_results .".". VERIFY_RESULTS_SHOW_CUSTOM_3;
	$f_verify_custom_4			= $tbl_verify_results .".". VERIFY_RESULTS_SHOW_CUSTOM_4;
	$f_verify_custom_5			= $tbl_verify_results .".". VERIFY_RESULTS_SHOW_CUSTOM_5;
	$f_verify_custom_6			= $tbl_verify_results .".". VERIFY_RESULTS_SHOW_CUSTOM_6;
	$f_verify_comment			= $tbl_verify_results .".". VERIFY_RESULTS_COMMENT;
	$f_verify_action			= $tbl_verify_results .".". VERIFY_RESULTS_ACTION;
	$f_verify_expected_result	= $tbl_verify_results .".". VERIFY_RESULTS_EXPECTED_RESULT;
	$f_verify_actual_result		= $tbl_verify_results .".". VERIFY_RESULTS_ACTUAL_RESULT;
	$f_verify_results_window	= $tbl_verify_results .".". VERIFY_RESULTS_WINDOW;
	$f_verify_object			= $tbl_verify_results .".". VERIFY_RESULTS_OBJ;
	$f_verify_object_type		= $tbl_verify_results .".". VERIFY_RESULTS_OBJ_TYPE;
	$f_verify_validation_id		= $tbl_verify_results .".". VERIFY_RESULTS_VAL_ID;
	$f_verify_ts_run_id			= $tbl_verify_results .".". VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
	$f_verify_time				= $tbl_verify_results .".". VERIFY_RESULTS_TIMESTAMP;
	$f_verify_defect_id			= $tbl_verify_results .".". VERIFY_RESULTS_DEFECT_ID;

	$q = "	SELECT
				$f_ts_results_unique_run_id,
				$f_ts_results_ts,
				$f_ts_results_os,
				$f_verify_action,
				$f_verify_test_status,
				$f_verify_time,
				$f_verify_results_window,
				$f_verify_object,
				$f_verify_custom_1,
				$f_verify_custom_2,
				$f_verify_custom_3,
				$f_verify_custom_4,
				$f_verify_custom_5,
				$f_verify_custom_6,
				$f_verify_validation_id,
				$f_verify_results_id,
				$f_verify_actual_result,
				$f_verify_expected_result,
				$f_verify_defect_id
			FROM $tbl_verify_results
			INNER JOIN $tbl_ts_results
				ON $f_verify_unique_run = $f_ts_results_unique_run_id
			INNER JOIN $tbl_testset
				ON $f_testset_id = $f_ts_results_testset_id
			WHERE
				$f_testset_id = $testset_id
				AND $f_testset_archive = 'N'";
			if($bugs_only) {
				$q .= " AND $f_verify_defect_id != 0";
			} else {
				$q .= " AND $f_verify_test_status = 'FAIL'";
			}
	$q .= "	ORDER BY $order_by $order_dir";

	//print"$q<br>";

	global $db;

	if( RECORDS_PER_PAGE_REPORT_FAILED_VERIFIS!=0 ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_REPORT_FAILED_VERIFIS );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_REPORT_FAILED_VERIFIS,
							$page_number );

		$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_REPORT_FAILED_VERIFIS;

	}

	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

# ----------------------------------------------------------------------
# Returns test signoff details
# OUTPUT:
#	test_name,
#	test_id,
#	test_area,
#	test_type,
#	test_man,
#	test_auto,
#	ts_assoc_test_status,
#	ts_assoc_comments,
#	ts_assoc_assigned_to,
#	ts_assoc_timestamp
# ----------------------------------------------------------------------
function report_get_test_signoff_details( $testset_id, $order_by, $order_dir, $page ) {

	$tbl_test		= TEST_TBL;
	$f_test_id		= $tbl_test .".". TEST_ID;
	$f_test_		= $tbl_test .".". TEST_PROJ_ID;
	$f_test_deleted	= $tbl_test .".". TEST_DELETED;
	$f_test_archived= $tbl_test .".". TEST_ARCHIVED;
	$f_test_		= $tbl_test .".". TEST_CODE_REVIEW;
	$f_test_		= $tbl_test .".". TEST_BA_APPROVAL;
	$f_test_man		= $tbl_test .".". TEST_MANUAL;
	$f_test_auto		= $tbl_test .".". TEST_AUTOMATED;
	$f_test_		= $tbl_test .".". TEST_LR;
	$f_test_		= $tbl_test .".". TEST_AUTO_PASS;
	$f_test_		= $tbl_test .".". TEST_DURATION;
	$f_test_		= $tbl_test .".". TEST_PURPOSE;
	$f_test_name		= $tbl_test .".". TEST_NAME;
	$f_test_type		= $tbl_test .".". TEST_TESTTYPE;
	$f_test_area	= $tbl_test .".". TEST_AREA_TESTED;
	$f_test_		= $tbl_test .".". TEST_BA_OWNER;
	$f_test_		= $tbl_test .".". TEST_QA_OWNER;
	$f_test_		= $tbl_test .".". TEST_APPROVED_FOR_AUTO;
	$f_test_		= $tbl_test .".". TEST_PRIORITY;
	$f_test_status		= $tbl_test .".". TEST_STATUS;
	$f_test_		= $tbl_test .".". TEST_COMMENTS;
	$f_test_		= $tbl_test .".". TEST_ASSIGNED_TO;
	$f_test_		= $tbl_test .".". TEST_ASSIGNED_BY;
	$f_test_		= $tbl_test .".". TEST_DATE_ASSIGNED;
	$f_test_		= $tbl_test .".". TEST_DATE_EXPECTED;
	$f_test_		= $tbl_test .".". TEST_DATE_COMPLETE;
	$f_test_		= $tbl_test .".". TEST_BA_SIGNOFF;
	$f_test_		= $tbl_test .".". TEST_UNIQUE_ID;

	$ts_assoc_tbl           = TEST_TS_ASSOC_TBL;
	$f_ts_assoc_id          = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ID;
	$f_ts_assoc_ts_id       = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TS_ID;
	$f_ts_assoc_test_id     = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TEST_ID;
	$f_ts_assoc_test_status = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_STATUS;
	$f_ts_assoc_assigned_to = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ASSIGNED_TO;
	$f_ts_assoc_comments    = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_COMMENTS;
	$f_ts_assoc_timestamp   = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TIMESTAMP;

	$tbl_ts_results				= TEST_RESULTS_TBL;
	$f_ts_results_testset_id	= $tbl_ts_results .".". TEST_RESULTS_TEST_SET_ID;
	$f_ts_results_unique_run_id	= $tbl_ts_results .".". TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_ts_results_ts			= $tbl_ts_results .".". TEST_RESULTS_TEST_SUITE;
	$f_ts_results_os			= $tbl_ts_results .".". TEST_RESULTS_OS;

	$q = " SELECT
				$f_test_name,
				$f_test_id,
				$f_test_area,
				$f_test_type,
				$f_test_man,
				$f_test_auto,
				$f_ts_assoc_test_status,
				$f_ts_assoc_comments,
				$f_ts_assoc_assigned_to,
				$f_ts_assoc_timestamp
			FROM $tbl_test
			INNER JOIN $ts_assoc_tbl
				ON $f_ts_assoc_test_id= $f_test_id
				AND $f_ts_assoc_ts_id = $testset_id
			WHERE
				$f_test_archived = 'N'
				AND $f_test_deleted = 'N'
			ORDER BY $order_by $order_dir";
//print $q;exit;
	global $db;

	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}


# ----------------------------------------------------------------------
# Get all requirements
# ----------------------------------------------------------------------
function report_requirements_get($release_id, $order_by=REQ_VERS_FILENAME, $order_dir="ASC", $page_number=1) {

	$tbl_req 			= REQ_TBL;
	$f_req_id 			= REQ_TBL .".". REQ_ID;
	$f_req_filename 	= REQ_TBL .".". REQ_FILENAME;
	$f_req_area_covered	= REQ_TBL .".". REQ_AREA_COVERED;
	$f_req_type		 	= REQ_TBL .".". REQ_TYPE;
	$f_req_parent	 	= REQ_TBL .".". REQ_PARENT;
	$f_req_label	 	= REQ_TBL .".". REQ_LABEL;
	$f_req_unique_id 	= REQ_TBL .".". REQ_UNIQUE_ID;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$release_tbl 	= RELEASE_TBL;
	$f_release_id	= $release_tbl.".".RELEASE_ID;
	$f_release_name	= $release_tbl.".".RELEASE_NAME;

	$tbl_req_area_covered		= REQ_AREA_COVERAGE_TBL;
	$f_req_area_covered_name	= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_req_area_covered_id		= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_req_area_covered_proj_id	= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	$q = "	SELECT
				$f_req_id,
				$f_req_area_covered_name,
				$f_req_filename,
				$f_req_ver_version
			FROM $tbl_req
			INNER JOIN $tbl_req_ver
				ON $f_req_ver_req_id = $f_req_id
			INNER JOIN $tbl_req_ver_assoc_rel
				ON $f_req_ver_assoc_rel_req_id = $f_req_ver_uid
			INNER JOIN $release_tbl
				ON $f_release_id = $f_req_ver_assoc_rel_rel_id
			LEFT JOIN $tbl_req_area_covered
				ON $f_req_area_covered_id = $f_req_area_covered
			WHERE
				$f_req_ver_assoc_rel_rel_id = $release_id
				AND $f_req_ver_latest = 'Y'
			ORDER BY $order_by $order_dir";

	global $db;
	/*
	if( RECORDS_PER_PAGE_REPORT_FAILED_VERIFIS!=0 ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_REPORT_FAILED_VERIFIS );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_REPORT_FAILED_VERIFIS,
							$page_number );

		$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_REPORT_FAILED_VERIFIS;

	}*/

//print$q;exit;

	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

# ------------------------------------
# $Log: report_api.php,v $
# Revision 1.10  2009/01/09 08:10:53  cryobean
# implemented feature request 2435387
# included the not finished tests of testsets in calculation
# fix was sent by Bruce Butler
#
# Revision 1.9  2008/08/08 12:03:12  peter_thal
# fixed bug, disabled deleted test count
#
# Revision 1.8  2007/02/03 10:25:53  gth2
# no message
#
# Revision 1.7  2006/09/28 02:42:04  gth2
# display correct bug ID on FailedVerificationsReport - gth
#
# Revision 1.6  2006/06/10 01:55:03  gth2
# no message
#
# Revision 1.5  2006/02/24 11:33:31  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.4  2006/02/09 12:34:26  gth2
# changing db field names for consistency - gth
#
# Revision 1.3  2006/01/14 19:08:03  gth2
# accounting for division by zero error - gth
#
# Revision 1.2  2005/12/08 19:39:51  gth2
# updating reports containing calls to jp-graph - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ------------------------------------
?>
