<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Release page
#
# $RCSfile: release_page.php,v $ $Revision: 1.8 $
# ------------------------------------
include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$form_name				= 'add_release';
$action_page			= 'release_add_action.php';
$release_edit_page		= 'release_edit_page.php';
$release_signoff_page	= 'release_signoff_page.php';
$build_page				= 'build_page.php';
$delete_page			= 'delete_page.php';
$s_project_properties	= session_get_project_properties();
$project_name			= $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];
$row_style				= '';

session_set_properties( "release", $_GET );

$display_options	= session_set_display_options( "release", $_POST );
$order_by			= $display_options['order_by'];
$order_dir			= $display_options['order_dir'];

html_window_title();
html_print_body( $form_name, 'rel_name_required');
html_page_title($project_name ." - ". lang_get('release_page') );
html_page_header( $db, $project_name );
html_print_menu();

error_report_check( $_GET );

print"<div align='center'>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<form method=post name=$form_name action=$action_page>". NEWLINE;
print"<table class=inner>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td colspan='2'><h4>". lang_get('add_release') ." - $project_name</h4></td>". NEWLINE;
print"</tr>". NEWLINE;

# RELEASE NAME
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('release_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class='form-data-l'><input type='text' maxlength='20' name='rel_name_required' value='".
	session_validate_form_get_field("rel_name_required"). "'></td>". NEWLINE;
print"</tr>". NEWLINE;

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('description') ."</td>". NEWLINE;
print"<td class='form-data-l'>";
print"<textarea name='rel_description' rows=5 cols=30 >".
	session_validate_form_get_field("rel_description"). "</textarea>";
print"</td>". NEWLINE;
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

$release_details = admin_get_all_release_details_by_project( $project_id, null, $order_by, $order_dir );

if( !empty( $release_details ) ) {
	print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
	print"<thead>".NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;

	#html_tbl_print_header( lang_get('release_name'), 	 RELEASE_NAME, 			$order_by, $order_dir, $page );
	#html_tbl_print_header( lang_get('rel_date_received'),RELEASE_DATE_RECEIVED, $order_by, $order_dir, $page );
	html_tbl_print_header_not_sortable( lang_get('release_name') );
	html_tbl_print_header( lang_get('rel_date_received') );
	html_tbl_print_header( lang_get('rel_description') );
	//html_tbl_print_header( lang_get('rel_ba_signoff') );
	//html_tbl_print_header( lang_get('rel_ba_signoff_by') );
	//html_tbl_print_header( lang_get('rel_ba_signoff_date') );
	//html_tbl_print_header( lang_get('rel_ba_signoff_note') );
	html_tbl_print_header( lang_get('rel_qa_signoff') );
	html_tbl_print_header( lang_get('rel_qa_signoff_by') );
	html_tbl_print_header( lang_get('rel_qa_signoff_date') );
	html_tbl_print_header( lang_get('rel_qa_signoff_note') );
	html_tbl_print_header_not_sortable( lang_get('sign_off') );
	html_tbl_print_header_not_sortable( lang_get('edit') );
	html_tbl_print_header_not_sortable( lang_get('delete') );

	print"</tr>". NEWLINE;
	print"</thead>".NEWLINE;
	print"<tbody>".NEWLINE;
	foreach( $release_details as $release_detail ) {


	//for( $i=0; $i < sizeof( $release_details ); $i++ ) {

	

		//extract( $release_details[$i], EXTR_PREFIX_ALL, 'v' );

		$release_id				= $release_detail[RELEASE_ID];
		$release_name			= $release_detail[RELEASE_NAME];
		$release_date_received	= date_trim_time( $release_detail[RELEASE_DATE_RECEIVED] );
		$release_description	= $release_detail[RELEASE_DESCRIPTION];
		$qa_signoff_by			= $release_detail[RELEASE_QA_SIGNOFF_BY];
		$qa_signoff_date		= date_trim_time( $release_detail[RELEASE_QA_SIGNOFF_DATE] );
		$qa_signoff_status		= $release_detail[RELEASE_QA_SIGNOFF];
		$qa_signoff_comment		= $release_detail[RELEASE_QA_SIGNOFF_COMMENTS];
		$ba_signoff_by			= $release_detail[RELEASE_BA_SIGNOFF_BY];
		$ba_signoff_date		= date_trim_time( $release_detail[RELEASE_BA_SIGNOFF_DATE] );
		$ba_signoff_status		= $release_detail[RELEASE_BA_SIGNOFF];
		$ba_signoff_comment		= $release_detail[RELEASE_BA_SIGNOFF_COMMENTS];


		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		print"<td class='tbl-l'><a href='$build_page?release_id=$release_id'>$release_name</a></td>". NEWLINE;
		print"<td class='tbl-c'>$release_date_received</td>". NEWLINE;
		print"<td class='tbl-l'>$release_description</td>". NEWLINE;
		//print"<td class='tbl-c'>$ba_signoff_status</td>". NEWLINE;
		//print"<td class='tbl-c'>$ba_signoff_by</td>". NEWLINE;
		//print"<td class='tbl-c'>$ba_signoff_date</td>". NEWLINE;
		//print"<td class='tbl-c'>$ba_signoff_comment</td>". NEWLINE;
		print"<td class='tbl-c'>$qa_signoff_status</td>". NEWLINE;
		print"<td class='tbl-c'>$qa_signoff_by</td>". NEWLINE;
		print"<td class='tbl-c'>$qa_signoff_date</td>". NEWLINE;
		print"<td class='tbl-c'>$qa_signoff_comment</td>". NEWLINE;
		print"<td class='tbl-c'><a href='$release_signoff_page?release_id=$release_id'>". lang_get('sign_off') ."</a></td>". NEWLINE;
		print"<td class='tbl-c'><a href='$release_edit_page?release_id=$release_id'>". lang_get('edit') ."</a></td>". NEWLINE;
		print"<td class='tbl-c'>";
			print"<form method=post action='$delete_page'>". NEWLINE;
			print"<input type='submit' name='delete' value='". lang_get( 'delete' ) ."' class='page-numbers'>";
			print"<input type='hidden' name='r_page' value=$page>". NEWLINE;
			print"<input type='hidden' name='f' value='delete_release'>". NEWLINE;
			print"<input type='hidden' name='id' value=$release_id>". NEWLINE;
			print"<input type='hidden' name='msg' value='20'>". NEWLINE;
			print"</form>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}
	print"</tbody>".NEWLINE;
	print"</table>". NEWLINE;
	#print"</form>". NEWLINE;
} else {
	html_no_records_found_message( lang_get('no_releases') );
}

print"</div>";

html_print_footer();

session_validate_form_reset();

# ------------------------------------
# $Log: release_page.php,v $
# Revision 1.8  2008/04/29 04:55:09  cryobean
# ui bugfixes from bruce
#
# Revision 1.7  2008/01/22 07:59:13  cryobean
# made the table sortable
#
# Revision 1.6  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.5  2006/06/10 01:55:06  gth2
# no message
#
# Revision 1.3  2006/04/05 12:38:41  gth2
# updates to release sign-off - th
#
# Revision 1.2  2006/02/24 11:33:32  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------

?>