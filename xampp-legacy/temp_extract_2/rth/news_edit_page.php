<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# News Edit Page
#
# $RCSfile: news_edit_page.php,v $ $Revision: 1.4 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'news_edit_action.php';

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$form_name 	= "edit_news";
$news_id	= $_POST['news_id'];

html_window_title();
html_print_body( $form_name, 'release_edit_name_required');
html_page_title($project_name ." - ". lang_get('news_edit_page') );
html_page_header( $db, $project_name );
html_print_menu();

$row_news 		= news_get($project_id, $news_id);
$row_subject 	= $row_news[0][NEWS_SUBJECT];
$row_body 		= $row_news[0][NEWS_BODY];

print"<div align=center>". NEWLINE;
print"<form method=post name=$form_name action=$action_page>". NEWLINE;
print"<input type=hidden name=project_id value=$project_id>";
print"<input type=hidden name=news_id value=$news_id>";

print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class=width80>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;

# SUBJECT
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('subject') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type='text' maxlength='20' name='subject_required' size=30 value='".
					session_validate_form_get_field('subject_required', $row_subject).
					"'>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# DESCRIPTION
print"<tr>". NEWLINE;
print"<td class=right>". lang_get('description') ." <span class='required'>*</span></td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<textarea name='body_required' rows=10 cols=80 >".
		session_validate_form_get_field('body_required', $row_body).
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
# $Log: news_edit_page.php,v $
# Revision 1.4  2007/02/03 10:25:07  gth2
# no message
#
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