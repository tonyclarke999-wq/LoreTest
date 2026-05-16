<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results View Verifications Page
#
# $RCSfile: results_view_verifications_page.php,v $  $Revision: 1.12 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                  		    = basename(__FILE__);
$project_properties     		= session_get_project_properties();
$project_name           		= $project_properties['project_name'];
$project_id						= $project_properties['project_id'];

$delete_page			= 'delete_page.php';

$redirect_url			= $page ."?project_id=". $project_id;

$s_results			= session_get_properties("results");
$test_run_id		= $s_results['test_run_id'];

$page                   			= basename(__FILE__);
$row_results_page					= "results_page.php";
$row_results_pass_test_run			= "results_update_pass_test_run_action.php";
$row_results_fail_test_run			= "results_update_fail_test_run_action.php";
$row_results_add_test_run_comment	= "results_update_test_result_page.php";
$row_results_delete_test_run 		= "delete_page.php";
$row_results_update_verification	= "results_update_verification_page.php";
$csv_export_page					= "csv_export.php?table=test_run";
#$report_defect_page			    = BUGTRACKER_URL."bug_report_advanced_page.php";
#$view_defect_page					= BUGTRACKER_URL."bug_view_advanced_page.php";
$report_defect_page					= "bug_add_page.php";
$view_defect_page					= "bug_detail_page.php";
$row_style              			= '';

# Set the session vars if coming from the bug_page
# We need to do this because the bug page contains only the verification id
if( isset($_GET['bug_page']) ) {
	$results_page_vars = results_build_session_data_from_verification_id($_GET['verify_id']);
}

$s_results 		= session_set_properties( "results", $_GET );
$testset_id 	= $s_results['testset_id'];
//$test_id 		= $s_results['test_id'];
$test_run_id 	= $s_results['test_run_id'];

$locked			= testset_get_lock_status($testset_id);

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('test_results') );
html_page_header( $db, $project_name );
html_print_menu();
html_test_results_menu( $db, $row_results_page, $project_id, $s_results);


print"<div align=center>". NEWLINE;

error_report_check( $_GET );

$test_results_details = results_get_test_results_detail( $test_run_id );
if($locked){
	print"<h3 class='hint'> <img src='images/locked.png' alt='locked'> Testset locked</h3>". NEWLINE;
}

