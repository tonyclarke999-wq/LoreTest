<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Manage Project Page
#
# $RCSfile: project_manage_areatested_page.php,v $ $Revision: 1.2 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_manage_page	= 'project_manage_page.php';
$project_add_page     	= 'project_add_page.php';
$project_edit_page		= 'project_edit_page.php';
$user_manage_page		= 'user_manage_page.php';
$user_add_page			= 'user_add_page.php';
$project_manage_action	= 'project_manage_action.php';
$delete_page			= 'delete_page.php';

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];

session_set_properties("project_manage", $_GET);
$selected_project_properties 	= session_get_properties("project_manage");
$selected_project_id 			= $selected_project_properties['project_id'];

$project_manager		= user_has_rights( $selected_project_id, $user_id, MANAGER );

$redirect_url			= $page ."?project_id=". $selected_project_id;

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$row_style              = '';

$order_by 		= AREA_TESTED_NAME;
$order_dir		= "ASC";
$page_number	= 1;

util_set_order_by($order_by, $_GET);
util_set_order_dir($order_dir, $_GET);
util_set_page_number($page_number, $_GET);

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);


html_window_title();
html_print_body();
html_page_title(project_get_name($selected_project_id) ." - ". lang_get('manage_project_page') );
html_page_header( $db, $project_name );
html_print_menu();
admin_menu_print( $page, $project_id, $user_id );

error_report_check( $_GET );

$project_details = project_get_details( $selected_project_id );

print"<div class=center>". NEWLINE;

html_project_manage_menu();

print"<br>". NEWLINE;

if( !empty( $project_details ) ) {

	$project_id						= $project_details[PROJ_ID];
	$project_name					= $project_details[PROJ_NAME];
	$project_status					= $project_details[PROJ_STATUS];
	$project_description			= $project_details[PROJ_DESCRIPTION];

	####################################################################################
	# Areas Tested
	# ---------------------------------------------------------------------

	# New Area Tested
	if( $project_manager ) {
		print"<form method=post action='project_add_area_action.php'>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_area_tested')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('area_tested') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=60 maxlength=50 name='area_tested_required' value='".session_validate_form_get_field( 'area_tested_required' )."'>". NEWLINE;
		print"&nbsp;<input type=submit name='new_area_tested' value='".lang_get("add")."'>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;

		print"<br>";

	}

	# ---------------------------------------------------------------------
	# Areas Tested Table
	# ---------------------------------------------------------------------
	print"<form method=post name='area_tested_table' action='$page?order_by=$order_by&amp;order_dir=$order_dir'>";
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$rows_areas_tested = project_get_areas_tested($selected_project_id, $order_by, $order_dir, $page_number);
	print"<input type=hidden name='order_dir' value='$order_dir'>";
	print"<input type=hidden name='order_by' value='$order_by'>";
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $rows_areas_tested ) {
		print"<input type=hidden name=table value=project_manage_areas>". NEWLINE;
		print"<table class='width70' rules='cols'>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('area_tested'), AREA_TESTED_NAME, $order_by, $order_dir, "$page?page_number=$page_number", $page_number );
		if( $project_manager ) {
			html_tbl_print_header( lang_get('edit') );
			html_tbl_print_header( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;

		foreach($rows_areas_tested as $row_area) {

			$area_tested 	= $row_area[AREA_TESTED_NAME];
			$area_tested_id	= $row_area[AREA_TESTED_ID];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>$area_tested</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_area_tested_page.php?area_id=$area_tested_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='delete_area_tested' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_area_tested' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#area_tested'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_area_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$area_tested_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_AREA_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}

			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;

		print"<br>". NEWLINE;
	}

} else {
	html_no_records_found_message( lang_get('no_projects') );
}

print"</div>". NEWLINE;

html_print_footer();

# ------------------------------------
# $Log: project_manage_areatested_page.php,v $
# Revision 1.2  2006/08/05 22:08:24  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------

?>