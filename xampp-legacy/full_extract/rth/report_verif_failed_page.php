<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Report Failed Verifications Page
#
# $RCSfile: report_verif_failed_page.php,v $  $Revision: 1.8 $
# ------------------------------------

include"./api/include_api.php";

$project_name = session_get_project_name();
$page                   = basename(__FILE__);
$show_verifications_page="results_view_verifications_page.php";
$report_verif_page		= "report_verif_page.php";

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$row_style 	= '';

$order_by 		= VERIFY_RESULTS_ID;
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

if( isset($_GET['bugs_only']) ) {
	$bugs_only 	= true;
} else {
	$bugs_only 	= false;
}

html_window_title();

auth_authenticate_user();

html_print_body();
html_page_title($project_name ." - " . lang_get('report_failed_verif_page') );
html_page_header( $db, $project_name );
html_print_menu();
html_browse_release_menu($db, $page, $project_id);

error_report_check($_GET);

print"<div align=center>". NEWLINE;

if( isset( $testset_id ) && $testset_id != 'all' ) {

	print"[ <a href='$report_verif_page?_release_id=$release_id&amp;_build_id=$build_id&amp;_testset_id=$testset_id'>".lang_get("back_to_verifs_report")."</a> ]". NEWLINE;

	print"<br><br>". NEWLINE;

	print"<form method=post action='$page?_release_id=$release_id&amp;_build_id=$build_id&amp;_testset_id=$testset_id'>". NEWLINE;

	$rows_failed_verifications = report_get_failed_verifs($testset_id, $order_by, $order_dir, $page_number, $bugs_only);

	if( !empty($rows_failed_verifications) ) {
		print"<table class=width100 rules=cols>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('test_id') );
		html_tbl_print_header( lang_get('test_name') );
		html_tbl_print_header( lang_get('action_') );
		html_tbl_print_header( lang_get('expected_') );
		html_tbl_print_header( lang_get('actual_') );
		html_tbl_print_header( '' );
		html_tbl_print_header( lang_get('defect') );
		html_tbl_print_header( lang_get('bug_status') );
		html_tbl_print_header( lang_get('time_failed') );
		html_tbl_print_header( lang_get('os') );
		html_tbl_print_header( lang_get('window') );
		html_tbl_print_header( lang_get('object') );
		html_tbl_print_header( lang_get('policy_id') );
		html_tbl_print_header( lang_get('claim_id') );
		print"</tr>". NEWLINE;

		foreach($rows_failed_verifications as $row_failed_verification) {
			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td><a href='$show_verifications_page?test_run_id=".$row_failed_verification[VERIFY_RESULTS_TS_UNIQUE_RUN_ID]."&amp;release_id=$release_id&amp;build_id=$build_id&amp;testset_id=$testset_id'>".$row_failed_verification[VERIFY_RESULTS_TS_UNIQUE_RUN_ID]."</a></td>". NEWLINE;
			print"<td>".$row_failed_verification[TEST_RESULTS_TEST_SUITE]."</td>". NEWLINE;
			print"<td>".$row_failed_verification[VERIFY_RESULTS_ACTION]."</td>". NEWLINE;
			print"<td>".$row_failed_verification[VERIFY_RESULTS_EXPECTED_RESULT]."</td>". NEWLINE;
			print"<td>".$row_failed_verification[VERIFY_RESULTS_ACTUAL_RESULT]."</td>". NEWLINE;
			print results_verfication_status_icon( $row_failed_verification[VERIFY_RESULTS_TEST_STATUS] );
			if($row_failed_verification[VERIFY_RESULTS_DEFECT_ID] != 0) {
				$defect_id = util_pad_id( $row_failed_verification[VERIFY_RESULTS_DEFECT_ID] );
				print"<td><a href='". VIEW_BUG_URL ."?defect_id=$defect_id&id=$defect_id'>$defect_id</a></td>". NEWLINE;
				print"<td>". bug_get_field_value( $defect_id, BUG_STATUS ) ."</td>". NEWLINE;
			} else {
				print"<td></td>". NEWLINE;
				print"<td></td>". NEWLINE;
			}
			//print"<td>". bug_get_field_value( $defect_id, BUG_STATUS ) ."</td>". NEWLINE;
			print"<td>".$row_failed_verification[VERIFY_RESULTS_TIMESTAMP]."</td>". NEWLINE;
			print"<td>".$row_failed_verification[TEST_RESULTS_OS]."</td>". NEWLINE;
			print"<td>".$row_failed_verification[VERIFY_RESULTS_WINDOW]."</td>". NEWLINE;
			print"<td>".$row_failed_verification[VERIFY_RESULTS_OBJ]."</td>". NEWLINE;
			print"<td>".$row_failed_verification[VERIFY_RESULTS_SHOW_CUSTOM_1]."</td>". NEWLINE;
			print"<td>".$row_failed_verification[VERIFY_RESULTS_SHOW_CUSTOM_2]."</td>". NEWLINE;
			print"</tr>". NEWLINE;
		}
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
		print"<br><br>". NEWLINE;
	}
}

print"</div>". NEWLINE;

html_print_footer();


# ------------------------------------
# $Log: report_verif_failed_page.php,v $
# Revision 1.8  2007/02/03 10:25:53  gth2
# no message
#
# Revision 1.7  2006/10/18 12:58:23  gth2
# add defect status to the failed verification page - gth
#
# Revision 1.6  2006/09/28 02:42:16  gth2
# display correct bug ID on FailedVerificationsReport - gth
#
# Revision 1.5  2006/09/28 00:09:25  gth2
# Changing label of header from status to '' - gth
#
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