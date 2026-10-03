<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Detail Page
#
# $RCSfile: requirement_detail_page.php,v $  $Revision: 1.12 $
# ---------------------------------------------------------------------

// Redirect when adding a new version
if( isset($_POST['submit_req_add_ver']) ) {

	require_once("requirement_add_new_version_page.php");
	exit;
}
// Redirect to delete the requirement
if( isset($_POST['submit_req_delete']) ) {

	require_once("delete_page.php");
	exit;
}
// Redirect for updates
if( isset($_POST['submit_req_update']) ) {

	require_once("requirement_edit_page.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();


session_validate_form_reset();

$page                   = basename(__FILE__);
$action_page            = 'requirement_action.php';
$num                    = 0;
$row_style				= '';

$s_project_properties   = session_get_project_properties();
$s_user_properties		= session_get_user_properties();
$s_display_options 		= session_set_display_options( "requirements", array_merge($_POST, $_GET) );
$s_properties			= session_set_properties("requirements", $_GET);

$s_user_id				= $s_user_properties['user_id'];
$s_username				= $s_user_properties['username'];
$s_tempest_admin		= $s_user_properties['tempest_admin'];
$s_project_rights		= $s_user_properties['project_rights'];
$s_delete_rights		= $s_user_properties['delete_rights'];
$s_email				= $s_user_properties['email'];

$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

$s_tab 					= $s_display_options['tab'];
$s_req_id				= $s_properties['req_id'];
$s_req_version_id		= $s_properties['req_version_id'];


# LOCK REQUIREMENT
if( isset($_POST['submit_req_lock']) ) {

	requirement_lock($s_req_id, $s_username);

	############################################################################
	# EMAIL NOTIFICATION
	############################################################################
	$recipients	= requirement_get_notify_users($project_id, $s_req_id);
	requirement_email($project_id, $s_req_id, $recipients, $action="lock");
	############################################################################
	############################################################################
}

# UNLOCK REQUIREMENT
if( isset($_POST['submit_req_unlock']) ) {

	requirement_unlock($s_req_id);

	############################################################################
	# EMAIL NOTIFICATION
	############################################################################
	$recipients	= requirement_get_notify_users($project_id, $s_req_id);
	requirement_email($project_id, $s_req_id, $recipients, $action="unlock");
	############################################################################
	############################################################################
}

# get requirement details
if( empty($_GET['req_version_id']) ) {

	$s_req_version_id = requirement_get_latest_version( $s_req_id );
}

$row_requirement 	= requirement_get_detail( $project_id, $s_req_id, $s_req_version_id );
foreach( $row_requirement as $row_detail ) {

		$req_name			= $row_detail[REQ_FILENAME];
		$req_version_id		= $row_detail[REQ_VERS_UNIQUE_ID];
		$req_defect_id		= $row_detail[REQ_VERS_DEFECT_ID];
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
		$req_parent			= $row_detail[REQ_PARENT];
		$req_priority		= $row_detail[REQ_PRIORITY];
		$req_last_updated	= $row_detail[REQ_LAST_UPDATED];
}

$assigned_to_release	= requirement_get_release( $req_version_id );
$project_manager		= user_has_rights( $project_id, $s_user_id, MANAGER );
$user_has_delete_rights	= ($s_delete_rights==="Y" || $project_manager);
$user_has_edit_rights	= (empty($locked_by) || $locked_by==$s_username || $project_manager);


# Redirect back to the requirements page if no requirement returned.
# This is useful when a requirement is deleted, because the user get redirected
# back to this page.
if( empty($row_requirement) ) {

	html_redirect("requirement_page.php");
}

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get("req_detail_page"));
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);

error_report_check( $_GET );

print"<br>". NEWLINE;

print"<form method=post action='$page'>". NEWLINE;

print"<table class=width100 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_id')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_name')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_version')."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td width='33%' class=grid-data-c>".util_pad_id($s_req_id)."</td>". NEWLINE;
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
print"<td nowrap class=grid-header-l valign=top>".lang_get('functionality')."</td>". NEWLINE;
print"<td class=grid-data-l>";
	foreach($rows_functions as $key => $value) {

		print$value."<br>";
	}
print"</td>". NEWLINE;
print"</tr>". NEWLINE;


