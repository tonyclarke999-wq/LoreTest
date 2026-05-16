<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug API
#
# $RCSfile: bug_api.php,v $ $Revision: 1.13 $
# ------------------------------------


# ----------------------------------------------------------------------
# Create and run query for displaying test records. Display table header.
# OUTPUT:
#   array of test records.
# ----------------------------------------------------------------------
/*
function &bug_apply_filter($project_id, $where_clause, $per_page, $order_by, $order_dir, $page_number, $csv_name=null) {

    global $db;

	$bug_tbl			= BUG_TBL;
	$f_bug_id			= $bug_tbl .".". BUG_ID;
	$f_project_id		= $bug_tbl .".". BUG_PROJECT_ID;
    $f_closed			= $bug_tbl .".". BUG_CLOSED;

	$q = "SELECT $bug_tbl.*
		 FROM $bug_tbl
		 WHERE $f_project_id = '$project_id'
		 AND $f_closed = 'N'";

    $order_clause 	= " ORDER BY $order_by $order_dir";
	$where_clause 	.= $where_clause." GROUP BY $f_bug_id";
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
	return db_fetch_array($db, $rs);
}
*/
# ----------------------------------------------------------------------
# Create where clause for tests and run query to extract test data
# OUTPUT:
#   array of test records.
# ----------------------------------------------------------------------
/*
function &bug_filter_rows($project_id, $status, $category, $component, $reporter, $assigned_to, $found_in_release, $assigned_to_release, $closed, $per_page, $orderby, $order_dir, $page_number, $csv_name=null) {


    $where_clause = bug_filter_generate_where_clause($status, $category, $component, $reporter, $assigned_to, $found_in_release, $assigned_to_release, $closed);
    $row = bug_apply_filter($project_id, $where_clause, $per_page, $orderby, $order_dir, $page_number, $csv_name);

    return $row;
}
*/

# ----------------------------------------------------------------------
# Create where clause for bugs and run query to extract bug data
# OUTPUT:
#   array of test records.
# ----------------------------------------------------------------------
/*
function &bug_copy_filter_rows(	$project_id,
								$release_id,
								$build_id,
								$testset_id,
								$filter_man_auto,
								$filter_ba_owner,
								$filter_qa_owner,
								$filter_test_type,
								$filter_area_tested,
								$per_page,
								$order_by,
								$order_dir,
								$page_number) {

    $where_clause = bug_filter_generate_where_clause(	$filter_man_auto,
														$filter_ba_owner,
														$filter_qa_owner,
														$filter_test_type,
														$filter_area_tested,
														$test_status="");

    $row = bug_copy_apply_filter(	$project_id,
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
*/
# ----------------------------------------------------------------------
# Create where clause for test query
# OUTPUT:
#   Where clause string
# ----------------------------------------------------------------------
/*
function bug_filter_generate_where_clause($status, $category, $component, $reporter, $assigned_to, $assigned_to_developer, $found_in_release, $assigned_to_release, $closed) {

	$bug_tbl				= BUG_TBL;
    $f_bug_status			= $bug_tbl .".". BUG_STATUS;
	$f_bug_category			= $bug_tbl .".". BUG_CATEGORY;
    $f_reporter				= $bug_tbl .".". BUG_REPORTER;
    $f_assigned_to			= $bug_tbl .".". BUG_ASSIGNED_TO;
    $f_found_in_release		= $bug_tbl .".". BUG_FOUND_IN_RELEASE;
    $f_assigned_to_release  = $bug_tbl .".". BUG_ASSIGN_TO_RELEASE;
    $f_bug_closed	        = $bug_tbl .".". BUG_CLOSED;


    $where_clause = '';

    # STATUS
    if ( !empty($status)  && $status != 'all') {

        $where_clause = $where_clause." AND $f_bug_status = '$status'";
    }
    # CATEGORY
    if ( !empty($category) && $category != 'all') {

        $where_clause = $where_clause." AND $f_bug_category = '$category'";
    }
    # REPORTER
    if ( !empty($reporter) && $reporter != 'all') {

        $where_clause = $where_clause." AND $f_reporter = '$reporter'";
    }
    # ASSIGNED TO
    if ( !empty($assigned_to) && $assigned_to != 'all') {

        $where_clause = $where_clause." AND $f_assigned_to = '$assigned_to'";
    }
    # FOUND IN RELEASE
    if ( !empty($found_in_release) && $found_in_release != 'all') {

        $where_clause = $where_clause." AND $f_found_in_release = '$found_in_release'";
    }
	 # FOUND IN RELEASE
    if ( !empty($assigned_to_release) && $assigned_to_release != 'all') {

        $where_clause = $where_clause." AND $f_assigned_to_release = '$assigned_to_release'";
    }

    return $where_clause;
}
*/

# ----------------------------------------------------------------------
# Print Bug submenu
# INPUT:
#   Current Page (so that it will not be shown as a hyperlink)
# ----------------------------------------------------------------------
function bug_menu_print($page) {

    $bug_link = lang_get('bug_view_link');
    $bug_add_link = lang_get('bug_add_link');

    $menu_items = array(
						$bug_link => 'bug_page.php',
						$bug_add_link => 'bug_add_page.php',
						);

    html_print_sub_menu( $page, $menu_items );
}

# ----------------------------------------------------------------------
# Add Bug record to database
# ----------------------------------------------------------------------
function bug_add( $project_id, $bug_category, $bug_component, $discovery_period, $bug_priority, $bug_severity, $found_in_release, $assign_to_release, $assigned_to, $assigned_to_developer, $summary, $description, $req_version_id="", $verify_id="" ) {

    global $db;

	$bug_tbl				= BUG_TBL;
	$f_project_id			= $bug_tbl .".". BUG_PROJECT_ID;
	$f_status				= $bug_tbl .".". BUG_STATUS;
	$f_reporter				= $bug_tbl .".". BUG_REPORTER;
	$f_date_reported		= $bug_tbl .".". BUG_REPORTED_DATE;
	$f_category				= $bug_tbl .".". BUG_CATEGORY;
	$f_component			= $bug_tbl .".". BUG_COMPONENT;
	$f_discovery_period	    = $bug_tbl .".". BUG_DISCOVERY_PERIOD;
	$f_priority				= $bug_tbl .".". BUG_PRIORITY;
	$f_severity				= $bug_tbl .".". BUG_SEVERITY;
	$f_found_in_release		= $bug_tbl .".". BUG_FOUND_IN_RELEASE;
	$f_assign_to_release	= $bug_tbl .".". BUG_ASSIGN_TO_RELEASE;
	$f_assigned_to			= $bug_tbl .".". BUG_ASSIGNED_TO;
	$f_assigned_to_dev		= $bug_tbl .".". BUG_ASSIGNED_TO_DEVELOPER;
	$f_summary				= $bug_tbl .".". BUG_SUMMARY;
	$f_description			= $bug_tbl .".". BUG_DESCRIPTION;
	$f_req_version_id		= $bug_tbl .".". BUG_REQ_VERSION_ID;
	$f_verify_id			= $bug_tbl .".". BUG_TEST_VERIFY_ID;
	$f_impl_in_release 		= $bug_tbl .".". BUG_IMPLEMENTED_IN_RELEASE;

	$reporter				= session_get_username();
	$current_date			= date_get_short_dt();

	# Make sure integers are entered
	if( $bug_category == '' ){
		$bug_category = 0;
	}
	if( $bug_component == '' ) {
		$bug_component = 0;
	}

	$q  = "INSERT INTO $bug_tbl
		  ($f_project_id, $f_status, $f_reporter, $f_date_reported, $f_category, $f_component, $f_discovery_period, $f_priority, $f_severity, $f_found_in_release, $f_assign_to_release, $f_assigned_to, $f_assigned_to_dev, $f_summary, $f_description,
		  $f_req_version_id, $f_verify_id)
		  VALUES
		  ('$project_id', 'New', '$reporter', '$current_date', '$bug_category', '$bug_component', '$discovery_period', '$bug_priority', '$bug_severity', '$found_in_release', '$assign_to_release', '$assigned_to', '$assigned_to_developer', '$summary', '$description', '$req_version_id', '$verify_id')";

	//print"$q<br>";
	db_query( $db, $q );

	$bug_id = db_get_last_autoincrement_id( $db );

	# ADD A RECORD TO THE BUG HISTORY TABLE
	bug_history_log_new_bug( $bug_id );

	# INSERT THE BUG_ID INTO THE VERIFY RESULTS TABLE IF THE VERIFY ID EXISTS
	if( !empty( $verify_id ) ) {
		results_update_verfication_record( $verify_id, VERIFY_RESULTS_DEFECT_ID, $bug_id );
	}

	# GET A LIST OF USER_IDS THAT WANT EMAIL NOTIFICATION
	$user_ids = project_get_user_email_prefs( $project_id, PROJ_USER_EMAIL_NEW_BUG );
	
	if( !empty($user_ids) ) {
		$email_to = user_get_email_by_user_id( $user_ids );
		bug_email($project_id, $bug_id, $email_to, "new_bug");
	}

	return $bug_id;

}

