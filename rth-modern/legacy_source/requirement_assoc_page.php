<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Assoc Page
#
# $RCSfile: requirement_assoc_page.php,v $  $Revision: 1.4 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_btn']) ) {

	require_once("requirement_assoc_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'requirement_assoc_action.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

$row_style		= '';

$records		= "";

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];
$s_req_version_id	= $s_properties['req_version_id'];

$filter_per_page		= 100;
$filter_doc_type		= "";
$filter_status			= "";
$filter_area_covered	= "";
$filter_functionality	= "";
$filter_assign_release	= "";
$filter_show_versions	= "latest";
$filter_search			= "";
$filter_priority		= "";

$order_by 		= REQ_FILENAME;
$order_dir		= "ASC";
$page_number	= 1;

util_set_filter('per_page', $filter_per_page, $_POST);
util_set_filter('doc_type', $filter_doc_type, $_POST);
util_set_filter('status', $filter_status, $_POST);
util_set_filter('area_covered', $filter_area_covered, $_POST);
util_set_filter('functionality', $filter_functionality, $_POST);
util_set_filter('assign_release', $filter_assign_release, $_POST);
util_set_filter('requirement_search', $filter_search, $_POST);
util_set_filter('priority', $filter_priority, $_POST);

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);

$rows_children = requirement_get_children($s_req_id);
$selected_rows = array();

foreach($rows_children as $row_child) {

	$selected_rows[$row_child["uid"]] = "";
}


session_records(	"requirement_requirement_assoc",
					$selected_rows );

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get("req_req_assoc_page"));
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);

error_report_check( $_GET );

$row_requirement = requirement_get_detail( $project_id, $s_req_id, $s_req_version_id );

foreach( $row_requirement as $row_detail ) {

		$req_name			= $row_detail[REQ_FILENAME];
		$req_version_num	= $row_detail[REQ_VERS_VERSION];
		$req_status			= $row_detail[REQ_VERS_STATUS];
		$area_covered		= $row_detail[REQ_AREA_COVERAGE];
		$req_author			= $row_detail[REQ_VERS_UPLOADED_BY];
		$req_doc_type		= $row_detail[REQ_DOC_TYPE_NAME];
		$date_created		= $row_detail[REQ_VERS_TIMESTAMP];
		$locked_by			= $row_detail[REQ_LOCKED_BY];
		$locked_date		= $row_detail[REQ_LOCKED_DATE];
		$assigned_to		= $row_detail[REQ_VERS_ASSIGNED_TO];
		$record_or_file		= $row_detail[REQ_REC_FILE];
		$req_file_name		= $row_detail[REQ_VERS_FILENAME]; # Blank if record based
		$req_detail			= $row_detail[REQ_VERS_DETAIL]; # Blank if file based
		$req_priority		= $row_detail[REQ_PRIORITY];

}

print"<br>". NEWLINE;

print"<form method=post action='$page'>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<table class=width100 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='50%' nowrap class=grid-header-c>".lang_get('req_id')."</td>". NEWLINE;
print"<td width='50%' nowrap class=grid-header-c>".lang_get('req_name')."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td width='50%' class=grid-data-c><a href='requirement_detail_page.php?req_id=$s_req_id&amp;req_version_id=$s_req_version_id'>".sprintf( "%05s",trim( $s_req_id ) )."</a></td>". NEWLINE;
print"<td width='50%' class=grid-data-c>$req_name</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br>";

html_print_requirements_filter(	$project_id,
								$filter_doc_type,
								$filter_status,
								$filter_area_covered,
								$filter_functionality,
								$filter_assign_release,
								$filter_per_page,
								$filter_show_versions,
								$filter_search,
								$filter_priority);

print"<br>". NEWLINE;

print"</div>". NEWLINE;

$rows_requirement = requirement_get_edit_children(  $project_id, 
													$s_req_id, 
													$page_number, 
													$order_by, 
													$order_dir, 
													$filter_doc_type, 
													$filter_status,
													$filter_area_covered, 
													$filter_functionality,
													$filter_assign_release, 
													$filter_show_versions,
													$filter_search,
													$filter_priority,
													$filter_per_page );

