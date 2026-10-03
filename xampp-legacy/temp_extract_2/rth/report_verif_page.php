<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Report Verifications Page
#
# $RCSfile: report_verif_page.php,v $  $Revision: 1.4 $
# ------------------------------------

include"./api/include_api.php";


$project_name = session_get_project_name();
$page                   = basename(__FILE__);

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$row_style = '';

$order_by		= BUILD_DATE_REC;
$order_dir		= "ASC";
$page_number	= 1;

if( isset($_POST['order_by']) ) {

	$order_by	= $_POST['order_by'];
	$order_dir	= util_change_order_dir($_POST['order_dir']);
}

if( isset($_GET['_release_id']) ) {
	$release_id 	= $_GET['_release_id'];;
}

if( isset($_GET['_build_id']) ) {
	$build_id 		= $_GET['_build_id'];
}

if( isset($_GET['_testset_id']) ) {
	$testset_id 	= $_GET['_testset_id'];
}

html_window_title();

auth_authenticate_user();

html_print_body();
html_page_title($project_name ." - " . lang_get('report_verif_page') );
html_page_header( $db, $project_name );
html_print_menu();
html_browse_release_menu($db, $page, $project_id);

error_report_check($_GET);

print"<div align=center>";
print"<br>". NEWLINE;

if( isset( $testset_id ) && $testset_id != 'all' ) {

	$verif_summary	= report_get_verifs_summary( $testset_id );
	$passed			= $verif_summary["Passed"];
	$failed			= $verif_summary["Failed"];
	$info			= $verif_summary["Info"];
	$bugs			= $verif_summary["Bugs"];

	print"<form method=post action='$page?_release_id=$release_id&amp;_build_id=$build_id&amp;_testset_id=$testset_id'>";
	print"<table class=width80>". NEWLINE;
	print"<tr>". NEWLINE;
	html_tbl_print_header( lang_get('verifs_passed') );
	html_tbl_print_header( lang_get('verifs_failed') );
	html_tbl_print_header( lang_get('num_bugs') );
	print"</tr>". NEWLINE;

	print"<tr>". NEWLINE;
	print"<td>$passed</td>". NEWLINE;
	if( $failed!==0 ) {
		print"<td><a href='report_verif_failed_page.php?_release_id=$release_id&amp;_build_id=$build_id&amp;_testset_id=$testset_id'>$failed</a></td>". NEWLINE;
	} else {
		print"<td>0</td>". NEWLINE;
	}
	if( $bugs!==0 ) {
		print"<td><a href='report_verif_failed_page.php?_release_id=$release_id&amp;_build_id=$build_id&amp;_testset_id=$testset_id&bugs_only=true'>$bugs</a></td>". NEWLINE;
	} else {
		print"<td>0</td>". NEWLINE;
	}
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>";
	print"<br><br>". NEWLINE;
}

print"</div>";

html_print_footer();


# ---------------------------------------------------------------------
# $Log: report_verif_page.php,v $
# Revision 1.4  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2005/12/08 19:39:51  gth2
# updating reports containing calls to jp-graph - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>