<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Add Page
#
# $RCSfile: bug_add_page.php,v $  $Revision: 1.3 $
# ------------------------------------

# TO DO
# add meta data ( component, category )
# Filter by component???
# check for test_run_id
# check for req_version_id
# add bug relationships

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'bug_add_action.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];

$s_show_options = session_get_show_options();
$show_priority = $s_show_options['show_priority'];

$s_project_properties = session_get_project_properties();
$project_id = $s_project_properties['project_id'];

if (isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
} else {
    $is_validation_failure = false;
}

global $db;

html_window_title();
html_print_body();
html_page_title( $project_name ." -  ". lang_get( 'add_bug_page' ));
html_page_header( $db, $project_name );
html_print_menu();
bug_menu_print( $page );

error_report_check( $_GET );

print"<br>". NEWLINE;
print"<div align=center>". NEWLINE;
print"<form method=post action=$action_page>". NEWLINE;

# check to see if the bug has an associated requirement or test
if( !empty( $_GET["assoc_req"] )) {

	print"<input type=hidden name='req_version_id' value='". $_GET["assoc_req"]."'>". NEWLINE;
}
if( !empty( $_GET["test_run_id"] ) && !empty( $_GET["verify_id"] ) ) {
	print"<input type=hidden name='test_run_id' value='". $_GET["test_run_id"] ."'>". NEWLINE;
	print"<input type=hidden name='verify_id' value='". $_GET["verify_id"] ."'>". NEWLINE;
}


print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span><br>". NEWLINE;

print"<table class=width95>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

    print"<table class=inner rules=none border=0>". NEWLINE;

    print"<tr>". NEWLINE;
    print"<td class=form-header-l colspan=2>".lang_get('add_bug')."</td>". NEWLINE;
    print"</tr>". NEWLINE;

	# CATEGORY
	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r>". lang_get('bug_category') ."</td>". NEWLINE;
	print"<td class=form-data-l>". NEWLINE;
	$selected_value = session_validate_form_get_field( 'bug_category' );
	print"<select name='bug_category'>". NEWLINE;
		html_print_list_box_from_key_array( bug_get_categories( $project_id, $blank=true ), $selected=$selected_value);
	print"</select>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# DISCOVERY PERIOD
	print"<tr>". NEWLINE;
    print"<td class=form-lbl-r nowrap>". lang_get('discovery_period') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'discovery_period' );
        print"<select name='discovery_period' size=1>". NEWLINE;
        $discovery_periods = bug_get_discovery_period( true );
        html_print_list_box_from_array( $discovery_periods, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	# COMPONENT
	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r>". lang_get('bug_component') ."</td>". NEWLINE;
	print"<td class=form-data-l>". NEWLINE;
	$selected_value = session_validate_form_get_field( 'bug_component' );
	print"<select name='bug_component'>". NEWLINE;
		html_print_list_box_from_key_array( bug_get_components( $project_id, $blank=true ), $selected=$selected_value);
	print"</select>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# PRIORITY
	print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('bug_priority') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'bug_priority' );
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
        $selected_value = session_validate_form_get_field( 'bug_severity' );
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
        $selected_value = session_validate_form_get_field( 'found_in_release' );
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
        $selected_value = session_validate_form_get_field( 'assign_to_release' );
        print"<select name='assign_to_release' size=1>". NEWLINE;
        $releases = admin_get_all_release_names( $project_id, $blank=true );
        html_print_list_box_from_array( $releases, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	# IMPLEMENTED IN RELEASE
	/*print"<tr>". NEWLINE;
	print"<td class=form-lbl-r nowrap>". lang_get('implemented_in_release') ."</td>". NEWLINE;
	print"<td class=form-data-l>". NEWLINE;
		$selected_value = session_validate_form_get_field('implemented_in_release');
		print"<select name='implemented_in_release' size=1>". NEWLINE;
		$releases = admin_get_all_release_names( $project_id, $blank=true );
		html_print_list_box_from_array( $releases, $selected_value);
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;*/
		
	# ASSIGN TO
	print"<tr>". NEWLINE;
    print"<td class=form-lbl-r nowrap>". lang_get('assign_to') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'assigned_to' );
        print"<select name='assigned_to' size=1>". NEWLINE;
        $assigned_to = user_get_usernames_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $assigned_to, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	# ASSIGN TO DEVELOPER
	print"<tr>". NEWLINE;
    print"<td class=form-lbl-r nowrap>". lang_get('assign_to_developer') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'assigned_to_developer' );
        print"<select name='assigned_to_developer' size=1>". NEWLINE;
        $assigned_to = user_get_usernames_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $assigned_to, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	# SUMMARY
	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r>". lang_get('bug_summary') ."<span class='required'>*</span></td>";
	print"<td class=form-data-l>". NEWLINE;
        print"<input type=text size='100' maxlength='128' name=summary_required value='". session_validate_form_get_field( 'summary_required' ) ."'>";
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# DESCRIPTION
	$description = session_validate_form_get_field( 'description_required', "", session_use_FCKeditor() );
    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('bug_description') ."<span class='required'>*</span></td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
    html_FCKeditor("description_required", 640, 240, $description);
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	util_add_spacer();

    print"<tr><td class=center colspan=2><input type=submit name='save' value='Save'><br><br></td>". NEWLINE;

    print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</form>". NEWLINE;

print"</div>". NEWLINE;


html_print_footer();

# ------------------------------------
# $Log: bug_add_page.php,v $
# Revision 1.3  2006/08/05 22:07:58  gth2
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
