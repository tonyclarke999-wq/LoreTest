<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Admin page
#
# $RCSfile: admin_page.php,v $ $Revision: 1.4 $
# ------------------------------------
include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_manage_page	= 'project_manage_page.php';
$project_add_page     	= 'project_add_page.php';
$project_edit_page		= 'project_edit_page.php';
$user_manage_page		= 'user_manage_page.php';
$user_add_page			= 'user_add_page.php';
$delete_page			= 'delete_page.php';
$redirect_url			= $page;

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$row_style              = '';

session_validate_form_reset();

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get("manage_projects_page") );
html_page_header( $db, $project_name );
html_print_menu();
admin_menu_print( $page, $project_id, $user_id );

$s_table_options = session_set_display_options("admin", $_POST);
$order_by = $s_table_options['order_by'];
$order_dir = $s_table_options['order_dir'];

$rows_project_details = project_get_all_projects_details( $order_by, $order_dir);

print"<br>";

print"<div align=center>". NEWLINE;

if( !empty( $rows_project_details ) ) {
	print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
	print"<thead>".NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;
	#html_tbl_print_header( lang_get('project_name'), PROJ_NAME, $order_by, $order_dir, $page );
	#html_tbl_print_header( lang_get('status'), PROJ_STATUS,	$order_by, $order_dir, $page );
	#html_tbl_print_header( lang_get('date_created'), PROJ_DATE_CREATED, $order_by, $order_dir, $page );
	#html_tbl_print_header( lang_get('description') );
	#html_tbl_print_header( lang_get('delete') );
	
	html_tbl_print_header( lang_get('project_name') );
	html_tbl_print_header_not_sortable( lang_get('status') );
	html_tbl_print_header( lang_get('date_created') );
	html_tbl_print_header( lang_get('description') );
	html_tbl_print_header_not_sortable( lang_get('delete') );
	print"</tr>". NEWLINE;
	print"</thead>".NEWLINE;
	print"<tbody>".NEWLINE;
	foreach($rows_project_details as $row_project_detail) {

		$project_id			= $row_project_detail[PROJ_ID];
		$project_name		= $row_project_detail[PROJ_NAME];
		$status				= $row_project_detail[PROJ_STATUS];
		$date_created		= $row_project_detail[PROJ_DATE_CREATED];
		$description		= $row_project_detail[PROJ_DESCRIPTION];

		#$row_style = html_tbl_alternate_bgcolor( $row_style );
		#print"<tr class='$row_style'>". NEWLINE;
		
		print"<tr>". NEWLINE;
		if( user_has_rights( $project_id, $user_id, USER ) ) {
			print"<td class='tbl-l'><a href='$project_manage_page?project_id=$project_id'>$project_name</a></td>". NEWLINE;
		} else {
			print"<td class='tbl-l'>$project_name</td>". NEWLINE;
		}
		$status == 'Y' ? $project_status = 'enabled' : $project_status = 'disabled';
		print"<td class='tbl-c'>$project_status</td>". NEWLINE;
		print"<td class='tbl-c'>$date_created</td>". NEWLINE;
		print"<td class='tbl-c'>$description</td>". NEWLINE;
		if( user_has_rights( $project_id, $user_id, ADMIN ) ) {
			print"<td class='tbl-c'>";
			print"<form method=post action='$delete_page'>". NEWLINE;
			print"<input type='submit' name='delete_project' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
			print"<input type='hidden' name='r_page' value='$redirect_url'>". NEWLINE;
			print"<input type='hidden' name='f' value='delete_project'>". NEWLINE;
			print"<input type='hidden' name='id' value='$project_id'>". NEWLINE;
			print"<input type='hidden' name='msg' value='". DEL_PROJECT ."'>". NEWLINE;
			print"</form>". NEWLINE;
			print"</td>". NEWLINE;
		} else {
			print"<td class='tbl-c'></td>". NEWLINE;
		}
		print"</tr>". NEWLINE;
	}
	print"</tbody>".NEWLINE;
	print"</table>". NEWLINE;

} else {
	html_no_records_found_message( lang_get('no_projects') );
}

print"</div>". NEWLINE;

html_print_footer();

# ------------------------------------
# $Log: admin_page.php,v $
# Revision 1.4  2008/01/22 07:55:16  cryobean
# made the table sortable
#
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