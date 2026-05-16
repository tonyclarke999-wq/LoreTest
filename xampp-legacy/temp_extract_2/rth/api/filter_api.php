<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Filter API
#
# $RCSfile: filter_api.php,v $ $Revision: 1.2 $
# ------------------------------------


function filter_results_page( $page, $manauto, $baowner, $qaowner,$tester, $testtype, $test_area, $test_status, $per_page, $orderby,
                          $order_dir, $page_number, $release_id, $build_id, $testset_id ) {

    global $db;
    $test_tbl               = TEST_TBL;
    $test_id                = TEST_TBL. "." .TEST_ID;
    $test_name              = TEST_TBL. "." .TEST_NAME;
    $manual_tests           = TEST_TBL. "." .TEST_MANUAL;
    $automated_tests        = TEST_TBL. "." .TEST_AUTOMATED;
    $ba_owner               = TEST_TBL. "." .TEST_BA_OWNER;
    $qa_owner               = TEST_TBL. "." .TEST_QA_OWNER;
    $test_tester			= TEST_TBL. "." .TEST_TESTER;
    $test_load              = TEST_TBL. "." .TEST_LR;
    $test_type              = TEST_TBL. "." .TEST_TESTTYPE;
    $area_tested            = TEST_TBL. "." .TEST_AREA_TESTED;
    $deleted                = TEST_TBL. "." .TEST_DELETED;
    $archived               = TEST_TBL. "." .TEST_ARCHIVED;
    $test_priority          = TEST_TBL. "." .TEST_PRIORITY;
    $auto_pass              = TEST_TBL. "." .TEST_AUTO_PASS;

    $ts_assoc_tbl           = TEST_TS_ASSOC_TBL;
    $ts_assoc_id            = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ID;
    $ts_assoc_ts_id         = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TS_ID;
    $ts_assoc_test_id       = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TEST_ID;
    $ts_assoc_finished      = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_FINISHED;
    $ts_assoc_timestamp     = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TIMESTAMP;
    $ts_assoc_test_status   = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_STATUS;
    $ts_assoc_assigned_to   = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ASSIGNED_TO;
    $ts_assoc_comments      = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_COMMENTS;

    $limit_clause       = '';

    //$q = "SELECT * FROM $test_tbl WHERE $deleted = 'N' AND $archived = 'N'";
    $q = "SELECT $test_name, $test_id, $ts_assoc_id, $ts_assoc_test_status, $ts_assoc_comments, $ts_assoc_assigned_to, $manual_tests, $automated_tests, $qa_owner, $test_load, $ba_owner, $test_priority, $area_tested, $auto_pass, $test_type FROM $ts_assoc_tbl INNER JOIN $test_tbl ON $ts_assoc_test_id = $test_id WHERE $ts_assoc_ts_id = '$testset_id' AND $deleted = 'N'";

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
    # TESTER
	if ( !empty($tester) && $tester != 'all') {
	
	    $where_clause = $where_clause." AND $test_tester = '$tester'";
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
	    if ( !empty($test_status ) && $test_status != 'all') {

	        $where_clause = $where_clause." AND $ts_assoc_test_status = '$test_status'";
    }

    $where_clause = $where_clause." GROUP BY $test_name";
    $order_clause = " ORDER BY $orderby $order_dir";
    $q = $q.$where_clause.$order_clause;
    //print"$q<br>";
    $rs_count = db_query( $db, $q ); //$db->Execute( $q );
    $q_count = db_num_rows( $db, $rs_count ); //$rs_count->NumRows();

    # add a table header that includes the pages showing, export to csv, and links to other result pages
    $page_count = ceil($q_count / $per_page );

    if( $page_number > $page_count ) {
        $page_number = $page_count;
    }

    # Add the limit clause to the query so that we only show n number of records per page
    $offset = ( ( $page_number - 1 ) * $per_page );
    $limit_clause .= " LIMIT $offset, $per_page";

    $q = $q . $limit_clause;

    $rs = db_query($db, $q);

    $rows = db_fetch_array($db, $rs);

    print"<table class=hide100 rules='all' border='1'>"; // rules=all
        print"<tr>";
        html_print_records($q_count, $per_page, $page_number, $page_count);
        print"<td class='tbl-c' width='33%'><a href='csv_export.php?rs=$row'>";
		if( IMPORT_EXPORT_TO_EXCEL ) {
			print lang_get('excel_export');
		} 
		else {
			print lang_get('csv_export');
		}
		print"</a></td>";
        html_page_links( $page, 1, $page_count, $page_number );
        print"</tr>";
    print"</table>";

    return $rows;
}

# ------------------------------------
# $Log: filter_api.php,v $
# Revision 1.2  2006/01/20 02:36:03  gth2
# enable export to excel functionaltiy - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# ------------------------------------
?>