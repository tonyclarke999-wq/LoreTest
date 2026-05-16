<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# $RCSfile: release_signoff_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$s_project_properties	= session_get_project_properties();
$project_id				= $s_project_properties['project_id'];


html_window_title();
html_print_body();
html_page_title($project_name ." -  ". lang_get( 'release_signoff_page' ));
html_page_header( $db, $project_name );
html_print_menu();

error_report_check( $_GET );

print"<br><br>". NEWLINE;

$row = admin_get_release_detail( $_GET['release_id'] );

$release_id				= $row[RELEASE_ID];
$release_name			= $row[RELEASE_NAME];
$qa_status				= $row[RELEASE_QA_SIGNOFF];
$ba_status				= $row[RELEASE_BA_SIGNOFF];
$qa_signoff_date		= $row[RELEASE_QA_SIGNOFF_DATE];
$ba_signoff_date		= $row[RELEASE_BA_SIGNOFF_DATE];
$qa_signoff_by			= $row[RELEASE_QA_SIGNOFF_BY];
$ba_signoff_by			= $row[RELEASE_BA_SIGNOFF_BY];
$qa_comments			= $row[RELEASE_QA_SIGNOFF_COMMENTS];
$ba_comments			= $row[RELEASE_BA_SIGNOFF_COMMENTS];

print"<div align=center>". NEWLINE;

print"<form method=post action='release_signoff_action.php'>". NEWLINE;
print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

    print"<table class=inner rules=none border=0>". NEWLINE;

    print"<tr>". NEWLINE;
    print"<td class=form-header-l colspan=2>".lang_get('signoff').": $release_name</td>". NEWLINE;
    print"</tr>". NEWLINE;

	util_add_spacer();

	# RELEASE STATUS
	print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('status') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
		$statuses = admin_get_release_status();
        print"<select name='qa_status' size=1>". NEWLINE;
		html_print_list_box_from_array( $statuses, $qa_status );
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	# RELEASE COMMENTS
    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('test_comments') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<textarea rows='4' cols='60' name='qa_comments'>$qa_comments</textarea>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	print"<input type='hidden' name='release_id' value='$release_id'>". NEWLINE;

	util_add_spacer();

    print"<tr><td class=center colspan=2><input type=submit name='submit' value='". lang_get('submit_btn') ."'></td></tr>". NEWLINE;

    print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</form>". NEWLINE;

print"</div>". NEWLINE;


html_print_footer();

# ---------------------------------------------------------------------
# $Log: release_signoff_page.php,v $
# Revision 1.3  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/06/10 01:55:06  gth2
# no message
#
# Revision 1.1  2006/04/05 12:38:41  gth2
# updates to release sign-off - th
#
# ---------------------------------------------------------------------
?>