# ----------------------------------------------------------------------
# Query for returning bug records.
#
# OUTPUT:
#	html_table_offset
#	tests
# ----------------------------------------------------------------------
function bug_get($project_id, $page_number=0, $order_by=BUG_ID, $order_dir="DESC",
				 $bug_status, $category="", $component="", $reported_by="", $assigned_to="", $assigned_to_developer="", $found_in_release="", $assigned_to_release=null, $per_page=null, $view_closed='No', $search="", $jump="", $csv_name=null) {

	global $db;

	$bug_tbl 					= BUG_TBL;
	$f_bug_id					= $bug_tbl .".". BUG_ID;
	$f_bug_project_id 			= $bug_tbl .".". BUG_PROJECT_ID;
	$f_bug_category_id 			= $bug_tbl .".". BUG_CATEGORY;
	$f_bug_component_id	 		= $bug_tbl .".". BUG_COMPONENT;
	$f_priority		 			= $bug_tbl .".". BUG_PRIORITY;
	$f_severity		 			= $bug_tbl .".". BUG_SEVERITY;
	$f_closed_reason_code		= $bug_tbl .".". BUG_CLOSED_REASON_CODE;
	$f_status		 			= $bug_tbl .".". BUG_STATUS;
	$f_reporter		 			= $bug_tbl .".". BUG_REPORTER;
	$f_reported_date			= $bug_tbl .".". BUG_REPORTED_DATE;
	$f_assigned_to				= $bug_tbl .".". BUG_ASSIGNED_TO;
	$f_assigned_to_developer	= $bug_tbl .".". BUG_ASSIGNED_TO_DEVELOPER;
	$f_closed		 			= $bug_tbl .".". BUG_CLOSED;
	$f_closed_date	 			= $bug_tbl .".". BUG_CLOSED_DATE;
	$f_verify_id		 		= $bug_tbl .".". BUG_TEST_VERIFY_ID;
	$f_req_version_id 			= $bug_tbl .".". BUG_REQ_VERSION_ID;
	$f_found_in_release			= $bug_tbl .".". BUG_FOUND_IN_RELEASE;
	$f_assign_to_release		= $bug_tbl .".". BUG_ASSIGN_TO_RELEASE;
	$f_impl_in_release 			= $bug_tbl .".". BUG_IMPLEMENTED_IN_RELEASE;
	$f_discovery_period			= $bug_tbl .".". BUG_DISCOVERY_PERIOD;
	$f_summary		 			= $bug_tbl .".". BUG_SUMMARY;
	$f_description	 			= $bug_tbl .".". BUG_DESCRIPTION;
	$f_assigned_to_release  	= $bug_tbl .".". BUG_ASSIGN_TO_RELEASE;

	$category_tbl				= BUG_CATEGORY_TBL;
	$f_category_id				= $category_tbl .".". CATEGORY_ID;
	$f_category_proj_id			= $category_tbl .".". CATEGORY_PROJECT_ID;
	$f_category_name			= $category_tbl .".". CATEGORY_NAME;

	$component_tbl				= BUG_COMPONENT_TBL;
	$f_component_id				= $component_tbl .".". COMPONENT_ID;
	$f_component_proj_id		= $component_tbl .".". COMPONENT_PROJECT_ID;
	$f_component_name			= $component_tbl .".". COMPONENT_NAME;

	# Add Release Table
	$release_tbl				= RELEASE_TBL;


	$where_clause = " WHERE $f_bug_project_id = '$project_id'";

	# STATUS
    if ( !empty($status)  && $status != 'all') {

        $where_clause = $where_clause." AND $f_bug_status = '$status'";
    }
    # CATEGORY
    if ( !empty($category) && $category != 'all') {

        $where_clause = $where_clause." AND $f_category_id = '$category'";
    }
	# COMPONENT
    if ( !empty($component) && $component != 'all') {

        $where_clause = $where_clause." AND $f_component_id = '$component'";
    }
    # REPORTER
    if ( !empty($reporter) && $reporter != 'all') {

        $where_clause = $where_clause." AND $f_reporter = '$reporter'";
    }
    # ASSIGNED TO
    if ( !empty($assigned_to) && $assigned_to != 'all') {

        $where_clause = $where_clause." AND $f_assigned_to = '$assigned_to'";
    }
	# ASSIGNED TO DEVELOPER
    if ( !empty($assigned_to_developer) && $assigned_to_developer != 'all') {

        $where_clause = $where_clause." AND $f_assigned_to_developer = '$assigned_to_developer'";
    }
    # FOUND IN RELEASE
    if ( !empty($found_in_release) && $found_in_release != 'all') {

        $where_clause = $where_clause." AND $f_found_in_release = '$found_in_release'";
    }
	# ASSIGN TO RELEASE
    if ( !empty($assigned_to_release) && $assigned_to_release != 'all') {

        $where_clause = $where_clause." AND $f_assigned_to_release = '$assigned_to_release'";
    }
	# VIEW CLOSED
    if ( $view_closed == lang_get('no') ) {
        $where_clause = $where_clause." AND $f_status != 'Closed'";
    }
	else {
		//print"view_closed = $view_closed<br>";
	}
	# SEARCH
	if ( !empty($search) ) {
        $where_clause = $where_clause." AND ( ($f_summary LIKE '%$search%') OR ($f_description LIKE '%$search%') )";
    }


	//$where_clause = substr( $where_clause, 4, strlen($where_clause) );

	//$where_clause .= $where_clause;

	$q = "	SELECT
				$f_bug_id,
				$f_priority,
				$f_severity,
				$f_status,
				$f_bug_category_id,
				$f_reporter,
				$f_assigned_to,
				$f_assigned_to_developer,
				$f_found_in_release,
				$f_assign_to_release,
				$f_description,
				$f_summary,
				$f_bug_project_id,
				$f_bug_component_id,
				$f_closed_reason_code,
				$f_reported_date,
				$f_closed,
				$f_closed_date,
				$f_verify_id,
				$f_req_version_id,
				$f_impl_in_release,
				$f_discovery_period,
				$f_category_id,
				$f_category_name,
				$f_component_id,
				$f_component_name
			FROM $bug_tbl
			LEFT JOIN $category_tbl ON $f_bug_category_id = $f_category_id
			LEFT JOIN $component_tbl ON $f_bug_component_id = $f_component_id";


	$q .= "	$where_clause
			ORDER BY $order_by $order_dir";

	#print"$q<br>";

	if( is_null($per_page) ) {

		$display_options	= session_get_filter_options("bug");
		$per_page 			= $display_options['per_page'];
	}

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

	#print"$q<br>";

	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}


# ----------------------------------------------------------------------
# Get the details for a test
# INPUT:
#   test id
# OUTPUT:
#   test details
# ----------------------------------------------------------------------
function bug_get_detail( $bug_id ) {

    global $db;
    $bug_tbl 					= BUG_TBL;
	$f_bug_id					= $bug_tbl .".". BUG_ID;
	$f_bug_project_id 			= $bug_tbl .".". BUG_PROJECT_ID;
	$f_bug_category_id 			= $bug_tbl .".". BUG_CATEGORY;
	$f_bug_component_id	 		= $bug_tbl .".". BUG_COMPONENT;
	$f_priority		 			= $bug_tbl .".". BUG_PRIORITY;
	$f_severity		 			= $bug_tbl .".". BUG_SEVERITY;
	$f_closed_reason_code		= $bug_tbl .".". BUG_CLOSED_REASON_CODE;
	$f_status		 			= $bug_tbl .".". BUG_STATUS;
	$f_reporter		 			= $bug_tbl .".". BUG_REPORTER;
	$f_reported_date			= $bug_tbl .".". BUG_REPORTED_DATE;
	$f_assigned_to				= $bug_tbl .".". BUG_ASSIGNED_TO;
	$f_assigned_to_dev			= $bug_tbl .".". BUG_ASSIGNED_TO_DEVELOPER;
	$f_closed		 			= $bug_tbl .".". BUG_CLOSED;
	$f_closed_date	 			= $bug_tbl .".". BUG_CLOSED_DATE;
	$f_verify_id		 		= $bug_tbl .".". BUG_TEST_VERIFY_ID;
	$f_req_version_id 			= $bug_tbl .".". BUG_REQ_VERSION_ID;
	$f_found_in_release			= $bug_tbl .".". BUG_FOUND_IN_RELEASE;
	$f_assign_to_release		= $bug_tbl .".". BUG_ASSIGN_TO_RELEASE;
	$f_impl_in_release 			= $bug_tbl .".". BUG_IMPLEMENTED_IN_RELEASE;
	$f_discovery_period			= $bug_tbl .".". BUG_DISCOVERY_PERIOD;
	$f_summary		 			= $bug_tbl .".". BUG_SUMMARY;
	$f_description	 			= $bug_tbl .".". BUG_DESCRIPTION;

	$category_tbl				= BUG_CATEGORY_TBL;
	$f_category_id				= $category_tbl .".". CATEGORY_ID;
	$f_category_proj_id			= $category_tbl .".". CATEGORY_PROJECT_ID;
	$f_category_name			= $category_tbl .".". CATEGORY_NAME;

	$component_tbl				= BUG_COMPONENT_TBL;
	$f_component_id				= $component_tbl .".". COMPONENT_ID;
	$f_component_proj_id		= $component_tbl .".". COMPONENT_PROJECT_ID;
	$f_component_name			= $component_tbl .".". COMPONENT_NAME;

    $q = "	SELECT
				$f_bug_id,
				$f_bug_project_id,
				$f_bug_category_id,
				$f_bug_component_id,
				$f_priority,
				$f_severity,
				$f_closed_reason_code,
				$f_status,
				$f_reporter,
				$f_reported_date,
				$f_assigned_to,
				$f_assigned_to_dev,
				$f_closed,
				$f_closed_date,
				$f_verify_id,
				$f_req_version_id,
				$f_found_in_release,
				$f_assign_to_release,
				$f_impl_in_release,
				$f_discovery_period,
				$f_summary,
				$f_description,
				$f_category_id,
				$f_category_name,
				$f_component_id,
				$f_component_name
			FROM $bug_tbl
			LEFT JOIN $category_tbl ON $f_bug_category_id = $f_category_id
			LEFT JOIN $component_tbl ON $f_bug_component_id = $f_component_id
			WHERE $f_bug_id = '$bug_id'";

	# print"$q<br>";

    $rs = & db_query( $db, $q);
    $row = db_fetch_row( $db, $rs );

    return $row;

}

