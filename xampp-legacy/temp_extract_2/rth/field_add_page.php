<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Screen page
#
# $RCSfile: field_add_page.php,v $ 
# $Revision: 1.2 $
# ------------------------------------
include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$delete_page			= 'delete_page.php';
$s_project_properties	= session_get_project_properties();
$project_name			= $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];
$row_style				= '';


$display_options	= session_set_display_options( "field", $_POST );
$order_by			= $display_options['order_by'];
$order_dir			= $display_options['order_dir'];

html_window_title();
html_print_body( 'add_field', 'field_name_required');
html_page_title($project_name ." - ". lang_get('field_page') );
html_page_header( $db, $project_name );
html_print_menu();
test_menu_print ($page);

print"<br>"; 
error_report_check( $_GET );

print"<div align='center'>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<form method=post name='add_field' action='field_add_action.php'>". NEWLINE;
print"<table class=inner>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td colspan='2'><h4>". lang_get('add_field') ."</h4></td>". NEWLINE;
print"</tr>". NEWLINE;

# FIELD NAME
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('field_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class='form-data-l'><input type='text' size='50' maxlength='50' name='field_name_required' value='".
	session_validate_form_get_field("field_name_required"). "'></td>". NEWLINE;
print"</tr>". NEWLINE;

# SCREEN 
$screens = test_get_screens( $project_id, SCREEN_NAME, "ASC" );
$screen_array = array();
foreach( $screens as $screen ) {
	$screen_array[$screen[SCREEN_ID]] = $screen[SCREEN_NAME];
}
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('screen_name') ."</td>". NEWLINE;
print"<td align=left>". NEWLINE;
print"<select name='screen_id'>";
	html_print_list_box_from_key_array( $screen_array,  session_validate_form_get_field("screen_id"));
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>";

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('description') ."</td>". NEWLINE;
print"<td class='form-data-l'>";
print"<textarea name='field_desc' rows=5 cols=50 >".
	session_validate_form_get_field("field_desc"). "</textarea>";
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# FIELD ORDER
$order_numbers = test_get_field_order_numbers();
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('order') ."</td>". NEWLINE;
print"<td align=left>". NEWLINE;
print"<select name='field_order'>";
print"<option value='end'>At end of table</option>";
if( !empty($order_numbers) ) {
	html_print_list_box_from_key_array( $order_numbers, session_validate_form_get_field("field_order") );
}
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>";

# TEXT-ONLY
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('text_box') ."</td>". NEWLINE;
print"<td class='form-data-l'><input type='checkbox' name='text_box'".
	(session_validate_form_get_field("text_box")=='on'?" checked":"") ."></td>". NEWLINE;
print"</tr>". NEWLINE;


# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td colspan='2' class='form-data-c'><input type='submit' value='". lang_get('add') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;
print"</form>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br><br>". NEWLINE;
print"</div>";

html_print_footer();

session_validate_form_reset();

# ------------------------------------
# $Log: field_add_page.php,v $
# Revision 1.2  2006/08/05 22:08:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1  2006/05/03 20:18:31  gth2
# no message
#
# ------------------------------------

?>