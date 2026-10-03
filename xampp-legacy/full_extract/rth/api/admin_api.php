<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Admin API
#
# $RCSfile: admin_api.php,v $ $Revision: 1.5 $
# ------------------------------------


# ----------------------------------------------------------------------
# Create and run query for displaying release records.
# OUTPUT:
#   array of release records.
# ----------------------------------------------------------------------
function admin_get_releases( $project_id ) {

	# add other fields to query list
	global $db;

	$release_tbl        = RELEASE_TBL;
	$f_release_id       = RELEASE_ID;
	$f_project_id	    = PROJECT_ID;
	$f_release_name     = RELEASE_NAME;
	$f_release_archive  = RELEASE_ARCHIVE;

	$q = "	SELECT
				$f_release_name,
				$f_release_archive,
				$f_release_id
			FROM
			  	$release_tbl
			WHERE
			  	$f_project_id = $project_id
			ORDER BY
				$f_release_id";

	$rs = db_query($db, $q);

	return db_fetch_array($db, $rs);
}

# ----------------------------------------------------------------------
# Create and run query for displaying release records.
# OUTPUT:
#   array of release records.
# ----------------------------------------------------------------------
function admin_get_release_detail($release_id) {

	global $db;
	$release_tbl			= RELEASE_TBL;
	$f_release_id			= RELEASE_ID;
	$f_project_id			= PROJECT_ID;
	$f_release_name			= RELEASE_NAME;
	$f_release_archive		= RELEASE_ARCHIVE;
	$f_release_received		= RELEASE_DATE_RECEIVED;
	$f_qa_signoff			= RELEASE_QA_SIGNOFF;
	$f_ba_signoff			= RELEASE_BA_SIGNOFF;
	$f_qa_signoff_date		= RELEASE_QA_SIGNOFF_DATE;
	$f_ba_signoff_date		= RELEASE_BA_SIGNOFF_DATE;
	$f_qa_signoff_by		= RELEASE_QA_SIGNOFF_BY;
	$f_ba_signoff_by		= RELEASE_BA_SIGNOFF_BY;
	$f_qa_comments			= RELEASE_QA_SIGNOFF_COMMENTS;
	$f_ba_comments			= RELEASE_BA_SIGNOFF_COMMENTS;
	$f_release_desc			= RELEASE_DESCRIPTION;


	$q = "	SELECT
				$f_release_id,
				$f_release_name,
				$f_release_archive,
				$f_release_received,
				$f_qa_signoff,
				$f_ba_signoff,
				$f_qa_signoff_date,
				$f_ba_signoff_date,
				$f_qa_signoff_by,
				$f_ba_signoff_by,
				$f_qa_comments,
				$f_ba_comments,
				$f_release_desc
			FROM
			  	$release_tbl
			WHERE
			  	$f_release_id = $release_id";

	$rs = db_query($db, $q);

	return db_fetch_row($db, $rs);
}

# ----------------------------------------------------------------------
# Update the release signoff status
# INPUT:
#   release_id, status, comments
#	We may want to add qa and ba signoff status at a later date
# OUTPUT:
#   Updates release table
# ----------------------------------------------------------------------
function admin_release_signoff( $release_id, $qa_status, $qa_comments ) {

	global $db;
	$release_tbl			= RELEASE_TBL;
	$f_release_id			= RELEASE_ID;
	$f_qa_status			= RELEASE_QA_SIGNOFF;
	$f_qa_signoff_date		= RELEASE_QA_SIGNOFF_DATE;
	$f_qa_signoff_by		= RELEASE_QA_SIGNOFF_BY;
	$f_qa_comments			= RELEASE_QA_SIGNOFF_COMMENTS;
	/*
	$f_ba_status			= RELEASE_BA_SIGNOFF;
	$f_ba_signoff_date		= RELEASE_BA_SIGNOFF_DATE;
	$f_ba_signoff_by		= RELEASE_BA_SIGNOFF_BY;
	$f_ba_comments			= RELEASE_BA_SIGNOFF_COMMENTS;
	$f_release_desc			= RELEASE_DESCRIPTION;
	*/

	$current_date		  = date_get_short_dt();
	$username			  = session_get_username();


	$q = "UPDATE $release_tbl
	      SET 
			  $f_qa_signoff_date = '$current_date',
			  $f_qa_signoff_by = '$username',
			  $f_qa_status = '$qa_status',
			  $f_qa_comments = '$qa_comments'
		  WHERE
			  $f_release_id = '$release_id'";
	
	db_query( $db, $q );
		   
}

# ----------------------------------------------------------------------
# Get all Build informataion by release_id
# INPUT:
#   Release ID, optional Build ID
# OUTPUT:
#   array of Build Information
# ----------------------------------------------------------------------
function admin_get_builds($release_id, $order_by=BUILD_ID, $order_dir="ASC") {

	global $db;
	$build_tbl 		= BUILD_TBL;
	$f_build_id		= BUILD_ID;
	$f_release_id	= BUILD_REL_ID;
	$f_build_name	= BUILD_NAME;
	$f_date			= BUILD_DATE_REC;
	$f_description	= BUILD_DESCRIPTION;
	$f_archive		= BUILD_ARCHIVE;

	$q = "	SELECT
				$f_build_id,
				$f_build_name,
				$f_archive,
				$f_date,
				$f_description
			FROM $build_tbl
			WHERE $f_release_id = $release_id
				AND $f_archive = 'N'
			ORDER BY $order_by $order_dir";

	$rs = db_query( $db, $q);

	return db_fetch_array($db, $rs);
}

