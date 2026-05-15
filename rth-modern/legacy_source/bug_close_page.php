<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Close Page
#
# $RCSfile: bug_close_page.php,v $  $Revision: 1.3 $
# ------------------------------------

$page							= basename(__FILE__);
$action_page					= 'bug_close_action.php';

include"./api/include_api.php";
auth_authenticate_user();

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

$s_user_properties		= session_get_user_properties();
$s_user_id				= $s_user_properties['user_id'];
$s_username				= $s_user_properties['username'];

$s_bug_details			= session_set_properties("bug", $_GET);
$bug_id					= $s_bug_details['bug_id'];
$padded_bug_id			= util_pad_id( $bug_id );


html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('bug_close_page') );
html_page_header( $db, $project_name );
html_print_menu();
bug_menu_print( $page );
print"<br>". NEWLINE;

error_report_check( $_GET );

$row = bug_get_detail( $bug_id );

$closed_reason_code		= $row[BUG_CLOSED_REASON_CODE];

print"<div align=center>";
print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span><br>";

# ADD BUGNOTE TABLE
print"<table class=width75>";
print"<tr>";
print"<td>";

	print"<form method=post action=$action_page>";
	print"<input type=hidden name='bug_id' value='$bug_id'>". NEWLINE;

	print"<table class=inner rules=none border=0>";

	# FORM TITLE
	print"<tr>";
	print"<td class=form-header-l colspan=2>".lang_get('close_bug')."</td>";
	print"</tr>";

	# IMPLEMENTED IN RELEASE
	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r nowrap>". lang_get('closed_reason_code') ."<span class='required'>*</span></td>". NEWLINE;
	print"<td class=form-data-l>". NEWLINE;
		$selected_value = session_validate_form_get_field('closed_reason_code', $closed_reason_code);
		print"<select name='closed_reason_code_required' size=1>". NEWLINE;
		$closed_reasons = bug_get_closed_reason_code();
		html_print_list_box_from_array( $closed_reasons, $selected_value);
		print"</select>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# BUGNOTE
	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r nowrap>". lang_get('bug_note') ."</td>". NEWLINE;
	print"<td class=form-data-l>". NEWLINE;
	print"<textarea rows='6' cols='50' name='bugnote'></textarea>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;

	util_add_spacer();

	# BUTTON
	print"<tr>". NEWLINE;
	print"<td colspan='2'><input type=submit name='update' value='". lang_get("update") ."'></td>". NEWLINE;
	print"</tr>". NEWLINE;

	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

print"</td>";
print"</tr>";
print"</table>";
print"</div>";

print"<br>";

html_print_footer();

# ------------------------------------
# $Log: bug_close_page.php,v $
# Revision 1.3  2006/08/05 22:07:58  gth2
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