if( !empty( $test_results_details ) ) {

	print"<table class=width100 rules=cols>". NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;
	html_tbl_print_header( lang_get('test_run_id') );
	html_tbl_print_header( lang_get('test_name') );
	//html_tbl_print_header( lang_get('time_started') );
	//html_tbl_print_header( lang_get('time_finished') );
	//html_tbl_print_header( lang_get('finished') );
	//html_tbl_print_header( lang_get('machine_name') );
	//html_tbl_print_header( lang_get('os') );
	//html_tbl_print_header( lang_get('environment') );
	html_tbl_print_header( lang_get('cvs_version') );
	if(!$locked){
		html_tbl_print_header( lang_get('pass_test') );
		html_tbl_print_header( lang_get('fail_test') );
		html_tbl_print_header( lang_get('comment') );
		html_tbl_print_header( lang_get('continue_test_run') );
	}
	//html_tbl_print_header( lang_get('delete') );
	print"</tr>". NEWLINE;

	$test_run_id		= $test_results_details[TEST_RESULTS_TS_UNIQUE_RUN_ID];
	$test_name			= $test_results_details[TEST_RESULTS_TEST_SUITE];
	$test_id			= $test_results_details[TEST_RESULTS_TEMPEST_TEST_ID];
	$time_started		= $test_results_details[TEST_RESULTS_TIME_STARTED];
	$time_finished		= $test_results_details[TEST_RESULTS_TIME_FINISHED];
	$finished			= $test_results_details[TEST_RESULTS_FINISHED];
	$machine_name		= $test_results_details[TEST_RESULTS_MACHINE_NAME];
	$os					= $test_results_details[TEST_RESULTS_OS];
	$env				= $test_results_details[TEST_RESULTS_ENVIRONMENT];
	$cvs				= $test_results_details[TEST_RESULTS_CVS_VERSION];

	print"<tr>". NEWLINE;
	print"<td class='tbl-c'>$test_run_id</td>". NEWLINE;
	print"<td class='tbl-c'>$test_name</td>". NEWLINE;
	//print"<td class='tbl-c'>$time_started</td>". NEWLINE;
	//print"<td class='tbl-c'>$time_finished</td>". NEWLINE;
	//print"<td class='tbl-c'>$finished</td>". NEWLINE;
	//print"<td class='tbl-c'>$machine_name</td>". NEWLINE;
	//print"<td class='tbl-c'>$os</td>". NEWLINE;
	//print"<td class='tbl-c'>$env</td>". NEWLINE;
	print"<td class='tbl-c'>$cvs</td>". NEWLINE;
	if(!$locked){
		print"<td class='tbl-c'><a href='$row_results_pass_test_run?test_id=$test_id&amp;testset_id=$testset_id'>". lang_get('pass') ."</a></td>". NEWLINE;
		print"<td class='tbl-c'><a href='$row_results_fail_test_run?test_id=$test_id&amp;testset_id=$testset_id'>". lang_get('fail') ."</a></td>". NEWLINE;
		print"<td class='tbl-c'><a href='$row_results_add_test_run_comment?test_id=$test_id&amp;testset_id=$testset_id'>". lang_get('comment') ."</a></td>". NEWLINE;
		print"<td class='tbl-c'><a href='results_continue_manual_test_page.php?test_run_id=$test_run_id&testset_id=$testset_id&test_id=$test_id'>". lang_get("continue_test_run")."</a></td>". NEWLINE;
	}
	/*
	print"<td class='tbl-c'>". NEWLINE;
	print"<form method=post action='$delete_page'>". NEWLINE;
	print"<input type='submit' value='". lang_get('delete') ."' class='page-numbers' >". NEWLINE;
		print"<input type='hidden' name='r_page' value='$page'>";
		print"<input type='hidden' name='f' value='results_delete_test_run'>";
		print"<input type='hidden' name='id' value='$test_run_id'>";
		print"<input type='hidden' name='msg' value='10'>";
	print"</form>". NEWLINE;
	print"</td>". NEWLINE;
	*/
	//print"<td class='tbl-c'><a href='$row_results_delete_test_run?r_page=$page&amp;f=results_delete_test_run&amp;id=$test_run_id&amp;msg=10'>". lang_get('delete') ."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;

	print"</table>". NEWLINE;

}

print"<br><br>". NEWLINE;

# get project preferences that determine what fields we do and don't show for each project
$s_show_options = session_get_show_options();
$show_custom_1		= $s_show_options['show_custom_1'];
$show_custom_2		= $s_show_options['show_custom_2'];
$show_custom_3		= $s_show_options['show_custom_3'];
$show_custom_4		= $s_show_options['show_custom_4'];
$show_custom_5		= $s_show_options['show_custom_5'];
$show_custom_6		= $s_show_options['show_custom_6'];
$show_window		= $s_show_options['show_window'];
$show_object		= $s_show_options['show_object'];
$show_memory_stats	= $s_show_options['show_memory_stats'];

print"<table class='hide100'><tr><td class='tbl-c'><a href='$csv_export_page'>";
if( IMPORT_EXPORT_TO_EXCEL ) {
	print lang_get('excel_export');
} 
else {
	print lang_get('csv_export');
}
print"</a></td></tr></table>". NEWLINE;
$rows_verify_details = results_get_verify_results_detail( $test_run_id );

