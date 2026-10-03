<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Build page
#
# $RCSfile: build_page.php,v $ $Revision: 1.5 $
# ------------------------------------
include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$form_name				= 'add_build';
$action_page       		= 'build_add_action.php';
$build_edit_page		= 'build_edit_page.php';
$testset_page			= 'testset_page.php';
$delete_page 			= 'delete_page.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$row_style              = '';

# Set release build properties in session and assign all values to variable
$s_release_properties	= session_set_properties( "release", $_GET );
$release_id				= $s_release_properties['release_id'];
$release_name			= admin_get_release_name($release_id);

$redirect_url			= $page ."?release_id=". $release_id;

$display_options	= session_set_display_options( "build", $_POST );
$order_by			= $display_options['order_by'];
$order_dir			= $display_options['order_dir'];

html_window_title();
html_print_body( $form_name, 'build_name_required');
html_page_title($project_name ." - ". lang_get('build_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_release_map( Array("release_link", lang_get("builds")) );

error_report_check( $_GET );

print"<div align=center>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<form method=post name=$form_name action=$action_page>". NEWLINE;
print"<table class='inner'>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td colspan='2'><h4>". lang_get('add_build') ." - $release_name</h4></td>". NEWLINE;
print"</tr>". NEWLINE;


# BUILD NAME
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('build_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class='form-data-l'><input type='text' maxlength='20' name='build_name_required' size=30 value='". session_validate_form_get_field("build_name_required")."'></td>". NEWLINE;
print"</tr>". NEWLINE;

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('description') ."</td>". NEWLINE;
print"<td class='form-data-l'><textarea name='build_description' rows=5 cols=30>".
	session_validate_form_get_field("build_description"). "</textarea></td>". NEWLINE;
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

$build_details = admin_get_builds( $release_id, $order_by, $order_dir );

if( !empty( $build_details ) ) {
	print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
	print"<thead>".NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;

	html_tbl_print_header_not_sortable( lang_get('build_name') );
	html_tbl_print_header( lang_get('build_date_received') );
	html_tbl_print_header_not_sortable( lang_get('build_description') );
	html_tbl_print_header_not_sortable( lang_get('edit') );
	html_tbl_print_header_not_sortable( lang_get('delete') );

	print"	</tr>". NEWLINE;
	print"</thead>".NEWLINE;
	print"<tbody>".NEWLINE;

	for( $i=0; $i < sizeof( $build_details ); $i++ ) {

		extract( $build_details[$i], EXTR_PREFIX_ALL, 'v' );

		$build_id				= ${'v_' . BUILD_ID};
		$build_name				= ${'v_' . BUILD_NAME};
		$build_date_received	= ${'v_' . BUILD_DATE_REC};
		$build_description		= ${'v_' . BUILD_DESCRIPTION};

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		print"<td class='tbl-l'><a href='$testset_page?build_id=$build_id'>$build_name</a></td>". NEWLINE;
		print"<td class='tbl-c'>$build_date_received</td>". NEWLINE;
		print"<td class='tbl-l'>$build_description</td>". NEWLINE;
		print"<td class='tbl-c'><a href='$build_edit_page?build_id=$build_id'>". lang_get('edit') ."</a></td>". NEWLINE;
		print"<td class='tbl-c'>";
			print"<form method=post action='$delete_page'>". NEWLINE;
			print"<input type='submit' name='delete' value='". lang_get( 'delete' ) ."' class='page-numbers'>";
			print"<input type='hidden' name='r_page' value=$redirect_url>". NEWLINE;
			print"<input type='hidden' name='f' value='delete_build'>". NEWLINE;
			print"<input type='hidden' name='id' value=$build_id>". NEWLINE;
			print"<input type='hidden' name='msg' value='30'>". NEWLINE;
			print"</form>";
		print"</td>". NEWLINE;
		/*
		print"<td class='tbl-c'><a href='$delete_page?r_page=$page&f=delete_build&id=$build_id&msg=30'>". lang_get('delete') ."</a></td>". NEWLINE;
		*/
		print"</tr>". NEWLINE;
	}
	print"</tbody>".NEWLINE;
	print"</table>". NEWLINE;

} else {
	html_no_records_found_message( lang_get('no_builds') );
}

print"</div>". NEWLINE;

html_print_footer();

session_validate_form_reset();

# ------------------------------------
# $Log: build_page.php,v $
# Revision 1.5  2008/04/23 06:31:29  cryobean
# *** empty log message ***
#
# Revision 1.4  2008/01/22 07:55:49  cryobean
# made the table sortable
#
# Revision 1.3  2006/08/05 22:07:59  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>