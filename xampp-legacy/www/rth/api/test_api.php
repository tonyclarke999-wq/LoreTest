<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Test API
#
# $RCSfile: test_api.php,v $ 
# $Revision: 1.31 $
# ------------------------------------

# ----------------------------------------------------------------------4/13/2006
# Returns array of all test statuses with optional blank value at end.
#
# OUTPUT:
#   array of all unique test statuses.
# ----------------------------------------------------------------------
function test_get_status( $blank=false ) {

	$test_status = array(	'New',
							'Assigned',
							'WIP',
							'Ready for Review',
							'Completed',
							'Rework',
							'Review Test Case',
							'Review Requirement');

	# add a blank value to the array.  Useful when populating a listbox when you want ' ' as an option
	if( $blank ) {
		$test_status[] = "";
	}

	return $test_status;
}

# ----------------------------------------------------------------------
# Create and run query for displaying test records. Display table header.
# OUTPUT:
#   array of test records.
# ----------------------------------------------------------------------
function &test_apply_filter($project_id, $where_clause, $per_page, $order_by, $order_dir, $page_number, $csv_name=null) {

    global $db;
	# WE SHOULD REWRITE TEST_GET_DETAIL SO YOU CAN INPUT A WHERE CLAUSE.
	$test_tbl				= TEST_TBL;
	$f_project_id			= $test_tbl .".". TEST_PROJ_ID;
	$f_test_id				= $test_tbl .".". TEST_ID;
	$f_test_name			= $test_tbl .".". TEST_NAME;
	$f_manual_test			= $test_tbl .".". TEST_MANUAL;
	$f_automated_test		= $test_tbl .".". TEST_AUTOMATED;
	$f_ba_owner				= $test_tbl .".". TEST_BA_OWNER;
	$f_qa_owner				= $test_tbl .".". TEST_QA_OWNER;
	$f_tester				= $test_tbl .".". TEST_TESTER;
	$f_test_type			= $test_tbl .".". TEST_TESTTYPE;
    $f_area_tested			= $test_tbl .".". TEST_AREA_TESTED;
	$f_test_priority		= $test_tbl .".". TEST_PRIORITY;
	$f_test_status			= $test_tbl .".". TEST_STATUS;
    $f_deleted				= $test_tbl .".". TEST_DELETED;
    $f_archived				= $test_tbl .".". TEST_ARCHIVED;
	$f_autopass				= $test_tbl .".". TEST_AUTO_PASS;
	$f_dateassigned			= $test_tbl .".". TEST_DATE_ASSIGNED;
	$f_dateexpcomplete		= $test_tbl .".". TEST_DATE_EXPECTED;
	$f_dateactcomplete		= $test_tbl .".". TEST_DATE_COMPLETE;
	$f_datebasignoff		= $test_tbl .".". TEST_BA_SIGNOFF;
	$f_test_comments		= $test_tbl .".". TEST_COMMENTS;
	/*
	$f_project_id		  = $test_tbl .".". PROJECT_ID;
	$f_test_type          = $test_tbl .".". TEST_TESTTYPE;
    $f_area_tested        = $test_tbl .".". TEST_AREA_TESTED;
	$f_autopass           = $test_tbl .".". TEST_AUTO_PASS;
	$f_test_deleted       = $test_tbl .".". TEST_DELETED;
    $f_test_archived      = $test_tbl .".". TEST_ARCHIVED;
	$f_test_purpose       = $test_tbl .".". TEST_PURPOSE;
	$f_test_comments      = $test_tbl .".". TEST_COMMENTS;
	$f_manual_tests       = $test_tbl .".". TEST_MANUAL;
	$f_manual_test        = $test_tbl .".". TEST_MANUAL;
	$f_performance        = $test_tbl .".". TEST_LR;
	$f_automated_test     = $test_tbl .".". TEST_AUTOMATED;
	$f_ba_owner           = $test_tbl .".". TEST_BA_OWNER;
	$f_qa_owner           = $test_tbl .".". TEST_QA_OWNER;
	$f_test_priority      = $test_tbl .".". TEST_PRIORITY;
	$f_assigned_to        = $test_tbl .".". TEST_ASSIGNED_TO;
	$f_assigned_by        = $test_tbl .".". TEST_ASSIGNED_BY;
	$f_dateassigned       = $test_tbl .".". TEST_DATE_ASSIGNED;
	$f_dateexpcomplete    = $test_tbl .".". TEST_DATE_EXPECTED;
	$f_dateactcomplete    = $test_tbl .".". TEST_DATE_COMPLETE;
	$f_datebasignoff      = $test_tbl .".". TEST_BA_SIGNOFF;
	$f_duration			  = $test_tbl .".". TEST_DURATION;
	*/

	//$test_ver_tbl		= TEST_VERS_TBL;
	//$f_vers_test_id		= $test_ver_tbl .".". TEST_VERS_TEST_ID;
	//$f_vers_id			= $test_ver_tbl .".". TEST_VERS_ID;

	$q = "SELECT
			$f_test_id,
			$f_test_name,
			$f_manual_test,
			$f_automated_test,
			$f_ba_owner,
			$f_qa_owner,
			$f_tester,
			$f_test_type,
			$f_area_tested,
			$f_test_priority,
			$f_test_status,
			$f_autopass,
			$f_dateassigned,
			$f_dateexpcomplete,
			$f_dateactcomplete,
			$f_datebasignoff,
			$f_test_comments
		 FROM $test_tbl
		 WHERE $f_project_id = '$project_id'
		 AND $f_deleted = 'N'
		 AND $f_archived = 'N'";
	/*
    $q = "SELECT $test_tbl.*, $f_vers_id
		 FROM $test_tbl, $test_ver_tbl
		 WHERE $f_test_id = $f_vers_test_id
		 AND $f_project_id = '$project_id'
		 AND $f_deleted = 'N'
		 AND $f_archived = 'N'";
	*/

    $order_clause 	= " ORDER BY $order_by $order_dir";
	$where_clause 	= $where_clause." GROUP BY $f_test_name";
    $q 				.= $where_clause.$order_clause;

	if( $per_page!=0 && $page_number!=0 ) {

		$row_count = db_num_rows( $db, db_query($db, $q) );

		$page_number = util_page_number($page_number, $row_count, $per_page);

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * $per_page );
		html_table_offset( 	$row_count,
							$per_page,
							$page_number,
							$order_by,
							$order_dir,
							$csv_name );

		$q .= " LIMIT $offset, ".$per_page;

	}

	$rs = db_query($db, $q);
	$arr = db_fetch_array($db, $rs);

	return $arr;
}

function test_get_all_ids($project_id) {

    global $db;
	$test_tbl			= TEST_TBL;
	$f_test_id			= $test_tbl .".". TEST_ID;
	$f_test_name        = $test_tbl .".". TEST_NAME;
	$f_project_id		= $test_tbl .".". TEST_PROJ_ID;
    $f_deleted			= $test_tbl .".". TEST_DELETED;
    $f_archived			= $test_tbl .".". TEST_ARCHIVED;

	$q = "SELECT $f_test_id
			 FROM $test_tbl
			 WHERE $f_project_id = '$project_id'
			 AND $f_deleted = 'N'
			 AND $f_archived = 'N'";

	$rs = db_query($db, $q);
	return db_fetch_array($db, $rs);
}


# ----------------------------------------------------------------------
# Create where clause for tests and run query to extract test data
# OUTPUT:
#   array of test records.
# ----------------------------------------------------------------------
function &test_filter_rows( $project_id, 
							$manauto, 
							$baowner, 
							$qaowner, 
							$tester, 
							$testtype, 
							$test_area, 
							$test_status, 
							$priority,
							$per_page, 
							$test_search, 
							$orderby, 
							$order_dir, 
							$page_number, 
							$csv_name=null) {

    $where_clause = test_filter_generate_where_clause(	$manauto, $baowner, $qaowner, $tester, 
														$testtype, $test_area, $test_status, 
														$priority, $test_search);

    $row = test_apply_filter($project_id, $where_clause, $per_page, $orderby, $order_dir, $page_number, $csv_name);

    return $row;
}

# ----------------------------------------------------------------------
# Create where clause for tests and run query to extract test data
# OUTPUT:
#   array of test records.
# ----------------------------------------------------------------------
function test_copy_filter_rows(	$project_id,
									$release_id,
									$build_id,
									$testset_id,
									$filter_man_auto,
									$filter_ba_owner,
									$filter_qa_owner,
									$filter_tester,
									$filter_test_type,
									$filter_area_tested,
									$filter_priority,
									$per_page,
									$test_search,
									$order_by,
									$order_dir,
									$page_number) {

    $where_clause = test_filter_generate_where_clause(	$filter_man_auto,
														$filter_ba_owner,
														$filter_qa_owner,
														$filter_tester,
														$filter_test_type,
														$filter_area_tested,
														$test_status="",
														$filter_priority,
														$test_search);

    $row = test_copy_apply_filter(	$project_id,
									$release_id,
									$build_id,
									$testset_id,
									$per_page,
									$order_by,
									$order_dir,
									$page_number,
									$where_clause );

    return $row;
}

# ----------------------------------------------------------------------
# Create where clause for test query
# OUTPUT:
#   Where clause string
# ----------------------------------------------------------------------
function test_filter_generate_where_clause($manauto, $baowner, $qaowner,$tester, $testtype, $test_area, $test_status, $priority, $test_search) {

    $f_test_name          = TEST_TBL. "." .TEST_NAME;
    $f_manual_tests       = TEST_TBL. "." .TEST_MANUAL;
    $f_automated_tests    = TEST_TBL. "." .TEST_AUTOMATED;
    $f_ba_owner           = TEST_TBL. "." .TEST_BA_OWNER;
    $f_qa_owner           = TEST_TBL. "." .TEST_QA_OWNER;
    $f_tester			  =	TEST_TBL. "." .TEST_TESTER;
    $f_test_type          = TEST_TBL. "." .TEST_TESTTYPE;
    $f_area_tested        = TEST_TBL. "." .TEST_AREA_TESTED;
    $f_test_status        = TEST_TBL. "." .TEST_STATUS;
	$f_priority			  = TEST_TBL. "." .TEST_PRIORITY;


    $where_clause = '';

    # MANUAL AUTOMATED
    if ( !empty($manauto) && $manauto != 'all') {
        if( $manauto == 'Manual' ) {
            $where_clause = $where_clause. " AND $f_manual_tests = 'YES' AND $f_automated_tests = ''";
        }
        elseif( $manauto == 'Automated' ) {
            $where_clause = $where_clause. " AND $f_manual_tests = '' AND $f_automated_tests = 'YES'";
        }
        else {
            $where_clause = $where_clause. " AND $f_manual_tests = 'YES' AND $f_automated_tests = 'YES'";
        }
    }
    # BA OWNER
    if ( !empty($baowner)  && $baowner != 'all') {

        $where_clause = $where_clause." AND $f_ba_owner = '$baowner'";
    }
    # QA OWNER
    if ( !empty($qaowner) && $qaowner != 'all') {

        $where_clause = $where_clause." AND $f_qa_owner = '$qaowner'";
    }
    # TESTER
	if ( !empty($tester) && $tester != 'all') {

	    $where_clause = $where_clause." AND $f_tester = '$tester'";
    }
    # TEST TYPE
    if ( !empty($testtype) && $testtype != 'all') {

        $where_clause = $where_clause." AND $f_test_type = '$testtype'";
    }
    # AREA TESTED
    if ( !empty($test_area) && $test_area != 'all') {

        $where_clause = $where_clause." AND $f_area_tested = '$test_area'";
    }
    # TEST STATUS
    if ( !empty($test_status) && $test_status != 'all') {

        $where_clause = $where_clause." AND $f_test_status = '$test_status'";
    }
	# PRIORITY
    if ( !empty($priority) && $priority != 'all') {

        $where_clause = $where_clause." AND $f_priority = '$priority'";
    }

	# SEARCH
	if ( !empty($test_search) ) {

		$where_clause = $where_clause." AND  ($f_test_name  LIKE '%$test_search%')";
    }

    return $where_clause;
}



# ----------------------------------------------------------------------
# Create where clause for test workflow and run query to extract test
# workflow data
# OUTPUT:
#   array of test workflow records.
# ----------------------------------------------------------------------
function test_workflow_filter_rows($project_id, $manauto, $test_type, $baowner, 
									$qaowner,$tester, $area_tested, $test_status, 
									$priority, $per_page, $test_search, $orderby, 
									$order_dir, $page_number, $csv_name=null) {

	$where_clause = test_workflow_filter_generate_where_clause($manauto, $test_type, $baowner, $qaowner, $tester, $area_tested, $test_status, 																	$priority, $test_search);

    $row = test_apply_filter($project_id, $where_clause, $per_page, $orderby, $order_dir, $page_number, $csv_name);
    return $row;

}


# ----------------------------------------------------------------------
# Create where clause for test workflow query
# OUTPUT:
#   Where clause string
# ----------------------------------------------------------------------
function test_workflow_filter_generate_where_clause($manauto, $test_type, $baowner, $qaowner,
													$tester, $test_area, $test_status, $priority,
													$test_search) {

    $f_test_name			= TEST_TBL. "." .TEST_NAME;
    $f_manual_tests			= TEST_TBL. "." .TEST_MANUAL;
    $f_automated_tests		= TEST_TBL. "." .TEST_AUTOMATED;
    $f_ba_owner				= TEST_TBL. "." .TEST_BA_OWNER;
    $f_qa_owner				= TEST_TBL. "." .TEST_QA_OWNER;
    $f_tester				= TEST_TBL. "." .TEST_TESTER;
    $f_test_status			= TEST_TBL. "." .TEST_STATUS;
	$f_test_type			= TEST_TBL. "." .TEST_TESTTYPE;
	$f_area_tested        	= TEST_TBL. "." .TEST_AREA_TESTED;
	$f_priority				= TEST_TBL. "." .TEST_PRIORITY;

    $where_clause = '';

    # MANUAL AUTOMATED
    if ( !empty($manauto) && $manauto != 'all') {
        if( $manauto == 'Manual' ) {
            $where_clause = $where_clause. " AND $f_manual_tests = 'YES' AND $f_automated_tests = ''";
        }
        elseif( $manauto == 'Automated' ) {
            $where_clause = $where_clause. " AND $f_manual_tests = '' AND $f_automated_tests = 'YES'";
        }
        else {
            $where_clause = $where_clause. " AND $f_manual_tests = 'YES' AND $f_automated_tests = 'YES'";
        }
    }
    # TEST TYPE
    if ( !empty($test_type) && $test_type != 'all') {

        $where_clause = $where_clause." AND $f_test_type = '$test_type'";
    }
    # BA OWNER
    if ( !empty($baowner)  && $baowner != 'all') {

        $where_clause = $where_clause." AND $f_ba_owner = '$baowner'";
    }
    # QA OWNER
    if ( !empty($qaowner) && $qaowner != 'all') {

        $where_clause = $where_clause." AND $f_qa_owner = '$qaowner'";
    }
    # TESTER
    if ( !empty($tester) && $tester != 'all') {

        $where_clause = $where_clause." AND $f_tester = '$tester'";
    }
    # AREA TESTED
    if ( !empty($test_area) && $test_area != 'all') {

        $where_clause = $where_clause." AND $f_area_tested = '$test_area'";
    }
    # TEST STATUS
    if ( !empty( $test_status ) && $test_status != 'all') {

        $where_clause = $where_clause." AND $f_test_status = '$test_status'";
    }
	# TEST PRIORITY
    if ( !empty( $priority ) && $priority != 'all') {

        $where_clause = $where_clause." AND $f_priority = '$priority'";
    }
	# SEARCH
	if ( !empty($test_search) ) {

		$where_clause = $where_clause." AND  ($f_test_name  LIKE '%$test_search%')";
    }

    return $where_clause;
}


# ----------------------------------------------------------------------
# Get filename corresponding to a test
#
# INPUT:
#   Test id
# OUTPUT:
#   Corresponding filename
# ----------------------------------------------------------------------
function test_get_filename($test_id) {

    global $db;

	$td_tbl					= MAN_TD_TBL;
	$f_td_id				= $td_tbl .".". MAN_TD_MANUAL_TEST_ID;
	$f_test_id				= $td_tbl .".". MAN_TD_TEST_ID;
	$f_display_name			= $td_tbl .".". MAN_TD_DISPLAY_NAME;
	$display_name			= MAN_TD_DISPLAY_NAME;

	$td_vers_tbl			= MAN_TD_VER_TBL;
	$f_td_vers_id			= MAN_TD_VER_TBL .".". MAN_TD_VER_MANUAL_TEST_ID;
	$f_timestamp			= MAN_TD_VER_TBL .".". MAN_TD_VER_TIME_STAMP;


    $q = "SELECT 
				MAX($f_timestamp), $f_display_name
		  FROM $td_tbl 
		  INNER JOIN $td_vers_tbl ON $f_td_vers_id = $f_td_id
		  WHERE $f_test_id = '$test_id' 
		  GROUP BY $f_timestamp ASC";
    $rs = $db->Execute( $q );
    $row = db_fetch_row( $db, $rs ) ;

    return ($row[$display_name]);

}


