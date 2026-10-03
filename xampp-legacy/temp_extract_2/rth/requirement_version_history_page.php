<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Version History Page
#
# $RCSfile: requirement_version_history_page.php,v $  $Revision: 1.6 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$s_project_properties	= session_get_project_properties();
$s_project_id			= $s_project_properties['project_id'];
$s_project_name			= $s_project_properties['project_name'];
$history_detail_page	= 'requirement_version_view_history_page.php';
$req_detail_page		= 'requirement_detail_page.php';

$row_style = '';

$s_properties 		= session_set_properties("requirements", $_GET);
$s_req_id		= $s_properties['req_id'];
$s_req_version_id	= $s_properties['req_version_id'];

html_window_title();
html_print_body();
html_page_title($s_project_name ." - REQUIREMENTS");
html_page_header( $db, $s_project_name );
html_print_menu();

#### Change to api submenu function for this page type ####
requirement_menu_print ($page);

error_report_check( $_GET );


$rows = requirement_get_detail( $s_project_id, $s_req_id );
$row_requirement = $rows[0];

$req_name		= $row_requirement[REQ_FILENAME];
$record_or_file		= $row_requirement[REQ_REC_FILE];
$req_version_num	= requirement_get_version_number( $s_req_id, $s_req_version_id );

print"<br>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<table class=width100 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_id')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_name')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_version')."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td width='33%' class=grid-data-c><a href='$req_detail_page?req_id=$s_req_id'>".sprintf( "%05s",trim( $s_req_id ) )."</a></td>". NEWLINE;
print"<td width='33%'class=grid-data-c>$req_name</td>". NEWLINE;
print"<td width='33%' class=grid-data-c>$req_version_num</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</div>". NEWLINE;

print"<br>". NEWLINE;
/*
print"<table class=width100 rules=all>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='25%' nowrap  class=grid-header-l>".lang_get('req_area')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>".$row_requirement[REQ_AREA_SPECD]."</td>". NEWLINE;

print"<td width='25%' nowrap class=grid-header-l>".lang_get('req_locked_date')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>".$row_requirement[REQ_LOCKED_DATE]."</td>". NEWLINE;
print"</tr>". NEWLINE;

$rows_functions = requirement_get_functionality($s_req_id);

print"<tr>". NEWLINE;
print"<td width='25%' nowrap class=grid-header-l>".lang_get('functionality')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>";
	foreach($rows_functions as $key => $value) {

		print$value."<br>";
	}
print"</td>". NEWLINE;

print"<td width='25%' nowrap class=grid-header-l>".lang_get('req_locked_by')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>".$row_requirement[REQ_LOCKED_BY]."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td width='25%' nowrap class=grid-header-l>".lang_get('req_type')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>".$row_requirement[REQ_TYPE]."</td>". NEWLINE;

print"<td width='25%' nowrap class=grid-header-l></td>". NEWLINE;
print"<td width='25%' class=grid-data-l></td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;
*/

if( !empty($rows) ) {

	print"<br><br>";
	print"<table class=width100 rules=cols>". NEWLINE;
	print"<tr>". NEWLINE;
	html_tbl_print_header( lang_get("version") );
	html_tbl_print_header( lang_get("status") );
	html_tbl_print_header( lang_get("author") );
	html_tbl_print_header( lang_get("created") );
	html_tbl_print_header( lang_get("req_assign_release") );
	html_tbl_print_header( lang_get("assigned_to") );
	html_tbl_print_header( lang_get("req_reason_change") );
	html_tbl_print_header( lang_get("view") );
	print"</tr>". NEWLINE;

	foreach($rows as $row ) {

		$req_version_id			= $row[REQ_VERS_UNIQUE_ID];
		$req_version			= $row[REQ_VERS_VERSION];
		$req_status				= $row[REQ_VERS_STATUS];
		$author					= $row[REQ_VERS_AUTHOR];
		$timestamp				= $row[REQ_VERS_TIMESTAMP];
		$assigned_to			= $row[REQ_VERS_ASSIGNED_TO];
		$reason_for_change		= $row[REQ_VERS_REASON_CHANGE];

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		$file_name = "";

		print"<tr class=$row_style>". NEWLINE;
		print"<td align='center'>$req_version</td>". NEWLINE;
		print"<td align='center'>$req_status</td>". NEWLINE;
		print"<td align='center'>$author</td>". NEWLINE;
		print"<td align='center'>$timestamp</td>". NEWLINE;
		print"<td align='center'>". NEWLINE;
		$assoc_releases = requirement_get_assoc_releases($req_version_id);
		foreach($assoc_releases as $row) {

			print$row[RELEASE_NAME]."<br>". NEWLINE;
		}
		print"</td>". NEWLINE;
		print"<td align='center'>$assigned_to</td>". NEWLINE;
		print"<td align='left'>$reason_for_change</td>". NEWLINE;
		if( $record_or_file == 'F' ) {

			$file_name	= $row[REQ_VERS_FILENAME];
			print"<td align='center'><a href='download.php?upload_filename=". $s_project_properties['req_upload_path'] . $file_name ."'>". lang_get('download') ."</a></td>". NEWLINE;
		}
		else {
			print"<td align='center'><a href='$history_detail_page?req_version_id=$req_version_id'>". lang_get('view') ."</a></td>". NEWLINE;
		}
		print"</tr>". NEWLINE;

	}

	print"</table>". NEWLINE;

	print"<br><br>". NEWLINE;

}

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_version_history_page.php,v $
# Revision 1.6  2007/02/03 10:25:53  gth2
# no message
#
# Revision 1.5  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.4  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.3  2006/01/09 04:11:44  gth2
# fixing problem with file download for req history - gth
#
# Revision 1.2  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>