################################################################################
# Testset table

if($rows_requirement) {


	print"<div align=center>". NEWLINE;

	print"<table class=width100 rules=cols>". NEWLINE;

	# Table headers
	print"<tr class=tbl_header>". NEWLINE;
	print"<th></th>";
	html_tbl_print_header( lang_get('req_id'), REQ_ID, $order_by, $order_dir );
	html_tbl_print_header( "" );
	html_tbl_print_header( lang_get('req_name'), REQ_FILENAME, $order_by, $order_dir );
	html_tbl_print_header( lang_get('req_detail'), REQ_VERS_DETAIL, $order_by, $order_dir );
	html_tbl_print_header( lang_get('req_type'), REQ_DOC_TYPE_NAME, $order_by, $order_dir );
	html_tbl_print_header( lang_get('status'), REQ_VERS_STATUS, $order_by, $order_dir );
	html_tbl_print_header( lang_get('req_area'), REQ_AREA_COVERAGE, $order_by, $order_dir );
	html_tbl_print_header( lang_get('functionality') );
	html_tbl_print_header( lang_get('req_locked_by'), REQ_LOCKED_BY, $order_by, $order_dir );
	html_tbl_print_header( lang_get('req_locked_date'),	REQ_LOCKED_DATE, $order_by, $order_dir );
	print"</tr>". NEWLINE;

	foreach($rows_requirement as $row_requirement) {

		$req_id					= $row_requirement[REQ_ID];
		$req_version_id			= $row_requirement[REQ_VERS_UNIQUE_ID];

		if( session_records_ischecked("requirement_requirement_assoc", $req_id) ) {

			$checked = "checked";
		} else {

			$checked = "";
		}

		# Build list of records
		if( empty($records) ) {
			$records = $req_id." => ''";
		} else {
			$records .= ", ".$req_id." => ''";
		}

		$row_style = html_tbl_alternate_bgcolor($row_style);

		$rows_functions = requirement_get_functionality($project_id, $row_requirement[REQ_ID]);

		# Rows
		print"<tr class='$row_style'>". NEWLINE;
		print"<td><input type='checkbox' name=row_$req_id $checked></td>";
		print"<td><a href='requirement_detail_page.php?req_id=$req_id&amp;req_version_id=$req_version_id'>".util_pad_id($row_requirement[REQ_ID])."</a></td>". NEWLINE;
		print"<td>".html_file_type( $row_requirement[REQ_VERS_FILENAME] )."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_FILENAME]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_VERS_DETAIL]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_DOC_TYPE_NAME]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_VERS_STATUS]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_AREA_COVERAGE]."</td>". NEWLINE;
		print"<td class='tbl-l'>";
		foreach($rows_functions as $key => $value) {

			print$value."<br>";
		}
		print"</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_LOCKED_BY]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_LOCKED_DATE]."</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	print"</table>". NEWLINE;

	print"</div>". NEWLINE;

	//print lang_get("update").": &nbsp;". NEWLINE;
	if( session_use_javascript() ) {
		print"<input id=select_all type=checkbox name=thispage onClick='checkAll( this )'>". NEWLINE;
		print"<label for=select_all>".lang_get("select_all")."</label>";
		print"&nbsp;". NEWLINE;
	}

	print"<div align=center>";
	print"<br><input type=submit name=submit_btn value='".lang_get("update")."'>". NEWLINE;
	print"</div>";

} else {

	print lang_get("no_requirements");
}

print"<input type=hidden name=records value=\"$records\">". NEWLINE;
print"</form>". NEWLINE;

html_print_footer();


# ---------------------------------------------------------------------
# $Log: requirement_assoc_page.php,v $
# Revision 1.4  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2006/01/04 22:58:29  gth2
# fixing bug with filter on req-to-req assoc page - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>