# ----------------------------------------------------------------------
# Print Test submenu
# INPUT:
#   Current Page (so that it will not be shown as a hyperlink)
# ----------------------------------------------------------------------
function test_menu_print($page) {

	$screen_link = lang_get('screen_link');
	$field_link = lang_get('field_link');
	$add_field_link = lang_get('add_field');
	$function_link = lang_get('function_link');
    $test_link = lang_get('test_link');
    $test_add_link = lang_get('test_add_link');
    $test_workflow_link = lang_get('test_workflow_link');

	/*
    $menu_items = array(
			$function_link => 'function_page.php',
            $test_link => 'test_page.php',
            $test_add_link => 'test_add_page.php',
            $test_workflow_link => 'test_workflow_page.php',
			$screen_link => 'screen_page.php',
			$field_link => 'field_page.php',
			$add_field_link => 'field_add_page.php',
            );
	*/

	$menu_items = array(
			$test_link => 'test_page.php',
            $test_add_link => 'test_add_page.php',
            $test_workflow_link => 'test_workflow_page.php',
            );

    html_print_sub_menu( $page, $menu_items );
}

# ----------------------------------------------------------------------
# Print Test Menu that displays Test Steps, File Upload, and Requirement Associaton
# INPUT:
#   TestID
# ----------------------------------------------------------------------
function test_sub_menu_print( $test_id, $project_id, $page, $tab ) {

	#added parameter project_id
	$url = $page ."?test_id=". $test_id . "&project_id=". $project_id;
	$page_url = $page ."?test_id=". $test_id . "&project_id=". $project_id;

	$style_enabled = 'page-numbers';
	$style_disabled = 'page-numbers-disabled';

	print"<br>";
	print"<div class='center'>". NEWLINE;
	print "<form name='test_detail_tab' method=post action='$url'>". NEWLINE;
	print"<table class='width60'>". NEWLINE;
	print"<tr>". NEWLINE;

	print"<td class='menu' width='33%'>". NEWLINE;
	if( $tab == '1' ) {
		print lang_get('test_steps');
	}
	else {
		$url = $page_url . "&tab=1";
		print"<a href='$url'>". lang_get('test_steps') ."</a>";
	}
	print"</td>";

	print"<td class='menu' width='33%'>". NEWLINE;
	if( $tab == '3' ) {
		print lang_get('req_assoc_link');
	}
	else {
		$url = $page_url . "&tab=3";
		print"<a href='$url'>". lang_get('req_assoc_link') ."</a>";
	}
	print"</td>";

	print"<td class='menu' width='33%'>". NEWLINE;
	if( $tab == '2' ) {
		if(test_get_count_uploaded_documents($test_id) > 0)
		{
			print"<b class='special'>". lang_get( 'test_docs' ) ."</b>";
		}
		else
		{
			print lang_get( 'test_docs' );
		}
	}
	else {
		$url = $page_url . "&tab=2";
		if(test_get_count_uploaded_documents($test_id) > 0)
		{
			print"<a href='$url' class='speciallink'>". lang_get('test_docs') ."</a>";
		}
		else
		{
			print"<a href='$url'>". lang_get('test_docs') ."</a>";
		}
	}
	print"</td>". NEWLINE;

	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;
	print"</div>". NEWLINE;

}


# ----------------------------------------------------------------------
# Get list of test priorities
# OUTPUT:
#   Array of test available priorities
# ----------------------------------------------------------------------
function test_get_priorities() {
    $low = lang_get('priority_low');
    $medium = lang_get('priority_medium');
    $high = lang_get('priority_high');
    $priorities_arr = array($low, $medium, $high, '');
    return $priorities_arr;
}


# ----------------------------------------------------------------------
# Add Test record to database
# ----------------------------------------------------------------------
function test_add_test($project_id, $testname, $testpurpose, $testcomments, $testpriority, $teststatus, $testareatested, $testtype,  $ba_owner, $qa_owner, $assigned_to, $assigned_by, $dateassigned, $dateexpcomplete, $dateactcomplete, $datebasignoff, $duration, $autopass, $steps, $auto, $performance) {

    global $db;

	$s_project_properties = session_get_project_properties();
	$s_test_versions = $s_project_properties['test_versions'];

	$test_tbl             = TEST_TBL;
	$f_test_id			  = $test_tbl .".". TEST_ID;
	$f_test_name          = TEST_NAME;
	$f_project_id		  = PROJECT_ID;
	$f_test_type          = TEST_TESTTYPE;
    $f_area_tested        = TEST_AREA_TESTED;
	$f_autopass           = TEST_AUTO_PASS;
	$f_test_deleted       = TEST_DELETED;
    $f_test_archived      = TEST_ARCHIVED;
	$f_test_purpose       = TEST_PURPOSE;
	$f_test_comments      = TEST_COMMENTS;
	$f_manual_tests       = TEST_MANUAL;
	$f_manual_test        = TEST_MANUAL;
	$f_performance        = TEST_LR;
	$f_automated_test     = TEST_AUTOMATED;
	$f_ba_owner           = TEST_BA_OWNER;
	$f_qa_owner           = TEST_QA_OWNER;
	$f_test_priority      = TEST_PRIORITY;
	$f_test_status        = TEST_STATUS;
	$f_assigned_to        = TEST_ASSIGNED_TO;
	$f_assigned_by        = TEST_ASSIGNED_BY;
	$f_dateassigned       = TEST_DATE_ASSIGNED;
	$f_dateexpcomplete    = TEST_DATE_EXPECTED;
	$f_dateactcomplete    = TEST_DATE_COMPLETE;
	$f_datebasignoff      = TEST_BA_SIGNOFF;
	$f_duration			  = TEST_DURATION;

	$q  = "INSERT INTO $test_tbl
		  ($f_test_purpose, $f_test_name, $f_project_id, $f_test_comments, $f_area_tested, $f_test_type,
		   $f_test_priority, $f_test_status, $f_ba_owner, $f_qa_owner, $f_assigned_to,
		   $f_assigned_by, $f_dateassigned, $f_dateexpcomplete, $f_dateactcomplete,
		   $f_datebasignoff, $f_duration, $f_manual_test, $f_automated_test, $f_performance,
		   $f_autopass, $f_test_deleted, $f_test_archived)
		  VALUES
		  ('$testpurpose', '$testname', '$project_id', '$testcomments', '$testareatested',
		   '$testtype', '$testpriority', '$teststatus', '$ba_owner', '$qa_owner',
		   '$assigned_to', '$assigned_by', '$dateassigned', '$dateexpcomplete',
		   '$dateactcomplete', '$datebasignoff', '$duration', '$steps', '$auto', '$performance',
		   '$autopass', 'N', 'N')";

	db_query( $db, $query );


}


# ----------------------------------------------------------------------
# Add Test record to database
# ----------------------------------------------------------------------
function test_add_test_version($project_id, $test_name, $purpose, $comments, $priority, $status, $area_tested, $test_type,  $ba_owner, $qa_owner, $tester, $assigned_to, $assigned_by, $date_assigned, $date_expected, $date_completed, $date_signoff, $duration, $autopass, $manual, $automated, $performance, $assoc_req, $email_ba_owner, $email_qa_owner) {

    global $db;

	$test_req_assoc_tbl	 		= TEST_REQ_ASSOC_TBL;
	$f_test_req_assoc_id		= TEST_REQ_ASSOC_ID;
	$f_test_req_assoc_test_id	= TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_req_id	= TEST_REQ_ASSOC_REQ_ID;

	$test_tbl             = TEST_TBL;
	$f_test_id			  = TEST_ID;
	$f_test_name          = TEST_NAME;
	$f_project_id		  = PROJECT_ID;
	$f_purpose			  = TEST_PURPOSE;
	$f_priority			  = TEST_PRIORITY;
	$f_status			  = TEST_STATUS;
	$f_test_type          = TEST_TESTTYPE;
    $f_area_tested        = TEST_AREA_TESTED;
	$f_autopass           = TEST_AUTO_PASS;
	$f_test_deleted       = TEST_DELETED;
    $f_test_archived      = TEST_ARCHIVED;
	$f_ba_owner           = TEST_BA_OWNER;
	$f_qa_owner           = TEST_QA_OWNER;
	$f_tester			  = TEST_TESTER;
	$f_manual_test        = TEST_MANUAL;
	$f_automated_tests    = TEST_AUTOMATED;
	$f_performance        = TEST_LR;
	$f_assigned_to		  = TEST_ASSIGNED_TO;
	$f_assigned_by		  = TEST_ASSIGNED_BY;
	$f_date_assigned	  = TEST_DATE_ASSIGNED;
	$f_date_expected	  = TEST_DATE_EXPECTED;
	$f_date_completed	  = TEST_DATE_COMPLETE;
	$f_duration			  = TEST_DURATION;
	$f_last_updated		  = TEST_LAST_UPDATED;
	$f_last_updated_by	  = TEST_LAST_UPDATED_BY;
	$f_email_ba			  = TEST_EMAIL_BA_OWNER;
	$f_email_qa			  = TEST_EMAIL_QA_OWNER;

	$last_updated		  = date_get_short_dt();
	$s_user_properties 	  = session_get_user_properties();
	$s_user_name		  = $s_user_properties['username'];
	$last_updated_by	  = $s_user_name;

	$test_version_tbl	  = TEST_VERS_TBL;
	$f_vers_test_id		  = TEST_VERS_TEST_ID;
	$f_version_no		  = TEST_VERS_NUMBER;
	$f_latest_vers		  = TEST_VERS_LATEST;
	$f_active_version	  = TEST_VERS_ACTIVE;
	$f_test_comments      = TEST_VERS_COMMENTS;
	$f_test_status        = TEST_VERS_STATUS;
	$f_version_author	  = TEST_VERS_AUTHOR;
	$f_date_created		  = TEST_VERS_DATE_CREATED;

	$q = "INSERT INTO $test_tbl
		  ( $f_test_name, $f_project_id, $f_test_comments, $f_purpose, $f_area_tested, $f_test_type,
		   $f_autopass, $f_test_deleted, $f_test_archived, $f_ba_owner, $f_qa_owner, $f_tester,
		   $f_manual_test, $f_automated_tests, $f_performance, $f_priority, $f_status,
		   $f_assigned_to, $f_assigned_by, $f_date_assigned, $f_date_expected,
		   $f_date_completed, $f_duration, $f_last_updated, $f_last_updated_by, $f_email_ba, $f_email_qa )
		  VALUES
		  ('$test_name', '$project_id', '$comments', '$purpose', '$area_tested', '$test_type',
		   '$autopass', 'N', 'N', '$ba_owner', '$qa_owner', '$tester', '$manual', '$automated',
		   '$performance', '$priority', '$status', '$assigned_to', '$assigned_by', '$date_assigned',
		   '$date_expected', '$date_completed', '$duration', '$last_updated', '$last_updated_by',
		   '$email_ba_owner', '$email_qa_owner')";

	print"<br>";
	db_query( $db, $q );


	$q_id = "SELECT $f_test_id FROM $test_tbl WHERE $f_test_name = '$test_name'";
	$test_id = db_get_one( $db, $q_id );

	$created_date = date_get_short_dt();
	$author = session_get_username();

	$q_version = "INSERT INTO $test_version_tbl
			($f_vers_test_id, $f_version_no, $f_latest_vers, $f_test_comments, $f_test_status,
			 $f_version_author, $f_date_created,  $f_active_version )
			VALUES
			('$test_id', '1.0', 'Y', '$comments', '$status',
			'$author', '$created_date', 'Y' )";

	#print"$q_version<br>";
	db_query( $db, $q_version );

	# associate with requirement
	if( $assoc_req ) {

		$q = "	INSERT INTO	$test_req_assoc_tbl
					($f_test_req_assoc_test_id, $f_test_req_assoc_req_id)
				VALUES
					($test_id, $assoc_req)";

		db_query($db, $q);
	}

}

# ----------------------------------------------------------------------
# Add Test record to database and return the created test id
# ----------------------------------------------------------------------
function test_add_test_version_return_id($project_id, $test_name, $purpose, $comments, $priority, $status, $area_tested, $test_type,  $ba_owner, $qa_owner, $tester, $assigned_to, $assigned_by, $date_assigned, $date_expected, $date_completed, $date_signoff, $duration, $autopass, $manual, $automated, $performance, $assoc_req, $email_ba_owner, $email_qa_owner) {

    global $db;

	$test_req_assoc_tbl	 		= TEST_REQ_ASSOC_TBL;
	$f_test_req_assoc_id		= TEST_REQ_ASSOC_ID;
	$f_test_req_assoc_test_id	= TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_req_id	= TEST_REQ_ASSOC_REQ_ID;

	$test_tbl             = TEST_TBL;
	$f_test_id			  = TEST_ID;
	$f_test_name          = TEST_NAME;
	$f_project_id		  = PROJECT_ID;
	$f_purpose			  = TEST_PURPOSE;
	$f_priority			  = TEST_PRIORITY;
	$f_status			  = TEST_STATUS;
	$f_test_type          = TEST_TESTTYPE;
    $f_area_tested        = TEST_AREA_TESTED;
	$f_autopass           = TEST_AUTO_PASS;
	$f_test_deleted       = TEST_DELETED;
    $f_test_archived      = TEST_ARCHIVED;
	$f_ba_owner           = TEST_BA_OWNER;
	$f_qa_owner           = TEST_QA_OWNER;
	$f_tester			  = TEST_TESTER;
	$f_manual_test        = TEST_MANUAL;
	$f_automated_tests    = TEST_AUTOMATED;
	$f_performance        = TEST_LR;
	$f_assigned_to		  = TEST_ASSIGNED_TO;
	$f_assigned_by		  = TEST_ASSIGNED_BY;
	$f_date_assigned	  = TEST_DATE_ASSIGNED;
	$f_date_expected	  = TEST_DATE_EXPECTED;
	$f_date_completed	  = TEST_DATE_COMPLETE;
	$f_duration			  = TEST_DURATION;
	$f_last_updated		  = TEST_LAST_UPDATED;
	$f_last_updated_by	  = TEST_LAST_UPDATED_BY;
	$f_email_ba			  = TEST_EMAIL_BA_OWNER;
	$f_email_qa			  = TEST_EMAIL_QA_OWNER;

	$last_updated		  = date_get_short_dt();
	$s_user_properties 	  = session_get_user_properties();
	$s_user_name		  = $s_user_properties['username'];
	$last_updated_by	  = $s_user_name;

	$test_version_tbl	  = TEST_VERS_TBL;
	$f_vers_test_id		  = TEST_VERS_TEST_ID;
	$f_version_no		  = TEST_VERS_NUMBER;
	$f_latest_vers		  = TEST_VERS_LATEST;
	$f_active_version	  = TEST_VERS_ACTIVE;
	$f_test_comments      = TEST_VERS_COMMENTS;
	$f_test_status        = TEST_VERS_STATUS;
	$f_version_author	  = TEST_VERS_AUTHOR;
	$f_date_created		  = TEST_VERS_DATE_CREATED;

	$q = "INSERT INTO $test_tbl
		  ( $f_test_name, $f_project_id, $f_test_comments, $f_purpose, $f_area_tested, $f_test_type,
		   $f_autopass, $f_test_deleted, $f_test_archived, $f_ba_owner, $f_qa_owner, $f_tester,
		   $f_manual_test, $f_automated_tests, $f_performance, $f_priority, $f_status,
		   $f_assigned_to, $f_assigned_by, $f_date_assigned, $f_date_expected,
		   $f_date_completed, $f_duration, $f_last_updated, $f_last_updated_by, $f_email_ba, $f_email_qa )
		  VALUES
		  ('$test_name', '$project_id', '$comments', '$purpose', '$area_tested', '$test_type',
		   '$autopass', 'N', 'N', '$ba_owner', '$qa_owner', '$tester', '$manual', '$automated',
		   '$performance', '$priority', '$status', '$assigned_to', '$assigned_by', '$date_assigned',
		   '$date_expected', '$date_completed', '$duration', '$last_updated', '$last_updated_by',
		   '$email_ba_owner', '$email_qa_owner')";

	print"<br>";
	db_query( $db, $q );


	$q_id = "SELECT MAX($f_test_id) FROM $test_tbl WHERE $f_test_name = '$test_name'";
	$test_id = db_get_one( $db, $q_id );

	$created_date = date_get_short_dt();
	$author = session_get_username();

	$q_version = "INSERT INTO $test_version_tbl
			($f_vers_test_id, $f_version_no, $f_latest_vers, $f_test_comments, $f_test_status,
			 $f_version_author, $f_date_created,  $f_active_version )
			VALUES
			('$test_id', '1.0', 'Y', '$comments', '$status',
			'$author', '$created_date', 'Y' )";

	#print"$q_version<br>";
	db_query( $db, $q_version );

	# associate with requirement
	if( $assoc_req ) {

		$q = "	INSERT INTO	$test_req_assoc_tbl
					($f_test_req_assoc_test_id, $f_test_req_assoc_req_id)
				VALUES
					($test_id, $assoc_req)";

		db_query($db, $q);
	}
	return $test_id;
}