print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l valign=top>".lang_get('req_created')."</td>". NEWLINE;
print"<td class=grid-data-l valign=top>$date_created</td>". NEWLINE;
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
print"<td nowrap class=grid-header-l>".lang_get('create_test')."</td>". NEWLINE;
print"<td class=grid-data-l><a href='test_add_page.php?assoc_req=$s_req_id'>".lang_get('test')."</a></td>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('show_history')."</td>". NEWLINE;
print"<td class=grid-data-l><a href='requirement_version_history_page.php?req_id=$s_req_id&amp;req_version_id=$s_req_version_id'>".lang_get('history')."</a></td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td nowrap class=grid-header-l>".lang_get('create_child_req')."</td>". NEWLINE;
print"<td class=grid-data-l><a href='requirement_add_page.php?type=F&amp;parent_req=$s_req_id'>".lang_get('file')."</a> | <a href='requirement_add_page.php?type=R&amp;parent_req=$s_req_id'>".lang_get('record')."</a></td>". NEWLINE;
print"<td nowrap class=grid-header-l>". lang_get('req_defect_id') ."</td>". NEWLINE;
if( $req_defect_id == 0 ) {
	print"<td class=grid-data-l></td>". NEWLINE;
}
else {
	$req_defect_id = util_pad_id($req_defect_id);
	print"<td class=grid-data-l><a href='". VIEW_BUG_URL ."?bug_id=$req_defect_id&id=$req_defect_id'>$req_defect_id</a></td>". NEWLINE;
}
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

# enable/disable lock buttons
# if not locked
if( empty($locked_by) ) {
	$lock = "";
	$unlock = "disabled";

} # if locked and current user locked it, or current user a manager
elseif( $locked_by==$s_username || $project_manager ) {
	$lock = "disabled";
	$unlock = "";

} # if locked and current user did not lock it
else {
	$lock = "disabled";
	$unlock = "disabled";
}

# disable new version and update if user does not have the rights
if( $user_has_edit_rights ) {
	$update_disabled = "";
} else {
	$update_disabled = "disabled";
}

# disable delete button if user does not have the rights
if( $user_has_delete_rights ) {
	$delete_disabled = "";
} else {
	$delete_disabled = "disabled";
}

print"<table class=hide100>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='20%' align=center><input type=submit name=submit_req_add_ver value='".lang_get('req_add_ver')."' $update_disabled></td>". NEWLINE;
print"<td width='20%' align=center><input type=submit name=submit_req_update value='".lang_get('req_update')."' $update_disabled></td>". NEWLINE;
print"<td width='20%' align=center>". NEWLINE;
	print"<input type=submit name=submit_req_delete value='".lang_get('req_delete')."' $delete_disabled>". NEWLINE;
	print"<input type='hidden' name='r_page' value='requirement_page.php'>". NEWLINE;
	print"<input type='hidden' name='f' value='delete_requirement'>". NEWLINE;
	print"<input type='hidden' name='id' value='$s_req_id'>". NEWLINE;
	print"<input type='hidden' name='project_id' value='$project_id'>". NEWLINE;
	print"<input type='hidden' name='msg' value='". DEL_REQUIREMENT ."'>". NEWLINE;
	print"<input type='hidden' name='req_version_id' value='$s_req_version_id'>". NEWLINE;
print"</td>". NEWLINE;
print"<td width='20%' align=center><input type=submit name=submit_req_lock $lock value='".lang_get('req_lock')."'></td>". NEWLINE;
print"<td width='20%' align=center><input type=submit name=submit_req_unlock $unlock value='".lang_get('req_unlock')."'></td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;

print"</form>". NEWLINE;

print"<br>". NEWLINE;
print"<br>". NEWLINE;

print"<table class=hide100>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
req_sub_menu_print( $s_req_id, $s_req_version_id, $page, $s_tab  );
//html_print_tabs($tabs, $s_tab);
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td align=center>". NEWLINE;

# EDIT LINKS
# if requirement not locked OR current user locked requirement OR current user is a manager
# display edit links
if( $user_has_delete_rights ) {

	switch( $s_tab ) {
	case 1:
		print"[ <a href='requirement_assoc_page.php?req_id=$s_req_id&amp;req_version_id=$s_req_version_id'>".lang_get("select_children")."</a> ]". NEWLINE;
		break;
	case 2:
		print"[ <a href='requirement_tests_assoc_page.php?req_id=$s_req_id&amp;req_version_id=$s_req_version_id'>".lang_get("edit_assoc")."</a> ]". NEWLINE;
		break;
	case 3:
		print"&nbsp;". NEWLINE;
		break;
	case 4:
		print"[ <a href='requirement_releases_assoc_page.php?req_id=$s_req_id&amp;req_version_id=$s_req_version_id'>".lang_get("edit_assoc")."</a> ]". NEWLINE;
		break;
	}

} else {
	print"&nbsp;";
}

print"</td>". NEWLINE;
print"</tr>". NEWLINE;


