<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Copy Add Page
#
# $RCSfile: testset_copy_add_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$form_name				= 'add_testset';
$action_page			= 'testset_copy_action.php';
$testset_edit_page		= 'testset_edit_page.php';
$testset_add_tests_page	= 'testset_add_tests_page.php';
$delete_page 			= 'delete_page.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$row_style              = '';

$s_properties	= session_get_properties("testset_copy");
$release_id		= $s_properties['release_id'];
$release_name	= admin_get_release_name($release_id);
$build_id		= $s_properties['build_id'];
$build_name		= admin_get_build_name($build_id);

session_records("testset_copy");

html_window_title();
html_print_body( $form_name, 'testset_name_required');
html_page_title($project_name ." - ". lang_get('release_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_release_map(	Array(	"release_link",
							"build_link",
							lang_get("copy_testset_to") ) );

error_report_check( $_GET );

print"<div align=center>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;
print"<tr>". NEWLINE;
print"<td><h4>". lang_get('add_testset') ." - $build_name</h4></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=center>". NEWLINE;
print"<form method=post name=$form_name action=$action_page>". NEWLINE;
print"<table>". NEWLINE;

# TESTSET NAME
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('testset_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=left><input type='text' name='testset_name_required' size=30 value='".
					session_validate_form_get_field('testset_name_required').
					"'></td>". NEWLINE;
print"</tr>". NEWLINE;

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('description') ."</td>". NEWLINE;
print"<td class=left>";
print"<textarea  name='testset_description' rows=5 cols=30>".
					session_validate_form_get_field("testset_description").
					"</textarea></td>". NEWLINE;
print"</tr>". NEWLINE;

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td></td>". NEWLINE;
print"<td colspan='2' class=center><input type='submit' value='". lang_get('add') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: testset_copy_add_page.php,v $
# Revision 1.3  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>