# ----------------------------------------------------------------------
# Update Test record
# ----------------------------------------------------------------------

function test_update_test($test_id, $test_version_id, $test_name, $test_purpose, $testcomments, $priority,
                          $test_status, $area_tested, $test_type,  $ba_owner, $qa_owner, $tester,
                          $assigned_to, $assigned_by, $date_assigned, $date_expected,
                          $date_completed, $duration, $autopass, $manual_test, $automated_test, $performance,
						  $email_ba_owner, $email_qa_owner) {

    global $db;

	$test_tbl             = TEST_TBL;
	$f_test_id			  = TEST_ID;
	$f_test_name          = TEST_NAME;
	$f_project_id		  = PROJECT_ID;
	$f_test_purpose		  = TEST_PURPOSE;
	$f_priority			  = TEST_PRIORITY;
	$f_comments   		  = TEST_COMMENTS;
	$f_test_type          = TEST_TESTTYPE;
    $f_area_tested        = TEST_AREA_TESTED;
	$f_autopass           = TEST_AUTO_PASS;
	$f_test_deleted       = TEST_DELETED;
    $f_test_archived      = TEST_ARCHIVED;
	$f_ba_owner           = TEST_BA_OWNER;
	$f_qa_owner           = TEST_QA_OWNER;

	$f_tester			  = TEST_TESTER;
	$f_manual_test        = TEST_MANUAL;
	$f_automated_test     = TEST_AUTOMATED;
	$f_performance        = TEST_LR;
	$f_assigned_to		  = TEST_ASSIGNED_TO;
	$f_assigned_by		  = TEST_ASSIGNED_BY;
	$f_date_assigned	  = TEST_DATE_ASSIGNED;
	$f_date_expected	  = TEST_DATE_EXPECTED;
	$f_date_completed	  = TEST_DATE_COMPLETE;
	$f_duration			  = TEST_DURATION;
	$f_test_status		  = TEST_STATUS;
	$f_email_ba_owner	  = TEST_EMAIL_BA_OWNER;
	$f_email_qa_owner     = TEST_EMAIL_QA_OWNER;


	/*
	$f_signoff_by		  = TEST_SIGNOFF_BY;
	$f_signoff_date		  = TEST_SIGNOFF_DATE;
	$test_version_tbl	  = TEST_VERS_TBL;
	$f_vers_id			  = TEST_VERS_ID;
	$f_vers_test_id		  = TEST_VERS_TEST_ID;
	$f_version_no		  = TEST_VERS_NUMBER;
	$f_latest_vers		  = TEST_VERS_LATEST;
	$f_test_status        = TEST_VERS_STATUS;
	$f_signoff_date       = TEST_VERS_SIGNOFF_DATE;
	$f_signoff_by		  = TEST_VERS_SIGNOFF_BY;
	*/

    $q = "UPDATE $test_tbl SET
              $f_test_purpose = '$test_purpose',
              $f_comments = '$testcomments',
              $f_test_name = '$test_name',
              $f_area_tested = '$area_tested',
              $f_test_type = '$test_type',
              $f_priority = '$priority',
              $f_ba_owner = '$ba_owner',
              $f_qa_owner = '$qa_owner',
              $f_tester   = '$tester',
              $f_assigned_to = '$assigned_to',
              $f_assigned_by = '$assigned_by',
              $f_date_assigned = '$date_assigned',
              $f_date_expected = '$date_expected',
              $f_date_completed = '$date_completed',
			  $f_duration = '$duration',
              $f_manual_test = '$manual_test',
              $f_automated_test = '$automated_test',
              $f_performance = '$performance',
              $f_autopass = '$autopass',
			  $f_test_status = '$test_status',
			  $f_email_ba_owner = '$email_ba_owner',
			  $f_email_qa_owner = '$email_qa_owner'
              WHERE
              $f_test_id = '$test_id'";

	db_query( $db, $q );

	# update last updated by and date
	test_change_last_update( $test_id );


	//print"$q<br>";
	/*
	# UPDATE VERSION
	$q_ver = "UPDATE $test_version_tbl SET
			 $f_test_status = '$test_status',
			 $f_signoff_date = '$signoff_date',
			 $f_signoff_by = '$signoff_by'
			 WHERE
			 $f_vers_id = '$test_version_id'";


	db_query( $db, $q_ver );
	*/

}


function test_update_field( $project_id, $test_id, $field_name, $value ) {

	global $db;
	$test_tbl		= TEST_TBL;
	$f_project_id	= TEST_PROJ_ID;
	$f_test_id		= TEST_ID;

	$q = "UPDATE $test_tbl
		 SET $field_name = '$value'
		 WHERE $f_project_id = '$project_id'
		 AND $f_test_id IN ( $test_id )";

	/*  I thought of trying to make this query less expensive by removing the
	/*  in clause when it's not needed.  It works the way it is but
	/*  we may want to change it if it becomes too expensive
	if( $in_clause ) {
		$q .= " AND $f_test_id IN ( $test_id )";
	}
	else {
		$q .= " AND $f_test_id = '$test_id'";
	}
	*/

	db_query( $db, $q );

}

function test_update_field_man_auto( $project_id, $test_id, $manual, $auto ) {

	global $db;
	$test_tbl		= TEST_TBL;
	$f_project_id	= TEST_PROJ_ID;
	$f_test_id		= TEST_ID;
	$f_manual		= TEST_MANUAL;
	$f_auto			= TEST_AUTOMATED;

	$q = "UPDATE $test_tbl
		 SET
		 	$f_manual = '$manual',
			$f_auto = '$auto'
		 WHERE $f_project_id = '$project_id'
		 	AND $f_test_id IN ( $test_id )";

	db_query( $db, $q );
}

# ----------------------------------------------------------------------
# Set the deleted flag for a test
# ----------------------------------------------------------------------
function test_delete($test_id){

	global $db;
    $test_tbl             = TEST_TBL;
    $f_test_id            = TEST_ID;
    $f_test_deleted       = TEST_DELETED;

    $query = "UPDATE $test_tbl
              SET $f_test_deleted = 'Y'
              WHERE $f_test_id  = '$test_id'";
    db_query( $db, $query );
}


# ----------------------------------------------------------------------
# Check if Test name already exists
# INPUT:
#   Test Name to Check
# OUTPUT:
#   True if Test with Test Name already exists, otherwise false.
# ----------------------------------------------------------------------
function test_name_exists( $project_id, $test_name ) {

    global $db;
    $test_tbl			= TEST_TBL;
    $f_test_id			= TEST_ID;
    $f_test_name		= TEST_NAME;
    $f_test_project_id	= TEST_PROJ_ID;

    $query = "	SELECT COUNT($f_test_name)
				FROM $test_tbl
				WHERE $f_test_name='$test_name'
					AND $f_test_project_id=$project_id";

    $result = db_get_one( $db, $query );

    if ( 0 == $result) {
        return false;
    } else {
        return true;
    }
}

function test_name_exists_with_id( $project_id, $test_name, $test_id ) {

    global $db;
    $test_tbl			= TEST_TBL;
    $f_test_id			= TEST_ID;
    $f_test_name		= TEST_NAME;
    $f_test_project_id	= TEST_PROJ_ID;

    $query = "	SELECT COUNT($f_test_name)
				FROM $test_tbl
				WHERE $f_test_name='$test_name'
					AND $f_test_project_id=$project_id
					AND $f_test_id<>$test_id";

    $result = db_get_one( $db, $query );

    if ( 0 == $result) {
        return false;
    } else {
        return true;
    }
}
# ----------------------------------------------------------------------
# Check if test exists in project
# INPUT:
#   test id, project id
# OUTPUT:
#   true OR false
# ----------------------------------------------------------------------
function test_id_exists($project_id, $test_id)
{
	global $db;
    $test_tbl			= TEST_TBL;
    $f_test_id			= TEST_ID;
    $f_test_project_id	= TEST_PROJ_ID;
    
    $query = " SELECT COUNT($f_test_id)
    		   FROM $test_tbl
    		   WHERE $f_test_id = $test_id
    		   AND $f_test_project_id = $project_id";
    		   
    $result = db_get_one( $db, $query);
    
    if ( $result == 0){
    	return false;
    }else{
    	return true;
    }
}


# ----------------------------------------------------------------------
# Get the details for a test
# INPUT:
#   test id
# OUTPUT:
#   test details
# ----------------------------------------------------------------------
function test_get_detail( $test_id ) {

    global $db;
    $test_tbl             = TEST_TBL;
    $f_test_id            = $test_tbl .".". TEST_ID;
    $f_test_name          = $test_tbl .".". TEST_NAME;
    $f_purpose			  = $test_tbl .".". TEST_PURPOSE;
	$f_comments			  = $test_tbl .".". TEST_COMMENTS;
    $f_ba_owner           = $test_tbl .".". TEST_BA_OWNER;
    $f_qa_owner           = $test_tbl .".". TEST_QA_OWNER;
    $f_tester			  = $test_tbl .".". TEST_TESTER;
    $f_test_type          = $test_tbl .".". TEST_TESTTYPE;
    $f_area_tested        = $test_tbl .".". TEST_AREA_TESTED;
	$f_status			  = $test_tbl .".". TEST_STATUS;
    $f_test_priority      = $test_tbl .".". TEST_PRIORITY;
	$f_signoff_by		  = $test_tbl .".". TEST_SIGNOFF_BY;
	$f_signoff_date		  = $test_tbl .".". TEST_SIGNOFF_DATE;
	//$f_creator			  = $test_tbl .".". TEST_AUTHOR;
	$f_date_created		  = $test_tbl .".". TEST_DATE_CREATED;
    $f_steps              = $test_tbl .".". TEST_MANUAL;
    $f_script             = $test_tbl .".". TEST_AUTOMATED;
    $f_autopass           = $test_tbl .".". TEST_AUTO_PASS;
    $f_performance        = $test_tbl .".". TEST_LR;
    $f_assigned_to        = $test_tbl .".". TEST_ASSIGNED_TO;
    $f_assigned_by        = $test_tbl .".". TEST_ASSIGNED_BY;
    $f_date_assigned      = $test_tbl .".". TEST_DATE_ASSIGNED;
    $f_date_expected	  = $test_tbl .".". TEST_DATE_EXPECTED;
    $f_date_completed     = $test_tbl .".". TEST_DATE_COMPLETE;
	$f_duration		      = $test_tbl .".". TEST_DURATION;
	$f_last_updated		  = $test_tbl .".". TEST_LAST_UPDATED;
	$f_last_updated_by	  = $test_tbl .".". TEST_LAST_UPDATED_BY;
	$f_email_ba_owner	  = $test_tbl .".". TEST_EMAIL_BA_OWNER;
	$f_email_qa_owner     = $test_tbl .".". TEST_EMAIL_QA_OWNER;

    $q = "SELECT $f_test_id, $f_comments, $f_status, $f_test_name, $f_test_type,
				 $f_area_tested, $f_test_priority, $f_purpose, $f_qa_owner, $f_ba_owner, $f_tester,
				 $f_steps, $f_script, $f_performance, $f_autopass, $f_assigned_to ,
				 $f_assigned_by, $f_date_assigned, $f_date_expected,  $f_date_completed,
				 $f_duration, $f_signoff_by, $f_signoff_date, $f_date_created,
				 $f_last_updated, $f_last_updated_by, $f_email_ba_owner, $f_email_qa_owner
              FROM $test_tbl
              WHERE $f_test_id = '$test_id'";

	//print"$q<br>";

    $rs = & db_query( $db, $q);
    $row = db_fetch_row( $db, $rs ) ;

    return $row;

}
# ----------------------------------------------------------------------
# Get the details for a test
# INPUT:
#   test id
# OUTPUT:
#   test details
# ----------------------------------------------------------------------
/*
function test_get_detail( $test_id, $version_id=null ) {

    global $db;
    $test_tbl             = TEST_TBL;
    $f_test_id            = $test_tbl .".". TEST_ID;
    $f_test_name          = $test_tbl .".". TEST_NAME;
    $f_purpose			  = $test_tbl .".". TEST_PURPOSE;
    $f_ba_owner           = $test_tbl .".". TEST_BA_OWNER;
    $f_qa_owner           = $test_tbl .".". TEST_QA_OWNER;
    $f_test_type          = $test_tbl .".". TEST_TESTTYPE;
    $f_area_tested        = $test_tbl .".". TEST_AREA_TESTED;
    $f_test_priority      = $test_tbl .".". TEST_PRIORITY;
    $f_steps              = $test_tbl .".". TEST_MANUAL;
    $f_script             = $test_tbl .".". TEST_AUTOMATED;
    $f_autopass           = $test_tbl .".". TEST_AUTO_PASS;
    $f_performance        = $test_tbl .".". TEST_LR;
    $f_assigned_to        = $test_tbl .".". TEST_ASSIGNED_TO;
    $f_assigned_by        = $test_tbl .".". TEST_ASSIGNED_BY;
    $f_date_assigned      = $test_tbl .".". TEST_DATE_ASSIGNED;
    $f_date_expected	  = $test_tbl .".". TEST_DATE_EXPECTED;
    $f_date_completed     = $test_tbl .".". TEST_DATE_COMPLETE;
	$f_duration		      = $test_tbl .".". TEST_DURATION;

	$version_tbl		  = TEST_VERS_TBL;
	$f_version_id		  = $version_tbl .".". TEST_VERS_ID;
	$f_vers_test_id		  = $version_tbl .".". TEST_VERS_TEST_ID;
	$f_version_no		  = $version_tbl .".". TEST_VERS_NUMBER;
	$f_latest			  = $version_tbl .".". TEST_VERS_LATEST;
	$f_active			  = $version_tbl .".". TEST_VERS_ACTIVE;
	$f_comments			  = $version_tbl .".". TEST_VERS_COMMENTS;
	$f_status			  = $version_tbl .".". TEST_VERS_STATUS;
	$f_signoff_by		  = $version_tbl .".". TEST_VERS_SIGNOFF_BY;
	$f_signoff_date		  = $version_tbl .".". TEST_VERS_SIGNOFF_DATE;
	$f_creator			  = $version_tbl .".". TEST_VERS_AUTHOR;
	$f_date_created		  = $version_tbl .".". TEST_VERS_DATE_CREATED;



    $q = "SELECT $f_test_id, $f_comments, $f_status, $f_test_name, $f_test_type,
				 $f_area_tested, $f_test_priority, $f_purpose, $f_qa_owner, $f_ba_owner,
				 $f_steps, $f_script, $f_performance, $f_autopass, $f_assigned_to , $f_assigned_by, $f_date_assigned, $f_date_expected,  $f_date_completed, $f_duration, $f_version_id, $f_latest, $f_active, $f_signoff_by, $f_signoff_date, $f_creator, $f_date_created,
				 $f_version_no
              FROM $test_tbl, $version_tbl
              WHERE $f_test_id = $f_vers_test_id
			  AND $f_test_id = '$test_id'";

	if( isset( $version_id ) ) {
		$q .= " AND $f_version_id = '$version_id'";
	}
	else {
		$q .= " AND $f_latest ='Y'";
	}
	#print"$q<br>";

    $rs = & db_query( $db, $q);
    $row = db_fetch_row( $db, $rs ) ;

    return $row;

}
*/

