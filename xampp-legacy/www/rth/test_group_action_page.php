<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Group Action Page
#
# $RCSfile: test_group_action_page.php,v $  $Revision: 1.6 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('test_mass_update_page'));
html_page_header( $db, $project_name );
html_print_menu();
test_menu_print($page);

$ids = "";

# if submit from test_page.php
if( isset($_POST['mass_update_btn']) && isset($_POST['row_test_arr']) ) {
	
	$field = $_POST['action'];
	
	foreach( $_POST['row_test_arr'] as $key => $value) {
		$ids .= $key .":";
	}
}
else {
	error_report_show("test_page.php", NO_TESTS_SELECTED);
}

switch( $field ) {

	case 'man_auto':
		$form_title = lang_get( 'update_man_auto' );
		$possible_values = test_get_man_auto_values();
		break;
	case 'ba_owner':
		$form_title = lang_get( 'update_ba_owner' );
		$possible_values = user_get_baowners_by_project($project_id, $blank=true);
		break;
	case 'qa_owner':
		$form_title = lang_get( 'update_qa_owner' );
		$possible_values = user_get_qaowners_by_project($project_id, $blank=true);
		break;
	case 'tester':
		$form_title = lang_get( 'update_tester' );
		$possible_values = user_get_usernames_by_project($project_id, $blank=true);
		break;
	case 'test_status':
		$form_title = lang_get( 'update_test_status' );
		$possible_values = test_get_status( $blank=true );
		break;
	case 'test_priority':
		$form_title = lang_get( 'update_priority' );
		$possible_values = test_get_priorities();
		break;
	case 'auto_pass':
		$form_title = lang_get( 'update_autopass' );
		$possible_values = test_get_autopass_values();
		break;
	case 'test_type':
		$form_title = lang_get( 'update_testtype' );
		$possible_values = test_get_test_type( $project_id  );
		break;
	case 'area_tested':
		$form_title = lang_get( 'update_area_tested' );
		$possible_values = test_get_test_value($project_id, TEST_AREA_TESTED, $blank=true);
		break;
	case 'email_ba_owner':
		$form_title = lang_get( 'email_ba_owner' );
		$possible_values = array("Enabled", "Disabled"); 
		break;
	case 'email_qa_owner':
		$form_title = lang_get( 'email_qa_owner' );
		$possible_values = array("Enabled", "Disabled");
		break;

}


print"<br>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<form method=post action='test_group_action.php'>". NEWLINE;

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
	html_print_list_box_from_array(	$possible_values );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<input type='hidden' name=test_ids value='$ids'>";
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

# ---------------------------------------------------------------------
# $Log: test_group_action_page.php,v $
# Revision 1.6  2008/06/30 12:38:42  peter_thal
# changed misspelling at line 84
#
# Revision 1.5  2008/03/25 12:59:04  cryobean
# fixed bug with mass update of testers in test library
#
# Revision 1.4  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.3  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>