# ----------------------------------------------------------------------
# Get the details for a test
# INPUT:
#   test id
# OUTPUT:
#   test details
# ----------------------------------------------------------------------
function bug_get_field_value( $bug_id, $field_name ) {

    global $db;
    $bug_tbl 		= BUG_TBL;
	$f_bug_id		= $bug_tbl .".". BUG_ID;


    $q = "	SELECT
				$field_name
			FROM
				$bug_tbl
			WHERE
				$f_bug_id = '$bug_id'";

	$value = db_get_one( $db, $q );

    return $value;

}

# ----------------------------------------------------------------------
# Get the bug_id given a specific verification_id for a test
# This function is used when updating or deleting test verifications
# INPUT:
#   verify id
# OUTPUT:
#   bug_id
# ----------------------------------------------------------------------
function bug_get_bug_id_from_verification_id( $verify_id ) {

	global $db;
	$bug_tbl 		= BUG_TBL;
	$f_bug_id		= BUG_ID;
	$f_verify_id	= BUG_TEST_VERIFY_ID;
	$bug_ids		= array();

	$q = "SELECT
			$f_bug_id
		  FROM
			$bug_tbl
		  WHERE
			$f_verify_id = '$verify_id'";

	$bug_id = db_get_one( $db, $q );

	return $bug_id;

}

# ----------------------------------------------------------------------
# This function will validate that a bug exists
# It is used when users try to create a relationship.
# We want to make sure the relationship to the second bug is valid
# INPUT:
#   bug id
# OUTPUT:
#   true	= if the bug_id exists
#	false   = if the bug doesn't exist
# ----------------------------------------------------------------------
function bug_exists( $bug_id ) {

	global $db;
    $bug_tbl 		= BUG_TBL;
	$f_bug_id		= $bug_tbl .".". BUG_ID;


    $q = "	SELECT
				$f_bug_id
			FROM
				$bug_tbl
			WHERE
				$f_bug_id = '$bug_id'";

	$row_count = db_num_rows( $db, db_query($db, $q) );

	if( $row_count == 1 ) {
		return true;
	}
	else {
		return false;
	}

}

# ----------------------------------------------------------------------
# Update a record in the Bug table
# ----------------------------------------------------------------------
function bug_update( $bug_id, $status, $category, $discovery_period, $component, $priority, $severity, $found_in_release, 						 $assign_to_release, $implemented_in_release, $assigned_to, $assigned_to_developer, $summary, $description ) {

    global $db;

    $bug_tbl 					= BUG_TBL;
	$f_bug_id					= $bug_tbl .".". BUG_ID;
	$f_bug_project_id 			= $bug_tbl .".". BUG_PROJECT_ID;
	$f_bug_category_id 			= $bug_tbl .".". BUG_CATEGORY;
	$f_discovery_period			= $bug_tbl .".". BUG_DISCOVERY_PERIOD;
	$f_bug_component_id	 		= $bug_tbl .".". BUG_COMPONENT;
	$f_priority		 			= $bug_tbl .".". BUG_PRIORITY;
	$f_severity		 			= $bug_tbl .".". BUG_SEVERITY;
	$f_status		 			= $bug_tbl .".". BUG_STATUS;
	$f_reporter		 			= $bug_tbl .".". BUG_REPORTER;
	$f_reported_date			= $bug_tbl .".". BUG_REPORTED_DATE;
	$f_assigned_to				= $bug_tbl .".". BUG_ASSIGNED_TO;
	$f_assigned_to_developer	= $bug_tbl .".". BUG_ASSIGNED_TO_DEVELOPER;
	$f_closed		 			= $bug_tbl .".". BUG_CLOSED;
	$f_closed_date	 			= $bug_tbl .".". BUG_CLOSED_DATE;
	$f_verify_id		 		= $bug_tbl .".". BUG_TEST_VERIFY_ID;
	$f_req_version_id 			= $bug_tbl .".". BUG_REQ_VERSION_ID;
	$f_found_in_release			= $bug_tbl .".". BUG_FOUND_IN_RELEASE;
	$f_assign_to_release		= $bug_tbl .".". BUG_ASSIGN_TO_RELEASE;
	$f_impl_in_release 			= $bug_tbl .".". BUG_IMPLEMENTED_IN_RELEASE;
	$f_summary		 			= $bug_tbl .".". BUG_SUMMARY;
	$f_description	 			= $bug_tbl .".". BUG_DESCRIPTION;

	# Make sure integers are entered
	if( $category == '' ){
		$category = 0;
	}
	if( $component == '' ) {
		$component = 0;
	}

	$old_value = bug_get_detail( $bug_id );

    $q = "UPDATE $bug_tbl SET
              $f_status = '$status',
              $f_bug_category_id = '$category',
              $f_discovery_period = '$discovery_period',
              $f_bug_component_id = '$component',
              $f_priority = '$priority',
			  $f_severity = '$severity',
              $f_found_in_release = '$found_in_release',
              $f_assign_to_release = '$assign_to_release',
              $f_impl_in_release = '$implemented_in_release',
              $f_assigned_to = '$assigned_to',
			  $f_assigned_to_developer = '$assigned_to_developer',
              $f_summary = '$summary',
              $f_description = '$description'
              WHERE
              $f_bug_id = '$bug_id'";

	#print"$q<br>";
	db_query( $db, $q );

	
	# Only add record to monitor table and email user if status array is true
	if( $GLOBALS['default_notify_flags']['update'] ) {
			
		# enter user_id in bug_monitor table and gather recipients
		bug_monitor_attach_user($bug_id);
		$action = "update_bug";
		$recipients = bug_email_collect_recipients( $bug_id, $action );
		
		if( $recipients != '' ) {

			$project_id	= session_get_project_id();
			bug_email($project_id, $bug_id, $recipients, $action);
		}
	}

	bug_history_log_event( $bug_id, BUG_STATUS, $old_value[BUG_STATUS], $status );
	bug_history_log_event_special( $bug_id, BUG_CATEGORY, $old_value[BUG_CATEGORY], $category );
	bug_history_log_event( $bug_id, BUG_DISCOVERY_PERIOD, $old_value[BUG_DISCOVERY_PERIOD], $discovery_period );
	bug_history_log_event_special( $bug_id, BUG_COMPONENT, $old_value[BUG_COMPONENT], $component);
	bug_history_log_event( $bug_id, BUG_PRIORITY, $old_value[BUG_PRIORITY], $priority );
	bug_history_log_event( $bug_id, BUG_SEVERITY, $old_value[BUG_SEVERITY], $severity );
	bug_history_log_event( $bug_id, BUG_FOUND_IN_RELEASE, $old_value[BUG_FOUND_IN_RELEASE], $found_in_release );
	bug_history_log_event( $bug_id, BUG_ASSIGN_TO_RELEASE, $old_value[BUG_ASSIGN_TO_RELEASE], $assign_to_release );
	bug_history_log_event( $bug_id, BUG_IMPLEMENTED_IN_RELEASE, $old_value[BUG_IMPLEMENTED_IN_RELEASE], $implemented_in_release );
	bug_history_log_event( $bug_id, BUG_ASSIGNED_TO, $old_value[BUG_ASSIGNED_TO], $assigned_to );
	bug_history_log_event( $bug_id, BUG_ASSIGNED_TO_DEVELOPER, $old_value[BUG_ASSIGNED_TO_DEVELOPER], $assigned_to_developer );
	bug_history_log_event( $bug_id, BUG_SUMMARY, $old_value[BUG_SUMMARY], $summary );
	bug_history_log_event( $bug_id, BUG_DESCRIPTION, $old_value[BUG_DESCRIPTION], $description );

}

# ----------------------------------------------------------------------
# Update a single field in the Bug table
# This function first gets the current value of the field from the database
# so that we can log the change in the history table after making the change
# INPUT:
#	bug_id - the id you want to update
#	field_name - the field you want to udpate
#	value - the new value of the field
# ----------------------------------------------------------------------
function bug_update_field( $bug_id, $field_name, $value ) {

	global $db;

	$bug_tbl	= BUG_TBL;
	$f_bug_id	= BUG_ID;
	$old_value	= bug_get_field_value( $bug_id, $field_name ); # get the current value
	$project_id	= session_get_project_id();

	$q = "UPDATE $bug_tbl
		 SET $field_name = '$value'
		 WHERE $f_bug_id = '$bug_id'";
	db_query( $db, $q ); # update field

	# If the user has assigned a bug
	if( $field_name == BUG_ASSIGNED_TO || $field_name ==  BUG_ASSIGNED_TO_DEVELOPER ) {

		# Only add record to monitor table and email user if assigned_to array is true
		if( $GLOBALS['default_notify_flags']['assigned_to'] ) {
				
			$user_id = user_get_id( $value );
			$action	 = "assign_bug";

			# enter user_id in bug_monitor table and gather recipients for email
			bug_monitor_attach_user($bug_id, $user_id);
			$recipients = bug_email_collect_recipients( $bug_id, $action );

			if( $recipients != '' ) {
				bug_email($project_id, $bug_id, $recipients, $action);
			}
		}

	}
	# If the user has updated the status
	if( $field_name == BUG_STATUS ) {

		# Only add record to monitor table and email user if status array is true
		if( $GLOBALS['default_notify_flags']['status'] ) {
				
			# enter user_id in bug_monitor table and gather recipients to send email
			$action = "update_status";
			bug_monitor_attach_user($bug_id);
			$recipients = bug_email_collect_recipients( $bug_id, $action );

			if( $recipients != '' ) {
				bug_email($project_id, $bug_id, $recipients, $action);
			}
		}
	}

	
	# LOG CHANGE IN HISTORY
	bug_history_log_event( $bug_id, $field_name, $old_value, $value );

}

