<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Release Edit Page
#
# $RCSfile: release_edit_page.php,v $  $Revision: 1.4 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page           	 	= basename(__FILE__);
$form_name				= 'release_name';
$action_page    	 	= 'release_edit_action.php';
$build_edit_page		= 'release_edit_page.php';
$testset_page			= 'testset_page.php';
$delete_page 			= 'delete_page.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$row_style              = '';

$s_release_properties	= session_set_properties( "release", $_GET );
$release_id 			= $s_release_properties['release_id'];
$release_name			= admin_get_release_name($release_id);

html_window_title();
html_print_body( $form_name, 'release_edit_name_required');
html_page_title($project_name ." - ". lang_get('release_edit_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_release_map( Array("release_link", lang_get("edit") ) );

error_report_check( $_GET );

$release_details = admin_get_all_release_details_by_project( $project_id, $release_id );

extract( $release_details[0], EXTR_PREFIX_ALL, 'v' );

$release_name			= ${'v_' . RELEASE_NAME};
$release_date_received	= ${'v_' . RELEASE_DATE_RECEIVED};
$release_description	= ${'v_' . RELEASE_DESCRIPTION};

print"<div align=center>". NEWLINE;

print"<form method=post name=$form_name action=$action_page>". NEWLINE;
print"<input type=hidden name=release_id value='$release_id'>". NEWLINE;

print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td class='form-header-l' colspan='2'>". lang_get('edit_release') ." - $release_name</td>". NEWLINE;
print"</tr>". NEWLINE;

# RELEASE NAME
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('release_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type='text' maxlength='20' name='release_edit_name_required' size=30 value='".
					session_validate_form_get_field('release_edit_name_required', $release_name).
					"'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# RELEASE DATE
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('date_received') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type='text' maxlength='19' name='release_edit_date' size=30 value='".
		session_validate_form_get_field('release_edit_date', $release_date_received).
		"'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('description') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<textarea name='release_edit_description' rows=5 cols=30 >".
		session_validate_form_get_field('release_edit_description', $release_description).
		"</textarea>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td colspan='2' class=center><input type='submit' value='". lang_get('submit_btn') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</form>". NEWLINE;

print"</div>". NEWLINE;

html_print_footer();

# ------------------------------------
# $Log: release_edit_page.php,v $
# Revision 1.4  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/06/10 01:55:06  gth2
# no message
#
# Revision 1.2  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------

?>