<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Group Action Page
#
# $RCSfile: results_group_action_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page			= 'results_group_action.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];
$s_properties			= session_get_properties("results");
$testset_id				= $s_properties['testset_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('results_mass_update_page'));
html_page_header( $db, $project_name );
html_print_menu();
html_test_results_menu( $db, $page, $project_id, $s_properties );

$ids = "". NEWLINE;

# if submit from test_page.php
if( isset($_POST['mass_update']) && isset($_POST['row_results_arr']) ) {

	$field = $_POST['action'];

	foreach( $_POST['row_results_arr'] as $key => $value) {
		$ids .= $key .":";
	}
}
else {
	error_report_show("results_page.php", NO_TESTS_SELECTED);
}



print"<br>". NEWLINE;

print"<form method=post action=$action_page>". NEWLINE;

print"<div align=center>". NEWLINE;
print"<table class=width50>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<table class=inner rules=none border=0>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=form-header-l colspan=2>". lang_get('update_test_result') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('test_comments') ."</td>". NEWLINE;
print"<td class=left>". NEWLINE;
	print"<textarea rows='5' cols='40' name='test_result_comments'></textarea>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;


print"<td><input type='hidden' name='action' value='$field'></td>". NEWLINE;
print"<td><input type='hidden' name='ids' value='$ids'></td>". NEWLINE;

util_add_spacer();

print"<tr><td class=center colspan=2><input type=submit name='save' value='". lang_get( 'update' ) ."'></td></tr>". NEWLINE;

print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</div>". NEWLINE;

print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: results_group_action_page.php,v $
# Revision 1.3  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>