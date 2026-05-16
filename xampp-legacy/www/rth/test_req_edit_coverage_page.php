<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Requirement Edit Coverage Page
#
# $RCSfile: test_req_edit_coverage_page.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";

$page                   = basename(__FILE__);
$action_page            = 'test_req_edit_coverage_action.php';
$s_project_properties   = session_get_project_properties();
$s_project_name         = $s_project_properties['project_name'];
$s_project_id			= $s_project_properties['project_id'];

$s_test_details		= session_set_properties("test", $_GET);
$s_test_id			= $s_test_details['test_id'];
# $test_version_id	= $s_test_details['test_version_id'];
$test_name			= test_get_name( $s_test_id );

$req_id				= $_GET['req_id'];
$req_name			= requirement_get_name( $req_id );

if (isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
} else {
    $is_validation_failure = false;
}

html_window_title();
html_page_title( $s_project_name ." - ". lang_get('test_req_coverage_page') ); 
html_page_header( $db, $s_project_name );
html_print_body();
html_print_menu();
test_menu_print ($page);

error_report_check( $_GET );

print"<br>";

print"<div align=center>";
//print "<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span><br>";
print "<div align='center'>";
print"<table class=width70>";
print"<tr>";
print"<td>";
    print"<table class=inner rules=none border=0>";
    print"<form method=post action=$action_page>";

    print"<tr>";
    print"<td class=form-header-l colspan=2>". lang_get('update_test_coverage') ." - ". $test_name ."</td>";
    print"</tr>";

	# REQUIREMENT NAME
    print"<tr>";
    print"<td class=form-lbl-r nowrap>". lang_get('req_name') .":</td>";
    print"<td class=form-data-l>$req_name</td>";
    print"</tr>";

	$assoc_info = test_get_percent_req_coverage( $s_test_id, $req_id );
	$assoc_id = $assoc_info[TEST_REQ_ASSOC_ID];
	$percent_covered = $assoc_info[TEST_REQ_ASSOC_PERCENT_COVERED];
	# PERCENT COVERED
	print"<tr>";
    print"<td class=form-lbl-r>". lang_get('percent_covered_string') ."</td>";
    print"<td class=form-data-l><input type=text size=5 maxlength=3 name=percent_covered value='$percent_covered'>%</td>";
    print"</tr>";
	
   	print"<input type='hidden' name='test_id' value='$s_test_id'>";
	print"<input type='hidden' name='assoc_id' value='$assoc_id'>";
	#print"<input type='hidden' name='test_version_id' value='$test_version_id'>";

	util_add_spacer();

    print"<tr><td class=center colspan=2><input type=submit name='save' value='". lang_get( 'update') ."'><br><br></td>";

    print"</form>";
    print"</table>";
print"</td>";
print"</tr>";
print"</table>";
print"<br>";
print"</div>";
print"</div>";


html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_req_edit_coverage_page.php,v $
# Revision 1.2  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>