print"<tr>". NEWLINE;
print"<td>". NEWLINE;
switch( $s_tab ) {
# REQUIREMENTS ASSOC
case 1:

	print"<table cellpadding=4 width='100%'>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;

	# GET CHILDREN OF THIS REQUIREMENT
	$rows_children = requirement_get_related($s_req_id);


	# if this requirement has children OR a parent
	if( !empty($rows_children) || $req_parent != 0 ) {

		$row_style = html_tbl_alternate_bgcolor($row_style);
		
		print"<table rules=cols align=center class=width90>";
		print"<tr>";
		html_tbl_print_header( lang_get('req_id') );
		html_tbl_print_header( lang_get('req_name') );
		
		if( $user_has_delete_rights ) {
			html_tbl_print_header( lang_get('delete') );
		}
		print"</tr>";

		# PRINT PARENT RECORD
		if( $req_parent != 0 ) {

			$assoc_id			= $s_req_id;
			$rel_parent_id 		= util_pad_id( $req_parent );
			$req_parent_name 	= requirement_get_name($req_parent);
			$req_parent_version = requirement_get_latest_version( $rel_parent_id );
			$rel_type = 'child of';

			print"<tr class='$row_style' align=left>". NEWLINE;
			print"<td class='tbl-c'>$rel_type <a href='requirement_detail_page.php?req_id=$req_parent&amp;req_version_id=$req_parent_version'>$rel_parent_id</a></td>". NEWLINE;
			print"<td class='tbl-l'>$req_parent_name</td>". NEWLINE;
			if( $user_has_delete_rights ) {
				print"<td class='tbl-c'></td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}

		# PRINT CHILDREN RECORDS
		if( !empty($rows_children) ) {

			foreach($rows_children as $row_child) {

				$rel_req_id 	= util_pad_id( $row_child['req_id'] );
				$child_id		= $rel_req_id;
				$parent_id		= $s_req_id;
				$rel_req_name 	= $row_child['req_name'];
				$req_child_version = requirement_get_latest_version( $child_id );

				$row_style = html_tbl_alternate_bgcolor($row_style);
				print"<tr class='$row_style' align=left>". NEWLINE;
				
				print"<td class='tbl-c'><a href='requirement_detail_page.php?req_id=$rel_req_id&amp;req_version_id=$req_child_version'>$rel_req_id</a></td>". NEWLINE;
			
				print"<td class='tbl-l'>$rel_req_name</td>". NEWLINE;
				if( $user_has_delete_rights ) {
					print"<td class='tbl-c'><a href='requirement_delete_assoc_action.php?assoc=req&amp;parent_id=$parent_id&amp;assoc_id=$child_id'>".lang_get('delete')."</a></td>". NEWLINE;
				}
				print"</tr>";
			}
		}

		print"</table>";


	}
	else {
		print"<div align=center>". NEWLINE;
		print lang_get('no_related_reqs');
		print"</div>". NEWLINE;
	}

	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	break;
# TEST ASSOC
case 2:
	print"<table cellpadding=4 width='100%'>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;

	$req_test_relationships = requirement_get_test_relationships($s_req_id);

	if( !empty($req_test_relationships) ) {
		print"<table rules=cols class=width100>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( "&nbsp;".lang_get('id')."&nbsp;" );
		html_tbl_print_header( "&nbsp;".lang_get('test_name')."&nbsp;" );
		html_tbl_print_header( "&nbsp;".lang_get('percent_covered_test')."&nbsp;" );
		if( $user_has_delete_rights ) {
			html_tbl_print_header( "&nbsp;".lang_get('delete')."&nbsp;" );
		}
		print"</tr>". NEWLINE;

		foreach($req_test_relationships as $row_req_test_rels) {

			$test_id		= $row_req_test_rels[TEST_ID];
			$test_id_link	= util_pad_id($test_id);
			$test_name		= $row_req_test_rels[TEST_NAME];
			$assoc_id		= $row_req_test_rels[TEST_REQ_ASSOC_ID];
			$pc_covered		= $row_req_test_rels[TEST_REQ_ASSOC_PERCENT_COVERED];


			$row_style = html_tbl_alternate_bgcolor($row_style);
			print"<tr class='$row_style'>". NEWLINE;
			print"<td nowrap><a href='test_detail_page.php?test_id=$test_id&project_id=$project_id&amp;tab=3'>$test_id_link</a></td>". NEWLINE;
			print"<td nowrap>$test_name</td>". NEWLINE;
			print"<td nowrap>$pc_covered%</td>". NEWLINE;
			if( $user_has_delete_rights ) {
				print"<td nowrap><a href='requirement_delete_assoc_action.php?assoc=test&amp;assoc_id=$assoc_id'>".lang_get('delete')."</a></td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;

	} else {
		print"<div align=center>". NEWLINE;
		print lang_get('no_related_tests');
		print"</div>". NEWLINE;
	}

	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	break;

# DISCUSSIONS
case 3:

	$rows_discussion = discussion_get($s_req_id);
	if( !empty($rows_discussion) ) {
		print"<table class=width100 rules=cols>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<th>".lang_get('subject')."</th>". NEWLINE;
		print"<th>".lang_get('author')."</th>". NEWLINE;
		print"<th>".lang_get('date_started')."</th>". NEWLINE;
		print"<th>".lang_get('num_posts')."</th>". NEWLINE;
		print"<th>".lang_get('status')."</th>". NEWLINE;
		print"</tr>". NEWLINE;

		foreach($rows_discussion as $row_discussion) {
			$row_style = html_tbl_alternate_bgcolor($row_style);
			print"<tr class='$row_style'>". NEWLINE;
			print"<td><a href='requirement_discussion_page.php?discussion_id=".$row_discussion[DISC_ID]."'>".$row_discussion[DISC_SUBJECT]."</a></td>". NEWLINE;
			print"<td>".$row_discussion[DISC_AUTHOR]."</td>". NEWLINE;
			print"<td>".$row_discussion[DISC_DATE]."</td>". NEWLINE;
			print"<td>".discussion_get_num_posts($row_discussion[DISC_ID])."</td>". NEWLINE;
			print"<td>".$row_discussion[DISC_STATUS]."</td>". NEWLINE;
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
	} else {
		print"<div align=center>". NEWLINE;
		print lang_get('no_discussions');
		print"</div>". NEWLINE;
	}


	print"<br>". NEWLINE;
	print"<br>". NEWLINE;

	print"<div align=center>";

	print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

	print"<table class=width90>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;

	print"<form method=post action='requirement_discussion_action.php'>". NEWLINE;
	print"<input type=hidden name='req_id' value='$s_req_id'>". NEWLINE;
	print"<input type=hidden name='status' value='OPEN'>". NEWLINE;
	print"<input type=hidden name='author' value='$s_username'>". NEWLINE;
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
	$add_discussion = session_validate_form_get_field( "discussion", "", session_use_FCKeditor() );
	print"<tr>". NEWLINE;
	print"<td class='form-lbl-r'>". lang_get('discussion') ."</td>". NEWLINE;
	print"<td class='form-data-l'>";
	html_FCKeditor("discussion", 360, 200, $add_discussion);
	print"</td>";
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

# RELEASE ASSOC
case 4:
		print"<table cellpadding=4 width='100%'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;

	$req_rel_relationships = requirement_get_assoc_releases($s_req_version_id);

	if( !empty($req_rel_relationships) ) {
		print"<table rules=cols class=width100>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( "&nbsp;".lang_get('release_name')."&nbsp;" );
		if( $user_has_delete_rights ) {
			html_tbl_print_header( "&nbsp;".lang_get('delete')."&nbsp;" );
		}
		print"</tr>". NEWLINE;

		foreach($req_rel_relationships as $row_req_rel_rels) {

			$release_name	= $row_req_rel_rels[RELEASE_NAME];
			$assoc_id		= $row_req_rel_rels[REQ_VERS_ASSOC_REL_ID];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td nowrap>$release_name</td>". NEWLINE;
			if( $user_has_delete_rights ) {
				print"<td nowrap><a href='requirement_delete_assoc_action.php?assoc=release&amp;assoc_id=$assoc_id'>".lang_get('delete')."</a></td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;

	} else {
		print"<div align=center>". NEWLINE;
		print lang_get('no_related_releases');
		print"</div>". NEWLINE;
	}

	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	break;
}

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_detail_page.php,v $
# Revision 1.12  2008/07/11 07:05:49  peter_thal
# fixed order_by and order_dir bug and added a missing parameter to test_detail_page
#
# Revision 1.11  2006/11/03 14:02:16  gth2
# correcting errors with requirements pages.
# Change Request field dispalying 00000
# Undefined index error when updating a child requirement
# gth2
#
# Revision 1.10  2006/09/27 23:58:33  gth2
# Adding functionality to link a change request (defect_id) to a requirement - gth
#
# Revision 1.9  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.8  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.7  2006/02/06 13:07:53  gth2
# fixing bug when deleting a requirement - gth
#
# Revision 1.6  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.5  2006/01/06 00:34:53  gth2
# fixed bug with associations - gth
#
# Revision 1.4  2005/12/08 22:13:57  gth2
# adding Assign To Release to requirment edit page - gth
#
# Revision 1.3  2005/12/06 13:56:50  gth2
# Adding requirement priority and last updated - gth
#
# Revision 1.2  2005/12/05 19:41:33  gth2
# Adding fields: priority and untestable - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>