# ----------------------------------------------------------------------
# Get the details for a test when using test version
# INPUT:
#   test id
# OUTPUT:
#   test details
# ----------------------------------------------------------------------
/*
function test_get_version_detail( $test_id ) {

	global $db;

	$test_tbl             = TEST_TBL;
	$f_test_id			  = $test_tbl .".". TEST_ID;
	$f_test_name          = $test_tbl .".". TEST_NAME;
	$f_project_id		  = $test_tbl .".". PROJECT_ID;
	$f_purpose			  = $test_tbl .".". TEST_PURPOSE;
	$f_test_type          = $test_tbl .".". TEST_TESTTYPE;
    $f_area_tested        = $test_tbl .".". TEST_AREA_TESTED;
	$f_autopass           = $test_tbl .".". TEST_AUTO_PASS;
	$f_test_deleted       = $test_tbl .".". TEST_DELETED;
    $f_test_archived      = $test_tbl .".". TEST_ARCHIVED;
	$f_ba_owner           = $test_tbl .".". TEST_BA_OWNER;
	$f_qa_owner           = $test_tbl .".". TEST_QA_OWNER;
	$f_manual_test        = $test_tbl .".". TEST_MANUAL;
	$f_automated_test     = $test_tbl .".". TEST_AUTOMATED;
	$f_performance        = $test_tbl .".". TEST_LR;

	$test_version_tbl	  = TEST_VERS_TBL;
	$f_version_id		  = $test_version_tbl .".". TEST_VERS_ID;
	$f_vers_test_id		  = $test_version_tbl .".". TEST_VERS_TEST_ID;
	$f_version_no		  = $test_version_tbl .".". TEST_VERS_NUMBER;
	$f_latest_vers		  = $test_version_tbl .".". TEST_VERS_LATEST;
	$f_comments			  = $test_version_tbl .".". TEST_VERS_COMMENTS;
	$f_status			  = $test_version_tbl .".". TEST_VERS_STATUS;
	$f_priority			  = $test_version_tbl .".". TEST_VERS_PRIORITY;
	$f_assigned_to        = $test_version_tbl .".". TEST_VERS_ASSIGNED_TO;
	$f_assigned_by        = $test_version_tbl .".". TEST_VERS_ASSIGNED_BY;
	$f_date_assigned      = $test_version_tbl .".". TEST_VERS_DATE_ASSIGNED;
	$f_date_expected      = $test_version_tbl .".". TEST_VERS_DATE_EXPECTED;
	$f_date_completed     = $test_version_tbl .".". TEST_VERS_DATE_COMPLETE;
	$f_date_signoff       = $test_version_tbl .".". TEST_VERS_BA_SIGNOFF;
	$f_version_author	  = $test_version_tbl .".". TEST_VERS_AUTHOR;
	$f_date_created		  = $test_version_tbl .".". TEST_VERS_DATE_CREATED;
	$f_duration			  = $test_version_tbl .".". TEST_VERS_DURATION;

	$q = "SELECT $f_test_id, $f_test_name, $f_project_id, $f_purpose, $f_test_type, $f_area_tested,
		  $f_autopass, $f_test_deleted, $f_test_archived, $f_ba_owner, $f_qa_owner, $f_version_id, $f_version_no, $f_latest_vers, $f_manual_test, $f_automated_test, $f_performance, $f_comments, $f_status, $f_priority, $f_assigned_to, $f_assigned_by, $f_date_assigned, $f_date_expected,
		  $f_date_completed, $f_date_signoff, $f_version_author, $f_date_created, $f_duration
		  FROM $test_tbl
		  INNER JOIN $test_version_tbl ON $f_test_id = $f_vers_test_id
		  WHERE $f_test_id = '$test_id'
		  AND $f_latest_vers = 'Y'";

	#print"$q<br>";
	$rs = db_query( $db, $q );
	$row = db_fetch_row( $db, $rs );


	return $row;

}
*/

# ----------------------------------------------------------------------
# Get test name
# INPUT:
#   Test id
# OUTPUT:
#   Corresponding test name
# ----------------------------------------------------------------------
function test_get_name( $testid ) {

    global $db;
    $test_tbl             = TEST_TBL;
    $f_test_id            = TEST_ID;
    $f_test_name          = TEST_NAME;

    $query = "SELECT $f_test_name
              FROM $test_tbl
              WHERE $f_test_id = '$testid'";

     $result = db_get_one( $db, $query );

     return $result;
}


# ----------------------------------------------------------------------
# Get uploaded documents for a test
# INPUT:
#   Test id
# OUTPUT:
#   Array of uploaded document records
# ----------------------------------------------------------------------
function test_get_uploaded_documents($testid) {

    global $db;
    $f_test_doc_tbl       = MAN_TD_TBL;
    $f_test_display_name  = MAN_TD_DISPLAY_NAME;
    $f_test_id            = MAN_TD_TEST_ID;
    $f_man_test_id        = MAN_TD_MANUAL_TEST_ID;

    $query = "SELECT DISTINCT $f_man_test_id, $f_test_display_name
              FROM $f_test_doc_tbl
              WHERE $f_test_id = '$testid'";

    $rs = &db_query( $db, $query );
    $num = db_num_rows( $db, $rs );

    $row = array();

    for ( $i=0 ; $i < $num ; $i++ ) {
        array_push( $row, db_fetch_row( $db, $rs ) );
    }

    return $row;
}

# ----------------------------------------------------------------------
# Get count of uploaded documents for a test
# INPUT:
#   Test id
# OUTPUT:
#   count of uploaded documents for a test
# ----------------------------------------------------------------------
function test_get_count_uploaded_documents($testid) {

    global $db;
    $f_test_doc_tbl       = MAN_TD_TBL;
    $f_test_display_name  = MAN_TD_DISPLAY_NAME;
    $f_test_id            = MAN_TD_TEST_ID;
    $f_man_test_id        = MAN_TD_MANUAL_TEST_ID;

    $query = "SELECT DISTINCT $f_man_test_id, $f_test_display_name
              FROM $f_test_doc_tbl
              WHERE $f_test_id = '$testid'";

    $rs = &db_query( $db, $query );
    $num = db_num_rows( $db, $rs );

    return $num;
}

# ----------------------------------------------------------------------
# Get uploaded document details for a manual test
# INPUT:
#   Manual Test id
# OUTPUT:
#   document details
# ----------------------------------------------------------------------
function test_get_uploaded_document_detail($man_test_id) {

    global $db;

	$td_tbl					= MAN_TD_TBL;
	$f_td_test_id			= MAN_TD_TBL .".". MAN_TD_MANUAL_TEST_ID;
	$f_display_name			= MAN_TD_TBL .".". MAN_TD_DISPLAY_NAME;

    $test_docs_ver_tbl		= MAN_TD_VER_TBL;
    $f_man_test_id			= MAN_TD_VER_TBL .".". MAN_TD_VER_MANUAL_TEST_ID;
    $f_filename				= MAN_TD_VER_TBL .".". MAN_TD_VER_FILENAME;
    $f_comments				= MAN_TD_VER_TBL .".". MAN_TEST_DOCS_VERS_COMMENTS;
    $f_time_stamp			= MAN_TD_VER_TBL .".". MAN_TD_VER_TIME_STAMP;
    $f_uploaded_by			= MAN_TD_VER_TBL .".". MAN_TD_VER_UPLOADED_BY;
    $f_version				= MAN_TD_VER_TBL .".". MAN_TD_VER_VERSION;
    $f_doc_type				= MAN_TD_VER_TBL .".". MAN_TEST_DOCS_VERS_MANUAL_DOC_TYPE_NAME;



    $query = "SELECT $f_man_test_id, $f_filename, $f_comments, $f_time_stamp, $f_uploaded_by,
             $f_version, $f_doc_type, $f_display_name
             FROM $test_docs_ver_tbl, $td_tbl
			 WHERE $f_td_test_id = $f_man_test_id
             AND $f_man_test_id = '$man_test_id'
             ORDER BY $f_version DESC
             LIMIT 1";

    $rs = & db_query( $db, $query );
    $row = db_fetch_row( $db, $rs ) ;
    return $row;
}


# ----------------------------------------------------------------------
# Get associated requirements for a test
# INPUT:
#   Test id
# OUTPUT:
#   Array of requirement records
# ----------------------------------------------------------------------
function test_get_associated_requirements($test_id) {

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

    $tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
    $f_test_req_assoc_id		= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_ID;
    $f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
    $f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_covered	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_PERCENT_COVERED;

	$tbl_test			= TEST_TBL;
	$f_test_name 		= $tbl_test .".". TEST_NAME;
	$f_test_id 			= $tbl_test .".". TEST_ID;

	$q = "	SELECT
				$f_test_req_assoc_id,
				$f_req_id,
				$f_req_filename,
				$f_test_req_assoc_covered
			FROM $tbl_test_req_assoc
			INNER JOIN $tbl_req ON $f_test_req_assoc_req_id = $f_req_id
			WHERE $f_test_req_assoc_test_id = $test_id";

	global $db;

	$rs = db_query($db, $q);
	$rows = db_fetch_array($db, $rs);

	return $rows;
}

function test_edit_assoc_requirements( $test_id, $session_records_name, $pc_covered_text_input_name ) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

    $tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
    $f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
    $f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_covered	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_PERCENT_COVERED;

	$tbl_test			= TEST_TBL;
	$f_test_name 		= $tbl_test .".". TEST_NAME;
	$f_test_id 			= $tbl_test .".". TEST_ID;

	$s_project_properties   = session_get_project_properties();
	$project_id				= $s_project_properties['project_id'];

	$req_ids = requirement_get_all_ids($project_id);

	foreach($req_ids as $row) {

		$req_id = $row[REQ_ID];

		$q = "	SELECT $f_test_req_assoc_req_id
				FROM $tbl_test_req_assoc
				WHERE $f_test_req_assoc_req_id = $req_id
					AND	$f_test_req_assoc_test_id = $test_id";

		$rs = db_query($db, $q);
		$record_exists = db_num_rows($db, $rs);

		if( session_records_ischecked($session_records_name, $req_id) ) {

			$pc_covered = session_validate_form_get_field($pc_covered_text_input_name.$req_id);
			if( $pc_covered == '' ) {
				$pc_covered = 0;
			}
			//print"pc_covered = $pc_covered<br>";

			if(!$record_exists) {

				# Add new record
				$q = "	INSERT INTO $tbl_test_req_assoc
							($f_test_req_assoc_req_id, $f_test_req_assoc_test_id, $f_test_req_assoc_covered)
						VALUES
							($req_id, $test_id, '$pc_covered')";
			} else {

				# Update current record
				$q = "	UPDATE $tbl_test_req_assoc
						SET
							$f_test_req_assoc_covered = '$pc_covered'
						WHERE
							$f_test_req_assoc_req_id = $req_id
							AND $f_test_req_assoc_test_id = $test_id";
			}
		} else {

			if($record_exists) {
				$q = "	DELETE FROM $tbl_test_req_assoc
						WHERE $f_test_req_assoc_req_id = $req_id
							AND	$f_test_req_assoc_test_id = $test_id";
			}
		}

		db_query($db, $q);
	}
}


# ----------------------------------------------------------------------
# Get associated requirement for a test
# INPUT:
#   Test ID, Requirement ID
# OUTPUT:
#   Percent of covered
# ----------------------------------------------------------------------
function test_get_percent_req_coverage( $test_id, $req_id ) {

    global $db;
    $f_assoc_tbl			= TEST_REQ_ASSOC_TBL;
    $f_assoc_id				= TEST_REQ_ASSOC_ID;
    $f_percent_covered	    = TEST_REQ_ASSOC_PERCENT_COVERED;
    $f_test_id				= TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_req_id				= TEST_REQ_ASSOC_REQ_ID;
	$row					= array();

    $q = "SELECT $f_assoc_id, $f_percent_covered
          FROM $f_assoc_tbl
          WHERE $f_test_id = '$test_id'
		  AND $f_req_id = '$req_id'";

    $rs = &db_query( $db, $q );
    $num = db_num_rows( $db, $rs );

	if( $num > 0 ) {
		$row = db_fetch_row( $db, $rs );
	}
	else {
		//$row[TEST_REQ_ASSOC_PERCENT_COVERED] = "0";
		$row = "";
	}

    return $row;
}

function test_set_percent_req_coverage( $assoc_id, $percent_covered ) {

	global $db;
    $f_assoc_tbl			= TEST_REQ_ASSOC_TBL;
    $f_assoc_id				= TEST_REQ_ASSOC_ID;
    $f_percent_covered	    = TEST_REQ_ASSOC_PERCENT_COVERED;

    $q = "UPDATE $f_assoc_tbl
		 SET $f_percent_covered = '$percent_covered'
		 WHERE $f_assoc_id = '$assoc_id'";
	//print"$q<br>";
    db_query( $db, $q );

}


function test_get_req_assoc_ids( $test_id ) {

	global $db;
    $f_assoc_tbl	= TEST_REQ_ASSOC_TBL;
    $f_req_id		= TEST_REQ_ASSOC_REQ_ID;
    $f_test_id		= TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$rows			= array();

    $q = "SELECT $f_req_id
          FROM $f_assoc_tbl
          WHERE $f_test_id = '$test_id'";

    $rs = db_query( $db, $q );

    while( $row=db_fetch_row($db, $rs) ) {

		$rows[$row[TEST_REQ_ASSOC_REQ_ID]] = "";
    }

    return $rows;
}

# ----------------------------------------------------------------------
# Get field value from test table
# Useful for getting qa_owners, ba_owners, etc from tests
# INPUT:
#   $project_id:
#   $field = field in test table you want to query
#   $blank: set equal to true if you want a blank added to the end of return value
# OUTPUT:
#   array containing array containing field value
# ----------------------------------------------------------------------
function test_get_test_value($project_id, $field, $blank=false) {

    global $db;
    $test_tbl    		= TEST_TBL;
    $f_test_id			= TEST_ID;
    $f_project_id		= TEST_PROJ_ID;
    $f_deleted			= TEST_DELETED;
    $f_archive			= TEST_ARCHIVED;
    $arr_value 			= array();

    $q = "SELECT DISTINCT($field)
          FROM $test_tbl
          WHERE $f_project_id = '$project_id'
          AND $f_deleted = 'N'
          AND $f_archive = 'N'
          AND $field != ''
          ORDER BY $field ASC";
    //print"$q<br>";

    $rs = & db_query( $db, $q );
    while($row = db_fetch_row( $db, $rs ) ) {
		array_push($arr_value, $row[$field]);
    }

    if( $blank == true ) {
    	$arr_value[] = "";
    }

    return $arr_value;

}


# ----------------------------------------------------------------------
# Return values for manaul and automated tests
# OUTPUT:
#   array containing array manual / auto values
# ----------------------------------------------------------------------
function test_get_man_auto_values() {

    $arr = array("Manual", "Automated", "");
    //deleted array content Man/Auto
    return $arr;
}


# ----------------------------------------------------------------------
# Get TestTypeName from test type table
# INPUT:
#   $project_id:
# OUTPUT:
#   array containing array containing field value
# ----------------------------------------------------------------------
function test_get_test_type( $project_id, $blank=false ) {

	global $db;
	$test_type_tbl		  	= TEST_TYPE_TBL;
	$f_test_type_name		= TEST_TYPE_TYPE;
	$f_project_id			= TEST_TYPE_PROJ_ID;
	$test_types				= Array();

	$q = "SELECT DISTINCT $f_test_type_name
		 FROM $test_type_tbl
		 WHERE $f_project_id = '$project_id'
		 ORDER BY $f_test_type_name";

	$rs = & db_query( $db, $q );

	while($row = db_fetch_row( $db, $rs ) ) { ;
		array_push($test_types, $row[$f_test_type_name]);
	}

	if( $blank == true ) {
	   	$test_types[] = "";
	}

    return $test_types;

}

# ----------------------------------------------------------------------
# Get TestTypeName from test type table
# INPUT:
#   $project_id:
# OUTPUT:
#   array containing array containing field value
# ----------------------------------------------------------------------
function test_get_areas_tested( $project_id, $blank=false ) {

	global $db;

	$tbl_test_area		  	= AREA_TESTED_TBL;
	$f_test_area_id			= AREA_TESTED_ID;
	$f_test_area_project_id	= AREA_TESTED_PROJ_ID;
	$f_test_area_name		= AREA_TESTED_NAME;

	$test_areas				= Array();

	$q = "	SELECT DISTINCT $f_test_area_name
			FROM $tbl_test_area
			WHERE $f_test_area_project_id = '$project_id'
			ORDER BY $f_test_area_name";

	$rs = db_query( $db, $q );

	while($row = db_fetch_row( $db, $rs ) ) {
		array_push($test_areas, $row[$f_test_area_name]);
	}

	if( $blank == true ) {
	   	$test_areas[] = "";
	}

    return $test_areas;
}

# ---------------------------------------------------------------------------------
# Return an array with the values for auto pass ( Enabled, Disabled )
# ---------------------------------------------------------------------------------
function test_get_autopass_values() {

	$auto_pass = array('Enabled', 'Disabled');

	return $auto_pass;
}


