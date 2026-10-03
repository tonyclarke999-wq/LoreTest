<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# TestSet API
#
# $RCSfile: testset_api.php,v $ $Revision: 1.12 $
# ------------------------------------


#------------------------------------------------------------------------------------------
# Creates a testset and inserts the name, description and date created.
#
# INPUT:
#	testset name
#	testset description
#	build id
# OUTPUT:
#	new testset id
#------------------------------------------------------------------------------------------
function testset_add( $testset_name, $testset_description, $build_id, $page_name ) {

	global $db;
	$ts_tbl			= TS_TBL;
	$f_ts_id		= TS_ID;
	$f_name 		= TS_NAME;
	$f_desc			= TS_DESCRIPTION;
	$f_date_created	= TS_DATE_CREATED;
	$f_orderby		= TS_ORDERBY;
	$f_archive		= TS_ARCHIVE;
	$f_build_id		= TS_BUILD_ID;

	$date = date_get_short_dt();
	$archive = 'N';

	# query testset table by build and get the max order by
	# add one to the order by when inserting into the testset table
	$q = "SELECT MAX($f_orderby) FROM $ts_tbl WHERE $f_build_id = '$build_id'";
	$order_by = db_get_one( $db, $q );

	if( !isset( $order_by ) || $order_by == '' ) {
		$order_by = '1';
	} else {
		$order_by = $order_by + 1;
	}

	$q = "INSERT INTO $ts_tbl
		 ($f_name, $f_desc, $f_date_created, $f_orderby, $f_archive, $f_build_id)
		 VALUES
		 ('$testset_name', '$testset_description', '$date', '$order_by', '$archive', '$build_id')";

	db_query( $db, $q );

	$q = "SELECT MAX($f_ts_id) FROM $ts_tbl WHERE $f_build_id = '$build_id'";
	$testset_id = db_get_one( $db, $q );
	#######################################################################################################
	#Add entry into the log table for the project

	$build_name = admin_get_build_name( $build_id );
	$deletion = 'N';
	$creation = 'Y';
	$upload = 'N';
	$action = "ADDED TESTSET $testset_name to $build_name";

	log_activity_log( $page_name, $deletion, $creation, $upload, $action );

	#logfile entry end
	#######################################################################################################

	session_set_properties( "release", Array("testset_id"=> $testset_id) );
	return $testset_id;
}

#------------------------------------------------------------------------------------------
# Returns the rows, which are equal with filter rows
#
# INPUT:
#	build name, release name
# OUTPUT:
#	rows matching with filter options
#------------------------------------------------------------------------------------------
function testset_filter_row($project_id, $build_name, $release_name, $per_page, $order_by, $order_dir, $page_number){
	
	$where_clause	=	testset_filter_generate_where_clause($build_name, $release_name);
	
	$row = testset_filter_apply($where_clause, $project_id, $per_page, $order_by, $order_dir, $page_number);
	
	return $row;
}

function testset_filter_apply($where_clause, $project_id, $per_page, $order_by, $order_dir, $page_number){
	
	global $db;
	$tbl_release		  = RELEASE_TBL;
	$tbl_build			  = BUILD_TBL;
	$tbl_testset          = TS_TBL;
	$tbl_tsa			  = TEST_TS_ASSOC_TBL;
	$f_testset_id		  = $tbl_testset.".".TS_ID;
	$f_testset_name		  = $tbl_testset.".".TS_NAME;
	$f_test_ts_id		  = $tbl_tsa.".".TEST_TS_ASSOC_TS_ID;
	$f_testset_build_id   = $tbl_testset.".".TS_BUILD_ID;
	$f_build_name		  = $tbl_build.".".BUILD_NAME;
	$f_build_id           = $tbl_build.".".BUILD_ID;
	$f_build_release_id   = $tbl_build.".".BUILD_REL_ID;
	$f_release_id		  = $tbl_release.".".RELEASE_ID;
	$f_project_id		  = $tbl_release.".".RELEASE_PROJECT_ID	;
	$f_release_name		  = $tbl_release.".".RELEASE_NAME;
	$f_date_created  	  = $tbl_testset.".".TS_DATE_CREATED;
	$csv_name			  = null;
	

	$q = "SELECT  $f_testset_id, $f_date_created, $f_testset_name, $f_build_name, $f_release_name, $f_date_created 		
		FROM $tbl_testset, $tbl_build, $tbl_release
		WHERE $f_testset_build_id  = $f_build_id 
		AND $f_build_release_id = $f_release_id
		AND $f_project_id = $project_id ". $where_clause ."
		GROUP BY $f_testset_id";
		

	$order_clause	=	" ORDER BY $order_by $order_dir";
	
	$q .= $order_clause;
		
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

	return db_fetch_array($db, $rs);
}

#------------------------------------------------------------------------------------------
# Generates the where clause for SQL statement
#
# INPUT:
#	build name, release name
# OUTPUT:
#	where clause
#------------------------------------------------------------------------------------------
function testset_filter_generate_where_clause($build_name, $release_name){
	$f_build_name          = BUILD_TBL. "." .BUILD_NAME;
    $f_release_name        = RELEASE_TBL. "." .RELEASE_NAME;
    
    $where_clause = "";
    if( !empty($build_name)){
    	$where_clause = $where_clause." AND $f_build_name = '$build_name'";
    }
    if( !empty($release_name)){
    	$where_clause = $where_clause." AND $f_release_name = '$release_name'";
    }
    
    return $where_clause;
}



