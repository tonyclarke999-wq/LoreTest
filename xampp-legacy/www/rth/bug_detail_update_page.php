<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Detail Update Page
#
# $RCSfile: bug_detail_update_page.php,v $  $Revision   $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'bug_detail_update_action.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id             = $s_project_properties['project_id'];

# Check to see if test_id is in GET (the user has clicked the pencil to go straight to update)
if( isset($_GET['bug_id']) ) {
	$s_bug_details		= session_set_properties("bug", $_GET);
}
else {
	$s_bug_details		= session_get_properties("bug");
}
$bug_id			= $s_bug_details['bug_id'];
$padded_bug_id	= util_pad_id( $bug_id );


$s_show_options = session_get_show_options();
$show_priority = $s_show_options['show_priority'];

if (isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
}
else {
    $is_validation_failure = false;

}

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('update_bug_page') );
html_page_header( $db, $project_name );
html_print_menu();
bug_menu_print ($page);

error_report_check( $_GET );

$row = bug_get_detail( $bug_id );

$category    			= $row[CATEGORY_NAME];
$category_id   			= $row[CATEGORY_ID];
$component				= $row[COMPONENT_NAME];
$component_id			= $row[COMPONENT_ID];
$priority        		= $row[BUG_PRIORITY];
$severity        		= $row[BUG_SEVERITY];
$closed_reason_code		= $row[BUG_CLOSED_REASON_CODE];
$bug_status				= $row[BUG_STATUS];
$reporter	     		= $row[BUG_REPORTER];
$reported_date   		= $row[BUG_REPORTED_DATE];
$assigned_to      		= $row[BUG_ASSIGNED_TO];
$assigned_to_developer	= $row[BUG_ASSIGNED_TO_DEVELOPER];
$closed		       		= $row[BUG_CLOSED];
$closed_date	 		= $row[BUG_CLOSED_DATE];
$test_verify_id       	= $row[BUG_TEST_VERIFY_ID];
$req_version_id    		= $row[BUG_REQ_VERSION_ID];
$found_in_release  		= $row[BUG_FOUND_IN_RELEASE];
$assign_to_release 		= $row[BUG_ASSIGN_TO_RELEASE];
$imp_in_release 		= $row[BUG_IMPLEMENTED_IN_RELEASE];
$discovery_period 		= $row[BUG_DISCOVERY_PERIOD];
$summary		 		= $row[BUG_SUMMARY];
$description			= $row[BUG_DESCRIPTION];

