<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Page
#
# $RCSfile: test_page.php,v $  $Revision: 1.10 $
# ---------------------------------------------------------------------

if( isset($_POST['mass_update_btn']) ) {

	require_once("test_group_action_page.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'test_action.php';
$detail_page			= 'test_detail_page.php';
$test_update_url		= 'test_detail_update_page.php';
$num                    = 0;
$bg_color               = '';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];


$s_display_options 		= session_set_display_options("test", $_POST);
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
$filter_search			= $s_display_options['filter']['test_search'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('test_page') );
html_page_header( $db, $project_name );
html_print_menu();
test_menu_print ($page);

error_report_check( $_GET );

print"<br>";

print"<form method='post' action='$page' name='tests_form' id='form_order'>". NEWLINE;

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
							$filter_search);

print"<br>". NEWLINE;

$g_timer->mark_time( "Load rows to display on page from db into memory" );

$row = test_filter_rows(	$project_id,
							$filter_manual_auto,
							$filter_ba_owner,
							$filter_qa_owner,
							$filter_tester,
							$filter_test_type,
							$filter_area_tested,
							$filter_test_status,
							$filter_priority,
							$filter_per_page,
							$filter_search,
							$order_by,
							$order_dir,
							$page_number,
							$csv_name="tests");

print"</div>". NEWLINE;