# ----------------------------------------------------------------------
# Closed a bug
# INPUT:
#	bug_id - the id you want to update
#	closed_reson_code - Fixed, Rejected, etc
#   bugnote
# ----------------------------------------------------------------------
function bug_close(	$bug_id, $closed_reason_code, $bugnote ) {

	# Need to update the status field, who closed the ticket, the date, and closed reason code
	bug_update_field( $bug_id, BUG_CLOSED_REASON_CODE, $closed_reason_code );

	# add a bugnote only if the user entered a comment
	if( !empty( $bugnote ) ){
		bug_add_bugnote( $bug_id, $bugnote );
	}

	# Only add record to monitor table and email user if status array is true
	if( $GLOBALS['default_notify_flags']['status'] ) {
			
		# enter user_id in bug_monitor table and gather recipients to send email
		$action = "update_status";
		bug_monitor_attach_user($bug_id);
		$recipients = bug_email_collect_recipients( $bug_id, $action );

		if( $recipients != '' ) {
			bug_email($project_id, $bug_id, $recipients, $action);
		}
	}
}


# ----------------------------------------------------------------------
# Delete a bug and all associated bug notes
# ----------------------------------------------------------------------
function bug_delete( $bug_id ) {

	global $db;
	$bug_tbl	= BUG_TBL;
	$f_bug_id	= BUG_ID;

	bug_delete_all_bugnotes( $bug_id );

	bug_delete_history( $bug_id );

	bug_delete_relationships( $bug_id );

	bug_delete_test_assoc( $bug_id );

	$q = "DELETE FROM
			$bug_tbl
		 WHERE
			$f_bug_id = '$bug_id'";


	db_query( $db, $q );
}

# ----------------------------------------------------------------------
# Add a relationship between two bugs
# INPUT:
#   bug id  - the bug the user is creating the relationship from (src)
#   related_bug_id - the related bug_id (dest)
#   rel_type - the type of relationship (related to, parent, child)
# OUTPUT:
#   a record in the bug_assoc table
# ----------------------------------------------------------------------
function bug_add_relationship( $bug_id, $related_bug_id, $rel_type ) {

	global $db;
	$bug_assoc_tbl		= BUG_ASSOC_TBL;
	$f_assoc_id			= BUG_ASSOC_ID;
	$f_src_id			= BUG_ASSOC_SRC_ID;
	$f_dest_id			= BUG_ASSOC_DEST_ID;
	$f_rel_type			= BUG_ASSOC_REL_TYPE;

	# Make sure the related_bug_id is valid
	$bug_exists = bug_exists( $related_bug_id );
	if( !$bug_exists ) {
		return false;
	}

	# Make sure a relationship has not already been created
	$q = "	SELECT $f_assoc_id
			FROM $bug_assoc_tbl
			WHERE ( $f_src_id = '$bug_id' AND $f_dest_id = '$related_bug_id' )
				OR ( $f_src_id = '$related_bug_id' AND $f_dest_id = '$bug_id' )";

	$rs = db_query($db, $q);
	$relationship_exits = db_num_rows($db, $rs);

	if( $relationship_exits ) {
		return false;
	}

	# rel_type
	# Related = 0
	# Child	  = 1
	# Parent  = 2
	$q = "INSERT INTO $bug_assoc_tbl
		  ( $f_src_id, $f_dest_id, $f_rel_type )
		  VALUES
		  ( '$bug_id', '$related_bug_id', '$rel_type' )";

	#print"$q<br>";
	db_query( $db, $q );

	# Only add record to monitor table and email user if status array is true
	if( $GLOBALS['default_notify_flags']['update'] ) {
			
		# enter user_id in bug_monitor table and gather recipients
		bug_monitor_attach_user($bug_id);
		$recipients = bug_email_collect_recipients( $bug_id, "update_bug" );
		
		if( $recipients != '' ) {

			$project_id	= session_get_project_id();
			bug_email($project_id, $bug_id, $recipients, "add_relationship");
		}
	}

	return true;
}

# ----------------------------------------------------------------------
# This function displays related bugs
# INPUT:
#   bug id - the bug_id the user is viewing
# OUTPUT:
#   a record in the bug_assoc table
# ----------------------------------------------------------------------
function bug_display_related_items( $bug_id, $user_tempest_admin, $user_project_manager ) {

	# Get all records where the bug_id is in the src column and display the dest bugs
	$delete_page = 'delete_page.php';
	$redirect_url = "bug_detail_page.php?bug_id=$bug_id";

	$relations = bug_get_relations( $bug_id );
	$row_style = '';
	foreach( $relations as $rel ) {

		$assoc_id	= $rel[BUG_ASSOC_ID];
		$src_id		= $rel[BUG_ASSOC_SRC_ID];
		$dest_id	= $rel[BUG_ASSOC_DEST_ID];
		$rel_type	= $rel[BUG_ASSOC_REL_TYPE];

		if( $bug_id == $src_id ) { # viewing bug from the src side

			$dest_bug_detail = bug_get_detail( $dest_id );
			$relationship = bug_get_relationship_type_for_src( $rel_type );
			$project_name		= project_get_name( $dest_bug_detail[BUG_PROJECT_ID] );
			$related_bug_id		= $dest_id;
			$padded_bug_id		= util_pad_id( $dest_id );
			$bug_status			= $dest_bug_detail[BUG_STATUS];
			$assigned_to_dev	= $dest_bug_detail[BUG_ASSIGNED_TO_DEVELOPER];
			$bug_summary		= $dest_bug_detail[BUG_SUMMARY];

		}
		else { # viewing bug from dest side


			$src_bug_detail		= bug_get_detail( $src_id );
			$relationship		= bug_get_relationship_type_for_dest( $rel_type );
			$project_name		= project_get_name( $src_bug_detail[BUG_PROJECT_ID] );
			$related_bug_id		= $src_id;
			$padded_bug_id		= util_pad_id( $src_id );
			$bug_status			= $src_bug_detail[BUG_STATUS];
			$assigned_to_dev	= $src_bug_detail[BUG_ASSIGNED_TO_DEVELOPER];
			$bug_summary		= $src_bug_detail[BUG_SUMMARY];
		}

		$row_style = html_tbl_alternate_bgcolor($row_style);

		print"<tr class='$row_style'>". NEWLINE;
		print"<td class='tbl-r' width='10%'>$relationship</td>". NEWLINE;
		print"<td class='tbl-c' width='10%'><a href='bug_detail_page.php?bug_id=$related_bug_id'>$padded_bug_id</a></td>". NEWLINE;
		print"<td class='tbl-c' width='10%'>$project_name</td>". NEWLINE;
		print"<td class='tbl-c' width='10%'>$bug_status</td>". NEWLINE;
		#print"<td class='tbl-c' width='10%'>$bug_assigned_to</td>". NEWLINE;
		print"<td class='tbl-c' width='10%'>$assigned_to_dev</td>". NEWLINE;
		print"<td class='tbl-l' width='35%'>$bug_summary</td>". NEWLINE;
		# DELETE LINK
		print"<td class='tbl-c' width='5%'>". NEWLINE;
		if( $user_tempest_admin ) {
			# nesting forms breaks html, if the delete button is pressed, the delete page will be included at the top of the bug_detail_page
			//print"<form method=post action='$delete_page'>". NEWLINE;
			print"<input type='submit' name='delete' value='[". lang_get( 'delete' ) ."]' class='page-numbers'>";
			print"<input type='hidden' name='r_page' value=$redirect_url>". NEWLINE;
			print"<input type='hidden' name='f' value='delete_bug_assoc'>". NEWLINE;
			print"<input type='hidden' name='id' value=$assoc_id>". NEWLINE;
			print"<input type='hidden' name='msg' value='270'>". NEWLINE;
			//print"</form>";
		}
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}
}

# ----------------------------------------------------------------------
# Display the output of the parent/child relationship
# When viewing a bug where the bug_id is the src in the bug_assoc table,
# the relationship will be straight forward.  The bug_id will be the Parent
# and any other bug will be the child.
# INPUT:
#   rel_type  - (related=0 - child=1 - parent=2)
# OUTPUT:
#   a string describing the relationship type
# ----------------------------------------------------------------------
function bug_get_relationship_type_for_src( $rel_type ) {

	switch( $rel_type ) {

		case BUG_RELATED:
			$relationship = lang_get('related_to');
			break;
		case BUG_CHILD:
			$relationship = lang_get('child_of');
			break;
		case BUG_PARENT:
			$relationship = lang_get('parent_of');
			break;
		default:
			$relationship = lang_get('related_to');
			break;
	}

	return $relationship;

}

# ----------------------------------------------------------------------
# Display the output of the child/parent relationship
# When viewing a bug where the bug_id is the dest in the bug_assoc table,
# the relationship will be reversed.  If the relationship type = parent but the bug_id
# is in the dest column of the assoc table, it's actually a child.  We're keeping the data
# in the database simple and dealing with the complexity of the relationship in the code.
# INPUT:
#   rel_type  - (related=0 - child=2 - parent=1)
# OUTPUT:
#   a string describing the relationship type
# ----------------------------------------------------------------------
function bug_get_relationship_type_for_dest( $rel_type ) {

	switch( $rel_type ) {

		case BUG_RELATED:
			$relationship = lang_get('related_to');
			break;
		case BUG_CHILD:
			$relationship = lang_get('parent_of');
			break;
		case BUG_PARENT:
			$relationship = lang_get('child_of');
			break;
		default:
			$relationship = lang_get('related_to');
			break;
	}

	return $relationship;
}

