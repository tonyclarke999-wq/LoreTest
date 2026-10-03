<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Version Detail Page
#
# $RCSfile: requirement_version_detail_page.php,v $  $Revision: 1.5 $
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

$s_properties 	= session_set_properties("requirements", $_GET);
$s_req_id		= $s_properties['req_id'];

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

$rows_requirement = requirement_get_detail($project_id, $s_req_id, $_GET['version'] );
$row_requirement = $rows_requirement[0];

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
print"<td width='33%' class=grid-data-c>".$row_requirement[REQ_FILENAME]."</td>". NEWLINE;
print"<td width='33%' class=grid-data-c>".$row_requirement[REQ_VERS_VERSION]."</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br>". NEWLINE;

print"<table class=width100 rules=all>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='25%' nowrap class=grid-header-l>".lang_get('status')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>".$row_requirement[REQ_VERS_STATUS]."</td>". NEWLINE;
print"<td width='25%' nowrap  class=grid-header-l>".lang_get('req_area')."</td>". NEWLINE;
print"<td width='25%' class=grid-data-l>".$row_requirement[REQ_AREA_COVERAGE]."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('author')."</td>". NEWLINE;
print"<td class=grid-data-l>".$row_requirement[REQ_VERS_UPLOADED_BY]."</td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('req_type')."</td>". NEWLINE;
print"<td class=grid-data-l>".$row_requirement[REQ_TYPE]."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('req_created')."</td>". NEWLINE;
print"<td class=grid-data-l>".$row_requirement[REQ_VERS_TIMESTAMP]."</td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('functionality')."</td>". NEWLINE;
print"<td class=grid-data-l>".$row_requirement[REQ_FUNCTIONALITY]."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('req_assign_release')."</td>". NEWLINE;
print"<td class=grid-data-l>".admin_get_release_name($row_requirement[REQ_VERS_ASSIGN_RELEASE])."</td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('req_locked_by')."</td>". NEWLINE;
print"<td class=grid-data-l>".$row_requirement[REQ_LOCKED_BY]."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('assigned_to')."</td>". NEWLINE;
print"<td class=grid-data-l>".$row_requirement[REQ_VERS_ASSIGNED_TO]."</td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('req_locked_date')."</td>". NEWLINE;
print"<td class=grid-data-l>".$row_requirement[REQ_LOCKED_DATE]."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l></td>". NEWLINE;
print"<td class=grid-data-l></td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('show_history')."</td>". NEWLINE;
print"<td class=grid-data-l><a href='requirement_version_history_page.php?req_id=$s_req_id'>".lang_get('history')."</a></td>". NEWLINE;
print"</tr>". NEWLINE;

if($row_requirement[REQ_REC_FILE]=="F") {
	print"<tr>". NEWLINE;
	print"<td nowrap class=grid-header-l>".lang_get('file_name')."</td>". NEWLINE;
	print"<td class=grid-data-l>".$row_requirement[REQ_FILENAME]."</td>". NEWLINE;
	print"<td nowrap class=grid-header-l>".lang_get('download')."</td>". NEWLINE;
	print"<td class=grid-data-l><a href='download.php?upload_filename=".$s_project_properties['req_upload_path'].$row_requirement[REQ_VERS_FILENAME]."'>".lang_get('download')."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
} else {
	print"<tr>". NEWLINE;
	print"<td nowrap class=grid-header-l>".lang_get('detail')."</td>". NEWLINE;
	print"<td colspan=3 class=grid-data-l>".$row_requirement[REQ_VERS_DETAIL]."</td>". NEWLINE;
	print"</tr>". NEWLINE;
}

print"</table>". NEWLINE;

if( empty($row_requirement[REQ_LOCKED_BY]) ) {
	$disable_lock = "";
	$disable_unlock = "disabled";
} else {
	$disable_lock = "disabled";
	$disable_unlock = "";
}

print"<table class=hide100>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='20%' align=center><input type=submit name=submit_req_add_ver value='".lang_get('req_add_ver')."'></td>". NEWLINE;
print"<td width='20%' align=center><input type=submit name=submit_req_update value='".lang_get('req_update')."'></td>". NEWLINE;
print"<td width='20%' align=center>". NEWLINE;
	print"<input type=submit name=submit_req_delete value='".lang_get('req_delete')."'>". NEWLINE;
	print"<input type='hidden' name='r_page' value='requirement_page.php'>". NEWLINE;
	print"<input type='hidden' name='f' value='delete_requirement'>". NEWLINE;
	print"<input type='hidden' name='id' value='$s_req_id'>". NEWLINE;
	print"<input type='hidden' name='msg' value='". DEL_REQUIREMENT ."'>". NEWLINE;
