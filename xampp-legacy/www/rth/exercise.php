<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Group Action Page
#
# $RCSfile: exercise.php,v $    $Revision: 1.3 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_name 		= session_get_project_name();
$page				= basename(__FILE__);
$row_style			= '';

$project_properties     = session_get_project_properties();

$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

if( isset($_POST['filter_jump'] )  && $_POST['filter_jump'] != '' ) {
	html_redirect( "$detail_page?bug_id=$_POST[filter_jump]" );
}
else {
	$filter_jump="";
}

$order_by 		= BUG_ID;
$order_dir		= "ASC";
$page_number	= 1;

$s_display_options 		= session_set_display_options("bug", $_POST);
$order_by				= $s_display_options['order_by'];
$order_dir				= $s_display_options['order_dir'];
$page_number			= $s_display_options['page_number'];

$filter_per_page		= $s_display_options['filter']['per_page'];
$filter_bug_status		= $s_display_options['filter']['status'];
$filter_bug_category	= $s_display_options['filter']['category'];
$filter_bug_component	= $s_display_options['filter']['component'];
$filter_reported_by		= $s_display_options['filter']['reported_by'];
$filter_assigned_to		= $s_display_options['filter']['assigned_to'];
$filter_assigned_to_dev	= $s_display_options['filter']['assigned_to_developer'];
$filter_found_in_rel	= $s_display_options['filter']['found_in_release'];
$filter_assigned_to_rel	= $s_display_options['filter']['assigned_to_release'];
$filter_view_closed		= $s_display_options['filter']['view_closed'];
$filter_search			= $s_display_options['filter']['bug_search'];

html_window_title();
html_print_body();
html_page_title("Exercise");
html_page_header( $db, "no project" );
html_print_menu();

error_report_check( $_GET );

print"<br>";
print"<form method='post' action='$page' name='bug_form'>". NEWLINE;
print"<div align=center>". NEWLINE;

html_print_bug_filter(	$project_id,
						$filter_bug_status,
						$filter_bug_category,
						$filter_bug_component,
						$filter_reported_by,
						$filter_assigned_to,
						$filter_assigned_to_dev,
						$filter_found_in_rel,
						$filter_assigned_to_rel,
						$filter_per_page,
						$filter_view_closed,
						$filter_search,
						$filter_jump);


print"<br>". NEWLINE;