if( !empty( $rows_verify_details ) ) {

	print"<table class=width100 rules=cols>". NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;
	html_tbl_print_header( lang_get('step_no') );
	html_tbl_print_header( lang_get('action') );
	html_tbl_print_header( lang_get('expected_result') );
	html_tbl_print_header( lang_get('actual_result') );
	html_tbl_print_header( lang_get('status') );
	print"<th></th>". NEWLINE;
	if(!$locked){
		html_tbl_print_header( lang_get('edit') );
	}
	html_tbl_print_header( lang_get('defect') );
	if( $show_custom_1 == 'Y' ) {
		html_tbl_print_header( lang_get('show_custom_1') );
	}
	if( $show_custom_2 == 'Y' ) {
		html_tbl_print_header( lang_get('show_custom_2') );
	}
	if( $show_custom_3 == 'Y' ) {
		html_tbl_print_header( lang_get('show_custom_3') );
	}
	if( $show_custom_4 == 'Y' ) {
		html_tbl_print_header( lang_get('show_custom_4') );
	}
	if( $show_custom_5 == 'Y' ) {
		html_tbl_print_header( lang_get('show_custom_5') );
	}
	if( $show_custom_6 == 'Y' ) {
		html_tbl_print_header( lang_get('show_custom_6') );
	}
	html_tbl_print_header( lang_get('time_tested') );
	html_tbl_print_header( lang_get('window') );
	html_tbl_print_header( lang_get('object') );
	html_tbl_print_header( lang_get('line_no') );
	if( $show_memory_stats == 'Y' ) {
		html_tbl_print_header( lang_get('tot_phy_mem') );
		html_tbl_print_header( lang_get('free_phy_mem') );
		html_tbl_print_header( lang_get('tot_vir_mem') );
		html_tbl_print_header( lang_get('free_vir_mem') );
		html_tbl_print_header( lang_get('cur_mem_util') );
		html_tbl_print_header( lang_get('tot_page_file') );
		html_tbl_print_header( lang_get('free_page_file') );
	}
	print"</tr>". NEWLINE;

	foreach( $rows_verify_details as $row_result ) {

		$vr_id				= $row_result[VERIFY_RESULTS_ID];
		$step_no			= $row_result[VERIFY_RESULTS_VAL_ID];
		$custom_1			= $row_result[VERIFY_RESULTS_SHOW_CUSTOM_1];
		$custom_2			= $row_result[VERIFY_RESULTS_SHOW_CUSTOM_2];
		$custom_3			= $row_result[VERIFY_RESULTS_SHOW_CUSTOM_3];
		$custom_4			= $row_result[VERIFY_RESULTS_SHOW_CUSTOM_4];
		$custom_5			= $row_result[VERIFY_RESULTS_SHOW_CUSTOM_5];
		$custom_6			= $row_result[VERIFY_RESULTS_SHOW_CUSTOM_6];
		$timestamp			= $row_result[VERIFY_RESULTS_TIMESTAMP];
		$action				= $row_result[VERIFY_RESULTS_ACTION];
		$expected_result	= $row_result[VERIFY_RESULTS_EXPECTED_RESULT];
		$actual_result		= $row_result[VERIFY_RESULTS_ACTUAL_RESULT];
		$status				= $row_result[VERIFY_RESULTS_TEST_STATUS];
		$window				= $row_result[VERIFY_RESULTS_WINDOW];
		$object				= $row_result[VERIFY_RESULTS_OBJ];
		$line_no			= $row_result[VERIFY_RESULTS_LINE_NUMBER];
		$tot_phy_mem		= $row_result[VERIFY_RESULTS_TOTAL_PHY_MEM];
		$free_phy_mem		= $row_result[VERIFY_RESULTS_FREE_PHY_MEM];
		$tot_vir_mem		= $row_result[VERIFY_RESULTS_TOTAL_VIR_MEM];
		$free_vir_mem		= $row_result[VERIFY_RESULTS_FREE_VIR_MEM];
		$cur_mem_util		= $row_result[VERIFY_RESULTS_CUR_MEM_UTIL];
		$tot_page_file		= $row_result[VERIFY_RESULTS_TOTAL_PAGE_FILE];
		$free_page_file		= $row_result[VERIFY_RESULTS_FREE_PAGE_FILE];
		$comment			= $row_result[VERIFY_RESULTS_COMMENT];
		$defect_id			= $row_result[VERIFY_RESULTS_DEFECT_ID];

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		print"<td class='tbl-c'>$step_no</td>". NEWLINE;
		//if( $show_testcase == 'Y' ) {
			//print"<td class='tbl-c'>$tc_name</td>". NEWLINE;
		//}
		print"<td class='tbl-l'>$action</td>". NEWLINE;
		print"<td class='tbl-l'>$expected_result</td>". NEWLINE;
		# actual result & comments
		if( !empty( $comment ) ) {
			$comment = "<br><u><i><font color=#FF0000>". lang_get('comments') ."</u></i><br><i>$comment</font></i>". NEWLINE;
		} else {
			$comment = "";
		}
		print"<td class='tbl-l'>$actual_result $comment</td>". NEWLINE;
		print"<td class='tbl-c'>$status</td>". NEWLINE;
		print results_verfication_status_icon( $status );
		if(!$locked){
			print"<td class='tbl-c'><a href='$row_results_update_verification?verify_id=$vr_id&amp;test_run_id=$test_run_id'>". lang_get( 'edit' ) ."</a></td>". NEWLINE;
		}
		#--------------------------------------------------------------------------
		# Link to bug tracker.  RTH or MANTIS
		#--------------------------------------------------------------------------
		if(  empty($defect_id) ) { # display a link allowing users to create a defect

			print"<td class='tbl-c'><a href='". REPORT_BUG_URL ."?test_run_id=$test_run_id&verify_id=$vr_id'";
			/*  Add this code if you want another bugtracker to open in a new window
			if( BUGTRACKER != 'rth' ) { 
				print" target='new'";
			}
			*/
			print">". lang_get( 'defect_link' ) ."</a></td>". NEWLINE; # target='new'
		
		}
		else { # display a link to the defect id

			$padded_defect_id = util_pad_id( $defect_id );

			//print"<td class='tbl-c'><a href='$view_defect_page?bug_id=$defect_id'>$padded_defect_id</a></td>". NEWLINE; #target='new'
			print"<td class='tbl-c'><a href='". VIEW_BUG_URL ."?bug_id=$defect_id&id=$defect_id'";
			/*  Add this code if you want another bugtracker to open in a new window
			if( BUGTRACKER != 'rth' ) { 
				print" target='new'";
			}
			*/
			print">$padded_defect_id</a></td>". NEWLINE; #target='new'
		}
		if( $show_custom_1 == 'Y' ) {
			print"<td class='tbl-c'>$custom_1</td>". NEWLINE;
		}
		if( $show_custom_2 == 'Y' ) {
			print"<td class='tbl-c'>$custom_2</td>". NEWLINE;
		}
		if( $show_custom_3 == 'Y' ) {
			print"<td class='tbl-c'>$custom_3</td>". NEWLINE;
		}
		if( $show_custom_4 == 'Y' ) {
			print"<td class='tbl-c'>$custom_4</td>". NEWLINE;
		}
		if( $show_custom_5 == 'Y' ) {
			print"<td class='tbl-c'>$custom_5</td>". NEWLINE;
		}
		if( $show_custom_6 == 'Y' ) {
			print"<td class='tbl-c'>$custom_6</td>". NEWLINE;
		}
		print"<td class='tbl-c'>$timestamp</td>". NEWLINE;
		print"<td class='tbl-c'>$window</td>". NEWLINE;
		print"<td class='tbl-c'>$object</td>". NEWLINE;
		print"<td class='tbl-c'>$line_no</td>". NEWLINE;

		if( $show_memory_stats == 'Y' ) {
			print"<td class='tbl-c'>". results_format_memory_stats( $tot_phy_mem) ."</td>". NEWLINE;
			print"<td class='tbl-c'>". results_format_memory_stats( $free_phy_mem ) ."</td>". NEWLINE;
			print"<td class='tbl-c'>". results_format_memory_stats( $tot_vir_mem ) ."</td>". NEWLINE;
			print"<td class='tbl-c'>". results_format_memory_stats( $free_vir_mem ) ."</td>". NEWLINE;
			print"<td class='tbl-c'>". results_format_memory_stats( $cur_mem_util ) ."</td>". NEWLINE;
			print"<td class='tbl-c'>". results_format_memory_stats( $tot_page_file ) ."</td>". NEWLINE;
			print"<td class='tbl-c'>". results_format_memory_stats( $free_page_file ) ."</td>". NEWLINE;
		}
		print"</tr>". NEWLINE;

	}
	print"</table>". NEWLINE;
} else {
	print"place no test result message here<br>". NEWLINE;
}