function testset_name_exists( $build_id, $testset_name ) {

	global $db;
	$testset_tbl 	= TS_TBL;
	$f_testset_name	= $testset_tbl.".".TS_NAME;
	$f_build_id	= $testset_tbl.".".TS_BUILD_ID;
	

	$q = "SELECT COUNT($f_testset_name)
		  FROM $testset_tbl
		  WHERE $f_testset_name = '$testset_name'
		  AND $f_build_id = '$build_id'";
		  
	
	$result = db_get_one( $db, $q );
	
	if ( 0 == $result) {
	    return false;
	} else {
	    return true;
    }

}

# ---------------------------------------------------------------------------------
# Edits a testset.
#
# INPUT:
#	Array of tests to add
# ---------------------------------------------------------------------------------
function testset_edit( $testset_id, $tests ) {

	$project_id 	= session_get_project_id();

	global $db;
	$testsuite_tbl	= TEST_TBL;
	$f_test_id		= TEST_TBL. "." .TEST_ID;
	$f_project_id	= TEST_TBL. "." .PROJECT_ID;
	$f_test_name    = TEST_TBL. "." .TEST_NAME;
	$f_test_type    = TEST_TBL. "." .TEST_TESTTYPE;
	$f_area_tested  = TEST_TBL. "." .TEST_AREA_TESTED;
	$f_deleted      = TEST_TBL. "." .TEST_DELETED;
	$f_archived     = TEST_TBL. "." .TEST_ARCHIVED;
	$f_test_priority= TEST_TBL. "." .TEST_PRIORITY;
	$f_auto_pass    = TEST_TBL. "." .TEST_AUTO_PASS;

	$ts_assoc_tbl           = TEST_TS_ASSOC_TBL;
	$f_ts_assoc_id          = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ID;
	$f_ts_assoc_ts_id       = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TS_ID;
	$f_ts_assoc_test_id     = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TEST_ID;
	$f_ts_assoc_test_status = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_STATUS;
	$f_ts_assoc_assigned_to = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ASSIGNED_TO;
	$f_ts_assoc_comments    = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_COMMENTS;

	# Need variables for INSERT statement
	$f_ts_id				= TEST_TS_ASSOC_TS_ID;
	$f_tst_id				= TEST_TS_ASSOC_TEST_ID;

	$q = "	SELECT	$f_test_id,
					$f_ts_assoc_id
			FROM $ts_assoc_tbl
			RIGHT JOIN $testsuite_tbl ON $f_ts_assoc_test_id = $f_test_id
			WHERE $f_project_id = $project_id
			AND $f_archived = 'N'
			AND $f_deleted = 'N'
			GROUP BY $f_test_id";

	$rs = db_query( $db, $q);

	while( $row = db_fetch_row($db, $rs) ) {

		if( isset( $tests[$row[TEST_ID]] ) ) {
			# If the test is set, then form an association between Test/TestSet

			# Check for associations between TestSet and the Test
			$query_check = "
				SELECT $f_ts_assoc_test_id
				FROM $ts_assoc_tbl
				WHERE $f_ts_assoc_ts_id = $testset_id
					AND $f_ts_assoc_test_id = " . $row[TEST_ID];

			$num_check	= db_num_rows( $db, db_query($db, $query_check) );

			if($num_check == 0) {
				$query_Assoc = "
					INSERT INTO	$ts_assoc_tbl
						($f_ts_id, $f_tst_id )
					VALUES
						($testset_id, ". $row[TEST_ID] .")";
				db_query($db, $query_Assoc);
			}

		} else {

			# Check for associations between TestSet and the Test
			$query_check = "
				SELECT $f_ts_assoc_test_id
				FROM $ts_assoc_tbl
				WHERE $f_ts_assoc_ts_id = $testset_id
					AND $f_ts_assoc_test_id = " . $row[TEST_ID];

			$num_check	= db_num_rows( $db, db_query($db, $query_check) );

			if($num_check != 0) {
				$query_Assoc = "
					DELETE FROM $ts_assoc_tbl
					WHERE $f_tst_id = $row[TestID]
					AND  $f_ts_id = $testset_id";
				db_query($db, $query_Assoc);
			}
		}
	}
}

# ---------------------------------------------------------------------------
# Creates an array of selected tests and passes them to testset_edit.
# ---------------------------------------------------------------------------
function testset_edit_from_session( $testset_id, $f_status, $property_set ) {

	$checked_tests	= array();
	$project_id 			= session_get_project_id();
	$s_project_properties	= session_get_project_properties();

	global $db;
	$testsuite_tbl	= TEST_TBL;
	$f_test_id		= TEST_TBL. "." .TEST_ID;
	$f_project_id	= TEST_TBL. "." .PROJECT_ID;
	$f_deleted      = TEST_TBL. "." .TEST_DELETED;
	$f_archived     = TEST_TBL. "." .TEST_ARCHIVED;

	$ts_assoc_tbl           = TEST_TS_ASSOC_TBL;
	$f_ts_assoc_id          = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ID;
	$f_ts_assoc_test_id     = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TEST_ID;

	$q = "SELECT $f_test_id,
			$f_ts_assoc_id,
			$f_status
	     FROM $ts_assoc_tbl
	     RIGHT JOIN $testsuite_tbl ON $f_ts_assoc_test_id = $f_test_id
	     WHERE $f_project_id = $project_id
	     AND $f_archived = 'N'
	     AND $f_deleted = 'N'
	     GROUP BY $f_test_id";
//print$q;exit;
	$rs = db_query($db, $q);

	while( $row = db_fetch_row($db, $rs) ) {

//		if( isset($row[$f_status]) ) {

			# If the checkbox is ticked, then form an association between Test/TestSet
			if( session_records_ischecked($property_set, $row[TEST_ID], $row[$f_status]) ) {

				$checked_tests[$row[TEST_ID]] = "on";
			}
//		}
	}

	testset_edit( $testset_id, $checked_tests );
}