# ----------------------------------------------------------------------
# Query for returning test records.
#
# OUTPUT:
#	html_table_offset
#	tests
# ----------------------------------------------------------------------
function test_get(	$page,
					$project_id,
					$per_page,
					$order_by,
					$order_dir,
					$page_number ) {

	global $db;

	$tbl_test		= TEST_TBL;
	$f_name 		= TEST_NAME;
	$f_type 		= TEST_TESTTYPE;
	$f_priority 	= TEST_PRIORITY;
	$f_id 			= TEST_ID;
	$f_steps 		= TEST_MANUAL;
	$f_script 		= TEST_AUTOMATED;
	$f_status 		= TEST_STATUS;
	$f_area 		= TEST_AREA_TESTED;
	$f_deleted 		= TEST_DELETED;
	$f_archive 		= TEST_ARCHIVED;
	$f_area_tested 	= TEST_AREA_TESTED;
	$f_project_id 	= TEST_PROJ_ID;

	$q = "	SELECT $f_id,
			$f_name,
			$f_type,
			$f_priority,
			$f_script,
			$f_steps,
			$f_status,
			$f_area_tested,
			$f_archive
		FROM $tbl_test
		WHERE $f_deleted = 'N'
			AND $f_archive = 'N'
			AND $f_project_id = $project_id
		GROUP BY $f_id
		ORDER BY '$order_by' $order_dir";

	if( $per_page!=0 ) {

		$row_count = db_num_rows( $db, db_query($db, $q) );

		$page_number = util_page_number($page_number, $row_count, $per_page);

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * $per_page );

		html_table_offset( 	$row_count,
							$per_page,
							$page_number,
							$order_by,
							$order_dir );

		$q .= " LIMIT $offset, $per_page";
	}
//print$q;
	return db_fetch_array($db, db_query($db, $q) );
}

# ----------------------------------------------------------------------
# Create and run query for displaying test result records.
# Display table header.
#
# OUTPUT:
#   array of test records to copy
# ----------------------------------------------------------------------
function test_copy_apply_filter(	$project_id,
									$release_id,
									$build_id,
									$testset_id,
									$per_page,
									$order_by,
									$order_dir,
									$page_number,
									$where_clause ) {

    global $db;
    $test_tbl          		= TEST_TBL;
	$f_test_id				= TEST_TBL. "." .TEST_ID;
	$f_project_id			= TEST_TBL. "." .PROJECT_ID;
	$f_test_name            = TEST_TBL. "." .TEST_NAME;
	$f_manual_tests         = TEST_TBL. "." .TEST_MANUAL;
	$f_automated_tests      = TEST_TBL. "." .TEST_AUTOMATED;
	$f_ba_owner             = TEST_TBL. "." .TEST_BA_OWNER;
	$f_qa_owner             = TEST_TBL. "." .TEST_QA_OWNER;
	$f_tester				= TEST_TBL. "." .TEST_TESTER;
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

	$q = "SELECT $f_test_name,
			$f_test_id,
			$f_ts_assoc_id,
			$f_ts_assoc_test_status,
			$f_ts_assoc_comments,
			$f_ts_assoc_assigned_to,
			$f_manual_tests,
			$f_automated_tests,
			$f_qa_owner,
			$f_test_load,
			$f_ba_owner,
			$f_tester,
			$f_test_priority,
			$f_area_tested,
			$f_auto_pass,
			$f_test_type
		FROM $ts_assoc_tbl
		INNER JOIN $test_tbl ON $f_ts_assoc_test_id = $f_test_id
		WHERE $f_project_id = '$project_id'
			AND $f_ts_assoc_ts_id = '$testset_id'
			AND $f_deleted = 'N'
			AND $f_archived = 'N'
			$where_clause
		GROUP BY $f_test_id
		ORDER BY $order_by $order_dir";


	if( $per_page!=0 ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * $per_page );

		$row_count = db_num_rows( $db, db_query($db, $q) );

		$page_number = util_page_number($page_number, $row_count, $per_page);

		html_table_offset(	$row_count,
							$per_page,
							$page_number,
							$order_by,
							$order_dir);

		$q .= " LIMIT $offset, $per_page";
	}

    return db_fetch_array($db, db_query($db, $q));
}

# ----------------------------------------------------------------------
# Get field value from test table that is associated in the  test set assoc table
# Useful for getting qa_owners, ba_owners, etc from tests that are in a test set
# INPUT:
#   $project_id:
#   $testset_id
#   $field = field in test table you want to query
#   $blank: set equal to true if you want a blank added to the end of return value
# OUTPUT:
#   array containing usernames of qa_owners
# ----------------------------------------------------------------------
function test_get_types( $project_id ) {

    global $db;

	$test_tbl		= TEST_TBL;
	$f_test_type	= TEST_TESTTYPE;
    $arr_value		= array();

    $q = "SELECT DISTINCT($f_test_type)
          FROM $test_tbl
          ORDER BY $f_test_type ASC";

    $rs = & db_query( $db, $q );
    while($row = db_fetch_row( $db, $rs ) ) { ;
		$arr_value[] = $row[TEST_TESTTYPE];
    }

    return $arr_value;
}


# ----------------------------------------------------------------------
# Create and run query for displaying test step records.
# INPUT:
#	test_id
#   page number is optional.  If not passed in no page number will be created
# OUTPUT:
#   array of test step records
# ----------------------------------------------------------------------
function test_get_test_steps( $test_id, $page_number=null, $csv_name=null,$order_by=null,$order_dir=null ) {

	global $db;

	$ts_tbl				= TEST_STEP_TBL;
	$f_ts_id			= TEST_STEP_ID;
	$f_ts_test_id		= TEST_STEP_TEST_ID;
	$f_ts_step_no		= TEST_STEP_NO;
	$f_ts_action		= TEST_STEP_ACTION;
	$f_ts_test_inputs	= TEST_STEP_TEST_INPUTS;
	$f_ts_expected		= TEST_STEP_EXPECTED;
	$f_info_step		= TEST_STEP_INFO_STEP;
	

	$q = "SELECT
		  $f_ts_id,
		  $f_ts_test_id,
		  $f_ts_step_no,
		  $f_ts_action,
		  $f_ts_test_inputs,
		  $f_ts_expected,
		  $f_info_step

		  FROM $ts_tbl
		  WHERE $f_ts_test_id = '$test_id'";
		  
	if($order_by!=null && $order_dir!=null){
		$q = $q."ORDER BY $order_by $order_dir";
	}else{
		$q = $q."ORDER BY $f_ts_step_no";
	}

	if( RECORDS_PER_PAGE_TEST_STEPS != 0 && isset($page_number) ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_TEST_STEPS );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_TEST_STEPS,
							$page_number,
							$order_by,
							$order_dir,
							$csv_name);

		$q .= " LIMIT $offset, ". RECORDS_PER_PAGE_TEST_STEPS;

	}


	return db_fetch_array( $db, db_query($db, $q) );

}

# ----------------------------------------------------------------------
# Create and run query for displaying test step records in excel or csv format.
# INPUT:
#	test_id
# OUTPUT:
#   array of test step records
# ----------------------------------------------------------------------
function test_get_test_steps_for_export( $test_id ) {

	global $db;

	$ts_tbl				= TEST_STEP_TBL;
	$f_ts_id			= TEST_STEP_ID;
	$f_ts_test_id		= TEST_STEP_TEST_ID;
	$f_ts_step_no		= TEST_STEP_NO;
	$f_ts_action		= TEST_STEP_ACTION;
	$f_ts_test_inputs	= TEST_STEP_TEST_INPUTS;
	$f_ts_expected		= TEST_STEP_EXPECTED;
	$f_info_step		= TEST_STEP_INFO_STEP;

	$q = "SELECT
		  $f_ts_step_no,
		  $f_ts_action,
		  $f_ts_test_inputs,
		  $f_ts_expected,
		  $f_info_step
		  FROM $ts_tbl
		  WHERE $f_ts_test_id = '$test_id'
		  ORDER BY $f_ts_step_no";

	/*
	if( RECORDS_PER_PAGE_TEST_STEPS != 0 && isset($page_number) ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_TEST_STEPS );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_TEST_STEPS,
							$page_number,
							$order_by=null,
							$order_dir=null,
							$csv_name);

		$q .= " LIMIT $offset, ". RECORDS_PER_PAGE_TEST_STEPS;

	}
	*/


	return db_fetch_array( $db, db_query($db, $q) );

}
/*
function test_get_test_steps( $test_version_id, $page_number=null ) {

	global $db;

	$ts_tbl				= TEST_STEP_TBL;
	$f_ts_id			= TEST_STEP_ID;
	$f_ts_version_id	= TEST_STEP_VERSION_ID;
	$f_ts_step_no		= TEST_STEP_NO;
	$f_ts_action		= TEST_STEP_ACTION;
	$f_ts_expected		= TEST_STEP_EXPECTED;

	$q = "SELECT
		  $f_ts_id,
		  $f_ts_version_id,
		  $f_ts_step_no,
		  $f_ts_action,
		  $f_ts_expected
		  FROM $ts_tbl
		  WHERE $f_ts_version_id = '$test_version_id'
		  ORDER BY $f_ts_step_no";

	if( RECORDS_PER_PAGE_TEST_STEPS != 0 && isset($page_number) ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_TEST_STEPS );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_TEST_STEPS,
							$page_number );

		$q .= " LIMIT $offset, ". RECORDS_PER_PAGE_TEST_STEPS;

	}


	return db_fetch_array( $db, db_query($db, $q) );

}
*/

# ----------------------------------------------------------------------
# Get the step numbers from the Test Step table for a given test
# This function is used to populate a list box with "Add Step After Step (1, 2, 3, etc)
# INPUT:
#	test_id
# OUTPUT:
#   array of step numbers
# ----------------------------------------------------------------------
function test_get_test_step_numbers( $test_id ) {

	global $db;

	$ts_tbl				= TEST_STEP_TBL;
	$f_ts_test_id	= TEST_STEP_TEST_ID;
	$f_ts_step_no		= TEST_STEP_NO;
	$row				= array();

	$q = "SELECT
		  $f_ts_step_no
		  FROM $ts_tbl
		  WHERE $f_ts_test_id = '$test_id'
		  ORDER BY $f_ts_step_no";
	$rs = db_query( $db, $q );
	$num = db_num_rows( $db, $rs );

	for ( $i=0 ; $i < $num ; $i++ ) {
        $row[] = $row[$i];
    }
	//$row = db_fetch_row( $db, $rs );

	return $row;

}
/*
function test_get_test_step_numbers( $test_version_id ) {

	global $db;

	$ts_tbl				= TEST_STEP_TBL;
	$f_ts_version_id	= TEST_STEP_VERSION_ID;
	$f_ts_step_no		= TEST_STEP_NO;
	$row				= array();

	$q = "SELECT
		  $f_ts_step_no
		  FROM $ts_tbl
		  WHERE $f_ts_version_id = '$test_version_id'
		  ORDER BY $f_ts_step_no";
	$rs = db_query( $db, $q );
	$num = db_num_rows( $db, $rs );

	for ( $i=0 ; $i < $num ; $i++ ) {
        $row[] = $row[$i];
    }
	//$row = db_fetch_row( $db, $rs );

	return $row;

}
*/
# ----------------------------------------------------------------------
# Add a test step to a test
# INPUT:
#	test_id
#   location - enter the step after another or to the end of the list
#   step_action - the action taken in the test
#   step_expected - the expected result
#   action - used for logging.  Adding a Test Step to test_id ...
#	page_name - used for logging
# OUTPUT:
#   none
# ----------------------------------------------------------------------
function test_add_test_step( $test_id, $location, $action, $expected, $test_inputs, $info_step, $page_name ) {

	global $db;

	$ts_tbl			= TEST_STEP_TBL;
	$f_id			= TEST_STEP_ID;
	$f_test_id		= TEST_STEP_TEST_ID;
	$f_step_no		= TEST_STEP_NO;
	$f_action		= TEST_STEP_ACTION;
	$f_expected		= TEST_STEP_EXPECTED;
	$f_test_inputs  = TEST_STEP_TEST_INPUTS;
	$f_info_step	= TEST_STEP_INFO_STEP;

	$max_step_no = test_step_get_max_step_number( $test_id );

	if( $location == 'end' ) {
		$step_no = $max_step_no + 1;
	}
	elseif( $location > 0 ) {
		$step_no = $location + 0.1;
	}

	$q = "INSERT INTO $ts_tbl
		($f_test_id, $f_step_no, $f_action, $f_expected, $f_test_inputs, $f_info_step)
		VALUES ('$test_id', '$step_no', '$action', '$expected', '$test_inputs', '$info_step')";
	db_query( $db, $q );

	################################
	# Add entry into the log table for the project

	$deletion = 'N';
	$creation = 'Y';
	$upload = 'N';
	$log_action = "ADDED TEST STEP TO TEST ID $test_id";

	log_activity_log( $page_name, $deletion, $creation, $upload, $log_action );
	#################################

}
/*
function test_add_test_step( $test_version_id, $location, $action, $expected, $page_name ) {

	global $db;

	$ts_tbl			= TEST_STEP_TBL;
	$f_id			= TEST_STEP_ID;
	$f_version_id	= TEST_STEP_VERSION_ID;
	$f_step_no		= TEST_STEP_NO;
	$f_action		= TEST_STEP_ACTION;
	$f_expected		= TEST_STEP_EXPECTED;

	$max_step_no = test_step_get_max_step_number( $test_version_id );

	if( $location == 'end' ) {
		$step_no = $max_step_no + 1;
	}
	elseif( $location > 0 ) {
		$step_no = $location + 0.1;
	}

	$q = "INSERT INTO $ts_tbl
		($f_version_id, $f_step_no, $f_action, $f_expected)
		VALUES ('$test_version_id', '$step_no', '$action', '$expected')";
	db_query( $db, $q );


	################################
	# Add entry into the log table for the project

	$deletion = 'N';
	$creation = 'Y';
	$upload = 'N';
	$log_action = "ADDED TEST STEP TO TEST ID $test_version_id";

	log_activity_log( $page_name, $deletion, $creation, $upload, $log_action );
	#################################

}
*/
# ----------------------------------------------------------------------
# Returns the max step number for a group of test steps.
# Used when a user wants to add a test step after all the others.
# INPUT:
#	test_id
# OUTPUT:
#   the max test step number
# ----------------------------------------------------------------------
function test_step_get_max_step_number( $test_id ) {

	global $db;

	$test_step_tbl	= TEST_STEP_TBL;
	$f_test_id		= TEST_STEP_TEST_ID;
	$f_step_no		= TEST_STEP_NO;

	$q = "SELECT MAX($f_step_no)
		  FROM $test_step_tbl
		  WHERE $f_test_id = '$test_id'";
	$row = db_get_one( $db, $q );

	return $row;

}

/*
function test_step_get_max_step_number( $test_version_id ) {

	global $db;

	$test_step_tbl	= TEST_STEP_TBL;
	$f_version_id	= TEST_STEP_VERSION_ID;
	$f_step_no		= TEST_STEP_NO;

	$q = "SELECT MAX($f_step_no)
		  FROM $test_step_tbl
		  WHERE $f_version_id = '$test_version_id'";
	$row = db_get_one( $db, $q );

	return $row;

}
*/

# ----------------------------------------------------------------------
# Renumber test steps to 1, 2, 3... and remove 1, 1.1, 1.2, etc.
# INPUT:
#	test_id
# OUTPUT:
#   array of test step records
# ----------------------------------------------------------------------
function test_renumber_test_steps( $test_id ) {

	global $db;

	$test_step_tbl	= TEST_STEP_TBL;
	$f_step_id		= TEST_STEP_ID;
	$f_test_id	= TEST_STEP_TEST_ID;
	$f_step_no		= TEST_STEP_NO;
	$i				= 1;

	$q = "SELECT $f_step_id
		  FROM $test_step_tbl
		  WHERE $f_test_id = '$test_id'
		  ORDER BY $f_step_no ASC";
	$rs = db_query( $db, $q );

	while($row = db_fetch_row($db, $rs) ) {

		$step_id = $row[TEST_STEP_ID];

		$q_update = "UPDATE $test_step_tbl
					SET $f_step_no = '$i'
					WHERE $f_step_id = '$step_id'
					AND $f_test_id = '$test_id'";
		#print"$q_update;<br>";
		db_query( $db, $q_update );

		$i = $i + 1;
	}

}
/*
function test_renumber_test_steps( $test_version_id ) {

	global $db;

	$test_step_tbl	= TEST_STEP_TBL;
	$f_step_id		= TEST_STEP_ID;
	$f_version_id	= TEST_STEP_VERSION_ID;
	$f_step_no		= TEST_STEP_NO;
	$i				= 1;

	$q = "SELECT $f_step_id
		  FROM $test_step_tbl
		  WHERE $f_version_id = '$test_version_id'
		  ORDER BY $f_step_no ASC";
	$rs = db_query( $db, $q );

	while($row = db_fetch_row($db, $rs) ) {

		$step_id = $row[TEST_STEP_ID];

		$q_update = "UPDATE $test_step_tbl
					SET $f_step_no = '$i'
					WHERE $f_step_id = '$step_id'
					AND $f_version_id = '$test_version_id'";
		#print"$q_update;<br>";
		db_query( $db, $q_update );

		$i = $i + 1;
	}

}
*/

