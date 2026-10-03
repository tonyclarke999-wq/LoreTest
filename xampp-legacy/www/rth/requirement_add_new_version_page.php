<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Add New Version Page
#
# $RCSfile: requirement_add_new_version_page.php,v $  $Revision: 1.9 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_properties     = session_get_project_properties();
$project_id				= $project_properties['project_id'];
$project_name           = $project_properties['project_name'];
$username				= session_get_username();

$s_properties 		= session_get_properties("requirements");
$s_req_id			= $s_properties['req_id'];
$s_req_version_id  	= $s_properties['req_version_id'];


html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('requirements_new_version'));
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);
print"<br><br>". NEWLINE;
error_report_check( $_GET );

$rows_requirement = requirement_get_detail( $project_id, $s_req_id, $s_req_version_id );
$row_requirement = $rows_requirement[0];

foreach( $rows_requirement as $rows_req ) {

		$req_name				= $rows_req[REQ_FILENAME];
		$doc_type				= $rows_req[REQ_DOC_TYPE_NAME];
		$version_num			= $rows_req[REQ_VERS_VERSION];
		$status					= $rows_req[REQ_VERS_STATUS];
		$area_covered			= $rows_req[REQ_AREA_COVERAGE];
		$reason_for_change		= $rows_req[REQ_VERS_REASON_CHANGE];
		$record_or_file			= $rows_req[REQ_REC_FILE];
		$req_detail				= $rows_req[REQ_VERS_DETAIL];
		$req_priority			= $rows_req[REQ_PRIORITY];
		$req_area_covered_id	= $row_requirement[REQ_AREA_COVERAGE_ID];
		$req_defect_id			= $row_requirement[REQ_VERS_DEFECT_ID];
}

print"<div align=center>". NEWLINE;
print"<form enctype=\"multipart/form-data\" name=\"upload\" method=post action='requirement_add_new_version_action.php'>". NEWLINE;
print"<input type=hidden name=MAX_FILE_SIZE  value=25000000>". NEWLINE;

print"<input type=hidden name=project_id value=$project_id>". NEWLINE;
print"<input type=hidden name=req_author value=$username>". NEWLINE;
print"<input type=hidden name=req_id value=$s_req_id>". NEWLINE;
print"<input type=hidden name=req_record_or_file value=".$record_or_file.">". NEWLINE;

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
print"<td class=form-lbl-r nowrap>". lang_get('req_name') ."</td>". NEWLINE;
print"<td class=form-data-l>$req_name</td>". NEWLINE;
print"</tr>". NEWLINE;

# VERSION
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('version') ."</td>". NEWLINE;
print"<td class=form-data-l><input type=text name=req_version value='".session_validate_form_get_field('req_version', util_increment_version($version_num) )."' size=5 maxlength=10></td>". NEWLINE;
print"</tr>". NEWLINE;

# STATUS
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_status') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name=req_status size=1>". NEWLINE;
	$list_box = requirement_get_statuses();
	html_print_list_box_from_array(	$list_box, session_validate_form_get_field('req_status', $status) );
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
									session_validate_form_get_field('req_priority', $req_priority) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# CHANGE REQUEST
$defect_id = session_validate_form_get_field('defect_id');
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_defect_id') ."</td>". NEWLINE;
print"<td class=form-data-l><input type='text' size='10' name='defect_id' value='$defect_id'></td>". NEWLINE;
print"</tr>". NEWLINE;

# AREA COVERED
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_area') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;

print"<select name=req_area size=1>". NEWLINE;
$list_box = array();

$rows_areas = project_get_req_areas_covered($project_id);

foreach($rows_areas as $row_area) {

	$list_box[$row_area[REQ_AREA_COVERAGE_ID]] = $row_area[REQ_AREA_COVERAGE];
}
$list_box[""] = "";

