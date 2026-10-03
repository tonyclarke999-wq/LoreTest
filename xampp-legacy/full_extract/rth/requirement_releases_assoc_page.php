<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Release Assoc Page
#
# $RCSfile: requirement_releases_assoc_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_btn']) ) {

	require_once("requirement_releases_assoc_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$project_name 	= session_get_project_name();
$page			= basename(__FILE__);
$row_style 		= '';
$records		= '';

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];
$s_req_version_id	= $s_properties['req_version_id'];

$order_by 		= RELEASE_DATE_RECEIVED;
$order_dir		= "ASC";
$page_number	= 1;

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);

html_window_title();
html_print_body();
html_page_title($project_name ." - " . lang_get('req_assoc_releases_page') );
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
		//$assign_to_rel		= $row_detail[REQ_VERS_ASSIGN_RELEASE];
		$locked_by			= $row_detail[REQ_LOCKED_BY];
		$locked_date		= $row_detail[REQ_LOCKED_DATE];
		$assigned_to		= $row_detail[REQ_VERS_ASSIGNED_TO];
		$record_or_file		= $row_detail[REQ_REC_FILE];
		$req_file_name		= $row_detail[REQ_VERS_FILENAME]; # Blank if record based
		$req_detail			= $row_detail[REQ_VERS_DETAIL]; # Blank if file based

}

print"<br>". NEWLINE;

print"<form method=post action='$page'>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<table class=width100 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_id')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_name')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_version')."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td width='33%' class=grid-data-c><a href='requirement_detail_page.php?req_id=$s_req_id'>".sprintf( "%05s",trim( $s_req_id ) )."</a></td>". NEWLINE;
print"<td width='33%' class=grid-data-c>$req_name</td>". NEWLINE;
print"<td width='33%' class=grid-data-c>$req_version_num</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</div>". NEWLINE;

print"<br>". NEWLINE;

# Get associated releases
$assoc_releases = array();
foreach( requirement_get_assoc_releases($s_req_version_id) as $row ) {
	$assoc_releases[] = $row[RELEASE_ID];
}

$release_details = admin_get_all_release_details_by_project( $project_id, null, $order_by, $order_dir );

if( !empty( $release_details ) ) {
	print"<div align=center>". NEWLINE;
	print"<table class=width100 rules=cols>". NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;
	print"<th></th>". NEWLINE;
	html_tbl_print_header( lang_get('release_name'), 	 RELEASE_NAME, 			$order_by, $order_dir );
	html_tbl_print_header( lang_get('rel_date_received'),RELEASE_DATE_RECEIVED, $order_by, $order_dir );
	html_tbl_print_header( lang_get('rel_description') );

	print"</tr>". NEWLINE;

	foreach($release_details as $release_detail ) {

		$release_id				= $release_detail[RELEASE_ID];
		$release_name			= $release_detail[RELEASE_NAME];
		$release_date_received	= date_trim_time( $release_detail[RELEASE_DATE_RECEIVED] );
		$release_description	= $release_detail[RELEASE_DESCRIPTION];

		$row_style = html_tbl_alternate_bgcolor( $row_style );

		if( util_array_value_search($release_id, $assoc_releases) ) {

			$checked = "checked";
		} else {

			$checked = "";
		}

		print"<tr class='$row_style'>". NEWLINE;
		print"<td><input type='checkbox' name='row_releases[$release_id]' $checked></td>". NEWLINE;
		print"<td class='tbl-l'>$release_name</td>". NEWLINE;
		print"<td class='tbl-c'>$release_date_received</td>". NEWLINE;
		print"<td class='tbl-c'>$release_description</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	print"</table>". NEWLINE;
	print"</div>". NEWLINE;

	if( session_use_javascript() ) {

		print"<input id=select_all type=checkbox name=thispage onClick='checkAll( this )'>". NEWLINE;
		print"<label for=select_all>".lang_get("select_all")."</label>";
	}

	print"<div align=center>";
	print"<input type=submit name=submit_btn value='".lang_get("edit")."'>";
	print"</div>";

} else {
	html_no_records_found_message( lang_get('no_releases') );
}

print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_releases_assoc_page.php,v $
# Revision 1.3  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>