util_set_order_by($order_by, $_GET);
util_set_order_dir($order_dir, $_GET);
util_set_page_number($page_number, $_GET);

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);

	
function exercise($project_id, $page_number=0, $order_by=BUG_ID, $order_dir="DESC",
				 $bug_status="", $category="", $component="", $reported_by="", $assigned_to="", $assigned_to_developer="", $found_in_release="", $assigned_to_release=null, $per_page=null, $view_closed='No', $search="", $jump="", $csv_name=null) 
				 {

	$tbl_bug						= BUG_TBL;
	$f_bug_id						= $tbl_bug	 .".". BUG_ID;
	$f_bug_project_id				= $tbl_bug	 .".". BUG_PROJECT_ID;
	$f_bug_category					= $tbl_bug	 .".". BUG_CATEGORY;
	$f_bug_component				= $tbl_bug	 .".". BUG_COMPONENT;
	$f_bug_priority					= $tbl_bug	 .".". BUG_PRIORITY;
	$f_bug_severity					= $tbl_bug	 .".". BUG_SEVERITY;
	$f_bug_closed_reason_code		= $tbl_bug	 .".". BUG_CLOSED_REASON_CODE;
	$f_bug_status					= $tbl_bug	 .".". BUG_STATUS;
	$f_bug_reporter					= $tbl_bug	 .".". BUG_REPORTER;
	$f_bug_reported_date			= $tbl_bug	 .".". BUG_REPORTED_DATE;
	$f_bug_assigned_to				= $tbl_bug	 .".". BUG_ASSIGNED_TO;
	$f_bug_assigned_to_developer	= $tbl_bug	 .".". BUG_ASSIGNED_TO_DEVELOPER;
	$f_bug_closed					= $tbl_bug	 .".". BUG_CLOSED;
	$f_bug_closed_date				= $tbl_bug	 .".". BUG_CLOSED_DATE;
	$f_bug_test_verify_id			= $tbl_bug	 .".". BUG_TEST_VERIFY_ID;
	$f_bug_req_version_id			= $tbl_bug	 .".". BUG_REQ_VERSION_ID;
	$f_bug_found_in_release			= $tbl_bug	 .".". BUG_FOUND_IN_RELEASE;
	$f_bug_assign_to_release		= $tbl_bug	 .".". BUG_ASSIGN_TO_RELEASE;
	$f_bug_implemented_in_release	= $tbl_bug	 .".". BUG_IMPLEMENTED_IN_RELEASE;
	$f_bug_discovery_period			= $tbl_bug	 .".". BUG_DISCOVERY_PERIOD;
	$f_bug_summary					= $tbl_bug	 .".". BUG_SUMMARY;
	$f_bug_description				= $tbl_bug	 .".". BUG_DESCRIPTION;

	
	$q = "		SELECT 	
					$f_bug_id,
					$f_bug_project_id,
					$f_bug_category	, 
					$f_bug_component, 
					$f_bug_priority, 
					$f_bug_severity, 
					$f_bug_closed_reason_code, 
				 	$f_bug_status, 
				 	$f_bug_reporter, 
				 	$f_bug_reported_date, 
				 	$f_bug_assigned_to, 
				 	$f_bug_assigned_to_developer, 
				 	$f_bug_closed,
				 	$f_bug_closed_date, 
				 	$f_bug_test_verify_id, 
				 	$f_bug_req_version_id, 
				 	$f_bug_found_in_release, 
				 	$f_bug_assign_to_release, 
				 	$f_bug_implemented_in_release, 
				 	$f_bug_discovery_period, 
				 	$f_bug_summary, 
				 	$f_bug_description
				 FROM
				 	$tbl_bug";
				 
	$where_clause = " WHERE ";

	# STATUS
    if ( !empty($bug_status)  && $bug_status != 'all') {

        $where_clause = $where_clause." AND $f_bug_status = '$bug_status'";
    }
    # CATEGORY
    if ( !empty($category) && $category != 'all') {

        $where_clause = $where_clause." AND $f_bug_category = '$category'";
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

        $where_clause = $where_clause." AND $f_bug_assigned_to = '$assigned_to'";
    }
	# ASSIGNED TO DEVELOPER
    if ( !empty($assigned_to_developer) && $assigned_to_developer != 'all') {

        $where_clause = $where_clause." AND $f_bug_assigned_to_developer = '$assigned_to_developer'";
    }
    # FOUND IN RELEASE
    if ( !empty($found_in_release) && $found_in_release != 'all') {

        $where_clause = $where_clause." AND $f_bug_found_in_release = '$found_in_release'";
    }
	# ASSIGN TO RELEASE
    if ( !empty($assigned_to_release) && $assigned_to_release != 'all') {

        $where_clause = $where_clause." AND $f_assigned_to_release = '$assigned_to_release'";
    }

	# SEARCH
	if ( !empty($search) ) {
        $where_clause = $where_clause." AND ( ($f_summary LIKE '%$search%') OR ($f_description LIKE '%$search%') )";
    }

print$where_clause;

/*
	$q .= "	$where_clause
			ORDER BY $order_by $order_dir";
			
print$q;
*/			
	global $db;

	# Query results are stored in a record set 
	$rs = db_query( $db, $q);

	$rows = db_fetch_array( $db, db_query($db, $q) );

	return $rows;

}	


print"<form action=$page method=post>";


print"<table class=width100 rules=cols>". NEWLINE;;

	print"<tr>". NEWLINE;

	html_tbl_print_header( lang_get('bug_id'),	BUG_ID,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_project_id'), 	BUG_PROJECT_ID,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_category'), 	BUG_CATEGORY,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_component'), 	BUG_COMPONENT,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_priority'), BUG_PRIORITY,	$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_severity'), BUG_SEVERITY,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('closed_reason_code'),	BUG_CLOSED_REASON_CODE,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_status'),	BUG_STATUS,			$order_by, $order_dir );
	html_tbl_print_header( lang_get('reported_by'), 	BUG_REPORTER,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('reported_date'), 	BUG_REPORTED_DATE,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('assigned_to'), 	BUG_ASSIGNED_TO,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('assigned_to_developer'), BUG_ASSIGNED_TO_DEVELOPER,	$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_closed'), BUG_CLOSED,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_closed_date'), 	BUG_CLOSED_DATE,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('test_verification_id'), 	BUG_TEST_VERIFY_ID,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('req_version_id'), 	BUG_REQ_VERSION_ID,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('found_in_release'), BUG_FOUND_IN_RELEASE,	$order_by, $order_dir );
	html_tbl_print_header( lang_get('assigned_to_release'), BUG_ASSIGN_TO_RELEASE,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('implemented_in_release'),	BUG_IMPLEMENTED_IN_RELEASE,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('discovery_period'),	BUG_DISCOVERY_PERIOD,			$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_summary'), 	BUG_SUMMARY,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_description'), 	BUG_DESCRIPTION,		$order_by, $order_dir );

	print"</tr>". NEWLINE;