function admin_get_build($project_id, $build_id) {

	global $db;
	$tbl_release			= RELEASE_TBL;
	$f_release_id			= RELEASE_TBL .".". RELEASE_ID;
	$f_release_archive		= RELEASE_TBL .".". RELEASE_ARCHIVE;
	$f_release_project_id	= RELEASE_TBL .".". PROJECT_ID;

	$tbl_build 				= BUILD_TBL;
	$f_build_id				= BUILD_TBL .".". BUILD_ID;
	$f_build_name			= BUILD_TBL .".". BUILD_NAME;
	$f_build_date			= BUILD_TBL .".". BUILD_DATE_REC;
	$f_build_release_id		= BUILD_TBL .".". BUILD_REL_ID;
	$f_build_archive		= BUILD_TBL .".". BUILD_ARCHIVE;
	$f_build_description	= BUILD_TBL .".". BUILD_DESCRIPTION;

	$testset_tbl			= TS_TBL;
	$f_testset_id			= TS_TBL .".". TS_ID;
	$f_testset_build_id		= TS_TBL .".". TS_BUILD_ID;
	$f_testset_archive		= TS_TBL .".". TS_ARCHIVE;

	# th - changing this.
	# passing in a release_id when you know the build_id seems silly
	# not sure why the function was written this way but I'm changing it.
	# hopefully I'm not breaking this elsewhere.
	$q = "SELECT
			$f_build_id,
			$f_build_name,
			$f_build_archive,
			$f_build_date,
			$f_build_description
		FROM $tbl_build
		INNER JOIN $tbl_release ON
			$f_build_release_id = $f_release_id
		WHERE
			$f_release_project_id = $project_id
			AND $f_build_id = $build_id";

	$rs = db_query( $db, $q);
	return db_fetch_row($db, $rs);
}

function admin_count_tests_in_testset( $testset_id ) {

	global $db;

	$tsa_tbl		= TEST_TS_ASSOC_TBL;
	$f_ts_id		= TEST_TS_ASSOC_TS_ID;
	$f_test_id		= TEST_TS_ASSOC_TEST_ID;


	$q = "SELECT COUNT($f_test_id)
		  FROM $tsa_tbl
		  WHERE $f_ts_id = $testset_id";

	$num_tests = db_get_one( $db, $q );

	return $num_tests;
}

# ----------------------------------------------------------------------
# Get all MAX Release ID from the Build table by project
# INPUT:
#   project_id
# OUTPUT:
#   The MAX release id
# ----------------------------------------------------------------------
function admin_get_max_release_id_from_build_tbl( $project_id ) {

	global $db;
	$rel_tbl        = RELEASE_TBL;
	$f_rel_id       = $rel_tbl .".". RELEASE_ID;
	$f_project_id	= $rel_tbl .".". PROJECT_ID;

	$tbl_release			= RELEASE_TBL;
	$f_release_id			= RELEASE_TBL .".". RELEASE_ID;
	$f_release_archive		= RELEASE_TBL .".". RELEASE_ARCHIVE;
	$f_release_project_id	= RELEASE_TBL .".". PROJECT_ID;

/*
	$build_tbl 		= BUILD_TBL;
	$f_build_id		= $build_tbl .".". BUILD_ID;
	$f_release_id	= $build_tbl .".". BUILD_REL_ID;
	$f_archive		= $build_tbl .".". BUILD_ARCHIVE;

	$q = "SELECT MAX( $f_release_id )
		  FROM $rel_tbl, $build_tbl
		  WHERE $f_rel_id = $f_release_id
		  AND $f_project_id = $project_id
		  AND $f_archive = 'N'";
*/

	$q = "	SELECT MAX($f_release_id)
			FROM $tbl_release
			WHERE $f_release_project_id = $project_id";

	//print"$q<br>";
	$release_id = db_get_one( $db, $q );

	return $release_id;



}

# ----------------------------------------------------------------------
# Get all MAX Build ID from the Build table by release
# INPUT:
#   release_id
# OUTPUT:
#   The MAX build id
# ----------------------------------------------------------------------
function admin_get_max_build_id( $release_id ) {

	global $db;
	$build_tbl 		= BUILD_TBL;
	$f_build_id		= BUILD_ID;
	$f_release_id	= BUILD_REL_ID;
	$f_archive		= BUILD_ARCHIVE;

	$q = "SELECT MAX( $f_build_id )
		  FROM $build_tbl
		  WHERE $f_release_id = '$release_id'
		  AND $f_archive = 'N'";
	$build_id = db_get_one( $db, $q );

	return $build_id;
}

function admin_get_max_testset( $build_id ) {

	global $db;

	$ts_tbl			= TS_TBL;
	$f_ts_id		= $ts_tbl .".". TS_ID;
	$f_ts_build_id	= $ts_tbl .".". TS_BUILD_ID;
	$f_archive		= $ts_tbl .".". TS_ARCHIVE;

	$tsa_tbl		= TEST_TS_ASSOC_TBL;
	$f_tsa_id		= $tsa_tbl .".". TEST_TS_ASSOC_ID;
	$f_test_ts_id	= $tsa_tbl .".". TEST_TS_ASSOC_TS_ID;


	$q = "SELECT MAX($f_ts_id)
		  FROM $ts_tbl, $tsa_tbl
		  WHERE $f_test_ts_id = $f_ts_id
		  AND $f_ts_build_id = '$build_id'
		  AND $f_archive = 'N'";
	//print"$q<br>";

	$testset_id = db_get_one( $db, $q );

	return $testset_id;


}

