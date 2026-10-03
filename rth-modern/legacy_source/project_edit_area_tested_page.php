<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Edit User Page
#
# $RCSfile: project_edit_area_tested_page.php,v $  $Revision: 1.3 $
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
$area_id 						= $selected_project_properties['area_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('edit_area_tedsted_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_project_manage_map( Array("project_manage_link", "area_tested_link", "Edit") );

print"<br>". NEWLINE;

error_report_check( $_GET );

print"<div align=center>";

########################################################################################
# Edit Area Tested

$row = project_get_area_tested($selected_project_id, $area_id);

print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class=width50>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<form method=post action=project_edit_area_tested_action.php>". NEWLINE;
print"<input type=hidden name=project_id value=$selected_project_id>";
print"<input type=hidden name=area_id value=$area_id>";
print"<table class=inner>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=form-header-l>".lang_get('area_tested')."</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=form-data-c><span class='required'>*</span> <input type=text value='"
		.session_validate_form_get_field("area_tested_required", $row[AREA_TESTED_NAME])
		."' name=area_tested_required maxlength=50 size=50></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=center><br><input type=submit name='user_submit' value='".lang_get("save")."'><br>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</div>";

html_print_footer();

# ------------------------------------
# $Log: project_edit_area_tested_page.php,v $
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