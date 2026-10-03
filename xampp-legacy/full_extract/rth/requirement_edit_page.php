<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Edit Page
#
# $RCSfile: requirement_edit_page.php,v $  $Revision: 1.9 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_properties     = session_get_project_properties();
$project_id				= $project_properties['project_id'];
$project_name           = $project_properties['project_name'];
$username				= session_get_username();

$display_options 		= session_set_display_options( "requirements", array_merge($_POST, $_GET) );
$s_tab 					= $display_options['tab'];

$s_properties 			= session_set_properties("requirements", $_GET);
$s_req_id				= $s_properties['req_id'];
$s_req_version_id		= $s_properties['req_version_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - REQUIREMENT DETAIL");
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);

error_report_check( $_GET );

$rows_requirement = requirement_get_detail( $project_id, $s_req_id, $s_req_version_id );
$row_requirement = $rows_requirement[0];

$req_version_id			= $row_requirement[REQ_VERS_UNIQUE_ID];
$req_rec_or_file		= $row_requirement[REQ_REC_FILE];
$req_name				= $row_requirement[REQ_FILENAME];
$req_detail				= $row_requirement[REQ_VERS_DETAIL];
$req_reason_for_change	= $row_requirement[REQ_VERS_REASON_CHANGE];
$req_version_status		= $row_requirement[REQ_VERS_STATUS];
$req_area_covered		= $row_requirement[REQ_AREA_COVERAGE];
$req_area_covered_id	= $row_requirement[REQ_AREA_COVERAGE_ID];
$req_doc_type			= $row_requirement[REQ_DOC_TYPE_NAME];
$req_doc_type			= $row_requirement[REQ_DOC_TYPE_ID];
$req_priority			= $row_requirement[REQ_PRIORITY];
$req_defect_id			= $row_requirement[REQ_VERS_DEFECT_ID];
$selected_release		= requirement_get_release( $req_version_id );

print"<br>";

print"<form method=post action='requirement_edit_action.php'>". NEWLINE;
print"<input type=hidden name=req_id value=$s_req_id>". NEWLINE;
print"<input type=hidden name=project_id value=$project_id>". NEWLINE;
print"<input type=hidden name=req_ver_id value=$req_version_id>". NEWLINE;
print"<input type=hidden name=record_or_file value='$req_rec_or_file'>". NEWLINE;
print"<div align=center>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width90>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;
print"<tr>". NEWLINE;
print"<td colspan=2><h4>". lang_get('edit_requirement') ."</h4></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=center>". NEWLINE;

# NAME
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;
print"<input type='text' maxlength='255' name='req_name_required' size=65 value='".
					session_validate_form_get_field('req_name_required', $req_name).
					"'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

if($row_requirement[REQ_REC_FILE]=="R") {
	# DETAIL
	$detail = session_validate_form_get_field('req_detail_required', $req_detail, session_use_FCKeditor());
	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r nowrap>". lang_get('req_detail') ." <span class='required'>*</span></td>". NEWLINE;
	print"<td class=form-data-l>$detail</td>". NEWLINE;
	/*
	print"<td class=form-data-l>";
	html_FCKeditor("req_detail_required", 360, 100, $detail);
	print"</td>". NEWLINE;
	*/
	print"<input type='hidden' name='req_detail_required' value='$detail'>";
	print"</tr>". NEWLINE;
}

# REASON FOR CHANGE
$reason_for_change = session_validate_form_get_field('req_reason_change', $req_reason_for_change, session_use_FCKeditor());
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_reason_change') ."</td>". NEWLINE;
print"<td class=form-data-l>";
html_FCKeditor("req_reason_change", 360, 100, $reason_for_change);
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# STATUS
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_status') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;

print"<select name=req_status size=1>". NEWLINE;
	$list_box = requirement_get_statuses();

	html_print_list_box_from_array(	$list_box,
									session_validate_form_get_field('req_status',
																	$req_version_status) );
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
# Set the field to an empty string if the value of the defect id = 0
if( $req_defect_id != 0 ) {
	$req_defect_id = util_pad_id( $req_defect_id );
}
else {  
	$req_defect_id = '';  
}
$defect_id = session_validate_form_get_field('defect_id');
print"<tr>". NEWLINE;
print"<td class=form-lbl-r nowrap>". lang_get('req_defect_id') ."</td>". NEWLINE;
print"<td class=form-data-l>";
print"<input type='text' size='10' name='defect_id' value='". session_validate_form_get_field('defect_id', $req_defect_id) ."'></td>". NEWLINE;
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

	html_print_list_box_from_key_array(	$list_box,
										session_validate_form_get_field('req_functionality',
																		$function_ids ) );

print"</select>". NEWLINE;
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
																		$req_doc_type) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

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
										session_validate_form_get_field('req_area',
																		$selected_release) );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td colspan='2' class=center><input type='submit' value='". lang_get('submit_btn') ."'></td>". NEWLINE;

print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</div>". NEWLINE;
print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_edit_page.php,v $
# Revision 1.9  2006/11/03 14:02:16  gth2
# correcting errors with requirements pages.
# Change Request field dispalying 00000
# Undefined index error when updating a child requirement
# gth2
#
# Revision 1.8  2006/09/27 23:58:33  gth2
# Adding functionality to link a change request (defect_id) to a requirement - gth
#
# Revision 1.7  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.6  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.5  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.4  2005/12/08 22:13:40  gth2
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