function admin_get_testset($project_id, $testset_id) {

	global $db;

	$tbl_release        	= RELEASE_TBL;
	$f_release_id       	= $tbl_release .".". RELEASE_ID;
	$f_release_project_id	= $tbl_release .".". RELEASE_PROJECT_ID;
	$f_release_archive  	= $tbl_release .".". RELEASE_ARCHIVE;

	$tbl_build 				= BUILD_TBL;
	$f_build_id				= $tbl_build .".". BUILD_ID;
	$f_build_archive		= $tbl_build .".". BUILD_ARCHIVE;
	$f_build_release_id		= $tbl_build .".". BUILD_REL_ID;

	$tbl_testset				= TS_TBL;
	$f_testset_id				= $tbl_testset .".". TS_ID;
	$f_testset_name 			= $tbl_testset .".". TS_NAME;
	$f_testset_status			= $tbl_testset .".". TS_STATUS;
	$f_testset_desc				= $tbl_testset .".". TS_DESCRIPTION;
	$f_testset_build_id			= $tbl_testset .".". TS_BUILD_ID;
	$f_testset_orderby			= $tbl_testset .".". TS_ORDERBY;
	$f_testset_archive			= $tbl_testset .".". TS_ARCHIVE;
	$f_testset_date_created		= $tbl_testset .".". TS_DATE_CREATED;
	$f_testset_signoff_date		= $tbl_testset .".". TS_SIGNOFF_DATE;
	$f_testset_signoff_by		= $tbl_testset .".". TS_SIGNOFF_BY;
	$f_testset_signoff_comment	= $tbl_testset .".". TS_SIGNOFF_COMMENTS;

	$q = "	SELECT
				$f_testset_id,
				$f_testset_name,
				$f_testset_status,
				$f_testset_desc,
				$f_testset_build_id,
				$f_testset_orderby,
				$f_testset_archive,
				$f_testset_date_created,
				$f_testset_signoff_date,
				$f_testset_signoff_by,
				$f_testset_signoff_comment
			FROM $tbl_testset
			INNER JOIN $tbl_build ON
				$f_testset_build_id = $f_build_id
			INNER JOIN $tbl_release ON
				$f_release_id = $f_build_release_id
			WHERE
				$f_release_project_id = $project_id
				AND $f_testset_id = $testset_id";

	$rs = db_query($db, $q);
	$row = db_fetch_row($db, $rs);

	return $row;
}

function admin_get_testsets( $build_id ) {

	global $db;

	$testset_tbl	= TS_TBL;
	$f_id			= TS_ID;
	$f_build_id		= TS_BUILD_ID;
	$f_name			= TS_NAME;
	$f_archive		= TS_ARCHIVE;

	$q = "	SELECT DISTINCT
				$f_name,
				$f_id,
				$f_archive
			FROM
				$testset_tbl
			WHERE
				$f_build_id = '$build_id'";

	$rs = db_query($db, $q);

	return db_fetch_array( $db, $rs );
}

################################################################
# Return the build id when given the testset_id
# This function is used to trace back from a bug -> verification
# -> test_run -> testset -> build -> release
# It is used when navigating from a bug to a test result.
# In this situation, we need to build the session information for
# the results page
# INPUT: testset_id
# OUTPUT build_id
################################################################
function admin_get_build_id_from_testset_id( $testset_id ) {

	global $db;
	$testset_tbl	= TS_TBL;
	$f_id			= TS_ID;
	$f_build_id		= TS_BUILD_ID;

	$q = "SELECT
			$f_build_id
		  FROM
			$testset_tbl
		  WHERE
			$f_id = '$testset_id'";

	$build_id = db_get_one( $db, $q );

	return $build_id;

}

################################################################
# Return the release id when given the build_id
# This function is used to trace back from a bug -> verification
# -> test_run -> testset -> build -> release
# It is used when navigating from a bug to a test result.
# In this situation, we need to build the session information for
# the results page
# INPUT: build_id
# OUTPUT testset_id
################################################################
function admin_get_release_id_from_build_id( $build_id ) {

	global $db;
	$build_tbl 		= BUILD_TBL;
	$f_build_id		= BUILD_ID;
	$f_release_id	= BUILD_REL_ID;

	$q = "SELECT
			$f_release_id
		  FROM
			$build_tbl
		  WHERE
			$f_build_id = '$build_id'";

	$release_id = db_get_one( $db, $q );

	return $release_id;

}


function admin_get_tests( $project_id, $page_number=0, $order_by=TEST_ID, $order_dir="ASC" ) {

	global $db;
	$tbl_test	= TEST_TBL;
	$f_name 	= TEST_NAME;
	$f_type 	= TEST_TESTTYPE;
	$f_priority = TEST_PRIORITY;
	$f_id 		= TEST_ID;
	$f_steps 	= TEST_MANUAL;
	$f_script 	= TEST_AUTOMATED;
	$f_status 	= TEST_STATUS;
	$f_area 	= TEST_AREA_TESTED;
	$f_deleted 	= TEST_DELETED;
	$f_archive 	= TEST_ARCHIVED;
	$f_area_tested 	= TEST_AREA_TESTED;
	$f_project_id = PROJECT_ID;

	$q = "	SELECT
				$f_id,
				$f_name,
				$f_type,
				$f_priority,
				$f_script,
				$f_steps,
				$f_status,
				$f_area_tested,
				$f_archive
		FROM $tbl_test
		ORDER BY $order_by $order_dir";

	if( $page_number!=0 ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_ARCHIVE_TESTS );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_ARCHIVE_TESTS,
							$page_number );

		$q .= " LIMIT $offset, ". RECORDS_PER_PAGE_ARCHIVE_TESTS;

	}

	return db_fetch_array($db, db_query($db, $q) );
}