/*
function bug_display_related_item_detail( $related_bug_id, $relationship_type ) {

	$bug_detail = bug_get_detail( $related_bug_id );
	$padded_bug_id = util_pad_id( $related_bug_id );

	# relationship type, link to related ticket, project, status, summary [delete]
	print"<tr>". NEWLINE;
	print"<td>$relationship_type</td>". NEWLINE;
	print"<td><a href='bug_detail_page.php?bug_id=$related_bug_id'>$padded_bug_id</a></td>". NEWLINE;

}
*/
# ----------------------------------------------------------------------
# Delete a relationship from the bug_assoc table
# INPUT:
#   bug_assoc_id
# ----------------------------------------------------------------------
function bug_delete_bug_assoc( $bug_assoc_id ) {

	global $db;
	$bug_assoc_tbl		= BUG_ASSOC_TBL;
	$f_assoc_id			= BUG_ASSOC_ID;

	$q = "DELETE FROM
			$bug_assoc_tbl
		  WHERE
			$f_assoc_id = '$bug_assoc_id'";

	db_query( $db, $q );
}

/*
function bug_get_src_relations( $bug_id ) {

	global $db;
	$bug_assoc_tbl		= BUG_ASSOC_TBL;
	$f_assoc_id			= BUG_ASSOC_ID;
	$f_src_id			= BUG_ASSOC_SRC_ID;
	$f_dest_id			= BUG_ASSOC_DEST_ID;
	$f_rel_type			= BUG_ASSOC_REL_TYPE;

	$q = "SELECT
			$f_assoc_id,
			$f_src_id,
			$f_dest_id,
			$f_rel_type
		  FROM
			$bug_assoc_tbl
		  WHERE
			$f_src_id = '$bug_id'";

	$rs = db_query( $db, $q );

	$rows = db_fetch_array($db, $rs);

    return $rows;
}
*/

function bug_get_relations( $bug_id ) {

	global $db;
	$bug_assoc_tbl		= BUG_ASSOC_TBL;
	$f_assoc_id			= BUG_ASSOC_ID;
	$f_src_id			= BUG_ASSOC_SRC_ID;
	$f_dest_id			= BUG_ASSOC_DEST_ID;
	$f_rel_type			= BUG_ASSOC_REL_TYPE;

	$q = "SELECT
			$f_assoc_id,
			$f_src_id,
			$f_dest_id,
			$f_rel_type
		  FROM
			$bug_assoc_tbl
		  WHERE
			$f_src_id = '$bug_id'
		  OR
			$f_dest_id = '$bug_id'";

	$rs = db_query( $db, $q );
	$rows = db_fetch_array($db, $rs);

    return $rows;

}

function bug_get_dest_relations( $bug_id ) {

	global $db;
	$bug_assoc_tbl		= BUG_ASSOC_TBL;
	$f_assoc_id			= BUG_ASSOC_ID;
	$f_src_id		= BUG_ASSOC_SRC_ID;
	$f_dest_id		= BUG_ASSOC_DEST_ID;
	$f_rel_type			= BUG_ASSOC_REL_TYPE;

	$q = "SELECT
			$f_assoc_id,
			$f_src_id,
			$f_dest_id,
			$f_rel_type
		  FROM
			$bug_assoc_tbl
		  WHERE
			$f_dest_id = '$bug_id'";

	$rs = db_query( $db, $q );

	$rows = db_fetch_array($db, $rs);

    return $rows;

}

# ----------------------------------------------------------------------
# Add a note to the Bugnotes table
# INPUT:
#   BugID and the BugNote detail
# OUTPUT:
#   None on success
# ----------------------------------------------------------------------
function bug_add_bugnote( $bug_id, $bugnote ) {

    global $db;
    $bugnote_tbl        = BUG_NOTE_TBL;
	$f_bug_id			= BUG_NOTE_BUG_ID;
    $f_author			= BUG_NOTE_AUTHOR;
	$f_date_created		= BUG_NOTE_DATE_CREATED;
	$f_bugnote			= BUG_NOTE_DETAIL;

	$author				= session_get_username();
	$created_date		= date_get_short_dt();

    $q = "INSERT INTO $bugnote_tbl
		  ( $f_bug_id, $f_author, $f_date_created, $f_bugnote )
		  VALUES
		  ( '$bug_id', '$author', '$created_date', '$bugnote' )";

	db_query( $db, $q );

	$bugnote_id = db_get_last_autoincrement_id( $db );
	$bugnote_id = util_pad_id( $bugnote_id );

	if( $GLOBALS['default_notify_flags']['bugnote'] ) {

		$action				= "add_bugnote";
		$project_id			= session_get_project_id();
		$s_user_properties	= session_get_user_properties() ;
		$user_id			= $s_user_properties['user_id'];

		bug_monitor_attach_user($bug_id);
		# Gather recipients and send email
		$recipients = bug_email_collect_recipients( $bug_id, $action );
		bug_email($project_id, $bug_id, $recipients, $action);
	}

	bug_history_log_event( $bug_id, lang_get('add_bugnote'), '', $bugnote_id );
}

# ----------------------------------------------------------------------
# Get all bugnotes for a given bug
# INPUT:
#   BugID
# OUTPUT:
#   Array containing all notes for the given bug.
# ----------------------------------------------------------------------
function bug_get_notes( $bug_id ) {

	global $db;
    $bugnote_tbl        = BUG_NOTE_TBL;
	$f_bugnote_id		= BUG_NOTE_ID;
	$f_bug_id			= BUG_NOTE_BUG_ID;
    $f_author			= BUG_NOTE_AUTHOR;
	$f_date_created		= BUG_NOTE_DATE_CREATED;
	$f_bugnote			= BUG_NOTE_DETAIL;

	$q = "SELECT
			$f_bugnote_id,
			$f_author,
			$f_date_created,
			$f_bugnote
		  FROM
			$bugnote_tbl
		  WHERE
			$f_bug_id = '$bug_id'";

	$rs = db_query($db, $q);
	$rows = db_fetch_array($db, $rs);

    return $rows;
}

# ----------------------------------------------------------------------
# Get a specific bugnote for a given bug
# INPUT:
#   BugnoteID
# OUTPUT:
#   Array containing the details for the bugnote
# ----------------------------------------------------------------------
function bug_get_bugnote( $bugnote_id ) {

	global $db;
    $bugnote_tbl        = BUG_NOTE_TBL;
	$f_bugnote_id		= BUG_NOTE_ID;
	$f_bugnote_bug_id	= BUG_NOTE_BUG_ID;
	$f_bug_id			= BUG_NOTE_BUG_ID;
    $f_author			= BUG_NOTE_AUTHOR;
	$f_date_created		= BUG_NOTE_DATE_CREATED;
	$f_bugnote			= BUG_NOTE_DETAIL;

	$q = "SELECT
			$f_bugnote_id,
			$f_bugnote_bug_id,
			$f_author,
			$f_date_created,
			$f_bugnote
		  FROM
			$bugnote_tbl
		  WHERE
			$f_bugnote_id = '$bugnote_id'";

	$rs = db_query($db, $q);
	$row = db_fetch_row($db, $rs);

    return $row;
}

function bug_get_bug_id_from_bugnote( $bugnote_id ) {

	global $db;
    $bugnote_tbl        = BUG_NOTE_TBL;
	$f_bugnote_id		= BUG_NOTE_ID;
	$f_bug_id			= BUG_NOTE_BUG_ID;

	$q = "SELECT
			$f_bug_id
		  FROM
			$bugnote_tbl
		  WHERE
			$f_bugnote_id = '$bugnote_id'";

	$bug_id = db_get_one( $db, $q );

	return $bug_id;

}

# ----------------------------------------------------------------------
# Edit a specific bugnote for a given bug
# INPUT:
#   BugnoteID
# OUTPUT:
#   Array containing the details for the bugnote
# ----------------------------------------------------------------------
function bug_edit_bugnote( $bug_id, $bugnote_id, $bugnote ) {

	global $db;
    $bugnote_tbl        = BUG_NOTE_TBL;
	$f_bugnote_id		= BUG_NOTE_ID;
	$f_bugnote_bug_id	= BUG_NOTE_BUG_ID;
	$f_bug_id			= BUG_NOTE_BUG_ID;
    $f_author			= BUG_NOTE_AUTHOR;
	$f_date_created		= BUG_NOTE_DATE_CREATED;
	$f_bugnote			= BUG_NOTE_DETAIL;

	$q = "UPDATE $bugnote_tbl
		  SET
			  $f_bugnote = '$bugnote'
		  WHERE
			  $f_bugnote_id = '$bugnote_id'";

	db_query($db, $q);

	# Log update in history
	$bugnote_id = util_pad_id( $bugnote_id );
	bug_history_log_event( $bug_id, lang_get('edit_bugnote'), '', $bugnote_id );

}

# ----------------------------------------------------------------------
# Delete a note from the Bugnotes table
# INPUT:
#   BugNote ID
# OUTPUT:
#   None on success
# ----------------------------------------------------------------------
function bug_delete_bugnote( $bugnote_id ) {

	global $db;
    $bugnote_tbl    = BUG_NOTE_TBL;
	$f_bugnote_id	= BUG_NOTE_ID;
	$bug_id			= bug_get_bug_id_from_bugnote( $bugnote_id ); # get bug_id for history

	$q = "DELETE FROM $bugnote_tbl WHERE $f_bugnote_id = '$bugnote_id'";

	db_query( $db, $q );

	# Log update in history
	$bugnote_id = util_pad_id( $bugnote_id );
	bug_history_log_event( $bug_id, lang_get('delete_bugnote'), '', $bugnote_id );

}

