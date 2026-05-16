<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Report Signoff Page
#
# $RCSfile: report_signoff_page.php,v $  $Revision: 1.6 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_name = session_get_project_name();
$page         = basename(__FILE__);

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$row_style = '';

$order_by		= TEST_NAME;
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
html_print_body();
html_page_title($project_name ." - " . lang_get('report_signoff_page') );
html_page_header( $db, $project_name );
html_print_menu();
html_browse_release_menu($db, $page, $project_id);

error_report_check($_GET);

print"<div align=center>";
print"<br>". NEWLINE;

if( isset( $testset_id ) && $testset_id != 'all' ) {
$g_timer->mark_time( "Image" );

	$testset_status 				= report_get_testset_status( $testset_id );
	$testset_num_of_tests 			= $testset_status[0];
	$testset_num_of_passed 			= $testset_status[1];
	$testset_num_of_failed 			= $testset_status[2];
	$testset_num_of_awaiting_review	= $testset_status[3];
	$testset_num_of_wip				= $testset_status[4];
	$testset_num_of_not_running		= $testset_status[5];
	$testset_num_of_not_started		= $testset_status[6];

	$build_status 					= report_get_build_status( $build_id );
	$build_date_received 			= $build_status[0];
	$build_num_of_test_sets 		= $build_status[1];
	$build_num_of_tests 			= $build_status[2];
	$build_num_of_passed 			= $build_status[3];
	$build_num_of_failed 			= $build_status[4];
	$build_num_of_awaiting_review	= $build_status[5];
	$build_num_of_wip				= $build_status[6];
	$build_num_of_not_running		= $build_status[7];
	$build_num_of_not_started		= $build_status[8];

	$project_status 				= report_get_project_status( $project_id );
	$project_num_of_test_sets 		= $project_status[0];
	$project_num_of_tests 			= $project_status[1];
	$project_num_of_passed 			= $project_status[2];
	$project_num_of_failed 			= $project_status[3];
	$project_num_of_awaiting_review	= $project_status[4];
	$project_num_of_wip				= $project_status[5];
	$project_num_of_not_running		= $project_status[6];
	$project_num_of_not_started		= $project_status[7];

	$pc_tests_not_run				= 100/$project_num_of_tests * ($project_num_of_tests- $testset_num_of_tests);

	$pc_passed_in_project			= 100/$project_num_of_tests*$testset_num_of_passed;
	$pc_failed_in_project			= 100/$project_num_of_tests*$testset_num_of_failed;
	$pc_awaiting_review_in_project	= 100/$project_num_of_tests*$testset_num_of_awaiting_review;
	$pc_wip_in_project				= 100/$project_num_of_tests*$testset_num_of_wip;
	$pc_not_started_in_project		= 100/$project_num_of_tests*$testset_num_of_not_started;
	$pc_not_running_in_project		= 100/$project_num_of_tests*$testset_num_of_not_running;


	if( $testset_num_of_tests == 0 ) {
		print"<p class='error'>". lang_get('no_tests_testset') ."</p>";
	}
	# start img pie chart
	print"<img src=\"./images/pie_chart_2_image.php";
	# chart title
	print"?graph_title=".lang_get("status_of")." ".admin_get_build_name($build_id).", ".admin_get_release_name($release_id);
	# legend
	print"&amp;legend=";
	print"'Passed',";
	print"'Failed',";
	print"'Awaiting Review',";
	print"'WIP',";
	print"'Not Running',";
	print"'Not Started'";
	//results_get_teststatus_by_project( $project_id );
	
	# pie 1 title
	print"&amp;p1_title=". lang_get("all_tests_in") ." ". $project_name;
	# theme
	print"&amp;p1_theme=test";
	
	# pie 1 data
	print"&amp;p1_data=";
	print"$pc_passed_in_project,";
	print"$pc_failed_in_project,";
	print"$pc_awaiting_review_in_project,";
	print"$pc_wip_in_project,";
	print"$pc_not_running_in_project,";
	print"$pc_not_started_in_project,";

	

	# pie 2 title
	print"&amp;p2_title=".lang_get("tests_in_ts")." ".admin_get_testset_name($testset_id);
	# pie 2 theme
	print"&amp;p2_theme=test";
	
	# pie 2 data
	print"&amp;p2_data=";
	print"$testset_num_of_passed,";
	print"$testset_num_of_failed,";
	print"$testset_num_of_awaiting_review,";
	print"$testset_num_of_wip,";
	print"$testset_num_of_not_running,";
	print"$testset_num_of_not_started,";
	# end img
	print"\">". NEWLINE;

	$g_timer->mark_time( "First Table" );

	print"<br><br>". NEWLINE;

	print"<form method=post action='$page?_release_id=$release_id&amp;_build_id=$build_id'>";
	print"<table class=width80 rules=all>". NEWLINE;
	print"<tr>". NEWLINE;
	html_tbl_print_header( "" );
	html_tbl_print_header( lang_get('total') );
	html_tbl_print_header( lang_get('percent_of_all_tests') );
	html_tbl_print_header( lang_get('percent_of_tests_in_ts') );
	print"</tr>". NEWLINE;

	# TOTAL NUMBER OF TESTS
	$row_style = html_tbl_alternate_bgcolor($row_style);
	print"<tr class=$row_style>". NEWLINE;
	html_tbl_print_header( lang_get("tot_num_tests") );
	print"<td>$project_num_of_tests</td>". NEWLINE;
	print"<td>100%</td>". NEWLINE;
	print"<td>-</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# NUMBER OF TESTS IN TEST SET
	$row_style = html_tbl_alternate_bgcolor($row_style);
	if( $project_num_of_tests > 0 ) {
		$pc_tests_in_ts = sprintf( "%01.2f", (100/$project_num_of_tests*$testset_num_of_tests) );
	} else {
		$pc_tests_in_ts = "0.00";
	}
	print"<tr class=$row_style>". NEWLINE;
	html_tbl_print_header( lang_get("tests_in_ts") );
	print"<td>$testset_num_of_tests</td>". NEWLINE;
	print"<td>$pc_tests_in_ts%</td>". NEWLINE;
	print"<td>100%</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# NUMBER OF PASSED
	$row_style = html_tbl_alternate_bgcolor($row_style);
	if( $project_num_of_tests ) {
		$pc_passed_in_project = sprintf( "%01.2f", $pc_passed_in_project );
	} else {
		$pc_passed_in_project = "0.00";
	}
	if( $project_num_of_tests > 0 && $testset_num_of_passed > 0 ) {
		$pc_passed_in_ts = sprintf( "%01.2f", (100/$testset_num_of_tests*$testset_num_of_passed) );
	} else {
		$pc_passed_in_ts = "0.00";
	}
	print"<tr class=$row_style>". NEWLINE;
	html_tbl_print_header( "Passed" );
	print"<td>$testset_num_of_passed</td>". NEWLINE;
	print"<td>$pc_passed_in_project%</td>". NEWLINE;
	print"<td>$pc_passed_in_ts%</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# NUMBER OF FAILED
	$row_style = html_tbl_alternate_bgcolor($row_style);
	if( $project_num_of_tests ) {
		$pc_failed_in_project = sprintf( "%01.2f", $pc_failed_in_project );
	} else {
		$pc_failed_in_project = "0.00";
	}
	if( $project_num_of_tests > 0 && $testset_num_of_failed > 0 ) {
		$pc_failed_in_ts = sprintf( "%01.2f", (100/$testset_num_of_tests*$testset_num_of_failed) );
	} else {
		$pc_failed_in_ts = "0.00";
	}
	print"<tr class=$row_style>". NEWLINE;
	html_tbl_print_header( "Failed" );
	print"<td>$testset_num_of_failed</td>". NEWLINE;
	print"<td>$pc_failed_in_project%</td>". NEWLINE;
	print"<td>$pc_failed_in_ts%</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# NUMBER FINISHED: AWAITING REVIEW
	$row_style = html_tbl_alternate_bgcolor($row_style);
	if( $project_num_of_tests ) {
		$pc_awaiting_review_in_project = sprintf( "%01.2f", $pc_awaiting_review_in_project );
	} else {
		$pc_awaiting_review_in_project = "0.00";
	}
	if( $project_num_of_tests > 0 && $testset_num_of_awaiting_review > 0 ) {
		$pc_awaiting_review_in_ts = sprintf( "%01.2f", (100/$testset_num_of_tests*$testset_num_of_awaiting_review) );
	} else {
		$pc_awaiting_review_in_ts = "0.00";
	}
	print"<tr class=$row_style>". NEWLINE;
	html_tbl_print_header( "Finished: Awaiting Review" );
	print"<td>$testset_num_of_awaiting_review</td>". NEWLINE;
	print"<td>$pc_awaiting_review_in_project%</td>". NEWLINE;
	print"<td>$pc_awaiting_review_in_ts%</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# NUMBER WIP
	$row_style = html_tbl_alternate_bgcolor($row_style);
	if( $project_num_of_tests ) {
		$pc_wip_in_project = sprintf( "%01.2f", $pc_wip_in_project );
	} else {
		$pc_wip_in_project = "0.00";
	}
	if( $project_num_of_tests > 0 && $testset_num_of_wip > 0 ) {
		$pc_wip_in_ts = sprintf( "%01.2f", (100/$testset_num_of_tests*$testset_num_of_wip) );
	} else {
		$pc_wip_in_ts = "0.00";
	}
	print"<tr class=$row_style>". NEWLINE;
	html_tbl_print_header( "WIP" );
	print"<td>$testset_num_of_wip</td>". NEWLINE;
	print"<td>$pc_wip_in_project%</td>". NEWLINE;
	print"<td>$pc_wip_in_ts%</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# NUMBER NOT RUNNING
	$row_style = html_tbl_alternate_bgcolor($row_style);
	if( $project_num_of_tests ) {
		$pc_not_running_in_project = sprintf( "%01.2f", $pc_not_running_in_project );
	} else {
		$pc_not_running_in_project = "0.00";
	}
	if( $project_num_of_tests > 0 && $testset_num_of_not_running > 0 ) {
		$pc_not_running_in_ts = sprintf( "%01.2f", (100/$testset_num_of_tests*$testset_num_of_not_running) );
	} else {
		$pc_not_running_in_ts = "0.00";
	}
	print"<tr class=$row_style>". NEWLINE;
	html_tbl_print_header( "Not Running" );
	print"<td>$testset_num_of_not_running</td>". NEWLINE;
	print"<td>$pc_not_running_in_project%</td>". NEWLINE;
	print"<td>$pc_not_running_in_ts%</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# NUMBER NOT STARTED
	$row_style = html_tbl_alternate_bgcolor($row_style);
	if( $project_num_of_tests ) {
		$pc_not_started_in_project = sprintf( "%01.2f", $pc_not_started_in_project );
	} else {
		$pc_not_started_in_project = "0.00";
	}
	if( $project_num_of_tests > 0 && $testset_num_of_not_started > 0 ) {
		$pc_not_started_in_ts = sprintf( "%01.2f", (100/$testset_num_of_tests*$testset_num_of_not_started) );
	} else {
		$pc_not_started_in_ts = "0.00";
	}
	print"<tr class=$row_style>". NEWLINE;
	html_tbl_print_header( "Not Started" );
	print"<td>$testset_num_of_not_started</td>". NEWLINE;
	print"<td>$pc_not_started_in_project%</td>". NEWLINE;
	print"<td>$pc_not_started_in_ts%</td>". NEWLINE;
	print"</tr>". NEWLINE;

	print"</table>". NEWLINE;
	print"</form>";

	print"<br><br>". NEWLINE;

	$g_timer->mark_time( "Second Table" );

	$test_signoff_details = report_get_test_signoff_details($testset_id, $order_by, $order_dir, $page);

	if( !empty($test_signoff_details) ) {
		print"<form method=post action='$page?_release_id=$release_id&amp;_build_id=$build_id&amp;_testset_id=$testset_id'>";
		print"<table class=width100 rules=cols>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( "" );
		html_tbl_print_header( lang_get('test_name'), 		TEST_NAME, 					$order_by, $order_dir);
		html_tbl_print_header( lang_get('area_tested'),		TEST_AREA_TESTED, 			$order_by, $order_dir);
		html_tbl_print_header( lang_get('test_assigned_to'),TEST_TS_ASSOC_ASSIGNED_TO,	$order_by, $order_dir);
		html_tbl_print_header( lang_get('test_status'), 	TEST_TS_ASSOC_STATUS, 		$order_by, $order_dir);
		html_tbl_print_header( "" );
		html_tbl_print_header( lang_get('os') );
		html_tbl_print_header( lang_get('info'), 			TEST_TS_ASSOC_COMMENTS, 	$order_by, $order_dir);
		html_tbl_print_header( lang_get('time_tested') );
		html_tbl_print_header( lang_get('time_approved'), 	TEST_TS_ASSOC_TIMESTAMP, 	$order_by, $order_dir);
		print"</tr>". NEWLINE;

		foreach($test_signoff_details as $row_test_signoff) {

			$row_style = html_tbl_alternate_bgcolor($row_style);

			$last_test_run	= test_get_last_run($row_test_signoff[TEST_ID], $testset_id);

			$os				= $last_test_run[TEST_RESULTS_OS];
			$time_started	= $last_test_run[TEST_RESULTS_TIME_STARTED];

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>".html_print_testtype_icon( $row_test_signoff[TEST_MANUAL], $row_test_signoff[TEST_AUTOMATED] )."</td>". NEWLINE;
			print"<td>".$row_test_signoff[TEST_NAME]."</td>". NEWLINE;
			print"<td>".$row_test_signoff[TEST_AREA_TESTED]."</td>". NEWLINE;
			print"<td>";
				if( !empty($row_test_signoff[TEST_TS_ASSOC_ASSIGNED_TO]) ) {
					print $row_test_signoff[TEST_TS_ASSOC_ASSIGNED_TO];
				} else {
					print"Not Assigned";
				}
			print"</td>". NEWLINE;
			print"<td>";
				if( !empty($row_test_signoff[TEST_TS_ASSOC_STATUS]) ) {
					print $row_test_signoff[TEST_TS_ASSOC_STATUS];
				} else {
					print"Not Used";
				}
			print"</td>". NEWLINE;
			if( isset($row_test_signoff[TEST_TS_ASSOC_STATUS]) ) {
				print results_verfication_status_icon( $row_test_signoff[TEST_TS_ASSOC_STATUS] );
			} else {
				print"<td></td>";
			}
			print"<td>$os</td>". NEWLINE;
			print"<td>";
				if( isset($row_test_signoff[TEST_TS_ASSOC_COMMENTS]) ) {
					print html_info_icon( $row_test_signoff[TEST_TS_ASSOC_COMMENTS] );
				}
			print"</td>". NEWLINE;
			print"<td>$time_started</td>". NEWLINE;
			print"<td>";
				if( isset($row_test_signoff[TEST_TS_ASSOC_TIMESTAMP]) ) {
					print $row_test_signoff[TEST_TS_ASSOC_TIMESTAMP];
				}
			print"</td>". NEWLINE;

			print"</tr>". NEWLINE;
		}
		print"</table>". NEWLINE;
		print"</form>";
		print"<br><br>". NEWLINE;
	}
}

print"</div>";

html_print_footer();


# ------------------------------------
# $Log: report_signoff_page.php,v $
# Revision 1.6  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.5  2006/06/10 01:55:06  gth2
# no message
#
# Revision 1.4  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.3  2006/01/14 19:08:05  gth2
# accounting for division by zero error - gth
#
# Revision 1.2  2005/12/08 19:39:51  gth2
# updating reports containing calls to jp-graph - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>