function admin_get_archived_tests($project_id) {

	global $db;
	$tbl_test		= TEST_TBL;
	$f_id 			= TEST_ID;
	$f_archive 		= TEST_ARCHIVED;
	$f_project_id 	= PROJECT_ID;

	$q = "	SELECT
				$f_id,
				$f_archive
			FROM $tbl_test
			WHERE
				$f_archive = 'Y'";

	$rs = db_query($db, $q);

	$rows = array();
	while( $fields = db_fetch_row($db, $rs) ) {

		$rows[$fields[TEST_ID]] = "";
	}

	return $rows;
}

function admin_get_archived_releases($project_id) {

	global $db;
	$tbl_release        	= RELEASE_TBL;
	$f_release_id       	= RELEASE_ID;
	$f_release_project_id	= RELEASE_PROJECT_ID;
	$f_release_archive  	= RELEASE_ARCHIVE;

	$q = "SELECT
			 $f_release_id
		 FROM $tbl_release
		 WHERE $f_release_project_id = '$project_id'
		 AND $f_release_archive = 'Y'";

	$rs = db_query($db, $q);

	$rows = array();
	while( $fields = db_fetch_row($db, $rs) ) {

		$rows[$fields[RELEASE_ID]] = "";
	}

	return $rows;
}

function admin_get_archived_builds($project_id) {

	global $db;

	$tbl_release        	= RELEASE_TBL;
	$f_release_id       	= $tbl_release .".". RELEASE_ID;
	$f_release_project_id	= $tbl_release .".". RELEASE_PROJECT_ID;
	$f_release_archive  	= $tbl_release .".". RELEASE_ARCHIVE;

	$tbl_build 				= BUILD_TBL;
	$f_build_id				= $tbl_build .".". BUILD_ID;
	$f_build_archive		= $tbl_build .".". BUILD_ARCHIVE;
	$f_build_release_id		= $tbl_build .".". BUILD_REL_ID;

	$rows = array();

	$q = "	SELECT
				$f_build_id
			FROM $tbl_build
			INNER JOIN $tbl_release ON
				$f_release_id = $f_build_release_id
			WHERE
				$f_release_project_id = $project_id
				AND $f_build_archive = 'Y'";

	$rs = db_query($db, $q);

	while( $fields = db_fetch_row($db, $rs) ) {

		$rows[$fields[BUILD_ID]] = "";
	}

	return $rows;
}

function admin_get_archived_testsets($project_id) {

	global $db;

	$tbl_release        	= RELEASE_TBL;
	$f_release_id       	= $tbl_release .".". RELEASE_ID;
	$f_release_project_id	= $tbl_release .".". RELEASE_PROJECT_ID;
	$f_release_archive  	= $tbl_release .".". RELEASE_ARCHIVE;

	$tbl_build 				= BUILD_TBL;
	$f_build_id				= $tbl_build .".". BUILD_ID;
	$f_build_archive		= $tbl_build .".". BUILD_ARCHIVE;
	$f_build_release_id		= $tbl_build .".". BUILD_REL_ID;

	$tbl_testset			= TS_TBL;
	$f_testset_id			= $tbl_testset .".". TS_ID;
	$f_testset_build_id		= $tbl_testset .".". TS_BUILD_ID;
	$f_testset_name			= $tbl_testset .".". TS_NAME;
	$f_testset_archive		= $tbl_testset .".". TS_ARCHIVE;

	$rows = array();

	$q = "	SELECT
				$f_testset_id
			FROM $tbl_testset
			INNER JOIN $tbl_build ON
				$f_testset_build_id = $f_build_id
			INNER JOIN $tbl_release ON
				$f_release_id = $f_build_release_id
			WHERE
				$f_release_project_id = $project_id
				AND $f_testset_archive = 'Y'";

	$rs = db_query($db, $q);

	while( $fields = db_fetch_row($db, $rs) ) {

		$rows[$fields[TS_ID]] = "";
	}

	return $rows;
}