# ----------------------------------------------------------------------
# Return the detail of a specific test step
# INPUT:
#	test_step_id
# OUTPUT:
#   array containing the test step information
# ----------------------------------------------------------------------
function test_get_test_step_detail( $test_step_id ) {

	global $db;

	$ts_tbl			= TEST_STEP_TBL;
	$f_id			= TEST_STEP_ID;
	$f_version_id	= TEST_STEP_VERSION_ID;
	$f_step_no		= TEST_STEP_NO;
	$f_action		= TEST_STEP_ACTION;
	$f_inputs		= TEST_STEP_TEST_INPUTS;
	$f_expected		= TEST_STEP_EXPECTED;
	$f_info_step	= TEST_STEP_INFO_STEP;

	$q = "SELECT
		  $f_id,
		  $f_version_id,
		  $f_step_no,
		  $f_action,
		  $f_inputs,
		  $f_expected,
		  $f_info_step

		  FROM $ts_tbl
		  WHERE $f_id = '$test_step_id'";
	$rs = & db_query( $db, $q );
    $row = db_fetch_row( $db, $rs ) ;

	return $row;

}


function test_get_all_versions( $test_id ) {

	global $db;

	$version_tbl		  = TEST_VERS_TBL;
	$f_test_id			  = TEST_VERS_TEST_ID;
	$f_version_id		  = TEST_VERS_ID;
	$f_version_no		  = TEST_VERS_NUMBER;
	$f_latest			  = TEST_VERS_LATEST;
	$f_active_version	  = TEST_VERS_ACTIVE;
	$f_test_comments      = TEST_VERS_COMMENTS;
	$f_test_status        = TEST_VERS_STATUS;
	$f_assigned_to		  = TEST_VERS_ASSIGNED_TO;
	$f_version_author	  = TEST_VERS_AUTHOR;
	$f_date_created		  = TEST_VERS_DATE_CREATED;
	$f_signoff_by		  = TEST_VERS_SIGNOFF_BY;
	$f_signoff_date		  = TEST_VERS_SIGNOFF_DATE;

	$q = "SELECT
		  $f_test_id,
		  $f_version_id,
		  $f_version_no,
		  $f_latest,
		  $f_active_version,
		  $f_test_comments,
		  $f_test_status,
		  $f_assigned_to,
		  $f_version_author,
		  $f_date_created,
		  $f_signoff_by,
		  $f_signoff_date
		  FROM $version_tbl
		  WHERE $f_test_id = '$test_id'";
	$rs = & db_query( $db, $q );
	$num = db_num_rows( $db, $rs );

    $row = array();

    for ( $i=0 ; $i < $num ; $i++ ) {
        array_push( $row, db_fetch_row( $db, $rs ) );
    }

    return $row;
    //$row = db_fetch_array( $db, $rs ) ;

	//return $row;

}

# ----------------------------------------------------------------------
# Update the detail of a specific test step
# INPUT:
#	test_id = used to get the max step number for all the steps associated with a test
#   test_step_id = the step in the test_step table to update
#   step_action = field to update (action taken durning test)
#   step_expeced = field to update (expected result)
#	action = used for logging
#   $page = used for logging
# OUTPUT:
#   none
# ----------------------------------------------------------------------
function test_update_test_step( $test_version_id, $test_step_id, $step_no, $step_action, $step_inputs,
								$step_expected=null, $info_step) {

	global $db;

	$ts_tbl			= TEST_STEP_TBL;
	$f_id			= TEST_STEP_ID;
	$f_step_no		= TEST_STEP_NO;
	$f_action		= TEST_STEP_ACTION;
	$f_inputs		= TEST_STEP_TEST_INPUTS;
	$f_expected		= TEST_STEP_EXPECTED;
	$f_info_step	= TEST_STEP_INFO_STEP;

	$update_clause	= "";

	if( $step_no != 'none' ) {

		if( $step_no == 'end' ) {

			$max_step_no = test_step_get_max_step_number( $test_version_id );
			$step_no = $max_step_no + 1;
		}
		else {
			$step_no = $step_no + 0.1;
		}

		$update_clause = ", ". $f_step_no ."='". $step_no ."'";
	}


	$q = "UPDATE $ts_tbl
		  SET $f_action = '$step_action',
		  	  $f_inputs = '$step_inputs',
			  $f_expected = '$step_expected',
			  $f_info_step = '$info_step'";


	$where_clause = " WHERE $f_id = '$test_step_id'";

	$q = $q . $update_clause . $where_clause;
	db_query( $db, $q );

}

function test_delete_test_step( $test_step_id ) {

	global $db;

	$test_step_tbl	= TEST_STEP_TBL;
	$f_test_step_id	= TEST_STEP_ID;

	$q = "DELETE
		  FROM $test_step_tbl
		  WHERE $f_test_step_id = '$test_step_id'";
	db_query( $db, $q );

}

# -----------------------------------------------------------
# Set the Latest Flag = 'N' where the Latest Flag = 'Y'
# This is used when creating a new test version
function test_reset_latest( $test_id ) {

	global $db;
	$test_version_tbl	= TEST_VERS_TBL;
	$f_test_id			= TEST_VERS_TEST_ID;
	$f_latest			= TEST_VERS_LATEST;


	$q = "UPDATE
		 $test_version_tbl
		 SET $f_latest = 'N'
		 WHERE $f_latest = 'Y'
		 AND $f_test_id = '$test_id'";
	//print"$q<br>";
	db_query( $db, $q );

}

# --------------------------------------------------------------------------
# Update the TestVersion table making and change the Active Version
# The Active Version is the version of the test that will appear
# when somebody runs a test.  It only applies if they try to run a manual
# test that has test steps.
# INPUT:
#	TestID: The TestID.
#   TestVersionID: The version that will become active
# --------------------------------------------------------------------------
function test_make_active_version( $test_id, $test_version_id ) {

	global $db;

	$version_tbl		= TEST_VERS_TBL;
	$f_test_id			= TEST_VERS_TEST_ID;
	$f_version_id		= TEST_VERS_ID;
	$f_active			= TEST_VERS_ACTIVE;


	$q = "UPDATE $version_tbl SET $f_active = 'N' WHERE $f_test_id = '$test_id'";
	db_query( $db, $q );

	$q2 = "UPDATE $version_tbl SET $f_active = 'Y' WHERE $f_version_id = '$test_version_id'";
	db_query( $db, $q2 );
}

# THIS INS'T FINISHED.  WE CAN:
# REMOVE ASSIGNED_TO FROM THE VERSION TABLE
# ADD INPUTS TO THE VERSION TABLE
# ADD INFO STEP TO THE VERSION TABLE
function test_add_new_version( $test_id, $test_version_id, $comments, $status, $assigned_to ) {

	global $db;

	$version_tbl		  = TEST_VERS_TBL;
	$f_test_id			  = TEST_VERS_TEST_ID;
	$f_version_id		  = TEST_VERS_ID;
	$f_version_no		  = TEST_VERS_NUMBER;
	$f_latest			  = TEST_VERS_LATEST;
	$f_active_version	  = TEST_VERS_ACTIVE;
	$f_test_comments      = TEST_VERS_COMMENTS;
	$f_test_status        = TEST_VERS_STATUS;
	$f_assigned_to		  = TEST_VERS_ASSIGNED_TO;
	$f_version_author	  = TEST_VERS_AUTHOR;
	$f_date_created		  = TEST_VERS_DATE_CREATED;

	$ts_tbl				= TEST_STEP_TBL;
	$f_ts_id			= TEST_STEP_ID;
	$f_ts_version_id	= TEST_STEP_VERSION_ID;
	$f_step_no			= TEST_STEP_NO;
	$f_action			= TEST_STEP_ACTION;
	$f_expected			= TEST_STEP_EXPECTED;



	# SELECT THE MAX VERSION AND ADD ONE
	$q_version = "SELECT $f_version_no
				 FROM $version_tbl
				 WHERE $f_test_id = '$test_id'
				 AND $f_latest = 'Y'";
	#print"$q_version<br>";
	$current_version = db_get_one( $db, $q_version );
	#print"current_version = $current_version<br>";
	$new_version = util_increment_version( $current_version );
	#print"new_version = $new_version<br>";

	# SET THE LATEST FLAG TO 'N' FOR THE LAST VERSION THAT EQUALED 'Y'
	test_reset_latest( $test_id );


	# INSERT INTO VERSION TABLE
	$created_date = date_get_short_dt();
	$author = session_get_username();

	$q = "INSERT INTO $version_tbl
		  ( $f_test_id, $f_version_no, $f_latest, $f_active_version, $f_test_comments,
			$f_test_status, $f_assigned_to, $f_version_author, $f_date_created )
		  VALUES
		  ( '$test_id', '$new_version', 'Y', 'N', '$comments', '$status', '$assigned_to',
			'$author', '$created_date')";
	db_query( $db, $q );

	$new_version_id = test_get_latest_version( $test_id );

	# COPY TEST STEPS INTO NEW VERSION
	$q_steps = "SELECT $f_step_no, $f_action, $f_expected
				FROM $ts_tbl
				WHERE $f_ts_version_id = $test_version_id";
	$rs_steps = db_query( $db, $q_steps );
	$num_steps = db_num_rows( $db, $rs_steps );

	if( $num_steps > 0 ) {

		 while( $row_steps = db_fetch_row($db, $rs_steps) ) {

			$q_insert = "INSERT INTO $ts_tbl
						($f_ts_version_id, $f_step_no, $f_action, $f_expected)
						 VALUES
						('$new_version_id', '$row_steps[$f_step_no]', '$row_steps[$f_action]',
						 '$row_steps[$f_expected]')";
			//print"$q_insert<br>";
			db_query( $db, $q_insert );
		 }
	}

}

function test_get_latest_version( $test_id ) {

	global $db;
	$version_tbl		  = TEST_VERS_TBL;
	$f_test_id			  = TEST_VERS_TEST_ID;
	$f_version_id		  = TEST_VERS_ID;

	$q = "SELECT MAX($f_version_id) FROM $version_tbl WHERE $f_test_id = '$test_id'";
	$version_id = db_get_one( $db, $q );

	return $version_id;
}

function test_get_last_run($test_id, $testset_id) {

	global $db;
	$test_results_tbl		= TEST_RESULTS_TBL;
	$f_results_os			= TEST_RESULTS_OS;
	$f_time_started			= TEST_RESULTS_TIME_STARTED;
	$f_testset_id			= TEST_RESULTS_TEST_SET_ID;
	$f_test_id				= TEST_RESULTS_TEMPEST_TEST_ID;

	$q = "	SELECT
				$f_results_os,
				$f_time_started
			FROM $test_results_tbl
			WHERE
				$f_test_id = $test_id
				AND $f_testset_id = $testset_id
			ORDER BY $f_time_started DESC LIMIT 1";

	$row = db_fetch_row($db, db_query($db, $q));

	return $row;
}

function test_get_last_modified($project_id) {

    global $db;

	$tbl_test             = TEST_TBL;
	$f_test_id			  = TEST_ID;
	$f_test_name          = TEST_NAME;
	$f_project_id		  = PROJECT_ID;
	$f_purpose			  = TEST_PURPOSE;
	$f_priority			  = TEST_PRIORITY;
	$f_test_type          = TEST_TESTTYPE;
    $f_area_tested        = TEST_AREA_TESTED;
	$f_autopass           = TEST_AUTO_PASS;
	$f_test_deleted       = TEST_DELETED;
    $f_test_archived      = TEST_ARCHIVED;
	$f_ba_owner           = TEST_BA_OWNER;
	$f_qa_owner           = TEST_QA_OWNER;
	$f_manual_test        = TEST_MANUAL;
	$f_automated_tests    = TEST_AUTOMATED;
	$f_performance        = TEST_LR;
	$f_assigned_to		  = TEST_ASSIGNED_TO;
	$f_assigned_by		  = TEST_ASSIGNED_BY;
	$f_date_assigned	  = TEST_DATE_ASSIGNED;
	$f_date_expected	  = TEST_DATE_EXPECTED;
	$f_date_completed	  = TEST_DATE_COMPLETE;
	$f_duration			  = TEST_DURATION;
	$f_last_updated		  = TEST_LAST_UPDATED;
	$f_last_updated_by	  = TEST_LAST_UPDATED_BY;

	$f_last_updated		  = TEST_LAST_UPDATED;
	$f_last_updated_by	  = TEST_LAST_UPDATED_BY;

	$last_updated		  = date_get_short_dt();
	$s_user_properties 	  = session_get_user_properties();
	$s_user_name		  = $s_user_properties['username'];
	$last_updated_by	  = $s_user_name;

	$q = "	SELECT
				$f_test_id,
				$f_test_name,
				$f_test_type,
				$f_priority,
				$f_automated_tests,
				$f_manual_test,
				$f_area_tested,
				$f_test_archived,
				$f_last_updated,
				$f_last_updated_by
		FROM $tbl_test
		WHERE $f_test_deleted = 'N'
			AND $f_test_archived = 'N'
			AND $f_project_id = $project_id
		GROUP BY $f_test_id
		ORDER BY $f_last_updated DESC
		LIMIT 5";

	$rs = db_query($db, $q);

	return db_fetch_array($db, $rs);

}

# --------------------------------------------------------------------------
# Update the LastUpdatedBy and LastUpdatedDate fields
# Putting this in a function because different people may not want to
# update these fields for every action (test_update, test_step_update, file_upload, etc)
# INPUT:
#	TestID: The TestID.
# --------------------------------------------------------------------------
function test_change_last_update( $test_id ) {

	global $db;

	$test_tbl             = TEST_TBL;
	$f_test_id			  = TEST_ID;
	$f_last_updated		  = TEST_LAST_UPDATED;
	$f_last_updated_by	  = TEST_LAST_UPDATED_BY;

	$last_updated_date	  = date_get_short_dt();
	$s_user_properties 	  = session_get_user_properties();
	$s_user_name		  = $s_user_properties['username'];
	$last_updated_by	  = $s_user_name;

	$q = "UPDATE $test_tbl
		  SET
		  $f_last_updated = '$last_updated_date',
		  $f_last_updated_by = '$last_updated_by'
		  WHERE $f_test_id = '$test_id'";

	db_query( $db, $q );

}

function test_requirement_get_pc_covered($test_id, $req_id) {

	global $db;

    $tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
    $f_test_req_assoc_id		= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_ID;
    $f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
    $f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_covered	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_PERCENT_COVERED;

	$q = "	SELECT
				$f_test_req_assoc_covered
			FROM $tbl_test_req_assoc
			WHERE
				$f_test_req_assoc_req_id = $req_id
				AND $f_test_req_assoc_test_id = $test_id";

	$pc_covered = db_get_one($db, $q);

	return $pc_covered;
}

