<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Test Assoc Report Page
#
# $RCSfile: requirement_tests_assoc_report_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
include"./jpgraph-1.8/src/jpgraph.php";
include"./jpgraph-1.8/src/jpgraph_pie.php";

$project_name = session_get_project_name();
$page                   = basename(__FILE__);

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$row_style = '';


$order_by 		= REQ_FILENAME;
$order_dir		= "ASC";
$page_number	= 1;

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);

html_window_title();

auth_authenticate_user();

html_print_body();
html_page_title($project_name ." - " . lang_get('report_requirements_page') );
html_page_header( $db, $project_name );
html_print_menu();

error_report_check($_GET);

$table_body = "";

foreach(requirement_get($project_id) as $row_requirements) {

	$row_style = html_tbl_alternate_bgcolor($row_style);
	$rows_test_assoc = requirements_test_assoc($row_requirements[REQ_ID]);
	$rowspan = sizeof($rows_test_assoc) + 2;

	$table_body .= "<tbody>". NEWLINE;

	$table_body .= "<tr class='$row_style'>". NEWLINE;
	$table_body .= "<td class=tbl-l rowspan=$rowspan colspan=1 valign=top>".$row_requirements[REQ_FILENAME]."</td>". NEWLINE;
	$table_body .= "</tr>". NEWLINE;

	$table_body .= "<tr class='$row_style'>". NEWLINE;
	$table_body .= "<td>b</td>". NEWLINE;
	$table_body .= "<td>c</td>". NEWLINE;
	$table_body .= "<td>d</td>". NEWLINE;
	$table_body .= "<td>e</td>". NEWLINE;
	$table_body .= "</tr>". NEWLINE;

	foreach($rows_test_assoc as $row_test_assoc) {

		$table_body .= "<tr class='$row_style'>". NEWLINE;
		$table_body .= "<td>".$row_test_assoc[TEST_NAME]."&nbsp;</td>". NEWLINE;
		$table_body .= "<td></td>". NEWLINE;
		$table_body .= "<td></td>". NEWLINE;
		$table_body .= "<td></td>". NEWLINE;
		$table_body .= "</tr>". NEWLINE;
	}
/*
	$table_body .= "<tr class='$row_style'>". NEWLINE;
	$table_body .= "<td></td>". NEWLINE;
	$table_body .= "<td></td>". NEWLINE;
	$table_body .= "<td></td>". NEWLINE;
	$table_body .= "<td></td>". NEWLINE;
	$table_body .= "<td></td>". NEWLINE;

	if($count_percent_cov>100) {
		$count_percent_cov = "100";
	}
	$count_total_percent_cov += $count_percent_cov;

	$table_body .= "<td>$count_percent_cov%</td>". NEWLINE;
	$table_body .= "</tr>". NEWLINE;
*/
	$table_body .= "</tbody>". NEWLINE;

}

print"<div align=center>";
print"<br>". NEWLINE;

print"<table class=width90 rules=cols>". NEWLINE;
print"<thead>". NEWLINE;
print"<tr>". NEWLINE;
html_tbl_print_header( "a" );
html_tbl_print_header( "b" );
html_tbl_print_header( "c" );
html_tbl_print_header( "d" );
html_tbl_print_header( "e" );
print"</tr>". NEWLINE;
print"</thead>". NEWLINE;

print $table_body;

print"</table>". NEWLINE;

print"</div>";

html_print_footer();


# ---------------------------------------------------------------------
# $Log: requirement_tests_assoc_report_page.php,v $
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