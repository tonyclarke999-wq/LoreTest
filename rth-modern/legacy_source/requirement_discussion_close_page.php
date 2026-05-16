<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Discussion Close Page
#
# $RCSfile: requirement_discussion_close_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_properties 		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];
$s_discussion_id	= $s_properties['discussion_id'];

if( isset($_POST['confirm']) ) {

	if( $_POST['confirm']==lang_get('yes') ) {


		$redirect_page = "requirement_discussion_page.php";
		$discussion_id	= $_POST['discussion_id'];

		discussion_set_status(	$discussion_id,
								"CLOSED");

		############################################################################
		# EMAIL NOTIFICATION
		############################################################################
		$notify_recipients		= requirement_get_notify_users($project_id, $s_req_id);
		$discussion_recipients	= requirement_get_discussion_users($project_id);

		# merge arrays and remove duplicates
		$recipients				= array_merge($discussion_recipients, $notify_recipients);

		requirement_email($project_id, $s_req_id, $recipients, $action="close_discussion", $discussion_id);
		############################################################################
		############################################################################

		html_print_operation_successful( "discussion_close_page", $redirect_page );

	} else {

		html_redirect( "requirement_discussion_page.php" );
		exit;
	}
}

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get("discussion_close_page"));
html_page_header( $db, $project_name );
html_print_menu();
requirement_menu_print($page);


print"<br>". NEWLINE;

print"<div align=center>";

print"<form method=post action='$page'>". NEWLINE;
print"<input type=hidden name=discussion_id value=$s_discussion_id >". NEWLINE;

print"<table class=width40>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<form name='delete' action=delete_page.php method='post'>". NEWLINE;
print"<input type='hidden' name='confirm_f' value='delete_release'>". NEWLINE;
print"<input type='hidden' name='confirm_id' value='4'>". NEWLINE;
print"<input type='hidden' name='confirm_r_page' value='release_page.php'>". NEWLINE;
print"<input type='hidden' name='confirm_msg' value='20'>". NEWLINE;
print"<table class=inner rules='none' border='0'>". NEWLINE;
print"<tr>". NEWLINE;
print"<td colspan=2>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td colspan=2 class=form-lbl-c>". lang_get("ask_close_discussion")."</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td colspan=2>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='center'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='confirm' value='". lang_get('yes') ."'></td>". NEWLINE;

print"<td class='left'><input type='submit' name='confirm' value='". lang_get('no') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</form>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</form>". NEWLINE;

print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_discussion_close_page.php,v $
# Revision 1.3  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>