$bugs_rows = exercise($project_id, $page_number,$order_by, $order_dir, $filter_bug_status, 	$filter_bug_category ,
$filter_bug_component,
$filter_reported_by,
$filter_assigned_to,
$filter_assigned_to_dev,
$filter_found_in_rel,
$filter_assigned_to_rel,
$filter_view_closed,
$filter_search);
foreach( $bugs_rows as $bug_row ) {

	$row_style = html_tbl_alternate_bgcolor($row_style);

	$bug_id 			= $bug_row[BUG_ID];
	$project_id 		= $bug_row[BUG_PROJECT_ID];
	$category 			= $bug_row[BUG_CATEGORY];
	$component 			= $bug_row[BUG_COMPONENT];
	$priority 			= $bug_row[BUG_PRIORITY];
	$severity 			= $bug_row[BUG_SEVERITY];
	$closed_reason_code = $bug_row[BUG_SEVERITY];
	$status 			= $bug_row[BUG_STATUS];	
	$reporter 			= $bug_row[BUG_REPORTER];	
	$reporter_date 		= $bug_row[BUG_REPORTED_DATE];	
	$assigned_to		= $bug_row[BUG_ASSIGNED_TO];
	$assigned_to_developer = $bug_row[BUG_ASSIGNED_TO_DEVELOPER];
	$closed 			= $bug_row[BUG_CLOSED];
	$closed_date 		= $bug_row[BUG_CLOSED_DATE];
	$test_id 			= $bug_row[BUG_TEST_VERIFY_ID];
	$req_id 			= $bug_row[BUG_REQ_VERSION_ID];
	$found_in_release	= $bug_row[BUG_FOUND_IN_RELEASE];
	$assign_to_release 	= $bug_row[BUG_ASSIGN_TO_RELEASE];
	$implemented_in_release = $bug_row[BUG_IMPLEMENTED_IN_RELEASE];
	$discovery 			= $bug_row[BUG_DISCOVERY_PERIOD];
	$summary 			= $bug_row[BUG_SUMMARY];
	$description 		= $bug_row[BUG_DESCRIPTION];

	print"<tr class='$row_style'>". NEWLINE;


		print"<td>$bug_id </td>". NEWLINE;		
		print"<td>$project_id </td>". NEWLINE;		
		print"<td>$category </td>". NEWLINE;		
		print"<td>$component </td>". NEWLINE;		
		print"<td>$priority </td>". NEWLINE;		
		print"<td>$severity </td>". NEWLINE;		
		print"<td>$closed_reason_code </td>". NEWLINE;		
		print"<td>$status </td>". NEWLINE;		
		print"<td>$reporter </td>". NEWLINE;		
		print"<td>$reporter_date</td>". NEWLINE;		
		print"<td>$assigned_to </td>". NEWLINE;		
		print"<td>$assigned_to_developer </td>". NEWLINE;		
		print"<td>$closed </td>". NEWLINE;		
		print"<td>$closed_date </td>". NEWLINE;
		print"<td>$test_id </td>". NEWLINE;		
		print"<td>$req_id </td>". NEWLINE;		
		print"<td>$found_in_release </td>". NEWLINE;		
		print"<td>$assign_to_release </td>". NEWLINE;		
		print"<td>$implemented_in_release </td>". NEWLINE;		
		print"<td>$discovery </td>". NEWLINE;		
		print"<td>$summary </td>". NEWLINE;		
		print"<td>$description </td>". NEWLINE;


	print"</tr>". NEWLINE;



}


print"</table>". NEWLINE;
print"</form>". NEWLINE;


html_print_footer();


# ------------------------------------
# $Log: exercise.php,v $
# Revision 1.3  2006/08/05 22:08:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>