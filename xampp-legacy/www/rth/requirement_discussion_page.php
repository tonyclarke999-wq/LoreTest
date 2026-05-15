<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Discussion Page
#
# $RCSfile: requirement_discussion_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'requirement_action.php';
$num                    = 0;
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$username				= $s_user_properties['username'];

$row_style				= '';

$display_options 	= session_set_display_options( "requirements", array_merge($_POST, $_GET) );
$s_tab 				= $display_options['tab'];

$s_properties 		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];
$s_discussion_id	= $s_properties['discussion_id'];

$project_manager	= user_has_rights($project_id, $user_id, MANAGER);

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get("req_discussion_page"));
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);

error_report_check($_GET);

$row_discussion			= discussion_get_detail($s_discussion_id);
$req_id					= util_pad_id($row_discussion[DISC_REQ_ID]);
$discussion_author		= $row_discussion[DISC_AUTHOR];
$discussion_date		= $row_discussion[DISC_DATE];
$discussion_subject		= $row_discussion[DISC_SUBJECT];
$discussion_status		= $row_discussion[DISC_STATUS];
$discussion_discussion	= util_html_encode_string($row_discussion[DISC_DISCUSSION]);

$rows_requirement 	= requirement_get_detail($project_id, $req_id);
$row_requirement 	= $rows_requirement[0];
$req_name			= $row_requirement[REQ_FILENAME];

print"<br>". NEWLINE;

print"<div align=center>";

# REQUIREMENT DETAILS
print"<table class=width100 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
html_tbl_print_header( lang_get('req_id') );
html_tbl_print_header( lang_get('req_name') );
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='33%' class=grid-data-c><a href='requirement_detail_page.php?req_id=$req_id'>$req_id</a></td>". NEWLINE;
print"<td width='33%' class=grid-data-c>$req_name</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br>". NEWLINE;

# DISCUSSION DETAILS
print"<table class=width100 rules=all>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=grid-header-l width='25%' nowrap>".lang_get('author')."</td>". NEWLINE;
print"<td class=grid-data-l width='25%'>$discussion_author</td>". NEWLINE;
print"<td class=grid-header-l nowrap>".lang_get('created')."</td>". NEWLINE;
print"<td class=grid-data-l>$discussion_date</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=grid-header-l width='25%' nowrap>".lang_get('subject')."</td>". NEWLINE;
print"<td class=grid-data-l width='25%'>$discussion_subject</td>". NEWLINE;
print"<td class=grid-header-l nowrap>".lang_get('status')."</td>". NEWLINE;
print"<td class=grid-data-l>$discussion_status</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=grid-header-l valign=top nowrap>".lang_get('discussion')."</td>". NEWLINE;
print"<td class=grid-data-l colspan=3>$discussion_discussion</td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;

# CLOSE DISCUSSION
if( $discussion_status!=="CLOSED" && $project_manager ) {
	print"<br>". NEWLINE;
	print"<form method=post action='requirement_discussion_close_page.php'>". NEWLINE;
	print"<input type=hidden name=discussion_id value=$s_discussion_id >". NEWLINE;
	print"<input type=hidden name=author value=$username >". NEWLINE;
	print"<input type=submit name=submit_close_discussion value='".lang_get('close_discussion')."'>". NEWLINE;
	print"</form>". NEWLINE;
}

# POSTS
print"<form method=post action='requirement_discussion_action.php'>". NEWLINE;
print"<input type=hidden name=discussion_id value=$s_discussion_id >". NEWLINE;
print"<input type=hidden name=author value=$username >". NEWLINE;

print"<h2>".lang_get("posts")."</h2>". NEWLINE;

$rows_posts 	= discussion_get_posts( $s_discussion_id );

if( $rows_posts ) {
	print"<table class=width90 rules=all>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td class=grid-header-c>".lang_get("author")."</td>". NEWLINE;
	print"<td class=grid-header-c>".lang_get("post")."</td>". NEWLINE;
	print"</tr>". NEWLINE;

	foreach($rows_posts as $row_post) {

		$post_author	= $row_post[POST_AUTHOR];
		$post_date		= $row_post[POST_DATE];
		$post_message	= util_html_encode_string($row_post[POST_MESSAGE]);

		$row_style = html_tbl_alternate_bgcolor($row_style);
		print"<tr class='$row_style'>". NEWLINE;
		print"<td class=tbl-l width='20%' valign=top>". NEWLINE;
		print"<p>$post_author<br><br>". NEWLINE;
		print lang_get('posted')." $post_date</p>". NEWLINE;
		print"</td>". NEWLINE;
		print"<td class=tbl-l valign=top>$post_message</td>". NEWLINE;
		print"</tr>". NEWLINE;

	}

	print"</table>". NEWLINE;
} else {
	print lang_get('no_posts');
}


# ADD POST
if( $discussion_status !== "CLOSED" ) {

	print"<br>". NEWLINE;
	print"<br>". NEWLINE;

	print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

	print"<table class=width60>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	print"<table class=inner>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td colspan=2><h4>". lang_get('add_new_post') ."</h4></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td class=center>". NEWLINE;

	# MESSAGE
	print"<tr>". NEWLINE;
	print"<td valign=top><span class='required'>*</span></td>". NEWLINE;
	print"<td align=left>". NEWLINE;
	html_FCKeditor("new_post_required", 600, 200);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# SUBMIT BUTTON
	print"<tr>". NEWLINE;
	print"<td colspan=2 align=center><input type='submit' name='submit_add_post' value='". lang_get('add') ."'></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
}

print"</form>". NEWLINE;

print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_discussion_page.php,v $
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