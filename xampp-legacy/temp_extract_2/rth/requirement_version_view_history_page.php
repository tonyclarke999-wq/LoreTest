<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Version View History Page
#
# $RCSfile: requirement_version_view_history_page.php,v $  $Revision: 1.4 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_req_add_ver']) ) {

	require_once("requirement_add_new_version_page.php");
	exit;
}

if( isset($_POST['submit_req_delete']) ) {

	require_once("delete_page.php");
	exit;
}

if( isset($_POST['submit_req_update']) ) {

	require_once("requirement_edit_page.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'requirement_action.php';
$num                    = 0;
$s_project_properties     = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];
$username				= session_get_username();
$row_style				= '';

$display_options 	= session_set_display_options( "requirements", array_merge($_POST, $_GET) );
$s_tab 				= $display_options['tab'];

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];
$s_req_version_id	= $s_properties['req_version_id'];

if( isset($_POST['submit_req_lock']) ) {

	global $db;

	$q = "	Update
				Requirement
			Set
				LockedBy = '$username',
				LockedDate = '".date("Y-m-d H:i:s")."'
			WHERE
				ReqID = '$s_req_id'";
	db_query($db, $q);
}

if( isset($_POST['submit_req_unlock']) ) {

	global $db;

	$q = "	Update
				Requirement
			Set
				LockedBy = '',
				LockedDate = ''
			WHERE
				ReqID = '$s_req_id'";
	db_query($db, $q);
}

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get("req_detail_page"));
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);

error_report_check( $_GET );

$row_requirement = requirement_get_detail( $project_id, $s_req_id, $s_req_version_id );
//$row_requirement = $row_requirements[0];

foreach( $row_requirement as $row_detail ) {

		$req_name			= $row_detail[REQ_FILENAME];
		$req_version_id		= $row_detail[REQ_VERS_UNIQUE_ID];
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
		$req_last_updated	= $row_detail[REQ_LAST_UPDATED];

}
$assigned_to_release	= requirement_get_release( $req_version_id );

print"<br>". NEWLINE;

print"<form method=post action='$page'>". NEWLINE;

print"<table class=width100 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_id')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_name')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_version')."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td width='33%' class=grid-data-c>".sprintf( "%05s",trim( $s_req_id ) )."</td>". NEWLINE;
print"<td width='33%' class=grid-data-c>$req_name</td>". NEWLINE;
print"<td width='33%' class=grid-data-c>$req_version_num</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br>". NEWLINE;

print"<table class=width100 rules=all>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='25%' nowrap class=grid-header-l>".lang_get('status')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>$req_status</td>". NEWLINE;
print"<td width='25%' nowrap  class=grid-header-l>".lang_get('req_priority')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>$req_priority</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('author')."</td>". NEWLINE;
print"<td class=grid-data-l>$req_author</td>". NEWLINE;
print"<td width='25%' nowrap  class=grid-header-l>".lang_get('req_area')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>$area_covered</td>". NEWLINE;
print"</tr>". NEWLINE;

$rows_functions = requirement_get_functionality($project_id, $s_req_id);

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('assigned_to')."</td>". NEWLINE;
print"<td class=grid-data-l>$assigned_to</td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('req_type')."</td>". NEWLINE;
print"<td class=grid-data-l>$req_doc_type</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l valign=top>".lang_get('assigned_to_release')."</td>". NEWLINE;
print"<td class=grid-data-l valign=top>$assigned_to_release</td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('functionality')."</td>". NEWLINE;
print"<td class=grid-data-l>";
	foreach($rows_functions as $key => $value) {

		print$value."<br>";
	}
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('req_created')."</td>". NEWLINE;
print"<td class=grid-data-l>$date_created</td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('req_locked_by')."</td>". NEWLINE;
print"<td class=grid-data-l>$locked_by</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('last_updated')."</td>". NEWLINE;
print"<td class=grid-data-l>$req_last_updated</td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('req_locked_date')."</td>". NEWLINE;
print"<td class=grid-data-l>$locked_date</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l></td>". NEWLINE;
print"<td class=grid-data-l></td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('show_history')."</td>". NEWLINE;
print"<td class=grid-data-l><a href='requirement_version_history_page.php?req_id=$s_req_id'>".lang_get('history')."</a></td>". NEWLINE;
print"</tr>". NEWLINE;


if( $record_or_file =="F" ) {
	print"<tr>". NEWLINE;
	print"<td nowrap class=grid-header-l>".lang_get('file_name')."</td>". NEWLINE;
	print"<td class=grid-data-l>$req_name</td>". NEWLINE;
	print"<td nowrap class=grid-header-l>".lang_get('download')."</td>". NEWLINE;
	print"<td class=grid-data-l><a href='download.php?upload_filename=".$s_project_properties['req_upload_path'].$req_file_name."'>".lang_get('download')."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
} else {
	print"<tr>". NEWLINE;
	print"<td nowrap class=grid-header-l>".lang_get('detail')."</td>". NEWLINE;
	print"<td colspan=3 class=grid-data-l>$req_detail</td>". NEWLINE;
	print"</tr>". NEWLINE;
}

print"</table>". NEWLINE;

html_print_footer();


# ---------------------------------------------------------------------
# $Log: requirement_version_view_history_page.php,v $
# Revision 1.4  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.2  2005/12/13 13:59:56  gth2
# Completed the addition of requirement priority - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>