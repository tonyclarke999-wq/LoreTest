<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Test Run Page
#
# $RCSfile: results_test_run_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$page                   		= basename(__FILE__);
$results_page					= "results_page.php";
$delete_page					= "delete_page.php";
$results_view_verify_page		= "results_view_verifications_page.php";
$results_pass_test_run			= "results_update_pass_test_run_action.php";
$results_update_test_run		= "results_update_test_run_page.php";
$row_style              		= '';


if( isset($_GET['testset_id']) && isset($_GET['test_id']) ) {
	$s_results = session_set_properties("results", $_GET);
    $testset_id = $s_results['testset_id'];
    $test_id = $s_results['test_id'];
} else {   # coming from redirect etc, get stored testset_id and test_run_id
    $s_results = session_get_properties("results");
    $testset_id = $s_results['testset_id'];
    $test_id = $s_results['test_id'];
}
$locked = testset_get_lock_status($testset_id);

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('test_results') );
html_page_header( $db, $project_name );
html_print_menu();
html_test_results_menu( $db, $results_page, $project_id, session_get_properties("results", $_GET) );

error_report_check( $_GET );

if($locked){
	print"<h3 class='hint'> <img src='images/locked.png' alt='locked'> Testset locked</h3>". NEWLINE;
}

$row_test_detail = test_get_detail( $test_id );

if ( !empty($row_test_detail) ) {

	//results_print_test_detail_table( $row_test_detail );
	print"<table class=width100 rules=cols>";
	print"<tr class='tbl_header'>";
	html_tbl_print_header( lang_get('test_id') );
	html_tbl_print_header( lang_get('test_name') );
	html_tbl_print_header( lang_get('ba_owner') );
	html_tbl_print_header( lang_get('qa_owner') );
	html_tbl_print_header( lang_get('area_tested') );
	print"</tr>";

	$test_id              = util_pad_id($row_test_detail[TEST_ID]);
	$test_name            = $row_test_detail[TEST_NAME];
	$ba_owner             = $row_test_detail[TEST_BA_OWNER];
	$qa_owner             = $row_test_detail[TEST_QA_OWNER];
	$area_tested          = $row_test_detail[TEST_AREA_TESTED];

	print"<tr>";
	print"<td class='tbl-c'><a href='test_detail_page.php?test_id=$test_id&project_id=$project_id'>$test_id</a></td>";
	print"<td class='tbl-c'>$test_name</td>";
	print"<td class='tbl-c'>$ba_owner</td>";
	print"<td class='tbl-c'>$qa_owner</td>";
	print"<td class='tbl-c'>$area_tested</td>";
	print"</tr>";
	print"</table>";
	print"<br><br>";
}

$rows_test_runs = results_get_test_run_by_test( $test_id, $testset_id );

