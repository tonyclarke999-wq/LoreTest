<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# --------------------------------------------------
# Testset viewlast page
#
# $RCSfile: testset_viewlast_page.php,v $ $Revision: 1.3 $
# --------------------------------------------------

include_once('./api/include_api.php');

auth_authenticate_user();

$page           		= basename(__FILE__);
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$row_style              = '';

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];

$test_detail_page		= 'test_detail_page.php';
$req_detail_page		= 'requirement_detail_page.php';
$results_page			= 'results_page.php';
$testsets_page			= 'testset_viewlast_page.php';

$s_display_options 		= session_set_display_options("testset", $_POST);
$order_by				= $s_display_options['order_by'];
$order_dir				= $s_display_options['order_dir'];
$page_number			= $s_display_options['page_number'];

$build_name				= $s_display_options['filter']['build_name'];
$release_name			= $s_display_options['filter']['release_name'];
$per_page				= $s_display_options['filter']['per_page'];



html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('testsets_status_page') );
html_page_header( $db, $project_name );
html_print_menu();

error_report_check( $_GET );

print"<br>";

print"<form method='post' action='$page' name='testset_form' id='form_order'>". NEWLINE;

print"<div align=center>". NEWLINE;

html_print_testsets_filter($project_id,	
							$build_name, 
							$release_name,
							$per_page);

print"<br>". NEWLINE;

$release_id		= admin_get_max_release_id_from_build_tbl( $project_id );
$build_id		= admin_get_max_build_id( $release_id );
$testset_id		= admin_get_max_testset( $build_id );
$statuses		= results_get_teststatus_by_project( $project_id );
#$results_url	= $results_page ."?release_id=$release_id&amp;build_id=$build_id&amp;testset_id=$testset_id";

$rows_testsets = testset_filter_row($project_id, $build_name, $release_name, $per_page, $order_by, $order_dir, $page_number);
#$rows_testsets = testset_get_last($project_id);

if($rows_testsets) {

	print"<table align=center rules=cols class=width95>". NEWLINE;
	print"<tr>". NEWLINE;
	html_tbl_print_sortable_header( lang_get('testset_id'),TS_TBL.".".TS_ID,$order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('testset_name'), TS_TBL.".".TS_NAME,$order_by, $order_dir);
	html_tbl_print_sortable_header( lang_get('build_name'), BUILD_TBL.".".BUILD_NAME,$order_by, $order_dir);
	html_tbl_print_sortable_header( lang_get('release_name'), RELEASE_TBL.".".RELEASE_NAME,$order_by, $order_dir);
	html_tbl_print_sortable_header( lang_get('tests') );
	# Display table headers based on the statuses of the project
	foreach( $statuses as $status ) {
		html_tbl_print_header( $status );
	}
	html_tbl_print_sortable_header( lang_get('testset_date_received'),TS_TBL.".".TS_DATE_CREATED ,$order_by, $order_dir);
	print"</tr>". NEWLINE;
	
	foreach($rows_testsets as $row_testset) {
	
		$testset_id	= $row_testset[TS_ID];
		$num_tests = admin_count_tests_in_testset( $testset_id );
		#$testset_name = admin_get_testset_name( $testset_id );
		$testset_name = $row_testset[TS_NAME];
		$testset_build_id = admin_get_build_id_from_testset_id( $testset_id );
		#$testset_build_name = admin_get_build_name($testset_build_id);
		$testset_build_name  = $row_testset[BUILD_NAME];
		$testset_release_id = admin_get_release_id_from_build_id( $testset_build_id ); 
		#$testset_release_name = admin_get_release_name($testset_release_id);
		$testset_release_name = $row_testset[RELEASE_NAME];
		$testset_date_created = $row_testset[TS_DATE_CREATED];

		$display_test_id = util_pad_id( $testset_id );
		$results_url = $results_page ."?release_id=$testset_release_id&amp;build_id=$testset_build_id&amp;testset_id=$testset_id";

		$row_style = html_tbl_alternate_bgcolor($row_style);
		print"<tr class=$row_style>". NEWLINE;
		if ($num_tests>0) {
		print"<td class='tbl-data-c'><a href='$results_url'>$display_test_id</a></td>";
		}
		else {
		print"<td class='tbl-data-c'>$display_test_id</td>";
		}
		print"<td class='tbl-data-c'>$testset_name</td>";
		print"<td class='tbl-data-c'>$testset_build_name</td>";
		print"<td class='tbl-data-c'>$testset_release_name</td>";
		print"<td class='tbl-data-c'>$num_tests</td>";
		# Display table data based on the statuses of the project as well
		foreach( $statuses as $status ) {
			$num = results_get_num_tests_by_status( $testset_id, $status );
			if($status == 'Failed' && $num > 0){
				print"<td class='tbl-data-c'>$num<img src='./images/sign-small.png' alt='failed'></td>";
			}else
				print"<td class='tbl-data-c'>$num</td>". NEWLINE;
		
		} 
		print"<td class='tbl-data-c'>$testset_date_created</td>";
		print"</tr>";
	}   
}
else {

	print lang_get('no_testsets_in_project');
}

print"</table>". NEWLINE;

print"</div>". NEWLINE;
print"<br>";

print"</form>". NEWLINE;

html_print_footer();


#------------------------------------
# $Log: testset_viewlast_page.php,v $
# Revision 1.3  2008/08/04 06:54:58  peter_thal
# added sorting function to several tables
#
# Revision 1.2  2008/07/24 07:41:46  peter_thal
# added supporting docs table in test run page
#
# ------------------------------------
?>