# ---------------------------------------------------------------------------
# Creates an array of selected tests and passes them to testset_edit.
# ---------------------------------------------------------------------------
function testset_add_tests_from_session( $testset_properties, $f_status, $property_set ) {

	$checked_tests			= array();
	$project_id 			= session_get_project_id();
	$s_user_properties		= session_get_user_properties();
	$s_project_properties	= session_get_project_properties();

	$testset_id				= $testset_properties['testset_id'];
	$testset_name			= admin_get_testset_name($testset_id);
	$build_id				= $testset_properties['build_id'];
	$build_name				= admin_get_build_name($build_id);
	$release_id				= $testset_properties['release_id'];
	$release_name			= admin_get_release_name($release_id);

	global $db;
	$testsuite_tbl	= TEST_TBL;
	$f_test_id		= TEST_TBL. "." .TEST_ID;
	$f_project_id	= TEST_TBL. "." .PROJECT_ID;
	$f_deleted      = TEST_TBL. "." .TEST_DELETED;
	$f_archived     = TEST_TBL. "." .TEST_ARCHIVED;

	$ts_assoc_tbl           = TEST_TS_ASSOC_TBL;
	$f_ts_assoc_id          = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ID;
	$f_ts_assoc_test_id     = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TEST_ID;

	$q = "SELECT $f_test_id,
			$f_ts_assoc_id,
			$f_status
	     FROM $ts_assoc_tbl
	     RIGHT JOIN $testsuite_tbl ON $f_ts_assoc_test_id = $f_test_id
	     WHERE $f_project_id = $project_id
	     AND $f_archived = 'N'
	     AND $f_deleted = 'N'
	     GROUP BY $f_test_id";

	$rs = db_query($db, $q);

	while( $row = db_fetch_row($db, $rs) ) {
		if( isset($row[$f_status]) ) {

			# If the checkbox is ticked, then form an association between Test/TestSet
			if( session_records_ischecked($property_set, $row[TEST_ID], $row[$f_status]) ) {

				$checked_tests[$row[TEST_ID]] = "on";
			}
		}
	}

	testset_edit( $testset_id, $checked_tests );

}

# ----------------------------------------------------------------------
# Get testset details for a specific build or testset
# INPUT:
#   BuildID
# OUTPUT:
#   returns an array with all the testsets associcated to a build
# ----------------------------------------------------------------------
function testset_get_details_by_build($build_id, $testset_id, $order_by=null, $order_dir=null) {

	global $db;
	$ts_tbl				= TS_TBL;
	$ts_id				= TS_ID;
	$ts_name 			= TS_NAME;
	$ts_status			= TS_STATUS;
	$ts_desc			= TS_DESCRIPTION;
	$ts_build_id		= TS_BUILD_ID;
	$ts_orderby			= TS_ORDERBY;
	$ts_archive			= TS_ARCHIVE;
	$ts_date_created	= TS_DATE_CREATED;
	$ts_signoff_date	= TS_SIGNOFF_DATE;
	$ts_signoff_by		= TS_SIGNOFF_BY;
	$ts_signoff_comment	= TS_SIGNOFF_COMMENTS;

	$q = "SELECT
		$ts_id,
		$ts_name,
		$ts_status,
		$ts_desc,
		$ts_build_id,
		$ts_orderby,
		$ts_archive,
		$ts_date_created,
		$ts_signoff_date,
		$ts_signoff_by,
		$ts_signoff_comment
		FROM $ts_tbl
		WHERE $ts_build_id = '$build_id'";

	if( $testset_id != null ) {
		$q .= " AND $ts_id = $testset_id";
	}

	if ( $order_by != null && $order_dir != null ) {
		$q .= " ORDER BY $order_by $order_dir";
	}

	$rs = &db_query( $db, $q );

	if( $testset_id != null ) {

		$row = db_fetch_row( $db, $rs );
		return $row;
	} else {

		$rows = db_fetch_array( $db, $rs );
		return $rows;
	}
}
# ----------------------------------------------------------------------
# Get testset details for a specific testset
# INPUT:
#   TestSetID and BuildID
# OUTPUT:
#   Corresponding testset information
# ----------------------------------------------------------------------
function testset_get( $testset_id, $build_id ) {

	global $db;

	$ts_tbl				= TS_TBL;
	$ts_id				= TS_ID;
	$ts_name 			= TS_NAME;
	$ts_status			= TS_STATUS;
	$ts_desc			= TS_DESCRIPTION;
	$ts_build_id		= TS_BUILD_ID;
	$ts_orderby			= TS_ORDERBY;
	$ts_archive			= TS_ARCHIVE;
	$ts_date_created	= TS_DATE_CREATED;
	$ts_signoff_date	= TS_SIGNOFF_DATE;
	$ts_signoff_by		= TS_SIGNOFF_BY;
	$ts_signoff_comment	= TS_SIGNOFF_COMMENTS;

	$q = " SELECT 	$ts_id,
					$ts_name,
					$ts_status,
					$ts_desc,
					$ts_build_id,
					$ts_orderby,
					$ts_archive,
					$ts_date_created,
					$ts_signoff_date,
					$ts_signoff_by,
					$ts_signoff_comment
			FROM	$ts_tbl
			WHERE	$ts_id = '$testset_id'
				AND $ts_build_id = '$build_id'";


	$rs = &db_query( $db, $q );
	$row = db_fetch_row( $db, $rs ) ;

    return $row;
}

# ----------------------------------------------------------------------
# Get testset name for a specific testset_id
# INPUT:
#   TestSetID 
# OUTPUT:
#   Corresponding testset name
# ----------------------------------------------------------------------
function testset_get_name( $testset_id ) {

	global $db;

	$ts_tbl				= TS_TBL;
	$ts_id				= TS_ID;
	$ts_name 			= TS_NAME;

	$q = "SELECT $ts_name
		  FROM $ts_tbl
		  WHERE	$ts_id = '$testset_id'";


	$testset_name = db_get_one( $db, $q );

    return $testset_name;
}