function admin_get_release_array($project_id) {

	global $db;

	$tbl_release        	= RELEASE_TBL;
	$f_release_id       	= $tbl_release .".". RELEASE_ID;
	$f_release_name       	= $tbl_release .".". RELEASE_NAME;
	$f_release_project_id	= $tbl_release .".". RELEASE_PROJECT_ID;
	$f_release_archive  	= $tbl_release .".". RELEASE_ARCHIVE;
	$f_release_date			= $tbl_release .".". RELEASE_DATE_RECEIVED;

	$tbl_build 				= BUILD_TBL;
	$f_build_id				= $tbl_build .".". BUILD_ID;
	$f_build_name			= $tbl_build .".". BUILD_NAME;
	$f_build_archive		= $tbl_build .".". BUILD_ARCHIVE;
	$f_build_release_id		= $tbl_build .".". BUILD_REL_ID;

	$tbl_testset			= TS_TBL;
	$f_testset_id			= $tbl_testset .".". TS_ID;
	$f_testset_build_id		= $tbl_testset .".". TS_BUILD_ID;
	$f_testset_name			= $tbl_testset .".". TS_NAME;
	$f_testset_archive		= $tbl_testset .".". TS_ARCHIVE;

	$q = "SELECT
			 $f_release_id,
			 $f_release_name,
			 $f_release_archive
		 FROM $tbl_release
		 WHERE $f_release_project_id = '$project_id'
		 ORDER BY $f_release_date ASC";

	$rs = db_query($db, $q);
	$releases = db_fetch_array($db, $rs);

	# loop through the releases and add builds to the array
	for($i=0; $i<sizeof($releases); $i++) {

		$q = "	SELECT
					$f_build_id,
					$f_build_name,
					$f_build_archive
				FROM $tbl_build
				INNER JOIN $tbl_release ON
					$f_release_id = $f_build_release_id
				WHERE
					$f_release_project_id = $project_id
					AND $f_build_release_id = ".$releases[$i][RELEASE_ID];

		$rs = db_query($db, $q);
		$builds = db_fetch_array($db, $rs);

		$releases[$i]["builds"] = $builds;

		# loop through the builds and add testsets to the array
		for($j=0; $j<sizeof($builds); $j++) {

			$q = "	SELECT
						$f_testset_id,
						$f_testset_name,
						$f_testset_archive
					FROM $tbl_testset
					INNER JOIN $tbl_build ON
						$f_testset_build_id = $f_build_id
					INNER JOIN $tbl_release ON
						$f_release_id = $f_build_release_id
					WHERE
						$f_testset_build_id = ".$releases[$i]["builds"][$j][BUILD_ID];

			$rs = db_query($db, $q);
			$testsets = db_fetch_array($db, $rs);

			$releases[$i]["builds"][$j]["testsets"] = $testsets;
		}
	}


	return($releases);
}

# ----------------------------------------------------------------------
# Create and run query for displaying release records.
# OUTPUT:
#   array of release records.
# name need to be changed because $release_id=null
# ----------------------------------------------------------------------
function admin_get_all_release_details_by_project( $project_id, $release_id=null, $order_by=null, $order_dir=null ) {

	# add other fields to query list
	global $db;
	$release_tbl        	= RELEASE_TBL;
	$f_release_id       	= RELEASE_ID;
	$f_project_id	    	= RELEASE_PROJECT_ID;
	$f_release_name     	= RELEASE_NAME;
	$f_date_received		= RELEASE_DATE_RECEIVED;
	$f_description			= RELEASE_DESCRIPTION;
	$f_qa_signoff_by		= RELEASE_QA_SIGNOFF_BY;
	$f_qa_signoff_date		= RELEASE_QA_SIGNOFF_DATE;
	$f_qa_signoff_status	= RELEASE_QA_SIGNOFF;
	$f_qa_signoff_comments	= RELEASE_QA_SIGNOFF_COMMENTS;
	$f_ba_signoff_by		= RELEASE_BA_SIGNOFF_BY;
	$f_ba_signoff_date		= RELEASE_BA_SIGNOFF_DATE;
	$f_ba_signoff_status	= RELEASE_BA_SIGNOFF;
	$f_ba_signoff_comments	= RELEASE_BA_SIGNOFF_COMMENTS;
	$f_release_archive  	= RELEASE_ARCHIVE;

	$release				= array();

	$q = "SELECT
			 $f_release_id,
			 $f_release_name,
			 $f_date_received,
			 $f_description,
			 $f_qa_signoff_by,
			 $f_qa_signoff_date,
			 $f_qa_signoff_status,
			 $f_qa_signoff_comments,
			 $f_ba_signoff_by,
			 $f_ba_signoff_date,
			 $f_ba_signoff_status,
			 $f_ba_signoff_comments
		 FROM $release_tbl
		 WHERE $f_project_id = '$project_id'
		 AND $f_release_archive = 'N'";

	if ( $release_id != null ) {
		$q .= " AND $f_release_id = '$release_id'";
	}

	if ( $order_by != null && $order_dir != null ) {
		$q .= " ORDER BY $order_by $order_dir";
	}

	$rs = db_query( $db, $q);

	while($row = db_fetch_row( $db, $rs ) ) {
		array_push($release, $row);
	}

	return $release;
}

# ----------------------------------------------------------------------
# Insert a release into the Release Table
# ----------------------------------------------------------------------
function admin_add_release( $rel_name, $rel_description, $project_id, $page_name ) {

	global $db;
	$release_tbl 		= RELEASE_TBL;
	$f_rel_name			= RELEASE_NAME;
	$f_rel_desc			= RELEASE_DESCRIPTION;
	$f_date				= RELEASE_DATE_RECEIVED;
	$f_project_id		= RELEASE_PROJECT_ID;
	$f_archive			= RELEASE_ARCHIVE;

	$date = date_get_short_dt();
	$archive = 'N';

	$q = "INSERT INTO $release_tbl
		  ($f_rel_name, $f_rel_desc, $f_date, $f_project_id, $f_archive)
		  VALUES
		  ('$rel_name', '$rel_description', '$date', '$project_id', '$archive')";

	db_query( $db, $q);

	#######################################################################################################
	# Add entry into the log table for the project

	$deletion = 'N';
	$creation = 'Y';
	$upload = 'N';
	$action = "ADDED RELEASE $rel_name";

	log_activity_log( $page_name, $deletion, $creation, $upload, $action );

	#logfile entry end
	#######################################################################################################
}


