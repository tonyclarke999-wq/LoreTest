<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Report Requirements Page
#
# $RCSfile: report_requirements_page.php,v $  $Revision: 1.4 $
# ------------------------------------

include"./api/include_api.php";

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
html_page_title($project_name ." - " . lang_get('report_requirements_page') );
html_page_header( $db, $project_name );
html_print_menu();
html_browse_release_menu($db, $page, $project_id);

error_report_check($_GET);

print"<div align=center>";
print"<br>". NEWLINE;

if( isset( $testset_id ) && $testset_id != 'all' ) {

	$test_count = 0;
	$passed_count = 0;
	$failed_count = 0;
	$awaiting_review_count = 0;
	$wip_count = 0;
	$not_running_count = 0;
	$not_started_count = 0;
	$finished_qa_count = 0;
	$finished_ba_count = 0;
	$wr_issue_count = 0;
	$count_total_percent_cov = 0;
	$table_body = "";

	$rows_requirements = report_requirements_get($release_id, $order_by, $order_dir, $page_number);

	$number_of_requirements = sizeof($rows_requirements);

	foreach($rows_requirements as $row_requirements) {

		$req_id 	= $row_requirements[REQ_ID];
		$req_name	= $row_requirements[REQ_FILENAME];
		$req_version= $row_requirements[REQ_VERS_VERSION];

		$row_style = html_tbl_alternate_bgcolor($row_style);
		$req_tests = requirement_get_test_details($req_id, $testset_id);
		$rowspan = sizeof($req_tests) + 3;

		$table_body .= "<tbody>". NEWLINE;
		$table_body .= "<tr class='$row_style'>". NEWLINE;
		$table_body .= "<td class=tbl-l rowspan=$rowspan colspan=1 valign=top>$req_name</td>". NEWLINE;
		$table_body .= "</tr>". NEWLINE;

		$table_body .= "<tr class='$row_style'>". NEWLINE;
		$table_body .= "<td>$req_version&nbsp;</td>". NEWLINE;
		$table_body .= "<td></td>". NEWLINE;
		$table_body .= "<td></td>". NEWLINE;
		$table_body .= "<td></td>". NEWLINE;
		$table_body .= "<td></td>". NEWLINE;
		$table_body .= "<td></td>". NEWLINE;
		$table_body .= "</tr>". NEWLINE;

		$count_percent_cov = 0;
		foreach($req_tests as $row_req_tests) {

			$pc_req_covered_by_test = $row_req_tests[TEST_REQ_ASSOC_PERCENT_COVERED];
			$test_status			= $row_req_tests[TEST_TS_ASSOC_STATUS];
			$test_id				= $row_req_tests[TEST_TS_ASSOC_TEST_ID];

			$count_percent_cov += $pc_req_covered_by_test ;

			switch( $test_status ) {
			case "Passed":
				$passed_count++;
				break;
			case "Failed":
				$failed_count++;
				break;
			case "Finished: Awaiting Review":
			case "Finished : Awaiting Review":
				$awaiting_review_count++;
				break;
			case "WIP":
				$wip_count++;
				break;
			case "Not Running":
				$not_running_count++;
				break;
			case "Not Started":
				$not_started_count++;
				break;
			case "Finished : QA Review":
				$finished_qa_count++;
				break;
			case "Finished : Business Review":
				$finished_ba_count++;
				break;
			case "WRIssue":
				$wr_issue_count++;
				break;
			}

			$table_body .= "<tr class='$row_style'>". NEWLINE;
			$table_body .= "<td></td>". NEWLINE;
			$table_body .= "<td class=tbl-l nowrap>".test_get_name($test_id)."</td>". NEWLINE;
			$table_body .= "<td class=tbl-r nowrap>$test_status</td>". NEWLINE;
			$table_body .=  results_verfication_status_icon( $test_status );
			$table_body .= "<td>$pc_req_covered_by_test%</td>". NEWLINE;
			$table_body .= "<td></td>". NEWLINE;
			$table_body .= "</tr>". NEWLINE;
		}

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
		$table_body .= "</tbody>". NEWLINE;
	}

	# If some of the requirements have been covered, display the graph
	if( $count_total_percent_cov ) {
		print"<img src=\"images/pie_chart_image.php";
			# pie chart title
			print"?graph_title=".lang_get("status_of")." ".admin_get_release_name($release_id).", ".admin_get_build_name($build_id).", ".admin_get_testset_name($testset_id);
			# legend
			print"&amp;legend='Passed','Failed','Awaiting Review','WIP','Not Running','Not Started','Finished QA','Finished BA','Winrunner Issue'";
			if($project_name=="PCA") {
				print",'Finished: QA Review', 'Finished: Business Review', 'WinRunner Issue'";
			}
			# data
			print"&amp;data=$passed_count,$failed_count,$awaiting_review_count,$wip_count,$not_running_count,$not_started_count,$finished_qa_count,$finished_ba_count,$wr_issue_count";
			# theme
			if($project_name=="PCA") {
				print"&amp;theme=pca";
			} else {
				print"&amp;theme=test";
			}
			print"\">". NEWLINE;
	}
	print"<br><br>". NEWLINE;

	print"<table class=width60>". NEWLINE;
	print"<tr>". NEWLINE;
	html_tbl_print_header( lang_get('num_req') );
	html_tbl_print_header( lang_get('tot_percent_req_cov') );
	print"</tr>". NEWLINE;

	print"<tr>". NEWLINE;
	print"<td>$number_of_requirements</td>". NEWLINE;
	print"<td>";
	if( $number_of_requirements ) {
		print sprintf( "%01.2f", ( $count_total_percent_cov/$number_of_requirements ) );
	} else {
		print "0";
	}
	print"%</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	if($number_of_requirements) {
		print"<br><br>". NEWLINE;

		print"<form action='$page?_release_id=$release_id&amp;_build_id=$build_id&amp;_testset_id=$testset_id' method=post>";
		print"<table class=width90 rules=cols>". NEWLINE;
		print"<thead>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('req'), REQ_FILENAME, $order_by, $order_dir );
		html_tbl_print_header( lang_get('version') );
		html_tbl_print_header( lang_get('test') );
		html_tbl_print_header( lang_get('test_status') );
		html_tbl_print_header( "" );
		html_tbl_print_header( "%" );
		html_tbl_print_header( lang_get('total_coverage') );
		print"</tr>". NEWLINE;
		print"</thead>". NEWLINE;
		print $table_body;

		print"</table>". NEWLINE;
		print"</form>";
	}
}

print"</div>";

html_print_footer();


# ------------------------------------
# $Log: report_requirements_page.php,v $
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
# ------------------------------------
?>