# ----------------------------------------------------------------------
# Get test details from the testset_test_assoc table for a test in a testset
# INPUT:
#   TestSetID and TestID
# OUTPUT:
#   Corresponding test and testset information
# ----------------------------------------------------------------------
function testset_query_test_details( $testset_id, $test_id ) {

	global $db;
	$ts_tbl			= TS_TBL;
	$f_ts_id		= $ts_tbl .".". TS_ID;

	$test_tbl		= TEST_TBL;
	$f_test_id		= $test_tbl .".". TEST_ID;
	$f_test_name	= $test_tbl .".". TEST_NAME;

	$assoc_tbl			= TEST_TS_ASSOC_TBL;
	$f_assoc_id			= $assoc_tbl .".". TEST_TS_ASSOC_ID;
	$f_assoc_ts_id		= $assoc_tbl .".". TEST_TS_ASSOC_TS_ID;
	$f_assoc_test_id	= $assoc_tbl .".". TEST_TS_ASSOC_TEST_ID;
	$f_status			= $assoc_tbl .".". TEST_TS_ASSOC_STATUS;
	$f_root_cause		= $assoc_tbl .".". TEST_TS_ASSOC_ROOT_CAUSE;
	$f_finished			= $assoc_tbl .".". TEST_TS_ASSOC_FINISHED;
	$f_assigned_to		= $assoc_tbl .".". TEST_TS_ASSOC_ASSIGNED_TO;
	$f_comments			= $assoc_tbl .".". TEST_TS_ASSOC_COMMENTS;

	$q = "SELECT
		$f_test_id,
		$f_ts_id,
		$f_assoc_id,
		$f_test_name,
		$f_status,
		$f_root_cause,
		$f_finished,
		$f_assigned_to,
		$f_comments
	     FROM $ts_tbl, $test_tbl, $assoc_tbl
	     WHERE $f_assoc_ts_id = $f_ts_id
	     AND $f_assoc_test_id = $f_test_id
	     AND $f_ts_id = '$testset_id'
	     AND $f_test_id = '$test_id'";

	$rs = &db_query( $db, $q );
	$row = db_fetch_row( $db, $rs ) ;

    return $row;

}


# ----------------------------------------------------------------------
# Get testset details
# INPUT:
#   TestSetID and BuildID
# OUTPUT:
#   Corresponding testset information
# ----------------------------------------------------------------------
function testset_get_status() {

	/*
	global $db;
	$ts_status		= TESTSET_STATUS_TBL;
	$status_id		= TS_STATUS_ID;
	$status_name 	= TS_STATUS_NAME;

	$q = "SELECT $status_id, $status_name FROM $ts_status";

	$rs = &db_query( $db, $q );
	$num = db_num_rows( $db, $rs );

	$row = array();

	for ( $i=0 ; $i < $num ; $i++ ) {
		array_push( $row, db_fetch_row( $db, $rs ) );
    }

    return $row;
	*/
	$status = array('Accepted',
					'Rejected',
					'Under Review',
					'');

    return $status;

}

	
# ----------------------------------------------------------------------
# Get last 5 created testsets, on condition that they have tests added.
# Input: project id
# Output: array of testsetsd limited to 5 
# ----------------------------------------------------------------------
function testset_get_last_5($project_id) {

    global $db;

	$tbl_release		  = RELEASE_TBL;
	$tbl_build			  = BUILD_TBL;
	$tbl_testset          = TS_TBL;
	$tbl_tsa			  = TEST_TS_ASSOC_TBL;
	$f_testset_id		  = $tbl_testset.".".TS_ID;
	$f_test_ts_id		  = $tbl_tsa.".".TEST_TS_ASSOC_TS_ID;
	$f_testset_build_id   = $tbl_testset.".".TS_BUILD_ID;
	$f_build_id           = $tbl_build.".".BUILD_ID;
	$f_build_release_id   = $tbl_build.".".BUILD_REL_ID;
	$f_release_id		  = $tbl_release.".".RELEASE_ID;
	$f_project_id		  = $tbl_release.".".RELEASE_PROJECT_ID	;
	$f_date_created  	  = $tbl_testset.".".TS_DATE_CREATED;
	

	$q = "SELECT  $f_testset_id, $f_date_created		
		FROM $tbl_testset, $tbl_build, $tbl_release
		WHERE $f_testset_build_id  = $f_build_id 
		AND $f_build_release_id = $f_release_id
		AND $f_project_id = $project_id
		GROUP BY $f_testset_id
		ORDER BY $f_date_created DESC
		LIMIT 5";

	$rs = db_query($db, $q);

	return db_fetch_array($db, $rs);

}

# ----------------------------------------------------------------------
# Get all created testsets, on condition that they have tests added.
# Input: project id
# Output: array of testsetsd limited to 5 
# ----------------------------------------------------------------------
function testset_get_last($project_id) {

    global $db;

	$tbl_release		  = RELEASE_TBL;
	$tbl_build			  = BUILD_TBL;
	$tbl_testset          = TS_TBL;
	$tbl_tsa			  = TEST_TS_ASSOC_TBL;
	$f_testset_id		  = $tbl_testset.".".TS_ID;
	$f_test_ts_id		  = $tbl_tsa.".".TEST_TS_ASSOC_TS_ID;
	$f_testset_build_id   = $tbl_testset.".".TS_BUILD_ID;
	$f_build_id           = $tbl_build.".".BUILD_ID;
	$f_build_release_id   = $tbl_build.".".BUILD_REL_ID;
	$f_release_id		  = $tbl_release.".".RELEASE_ID;
	$f_project_id		  = $tbl_release.".".RELEASE_PROJECT_ID	;
	$f_date_created  	  = $tbl_testset.".".TS_DATE_CREATED;
	

	$q = "SELECT  $f_testset_id, $f_date_created		
		FROM $tbl_testset, $tbl_build, $tbl_release
		WHERE $f_testset_build_id  = $f_build_id 
		AND $f_build_release_id = $f_release_id
		AND $f_project_id = $project_id
		GROUP BY $f_testset_id
		ORDER BY $f_date_created DESC";

	$rs = db_query($db, $q);

	return db_fetch_array($db, $rs);

}


