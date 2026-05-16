<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Add Page
#
# $RCSfile: project_add_page.php,v $  $Revision: 1.3 $
# ------------------------------------

include"./api/include_api.php";

auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'project_add_action.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];

$default_req_upload_path 		 = FILE_UPLOAD_PATH ."[ProjectName]_req_docs";
$default_test_upload_path 		 = FILE_UPLOAD_PATH ."[ProjectName]_test_docs";
$default_test_plan_upload_path   = FILE_UPLOAD_PATH ."[ProjectName]_test_plan_docs";
$default_test_result_upload_path = FILE_UPLOAD_PATH ."[ProjectName]_test_result_docs";


$s_show_options = session_get_show_options();
$show_priority = $s_show_options['show_priority'];


if (isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
} else {
    $is_validation_failure = false;
}

global $db;

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('project_add_page') );
html_page_header( $db, $project_name );
html_print_menu();
admin_menu_print( $page, $project_id, $user_id );

if( !user_has_rights( $project_id, $user_id, ADMIN ) ) {
	print"<div align=center>";
	error_report_display_msg( NO_RIGHTS_TO_VIEW_PAGE );
	print"</div>";
	exit;
}

error_report_check( $_GET );

print"<br>". NEWLINE;

print"<div align=center>". NEWLINE;
print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span><br>". NEWLINE;

print"<table class=width75>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<form method=post action=$action_page>". NEWLINE;
print"<table class=inner rules=none border=0>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class='form-header-l' colspan='2'>".lang_get('add_project')."</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('project_name') ."<span class='required'>*</span></td>". NEWLINE;
print"<td class='form-data-l'>". NEWLINE;
print"<input type='text' size='67' maxlength='128' name=project_name_required value='" . session_validate_form_get_field ('project_name_required') ."'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('description') ."</td>". NEWLINE;
print"<td class='form-data-l'>". NEWLINE;
print"<textarea rows='4' cols='50' name='project_description' >"
			.session_validate_form_get_field('project_description')
			."</textarea>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# Enabled

$status_list_box 			= array( 	"Y"	=> lang_get('project_enable'),
										"N" => lang_get('project_disable') );

$status_list_box_selected 	= session_validate_form_get_field( 'project_status', 'Y' );

print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('status') ."</td>". NEWLINE;
print"<td class='form-data-l'>". NEWLINE;
print"<select name='project_status' size=1>". NEWLINE;
html_print_list_box_from_key_array(	$status_list_box,
									$status_list_box_selected );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;


	/*
    # Req Doc Upload Path
	print"<tr>". NEWLINE;
	print"<td class='form-lbl-r'>". lang_get('req_upload_path') ."</td>". NEWLINE;
	print"<td class='form-data-l'>". NEWLINE;
		print"<input type='text' size='67' maxlenght='255' name='req_upload_path' value='" . session_validate_form_get_field ('req_upload_path', $default_req_upload_path) ."'>". NEWLINE;
	print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	# Test Doc Upload Path
	print"<tr>". NEWLINE;
	print"<td class='form-lbl-r'>". lang_get('test_upload_path') ."</td>". NEWLINE;
	print"<td class='form-data-l'>". NEWLINE;
		print"<input type='text' size='67' maxlenght='255' name='test_upload_path' value='" . session_validate_form_get_field ('test_upload_path', $default_test_upload_path) ."'>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

    # Test Plan Doc Upload Path
	print"<tr>". NEWLINE;
	print"<td class='form-lbl-r'>". lang_get('test_plan_upload_path') ."</td>". NEWLINE;
	print"<td class='form-data-l'>". NEWLINE;
		print"<input type='text' size='67' maxlenght='255' name='test_plan_upload_path' value='" . session_validate_form_get_field ('test_plan_upload_path', $default_test_plan_upload_path) ."'>". NEWLINE;
	print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

    # Test Result Upload Path
	print"<tr>". NEWLINE;
	print"<td class='form-lbl-r'>". lang_get('test_result_upload_path') ."</td>". NEWLINE;
	print"<td class='form-data-l'>". NEWLINE;
		print"<input type='text' size='67' maxlenght='255' name='test_result_upload_path' value='" . session_validate_form_get_field ('test_result_upload_path', $default_test_result_upload_path) ."'>". NEWLINE;
		print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

    # Results Custom Field
	print"<tr>". NEWLINE;
	print"<td class='form-lbl-r'>". lang_get('results_custom_field1') ."</td>". NEWLINE;
	print"<td class='form-data-l'>". NEWLINE;
		print"<input type='text' size='40' maxlenght='40' name='results_custom_field1' value='" . session_validate_form_get_field ('results_custom_field1') ."'>". NEWLINE;
		print"<input type='checkbox' name='cf1_enabled' value='Y'". NEWLINE;
		if (util_is_checked($is_validation_failure, 'cf1_enabled')) print" ' checked'". NEWLINE;
        print">". lang_get('enabled')."</td>". NEWLINE;
    print"</tr>". NEWLINE;
	*/

print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('project_view_hide') ."</td>". NEWLINE;
print"<td class=form-data-l>". NEWLINE;

# Show/Hide Checkboxes
print"<table style='margin-right:auto;margin-left:0px;'><tr>";

$checkboxes_per_column = 4;
$view_hide_lang_names = array(	lang_get("show_custom_1"),
								lang_get("show_custom_2"),
								lang_get("show_custom_3"),
								lang_get("show_custom_4"),
								lang_get("show_window"),
								lang_get("show_object"),
								lang_get("show_mem_stats"),
								lang_get("show_custom_5"),
								lang_get("show_custom_6"),
								lang_get("show_priority"),
								lang_get("show_test_input") );

$view_hide_field_names = array(	PROJ_SHOW_CUSTOM_1,
								PROJ_SHOW_CUSTOM_2,
								PROJ_SHOW_CUSTOM_3,
								PROJ_SHOW_CUSTOM_4,
								PROJ_SHOW_WINDOW,
								PROJ_SHOW_OBJECT,
								PROJ_SHOW_MEM_STATS,
								PROJ_SHOW_CUSTOM_5,
								PROJ_SHOW_CUSTOM_6,
								PROJ_SHOW_PRIORITY,
								PROJ_SHOW_TEST_INPUT );

$number_of_checkboxes 	= count($view_hide_field_names);
$number_of_columns 		= $number_of_checkboxes/$checkboxes_per_column;

for($i=0; $i<=$number_of_columns; $i++) {

	$start_index	= $i*$checkboxes_per_column;
	$end_index		= $start_index+$checkboxes_per_column-1;

	if( $end_index+1>$number_of_checkboxes ) {

		$end_index=$number_of_checkboxes-1;
	}

	print"<td valign=top>";

	for($j=$start_index; $j<=$end_index; $j++) {

		$checked = "";
		if( session_validate_form_get_field($view_hide_field_names[$j], "Y") ) {
			$checked = "checked";
		}

		print"<input id=view_hide_$j type=checkbox name='".$view_hide_field_names[$j]."' $checked>";
		print"<label for=view_hide_$j>".$view_hide_lang_names[$j]."</label><br>". NEWLINE;
	}

	print"</td>";
}

print"</tr></table>";

print"</td>";
print"</tr>";

util_add_spacer();

print"<tr><td class=center colspan=2><input type=submit name='submit' value='". lang_get('submit_btn') ."'><br><br></td>". NEWLINE;
print"</table>". NEWLINE;
print"</form>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"<br>". NEWLINE;
print"</div>". NEWLINE;


html_print_footer();

# ------------------------------------
# $Log: project_add_page.php,v $
# Revision 1.3  2006/08/05 22:08:24  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------

?>