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
# $RCSfile: field_page.php,v $ 
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
$page_number		= $display_options['page_number'];
$per_page			= RECORDS_PER_PAGE_25;
$filter_screen		= $display_options['filter']['filter_screen'];
$filter_search		= $display_options['filter']['filter_search'];


html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('screen_page') );
html_page_header( $db, $project_name );
html_print_menu();
test_menu_print ($page);

print"<br><br>". NEWLINE; 
print"<div align=center>". NEWLINE;
print"<form method='post' action='$page' name='field_form'>". NEWLINE;

error_report_check( $_GET );

# Print filter form
html_print_field_filter( $filter_screen, $filter_search );
print"<br><br>". NEWLINE;


# get data based on filters and users choices
$field_details = test_filter_fields( $filter_screen,
									 $filter_search,
									 $order_by,
								     $order_dir,
									 $page_number,
									 $per_page,
									 $csv_name="fields" );


//$field_details = test_get_fields( $project_id, $order_by, $order_dir );

if( !empty( $field_details ) ) {

	print"<table class=width100 rules=cols>". NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;

	html_tbl_print_header( lang_get('field_name'),  FIELD_NAME, $order_by, $order_dir, $page );
	html_tbl_print_header( lang_get('order'),  FIELD_ORDER, $order_by, $order_dir, $page );
	html_tbl_print_header( lang_get('screen_name'), SCREEN_NAME, $order_by, $order_dir, $page );
	html_tbl_print_header( lang_get('description') );
	html_tbl_print_header( lang_get('edit') );
	html_tbl_print_header( lang_get('delete') );

	print"</tr>". NEWLINE;

	foreach( $field_details as $field_detail ) {


		$field_id				= $field_detail[FIELD_ID];
		$field_name				= $field_detail[FIELD_NAME];
		$field_order			= $field_detail[FIELD_ORDER];
		$field_desc				= $field_detail[FIELD_DESC];
		$screen_name			= $field_detail[SCREEN_NAME];
		$screen_id				= $field_detail[SCREEN_ID];
		$text_box				= $field_detail[FIELD_TEXT_ONLY];

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		print"<td class='tbl-l'>$field_name</td>". NEWLINE;
		print"<td class='tbl-l'>$field_order</td>". NEWLINE;
		print"<td class='tbl-c'>$screen_name</td>". NEWLINE;
		print"<td class='tbl-c'>$field_desc</td>". NEWLINE;
		if( $text_box == 'Y' ) {
			print"<td class='tbl-c'><a href='field_edit_page.php?screen_id=$field_id'>". lang_get('field_edit_link') ."</a></td>". NEWLINE;
		}
		else {
			print"<td class='tbl-c'>";
			print"<a href='field_edit_page.php?screen_id=$field_id'>". lang_get('field_edit_link') ."</a> | ";
			print"<a href='field_edit_page.php?screen_id=$field_id'>". lang_get('field_value_link') ."</a>";
			print"</td>". NEWLINE;
		}
		print"<td class='tbl-c'>";
			print"<form method=post action='$delete_page'>". NEWLINE;
			print"<input type='submit' name='delete' value='". lang_get( 'delete' ) ."' class='page-numbers'>";
			print"<input type='hidden' name='r_page' value=$page>". NEWLINE;
			print"<input type='hidden' name='f' value='delete_field'>". NEWLINE;
			print"<input type='hidden' name='id' value=$field_id>". NEWLINE;
			print"<input type='hidden' name='msg' value='310'>". NEWLINE;
			print"</form>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	print"<input type='hidden' name='field_id' value='$field_id'>";

	print"</table>". NEWLINE;
	#print"</form>". NEWLINE;
} else {
	html_no_records_found_message( lang_get('no_fields') );
}

print"</form>". NEWLINE;
print"</div>";

html_print_footer();

session_validate_form_reset();

# ------------------------------------
# $Log: field_page.php,v $
# Revision 1.2  2006/08/05 22:08:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1  2006/05/03 20:18:31  gth2
# no message
#
# ------------------------------------

?>