# ----------------------------------------------------------------------
# Check if Release name already exists
# INPUT:
#   Release Name to Check and the Project ID
# OUTPUT:
#   True if Release with Release Name already exists, otherwise false.
# ----------------------------------------------------------------------
function admin_release_name_exists( $project_id, $rel_name ) {

	global $db;
	$release_tbl 		= RELEASE_TBL;
	$f_rel_name			= $release_tbl.".".RELEASE_NAME;
	$f_project_id		= $release_tbl.".".RELEASE_PROJECT_ID;


	$q = "SELECT COUNT($f_rel_name)
		  FROM $release_tbl
		  WHERE $f_rel_name = '$rel_name'
		  AND $f_project_id = '$project_id'";

	$result = db_get_one( $db, $q );


	if ( 0 == $result) {
	    return false;
	} else {
	    return true;
    }

}

# ----------------------------------------------------------------------
# Returns the release name for a given release id
# ----------------------------------------------------------------------
function admin_get_release_name( $release_id ) {

	global $db;
	$release_tbl 		= RELEASE_TBL;
	$f_rel_id			= RELEASE_ID;
	$f_rel_name			= RELEASE_NAME;
	$f_project_id		= RELEASE_PROJECT_ID;

	$q = "SELECT $f_rel_name
		FROM $release_tbl
		WHERE $f_rel_id = '$release_id'";

	$release_name = db_get_one( $db, $q );

	return $release_name;
}


# ----------------------------------------------------------------------
# Returns the release name for a given release id
# ----------------------------------------------------------------------
function admin_get_all_release_names( $project_id, $blank=false ) {

	global $db;
	$release_tbl 		= RELEASE_TBL;
	$f_rel_name			= RELEASE_NAME;
	$f_release_id		= RELEASE_ID;
	$f_project_id		= RELEASE_PROJECT_ID;
	$release_names		= array();

	$q = "SELECT $f_rel_name, $f_release_id
		FROM $release_tbl
		WHERE $f_project_id = '$project_id'";

	$rs = & db_query( $db, $q );

	while($row = db_fetch_row( $db, $rs ) ) { ;
		$release_names[$row[RELEASE_ID]] = $row[RELEASE_NAME];
		//array_push($release_names, $row[RELEASE_NAME]);
	}

	if( $blank ) {
		$release_names[""] = "";
    }

    return $release_names ;

}



# ----------------------------------------------------------------------
# Delete a release and any associated builds, testSets, and test results that might exist
# in the TestSetAssoc, TestResults, and VerifyResults tables.
#
# Not yet deleting Run docs as we may be changing the functionality
#
# INPUT:
#   Release ID
# ----------------------------------------------------------------------
function admin_release_delete( $release_id ) {

	global $db;
	$release_tbl			= RELEASE_TBL;
	$f_release_id			= RELEASE_ID;

	$build_tbl			= BUILD_TBL;
	$f_build_id			= BUILD_ID;
	$f_build_rel_id			= BUILD_REL_ID;

	$q_b = "SELECT $f_build_id FROM $build_tbl WHERE $f_build_rel_id = '$release_id'";
	$rs_b = db_query( $db, $q_b );

	while($row_b = db_fetch_row( $db, $rs_b ) ) {  # while there are records in the build tbl
		admin_build_delete( $row_b[$f_build_id] );
	}

	$q = "DELETE FROM $build_tbl WHERE $f_build_rel_id = '$release_id'";
	db_query( $db, $q );

	$q_rel = "DELETE FROM $release_tbl WHERE $f_release_id = '$release_id'";
	db_query( $db, $q_rel );

}

# ----------------------------------------------------------------------
# Delete a build and any associated testSets and test results that might exist
# in the TestSetAssoc, TestResults, and VerifyResults tables.
#
# Not yet deleting Run docs as we may be changing the functionality
#
# INPUT:
#   Build ID
# ----------------------------------------------------------------------
function admin_build_delete( $build_id ) {

	global $db;

	$build_tbl				= BUILD_TBL;
	$f_build_id				= BUILD_ID;
	$f_build_rel_id			= BUILD_REL_ID;

	$testset_tbl			= TS_TBL;
	$f_testset_id			= TS_ID;
	$f_ts_build_id			= TS_BUILD_ID;

	$q_ts = "SELECT $f_testset_id FROM $testset_tbl WHERE $f_ts_build_id = '$build_id'";
	$rs_ts = db_query( $db, $q_ts );

	while($row_ts = db_fetch_row( $db, $rs_ts ) ) {  # while there are records in the testset tbl
		admin_testset_delete( $row_ts[$f_testset_id] );
	}

	$q = "DELETE FROM $build_tbl WHERE $f_build_id = '$build_id'";
	db_query( $db, $q );
}

