<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Edit Description Page
#
# $RCSfile: testset_edit_description_page.php,v $  $Revision: 1.4 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page           	 	= basename(__FILE__);
$form_name				= 'testset_name';
$action_page    	 	= 'testset_edit_description_action.php';
$testset_page			= 'testset_page.php';
$delete_page 			= 'delete_page.php';

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_release_properties	= session_set_properties( "release", $_GET );
$build_id				= $s_release_properties['build_id'];
$testset_id 			= $s_release_properties['testset_id'];
$testset_name			= admin_get_testset_name( $testset_id );

html_window_title();
html_print_body( $form_name, 'testset_edit_name_required');
html_page_title($project_name ." - ". lang_get('edit_testset_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_release_map( Array("release_link", "build_link", "testset_link", "Edit") );

error_report_check( $_GET );

$testset_details = admin_get_testset($project_id, $testset_id);

extract( $testset_details, EXTR_PREFIX_ALL, 'v' );

$testset_name			= ${'v_' . TS_NAME};
$testset_date_received	= ${'v_' . TS_DATE_CREATED};
$testset_description	= ${'v_' . TS_DESCRIPTION};

print"<div align=center>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=center>". NEWLINE;
print"<form method=post name=$form_name action=$action_page>". NEWLINE;
print"<input type=hidden name=testset_id value='$testset_id'>". NEWLINE;
print"<table>". NEWLINE;

# BUILD NAME
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('testset_name') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type='text' maxlength='20' name='testset_edit_name_required' size=30 value='".
					session_validate_form_get_field('testset_edit_name_required', $testset_name).
					"'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# BUILD DATE
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('date_received') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type='text' maxlength='19' name='testset_edit_date' size=30 value='".
					session_validate_form_get_field('testset_edit_date', $testset_date_received).
					"'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('description') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<textarea name='testset_edit_description' rows=5 cols=30 >" .
					session_validate_form_get_field('testset_edit_description', $testset_description).
					"</textarea>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td></td>". NEWLINE;
print"<td colspan='2' class=center><input type='submit' value='". lang_get('update') ."'></td>". NEWLINE;
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
# $Log: testset_edit_description_page.php,v $
# Revision 1.4  2008/08/07 11:46:26  peter_thal
# Changed button value to update
#
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