function test_import_csv($test_id, $name) {

	$ts_tbl				= TEST_STEP_TBL;
	$f_ts_id			= TEST_STEP_ID;
	$f_ts_test_id		= TEST_STEP_TEST_ID;
	$f_ts_step_no		= TEST_STEP_NO;
	$f_ts_action		= TEST_STEP_ACTION;
	$f_ts_input			= TEST_STEP_TEST_INPUTS;
	$f_ts_expected		= TEST_STEP_EXPECTED;
	$f_info_step		= TEST_STEP_INFO_STEP;

	global $db;

	$s_project_properties   = session_get_project_properties();
	$project_name           = $s_project_properties['project_name'];
	$redirect_on_error		= "test_step_import_csv_page.php?test_id=$test_id";

	$uploaded_file = $_FILES[$name];

	if( $uploaded_file['size'] != '0' && is_uploaded_file($uploaded_file['tmp_name']) ) {

		# SCRIPT TO UPDATE RELEASES IN THE SYSTEM
		$debug = false;
		$show_tbl = false;
		$row = 1;

		# READ IN A LINE FROM DATA FILE
		$fhandle 	= fopen($uploaded_file['tmp_name'], "r");
		fgetcsv($fhandle, filesize($uploaded_file['tmp_name']), ",");

		if( !$debug ) {

			$q = "DELETE FROM $ts_tbl WHERE $f_ts_test_id = '$test_id'";
			db_query($db, $q);
		}


		while( $data = fgetcsv($fhandle, filesize($uploaded_file['tmp_name']), ",") ) {
			//print_r($data);exit;
			$num = count($data);
			$step_no = $row;

			$tc_no = '';
			$desc = '';
			$state = '';
			$inputs = '';
			$expected = '';
			$info = '';
			$action = '';
			$jursidiction = '';
			$test_input = '';

			# Get record from CSV file
			$tc_no 		= @util_html_special_chars_string($data[0]);
			$action		= @util_html_special_chars_string($data[1]);
			$test_input	= @util_html_special_chars_string($data[2]);
			$expected 	= @util_html_special_chars_string($data[3]);
			$info 		= @util_html_special_chars_string($data[4]);
		
		
			# Test if it is just blank data
			$str = $action.$test_input.$expected.$info;

			if( empty($str) ) {

				break;
			}

			$newline = "<br>";

			# Replace single quotes so the SQL works
			$test_input = str_replace( "|", ",", $test_input );
			$action = str_replace( "|", ",", $action );
			$expected = str_replace( "|", ",", $expected );

			$q = "	INSERT INTO $ts_tbl
						( $f_ts_test_id, $f_ts_step_no, $f_ts_action, $f_ts_input, $f_ts_expected, $f_info_step )
					VALUES
						( '$test_id', '$step_no', '$action', '$test_input', '$expected', '$info' )";

			if( $debug ) {
				print"$q;<hr>";
			}
			else {
				//print"$q;<hr>";
				db_query($db, $q);
			}


			$row++;
		}

		fclose ($fhandle);

		# Create a version of the file in Test Doc Version table if the upload was successful
		if( file_name_exists( $test_id, $file_name ) ) {  
			
			# Get the manual_test_id from the Manual Test Doc table and add a new file version
			$manual_test_id = test_get_manual_test_id( $test_id, $file_name );
			file_add_supporting_test_doc_version( $file_temp_name, $file_name, $test_id, $manual_test_id, $comments="", $file_type="" );
		}
		else {
			# Add a new file
			file_add_supporting_test_doc( $file_temp_name, $file_name, $test_id, $comments="", $file_type="" );
		}

	}
	else { # The user tried to upload and empty file
		error_report_show( $redirect_on_error, NO_FILE_SPECIFIED );
	}

}

function test_import_excel( $test_id, $name ) {

	global $db;

	$ts_tbl				= TEST_STEP_TBL;
	$f_ts_id			= TEST_STEP_ID;
	$f_ts_test_id		= TEST_STEP_TEST_ID;
	$f_ts_step_no		= TEST_STEP_NO;
	$f_ts_action		= TEST_STEP_ACTION;
	$f_ts_input			= TEST_STEP_TEST_INPUTS;
	$f_ts_expected		= TEST_STEP_EXPECTED;
	$f_info_step		= TEST_STEP_INFO_STEP;

	$s_project_properties   = session_get_project_properties();
	$project_name           = $s_project_properties['project_name'];
	$redirect_on_error		= "test_step_import_csv_page.php?test_id=$test_id";

	$uploaded_file		= $_FILES[$name];
	$file_name			= $_FILES[$name]['name'];
	$file_temp_name		= $_FILES[$name]['tmp_name'];
	$file_size			= $_FILES[$name]['size'];

	/*
	print"file_name = $file_name<br>";
	print"file_temp_name = $uploaded_file[tmp_name]<br>";
	print"file_size = $uploaded_file[size]<br>";
	*/

	if( $file_size != '0' && is_uploaded_file($file_temp_name) ) {

		
		require_once './Excel/reader.php';
		
		// ExcelFile($filename, $encoding);
		$data = new Spreadsheet_Excel_Reader();


		// Set output Encoding.
		$data->setOutputEncoding('CP1251');

		/***
		* if you want you can change 'iconv' to mb_convert_encoding:
		* $data->setUTFEncoder('mb');
		*
		**/

		/***
		* By default rows & cols indeces start with 1
		* For change initial index use:
		* $data->setRowColOffset(0);
		*
		**/

		/***
		*  Some function for formatting output.
		* $data->setDefaultFormat('%.2f');
		* setDefaultFormat - set format for columns with unknown formatting
		*
		* $data->setColumnFormat(4, '%.3f');
		* setColumnFormat - set format for column (apply only to number fields)
		*
		**/
		$data->read( $uploaded_file['tmp_name'] );

		//error_reporting(E_ALL ^ E_NOTICE);
		$debug = false;

		if( !$debug ) {

			$q = "DELETE FROM $ts_tbl WHERE $f_ts_test_id = '$test_id'";
			db_query($db, $q);
		}

		for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {

			$step_no	= '';
			$action		= '';
			$test_input = '';
			$expected	= '';
			$info		= '';
			
			//for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
				
			//echo "\"".$data->sheets[0]['cells'][$i][1]."\",";

			/*
			print"i = $i<br>";
				print"j = $j<br>";
			*/
			
			# Get record from CSV file
			$step_no	= @util_html_special_chars_string($data->sheets[0]['cells'][$i][1]);
			$action		= @util_html_special_chars_string($data->sheets[0]['cells'][$i][2]);
			$test_input	= @util_html_special_chars_string($data->sheets[0]['cells'][$i][3]);
			$expected 	= @util_html_special_chars_string($data->sheets[0]['cells'][$i][4]);
			$info 		= @util_html_special_chars_string($data->sheets[0]['cells'][$i][5]);
			

			# We don't need to insert the column headers
			if( $i > 1 ) {

				# Insert into the Test Set table
				$q = "	INSERT INTO $ts_tbl
						( $f_ts_test_id, $f_ts_step_no, $f_ts_action, $f_ts_input, $f_ts_expected, $f_info_step )
					VALUES
						( '$test_id', '$step_no', '$action', '$test_input', '$expected', '$info' )";

				if( $debug ) {
					print"$q;<br>";
				}
				else {
					db_query($db, $q);
				}

			}

		}

		# Create a version of the file in Test Doc Version table if the upload was successful
		if( file_name_exists( $test_id, $file_name ) ) { 

			# Get the manual_test_id from the Manual Test Doc table and add a new file version
			$manual_test_id = test_get_manual_test_id( $test_id, $file_name );
			file_add_supporting_test_doc_version( $file_temp_name, $file_name, $test_id, $manual_test_id, $comments="", $file_type="" );
		}
		else {
			# Add a new file
			file_add_supporting_test_doc( $file_temp_name, $file_name, $test_id, $comments="", $file_type="" );
		}
		
	}
	else { # The user tried to upload and empty file
		error_report_show( $redirect_on_error, NO_FILE_SPECIFIED );
	}

}

# ----------------------------------------------------------------------
# Get the max file version id for an uploaded test
# INPUT:
#   Manual Test id
# OUTPUT:
#   doc version
# ----------------------------------------------------------------------
function test_get_max_file_version( $manual_test_id ) {

	global $db;
    $version_tbl		  = MAN_TD_VER_TBL;
    $f_man_test_id        = MAN_TD_VER_MANUAL_TEST_ID;
    $f_version            = MAN_TD_VER_VERSION;
    

    $q = "SELECT MAX($f_version)
	      FROM $version_tbl
		  WHERE $f_man_test_id = '$manual_test_id'";
    $version = db_get_one( $db, $q );

    return $version;

}

# --------------------------------------------------------------------------
# Return the history related to a specific test id
# INPUT:
#	Manual Test ID
# --------------------------------------------------------------------------
function test_get_document_detail( $manual_test_id ) 
{

	global $db;

	$td_tbl				= MAN_TD_TBL;
	$f_man_test_id		= MAN_TD_TBL .".". MAN_TD_MANUAL_TEST_ID;
	$f_display_name		= MAN_TD_TBL .".". MAN_TD_DISPLAY_NAME;
	
	$td_vers_tbl		= MAN_TD_VER_TBL;
	$f_vers_test_id		= MAN_TD_VER_TBL .".". MAN_TD_VER_MANUAL_TEST_ID;
	$f_filename			= MAN_TD_VER_TBL .".". MAN_TD_VER_FILENAME;
	$f_comments			= MAN_TD_VER_TBL .".". MAN_TEST_DOCS_VERS_COMMENTS;
	$f_time_stamp		= MAN_TD_VER_TBL .".". MAN_TD_VER_TIME_STAMP;
	$f_uploaded_by		= MAN_TD_VER_TBL .".". MAN_TD_VER_UPLOADED_BY;
	$f_version			= MAN_TD_VER_TBL .".". MAN_TD_VER_VERSION;
	$f_doc_type			= MAN_TD_VER_TBL .".". MAN_TEST_DOCS_VERS_MANUAL_DOC_TYPE_NAME;

	$q = "SELECT
			$f_man_test_id, $f_display_name, $f_filename, $f_comments, $f_time_stamp,
			$f_uploaded_by, $f_version, $f_doc_type
		 FROM 
			$td_tbl, $td_vers_tbl
		 WHERE
			$f_man_test_id = $f_vers_test_id
		 AND
			$f_man_test_id = '$manual_test_id'";
	
	$rs = db_query( $db, $q );
	$row = db_fetch_array( $db, $rs );

	return $row;
			
		  

}

# -----------------------------------------------------------------------------
# Get manual_test_id from the ManualTestDoc table given a test_id and file name
# INPUT:
#   test_id
#	file_name - the display name of the file
# OUTPUT:
#   manual_test_id
# ------------------------------------------------------------------------------
function test_get_manual_test_id( $test_id, $file_name ) {

    global $db;

	$td_tbl					= MAN_TD_TBL;
	$f_manual_test_id		= MAN_TD_MANUAL_TEST_ID;
	$f_test_id				= MAN_TD_TEST_ID;
	$f_display_name			= MAN_TD_DISPLAY_NAME;

	$q = "SELECT 
			$f_manual_test_id
		  FROM
			$td_tbl
		  WHERE 
			$f_test_id = '$test_id'
		  AND
		  	$f_display_name = '$file_name'";
	
	
	$manual_test_id = db_get_one( $db, $q );

	return $manual_test_id;
}

# ----------------------------------------------------------------------
# Get all screen names associated to a project
# INPUT:
#   project_id
#   order_by  - order the results by this field
#   order_dir - the order in which the results will return ASC or DESC
# OUTPUT:
#   an array of all the screens in the screens_tbl
# ----------------------------------------------------------------------
function test_get_screens( $project_id, $order_by, $order_dir, $page_number=null ) {

	global $db;
	$screen_tbl		= SCREEN_TBL;
	$f_screen_id	= SCREEN_ID;
	$f_project_id	= SCREEN_PROJ_ID;
	$f_screen_name	= SCREEN_NAME;
	$f_screen_desc	= SCREEN_DESC;
	$f_screen_order = SCREEN_ORDER;


	$q = "SELECT
			$f_screen_id, $f_screen_name, $f_screen_desc, $f_screen_order
		 FROM
			$screen_tbl
		 WHERE 
			$f_project_id = '$project_id'
		 ORDER BY
			 $order_by $order_dir";
	
	if( !is_null($page_number) ) {
		if( RECORDS_PER_PAGE_25 != 0 ) {

			# Add the limit clause to the query so that we only show n number of records per page
			$offset = ( ( $page_number - 1 ) *  RECORDS_PER_PAGE_25 );

			html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
								RECORDS_PER_PAGE_25,
								$page_number );

			$q .= " LIMIT $offset, ". RECORDS_PER_PAGE_25;

		}
	}
	
	//print"$q<br>";
	$rs = db_query( $db, $q );

	return db_fetch_array( $db, $rs );

}


# ----------------------------------------------------------------------
# Get a specific screen name given the screen_id
# INPUT:
#   screen_id  - order the results by this field
# OUTPUT:
#   an array of the details for a speceific screen
# ----------------------------------------------------------------------
function test_get_screen( $screen_id ) {

	global $db;
	$screen_tbl		= SCREEN_TBL;
	$f_screen_id	= SCREEN_ID;
	$f_project_id	= SCREEN_PROJ_ID;
	$f_screen_name	= SCREEN_NAME;
	$f_screen_desc	= SCREEN_DESC;
	$f_screen_order = SCREEN_ORDER;

	$q = "SELECT
			$f_screen_id, $f_screen_name, $f_screen_desc, $f_screen_order
		 FROM
			$screen_tbl
		 WHERE 
			$f_screen_id = '$screen_id'";
			 
	//print"$q<br>";
	$rs = db_query( $db, $q );

	return db_fetch_row( $db, $rs );

}





# ----------------------------------------------------------------------
# Find out if a screen name already exists
# INPUT:
#   project_id
#   screen_name
# OUTPUT:
#   true if the screen name already exists in the system
#   false if the screen name doesn't exist
# ----------------------------------------------------------------------
function test_screen_name_exists( $project_id, $screen_name ) {

	global $db;
	$screen_tbl		= SCREEN_TBL;
	$f_project_id	= SCREEN_PROJ_ID;
	$f_screen_name	= SCREEN_NAME;

	$q = "SELECT
			$f_screen_name
		 FROM
			$screen_tbl
		 WHERE 
			$f_project_id = '$project_id'
		 AND 
			$f_screen_name = '$screen_name'";

	$rs = db_query( $db, $q );
	$num = db_num_rows( $db, $rs );
	
	return $num;

}

# ----------------------------------------------------------------------
# Insert a record into the screen table
# INPUT:
#   project_id
#   screen_name
#   description
# ----------------------------------------------------------------------
function test_add_screen( $project_id, $screen_name, $description, $screen_order ) {

	global $db;
	$screen_tbl		= SCREEN_TBL;
	$f_screen_id	= SCREEN_ID;
	$f_project_id	= SCREEN_PROJ_ID;
	$f_screen_name	= SCREEN_NAME;
	$f_screen_desc	= SCREEN_DESC;
	$f_screen_order = SCREEN_ORDER;

	$max_screen_no = test_get_max_screen_number();

	if( $screen_order == 'end' ) {
		$order_no = $max_screen_no + 1;
	}
	elseif( $screen_order > 0 ) {
		$order_no = $screen_order + 0.1;
	}

	$q = "INSERT INTO $screen_tbl
		  ( $f_project_id, $f_screen_name, $f_screen_desc, $f_screen_order )
		  VALUES( '$project_id', '$screen_name', '$description', '$order_no')";
	
	//print"$q<br>";
	db_query( $db, $q );

}

# ----------------------------------------------------------------------
# Update a record into the screen table
# INPUT:
#   screen_id
#   screen_name
#   description
# ----------------------------------------------------------------------
function test_update_screen( $screen_id, $screen_name, $description, $screen_order ) {

	global $db;
	$screen_tbl		= SCREEN_TBL;
	$f_screen_id	= SCREEN_ID;
	$f_screen_name	= SCREEN_NAME;
	$f_screen_desc	= SCREEN_DESC;
	$f_screen_order = SCREEN_ORDER;

	# Find out the current screen order number
	$current_screen_order = test_get_screen_value( $screen_id, $f_screen_order);

	# Need to update the screen_number if it's changed.  Else leave it the same
	if( $screen_order != $current_screen_order ) {

		# The user has changed the screen number.  Find the max number
		$max_screen_no = test_get_max_screen_number();

		if( $screen_order == 'end' ) {
			$order_no = $max_screen_no + 1;
		}
		elseif( $screen_order > 0 ) {
			$order_no = $screen_order + 0.1;
		}
	}
	else {
		$order_no = $current_screen_order;
	}

	$q = "UPDATE $screen_tbl
		  SET 
			  $f_screen_name = '$screen_name',
			  $f_screen_desc = '$description',
			  $f_screen_order = '$order_no'
		  WHERE
			  $f_screen_id = '$screen_id'";
	
	db_query( $db, $q );

}

# ----------------------------------------------------------------------
# Returns the all the screen order numbers.
# If $order_num is passed in, it will not be included in the list
# of numbers.
# OPTIONAL INPUT:
#	order_num
# OUTPUT:
#   an array of the order number as the appear in the db and the 
#	associated text as it will appear in the list box
# ----------------------------------------------------------------------
function test_get_screen_order_numbers( $order_num=null ) {

	global $db;
	$screen_tbl			= SCREEN_TBL;
	$f_screen_order		= SCREEN_ORDER;
	$order_numbers		= array();
	$i = 0;

	$q = "SELECT $f_screen_order FROM $screen_tbl ORDER BY $f_screen_order";
	$rs = db_query( $db, $q );

	while($row = db_fetch_row( $db, $rs ) ) {
		
		# Don't include order_num in the list if it's passed in
		if( !is_null($order_num) && $row[$f_screen_order] == $order_num ) {
			continue;
		}
		else {

			$order_numbers[$row[$f_screen_order]] = "After ". $row[$f_screen_order];	
		}
	}

	return $order_numbers;
		
}

