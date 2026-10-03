<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Edit Req Doc Type Page
#
# $RCSfile: project_edit_bug_category_page.php,v $  $Revision: 1.3 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page           		= basename(__FILE__);
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$row_style              = '';

session_set_properties("project_manage", $_GET);
$selected_project_properties 	= session_get_properties("project_manage");
$selected_project_id 			= $selected_project_properties['project_id'];
$bug_category_id 				= $selected_project_properties['bug_category_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('edit_bug_category_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_project_manage_map( Array("project_manage_link", "bug_category_link", "Edit") );

print"<br>". NEWLINE;

error_report_check( $_GET );

print"<div align=center>". NEWLINE;

########################################################################################
# Edit Area Tested

$row = project_get_bug_category($selected_project_id, $bug_category_id);
$category = $row[CATEGORY_NAME];


print"<form method=post action='project_edit_bug_category_action.php'>". NEWLINE;
print"<input type=hidden name=project_id value=$selected_project_id>". NEWLINE;
print"<input type=hidden name=bug_category_id value=$bug_category_id>";
print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class='width70'>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=form-header-l>".lang_get('edit_bug_category')."</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='form-lbl-c'>". lang_get('bug_category') ."<span class='required'>*</span>". NEWLINE;
print"<input type=text size=60 maxlength=64 name='bug_category_required' value='". session_validate_form_get_field( 'bug_category_required', $category ) ."'>". NEWLINE;
print"&nbsp;<input type=submit value='".lang_get("save")."'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>". NEWLINE;

print"</div>". NEWLINE;

html_print_footer();

# ------------------------------------
# $Log: project_edit_bug_category_page.php,v $
# Revision 1.3  2006/08/05 22:08:24  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>