# ----------------------------------------------------------------------
# Delete all notes from the Bugnotes table associated to a bug
# INPUT:
#   BugID
# OUTPUT:
#   None on success
# ----------------------------------------------------------------------
function bug_delete_all_bugnotes( $bug_id ) {

	global $db;
    $bugnote_tbl        = BUG_NOTE_TBL;
	$f_bug_id			= BUG_NOTE_BUG_ID;

	$q = "DELETE FROM $bugnote_tbl WHERE $f_bug_id = '$bug_id'";

	db_query( $db, $q );
}


# ----------------------------------------------------------------------
# Delete all records in the bug assoc table when deleting a bug
# INPUT:
#		BugID
# ----------------------------------------------------------------------
function bug_delete_relationships( $bug_id ) {

	global $db;
	$bug_assoc_tbl	= BUG_ASSOC_TBL;
	$f_src_id		= BUG_ASSOC_SRC_ID;
	$f_dest_id		= BUG_ASSOC_DEST_ID;

	$q = "DELETE FROM
			$bug_assoc_tbl
		  WHERE
			$f_src_id = '$bug_id'
		  OR
			$f_dest_id = '$bug_id'";

	db_query( $db, $q );

}

# ----------------------------------------------------------------------
# Update the verify results table.  Remove bug_id from table when
# INPUT:
#		BugID
# ----------------------------------------------------------------------
function bug_delete_test_assoc( $bug_id ) {

	global $db;

	$bug_detail = bug_get_detail( $bug_id );
	$verify_id	= $bug_detail[BUG_TEST_VERIFY_ID];

	$field		= VERIFY_RESULTS_DEFECT_ID;
	$value		= "";

	if( !empty( $verify_id ) ) {
		results_update_verfication_record( $verify_id, $field, 0 );
	}

}

# ----------------------------------------------------------------------
# Retrieve all records from the history table for a given bug
#
# INPUT:
#		BugID
# OUTPUT:
#		An array containing all the history records.
# ----------------------------------------------------------------------
function bug_get_history( $bug_id ) {

	global $db;

	$history_tbl		= BUG_HISTORY_TBL;
	$f_history_id		= BUG_HISTORY_ID;
	$f_bug_id			= BUG_HISTORY_BUG_ID;
	$f_date_modified	= BUG_HISTORY_DATE;
	$f_user				= BUG_HISTORY_USER;
	$f_field			= BUG_HISTORY_FIELD;
	$f_old_value		= BUG_HISTORY_OLD_VALUE;
	$f_new_value		= BUG_HISTORY_NEW_VALUE;

	$q = "SELECT
			$f_history_id,
			$f_bug_id,
			$f_user,
			$f_date_modified,
			$f_field,
			$f_old_value,
			$f_new_value
		  FROM
			$history_tbl
		  WHERE
			$f_bug_id = '$bug_id'
		  ORDER BY $f_date_modified ASC";

	$rs = db_query($db, $q);
	$rows = db_fetch_array($db, $rs);

	return $rows;
}

# ----------------------------------------------------------------------
# Delete all records in the history table when deleting a bug
# INPUT:
#		BugID
# OUTPUT:
#		None on success
# ----------------------------------------------------------------------
function bug_delete_history( $bug_id ) {

	global $db;

	$history_tbl		= BUG_HISTORY_TBL;
	$f_bug_id			= BUG_HISTORY_BUG_ID;

	$q = "DELETE FROM
			$history_tbl
		  WHERE
			$f_bug_id = '$bug_id'";

	db_query( $db, $q );

}

# ----------------------------------------------------------------------
# Update the history table with the changes to the bug
# Do this only after the update to the bug is complete
# Only run the update if the value has changed (old_value != new_value)
# INPUT:
#		BugID
#		Field - the field that changed
#		Old Value
#		New Value
# OUTPUT:
#		An entry in the history table.
# ----------------------------------------------------------------------
function bug_history_log_event( $bug_id, $field, $old_value, $new_value ) {

	if( $old_value != $new_value ) {

		global $db;
		$tbl_history		= BUG_HISTORY_TBL;
		$f_history_id		= BUG_HISTORY_ID;
		$f_bug_id			= BUG_HISTORY_BUG_ID;
		$f_date_modified	= BUG_HISTORY_DATE;
		$f_user				= BUG_HISTORY_USER;
		$f_field			= BUG_HISTORY_FIELD;
		$f_old_value		= BUG_HISTORY_OLD_VALUE;
		$f_new_value		= BUG_HISTORY_NEW_VALUE;

		$username			= session_get_username();
		$current_date		= date_get_short_dt();

		# TREAT SUMMARY AND DESCRIPTION SEPERATELY
		switch( $field ) {

			case BUG_SUMMARY:
				$old_value = '';
				$new_value = '';
				$field     = lang_get('summary_updated');
			break;
			case BUG_DESCRIPTION:
				$old_value = '';
				$new_value = '';
				$field     = lang_get('description_updated');
			break;
		}

		$q = "INSERT INTO $tbl_history
			  ( $f_bug_id, $f_date_modified, $f_user, $f_field, $f_old_value, $f_new_value )
			  VALUES
			  ('$bug_id', '$current_date', '$username', '$field', '$old_value', '$new_value' )";
		//print$q;
		db_query($db, $q);
	}

}

# ----------------------------------------------------------------------
# Update the history table with the changes to the bug
# This function is designed to deal with category, component, and other fields that
# require some extra work.
# INPUT:
#		BugID
#		Field - the field that changed
#		Old Value
#		New Value
# OUTPUT:
#		An entry in the history table.
# ----------------------------------------------------------------------
function bug_history_log_event_special( $bug_id, $field, $old_value, $new_value ) {

	if( ($old_value != $new_value) && $new_value != ''  ) {

		global $db;
		$tbl_history		= BUG_HISTORY_TBL;
		$f_history_id		= BUG_HISTORY_ID;
		$f_bug_id			= BUG_HISTORY_BUG_ID;
		$f_date_modified	= BUG_HISTORY_DATE;
		$f_user				= BUG_HISTORY_USER;
		$f_field			= BUG_HISTORY_FIELD;
		$f_old_value		= BUG_HISTORY_OLD_VALUE;
		$f_new_value		= BUG_HISTORY_NEW_VALUE;

		$username			= session_get_username();
		$current_date		= date_get_short_dt();

		switch( $field ) {
			case BUG_CATEGORY:
				$old_value = bug_get_category( $old_value );
				$new_value = bug_get_category( $new_value );
			break;
			case BUG_COMPONENT:
				$old_value = bug_get_component( $old_value );
				$new_value = bug_get_component( $new_value );
			break;
		}


		$q = "INSERT INTO $tbl_history
			  ( $f_bug_id, $f_date_modified, $f_user, $f_field, $f_old_value, $f_new_value )
			  VALUES
			  ('$bug_id', '$current_date', '$username', '$field', '$old_value', '$new_value' )";

		db_query($db, $q);
	}

}

# ----------------------------------------------------------------------
# Update the history table with the changes to the bug
# Do this only after the update to the bug is complete
# Only run the update if the value has changed (old_value != new_value)
# INPUT:
#		BugID
#		Field - the field that changed
#		Old Value
#		New Value
# OUTPUT:
#		An entry in the history table.
# ----------------------------------------------------------------------
function bug_history_log_new_bug( $bug_id ) {

	global $db;
	$tbl_history		= BUG_HISTORY_TBL;
	$f_bug_id			= BUG_HISTORY_BUG_ID;
	$f_date_modified	= BUG_HISTORY_DATE;
	$f_user				= BUG_HISTORY_USER;
	$f_field			= BUG_HISTORY_FIELD;
	$f_old_value		= BUG_HISTORY_OLD_VALUE;
	$f_new_value		= BUG_HISTORY_NEW_VALUE;

	$username			= session_get_username();
	$current_date		= date_get_short_dt();
	$field				= lang_get('new_bug');

	$q = "INSERT INTO $tbl_history
		  ( $f_bug_id, $f_date_modified, $f_user, $f_field, $f_old_value, $f_new_value )
		  VALUES
		  ( '$bug_id', '$current_date', '$username', '$field', '', '' )";

	db_query($db, $q);


}


# ----------------------------------------------------------------------
# Get list of test priorities
# OUTPUT:
#   Array of test available priorities
# ----------------------------------------------------------------------
function bug_get_priorities() {

    $low = lang_get('priority_low');
    $medium = lang_get('priority_medium');
    $high = lang_get('priority_high');

    $priorities_arr = array($low, $medium, $high, '');

    return $priorities_arr;
}

# ----------------------------------------------------------------------
# Get list of test severities
# OUTPUT:
#   Array of test available severities
# ----------------------------------------------------------------------
function bug_get_severities () {

	$low		= lang_get('severity_low');
    $medium		= lang_get('severity_medium');
    $high		= lang_get('severity_high');
	$critical	= lang_get('severity_critical');

	$severity_arr = array($low, $medium, $high, $critical, '');

	return $severity_arr;
}