# ----------------------------------------------------------------------
# Update TestSet SignOff
# ----------------------------------------------------------------------
function testset_update_testset_signoff ($testset_id, $build_id, $testset_status,
							$signoff_date, $signoff_by, $signoff_comment) {

    global $db;
	$db_testset_tbl 	= TS_TBL;
	$db_testset_id		= TS_ID;
	$db_build_id		= TS_BUILD_ID;
	$db_status			= TS_STATUS;
	$db_signoff_date	= TS_SIGNOFF_DATE;
	$db_signoff_by		= TS_SIGNOFF_BY;
	$db_signoff_comment	= TS_SIGNOFF_COMMENTS;

    $query = "UPDATE $db_testset_tbl
              SET
              $db_status = '$testset_status',
              $db_signoff_date = '$signoff_date',
              $db_signoff_by = '$signoff_by',
              $db_signoff_comment = '$signoff_comment'
              WHERE
              $db_testset_id = '$testset_id'
              AND
              $db_build_id = $build_id";

    db_query( $db, $query );
}

# ----------------------------------------------------------------------
# Get testsets associated to a build
# INPUT:
#   TestSetID and BuildID
# OUTPUT:
#   Corresponding testset information
# ----------------------------------------------------------------------
function testset_get_testset_count_by_build( $release_id, $build_id, $project_id ) {

    $testset_tbl                = TS_TBL;
    $build_tbl					= BUILD_TBL;
    $release_tbl				= RELEASE_TBL;
    $f_build_id				    = BUILD_TBL .".". BUILD_ID;
    $f_release_id			   	= RELEASE_TBL .".". RELEASE_ID;
    $f_testset_id               = TS_TBL .".". TS_ID;
    $f_testset_name             = TS_TBL .".". TS_NAME;
    $f_testset_date_created     = TS_TBL .".". TS_DATE_CREATED;
    $f_testset_desc             = TS_TBL .".". TS_DESCRIPTION;
    $f_testset_status           = TS_TBL .".". TS_STATUS;
    $f_testset_signoff_by       = TS_TBL .".". TS_SIGNOFF_BY;
    $f_testset_signoff_date     = TS_TBL .".". TS_SIGNOFF_DATE;
    $f_testset_comments         = TS_TBL .".". TS_SIGNOFF_COMMENTS;
    $f_testset_orderby          = TS_TBL .".". TS_ORDERBY;
    $f_testset_build_id         = TS_TBL .".". TS_BUILD_ID;



    $q = "	SELECT
    			$f_testset_id,
    			$f_testset_build_id,
    			$f_testset_name,
    			$f_testset_date_created,
    			$f_testset_desc,
    			$f_testset_status,
    			$f_testset_signoff_by,
    			$f_testset_signoff_date,
    			$f_testset_comments,
    			$f_testset_orderby
    		FROM $testset_tbl
    		WHERE $f_testset_build_id = '$_GET[build_id]'
    		ORDER BY $f_testset_orderby ASC";

    $num = db_num_rows( $db, db_query($db, $q) );
    print"$q";

}

