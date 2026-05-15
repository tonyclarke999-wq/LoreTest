<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Group Action Page
#
# $RCSfile: requirement_group_action_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page			= 'requirement_group_action.php';
$redirect_on_error		= 'requirement_page.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('req_mass_update_page'));
html_page_header( $db, $project_name );
html_print_menu();
requirement_menu_print($page);

$ids = "";
$version_ids = "";


# if submit from test_page.php
if( isset($_POST['mass_req_update']) && isset($_POST['row_req_arr']) ) {

	$field = $_POST['action'];
	$post_ids = $_POST['row_req_arr'];
	//print_r($ids);

	foreach($post_ids as $req_id => $value) {

		$version_array = $_POST['row_req_arr'][$req_id];

		foreach( $version_array as $vers_id => $vers_val ) {
			$version_ids .= $vers_id . "|";
		}

		$ids .= $req_id ."|";
	}

}
else {
	error_report_show( $redirect_on_error, NO_REQ_SELECTED);
}

# Trim off last "|"
$version_ids = trim($version_ids, "|");
$ids = trim($ids, "|");


switch( $field ) {

	case 'status':
		$form_title = lang_get( 'update_req_status' );
		$possible_values = requirement_get_statuses();
		break;
	case 'assigned_release':
		$form_title = lang_get( 'update_req_release' );
		$possible_values = admin_get_all_release_names( $project_id, $blank=true );
		break;
}


print"<br>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<form method=post action=$action_page>". NEWLINE;
print"<input type='hidden' name='ids' value='$ids'>". NEWLINE;
print"<input type='hidden' name='version_ids' value='$version_ids'>". NEWLINE;
print"<input type='hidden' name=field value='$field'>". NEWLINE;

print"<table class=width50>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<table class=inner>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td class='form-data-l'><h4>$form_title</h4></td>". NEWLINE;
print"</tr>". NEWLINE;

# VALUE
print"<tr>". NEWLINE;
print"<td class='form-data-c'>";
print"<select name=field_value size=1>". NEWLINE;
switch( $field ) {
	case 'status':
		html_print_list_box_from_array(	$possible_values );
		break;
	case 'assigned_release':
		html_print_list_box_from_key_array(	$possible_values );
		break;
}
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

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

# ---------------------------------------------------------------------
# $Log: requirement_group_action_page.php,v $
# Revision 1.3  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>