if( !empty( $rows_test_runs ) ) {

	print"<table class=width100 rules=cols>";
	print"<tr class=tbl_header>";
	html_tbl_print_header( lang_get('machine_name') );
	html_tbl_print_header( lang_get('man_auto') );
	html_tbl_print_header( lang_get('time_started') );
	html_tbl_print_header( lang_get('time_finished') );
	html_tbl_print_header( lang_get('environment') );
	#html_tbl_print_header( lang_get('finished') );
	html_tbl_print_header( lang_get('os') );
	html_tbl_print_header( lang_get('sp') );
	html_tbl_print_header( lang_get('pass') );
	html_tbl_print_header( lang_get('fail') );
	html_tbl_print_header( lang_get('blank') );
	html_tbl_print_header( lang_get('total') );
	html_tbl_print_header( lang_get('tester') );
    //html_tbl_print_header( lang_get('test_status') );
    //html_tbl_print_header( "" );
	//html_tbl_print_header( lang_get('comment') );
	//html_tbl_print_header( lang_get('sign_off') );
	//print"<th></th>";
	//html_tbl_print_header( lang_get('info') );
	//html_tbl_print_header( lang_get('doc') );
	html_tbl_print_header( lang_get('view_results') );
	//html_tbl_print_header( lang_get('update') );
	//html_tbl_print_header( lang_get('delete') );
	print"</tr>";

	foreach( $rows_test_runs as $row_test_run ) {

		$machine_name		= $row_test_run[TEST_RESULTS_MACHINE_NAME];
		$time_started		= $row_test_run[TEST_RESULTS_TIME_STARTED];
		$time_finished		= $row_test_run[TEST_RESULTS_TIME_FINISHED];
		$env				= $row_test_run[TEST_RESULTS_ENVIRONMENT];
		#$finished			= $row_test_run[TEST_RESULTS_FINISHED];
		$os					= $row_test_run[TEST_RESULTS_OS];
		$sp					= $row_test_run[TEST_RESULTS_SP];
		$assigned_to		= $row_test_run[TEST_RESULTS_ASSIGNED_TO];
		$status				= $row_test_run[TEST_RESULTS_TEST_STATUS];
		$comments			= $row_test_run[TEST_RESULTS_COMMENTS];
		$rows_test_runs_id	= $row_test_run[TEST_RESULTS_TS_UNIQUE_RUN_ID];
		$passed				= $row_test_run["Passed"];
		$failed				= $row_test_run["Failed"];
		$info				= $row_test_run["Info"];
		$hold				= $row_test_run["Hold"];
		$blank				= $row_test_run["Blank"];
		$total				= $row_test_run["Total"];

		if(substr("$rows_test_runs_id", 0, 1) == 'S') {
			$rows_test_runs_type =  lang_get( 'automated' );
		}
		else {
			$rows_test_runs_type =  lang_get( 'manual' );
		}

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>";
		print"<td class='tbl-c'>$machine_name</td>";
		print"<td class='tbl-c'>$rows_test_runs_type</td>";
		print"<td class='tbl-c'>$time_started</td>";
		print"<td class='tbl-c'>$time_finished</td>";
		print"<td class='tbl-c'>$env</td>";
		#print"<td class='tbl-c'>$finished</td>";
		print"<td class='tbl-c'>$os</td>";
		print"<td class='tbl-c'>$sp</td>";
		print"<td class='tbl-c'>$passed</td>";
		print"<td class='tbl-c'>$failed</td>";
		print"<td class='tbl-c'>$blank</td>";
		print"<td class='tbl-c'>$total</td>";
		print"<td class='tbl-c'>$assigned_to</td>";
		/*

		print"<td class='tbl-c'>$status</td>";
		print"<td class='tbl-c'>".html_teststatus_icon($status)."</td>";

		# -------- Comment Icon ----------

		if( !empty($comments) ) {
			print"<td class='center'><img src='images/info.gif' title='$comments'></td>";
		} else {
			print"<td></td>";
		}


		if(results_does_test_run_file_exist($rows_test_runs_id) == "Yes") {
			print"<td class='tbl-c'><IMG border=0 SRC='images/paperclip.gif'></td>";
		} else {
			print"<td class='tbl-c'></td>";
		}
		*/
		# VIEW
		print"<td class='tbl-c'><a href='$results_view_verify_page?test_run_id=$rows_test_runs_id&amp;testset_id=$testset_id&amp;test_id=$test_id'>$rows_test_runs_id</a></td>";
		//print"<td class='tbl-c'><a href='$results_update_test_run?test_run_id=$rows_test_runs_id'>". lang_get('update') ."</a></td>";
		//print"<td class='tbl-c'><a href='$results_add_test_run_comment?test_id=$test_id&testset_id=$testset_id'>". lang_get('comment') ."</a></td>";

		# DELETE TEST RUN
		//print"<td class='tbl-c'><a href='$delete_page?r_page=$page&f=results_delete_test_run&id=$rows_test_runs_id&msg=10'>". lang_get('delete') ."</a></td>";
		print"</tr>";

	}  // end for( $i=0; $i < sizeof( $rows_test_runs ); $i++ ) {

	print"</table>";
}
else {

	print"<br><span class='print'>" . lang_get('no_test_runs') . "</span>";

}

html_print_footer();

# ---------------------------------------------------------------------
# $Log: results_test_run_page.php,v $
# Revision 1.3  2008/07/25 09:50:02  peter_thal
# added lock testset feature
# disabled detail column in test result, because functionality is not implemented yet
#
# Revision 1.2  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>