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
# $RCSfile: build_edit_page.php,v $  $Revision: 1.5 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page           		= basename(__FILE__);
$form_name				= 'edit_build';
$action_page    	    = 'build_edit_action.php';
$build_edit_page		= 'build_edit_page.php';
$testset_page			= 'testset_page.php';
$delete_page 			= 'delete_page.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$row_style              = '';

$s_release_properties	= session_set_properties( "release", $_GET );
$release_id				= $s_release_properties['release_id'];
$release_name			= admin_get_release_name($release_id);
$build_id				= $s_release_properties['build_id'];
$build_name				= admin_get_build_name($build_id);

html_window_title();
html_print_body( $form_name, 'build_edit_name_required');
html_page_title($project_name ." - ". lang_get('build_edit_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_release_map( Array("release_link", "build_link", "Edit") );

print"<br>". NEWLINE;

error_report_check( $_GET );

####################################################################################################
# Create the form allowing the user to edit the record

$build_details = admin_get_build($project_id, $build_id);

extract($build_details, EXTR_PREFIX_ALL, 'v');
$build_name				= ${'v_' . BUILD_NAME};
$build_date_received	= ${'v_' . BUILD_DATE_REC};
$build_description		= ${'v_' . BUILD_DESCRIPTION};

print"<div align=center>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;

print"<td class=center>". NEWLINE;
print"<form method=post name=$form_name action=$action_page>". NEWLINE;
print"<input type=hidden name=build_id value='$build_id'>". NEWLINE;


# FORM TITLE
print"<tr>". NEWLINE;
print"<td class='form-header-l' colspan='2'>". lang_get('edit_build') ." - $build_name</td>". NEWLINE;
print"</tr>". NEWLINE;

# BUILD NAME
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('build_name') ."<span class='required'>*</span></td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type='text' maxlength='20' name='build_edit_name_required' size=30 value='".
					session_validate_form_get_field('build_edit_name_required', $build_name).
					"'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# BUILD DATE
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('date_received') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type='text' maxlength='19' name='build_edit_date' size=30 value='".
					session_validate_form_get_field("build_edit_date", $build_date_received).
					"'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('description') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<textarea name='build_edit_description' rows=5 cols=30 >".
					session_validate_form_get_field("build_edit_description", $build_description).
					"</textarea>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td colspan='2' class=center><input type='submit' value='". lang_get('update') ."'></td>". NEWLINE;
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
# $Log: build_edit_page.php,v $
# Revision 1.5  2008/08/08 11:22:06  peter_thal
# disabled update buildname to an existing buildname
# test_detail_update_action.php: changed redirect page on error
#
# Revision 1.4  2006/08/05 22:07:59  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/05/03 19:59:53  gth2
# no message
#
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>