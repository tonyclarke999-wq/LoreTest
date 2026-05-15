<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Add Page
#
# $RCSfile: requirement_add_page.php,v $  $Revision: 1.5 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_properties     = session_get_project_properties();
$project_id				= $project_properties['project_id'];
$project_name           = $project_properties['project_name'];
$username				= session_get_username();

$s_properties 	= session_get_properties("requirements");
$s_req_id		= $s_properties['req_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('req_add_page'));
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);

error_report_check( $_GET );

print"<br>". NEWLINE;

print"<div align=center>". NEWLINE;
print"<form enctype=\"multipart/form-data\" name=\"upload\" method=post action='requirement_add_action.php'>". NEWLINE;
print"<input type=hidden name=MAX_FILE_SIZE  value=25000000>". NEWLINE;
print"<input type=hidden name=req_record_or_file value=".$_GET["type"].">". NEWLINE;

# requirement this new requirement is a child of
if( !empty($_GET["parent_req"]) ) {

	print"<input type=hidden name=parent_req value=".$_GET["parent_req"].">". NEWLINE;
}

print"<input type=hidden name=req_author value=$username>". NEWLINE;
print"<input type=hidden name=project_id value=$project_id>". NEWLINE;

print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width90>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;
print"<tr>". NEWLINE;
print"<td colspan=2><h4>". lang_get('new_version') ."</h4></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=center>". NEWLINE;

# NAME
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=form-data-l><input type=text name=req_name_required size=69 maxlength=255 value='".session_validate_form_get_field("req_name_required")."'></td>". NEWLINE;

if($_GET['type']=="F") {

	# FILE
	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r nowrap>". lang_get('file_name') ." <span class='required'>*</span></td>". NEWLINE;
	print"<td class=form-data-l><input type=file name=upload_file size=69></td>". NEWLINE;

	print"</tr>". NEWLINE;
} else {

	# REQUIREMENT DETAIL
	$detail = session_validate_form_get_field('req_detail_required', "", session_use_FCKeditor());
	print"<tr>". NEWLINE;
	print"<td class='form-lbl-r' nowrap>". lang_get('detail') ." <span class='required'>*</span></td>". NEWLINE;
	print"<td class='form-data-l'>". NEWLINE;
	html_FCKeditor("req_detail_required", 600, 250, $detail);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
}

# REASON FOR CHANGE
$reason_for_change = session_validate_form_get_field('req_reason_change', "", session_use_FCKeditor());
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_reason_change') ."</td>". NEWLINE;
print"<td class=form-data-l>";
print"<textarea name=req_reason_change rows=5 cols=80 >$reason_for_change</textarea>";
print"</tr>". NEWLINE;

# VERSION
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('version') ."</td>". NEWLINE;
print"<td class=form-data-l><input type=text name=req_version value='".session_validate_form_get_field('req_version', "1.0" )."' size=5 maxlength=10></td>". NEWLINE;
print"</tr>". NEWLINE;

# AREA COVERED
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_area') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name=req_area_covered size=1>". NEWLINE;
	$list_box = array();

	$rows_areas = project_get_req_areas_covered($project_id);

	foreach($rows_areas as $row_area) {

		$list_box[$row_area[REQ_AREA_COVERAGE_ID]] = $row_area[REQ_AREA_COVERAGE];
	}
	$list_box[""] = "";

	html_print_list_box_from_key_array(	$list_box,
										session_validate_form_get_field('req_area') );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# DOC TYPE
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_type') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name=req_type size=1>". NEWLINE;
	$list_box = requirement_get_types($project_id, $blank=true);

	html_print_list_box_from_key_array(	$list_box,
										session_validate_form_get_field('req_type' ) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# STATUS
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_status') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name=req_status size=1>". NEWLINE;
	$list_box = requirement_get_statuses();

	html_print_list_box_from_array(	$list_box,
									session_validate_form_get_field('req_status') );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# PRIORITY
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_priority') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name=req_priority size=1>". NEWLINE;
	$list_box = requirement_get_priority();

	html_print_list_box_from_array(	$list_box,
									session_validate_form_get_field('req_priority') );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

/*
# ASSIGN TO RELEASE
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_assign_release') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;

print"<select name='req_assign_release'>". NEWLINE;
$rows_release = requirement_get_distinct_field($project_id, REQ_VERS_ASSIGN_RELEASE);
for ($i=0; $i<sizeof($rows_release); $i++) {

	$rows_release_2[$rows_release[$i]] = admin_get_release_name($rows_release[$i]);
}

$rows_release_2[""] = "";
html_print_list_box_from_key_array( $rows_release_2, session_validate_form_get_field('req_assign_release') );
print"</select>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
*/

# ASSIGNED TO
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('assigned_to') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name='req_assigned_to'>". NEWLINE;

    $rows_users = user_get_usernames_by_project($project_id, true);

    html_print_list_box_from_array( $rows_users, session_validate_form_get_field('req_assigned_to') );

print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# FUNCTIONALITY
print"<tr>". NEWLINE;
print"<td class=form-lbl-r valign=top nowrap>". lang_get('functionality') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name='req_functionality[]' size=6 multiple>". NEWLINE;
	$list_box = array();

	$rows = project_get_req_functionality($project_id);

	foreach($rows as $row) {

		$list_box[$row[REQ_FUNCT_ID]] = $row[REQ_FUNCT_NAME];
	}

	html_print_list_box_from_key_array( $list_box, session_validate_form_get_field('req_functionality') );
	#html_print_list_box_from_key_array( $list_box );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td colspan='2' class=center>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td colspan='2' class=center><input type='submit' value='". lang_get('create') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>". NEWLINE;
print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_add_page.php,v $
# Revision 1.5  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.4  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.3  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.2  2005/12/13 13:59:56  gth2
# Completed the addition of requirement priority - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>