print"</td>". NEWLINE;
print"<td width='20%' align=center><input type=submit name=submit_req_lock $disable_lock value='".lang_get('req_lock')."'></td>". NEWLINE;
print"<td width='20%' align=center><input type=submit name=submit_req_unlock $disable_unlock value='".lang_get('req_unlock')."'></td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;

print"</form>". NEWLINE;

print"<br>". NEWLINE;
print"<br>". NEWLINE;

$tabs = array(	"Req Assoc"=>"$page?tab=1",
				"Test Assoc"=>"$page?tab=2",
				"Discussion"=>"$page?tab=3" );

print"<table class=hide100>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
html_print_tabs($tabs, $s_tab);
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"&nbsp;";
print"</td>". NEWLINE;
print"</tr>". NEWLINE;


print"<tr>". NEWLINE;
print"<td>". NEWLINE;
switch( $s_tab ) {
case 1:
	$req_relationships = requirement_get_relationships($s_req_id);

	if( !empty($req_relationships[0]) && !empty($req_relationships[1]) ) {
		print"<table cellpadding=4 width='100%'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;

		print"<table rules=cols class=width100>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( "&nbsp;".lang_get('id')."&nbsp;" );
		html_tbl_print_header( "&nbsp;".lang_get('req_name')."&nbsp;" );
		html_tbl_print_header( "&nbsp;".lang_get('req_rel')."&nbsp;" );
		html_tbl_print_header( "" );
		print"</tr>". NEWLINE;

		foreach($req_relationships[0] as $row_req_rels) {
			$row_style = html_tbl_alternate_bgcolor($row_style);
			print"<tr class='$row_style'>". NEWLINE;
			print"<td nowrap>".$row_req_rels[REQ_REQ_SECONDARY_ID]."</td>". NEWLINE;
			print"<td nowrap>".$row_req_rels[REQ_FILENAME]."</td>". NEWLINE;
			print"<td nowrap>".$row_req_rels[REQ_REQ_RELATIONSHIP]."</td>". NEWLINE;
			print"<td nowrap><a href='requirement_detail_page.php?req_id=".$row_req_rels[REQ_REQ_SECONDARY_ID]."'>View</a> <a >Edit</a></td>". NEWLINE;
			print"</tr>". NEWLINE;
		}

		foreach($req_relationships[1] as $row_req_rels) {
			$row_style = html_tbl_alternate_bgcolor($row_style);
			print"<tr class='$row_style'>". NEWLINE;
			print"<td nowrap>".$row_req_rels[REQ_REQ_PRIMARY_ID]."</td>". NEWLINE;
			print"<td nowrap>".$row_req_rels[REQ_FILENAME]."</td>". NEWLINE;
			print"<td nowrap>".$row_req_rels[REQ_REQ_RELATIONSHIP]."</td>". NEWLINE;
			print"<td nowrap><a href='requirement_detail_page.php?req_id=".$row_req_rels[REQ_REQ_PRIMARY_ID]."'>View</a> <a >Edit</a></td>". NEWLINE;
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;

		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
	}

	break;
case 2:
	$req_test_relationships = requirement_get_test_relationships($s_req_id);

	if( !empty($req_test_relationships) ) {
		print"<table cellpadding=4 width='100%'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;

		print"<table rules=cols class=width100>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( "&nbsp;".lang_get('id')."&nbsp;" );
		html_tbl_print_header( "&nbsp;".lang_get('test_name')."&nbsp;" );
		html_tbl_print_header( "&nbsp;".lang_get('')."&nbsp;" );
		print"</tr>". NEWLINE;

		foreach($req_test_relationships as $row_req_test_rels) {
			$row_style = html_tbl_alternate_bgcolor($row_style);
			print"<tr class='$row_style'>". NEWLINE;
			print"<td nowrap>".$row_req_test_rels[TEST_ID]."</td>". NEWLINE;
			print"<td nowrap>".$row_req_test_rels[TEST_NAME]."</td>". NEWLINE;
			print"<td nowrap><a href='test_detail_page.php?test_id=".$row_req_test_rels[TEST_ID]."&project_id=$project_id'>View</a> <a >Edit</a></td>". NEWLINE;
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;

		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
	} else {

	}

	break;

case 3:

	$rows_discussion = discussion_get($s_req_id);
	if( !empty($rows_discussion) ) {
		print"<table class=width100 rules=cols>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<th>".lang_get('id')."</th>". NEWLINE;
		print"<th>".lang_get('subject')."</th>". NEWLINE;
		print"<th>".lang_get('author')."</th>". NEWLINE;
		print"<th>".lang_get('date_started')."</th>". NEWLINE;
		print"<th>".lang_get('num_posts')."</th>". NEWLINE;
		print"<th>".lang_get('status')."</th>". NEWLINE;
		print"</tr>". NEWLINE;

		foreach($rows_discussion as $row_discussion) {
			$row_style = html_tbl_alternate_bgcolor($row_style);
			print"<tr class='$row_style'>". NEWLINE;
			print"<td><a href='requirement_discussion_page.php?discussion_id=".$row_discussion[DISC_ID]."'>".sprintf( "%05s",trim( $row_discussion[DISC_ID] ) )."</a></td>". NEWLINE;
			print"<td>".$row_discussion[DISC_SUBJECT]."</td>". NEWLINE;
			print"<td>".$row_discussion[DISC_AUTHOR]."</td>". NEWLINE;
			print"<td>".$row_discussion[DISC_DATE]."</td>". NEWLINE;
			print"<td>".discussion_get_num_posts($row_discussion[DISC_ID])."</td>". NEWLINE;
			print"<td>".$row_discussion[DISC_STATUS]."</td>". NEWLINE;
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
	} else {
		print lang_get('no_discussions');
	}


	print"<br>". NEWLINE;
	print"<br>". NEWLINE;

	print"<div align=center>";
	//print"<form method=post action=requirement_discussion_add_page.php>". NEWLINE;
	//print"<input type=submit value='".lang_get('add_discussion')."'>". NEWLINE;
	//print"</form>". NEWLINE;
	print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

	print"<table class=width90>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;

	print"<form method=post action='requirement_discussion_action.php'>". NEWLINE;
	print"<input type=hidden name='req_id' value='$s_req_id'>". NEWLINE;
	print"<input type=hidden name='status' value='OPEN'>". NEWLINE;
	print"<input type=hidden name='author' value='$username'>". NEWLINE;
	print"<input type=hidden name='assign_to' value=''>". NEWLINE;
	print"<table class='inner'>". NEWLINE;

	# FORM TITLE
	print"<tr>". NEWLINE;
	print"<td colspan='2'><h4>". lang_get('add_discussion') ."</h4></td>". NEWLINE;
	print"</tr>". NEWLINE;


	# SUBJECT
	print"<tr>". NEWLINE;
	print"<td class='form-lbl-r'>". lang_get('subject') ." <span class='required'>*</span></td>". NEWLINE;
	print"<td class='form-data-l'><input type='text' maxlength='255' name='subject_required' size=60 value='"
		.session_validate_form_get_field("subject_required")
		."'></td>". NEWLINE;
	print"</tr>". NEWLINE;

	# DESCRIPTION
	print"<tr>". NEWLINE;
	print"<td class='form-lbl-r'>". lang_get('discussion') ."</td>". NEWLINE;
	print"<td class='form-data-l'><textarea name='discussion' rows=8 cols=60>"
		.session_validate_form_get_field("discussion")
		."</textarea></td>". NEWLINE;
	print"</tr>". NEWLINE;

	# SUBMIT BUTTON
	print"<tr>". NEWLINE;
	print"<td colspan='2' class='form-data-c'><input type='submit' name=submit_add_discussion value='"
		. lang_get('add')
		."'></td>". NEWLINE;
	print"</tr>". NEWLINE;

	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</div>";

	break;
}

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

html_print_footer();


# ---------------------------------------------------------------------
# $Log: requirement_version_detail_page.php,v $
# Revision 1.5  2008/07/21 07:42:34  peter_thal
# small bug fixes for test_detail_page linking parameter
#
# Revision 1.4  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>