# ----------------------------------------------------------------------
# Delete a testSets and any assiciated test results that might exist
# in the TestSetAssoc, TestResults, and VerifyResults tables.
#
# Not yet deleting Run docs as we may be changing the functionality
#
# INPUT:
#   TestSet ID
# ----------------------------------------------------------------------
function admin_testset_delete( $testset_id ) {

	global $db;

	$testset_tbl			= TS_TBL;
	$f_testset_id			= $testset_tbl .".". TS_ID; #TestSet.TestSetID

	$assoc_tbl				= TEST_TS_ASSOC_TBL;
	$f_assoc_id				= $assoc_tbl .".". TEST_TS_ASSOC_TS_ID;

	$test_results_tbl			= TEST_RESULTS_TBL;
	$f_test_results_run_id		= $test_results_tbl .".". TEST_RESULTS_TS_UNIQUE_RUN_ID;
	$f_test_results_testset_id	= $test_results_tbl .".". TEST_RESULTS_TEST_SET_ID;

	$verify_results_tbl				= VERIFY_RESULTS_TBL;
	$f_verify_results_test_run_id	= $verify_results_tbl .".". VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
	$f_verify_results_id			= $verify_results_tbl .".". VERIFY_RESULTS_ID;

	$bug_tbl				= BUG_TBL;
	$f_bug_verify_id		= $bug_tbl .".". BUG_TEST_VERIFY_ID;
	$f_bug_id				= $bug_tbl .".". BUG_ID;

	$q_tr = "SELECT $f_test_results_run_id FROM $test_results_tbl WHERE $f_test_results_testset_id = '$testset_id'";
	$rs_tr = db_query( $db, $q_tr );

	# I think we may be able to speed up the deletion by using a temp table
	while($row_tr = db_fetch_row( $db, $rs_tr ) ) {  # while there are records in the test results tbl

		$testrun_id = $row_tr[TEST_RESULTS_TS_UNIQUE_RUN_ID];

		/*
		$q_vid = "SELECT $f_verify_results_id
				  FROM $verify_results_tbl
				  WHERE $f_verify_results_test_run_id = '$testrun_id'";
		$rs_vr = db_query( $db, $q_vid );
		while( $row_vr = db_fetch_row( $db, $rs_vr ) ) {



		}
		*/

			$q_vr = "SELECT $f_verify_results_id FROM $verify_results_tbl WHERE $f_verify_results_test_run_id = '$testrun_id'";
					db_query( $db, $q_vr);

					$rs = db_query( $db, $q_vr);

						# Bug table is updated,all VerifyResultsId's of bugs recorded in the record set are set to Null
						while( $row = db_fetch_row($db, $rs) ) {


							$verifyResults = $row[VERIFY_RESULTS_ID];

							$q = "UPDATE $bug_tbl
								  SET $f_bug_verify_id =''
								  WHERE $f_bug_verify_id = '$verifyResults'";

							db_query( $db, $q );


						}

		$q_vr = "DELETE FROM $verify_results_tbl WHERE $f_verify_results_test_run_id = '$testrun_id'";
		db_query( $db, $q_vr);
	}

	$q = "DELETE FROM $testset_tbl WHERE $f_testset_id = '$testset_id'";
	db_query( $db, $q );

	$q = "DELETE FROM $assoc_tbl WHERE $f_assoc_id = '$testset_id'";
	db_query( $db, $q );

	$q = "DELETE FROM $test_results_tbl WHERE $f_test_results_testset_id = '$testset_id'";
	db_query( $db, $q );

}


# ----------------------------------------------------------------------
# Get Build Name by ID
# INPUT:
#   Build ID
# OUTPUT:
#   Build Name
# ----------------------------------------------------------------------
function admin_get_build_name( $build_id ) {

	global $db;
	$build_tbl 		= BUILD_TBL;
	$f_build_id		= BUILD_ID;
	$f_build_name		= BUILD_NAME;

	$q = "SELECT $f_build_name
		FROM $build_tbl
		WHERE $f_build_id = '$build_id'";

	$build_name = db_get_one( $db, $q );

	return $build_name;
}

# ----------------------------------------------------------------------
# Get Testset Name by ID
# INPUT:
#   Testset ID
# OUTPUT:
#   Testset Name
# ----------------------------------------------------------------------
function admin_get_testset_name( $testset_id ) {

	global $db;
	$testset_tbl 		= TS_TBL;
	$f_testset_id		= TS_ID;
	$f_testset_name		= TS_NAME;

	$q = "SELECT $f_testset_name
		FROM $testset_tbl
		WHERE $f_testset_id = '$testset_id'";

	return db_get_one( $db, $q );
}

# ----------------------------------------------------------------------
# Insert a build into the Build Table
# ----------------------------------------------------------------------
function admin_add_build( $release_id, $build_name, $build_description, $page_name ) {

	global $db;
	$build_tbl		= BUILD_TBL;
	$f_release_id	= BUILD_REL_ID;
	$f_build_name	= BUILD_NAME;
	$f_description	= BUILD_DESCRIPTION;
	$f_date			= BUILD_DATE_REC;
	$f_archive		= BUILD_ARCHIVE;

	$date = date_get_short_dt();
	$archive = 'N';

	$q = "INSERT INTO $build_tbl
		 ($f_build_name, $f_description, $f_date, $f_release_id, $f_archive)
		 VALUES
		 ('$build_name', '$build_description', '$date', '$release_id', '$archive')";

	db_query( $db, $q);

	#######################################################################################################
	# Add entry into the log table for the project

	$release_name = admin_get_release_name( $release_id );
	$deletion = 'N';
	$creation = 'Y';
	$upload = 'N';
	$action = "ADDED BUILD $build_name TO RELEASE $release_name";

	log_activity_log( $page_name, $deletion, $creation, $upload, $action );

	#logfile entry end
	#######################################################################################################
}

# ----------------------------------------------------------------------
# Check if Build name already exists
# INPUT:
#   Build Name to Check and the Project ID
# OUTPUT:
#   True if Build with Build Name already exists, otherwise false.
# ----------------------------------------------------------------------
function admin_build_name_exists( $release_id, $build_name ) {

	global $db;
	$build_tbl 		= BUILD_TBL;
	$f_build_name	= $build_tbl.".".BUILD_NAME;
	$f_release_id	= $build_tbl.".".BUILD_REL_ID;


	$q = "SELECT COUNT($f_build_name)
		  FROM $build_tbl
		  WHERE $f_build_name = '$build_name'
		  AND $f_release_id = '$release_id'";


	$result = db_get_one( $db, $q );

	if ( 0 == $result) {
	    return false;
	} else {
	    return true;
    }

}