html_print_list_box_from_key_array(	$list_box,
									session_validate_form_get_field('req_area',
									$req_area_covered_id) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
/*
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_area') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;

print"<select name=req_area size=1>". NEWLINE;
$list_box = array();
$rows_areas = project_get_areas_tested($project_id);

foreach($rows_areas as $row_area) {

	$list_box[] = $row_area[AREA_TESTED_NAME];
}
$list_box[] = "";
html_print_list_box_from_array(	$list_box, session_validate_form_get_field('req_area', $area_covered) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
*/

# FUNCTIONALITY
$rows_functions = requirement_get_functionality($project_id, $s_req_id);
$function_ids = array();
foreach($rows_functions as $key=>$value) {

	$function_ids[] = $key;
}
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('functionality') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name='req_functionality[]' size=5 multiple>". NEWLINE;
	$list_box = array();

	$rows = project_get_req_functionality($project_id);

	foreach($rows as $row) {

		$list_box[$row[REQ_FUNCT_ID]] = $row[REQ_FUNCT_NAME];
	}
html_print_list_box_from_key_array(	$list_box, session_validate_form_get_field('req_functionality', $function_ids ) );
print"</select>". NEWLINE;
#print lang_get("hold_ctrl");
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# DOC TYPE
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_type') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;

print"<select name=req_type size=1>". NEWLINE;
	$list_box = array();

	$rows = project_get_req_doc_types($project_id);
	foreach($rows as $row) {
		$list_box[$row[REQ_DOC_TYPE_ID]] = $row[REQ_DOC_TYPE_NAME];
	}

	$list_box[""] = "";

	html_print_list_box_from_key_array(	$list_box,
										session_validate_form_get_field('req_type',
																		$doc_type) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
/*
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_type') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name=req_type size=1>". NEWLINE;
	$list_box = requirement_get_types($project_id, $blank=true);
	html_print_list_box_from_array(	$list_box, session_validate_form_get_field('req_types', $doc_type) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
*/

# ASSIGNED TO RELEASE
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('assigned_to_release') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;

print"<select name=assigned_release size=1>". NEWLINE;
	$list_box = array();

	$rows_rel = admin_get_releases($project_id);

	foreach($rows_rel as $row_rel) {

		$list_box[$row_rel[RELEASE_ID]] = $row_rel[RELEASE_NAME];
	}
	$list_box[""] = "";

	html_print_list_box_from_key_array(	$list_box,
								session_validate_form_get_field('assigned_release') );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;


# ASSIGNED TO
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('assigned_to') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<select name='req_assigned_to'>". NEWLINE;
    $rows_users = user_get_usernames_by_project($project_id, true);
    html_print_list_box_from_array( $rows_users );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# REASON FOR CHANGE
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_reason_change') ."</td>". NEWLINE;
print"<td class=form-data-l><textarea rows='3' cols='45' name='req_reason_change'></textarea></td>". NEWLINE;
print"</tr>". NEWLINE;

if($record_or_file == "F") {

	# FILE
	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r nowrap>". lang_get('file_name') ." <span class='required'>*</span></td>". NEWLINE;
	print"<td class=form-data-l><input type=file name=upload_file size=45></td>". NEWLINE;
	print"</tr>". NEWLINE;
} else {

	# REQUIREMENT DETAIL
	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r nowrap>". lang_get('detail') ." <span class='required'>*</span></td>". NEWLINE;
	print"<td class=form-data-l>". NEWLINE;
	//print"<textarea rows='5' cols='45' name='req_detail_required'>$req_detail</textarea></td>". NEWLINE;
	html_FCKeditor("req_detail_required", 600, 250, $req_detail);
	print"</td>". NEWLINE;
	
	print"</tr>". NEWLINE;
}


# SUBMIT BUTTON

print"<tr>". NEWLINE;
print"<td colspan='2' class=center>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td colspan='2' class=center><input type='submit' value='". lang_get('add_new_version') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>". NEWLINE;
print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_add_new_version_page.php,v $
# Revision 1.9  2006/09/27 23:58:33  gth2
# Adding functionality to link a change request (defect_id) to a requirement - gth
#
# Revision 1.8  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.7  2006/08/01 23:41:34  gth2
# fixing bug related to fckeditor - gth
#
# Revision 1.6  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.5  2006/01/09 04:15:23  gth2
# cleaning up error checking for file upload - gth
#
# Revision 1.4  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.3  2005/12/13 13:59:56  gth2
# Completed the addition of requirement priority - gth
#
# Revision 1.2  2005/12/06 13:56:50  gth2
# Adding requirement priority and last updated - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>