if ( !empty($row) ) {

	print"<br>";

	print"<div align=center>";
	print "<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span><br>";
	print"</div>". NEWLINE;

	print "<div align=center>";

	print"<table class=width95>";
	print"<tr>";
	print"<td>";
		print"<table class=inner rules=none border=0>";
		print"<form method=post action=$action_page>";

		# FORM TITLE
		print"<tr>";
		print"<td class=form-header-l colspan=2>".lang_get('update_bug')."</td>";
		print"</tr>";

		# BUG ID
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('bug_id') ."</td>". NEWLINE;
		print"<td class=form-data-l><a href='bug_detail_page.php?bug_id=$bug_id'>$padded_bug_id</a></td>". NEWLINE;
		print"</tr>". NEWLINE;

		# STATUS
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('bug_status') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
		$selected_value = session_validate_form_get_field('bug_status', $bug_status);
		print"<select name='bug_status'>". NEWLINE;
			$statuses = bug_get_status();
			html_print_list_box_from_array( $statuses, $selected_value);
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# CATEGORY
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('bug_category') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
		$selected_value = session_validate_form_get_field('bug_category', $category_id);
		print"<select name='bug_category'>". NEWLINE;
			$categories = bug_get_categories( $project_id, $blank=true );
			html_print_list_box_from_key_array( $categories, $selected_value );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# DISCOVERY PERIOD
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r nowrap>". lang_get('discovery_period') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
			$selected_value = session_validate_form_get_field('discovery_period', $discovery_period);
			print"<select name='discovery_period' size=1>". NEWLINE;
			$discovery_periods = bug_get_discovery_period( true );
			html_print_list_box_from_array( $discovery_periods, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# COMPONENT
		print"<td class=form-lbl-r>". lang_get('bug_component') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
		$selected_value = session_validate_form_get_field('bug_component', $component_id);
		print"<select name='bug_component'>". NEWLINE;
			html_print_list_box_from_key_array( bug_get_components( $project_id, $blank=true ), $selected=$selected_value);
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# PRIORITY
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('bug_priority') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
			$selected_value = session_validate_form_get_field('bug_priority', $priority);
			print"<select name='bug_priority' size=1>". NEWLINE;
			$bug_priorities = bug_get_priorities( true );
			html_print_list_box_from_array( $bug_priorities, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# SEVERITY
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('bug_severity') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
			$selected_value = session_validate_form_get_field('bug_severity', $severity);
			print"<select name='bug_severity' size=1>". NEWLINE;
			$bug_severities = bug_get_severities( true );
			html_print_list_box_from_array( $bug_severities, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# FOUND IN RELEASE
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r nowrap>". lang_get('found_in_release') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
			$selected_value = session_validate_form_get_field('found_in_release', $found_in_release);
			print"<select name='found_in_release' size=1>". NEWLINE;
			$releases = admin_get_all_release_names( $project_id, $blank=true );
			html_print_list_box_from_array( $releases, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# ASSIGN TO RELEASE
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r nowrap>". lang_get('assign_to_release') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
			$selected_value = session_validate_form_get_field('assign_to_release', $assign_to_release);
			print"<select name='assign_to_release' size=1>". NEWLINE;
			$releases = admin_get_all_release_names( $project_id, $blank=true );
			html_print_list_box_from_array( $releases, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# IMPLEMENTED IN RELEASE
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r nowrap>". lang_get('implemented_in_release') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
			$selected_value = session_validate_form_get_field('implemented_in_release', $imp_in_release);
			print"<select name='implemented_in_release' size=1>". NEWLINE;
			$releases = admin_get_all_release_names( $project_id, $blank=true );
			html_print_list_box_from_array( $releases, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# ASSIGN TO
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r nowrap>". lang_get('assigned_to') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
			$selected_value = session_validate_form_get_field('assigned_to', $assigned_to);
			print"<select name='assigned_to' size=1>". NEWLINE;
			$assigned_to = user_get_usernames_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $assigned_to, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# ASSIGN TO DEVELOPER
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r nowrap>". lang_get('assigned_to_developer') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
			$selected_value = session_validate_form_get_field('assigned_to_developer', $assigned_to_developer);
			print"<select name='assigned_to_developer' size=1>". NEWLINE;
			$assigned_to_developer = user_get_usernames_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $assigned_to_developer, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# TEST VERIFICATION ID
		# YOU CAN ONLY UPDATE THIS FROM THE VERFICATION PAGE
		/*
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('test_verification_id') ."</td>";
		print"<td class=form-data-l>". NEWLINE;
			print"<input type=text size='10' name='verify_id' maxlength='10' value='" . session_validate_form_get_field ('verify_id', $test_verify_id);
		print"'</td>". NEWLINE;
		print"</tr>". NEWLINE;
		*/

		# SUMMARY
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('bug_summary') ."<span class='required'>*</span></td>";
		print"<td class=form-data-l>". NEWLINE;
			print"<input type=text size='100' name='summary_required' value='" . session_validate_form_get_field ('summary_required', $summary);
		print"'</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# DESCRIPTION
		$description = session_validate_form_get_field('bug_description_required', $description, session_use_FCKeditor());
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('bug_description') ."<span class='required'>*</span></td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
		html_FCKeditor("bug_description_required", 640, 240, $description);
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		print"<tr><td class=center colspan=2><input type=submit name='save' value='Update'><br><br></td></tr>";

		print"</form>";
		print"</table>";
	print"</td>";
	print"</tr>";
	print"</table>";
	print"<br>";
	print"</div>";
	print"</div>";

}

html_print_footer();

# ------------------------------------
# $Log: bug_detail_update_page.php,v $
# Revision 1.3  2006/08/05 22:07:59  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------

?>