<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Group Action Page
#
# $RCSfile: bug_group_action_page.php,v $    $Revision: 1.3 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page			= 'bug_group_action.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];
$redirect_on_error		= 'bug_page.php';

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('bug_mass_update_page'));
html_page_header( $db, $project_name );
html_print_menu();
test_menu_print($page);

$ids = "";

# if submit from bug_page.php
if( isset($_POST['mass_update_btn']) && isset($_POST['row_bug_arr']) ) {
	
	$field = $_POST['action'];
	
	foreach( $_POST['row_bug_arr'] as $key => $value) {
		$ids .= $key .":";
	}
}
else {
	error_report_show("test_page.php", NO_BUGS_SELECTED);
}


switch( $field ) {

	case 'bug_status':
		$form_title = lang_get( 'update_bug_status' );
		$possible_values = bug_get_status();
		break;
	case 'assign_to':
		$form_title = lang_get( 'update_assign_to' );
		$possible_values = user_get_usernames_by_project( $project_id );
		break;
	case 'assign_to_dev':
		$form_title = lang_get( 'update_assign_to_developer' );
		$possible_values = user_get_usernames_by_project( $project_id );
		break;
}

print"<br>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<form method=post action=$action_page>". NEWLINE;

print"<table class=width50>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<table class=inner>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td class='form-header-l'>$form_title</td>". NEWLINE;
print"</tr>". NEWLINE;

# VALUE
print"<tr>". NEWLINE;
print"<td class='form-data-c'>";
print"<select name=field_value size=1>". NEWLINE;
	html_print_list_box_from_array(	$possible_values );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<input type='hidden' name=bug_ids value='$ids'>";
print"<input type='hidden' name=field value='$field'>";

util_add_spacer();

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td class='form-data-c'><input type='submit' name=submit_status value='". lang_get('update') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</form>". NEWLINE;

print"</div>". NEWLINE;

html_print_footer();


# ------------------------------------
# $Log: bug_group_action_page.php,v $
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