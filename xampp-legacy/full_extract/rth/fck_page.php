<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# fck Page
#
# $RCSfile: fck_page.php,v $    $Revision: 1.3 $
# ------------------------------------

include_once"./api/include_api.php";
auth_authenticate_user();

session_validate_form_reset();

$project_name 		= session_get_project_name();
$page				= basename(__FILE__);
$row_style			= '';

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$test_detail_page		= 'test_detail_page.php';
$req_detail_page		= 'requirement_detail_page.php';
$results_page			= 'results_page.php';

html_window_title();
html_print_body();
html_page_title($project_name ." - HOME");
html_page_header( $db, $project_name );
html_print_menu();

error_report_check( $_GET );

print"<div align=center>". NEWLINE;
print"<form action='sampleposteddata.php' method=post>";

html_FCKeditor("", "510", "150");

print"</form>";
print"</div>";

html_print_footer();

# ------------------------------------
# $Log: fck_page.php,v $
# Revision 1.3  2006/08/05 22:08:13  gth2
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