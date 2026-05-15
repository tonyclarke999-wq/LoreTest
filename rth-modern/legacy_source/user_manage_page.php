<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# User Manage Page
#
# $RCSfile: user_manage_page.php,v $  $Revision: 1.6 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$delete_page			= 'delete_page.php';
$redirect_url			= $page;

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$row_style              = '';

$s_user_manage_display_options	= session_set_display_options("user_manage", $_POST);
$order_by						= $s_user_manage_display_options['order_by'];
$order_dir						= $s_user_manage_display_options['order_dir'];
$page_number					= $s_user_manage_display_options['page_number'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('user_manage_page') );
html_page_header( $db, $project_name );
html_print_menu();
admin_user_print( $page, $project_id, $user_id );

print"<br>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<form method=post action='$page'>". NEWLINE;
print"<table class=hide80>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
$users	= user_get_all($order_by, $order_dir, $page_number, '');
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>". NEWLINE;

print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
print"<thead>".NEWLINE;
print"<tr>". NEWLINE;
#html_tbl_print_header( lang_get('id'),			USER_ID,	$order_by, $order_dir, $page );
#html_tbl_print_header( lang_get('username'),	USER_UNAME,	$order_by, $order_dir, $page );
#html_tbl_print_header( lang_get('first_name'),	USER_FNAME,	$order_by, $order_dir, $page );
#html_tbl_print_header( lang_get('last_name'),	USER_LNAME, $order_by, $order_dir, $page );
#html_tbl_print_header( lang_get('email'),		USER_EMAIL, $order_by, $order_dir, $page );
#html_tbl_print_header( lang_get('deleted'),	  USER_DELETED, $order_by, $order_dir, $page );

html_tbl_print_header( lang_get('id') );
html_tbl_print_header( lang_get('username') );
html_tbl_print_header( lang_get('first_name') );
html_tbl_print_header( lang_get('last_name') );
html_tbl_print_header( lang_get('email') );
html_tbl_print_header( lang_get('deleted') );
if( user_has_rights( $project_id, $user_id, ADMIN ) ) {
	html_tbl_print_header_not_sortable( lang_get('edit') );
	html_tbl_print_header_not_sortable( lang_get('remove') );
}
print"</tr>". NEWLINE;
print"</thead>".NEWLINE;
print"<tbody>".NEWLINE;
foreach($users as $user_row) {

	$row_user_id	= util_pad_id($user_row[USER_ID]);
	$row_username	= $user_row[USER_UNAME];
	$row_first_name	= $user_row[USER_FNAME];
	$row_last_name	= $user_row[USER_LNAME];
	$row_user_email	= $user_row[USER_EMAIL];
	$user_deleted	= $user_row[USER_DELETED];

	#$row_style = html_tbl_alternate_bgcolor($row_style);
	#print"<tr class='$row_style'>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>$row_user_id</td>". NEWLINE;
	if( $user_deleted == 'Y' ) { 
		print"<td><del>$row_username</del></td>". NEWLINE;
	}
	else {
		print"<td>$row_username</td>". NEWLINE;
	}
	
	print"<td>$row_first_name</td>". NEWLINE;
	print"<td>$row_last_name</td>". NEWLINE;
	print"<td>$row_user_email</td>". NEWLINE;
	print"<td>$user_deleted</td>". NEWLINE;

	if ( user_has_rights($project_id, $user_id, ADMIN) ) {
		print"<td>". NEWLINE;
		if(  $user_deleted == 'N' ) {
			print"<a href='user_edit_account_page.php?user_id=$row_user_id'>Edit</a>";
		}
		print"</td>". NEWLINE;

		print"<td>". NEWLINE;
			if( $user_deleted == 'N' ) {
				print"<form method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_project' value='". lang_get( 'remove' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url'>". NEWLINE;
				print"<input type='hidden' name='f' value='delete_user'>". NEWLINE;
				print"<input type='hidden' name='id' value='".$user_row[USER_ID]."'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_USER ."'>". NEWLINE;
				print"</form>". NEWLINE;
			}
		print"</td>". NEWLINE;
	}

	print"</tr>". NEWLINE;
}
print"</tbody>".NEWLINE;
print"</table>". NEWLINE;
print"</div>". NEWLINE;
html_print_footer();

# ---------------------------------------------------------------------
# $Log: user_manage_page.php,v $
# Revision 1.6  2008/01/22 07:12:37  cryobean
# made the manage user table sortable
#
# Revision 1.5  2007/02/02 04:28:15  gth2
# adding strikethrough to make deleted users more visible - gth
#
# Revision 1.4  2006/12/05 05:02:05  gth2
# display deleted users on user page - gth
#
# Revision 1.3  2006/08/05 22:09:14  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:59  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>