# ----------------------------------------------------------------------
# Returns array of all bug statuses with optional blank value at end.
#
# OUTPUT:
#   array of all unique bug statuses.
# ----------------------------------------------------------------------
function bug_get_status( $blank=false ) {

	$bug_status = array(	'New',
							'Acknowledged',
							'Accepted',
							'Feedback',
							'Assigned',
							'Contructed',
							'Resolved',
							'Closed');

	if( $blank ) {
		$bug_status[] = "";
	}

	return $bug_status;
}

# ----------------------------------------------------------------------
# Returns array of all Closed Reason Codes with optional blank value at end.
#
# OUTPUT:
#   array of all unique bug closed reason codes.
# ----------------------------------------------------------------------
function bug_get_closed_reason_code( $blank=false ) {

	$closed_reason = array(	'Fixed',
							'Duplicate',
							'Rejected',
							'No Action' );

	if( $blank ) {
		$closed_reason[] = "";
	}

	return $closed_reason;
}
# ----------------------------------------------------------------------
# Returns array containing the phase of discovery with optional blank value at end.
#
# OUTPUT:
#   array containing phase of discovery.
# ----------------------------------------------------------------------
function bug_get_discovery_period( $blank=false ) {

	$discovery_period = array( 'Development',
							   'Test',
							   'Training',
							   'Production' );

	if( $blank ) {
		$discovery_period[] = "";
	}

	return $discovery_period;
}

function bug_get_relationship_types( $blank=false ) {

	$relationships = array( BUG_RELATED => lang_get('related_to'),
							BUG_CHILD => lang_get('child_of'),
							BUG_PARENT => lang_get('parent_of') );

	if( $blank ) {
		$relationships[] = "";
	}

	return $relationships;
}

# ----------------------------------------------------------------------
# Get the values from the BugCategory table
# OUTPUT:
#   array of all unique bug categories.
# ----------------------------------------------------------------------
function bug_get_categories($project_id, $blank=false) {

	global $db;

	$types = array();

	$bug_tbl 					= BUG_TBL;
	$f_bug_id 					= $bug_tbl .".". BUG_ID;
	$f_bug_proj_id				= $bug_tbl .".". BUG_PROJECT_ID;
	$f_bug_category		 		= $bug_tbl .".". BUG_CATEGORY;

	$category_tbl				= BUG_CATEGORY_TBL;
	$f_category_id				= $category_tbl .".". CATEGORY_ID;
	$f_category_proj_id			= $category_tbl .".". CATEGORY_PROJECT_ID;
	$f_category_name			= $category_tbl .".". CATEGORY_NAME;

	$q = "SELECT DISTINCT
			$f_category_id,
			$f_category_name
		 FROM $category_tbl
		 WHERE $f_category_proj_id = $project_id
		 ORDER BY $f_category_name ASC";

	#print"$q<br>";

	$rs = db_query($db, $q);

	while( $row = db_fetch_row($db, $rs) ) {

		$categories[$row[CATEGORY_ID]] = $row[CATEGORY_NAME];
	}

	if($blank) {

		$categories[""] = "";
	}

	return $categories;

}

# ----------------------------------------------------------------------
# Return the Category name for a given category id
# This function is used primarily to populate the history table
# OUTPUT:
#   The category name
# ----------------------------------------------------------------------
function bug_get_category( $category_id ) {

	if( $category_id == '0' ) { # the default value in the database. Doesn't map to a category
		return;
	}

	global $db;
	$category_tbl				= BUG_CATEGORY_TBL;
	$f_category_id				= $category_tbl .".". CATEGORY_ID;
	$f_category_name			= $category_tbl .".". CATEGORY_NAME;

	$q = "SELECT
			$f_category_name
		  FROM
			$category_tbl
		  WHERE
			$f_category_id = $category_id";

	$category = db_get_one( $db, $q );

	return $category;


}

# ----------------------------------------------------------------------
# Get the values from the BugComponent table
# OUTPUT:
#   array of all unique bug components.
# ----------------------------------------------------------------------
function bug_get_components($project_id, $blank=false) {

	global $db;

	$types = array();

	$bug_tbl 					= BUG_TBL;
	$f_bug_id 					= $bug_tbl .".". BUG_ID;
	$f_bug_proj_id				= $bug_tbl .".". BUG_PROJECT_ID;
	$f_bug_category		 		= $bug_tbl .".". BUG_CATEGORY;

	$component_tbl				= BUG_COMPONENT_TBL;
	$f_component_id				= $component_tbl .".". COMPONENT_ID;
	$f_component_proj_id		= $component_tbl .".". COMPONENT_PROJECT_ID;
	$f_component_name			= $component_tbl .".". COMPONENT_NAME;

	$q = "SELECT DISTINCT
			$f_component_id,
			$f_component_name
		 FROM $component_tbl
		 WHERE $f_component_proj_id = $project_id
		 ORDER BY $f_component_name ASC";

	#print"$q<br>";

	$rs = db_query($db, $q);

	while( $row = db_fetch_row($db, $rs) ) {

		$components[$row[COMPONENT_ID]] = $row[COMPONENT_NAME];
	}

	if($blank) {

		$components[""] = "";
	}

	return $components;

}

# ----------------------------------------------------------------------
# Return the Component name for a given component id
# This function is used primarily to populate the history table
# OUTPUT:
#   The category name
# ----------------------------------------------------------------------
function bug_get_component( $component_id ) {

	if( $component_id == '0' ) { # the default value for int(8) which doesn't map to a component
		return;
	}

	global $db;
	$component_tbl				= BUG_COMPONENT_TBL;
	$f_component_id				= $component_tbl .".". COMPONENT_ID;
	$f_component_name			= $component_tbl .".". COMPONENT_NAME;

	$q = "SELECT
			$f_component_name
		  FROM
			$component_tbl
		  WHERE
			$f_component_id = $component_id";

	$component = db_get_one( $db, $q );

	return $component;


}

# ----------------------------------------------------------------------
# Attach a user to a bug
# ----------------------------------------------------------------------
function bug_monitor_attach_user($bug_id, $user_id=null) {

	global $db;

	# If user id is not specified, then take it from the session
	if( !$user_id ) {
		$s_user_properties		= session_get_user_properties();
		$user_id				= $s_user_properties['user_id'];
	}

	$bug_tbl 					= BUG_TBL;
	$f_bug_id 					= $bug_tbl .".". BUG_ID;
	$f_bug_proj_id				= $bug_tbl .".". BUG_PROJECT_ID;
	$f_bug_category		 		= $bug_tbl .".". BUG_CATEGORY;

	$bug_monitor_tbl			= BUG_MONITOR_TBL;
	$f_bug_monitor_id			= $bug_monitor_tbl .".". BUG_MONITOR_ID;
	$f_bug_monitor_user_id		= $bug_monitor_tbl .".". BUG_MONITOR_USER_ID;
	$f_bug_monitor_bug_id		= $bug_monitor_tbl .".". BUG_MONITOR_BUG_ID;

	# Check if the record already exists
	$q = "	SELECT $f_bug_monitor_user_id
			FROM $bug_monitor_tbl
			WHERE
				$f_bug_monitor_user_id = '$user_id'
			AND
				$f_bug_monitor_bug_id = '$bug_id'";

	$rs = db_query($db, $q);
	$num_rows = db_num_rows($db, $rs);

	# Duplicate values will not be added to the database when an
	# unique index is created on user_id and bug_id. The IGNORE statement
	# stops an error being generated when an attempt to insert a duplicate
	# record is made
	/*
	$q = "INSERT IGNORE INTO $bug_monitor_tbl
				($f_bug_monitor_user_id, $f_bug_monitor_bug_id)
			  VALUES
				($user_id, $bug_id)";
	*/
	if( $num_rows < 1 ) {
		$q = "INSERT INTO $bug_monitor_tbl
				($f_bug_monitor_user_id, $f_bug_monitor_bug_id)
			  VALUES
				($user_id, $bug_id)";

		db_query($db, $q);
	}

}

# ----------------------------------------------------------------------
# Detach a user from a bug
# ----------------------------------------------------------------------
function bug_monitor_detach_user($user_id, $bug_id) {

	global $db;

	$bug_tbl 					= BUG_TBL;
	$f_bug_id 					= $bug_tbl .".". BUG_ID;
	$f_bug_proj_id				= $bug_tbl .".". BUG_PROJECT_ID;
	$f_bug_category		 		= $bug_tbl .".". BUG_CATEGORY;

	$bug_monitor_tbl			= BUG_MONITOR_TBL;
	$f_bug_monitor_id			= $bug_monitor_tbl .".". BUG_MONITOR_ID;
	$f_bug_monitor_user_id		= $bug_monitor_tbl .".". BUG_MONITOR_USER_ID;
	$f_bug_monitor_bug_id		= $bug_monitor_tbl .".". BUG_MONITOR_BUG_ID;

	$q = "	DELETE FROM $bug_monitor_tbl
			WHERE
				$f_bug_monitor_user_id = '$user_id',
				$f_bug_monitor_bug_id = '$bug_id'";

	db_query($db, $q);
}


