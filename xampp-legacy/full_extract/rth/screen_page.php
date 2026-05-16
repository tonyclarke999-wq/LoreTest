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
# $RCSfile: screen_page.php,v $ 
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


$display_options	= session_set_display_options( "screen", $_POST );
$order_by			= $display_options['order_by'];
$order_dir			= $display_options['order_dir'];
$page_number		= 1;

html_window_title();
html_print_body( 'add_screen', 'screen_name_required');
html_page_title($project_name ." - ". lang_get('screen_page') );
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

print"<form method=post name='add_screen' action='screen_add_action.php'>". NEWLINE;
print"<table class=inner>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td colspan='2'><h4>". lang_get('add_screen') ."</h4></td>". NEWLINE;
print"</tr>". NEWLINE;

# SCREEN NAME
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('screen_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class='form-data-l'><input type='text' size='50' maxlength='50' name='screen_name_required' value='".
	session_validate_form_get_field("screen_name_required"). "'></td>". NEWLINE;
print"</tr>". NEWLINE;

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('description') ."</td>". NEWLINE;
print"<td class='form-data-l'>";
print"<textarea name='screen_desc' rows=5 cols=50 >".
	session_validate_form_get_field("screen_desc"). "</textarea>";
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# SCREEN ORDER
$order_numbers = test_get_screen_order_numbers();
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('order') ."</td>". NEWLINE;
print"<td align=left>". NEWLINE;
print"<select name='screen_order'>";
print"<option value='end'>At end of table</option>";
if( !empty($order_numbers) ) {
	html_print_list_box_from_key_array( $order_numbers );
}
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>";

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

$screen_details = test_get_screens( $project_id, $order_by, $order_dir );

print"<form method=post action='$page?order_by=$order_by&amp;order_dir=$order_dir'>". NEWLINE;
print"<table class=hide100>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
$screen_details = test_get_screens( $project_id, $order_by, $order_dir, $page_number );
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>". NEWLINE;


if( !empty( $screen_details ) ) {
	print"<table class=width100 rules=cols>". NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;

	html_tbl_print_header( lang_get('screen_name'),  SCREEN_NAME, $order_by, $order_dir, "$page?page_number=$page_number" );
	html_tbl_print_header( lang_get('order'),  SCREEN_ORDER, $order_by, $order_dir, "$page?page_number=$page_number" );
	html_tbl_print_header( lang_get('description') );
	html_tbl_print_header( lang_get('edit') );
	html_tbl_print_header( lang_get('delete') );

	print"</tr>". NEWLINE;

	foreach( $screen_details as $screen_detail ) {


		$screen_id				= $screen_detail[SCREEN_ID];
		$screen_name			= $screen_detail[SCREEN_NAME];
		$screen_desc			= $screen_detail[SCREEN_DESC];
		$screen_order			= $screen_detail[SCREEN_ORDER];


		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		print"<td class='tbl-l'>$screen_name</td>". NEWLINE;
		print"<td class='tbl-l'>$screen_order</td>". NEWLINE;
		print"<td class='tbl-c'>$screen_desc</td>". NEWLINE;
		print"<td class='tbl-c'><a href='screen_edit_page.php?screen_id=$screen_id'>". lang_get('edit') ."</a></td>". NEWLINE;
		print"<td class='tbl-c'>";
			print"<form method=post action='$delete_page'>". NEWLINE;
			print"<input type='submit' name='delete' value='". lang_get( 'delete' ) ."' class='page-numbers'>";
			print"<input type='hidden' name='r_page' value=$page>". NEWLINE;
			print"<input type='hidden' name='f' value='delete_screen'>". NEWLINE;
			print"<input type='hidden' name='id' value=$screen_id>". NEWLINE;
			print"<input type='hidden' name='msg' value='300'>". NEWLINE;
			print"</form>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	print"<input type='hidden' name='screen_id' value='$screen_id'>";

	print"</table>". NEWLINE;
	#print"</form>". NEWLINE;
} else {
	html_no_records_found_message( lang_get('no_screens') );
}

print"</div>";

html_print_footer();

session_validate_form_reset();

# ------------------------------------
# $Log: screen_page.php,v $
# Revision 1.2  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1  2006/05/03 20:24:01  gth2
# no message
#
# ------------------------------------

?>