# ----------------------------------------------------------------------
# Returns the all the screen order numbers.
# If $order_num is passed in, it will not be included in the list
# of numbers.
# OPTIONAL INPUT:
#	order_num
# OUTPUT:
#   an array of the order number as the appear in the db and the 
#	associated text as it will appear in the list box
# ----------------------------------------------------------------------
function test_get_field_order_numbers( $order_num=null ) {

	global $db;
	$field_tbl			= FIELD_TBL;
	$f_field_order		= FIELD_ORDER;
	$order_numbers		= array();
	$i = 0;

	$q = "SELECT $f_field_order FROM $field_tbl ORDER BY $f_field_order";
	$rs = db_query( $db, $q );

	while($row = db_fetch_row( $db, $rs ) ) {
		
		# Don't include order_num in the list if it's passed in
		if( !is_null($order_num) && $row[$f_screen_order] == $order_num ) {
			continue;
		}
		else {

			$order_numbers[$row[$f_field_order]] = "After ". $row[$f_field_order];	
		}
	}
	
	return $order_numbers;
		
}

# ----------------------------------------------------------------------
# Returns the max screen number for a group of screen.
# Used when a user wants to add a test step after all the others.
# INPUT:
#	test_id
# OUTPUT:
#   the max test step number
# ----------------------------------------------------------------------
function test_get_max_screen_number() {

	global $db;
	$screen_tbl		= SCREEN_TBL;
	$f_screen_id	= SCREEN_ID;
	$f_screen_order = SCREEN_ORDER;

	$q = "SELECT MAX($f_screen_order)
		  FROM $screen_tbl";
	$row = db_get_one( $db, $q );

	return $row;

}


function test_get_screen_value( $screen_id, $field_name ) {

	global $db;
	$screen_tbl		= SCREEN_TBL;
	$f_screen_id	= SCREEN_ID;

	$q = "SELECT $field_name
	      FROM $screen_tbl
		  WHERE $f_screen_id = '$screen_id'";
	$row = db_get_one( $db, $q );

	return $row;

}

# ----------------------------------------------------------------------
# Delete a screen from the screen table
# INPUT:
#   screen_id
# ----------------------------------------------------------------------
function test_delete_screen( $screen_id ) {

	print"NO CODE YET.  MUST DELETE ALL ASSOCIATED FIELDS TOO";
}



# ----------------------------------------------------------------------
# Find out if a field name already exists
# INPUT:
#   field_name
#   screen_id
# OUTPUT:
#   true if the field name already exists on the screen
#   false if the field name doesn't exist
# ----------------------------------------------------------------------
function test_field_name_exists( $field_name, $screen_id ) {

	global $db;
	$field_tbl		= FIELD_TBL;
	$f_field_name	= FIELD_NAME;
	$f_screen_id	= FIELD_SCREEN_ID;

	$q = "SELECT
			$f_field_name
		 FROM
			$field_tbl
		 WHERE 
			$f_field_name = '$field_name'
		 AND 
			$f_screen_id = '$screen_id'";

	$rs = db_query( $db, $q );
	$num = db_num_rows( $db, $rs );
	
	return $num;

}

# ----------------------------------------------------------------------
# Insert a record into the field_tbl
# 
# ----------------------------------------------------------------------
function test_add_field( $field_name, $screen_id, $description, $field_order, $text_box ) {

	global $db;
	$field_tbl		= FIELD_TBL;
	$f_field_name	= FIELD_NAME;
	$f_screen_id	= FIELD_SCREEN_ID;
	$f_description	= FIELD_DESC;
	$f_field_order	= FIELD_ORDER;
	$f_text_only	= FIELD_TEXT_ONLY;

	$max_field_no = test_get_max_field_number( $screen_id );

	if( $field_order == 'end' ) {
		$order_no = $max_field_no + 1;
	}
	elseif( $field_order > 0 ) {
		$order_no = $field_order + 0.1;
	}

	# Set the value for text box to Y or N
	if( $text_box == 'on' ) {
		$text_box = 'Y';
	}
	else {
		$text_box = 'N';
	}

	$q = "INSERT INTO $field_tbl
		  ($f_field_name, $f_screen_id, $f_description, $f_field_order, $f_text_only)
		  VALUES('$field_name', '$screen_id', '$description', '$order_no', '$text_box')";

	//print"$q<br>";
	db_query( $db, $q );

}

# ----------------------------------------------------------------------
# Returns the max field number for all fields on a screen.
# Used when a user wants to add a field after all the others.
# INPUT:
#	screen_id
# OUTPUT:
#   the max test step number
# ----------------------------------------------------------------------
function test_get_max_field_number( $screen_id ) {

	global $db;
	$field_tbl		= FIELD_TBL;
	$f_screen_id	= FIELD_SCREEN_ID;
	$f_field_order	= FIELD_ORDER;

	$q = "SELECT MAX($f_field_order)
		  FROM $field_tbl
		  WHERE $f_screen_id = '$screen_id'";

	$row = db_get_one( $db, $q );

	return $row;

}

# ----------------------------------------------------------------------
# NO LONGER USED
# ----------------------------------------------------------------------
/*
function test_get_fields( $project_id, $order_by, $order_dir ) {

	global $db;
	$field_tbl		= FIELD_TBL;
	$f_field_id		= FIELD_TBL .".". FIELD_ID;
	$f_field_name	= FIELD_TBL .".". FIELD_NAME;
	$f_fscreen_id	= FIELD_TBL .".". FIELD_SCREEN_ID;
	$f_description	= FIELD_TBL .".". FIELD_DESC;
	$f_field_order	= FIELD_TBL .".". FIELD_ORDER;
	$f_text_only	= FIELD_TBL .".". FIELD_TEXT_ONLY;

	$screen_tbl		= SCREEN_TBL;
	$f_screen_id	= SCREEN_TBL .".". SCREEN_ID;
	$f_screen_name	= SCREEN_TBL .".". SCREEN_NAME;

	$q = "SELECT 
			$f_field_id, $f_field_name, $f_description, $f_field_order, $f_text_only, $f_screen_id, $f_screen_id, $f_screen_name
		  FROM 
			$field_tbl, $screen_tbl
		  WHERE
			$f_fscreen_id = $f_screen_id
		  ORDER BY 
			$order_by $order_dir";
	
	$rs = db_query( $db, $q );
	
	return db_fetch_array( $db, $rs );
		

}
*/

# ----------------------------------------------------------------------
# Create where clause for tests and run query to extract test data
# OUTPUT:
#   array of test records.
# ----------------------------------------------------------------------
function test_filter_fields( $filter_screen,
							 $filter_search,
							 $order_by,
							 $order_dir,
							 $page_number,
							 $per_page,
							 $csv_name ) {


	$where_clause = test_field_filter_generate_where_clause( $filter_screen, $filter_search );
    $row = test_field_apply_filter( $where_clause, $per_page, $order_by, $order_dir, $page_number, $csv_name);

    return $row;

}

# ----------------------------------------------------------------------
# Create where clause for fields
# OUTPUT:
#   the WHERE clause of the sql statement
# ----------------------------------------------------------------------
function test_field_filter_generate_where_clause( $filter_screen, $filter_search ) {

	$filter_tbl			= FIELD_TBL;
	$f_screen_id		= FIELD_TBL .".". FIELD_SCREEN_ID;
	$f_field_name		= FIELD_TBL .".". FIELD_NAME;


    $where_clause = '';

    # FIELD_NAME
    if ( !empty($filter_screen)  && $filter_screen != 'all') {

        $where_clause = $where_clause." AND $f_screen_id = '$filter_screen'";
    }
	# SEARCH
	if ( !empty($filter_search) ) {

		$where_clause = $where_clause." AND  ($f_field_name  LIKE '%$filter_search%')";
    }

    return $where_clause;
}

# ----------------------------------------------------------------------
# Run the query to get the fields
# OUTPUT:
#   array of field records.
# ----------------------------------------------------------------------
function test_field_apply_filter( $where_clause, $per_page, $order_by, $order_dir, $page_number, $csv_name) {

	global $db;
	$field_tbl		= FIELD_TBL;
	$f_field_id		= FIELD_TBL .".". FIELD_ID;
	$f_field_name	= FIELD_TBL .".". FIELD_NAME;
	$f_fscreen_id	= FIELD_TBL .".". FIELD_SCREEN_ID;
	$f_description	= FIELD_TBL .".". FIELD_DESC;
	$f_field_order	= FIELD_TBL .".". FIELD_ORDER;
	$f_text_only	= FIELD_TBL .".". FIELD_TEXT_ONLY;

	$screen_tbl		= SCREEN_TBL;
	$f_screen_id	= SCREEN_TBL .".". SCREEN_ID;
	$f_screen_name	= SCREEN_TBL .".". SCREEN_NAME;

	$q = "SELECT 
			$f_field_id, $f_field_name, $f_description, $f_field_order, $f_text_only, $f_screen_id, $f_screen_id, $f_screen_name
		  FROM 
			$field_tbl, $screen_tbl
		  WHERE
			$f_fscreen_id = $f_screen_id";

    $order_clause 	= " ORDER BY $order_by $order_dir";
	$where_clause 	= $where_clause." GROUP BY $f_field_name";
    $q 				.= $where_clause.$order_clause;

	if( $per_page!=0 && $page_number!=0 ) {

		$row_count = db_num_rows( $db, db_query($db, $q) );

		$page_number = util_page_number($page_number, $row_count, $per_page);

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * $per_page );
		html_table_offset( 	$row_count,
							$per_page,
							$page_number,
							$order_by,
							$order_dir,
							$csv_name );

		$q .= " LIMIT $offset, ".$per_page;

	}

	//print"$q<br>";
	$rs = db_query($db, $q);
	return db_fetch_array($db, $rs);
}

# ----------------------------------------------------------------------
# Send an email with the url to the test and test details
# INPUT:
#	project_id - the project_id that the test belongs to
#	test_id	- the test id 
#	recipients - an array of users email addresses
#	action - used to determine the subject line of the email
#   
# ----------------------------------------------------------------------
function test_compose_email($project_id, $test_id, $recipients, $action) {

	$display_generic_info 	= true;
	$display_generic_url	= true;

	$generic_url = RTH_URL."login.php?project_id=$project_id&page=test_detail_page.php&test_id=$test_id&project_id=$project_id";

	$username			= session_get_username();
	$project_name		= session_get_project_name();

	$user_details		= user_get_name_by_username($username);
	$first_name			= $user_details[USER_FNAME];
	$last_name			= $user_details[USER_LNAME];

	$test_detail		= test_get_detail( $test_id );
	$test_name      	= $test_detail[TEST_NAME];
	$status				= $test_detail[TEST_STATUS];
	$priority			= $test_detail[TEST_PRIORITY];
	$test_area			= $test_detail[TEST_AREA_TESTED];
	$test_type			= $test_detail[TEST_TESTTYPE];
	$ba_owner			= $test_detail[TEST_BA_OWNER];
	$qa_owner			= $test_detail[TEST_QA_OWNER];
	$assigned_to		= $test_detail[TEST_ASSIGNED_TO];
	$comments			= $test_detail[TEST_COMMENTS];

	# CREATE EMAIL SUBJECT AND MESSAGE
	switch($action) {
	case"status_change":

		$subject = "RTH: $test_name - Test Status Change";
		$message = "The test status of $test_name has been updated by $first_name $last_name\r\n". NEWLINE;
		break;

	case"steps_uploaded":

		$subject = "RTH: $test_name - Test Steps Added";
		$message = "Test Steps have been uploaded to $test_name by $first_name $last_name\r\n". NEWLINE;
		break;
	}

	# Generic link to results page if the $generic_url variable has been set
	if( $display_generic_url ) {
		$message .= "Click the following link to view the Test Results:". NEWLINE;
		$message .= "$generic_url\n". NEWLINE;
	}

	if( $display_generic_info ) {

		$message .= "Project Name: $project_name\r". NEWLINE;
		$message .= "Test Name: $test_name\r". NEWLINE;
		$message .= "Status: $status\r". NEWLINE;
		$message .= "Priority: $priority\r\n\r". NEWLINE;
		$message .= "Test Area: $test_name\r". NEWLINE;
		$message .= "Test Type: $test_area\r". NEWLINE;
		$message .= "BA Owner: $ba_owner\r". NEWLINE;
		$message .= "QA Owner: $qa_owner\r". NEWLINE;
		$message .= "Assigned To: $assigned_to\r". NEWLINE;
		$message .= "Comments: $comments\r\n\r". NEWLINE;
	}

	email_send($recipients, $subject, $message);
}

function test_copy_test_steps($from_test_id, $to_test_id)
{
	$steps = test_get_test_steps_for_export( $from_test_id );
	$first = true;
	$location = 0;
	foreach($steps as $value)
	{
		test_add_test_step( $to_test_id, $location, $value[TEST_STEP_ACTION], $value[TEST_STEP_EXPECTED], $value[TEST_STEP_TEST_INPUTS], $value[TEST_STEP_INFO_STEP], 'test_detail_copy_page.php' );
		if($first)
		{
			$location = 'end';
			$first = false;
		}
	}
}

function test_get_projectid($testid){
	global $db;
	$f_project_id 	= TEST_PROJ_ID;
	$f_test_tbl 	= TEST_TBL;
	$f_test_id 		= TEST_ID;
	
	$error = "SELECT COUNT($f_project_id) FROM $f_test_tbl WHERE $f_test_id = $testid";
	$q = "SELECT DISTINCT $f_project_id FROM $f_test_tbl WHERE $f_test_id = $testid";
	
	$return_id = db_get_one( $db, $q );
	$error_rs = db_get_one( $db, $error );
	
	if($error_rs > 0){
		return $return_id;
	}else
		return 0;
}

#------------------------------------
# $Log: test_api.php,v $
# Revision 1.31  2009/01/27 12:43:41  cryobean
# fixed problem during update of tests
#
# Revision 1.30  2008/08/08 09:30:25  peter_thal
# added direct navigate to testid function above project switch select box
#
# Revision 1.29  2008/08/04 06:55:01  peter_thal
# added sorting function to several tables
#
# Revision 1.28  2008/07/21 07:42:35  peter_thal
# small bug fixes for test_detail_page linking parameter
#
# Revision 1.27  2008/07/18 08:28:25  peter_thal
# disabled displaying SQL query after adding a test
#
# Revision 1.26  2008/07/09 07:13:19  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.25  2008/07/01 11:44:46  peter_thal
# disabled possibility to select,store and filter both options automated and manual in RTH test category
#
# Revision 1.24  2008/03/17 08:51:37  cryobean
# fixed another case bug
#
# Revision 1.23  2008/01/22 09:47:58  cryobean
# added function for copy test feature
#
# Revision 1.22  2007/11/20 09:54:10  cryobean
# comments disapear any more after test creation
#
# Revision 1.21  2007/11/19 10:22:25  cryobean
# special style for supporting doc link
#
# Revision 1.20  2007/03/14 17:45:52  gth2
# removing code that passes varables by reference - gth
#
# Revision 1.19  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.18  2007/02/03 10:28:34  gth2
# no message
#
# Revision 1.17  2007/02/03 10:26:19  gth2
# no message
#
# Revision 1.16  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.15  2006/08/05 22:12:29  gth2
# correcting error with file versions - gth
#
# Revision 1.13  2006/06/24 14:34:15  gth2
# updating changes lost with cvs problem.
#
# Revision 1.12  2006/06/23 03:17:10  gth2
# correcting SQL for test api
#
# Revision 1.11  2006/05/08 15:38:37  gth2
# commenting functions for fields and screens - gth
#
# Revision 1.10  2006/05/03 21:51:43  gth2
# adding screen and field functions - gth
#
# Revision 1.9  2006/04/11 12:11:01  gth2
# create a test version when uploading test steps - gth
#
# Revision 1.8  2006/04/09 18:09:42  gth2
# adding code lost during cvs outage - gth
#
# Revision 1.7  2006/04/09 17:29:59  gth2
# replacing code lost during sourceforge/cvs outage - gth
#
# Revision 1.6  2006/02/24 11:32:48  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.5  2006/02/14 12:38:33  gth2
# removing unnecessary sql in where clause - gth
#
# Revision 1.4  2006/02/09 12:34:26  gth2
# changing db field names for consistency - gth
#
# Revision 1.3  2006/01/16 13:27:48  gth2
# adding excel integration - gth
#
# Revision 1.2  2006/01/06 00:35:33  gth2
# fixed bug with associations - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:13  gth2
# importing initial version - gth
#
# ------------------------------------

?>