# ----------------------------------------------------------------------
# Get verification information associated to a test run
# INPUT:
#   Test Run ID and Verification ID
# OUTPUT:
#   Corresponding verification information
# ----------------------------------------------------------------------
function testset_query_verfication_details( $test_run_id, $verification_id ) {

	global $db;
	$verify_tbl		= VERIFY_RESULTS_TBL;
	$f_verify_id	= VERIFY_RESULTS_ID;
	$f_ts_id		= VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
	$f_action		= VERIFY_RESULTS_ACTION;
	$f_expected		= VERIFY_RESULTS_EXPECTED_RESULT;
	$f_actual		= VERIFY_RESULTS_ACTUAL_RESULT;
	$f_status		= VERIFY_RESULTS_TEST_STATUS;
	$f_comment		= VERIFY_RESULTS_COMMENT;
	$f_defect_id	= VERIFY_RESULTS_DEFECT_ID;

	$q = "SELECT
		$f_action,
		$f_expected,
		$f_actual,
		$f_status,
		$f_comment,
		$f_defect_id
	      FROM $verify_tbl
	      WHERE $f_ts_id = '$test_run_id'
	      AND $f_verify_id = '$verification_id'";
	$rs = db_query( $db, $q );
	$row = db_fetch_row( $db, $rs );

	return $row;

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
function testset_get_test_testset_value($project_id, $testset_id, $field, $blank=false) {

    global $db;
    $test_tbl    		= TEST_TBL;
    $f_test_id			= $test_tbl .".". TEST_ID;
    $f_project_id		= $test_tbl .".". TEST_PROJ_ID;

    $assoc_tbl			= TEST_TS_ASSOC_TBL;
    $f_assoc_testset_id		= $assoc_tbl .".". TEST_TS_ASSOC_TS_ID;
    $f_assoc_test_id		= $assoc_tbl .".". TEST_TS_ASSOC_TEST_ID;
    $arr_value 			= array();

    $q = "SELECT DISTINCT($field)
          FROM $test_tbl, $assoc_tbl
          WHERE $f_assoc_test_id = $f_test_id
          AND $f_project_id = '$project_id'
          AND $f_assoc_testset_id = '$testset_id'
          AND $field != ''
          ORDER BY $field ASC";
    //print"$q<br>";

    $rs = & db_query( $db, $q );
    while($row = db_fetch_row( $db, $rs ) ) { ;
	array_push($arr_value, $row[$field]);
    }

    if( $blank == true ) {
    	$arr_value[] = "";
    }

    return $arr_value;

}

# ----------------------------------------------------------------------
# Returns a testset array
#
# INPUT:
#	testset id
# OUTPUT:
#	array of the form TEST_ID => TEST_TESTTYPE
# ----------------------------------------------------------------------
function testset_get_tests_testtype( $testset_id ) {

	global $db;

	$tbl_test		= TEST_TBL;
	$f_test_id		= TEST_TBL. "." .TEST_ID;
	$f_test_type    = TEST_TBL. "." .TEST_TESTTYPE;
	$f_deleted      = TEST_TBL. "." .TEST_DELETED;
	$f_archive      = TEST_TBL. "." .TEST_ARCHIVED;
	$f_status		= TEST_TBL. "." .TEST_STATUS;

	$ts_assoc_tbl           = TEST_TS_ASSOC_TBL;
	$f_ts_assoc_ts_id       = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TS_ID;
	$f_ts_assoc_test_id     = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TEST_ID;

	$q = "	SELECT
				$f_test_id,
				$f_test_type
			FROM $tbl_test
			INNER JOIN $ts_assoc_tbl ON $f_ts_assoc_test_id = $f_test_id
			WHERE $f_deleted = 'N'
				AND $f_archive = 'N'
				AND $f_ts_assoc_ts_id = $testset_id";

	$rs = db_query( $db, $q );

	$rows = array();
	while( $fields = db_fetch_row($db, $rs) ) {

		$rows[$fields[TEST_ID]] = $fields[TEST_TESTTYPE];
	}

	return $rows;
}

function testset_get_tests( $testset_id ) {

	global $db;

	$tbl_test		= TEST_TBL;
	$f_test_id		= TEST_TBL. "." .TEST_ID;
	$f_test_area	= TEST_TBL. "." .TEST_AREA_TESTED;
	$f_test_type    = TEST_TBL. "." .TEST_TESTTYPE;
	$f_deleted      = TEST_TBL. "." .TEST_DELETED;
	$f_archive      = TEST_TBL. "." .TEST_ARCHIVED;
	$f_status		= TEST_TBL. "." .TEST_STATUS;

	$ts_assoc_tbl           = TEST_TS_ASSOC_TBL;
	$f_ts_assoc_ts_id       = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TS_ID;
	$f_ts_assoc_test_id     = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TEST_ID;

	$q = "	SELECT 	$f_test_id,
					$f_test_area,
					$f_test_type
			FROM 	$tbl_test
			LEFT JOIN $ts_assoc_tbl ON $f_ts_assoc_test_id = $f_test_id
			WHERE $f_ts_assoc_ts_id = $testset_id";

	$rows = db_fetch_array( $db, db_query($db, $q) );

	return $rows;
}

# ----------------------------------------------------------------------
# Returns the number of tests in a testset
#
# INPUT:
#	testset id
# OUTPUT:
#	number of tests in testset
# ----------------------------------------------------------------------
function testset_number_of_tests( $testset_id ) {

	global $db;

	$tbl_ts_ts_assoc	= TEST_TS_ASSOC_TBL;
	$f_ts_testset_id	= $tbl_ts_ts_assoc.".".TEST_TS_ASSOC_TS_ID;
	$f_ts_test_id		= $tbl_ts_ts_assoc.".".TEST_TS_ASSOC_TEST_ID;

	$tbl_test			= TEST_TBL;
	$f_test_id			= TEST_TBL.".".TEST_ID;
	$f_test_deleted		= TEST_TBL.".".TEST_DELETED;
	$f_test_archive		= TEST_TBL.".".TEST_ARCHIVED;

	$q = "	SELECT $f_ts_test_id
			FROM $tbl_ts_ts_assoc
			INNER JOIN $tbl_test ON
				$f_test_id = $f_ts_test_id
			WHERE
				$f_test_deleted = 'N'
				AND $f_test_archive = 'N'
				AND $f_ts_testset_id = $testset_id";

	$rs = db_query($db, $q);

	return db_num_rows($db, $rs);
}

# ----------------------------------------------------------------------
# Returns the testplans for a build
#
# INPUT:
#	build id
# OUTPUT:
#	array of test plans
# ----------------------------------------------------------------------
function testset_get_test_plans( $build_id ) {

	global $db;

	$tbl_test_plan			= TEST_PLAN;
	$f_test_plan_id			= TEST_PLAN . "." . TEST_PLAN_ID;
	$f_test_plan_build_id	= TEST_PLAN . "." . TEST_PLAN_BUILDID;
	$f_test_plan_name		= TEST_PLAN . "." . TEST_PLAN_NAME;

	$tbl_test_plan_version				= TEST_PLAN_VERSION;
	$f_test_plan_version_id				= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_ID;
	$f_test_plan_version_test_plan_id	= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_TESTPLANID;
	$f_version							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_VERSION;
	$f_uploaded_date					= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDDATE;
	$f_uploaded_by						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDBY;
	$f_file_name						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_FILENAME;
	$f_comments							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_COMMMENTS;
	$f_latest							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_LATEST;

	$q = "	SELECT	$f_test_plan_id,
					$f_test_plan_build_id,
					$f_test_plan_name,
					$f_test_plan_version_id,
					$f_version,
					$f_uploaded_date,
					$f_uploaded_by,
					$f_file_name,
					$f_comments
			FROM $tbl_test_plan
			INNER JOIN $tbl_test_plan_version ON $f_test_plan_id = $f_test_plan_version_test_plan_id
			WHERE $f_test_plan_build_id = $build_id
				AND $f_latest = 'Y'
			GROUP BY $f_test_plan_id
			ORDER BY $f_version DESC";

	$rs = db_query($db, $q);

	return db_fetch_array($db, $rs);
}

# ----------------------------------------------------------------------
# Returns all the versions of a test plan
#
# INPUT:
#	test plan id
# OUTPUT:
#	array of all test plan versions
# ----------------------------------------------------------------------
function testset_get_test_plan_log( $test_plan_id ) {

	global $db;

	$tbl_test_plan			= TEST_PLAN;
	$f_test_plan_id			= TEST_PLAN . "." . TEST_PLAN_ID;
	$f_test_plan_build_id	= TEST_PLAN . "." . TEST_PLAN_BUILDID;
	$f_test_plan_name		= TEST_PLAN . "." . TEST_PLAN_NAME;

	$tbl_test_plan_version				= TEST_PLAN_VERSION;
	$f_test_plan_version_id				= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_ID;
	$f_test_plan_version_test_plan_id	= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_TESTPLANID;
	$f_version							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_VERSION;
	$f_uploaded_date					= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDDATE;
	$f_uploaded_by						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDBY;
	$f_file_name						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_FILENAME;
	$f_comments							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_COMMMENTS;

	$q = "	SELECT	$f_test_plan_id,
					$f_test_plan_build_id,
					$f_test_plan_name,
					$f_test_plan_version_id,
					$f_version,
					$f_uploaded_date,
					$f_uploaded_by,
					$f_file_name,
					$f_comments
			FROM $tbl_test_plan
			INNER JOIN $tbl_test_plan_version ON $f_test_plan_id = $f_test_plan_version_test_plan_id
			WHERE $f_test_plan_id = $test_plan_id
			ORDER BY $f_version DESC";

	$rs = db_query($db, $q);

	return db_fetch_array($db, $rs);
}

# ----------------------------------------------------------------------
# Returns a test plan
#
# INPUT:
#	test plan id
# OUTPUT:
#	array of test plan fields
# ----------------------------------------------------------------------
function testset_get_test_plan_details( $test_plan_id ) {

	global $db;

	$tbl_test_plan			= TEST_PLAN;
	$f_test_plan_id			= TEST_PLAN . "." . TEST_PLAN_ID;
	$f_test_plan_build_id	= TEST_PLAN . "." . TEST_PLAN_BUILDID;
	$f_test_plan_name		= TEST_PLAN . "." . TEST_PLAN_NAME;

	$tbl_test_plan_version				= TEST_PLAN_VERSION;
	$f_test_plan_version_id				= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_ID;
	$f_test_plan_version_test_plan_id	= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_TESTPLANID;
	$f_version							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_VERSION;
	$f_uploaded_date					= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDDATE;
	$f_uploaded_by						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDBY;
	$f_file_name						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_FILENAME;
	$f_comments							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_COMMMENTS;
	$f_latest							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_LATEST;

	$q = "SELECT
			  $f_test_plan_id,
			  $f_test_plan_build_id,
			  $f_test_plan_name,
			  $f_test_plan_version_id,
			  $f_version,
			  $f_file_name
		  FROM $tbl_test_plan, $tbl_test_plan_version
		  WHERE $f_test_plan_id = $f_test_plan_version_test_plan_id
			  AND $f_test_plan_id = $test_plan_id
			  AND $f_latest = 'Y'";

	$rs = db_query($db, $q);

	return db_fetch_row($db, $rs);
}

# ----------------------------------------------------------------------
# Returns array of all test run statuses with optional blank value at end.
#
# OUTPUT:
#   array of all unique test statuses.
# ----------------------------------------------------------------------
function testset_get_run_statuses( $testset_id ) {

	$tbl_test_ts_assoc	= TEST_TS_ASSOC_TBL;
	$f_status			= $tbl_test_ts_assoc .".". TEST_TS_ASSOC_STATUS;
	$f_testset_id		= $tbl_test_ts_assoc .".". TEST_TS_ASSOC_TS_ID;

	$q = "	SELECT DISTINCT $f_status
			FROM $tbl_test_ts_assoc
			WHERE
				$f_status!=''
				AND $f_testset_id = $testset_id
			GROUP BY $f_status
			ORDER BY $f_status ASC";

	global $db;

	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

function testset_email($project_id, $release_id, $build_id, $testset_id, $recipients, $action) {

	$display_generic_info 	= true;
	$display_generic_url	= true;

	$generic_url = RTH_URL."login.php?project_id=$project_id&page=results_page.php&release_id=$release_id&build_id=$build_id&testset_id=$testset_id";

	$username				= session_get_username();
	$project_name			= session_get_project_name();
	$release_name			= admin_get_release_name($release_id);
	$build_name				= admin_get_build_name($build_id);

	$user_details			= user_get_name_by_username($username);
	$first_name				= $user_details[USER_FNAME];
	$last_name				= $user_details[USER_LNAME];

	$testset_detail 		= testset_get_details_by_build( $build_id, $testset_id );
	$testset_id				= $testset_detail[TS_ID];
	$testset_name			= $testset_detail[TS_NAME];
	$testset_date_created	= $testset_detail[TS_DATE_CREATED];
	$testset_description	= $testset_detail[TS_DESCRIPTION];


	# CREATE EMAIL SUBJECT AND MESSAGE
	switch($action) {
	case"new_testset":

		$subject = "RTH: New TestSet for $project_name";
		$message = "TestSet $testset_name has been created by $first_name $last_name". NEWLINE . NEWLINE;
		break;
	}

	# Generic link to results page if the $generic_url variable has been set
	if( $display_generic_url ) {
		$message .= "Click the following link to view results:". NEWLINE . NEWLINE;
		$message .= "$generic_url". NEWLINE . NEWLINE;
		$message .= "Please update automated scripts with TESTSETID=$testset_id". NEWLINE . NEWLINE;
	}

	if( $display_generic_info ) {
		$message .= "".lang_get("project_name").": $project_name". NEWLINE;
		$message .= "".lang_get("release").": $release_name". NEWLINE;
		$message .= "".lang_get("build").": $build_name". NEWLINE;
		$message .= "".lang_get("testset_name").": $testset_name". NEWLINE;
		$message .= "".lang_get("description").": $testset_description". NEWLINE;

		$message .= NEWLINE. "If you do not wish to be notified of any new testsets created, please edit your User profile by navigating to the Users link in RTH.";
	}

	# Convert any html entities stored in the DB back to characters.
	$message = util_unhtmlentities($message);

	email_send($recipients, $subject, $message);
}

function testset_delete_test_plan($testset_plan_id) {
	
	global $db;

	$tbl_test_plan			= TEST_PLAN;
	$f_test_plan_id			= TEST_PLAN . "." . TEST_PLAN_ID;
	$f_test_plan_build_id	= TEST_PLAN . "." . TEST_PLAN_BUILDID;
	$f_test_plan_name		= TEST_PLAN . "." . TEST_PLAN_NAME;

	$tbl_test_plan_version				= TEST_PLAN_VERSION;
	$f_test_plan_version_id				= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_ID;
	$f_test_plan_version_test_plan_id	= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_TESTPLANID;
	$f_version							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_VERSION;
	$f_uploaded_date					= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDDATE;
	$f_uploaded_by						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDBY;
	$f_file_name						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_FILENAME;
	$f_comments							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_COMMMENTS;
	
	$project_properties		= session_get_project_properties();
	$test_plan_upload_path = $project_properties['test_plan_upload_path'] ;
	
	
	# Get the filenames of all the test plan versions to delete	
	$q = "SELECT $f_file_name FROM $tbl_test_plan_version
		  WHERE $f_test_plan_version_test_plan_id = $testset_plan_id";
		  
	$rs = db_query( $db, $q );
	
	# delete test plans
	while( $row = db_fetch_row($db, $rs) ) {
		
		$file = $test_plan_upload_path.$row[TEST_PLAN_VERSION_FILENAME];
		unlink($file);
	}
	
	# delete test plan record	
	$q = "DELETE FROM $tbl_test_plan 
		  WHERE $f_test_plan_id = $testset_plan_id";
	
	db_query( $db, $q );
	
	# delete test plan version record
	$q = "DELETE FROM $tbl_test_plan_version
		  WHERE $f_test_plan_version_test_plan_id = $testset_plan_id";
		  
	db_query( $db, $q );
}

#------------------------------------------------------------------------------------------
# Returns the actual status of testset lock
#
# INPUT:
#	testset id
# OUTPUT:
#	true: if testset is locked
#	false: if testset is not locked
#------------------------------------------------------------------------------------------
function testset_get_lock_status( $testset_id ) {
	
	global $db;
	$testset_id_col		= TS_ID;
	$testset_tbl		= TS_TBL;
	$testset_lock		= TS_LOCK;
	
	$q	= "select $testset_lock from $testset_tbl " .
			"where $testset_id_col = $testset_id";
	
	$rs = db_get_one($db,$q);
	
	if($rs == 'N'){
		return false;
	}else
		return true;
}
function testset_update_testset_lock($testset_id, $build_id, $date, $user_name, $comments){
	global $db;
	$db_testset_tbl 	= TS_TBL;
	$db_testset_id		= TS_ID;
	$db_build_id		= TS_BUILD_ID;
	$db_lock_date		= TS_LOCKCHANGE_DATE;
	$db_lock_by			= TS_LOCK_BY;
	$db_lock_comment	= TS_LOCK_COMMENT;
	$db_lock			= TS_LOCK;
	$locked				= testset_get_lock_status($testset_id);
	if($locked){
		$lock = 'N';
	}else{
		$lock = 'Y';
	}

    $query = "UPDATE $db_testset_tbl
              SET
              $db_lock_date = '$date',
              $db_lock_by = '$user_name',
              $db_lock_comment = '$comments',
              $db_lock = '$lock'
              WHERE
              $db_testset_id = '$testset_id'
              AND
              $db_build_id = $build_id";

    db_query( $db, $query );
	
}

# ------------------------------------
# $Log: testset_api.php,v $
# Revision 1.12  2008/08/04 06:55:01  peter_thal
# added sorting function to several tables
#
# Revision 1.11  2008/07/25 09:50:07  peter_thal
# added lock testset feature
# disabled detail column in test result, because functionality is not implemented yet
#
# Revision 1.10  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.9  2007/02/03 10:26:19  gth2
# no message
#
# Revision 1.8  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.7  2006/06/30 00:55:43  gth2
# removing &$db from api files - gth
#
# Revision 1.6  2006/06/24 14:34:15  gth2
# updating changes lost with cvs problem.
#
# Revision 1.5  2006/02/27 17:24:13  gth2
# added autopass and testset duration functionality - gth
#
# Revision 1.4  2006/02/24 11:32:48  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.3  2006/02/09 12:34:26  gth2
# changing db field names for consistency - gth
#
# Revision 1.2  2006/01/08 22:00:25  gth2
# bug fixes.  missing some variables - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:13  gth2
# importing initial version - gth
#
# ------------------------------------
?>