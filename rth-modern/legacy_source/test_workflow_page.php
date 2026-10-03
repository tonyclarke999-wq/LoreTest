<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Workflow Page
#
# $RCSfile: test_workflow_page.php,v $  $Revision: 1.8 $
# ---------------------------------------------------------------------


if( isset($_POST['submit_button']) ) {

	require_once("test_workflow_group_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$num                    = 0;
$bg_color               = '';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$s_display_options 		= session_set_display_options("test_workflow", $_POST);
$order_by				= $s_display_options['order_by'];
$order_dir				= $s_display_options['order_dir'];
$page_number			= $s_display_options['page_number'];
$filter_per_page		= $s_display_options['filter']['per_page'];
$filter_manual_auto		= $s_display_options['filter']['manual_auto'];
$filter_test_type		= $s_display_options['filter']['test_type'];
$filter_ba_owner		= $s_display_options['filter']['ba_owner'];
$filter_qa_owner		= $s_display_options['filter']['qa_owner'];
$filter_tester			= $s_display_options['filter']['tester'];
$filter_area_tested		= $s_display_options['filter']['area_tested'];
$filter_test_status		= $s_display_options['filter']['test_status'];
$filter_priority		= $s_display_options['filter']['priority'];
$filter_test_search		= $s_display_options['filter']['test_search'];

html_window_title();
html_print_body();
html_page_title($project_name ." - TESTS WORKFLOW");
html_page_header($db, $project_name);
html_print_menu();

test_menu_print($page);

error_report_check( $_GET );

print"<br>". NEWLINE;

print"<form method=post action=$page id='form_order'>". NEWLINE;

print"<div align=center>". NEWLINE;

html_print_tests_filter(	$project_id,
							$filter_manual_auto,
							$filter_test_type,
							$filter_ba_owner,
							$filter_qa_owner,
							$filter_tester,
							$filter_area_tested,
							$filter_test_status,
							$filter_priority,
							$filter_per_page,
							$filter_test_search);

print"<br>". NEWLINE;


$g_timer->mark_time( "Load rows to display on page from db into memory" );

$row = test_workflow_filter_rows(	$project_id,
									$filter_manual_auto,
									$filter_test_type,
									$filter_ba_owner,
									$filter_qa_owner,
									$filter_tester,
									$filter_area_tested,
									$filter_test_status,
									$filter_priority,
									$filter_per_page,
									$filter_test_search,
									$order_by,
									$order_dir,
									$page_number,
									$csv_name="test_workflow" );

if( $row ) {

	$g_timer->mark_time( "Finished load rows to display on page from db into memory" );

	$page_count = ceil($num / $filter_per_page );

	#print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
	print"<table class='sortable' rules=cols>". NEWLINE;
	print"<thead>".NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;

	html_tbl_print_sortable_header( lang_get('test_id'), 	TEST_ID, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('man_auto') );
	html_tbl_print_sortable_header( lang_get('test_name'), 		TEST_NAME,		$order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('test_status'),		TEST_STATUS,	$order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('priority'), 		TEST_PRIORITY,	$order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('ba_owner'), 		TEST_BA_OWNER,	$order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('qa_owner'), 		TEST_QA_OWNER,	$order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('tester'),			TEST_TESTER,		$order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('date_assigned'),	TEST_DATE_ASSIGNED, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('date_expected'),	TEST_DATE_EXPECTED, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('date_complete'),	TEST_DATE_COMPLETE, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('ba_signoff_date'), TEST_BA_SIGNOFF,	$order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('info'), 			TEST_COMMENTS,		$order_by, $order_dir );
	
	#html_tbl_print_header( lang_get('test_id') );
	#html_tbl_print_header_not_sortable( lang_get('man_auto') );
	#html_tbl_print_header( lang_get('test_name') );
	#html_tbl_print_header( lang_get('test_status') );
	#html_tbl_print_header( lang_get('priority') );
	#html_tbl_print_header( lang_get('ba_owner') );
	#html_tbl_print_header( lang_get('qa_owner') );
	#html_tbl_print_header( lang_get('tester') );
	#html_tbl_print_header( lang_get('date_assigned') );
	#html_tbl_print_header( lang_get('date_expected') );
	#html_tbl_print_header( lang_get('date_complete') );
	#html_tbl_print_header( lang_get('ba_signoff_date') );
	#html_tbl_print_header_not_sortable( lang_get('info') );
	print"</tr>". NEWLINE;

	print"</thead>".NEWLINE;
	print"<tbody>".NEWLINE;
	$row_style = '';

	$g_timer->mark_time( "Outputting main html table to browser" );

	foreach( $row as $test_wf ) {

		$test_id        = util_pad_id($test_wf[TEST_ID]);
		$test_name      = $test_wf[TEST_NAME];
		$ba_owner       = $test_wf[TEST_BA_OWNER];
		$qa_owner       = $test_wf[TEST_QA_OWNER];
		$tester 		= $test_wf[TEST_TESTER];
		$test_status    = $test_wf[TEST_STATUS];
		$test_priority  = $test_wf[TEST_PRIORITY];
		$manual         = $test_wf[TEST_MANUAL];
		$automated      = $test_wf[TEST_AUTOMATED];
		$autopass       = $test_wf[TEST_AUTO_PASS];
		$date_assigned  = $test_wf[TEST_DATE_ASSIGNED];
		$date_expected  = $test_wf[TEST_DATE_EXPECTED];
		$date_complete  = $test_wf[TEST_DATE_COMPLETE];
		$ba_signoff     = $test_wf[TEST_BA_SIGNOFF];
		$comments       = $test_wf[TEST_COMMENTS];
		$priority		= $test_wf[TEST_PRIORITY];

		if ("Y" == $test_wf[TEST_AUTO_PASS] )
			$autopass = "Yes". NEWLINE;
		else
			$autopass = "No". NEWLINE;

		$filename = test_get_filename ($test_id ) ;

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		print"<td class='tbl-c'><a href='test_detail_page.php?test_id=$test_id&project_id=$project_id'>$test_id</a></td>". NEWLINE;
		print"<td class='tbl-l'>".html_print_testtype_icon( $manual, $automated)."</td>". NEWLINE;
		print"<td class='tbl-l'>$test_name</td>". NEWLINE;
		print"<td class='tbl-l'>$test_status</td>". NEWLINE;
		print"<td class='tbl-l'>$test_priority</td>". NEWLINE;
		print"<td class='tbl-l'>$ba_owner</td>". NEWLINE;
		print"<td class='tbl-l'>$qa_owner</td>". NEWLINE;
		print"<td class='tbl-l'>$tester</td>". NEWLINE;
		print"<td class='tbl-l'>$date_assigned</td>". NEWLINE;
		print"<td class='tbl-l'>$date_expected</td>". NEWLINE;
		print"<td class='tbl-l'>$date_complete</td>". NEWLINE;
		print"<td class='tbl-l'>$ba_signoff</td>". NEWLINE;
		print"<td class='tbl-l'>". NEWLINE;
			if($comments) {
				print "<img src='". IMG_SRC . "/info.gif' title='" . $comments . "'>". NEWLINE;
			}
			else {
				print '&nbsp;';
			}
			print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

	}
	$g_timer->mark_time( "Finished outputting main html table to browser" );
	print"</tbody>".NEWLINE;
	print"</table>". NEWLINE;
} else {

	html_no_records_found_message( lang_get('no_tests') );
}

print"</div>". NEWLINE;
print"</form>";

html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_workflow_page.php,v $
# Revision 1.8  2008/08/04 06:54:58  peter_thal
# added sorting function to several tables
#
# Revision 1.7  2008/07/09 07:13:26  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.6  2008/01/22 07:53:56  cryobean
# made the table sortable
#
# Revision 1.5  2007/02/03 10:26:19  gth2
# no message
#
# Revision 1.4  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2006/01/16 13:27:45  gth2
# adding excel integration - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>