$g_timer->mark_time( "Finished load rows to display on page from db into memory" );
if( $row ) {
	print"<div align=center>". NEWLINE;

	#print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
	print"<table class='sortable' rules=cols>". NEWLINE;
	print"<thead>".NEWLINE;
	print"<tr class=tbl_header>". NEWLINE;
	print"<th class='unsortable'></th>". NEWLINE;
	print"<th class='unsortable'></th>". NEWLINE;
	# html_tbl_print_header( lang_get('test_id'),		TEST_ID,			$order_by, $order_dir );
	# html_tbl_print_header( lang_get('man_auto') );
	# html_tbl_print_header( lang_get('file_type') );
	# html_tbl_print_header( lang_get('autopass'),	TEST_AUTO_PASS,		$order_by, $order_dir );
	# html_tbl_print_header( lang_get('test_name'),	TEST_NAME,			$order_by, $order_dir );
	# html_tbl_print_header( lang_get('ba_owner'), 	TEST_BA_OWNER,		$order_by, $order_dir );
	# html_tbl_print_header( lang_get('qa_owner'), 	TEST_QA_OWNER,		$order_by, $order_dir );
	# html_tbl_print_header( lang_get('tester'),		TEST_TESTER,		$order_by, $order_dir );
	# html_tbl_print_header( lang_get('testtype'), 	TEST_TESTTYPE,		$order_by, $order_dir );
	# html_tbl_print_header( lang_get('area_tested'), TEST_AREA_TESTED,	$order_by, $order_dir );
	# html_tbl_print_header( lang_get('test_status'), TEST_STATUS,		$order_by, $order_dir );
	# html_tbl_print_header( lang_get('priority'),    TEST_PRIORITY,	    $order_by, $order_dir );
	
	html_tbl_print_sortable_header( lang_get('test_id'),TEST_ID,$order_by,$order_dir );
	html_tbl_print_sortable_header( lang_get('man_auto') );
	html_tbl_print_sortable_header( lang_get('file_type') );
	html_tbl_print_sortable_header( lang_get('autopass'),TEST_AUTO_PASS,$order_by,$order_dir );
	html_tbl_print_sortable_header( lang_get('test_name'),TEST_NAME,$order_by,$order_dir );
	html_tbl_print_sortable_header( lang_get('ba_owner'),TEST_BA_OWNER,$order_by,$order_dir );
	html_tbl_print_sortable_header( lang_get('qa_owner'),TEST_QA_OWNER,$order_by,$order_dir );
	html_tbl_print_sortable_header( lang_get('tester'),TEST_TESTER,$order_by,$order_dir );
	html_tbl_print_sortable_header( lang_get('testtype'),TEST_TESTTYPE,$order_by,$order_dir );
	html_tbl_print_sortable_header( lang_get('area_tested'),TEST_AREA_TESTED,$order_by,$order_dir );
	html_tbl_print_sortable_header( lang_get('test_status'),TEST_STATUS,$order_by,$order_dir );
	html_tbl_print_sortable_header( lang_get('priority'),TEST_PRIORITY,$order_by,$order_dir );
	print"</tr>". NEWLINE;

	$g_timer->mark_time( "Outputting main html table to browser" );

	$row_style = '';
	print"</thead>".NEWLINE;
	print"<tbody>".NEWLINE;
	foreach( $row as $row_test_detail ) {

		$test_id         = $row_test_detail[TEST_ID];
		//$test_version_id = $row_test_detail[TEST_VERS_ID];
		$test_name       = $row_test_detail[TEST_NAME];
		$ba_owner        = $row_test_detail[TEST_BA_OWNER];
		$qa_owner        = $row_test_detail[TEST_QA_OWNER];
		$tester			 = $row_test_detail[TEST_TESTER];
		$test_type       = $row_test_detail[TEST_TESTTYPE];
		$manual          = $row_test_detail[TEST_MANUAL];
		$automated       = $row_test_detail[TEST_AUTOMATED];
		$area_tested     = $row_test_detail[TEST_AREA_TESTED];
		$autopass        = $row_test_detail[TEST_AUTO_PASS];
		$test_status     = $row_test_detail[TEST_STATUS];
		$priority		 = $row_test_detail[TEST_PRIORITY];

		$display_test_id = util_pad_id($test_id);

		if($row_test_detail[TEST_AUTO_PASS]=="Y") {

			$autopass = "Yes";
		} else {

			$autopass = "No";
		}

		$filename = test_get_filename($test_id);

		#$row_style = html_tbl_alternate_bgcolor($row_style);
		#print"<tr class='$row_style'>". NEWLINE;
		$row_style =html_tbl_alternate_bgcolor($row_style);
		print"<tr class='$row_style'>". NEWLINE;
		print"<td><input type='checkbox' name='row_test_arr[{$test_id}]'></td>". NEWLINE;
		print"<td class='tbl-c'><a href='$test_update_url?test_id=$test_id'><img src='".IMG_SRC."update.gif' title='". lang_get('update_test') ."' border=0></a></td>". NEWLINE;

		//$detail_url = $detail_page ."?test_id=". $test_id ."&test_version_id=". $test_version_id;
		$detail_url = $detail_page ."?test_id=". $test_id ."&project_id=". $project_id;

		print"<td class='tbl-c'><a href='$detail_url' title='". lang_get('test_view_detail') ."'>$display_test_id</a></td>". NEWLINE;
		print"<td class='tbl-c'>".html_print_testtype_icon( $manual, $automated )."</td>". NEWLINE;
		print"<td class='tbl-c'>".html_file_type( $filename )."</td>". NEWLINE;
		print"<td class='tbl-l'>$autopass</td>". NEWLINE;
		print"<td class='tbl-l'>$test_name</td>". NEWLINE;
		print"<td class='tbl-l'>$ba_owner</td>". NEWLINE;
		print"<td class='tbl-l'>$qa_owner</td>". NEWLINE;
		print"<td class='tbl-l'>$tester</td>". NEWLINE;
		print"<td class='tbl-l'>$test_type</td>". NEWLINE;
		print"<td class='tbl-l'>$area_tested</td>". NEWLINE;
		print"<td class='tbl-l'>$test_status</td>". NEWLINE;
		print"<td class='tbl-l'>$priority</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	$g_timer->mark_time( "Finished outputting main html table to browser" );

	print"</tbody>".NEWLINE;
	print"</table>". NEWLINE;
	print"</div>". NEWLINE;


	if( session_use_javascript() ) {
		//print"<input type=checkbox name=all_tests value=all onClick=checkall('tests_form', this.form.all_tests.checked)\">Select All&nbsp;&nbsp;";
		print"<input id=select_all type=checkbox onClick='checkAll( this )'>". NEWLINE;
		print"<label for=select_all>".lang_get("select_all")."</label>". NEWLINE;
	}
	print"<select name=action>";
	print"<option value=man_auto>". lang_get('man_auto') ."</option>";
	print"<option value=ba_owner>". lang_get('ba_owner') ."</option>";
	print"<option value=qa_owner>". lang_get('qa_owner') ."</option>";
	print"<option value=tester>". lang_get('tester') ."</option>";
	print"<option value=test_status>". lang_get('test_status') ."</option>";
	print"<option value=test_priority>". lang_get('priority') ."</option>";
	print"<option value=auto_pass>". lang_get('autopass') ."</option>";
	print"<option value=test_type>". lang_get('testtype') ."</option>";
	print"<option value=area_tested>". lang_get('area_tested') ."</option>";
	print"<option value=email_ba_owner>". lang_get('email_ba_owner') ."</option>";
	print"<option value=email_qa_owner>". lang_get('email_qa_owner') ."</option>";
	print"</select>";
	print"&nbsp;";
	print"<input type=submit name=mass_update_btn value='".lang_get("update")."'>";

} else {

	html_no_records_found_message( lang_get('no_tests') );
}

print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_page.php,v $
# Revision 1.10  2008/08/04 06:54:58  peter_thal
# added sorting function to several tables
#
# Revision 1.9  2008/07/09 07:13:26  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.8  2008/01/22 08:19:15  cryobean
# bugfixes
#
# Revision 1.7  2007/11/19 08:59:00  cryobean
# bugfixes
#
# Revision 1.6  2007/11/15 12:58:48  cryobean
# bugfixes
#
# Revision 1.5  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.4  2007/02/03 10:26:19  gth2
# no message
#
# Revision 1.3  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>