$test_suite_name = test_get_name($test_id);

print"<br>";
if(!$locked){
	print"<FORM ENCTYPE=multipart/form-data ACTION='results_upload_test_run_file.php' METHOD=post onSubmit='return validatefilename()'>". NEWLINE;
	print"<input type=hidden name=test_id 		VALUE=$test_id>". NEWLINE;
	print"<input type=hidden name=testset_id 	VALUE=$testset_id>". NEWLINE;
	print"<input type=hidden name=MAX_FILE_SIZE VALUE=5000000>". NEWLINE;
	print"<input type=hidden name=test_run_id 	VALUE=$test_run_id>". NEWLINE;
	
	print"<table class=width70>". NEWLINE;
	print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
			print"<table class=inner>". NEWLINE;
	
			# Title
			print"<tr><td colspan=2 align=left><h4>".lang_get('upload_doc_test_run')."</h4></td></tr>". NEWLINE;
	
			# uploadfile -->
			print"<tr>". NEWLINE;
			print"<td  class='form-lbl-r'>".lang_get('upload_file')."</td>". NEWLINE;
			print"<td  class='form-data-l'><input type=file name=upload_file size=60></td>". NEWLINE;
			print"</tr>". NEWLINE;
	
			# Comments -->
			print"<tr>". NEWLINE;
			print"<td class='form-lbl-r'>".lang_get('comments')."</td>". NEWLINE;
			print"<td class='form-data-l'><textarea rows='3' cols='45' name='comments'></textarea></td>". NEWLINE;
			print"</tr>". NEWLINE;
	
			# buttons -->
			print"<tr>". NEWLINE;
			print"<td COLSPAN=2><input type=submit value='".lang_get('upload')."'></td>". NEWLINE;
			print"</tr>". NEWLINE;
	
			print"</table>". NEWLINE;
			print"</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"</table>". NEWLINE;
	
			print"</FORM>". NEWLINE;
}
print("<br>");