# ----------------------------------------------------------------------
# Get bug email addresses
# INPUT:
#   BugID, Notify Type
# OUTPUT:
#   email addresses
# ----------------------------------------------------------------------
function bug_email_collect_recipients( $bug_id, $notify_type ) {

	global $db;
	$receive_own_email = BUG_EMAIL_ON_OWN_ACTIONS;

	$recipients 		= "";
	$recipient_ids	 	= array();
	$user_ids			= array();

	# Get the current project and user ids
	$project_id 		= bug_get_field_value( $bug_id, BUG_PROJECT_ID );
	$s_user_properties 	= session_get_user_properties() ;
	$user_id 			= $s_user_properties['user_id'];

	$monitor_tbl			= BUG_MONITOR_TBL;
	$f_monitor_user_id		= BUG_MONITOR_USER_ID;
	$f_monitor_bug_id		= BUG_MONITOR_BUG_ID;

	$proj_user_tbl			= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_id			= PROJ_USER_USER_ID;
	$f_proj_user_proj_id	= PROJ_USER_PROJ_ID;

	# Get user_ids for the bug
	$q = "	SELECT DISTINCT $f_monitor_user_id
			FROM $monitor_tbl
			WHERE $f_monitor_bug_id = $bug_id";
	$rs = db_query( $db, $q );
	
	
	while( $row = db_fetch_row( $db, $rs ) ) {
		
		# check to see if we should add the user performing the action to the list of recipients
		if( $row[BUG_MONITOR_USER_ID] == $user_id && !$receive_own_email) {
	
			continue;
		}
		else {
			$user_ids[] = $row[BUG_MONITOR_USER_ID];
		}
	}


	# Loop through each user_id and find out if the user wants to receive email for the action ($notify_type)
	foreach( $user_ids as $id ) {

		switch( $notify_type ) {
			
			case "update_status":
				$f_email_field = PROJ_USER_EMAIL_STATUS_BUG;
			break;
			case "add_bugnote":
				$f_email_field = PROJ_USER_EMAIL_BUGNOTE_BUG;
			break;
			case "assign_bug":
				$f_email_field = PROJ_USER_EMAIL_ASSIGNED_BUG;
			break;
			case "update_bug":
				$f_email_field = PROJ_USER_EMAIL_UPDATE_BUG;
			break;
		}
		
		# Find out if the user wants email for the specified action
		$q_assoc = "SELECT $f_email_field
					FROM $proj_user_tbl
					WHERE $f_proj_user_proj_id = '$project_id'
					AND $f_proj_user_id = '$id'";
		$rs_assoc = db_query( $db, $q_assoc );

		while( $row_assoc = db_fetch_row( $db, $rs_assoc ) ) {
			
			# If the project_user_assoc preference is Yes, add the user to the list of recipients
			if( $row_assoc[$f_email_field] == 'Y' ) {
				
				$recipient_ids[] = $id;
			}
		}
	}

	$recipients = user_get_email_by_user_id( $recipient_ids );

	return $recipients;
	
}


function bug_email($project_id, $bug_id, $recipients, $action) {

	# Don't send email if the preference it turned off in propterities_inc.php
	if( !SEND_EMAIL_NOTIFICATION ) {
		return;
	}

	# Link to the bug detail page
	$generic_url = RTH_URL."login.php?project_id=$project_id&page=bug_detail_page.php&bug_id=$bug_id";

	$username				= session_get_username();
	$project_name			= session_get_project_name();
	$bug_id					= util_pad_id($bug_id);

	$user_details			= user_get_name_by_username($username);
	$first_name				= $user_details[USER_FNAME];
	$last_name				= $user_details[USER_LNAME];

	$row_bug				= bug_get_detail( $bug_id );

	$category    			= $row_bug[CATEGORY_NAME];
	$bug_project_id			= $row_bug[BUG_PROJECT_ID];
	$component				= $row_bug[COMPONENT_NAME];
	$priority        		= $row_bug[BUG_PRIORITY];
	$severity        		= $row_bug[BUG_SEVERITY];
	$bug_status				= $row_bug[BUG_STATUS];
	$reporter	     		= $row_bug[BUG_REPORTER];
	$reported_date   		= $row_bug[BUG_REPORTED_DATE];
	$assigned_to      		= $row_bug[BUG_ASSIGNED_TO];
	$assigned_to_developer	= $row_bug[BUG_ASSIGNED_TO_DEVELOPER];
	$closed		       		= $row_bug[BUG_CLOSED];
	$closed_date	 		= $row_bug[BUG_CLOSED_DATE];
	$test_verify_id       	= $row_bug[BUG_TEST_VERIFY_ID];
	$req_version_id    		= $row_bug[BUG_REQ_VERSION_ID];
	$found_in_release  		= $row_bug[BUG_FOUND_IN_RELEASE];
	$assign_to_release 		= $row_bug[BUG_ASSIGN_TO_RELEASE];
	$imp_in_release 		= $row_bug[BUG_IMPLEMENTED_IN_RELEASE];
	$discovery_period 		= $row_bug[BUG_DISCOVERY_PERIOD];
	$summary		 		= $row_bug[BUG_SUMMARY];
	$description			= $row_bug[BUG_DESCRIPTION];

	$message = "";

	# CREATE EMAIL SUBJECT AND MESSAGE
	switch($action) {
		
		case "new_bug":
			$subject = "RTH: New defect entered for $project_name - ID: $bug_id";
			$message = "A New Defect has been entered by $first_name $last_name\r". NEWLINE;
			break;

		case "update_status":

			$subject = "RTH: Status of Bug in $project_name has changed to $bug_status - ID: $bug_id";
			$message = "The status of bug  has been changed to $bug_status by $first_name $last_name\n". NEWLINE;
			break;

		case "add_bugnote":

			$subject = "RTH: A bugnote was added to a Bug in $project_name - ID: $bug_id";
			$message = "A bugnote has been added by $first_name $last_name\n". NEWLINE;
			break;

		case "assign_bug":

			$subject = "RTH: Bug in $project_name has been assigned - ID: $bug_id";
			$message = "The bug  has been assigned by $first_name $last_name\n". NEWLINE;
			break;

		case "add_relationship":
			$subject = "RTH: New relationship created for bug in project: $project_name - ID: $bug_id";
			$message = "A new relationship was created by $first_name $last_name\n". NEWLINE;
			break;

		case "update_bug":
			$subject = "RTH: Bug in project: $project_name has been updated - ID: $bug_id";
			$message = "Bug has been updated by $first_name $last_name\n". NEWLINE;
			break;

		case"delete":
			$subject = "RTH: Bug Deleted in $project_name";
			$message = "Bug $req_name has been deleted by $first_name $last_name\n". NEWLINE;
			break;

	}

	# Generic link to requirement detail page if the $url variable has been set
	$message .= "Click the following link to view the Defect:\r". NEWLINE;
	$message .= "$generic_url\r\n\r". NEWLINE;
	
	$message		.= lang_get('project_name') .": $project_name\r". NEWLINE;
	$message		.= lang_get('bug_category') .": $category\r". NEWLINE;
	$message		.= lang_get('bug_component') .": $component\r". NEWLINE;
	$message		.= lang_get('bug_priority') .": $priority\r". NEWLINE;
	$message		.= lang_get('bug_severity') .": $severity\r". NEWLINE;
	$message		.= lang_get('bug_status') .": $bug_status\r". NEWLINE;
	$message		.= lang_get('reported_by') .": $reporter\r". NEWLINE;
	$message		.= lang_get('reported_date') .": $reported_date\r". NEWLINE;
	$message		.= lang_get('assigned_to') .": $assigned_to\r". NEWLINE;
	$message		.= lang_get('assigned_to_developer') .": $assigned_to_developer\r". NEWLINE;
	$message		.= lang_get('found_in_release') .": $found_in_release\r". NEWLINE;
	$message		.= lang_get('assigned_to_release') .": $assign_to_release\r". NEWLINE;
	$message		.= lang_get('bug_summary') .": $summary\r\n\r". NEWLINE;
	$message		.= lang_get('bug_description') .": $description\r". NEWLINE;

	# Convert any html entities stored in the DB back to characters.
	$message = util_unhtmlentities($message);

	email_send($recipients, $subject, $message);
}

function bug_get_uploaded_documents($bug_id) {

	global $db;
	$bug_file_tbl			= BUG_FILE_TBL;
	$f_bug_file_id			= BUG_FILE_ID;
	$f_bug_id				= BUG_FILE_BUG_ID;
	$f_uploaded_by			= BUG_FILE_UPLOAD_DATE;
	$f_uploaded_date		= BUG_FILE_UPLOAD_BY;
	$f_display_name			= BUG_FILE_DISPLAY_NAME;
	$f_file_name			= BUG_FILE_NAME;

	$q = "SELECT 
				$f_bug_file_id, $f_bug_id, $f_uploaded_by, $f_uploaded_date, $f_display_name, $f_file_name
		  FROM 
				$bug_file_tbl
		  WHERE 
				$f_bug_id = '$bug_id'";

	$rs = db_query( $db, $q );
    $num = db_num_rows( $db, $rs );

    $row = array();

    for ( $i=0 ; $i < $num ; $i++ ) {
        array_push( $row, db_fetch_row( $db, $rs ) );
    }

    return $row;
}

# ------------------------------------
# $Log: bug_api.php,v $
# Revision 1.13  2007/02/03 10:25:30  gth2
# no message
#
# Revision 1.12  2006/10/05 02:42:18  gth2
# adding file upload to the bug page - gth
#
# Revision 1.11  2006/09/27 05:35:14  gth2
# adding Mantis integration - gth
#
# Revision 1.10  2006/08/05 22:31:56  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.9  2006/08/05 22:07:59  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.8  2006/06/30 00:55:42  gth2
# removing &$db from api files - gth
#
# Revision 1.7  2006/06/24 14:34:15  gth2
# updating changes lost with cvs problem.
#
# Revision 1.6  2006/04/09 15:53:13  gth2
# moving code that writes to verifyresults after email code  - th
#
# Revision 1.5  2006/02/27 17:24:56  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.4  2006/02/24 11:33:31  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.3  2006/02/09 12:34:26  gth2
# changing db field names for consistency - gth
#
# Revision 1.2  2006/01/20 02:36:03  gth2
# enable export to excel functionaltiy - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# ------------------------------------

?>