<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Page name
#
# $RCSfile: screen_edit_page.php,v $  
# $Revision: 1.2 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page           		= basename(__FILE__);
$form_name				= 'edit_build';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$row_style              = '';
$screen_id				= $_GET['screen_id'];


html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('build_edit_page') );
html_page_header( $db, $project_name );
html_print_menu();
test_menu_print ($page);

print"<br><br>". NEWLINE;

error_report_check( $_GET );


# GET THE DETAILS FOR THE GIVEN SCREEN_ID
$screen_details = test_get_screen( $screen_id);


$screen_name		= $screen_details[SCREEN_NAME];
$screen_desc		= $screen_details[SCREEN_DESC];
$screen_order		= $screen_details[SCREEN_ORDER];

print"<div align=center>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;

print"<td class=center>". NEWLINE;
print"<form method=post name='screen_edit_page' action='screen_edit_action.php'>". NEWLINE;
print"<input type=hidden name=screen_id value='$screen_id'>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td class='form-header-l' colspan='2'><h4>". lang_get('edit_screen') ." - $screen_name</h4></td>". NEWLINE;
print"</tr>". NEWLINE;

# SCREEN NAME
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('screen_name') ."<span class='required'>*</span></td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type='text' maxlength='50' name='screen_name_required' size=50 value='".
					session_validate_form_get_field('screen_name_required', $screen_name).
					"'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;


# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('description') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<textarea name='screen_desc' rows=5 cols=50 >".
					session_validate_form_get_field("screen_desc", $screen_desc).
					"</textarea>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# SCREEN ORDER
$order_numbers = test_get_screen_order_numbers( $screen_order );
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('order') ."</td>". NEWLINE;
print"<td align=left>". NEWLINE;
print"<select name='screen_order'>";
print"<option value='end'>At end of table</option>";
	html_print_list_box_from_key_array( $order_numbers );
print"<option selected>$screen_order</option>". NEWLINE;
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>";

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td colspan='2' align=center><input type='submit' value='". lang_get('submit_btn') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;


print"</form>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</div>". NEWLINE;

html_print_footer();

# ------------------------------------
# $Log: screen_edit_page.php,v $
# Revision 1.2  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1  2006/05/03 20:24:01  gth2
# no message
#
# ------------------------------------
?>