# ----------------------------------------------------------------------
# Edit a testset name, date and description.
# ----------------------------------------------------------------------
function admin_edit_testset( $testset_id, $testset_name, $testset_date, $testset_description ) {

	global $db;
	$ts_tbl		= TS_TBL;
	$f_id		= TS_ID;
	$f_name		= TS_NAME;
	$f_desc		= TS_DESCRIPTION;
	$f_date		= TS_DATE_CREATED;

	# Ensure that there is no ' in the Testset name or description which would cause problems with any queries
	$testset_name = str_replace( "\'", "", "$testset_name");
	$testset_description = str_replace( "\'", "", "$testset_description");

	$q = "UPDATE $ts_tbl
		SET
			$f_name = '$testset_name',
			$f_desc = '$testset_description',
			$f_date = '$testset_date'
		WHERE $f_id = $testset_id";

	db_query( $db, $q);
}

# ----------------------------------------------------------------------
# Edit a build name, date and description.
# ----------------------------------------------------------------------
function admin_edit_build( $build_id, $build_name, $build_date, $build_description ) {
	global $db;
	$f_build_id		= BUILD_ID;
	$build_tbl 		= BUILD_TBL;
	$f_release_id	= BUILD_REL_ID;
	$f_build_name 	= BUILD_NAME;
	$f_description 	= BUILD_DESCRIPTION;
	$f_date			= BUILD_DATE_REC;

	# Ensure that there is no ' in the Build Name or description which would cause problems with any queries
	$build_name = str_replace( "\'", "", "$build_name");
	$build_description = str_replace( "\'", "", "$build_description");

	$q = "UPDATE $build_tbl
		SET
			$f_build_name = '$build_name',
			$f_description = '$build_description',
			$f_date = '$build_date'
		WHERE $f_build_id = '$build_id'";

	db_query( $db, $q);
}

# ----------------------------------------------------------------------
# Edit a release name, date and description.
# ----------------------------------------------------------------------
function admin_edit_release( $release_id, $release_name, $release_date, $release_description ) {
	global $db;
	$release_tbl 	= RELEASE_TBL;
	$f_release_id	= RELEASE_ID;
	$f_release_name	= RELEASE_NAME;
	$f_date_received= RELEASE_DATE_RECEIVED;
	$f_description	= RELEASE_DESCRIPTION;

	# Ensure that there is no ' in the Release Name or description which would cause problems with any queries
	$release_name = str_replace( "\'", "", "$release_name");
	$release_description = str_replace( "\'", "", "$release_description");

	$q = "UPDATE $release_tbl
		SET
			$f_release_name = '$release_name',
			$f_description = '$release_description',
			$f_date_received = '$release_date'
		WHERE $f_release_id = '$release_id'";

	db_query( $db, $q);
}

# ----------------------------------------------------------------------
# Print Test submenu
# INPUT:
#   Current Page (so that it will not be shown as a hyperlink)
# ----------------------------------------------------------------------
function admin_menu_print( $page, $project_id, $user_id ) {

	$menu = array();
	$user_menu = array();
	$manager_menu = array();
	$admin_menu = array();

	#user menu
	$user_menu 			= array(	lang_get('manage_projects') => 'admin_page.php' );

	# manager menu
	if( user_has_rights($project_id, $user_id, MANAGER) ) {

		$manager_menu 	= array(	 );
    }

	# admin menu
    if( user_has_rights($project_id, $user_id, ADMIN) ) {

    	$admin_menu 	= array(	lang_get('add_project') => 'project_add_page.php' );
    }

	$menu = array_merge($menu, $user_menu, $manager_menu, $admin_menu);

    html_print_sub_menu($page, $menu);

    echo"<br>". NEWLINE;
}

function admin_user_print( $page, $project_id, $user_id ) {

	$menu = array();
	$user_menu = array();
	$manager_menu = array();
	$admin_menu = array();

	#user menu
	$user_menu 			= array( 	lang_get('my_account') => "user_edit_my_account_page.php",
									lang_get('all_users') => 'user_manage_page.php' );

	# manager menu
	if( user_has_rights($project_id, $user_id, MANAGER) ) {

		$manager_menu 	= array(	 );
    }

	# admin menu
    if( user_has_rights($project_id, $user_id, ADMIN) ) {

    	$admin_menu 	= array(	lang_get('add_new_user') => 'user_add_page.php' );
    }

	$menu = array_merge($menu, $user_menu, $manager_menu, $admin_menu);

    html_print_sub_menu($page, $menu);
}

function admin_get_release_status( $blank=false ) {

	$status = array( "Accepted",
					 "Rejected",
					 "Production Ready" );

	if( $blank ) {
		$status[] = "";
	}

	return $status;
}

# ------------------------------------
# $Log: admin_api.php,v $
# Revision 1.5  2007/02/03 10:26:57  gth2
# no message
#
# Revision 1.4  2006/08/05 22:31:55  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.3  2006/06/30 00:55:42  gth2
# removing &$db from api files - gth
#
# Revision 1.2  2006/04/11 12:20:53  gth2
# adding code lost during sourceforge/cvs outage - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# ------------------------------------

?>