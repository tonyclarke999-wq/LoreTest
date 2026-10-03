<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# User Add Page
#
# $RCSfile: user_add_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page           		= basename(__FILE__);
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$row_style              = '';

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('add_users_page') );
html_page_header( $db, $project_name );
html_print_menu();
admin_user_print( $page, $project_id, $user_id );

if( !user_has_rights( $project_id, $user_id, ADMIN ) ) {
	print"<div align=center>";
	error_report_display_msg( NO_RIGHTS_TO_VIEW_PAGE );
	print"</div>";
	exit;
}

print"<br>". NEWLINE;

error_report_check( $_GET );

print"<div align=center>";

########################################################################################
# Add User
print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class=width80 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<form method=post action=user_add_action.php>". NEWLINE;

print"<table class=inner>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-header-l colspan=3>".lang_get('add_new_user')."</td>". NEWLINE;
print"</tr>". NEWLINE;
######################################################################################################
# username label, username field, projects label
print"<tr>". NEWLINE;
print"<td class=form-lbl-r width='33%'>".lang_get('username')."<span class='required'>*</span></td>". NEWLINE;
print"<td class=form-lbl-l width='33%'><input tabindex=10 type=text name='username_required' maxlength=25 value='".session_validate_form_get_field("username_required")."'></td>". NEWLINE;
print"<td class=form-lbl-l width='33%'>".lang_get('add_to_projects')."<span class='required'>*</span></td>". NEWLINE;
print"</tr>". NEWLINE;

# first name label, first name field, projects select box
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('first_name')."<span class='required'>*</span></td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=20 type=text name='first_name_required' maxlength=50 value='".session_validate_form_get_field("first_name_required")."' ></td>". NEWLINE;
# Projects

$project_names_array 	= array();
$projects 				= project_get_all_projects_details(PROJ_NAME, "ASC");

foreach($projects as $project_row) {
	$project_names_array[$project_row[PROJ_ID]] = $project_row[PROJ_NAME];
}
# rowspan is the number of rows the select projects box spans
print"<td class=form-lbl-l width='33%' rowspan=6 valign=top>". NEWLINE;
print"<select tabindex=100 name='user_add_to_projects_required[]' size=10 multiple>". NEWLINE;
html_print_list_box_from_key_array($project_names_array, session_validate_form_get_field("user_add_to_projects_required"));
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
######################################################################################################

print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('last_name')."<span class='required'>*</span></td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=25 type=text name='last_name_required' maxlength=50 value='".session_validate_form_get_field("last_name_required")."' ></td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('email')."<span class='required'>*</span></td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=30 type=text name='email_required' maxlength=50 value='".session_validate_form_get_field("email_required")."' ></td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('phone')."</td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=35 type=text name='phone' maxlength=20 value='".session_validate_form_get_field("phone")."' ></td>". NEWLINE;
print"</tr>". NEWLINE;

# User Rights
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('tempest_admin')."</td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=60 type=checkbox name='user_tempest_admin' ";
	if (session_validate_form_get_field('user_tempest_admin')) print ' checked';
	print"></td>". NEWLINE;
print"</tr>". NEWLINE;

# Delete Rights
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('delete_rights')."</td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=45 type=checkbox name='user_delete_rights' ";
	if (session_validate_form_get_field('user_delete_rights')) print ' checked';
	print"></td>". NEWLINE;
print"</tr>". NEWLINE;

# Email TestSet, Default Project Label
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('email_testset')."</td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=50 type=checkbox name='user_email_testset' ";
	if (session_validate_form_get_field('user_email_testset')) print ' checked';
	print"></td>". NEWLINE;
print"<td class=form-lbl-l>".lang_get('default_project')."</td>". NEWLINE;
print"</tr>". NEWLINE;

# Email Discussion, Default Project
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('email_discussions')."</td>". NEWLINE;
print"<td class=form-lbl-l>";
print"<input tabindex=55 type=checkbox name='user_email_discussions' ";
	if (session_validate_form_get_field('user_email_discussions')) print ' checked';
	print"></td>". NEWLINE;
# Default Project
print"<td class=form-lbl-l>". NEWLINE;
print"<select tabindex=101 name='user_default_project' size=1>". NEWLINE;
html_print_list_box_from_key_array($project_names_array, session_validate_form_get_field("user_default_project"));
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# QA Owner, Project Rights Label
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('qa_tester')."</td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=60 type=checkbox name='user_qa_owner' ";
	if (session_validate_form_get_field('user_qa_owner')) print ' checked';
	print"></td>". NEWLINE;
# Project User Rights
print"<td class=form-lbl-l>".lang_get("project_user_rights")."</td>". NEWLINE;
print"</tr>". NEWLINE;

# BA Owner, Project Rights
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('ba_owner')."</td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=70 type=checkbox name='user_ba_owner' ";
	if (session_validate_form_get_field('user_ba_owner')) print ' checked';
	print"></td>". NEWLINE;
# Project Rights
print"<td class=form-lbl-l>". NEWLINE;
print"<select tabindex=105 name='user_project_rights' size=1>". NEWLINE;

html_print_user_rights_list_box( session_validate_form_get_field("user_project_rights") );

print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# Submit button
print"<tr>". NEWLINE;
print"<td class=center colspan=3><br><input tabindex=200 type=submit name='user_submit' value='".lang_get("add")."'><br>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;

print"</form>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</div>";

session_validate_form_reset();

html_print_footer();

# ---------------------------------------------------------------------
# $Log: user_add_page.php,v $
# Revision 1.3  2006/08/05 22:09:13  gth2
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