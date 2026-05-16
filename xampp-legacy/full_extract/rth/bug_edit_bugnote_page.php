<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Edit Bugnote Page
#
# $RCSfile: bug_edit_bugnote_page.php,v $  $Revision: 1.3 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page           		= basename(__FILE__);
$form_name				= 'edit_bugnote';
$action_page    	    = 'bug_edit_bugnote_action.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$bugnote_id				= $_GET['bugnote_id'];


html_window_title();
html_print_body( $form_name, 'bugnote_required');
html_page_title($project_name ." - ". lang_get('edit_bugnote_page') );
html_page_header( $db, $project_name );
html_print_menu();
bug_menu_print( $page );

print"<br>";

error_report_check( $_GET );


$bugnote_detail = bug_get_bugnote( $bugnote_id );

$bugnote_id		= $bugnote_detail[BUG_NOTE_ID];
$bug_id			= $bugnote_detail[BUG_NOTE_BUG_ID];
$author			= $bugnote_detail[BUG_NOTE_AUTHOR];
$date_created	= $bugnote_detail[BUG_NOTE_DATE_CREATED];
$bugnote_detail	= $bugnote_detail[BUG_NOTE_DETAIL];

$padded_bug_id	= util_pad_id( $bug_id );


print"<div align=center>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

print"<table class=width85>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;

print"<td class=center>". NEWLINE;
print"<form method=post name=$form_name action=$action_page>". NEWLINE;

print"<input type=hidden name=bug_id value='$bug_id'>". NEWLINE;
print"<input type=hidden name=bugnote_id value='$bugnote_id'>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td class=form-header-l>".lang_get('edit_bugnote')."</td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

# BUG ID
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('bug_id') ."&nbsp;</td>". NEWLINE;
print"<td class='form-data-l'><a href='bug_detail_page.php?bug_id=$bug_id'>$padded_bug_id</a></td>". NEWLINE;
print"</tr>". NEWLINE;

# AUTHOR
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('author') ."&nbsp;</td>". NEWLINE;
print"<td class='form-data-l'>$author</td>". NEWLINE;
print"</tr>". NEWLINE;

# DATE CREATED
print"<tr>". NEWLINE;
print"<td class='form-lbl-r' nowrap>". lang_get('date_created') ."&nbsp;</td>". NEWLINE;
print"<td class='form-data-l'>$date_created</td>". NEWLINE;
print"</tr>". NEWLINE;

# BUGNOTE
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('bug_note') ."<span class='required'>*</span>&nbsp;</td>". NEWLINE;
print"<td class='form-data-l'>";
	print"<textarea name='bugnote_required' rows=7 cols=80 >". session_validate_form_get_field("bugnote_required", $bugnote_detail) ."</textarea>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td colspan='2' class=center><input type='submit' value='". lang_get('submit_btn') ."'></td>". NEWLINE;
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
# $Log: bug_edit_bugnote_page.php,v $
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