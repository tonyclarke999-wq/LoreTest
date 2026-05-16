<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Add Tests Page
#
# $RCSfile: testset_add_tests_page.php,v $  $Revision: 1.7 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_button']) ) {

	require_once("testset_add_tests_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

# Session variables
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$s_release_properties 	= session_set_properties( "release", $_GET );
$release_id 			= $s_release_properties['release_id'];
$release_name 			= admin_get_release_name($release_id);
$build_id 				= $s_release_properties['build_id'];
$build_name 			= admin_get_build_name($build_id);
$testset_id 			= $s_release_properties['testset_id'];
$testset_name 			= admin_get_testset_name($testset_id);

$s_table_display_options	= session_set_display_options("testset_add_tests", $_POST);
$order_by					= $s_table_display_options['order_by'];
$order_dir					= $s_table_display_options['order_dir'];
$page_number				= $s_table_display_options['page_number'];

$filter_manual_auto			= $s_table_display_options['filter']['manual_auto'];
$filter_test_type			= $s_table_display_options['filter']['test_type'];
$filter_ba_owner			= $s_table_display_options['filter']['ba_owner'];
$filter_qa_owner			= $s_table_display_options['filter']['qa_owner'];
$filter_tester				= $s_table_display_options['filter']['tester'];
$filter_area_tested			= $s_table_display_options['filter']['area_tested'];
$filter_priority			= $s_table_display_options['filter']['priority'];
$filter_per_page			= $s_table_display_options['filter']['per_page'];
$filter_search				= $s_table_display_options['filter']['test_search'];

session_records("testset_edit");

# Page variables
$test_name	= TEST_TBL. "." .TEST_NAME;

$row_style	='';

$page		= basename(__FILE__);

# These two variables store all the records and select groups in a string.
# The string is passed in the POST when the form is submitted so
# session_set_displayed_testset_records when called can determine what records
# where available for the user to check/uncheck.
$records			= "";
$select_group		= "";

/*
# Javascript for table header links
if( session_use_javascript() ) {

	$per_page			= RECORDS_PER_PAGE_TESTSET_ADD;
} else {

	$per_page			= 0;
}
*/

html_window_title();
html_print_body();
html_page_title( $project_name." - ".lang_get("testset_add_tests_page") );
html_page_header( $db, $project_name );
html_print_menu();

html_release_map(	Array(	"release_link",
							"build_link",
							"testset_link" ) );
/*

################################################################################
# Select Tests

if( session_use_javascript() ) {

	$test_types = test_get_types( $project_id );
	print"<input type=hidden name=order_by value=''>". NEWLINE;
	print"<table class=width50>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	print"<table class=inner>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td colspan=2 class=left><h4>".lang_get("testset_select_group")."</h4></td>". NEWLINE;
	print"</tr>". NEWLINE;

	for( $i=0; $i<count($test_types); $i++ ) {

		# Build array string of select groups
		if( empty($select_group) ) {
			$select_group = "'".$test_types[$i]."'";
		} else {
			$select_group .= ", '".$test_types[$i]."'";
		}

		print"<tr>". NEWLINE;

		# Test Type
		print"<td width='50%' class=right>$test_types[$i]:</td>". NEWLINE;

		# CheckBox
		$checked = "";
		if( session_records_ischecked_group("testset_edit", $test_types[$i]) ) {
			$checked = "checked";
		}
		print"<td class=left><input type=checkbox name='allpages_".$test_types[$i]."' value='".$test_types[$i]."' onClick='javascript: checkValue( this );' $checked></td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	print"<tr>". NEWLINE;
	print"<td colspan=2>&nbsp;</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td colspan=2><input type='submit' name=submit_button value='".lang_get("create")."'></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
}

################################################################################
*/

print"<form method=post action='$page' name=joe>". NEWLINE;
print"<div align=center>". NEWLINE;

print"<br>";

html_print_tests_filter(	$project_id,
							$filter_manual_auto,
							$filter_test_type,
							$filter_ba_owner,
							$filter_qa_owner,
							$filter_tester,
							$filter_area_tested,
							$filter_test_status=null,
							$filter_priority,
							$filter_per_page,
							$filter_search);

print"<br>". NEWLINE;
/*
# Get testset records and print table offset information
$row = test_get(	$page,
					$project_id,
					$per_page,
					$order_by,
					$order_dir,
					$page_number,
					$man_auto,
					$test_type );
					*/
$row = test_filter_rows(	$project_id,
							$filter_manual_auto,
							$filter_ba_owner,
							$filter_qa_owner,
							$filter_tester,
							$filter_test_type,
							$filter_area_tested,
							$filter_test_status=null,
							$filter_priority,
							$filter_per_page,
							$filter_search,
							$order_by,
							$order_dir,
							$page_number );

################################################################################
# Testset table

if($row) {
	print"<table table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
	print"<thead>". NEWLINE;
	# Table headers
	print"<tr class=tbl_header>". NEWLINE;

	print"<th class='unsortable'></th>". NEWLINE;
	#html_tbl_print_header( lang_get('test_id'),		TEST_ID,			$order_by, $order_dir );
	#html_tbl_print_header( lang_get('man_auto') );
	#html_tbl_print_header( lang_get('autopass'),	TEST_AUTO_PASS,		$order_by, $order_dir );
	#html_tbl_print_header( lang_get('test_name'),	TEST_NAME,			$order_by, $order_dir );
	#html_tbl_print_header( lang_get('ba_owner'), 	TEST_BA_OWNER,		$order_by, $order_dir );
	#html_tbl_print_header( lang_get('qa_owner'), 	TEST_QA_OWNER,		$order_by, $order_dir );
	#html_tbl_print_header( lang_get('tester'),  	TEST_TESTER,		$order_by, $order_dir );
	#html_tbl_print_header( lang_get('testtype'), 	TEST_TESTTYPE,		$order_by, $order_dir );
	#html_tbl_print_header( lang_get('area_tested'), TEST_AREA_TESTED,	$order_by, $order_dir );
	#html_tbl_print_header( lang_get('priority'),    TEST_PRIORITY,	    $order_by, $order_dir );
	
	html_tbl_print_header( lang_get('test_id') );
	html_tbl_print_header( lang_get('man_auto') );
	html_tbl_print_header( lang_get('autopass') );
	html_tbl_print_header( lang_get('test_name') );
	html_tbl_print_header( lang_get('ba_owner') );
	html_tbl_print_header( lang_get('qa_owner') );
	html_tbl_print_header( lang_get('tester') );
	html_tbl_print_header( lang_get('testtype') );
	html_tbl_print_header( lang_get('area_tested') );
	html_tbl_print_header( lang_get('priority') );
	print"</tr>". NEWLINE;
	print"</thead>".NEWLINE;
	print"<tbody>".NEWLINE;
	foreach($row as $row_test_detail) {

		$test_id         = $row_test_detail[TEST_ID];
		$test_name       = $row_test_detail[TEST_NAME];
		$ba_owner        = $row_test_detail[TEST_BA_OWNER];
		$qa_owner        = $row_test_detail[TEST_QA_OWNER];
		$tester	         = $row_test_detail[TEST_TESTER];
		$test_type       = $row_test_detail[TEST_TESTTYPE];
		$manual          = $row_test_detail[TEST_MANUAL];
		$automated       = $row_test_detail[TEST_AUTOMATED];
		$area_tested     = $row_test_detail[TEST_AREA_TESTED];
		$autopass        = $row_test_detail[TEST_AUTO_PASS];
		$priority		 = $row_test_detail[TEST_PRIORITY];


		$display_test_id = util_pad_id($test_id);

		#$row_style = html_tbl_alternate_bgcolor($row_style);

		if($row_test_detail[TEST_AUTO_PASS]=="Y") {

			$autopass = "Yes";
		} else {

			$autopass = "No";
		}

		# Build list of records
		if( empty($records) ) {
			$records = $test_id." => '".$test_type."'";
		} else {
			$records .= ", ".$test_id." => '".$test_type."'";
		}

		# Rows
		#print"<tr class='$row_style'>". NEWLINE;
		print"<tr>". NEWLINE;
		if( session_records_ischecked("testset_edit", $test_id, $test_type) ) {
			print"<td><input type=checkbox name=row_".$test_id." value='".$test_type."' checked></td>". NEWLINE;
		} else {
			print"<td><input type=checkbox name=row_".$test_id." value='".$test_type."'></td>". NEWLINE;
		}
		print"<td class='left'>$display_test_id</td>". NEWLINE;
		print"<td class='tbl-l'>".html_print_testtype_icon($manual, $automated)."</td>". NEWLINE;
		print"<td class='tbl-l'>$autopass</td>". NEWLINE;
		print"<td class='tbl-l'>$test_name</td>". NEWLINE;
		print"<td class='tbl-l'>$ba_owner</td>". NEWLINE;
		print"<td class='tbl-l'>$qa_owner</td>". NEWLINE;
		print"<td class='tbl-l'>$tester</td>". NEWLINE;
		print"<td class='tbl-l'>$test_type</td>". NEWLINE;
		print"<td class='tbl-l'>$area_tested</td>". NEWLINE;
		print"<td class='tbl-l'>$priority</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}
	print"</tbody>".NEWLINE;
	print"</table>". NEWLINE;
	print"</div>". NEWLINE;

	if( session_use_javascript() ) {

		print"<input id=select_all type=checkbox name=thispage onClick='checkAll( this )'>". NEWLINE;
		print"<label for=select_all>".lang_get("select_all")."</label>";
	}

	print"<br>". NEWLINE;

	################################################################################
	print"<div align=center>". NEWLINE;
	print"<input type='submit' name=submit_button value='".lang_get("create")."'>". NEWLINE;

} else {

	print lang_get('no_tests');
}
print"<input type=hidden name=records value=\"$records\">". NEWLINE;
print"<input type='hidden' name='record_groups' value=\"$select_group\">". NEWLINE;

print"</div>". NEWLINE;
print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: testset_add_tests_page.php,v $
# Revision 1.7  2008/07/18 07:43:36  peter_thal
# fixed search filter bug in some testset php pages
#
# Revision 1.6  2008/01/22 08:20:59  cryobean
# made the table sortable
#
# Revision 1.5  2007/02/03 10:26:19  gth2
# no message
#
# Revision 1.4  2007/02/02 04:27:31  gth2
# correcting error with records per page when adding tests to a test set - gth
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