$run_doc_tbl		= INDIV_RUN_DOCS_TBL;
$f_ts_unique_id		= INDIV_RUN_DOCS_TS_UNIQUE_RUN_ID;
$f_unique_doc_id	= INDIV_RUN_DOCS_UNIQUE_ID;
$f_timestamp		= INDIV_RUN_DOCS_TIMESTAMP;
$f_uploaded_by		= INDIV_RUN_DOCS_UPLOADED_BY;
$f_filename			= INDIV_RUN_DOCS_FILE_NAME;
$f_display_name		= INDIV_RUN_DOCS_DISPLAY_NAME;
$f_comments			= INDIV_RUN_DOCS_COMMENTS;

$q = "SELECT $f_unique_doc_id, $f_timestamp, $f_uploaded_by, $f_filename, $f_display_name, $f_comments 
	 FROM $run_doc_tbl
	 WHERE $f_ts_unique_id = '$test_run_id'";
$rs = $db->Execute($q);
$num_rows = $rs->NumRows();

if( $num_rows ) {
	print"<TABLE RULES=COLS class=width100>". NEWLINE;
	print"<tr>". NEWLINE;
	html_tbl_print_header( lang_get('file_type') );
	html_tbl_print_header( lang_get('file_name') );
	html_tbl_print_header( lang_get('view') );
	html_tbl_print_header( lang_get('download') );
	html_tbl_print_header( lang_get('uploaded_by') );
	html_tbl_print_header( lang_get('date_added') );
	html_tbl_print_header( lang_get('info') );
	if(!$locked){
		html_tbl_print_header( lang_get('delete') );
	}
	print"</tr>". NEWLINE;

	while($row = $rs->FetchRow()) {

		if(substr($row['FileName'], 0, 4) != 'Link') {

			$row_style = html_tbl_alternate_bgcolor( $row_style );

			$project_properties		= session_get_project_properties();
			$file_path				= $project_properties['test_run_upload_path'];

			$fname = $file_path . $row['DisplayName'];

			//Gets the extension of the file and loads the image for that type
			$fileType = substr($row['DisplayName'], -4);
			
			$doc_id = $row['UniqueDocID'];

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>".html_file_type($fileType)."</td>". NEWLINE;
			print"<td>$row[FileName]</td>". NEWLINE;
			print"<td><A HREF='$fname' target='new'>View</A></td>". NEWLINE;
			print"<td><A HREF='download.php?upload_filename=$fname'>Download</A></td>". NEWLINE;
			print"<td>$row[UploadedBy]</td>". NEWLINE;
			print"<td WIDTH='160'>$row[TimeStamp]</td>". NEWLINE;
			print"<td WIDTH='160'>$row[Comments]</td>". NEWLINE;
			#print"<td><a href='delete_individual_run_doc.php?test_run_id=$test_run_id&test_id=$test_id&testset_id=$testset_id&filename=$row[FileName]&displayname=$row[DisplayName]'>Delete</a></font></td>". NEWLINE;
			if(!$locked){
				print"<td>". NEWLINE;
					print"<form name='delete_uploaded_document' method=post action='$delete_page'>". NEWLINE;
						print"<input type='submit' name='delete_uploaded_testrun_document' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
						print"<input type='hidden' name='r_page' value='$redirect_url#'>". NEWLINE;
						print"<input type='hidden' name='f' value='delete_uploaded_testrun_document'>". NEWLINE;
						print"<input type='hidden' name='id' value='$doc_id'>". NEWLINE;
						print"<input type='hidden' name='project_id' value='$project_id'>". NEWLINE;
						print"<input type='hidden' name='msg' value='". DEL_TEST_RUN_DOC ."'>". NEWLINE;
						
					print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
			
		}
	}

	print"</table>". NEWLINE;
}

print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: results_view_verifications_page.php,v $
# Revision 1.12  2008/07/25 09:50:02  peter_thal
# added lock testset feature
# disabled detail column in test result, because functionality is not implemented yet
#
# Revision 1.11  2008/04/23 06:31:29  cryobean
# *** empty log message ***
#
# Revision 1.10  2006/09/27 06:09:56  gth2
# removing delete test run functionality - gth
#
# Revision 1.9  2006/09/27 05:34:28  gth2
# adding Mantis integration - gth
#
# Revision 1.8  2006/09/25 12:46:39  gth2
# Working on linking rth and other bugtrackers - gth
#
# Revision 1.7  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.6  2006/06/24 18:06:50  gth2
# no message
#
# Revision 1.5  2006/06/24 14:34:14  gth2
# updating changes lost with cvs problem.
#
# Revision 1.4  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.3  2006/01/20 02:36:05  gth2
# enable export to excel functionaltiy - gth
#
# Revision 1.2  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>