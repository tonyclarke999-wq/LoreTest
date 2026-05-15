<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Report Page
#
# $RCSfile: report_page.php,v $  $Revision: 1.7 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_name = session_get_project_name();
$page                   = basename(__FILE__);

html_window_title();
html_print_body();
html_page_title($project_name ." - " . lang_get('reporting_page') );
html_page_header( $db, $project_name );
html_print_menu();

error_report_check( $_GET );

#print"<br>Please select from one of the reports below<br><br>";

print"<br><br>". NEWLINE;
print"<div align=center>". NEWLINE;
print"<table class=width90 rules=cols>". NEWLINE;

print"<tr class=tbl_header>". NEWLINE;
html_tbl_print_header( lang_get('report_name') );
html_tbl_print_header( lang_get('description') );
print"</tr>". NEWLINE;

# Custom Report
print"<tr class='row-2'>";
//print"<td class='tbl-l'><a href='report_custom_page.php'>". lang_get('report_custom_page') ."</a></td>". NEWLINE;
print"<td class='tbl-l'>". lang_get('report_custom_page') ."</td>". NEWLINE;
print"<td class='tbl-l'>". lang_get('report_custom_page_desc') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

# TEST AREA REPORT
print"<tr class='row-1'>";
print"<td class='tbl-l'><a href='report_area_tested_page.php'>". lang_get('area_tested') ."</a></td>". NEWLINE;
print"<td class='tbl-l'>". lang_get('area_tested_desc') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

# BUILD STATUS REPORT
print"<tr class='row-2'>";
print"<td class='tbl-l'><a href='report_build_status_page.php'>". lang_get('build_status') ."</a></td>". NEWLINE;
print"<td class='tbl-l'>". lang_get('build_status_desc') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

# FAILED VERIFICATIONS
print"<tr class='row-1'>";
print"<td class='tbl-l'><a href='report_verif_page.php'>". lang_get('failed_verifications') ."</a></td>". NEWLINE;
print"<td class='tbl-l'>". lang_get('failed_ver_desc') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

# REQUIREMENT COVERAGE REPORT
print"<tr class='row-2'>";
print"<td class='tbl-l'><a href='report_requirements_page.php'>". lang_get('requirements_coverage') ."</a></td>". NEWLINE;
print"<td class='tbl-l'>". lang_get('requirements_coverage_desc') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

# SIGN OFF REPORT
print"<tr class='row-1'>";
print"<td class='tbl-l'><a href='report_signoff_page.php'>". lang_get('signoff_report') ."</a></td>". NEWLINE;
print"<td class='tbl-l'>". lang_get('signoff_report_desc') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

#TESTSETS STATUS PAGE
print"<tr class='row-2'>";
print"<td class='tbl-l'><a href='testset_viewlast_page.php'>". lang_get('testsets_status') ."</a></td>". NEWLINE;
print"<td class='tbl-l'>". lang_get('testsets_status_desc') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;
print"</div>". NEWLINE;

/*
print"<a href='report_build_status_page.php'>Build Status</a>";
print"<br><br>";

print"<a href='report_verif_page.php'>Failed Verifications</a>";
print" <br><br>";

print"<a href='report_requirements_page.php'>Requirements</a>";
print" <br><br>";

print"<a href='report_signoff_page.php'>Sign Off</a>";
print" <br><br>";
*/


html_print_footer();


# ------------------------------------
# $Log: report_page.php,v $
# Revision 1.7  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.6  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.5  2006/06/10 01:55:06  gth2
# no message
#
# Revision 1.4  2006/04/08 13:38:41  gth2
# Adding to reporting module
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
# ------------------------------------
?>
