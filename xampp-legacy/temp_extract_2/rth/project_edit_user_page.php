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
# $RCSfile: project_edit_user_page.php,v $  $Revision: 1.3 $
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

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('user_edit_page') );
html_page_header( $db, $project_name );
html_print_menu();

html_project_manage_map( Array("project_manage_link") );

print"<br>". NEWLINE;

error_report_check( $_GET );

print"<div align=center>";

########################################################################################
# Add User
print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class=width50>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<form method=post action=project_edit_user_action.php>". NEWLINE;
print"<table class=inner>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-header-l>".lang_get('edit_user')."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td>". NEWLINE;

$user_details = project_get_user_details($selected_project_id, $_GET['user_id']);

$user_id				= $user_details[USER_ID];
$user_first				= $user_details[USER_FNAME];
$user_last				= $user_details[USER_LNAME];
$username				= $user_details[USER_UNAME];
$user_email				= $user_details[USER_EMAIL];
$user_phone				= $user_details[USER_PHONE];
$user_default_project	= $user_details[USER_DEFAULT_PROJECT];
$user_rights			= $user_details[PROJ_USER_PROJECT_RIGHTS];
$user_delete_rights		= $user_details[PROJ_USER_DELETE_RIGHTS];
$user_email_testset		= $user_details[PROJ_USER_EMAIL_TESTSET];
$user_email_discussions	= $user_details[PROJ_USER_EMAIL_REQ_DISCUSSION];
$user_qa_owner			= $user_details[PROJ_USER_QA_OWNER];
$user_ba_owner			= $user_details[PROJ_USER_BA_OWNER];

print"<table class=hide90>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-header-l>".lang_get('full_name')."</td>". NEWLINE;
print"<td align=left>$user_first $user_last</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-header-l>".lang_get('username')."</td>". NEWLINE;
print"<td align=left>$username</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-header-l>".lang_get('email')."</td>". NEWLINE;
print"<td align=left>$user_email</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td colspan=2>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-header-l width='50%'>".lang_get('user_rights')."</td>". NEWLINE;
print"<td class=form-header-l width='50%'>".lang_get('prefs')."</td>". NEWLINE;
print"</tr>". NEWLINE;

# User Rights
print"<tr>". NEWLINE;
print"<td class=tbl-l valign=top>". NEWLINE;
print"<select name='user_rights' size=1>". NEWLINE;

html_print_user_rights_list_box($user_rights);

print"</select>". NEWLINE;
print"</td>". NEWLINE;

# Preferences

$user_delete_rights		= (($user_delete_rights=="Y") ? "checked":"");
$user_email_testset		= (($user_email_testset=="Y") ? "checked":"");
$user_email_discussions	= (($user_email_discussions=="Y") ? "checked":"");
$user_qa_owner			= (($user_qa_owner=="Y") ? "checked":"");
$user_ba_owner			= (($user_ba_owner=="Y") ? "checked":"");

print"<td class=tbl-l>". NEWLINE;

# Delete Rights
print"<input id=user_delete_rights type=checkbox name='user_delete_rights' $user_delete_rights>". NEWLINE;
print"<label for=user_delete_rights>". lang_get('delete_rights') ."</label><br>". NEWLINE;

# Email Testset
print"<input id=user_email_testset type=checkbox name='user_email_testset' $user_email_testset>". NEWLINE;
print"<label for=user_email_testset>". lang_get('email_testset') ."</label><br>". NEWLINE;

# Email Discussions
print"<input id=user_email_discussions type=checkbox name='user_email_discussions' $user_email_discussions>". NEWLINE;
print"<label for=user_email_discussions>". lang_get('email_discussions') ."</label><br>". NEWLINE;

# QA Tester
print"<input id=user_qa_owner type=checkbox name='user_qa_owner' $user_qa_owner>". NEWLINE;
print"<label for=user_qa_owner>". lang_get('qa_tester') ."</label><br>". NEWLINE;

# BA Owner
print"<input id=user_ba_owner type=checkbox name='user_ba_owner' $user_ba_owner>". NEWLINE;
print"<label for=user_ba_owner>". lang_get('ba_owner') ."</label><br>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>";
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=center colspan=3><br><input type=submit name='user_submit' value='".lang_get("save")."'><br>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;
print"</form>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</div>";

html_print_footer();

# ------------------------------------
# $Log: project_edit_user_page.php,v $
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