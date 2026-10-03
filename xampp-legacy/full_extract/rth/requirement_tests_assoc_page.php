<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Test Assoc Page
#
# $RCSfile: requirement_tests_assoc_page.php,v $  $Revision: 1.5 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_btn']) ) {

	require_once("requirement_tests_assoc_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$project_name 	= session_get_project_name();
$page			= basename(__FILE__);
$row_style 		= '';
$records		= '';

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];
$s_req_version_id	= $s_properties['req_version_id'];

$filter_per_page		= 25;
$filter_manual_auto		= "";
$filter_test_type		= "";
$filter_ba_owner		= "";
$filter_qa_owner		= "";
$filter_tester			= "";
$filter_area_tested		= "";
$filter_test_status		= "";
$filter_test_priority	= "";
$filter_test_search		= "";

util_set_filter('per_page', $filter_per_page, $_POST);
util_set_filter('manual_auto', $filter_manual_auto, $_POST);
util_set_filter('test_type', $filter_test_type, $_POST);
util_set_filter('ba_owner', $filter_ba_owner, $_POST);
util_set_filter('qa_owner', $filter_qa_owner, $_POST);
util_set_filter('tester', $filter_tester, $_POST);
util_set_filter('area_tested', $filter_area_tested, $_POST);
util_set_filter('test_status', $filter_test_status, $_POST);
util_set_filter('test_priority', $filter_test_priority, $_POST);
util_set_filter('test_search', $filter_test_search, $_POST);

$order_by 		= TEST_NAME;
$order_dir		= "ASC";
$page_number	= 1;

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);

# Set or Reset the % covered session vars
if( sizeof($_POST) ) {
	session_validate_form_set($_POST);
} else {
	session_validate_form_reset();
}


$assoc_tests = requirement_get_test_relationships($s_req_id);
$selected_rows = array();

foreach($assoc_tests as $row) {

	$selected_rows[$row[TEST_ID]] = "";
}

session_records(	"requirement_tests_assoc",
					$selected_rows );

html_window_title();
html_print_body();
html_page_title($project_name ." - " . lang_get('req_assoc_tests_page') );
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);

error_report_check( $_GET );

$row_requirement = requirement_get_detail( $project_id, $s_req_id, $s_req_version_id );

foreach( $row_requirement as $row_detail ) {

		$req_name			= $row_detail[REQ_FILENAME];
		$req_version_num	= $row_detail[REQ_VERS_VERSION];
		$req_status			= $row_detail[REQ_VERS_STATUS];
		$area_covered		= $row_detail[REQ_AREA_COVERAGE];
		$req_author			= $row_detail[REQ_VERS_UPLOADED_BY];
		$req_doc_type		= $row_detail[REQ_DOC_TYPE_NAME];
		$date_created		= $row_detail[REQ_VERS_TIMESTAMP];
		$locked_by			= $row_detail[REQ_LOCKED_BY];
		$locked_date		= $row_detail[REQ_LOCKED_DATE];
		$assigned_to		= $row_detail[REQ_VERS_ASSIGNED_TO];
		$record_or_file		= $row_detail[REQ_REC_FILE];
		$req_file_name		= $row_detail[REQ_VERS_FILENAME]; # Blank if record based
		$req_detail			= $row_detail[REQ_VERS_DETAIL]; # Blank if file based

}

print"<br>". NEWLINE;

print"<form method=post action='$page'>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<table class=width100 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_id')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_name')."</td>". NEWLINE;
print"<td width='33%' nowrap class=grid-header-c>".lang_get('req_version')."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td width='33%' class=grid-data-c><a href='requirement_detail_page.php?req_id=$s_req_id&amp;req_version_id=$s_req_version_id'>".sprintf( "%05s",trim( $s_req_id ) )."</a></td>". NEWLINE;
print"<td width='33%' class=grid-data-c>$req_name</td>". NEWLINE;
print"<td width='33%' class=grid-data-c>$req_version_num</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br>";

html_print_tests_filter(	$project_id,
							$filter_manual_auto,
							$filter_test_type,
							$filter_ba_owner,
							$filter_qa_owner,
							$filter_tester,
							$filter_area_tested,
							$filter_test_status,
							$filter_test_priority,
							$filter_per_page,
							$filter_test_search
							);

print"</div>". NEWLINE;

print"<br>". NEWLINE;

$row = test_filter_rows(	$project_id,
							$filter_manual_auto,
							$filter_ba_owner,
							$filter_qa_owner,
							$filter_tester,
							$filter_test_type,
							$filter_area_tested,
							$filter_test_status,
							$filter_test_priority,
							$filter_per_page,
							$filter_test_search,
							$order_by,
							$order_dir,
							$page_number );

							
if( $row ) {
	print"<div align=center>". NEWLINE;
	print"<table class=width100 rules=cols>". NEWLINE;

	print"<tr class=tbl_header>". NEWLINE;
	print"<th></th>". NEWLINE;
	html_tbl_print_header( lang_get('percent_covered_test') );
	html_tbl_print_header( lang_get('test_id'),		TEST_ID,			$order_by, $order_dir );
	html_tbl_print_header( lang_get('man_auto') );
	html_tbl_print_header( lang_get('test_name'),	TEST_NAME,			$order_by, $order_dir );
	html_tbl_print_header( lang_get('ba_owner'), 	TEST_BA_OWNER,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('qa_owner'), 	TEST_QA_OWNER,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('tester'), 	    TEST_TESTER,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('testtype'), 	TEST_TESTTYPE,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('area_tested'), TEST_AREA_TESTED,	$order_by, $order_dir );
	html_tbl_print_header( lang_get('priority'),	TEST_PRIORITY,		$order_by, $order_dir );
	print"</tr>". NEWLINE;

	$row_style = '';

	foreach( $row as $row_test_detail ) {

		$test_id         = $row_test_detail[TEST_ID];
		$test_name       = $row_test_detail[TEST_NAME];
		$ba_owner        = $row_test_detail[TEST_BA_OWNER];
		$qa_owner        = $row_test_detail[TEST_QA_OWNER];
		$tester	         = $row_test_detail[TEST_TESTER];
		$test_type       = $row_test_detail[TEST_TESTTYPE];
		$manual          = $row_test_detail[TEST_MANUAL];
		$automated       = $row_test_detail[TEST_AUTOMATED];
		$area_tested     = $row_test_detail[TEST_AREA_TESTED];
		$priority        = $row_test_detail[TEST_PRIORITY];
		$autopass        = $row_test_detail[TEST_AUTO_PASS];

		# Name of the % covered text input
		$pc_covered_input_name = "percent_covered_$test_id";

		# Get the % covered from db
		$percent_covered = requirement_test_get_pc_covered($s_req_id, $test_id);

		# Save and get the % covered from the session
		$percent_covered = session_validate_form_get_field($pc_covered_input_name, $percent_covered);

		$display_test_id = util_pad_id($test_id);

		$row_style = html_tbl_alternate_bgcolor($row_style);

		if( session_records_ischecked("requirement_tests_assoc", $test_id) ) {

			$checked = "checked";
		} else {

			$checked = "";
		}

		# Build list of records
		if( empty($records) ) {
			$records = $test_id." => '".$test_type."'";
		} else {
			$records .= ", ".$test_id." => '".$test_type."'";
		}

		print"<tr class='$row_style'>". NEWLINE;
		print"<td><input type='checkbox' name=row_$test_id $checked></td>". NEWLINE;
		print"<td><input type='text' name=$pc_covered_input_name size=3 maxlength=3 value='$percent_covered'></td>". NEWLINE;
		print"<td class='tbl-l'>$display_test_id</td>". NEWLINE;
		print"<td class='tbl-l'>".html_print_testtype_icon( $manual, $automated )."</td>". NEWLINE;
		print"<td class='tbl-l'>$test_name</td>". NEWLINE;
		print"<td class='tbl-l'>$ba_owner</td>". NEWLINE;
		print"<td class='tbl-l'>$qa_owner</td>". NEWLINE;
		print"<td class='tbl-l'>$tester</td>". NEWLINE;
		print"<td class='tbl-l'>$test_type</td>". NEWLINE;
		print"<td class='tbl-l'>$area_tested</td>". NEWLINE;
		print"<td class='tbl-l'>$priority</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	print"</table>". NEWLINE;
	print"</div>". NEWLINE;

	if( session_use_javascript() ) {

		print"<input id=select_all type=checkbox name=thispage onClick='checkAll( this )'>". NEWLINE;
		print"<label for=select_all>".lang_get("select_all")."</label>";
	}

	print"<div align=center>";
	print"<input type=submit name=submit_btn value='".lang_get("edit")."'>";
	print"</div>";

} else {
	print lang_get('no_tests');
}

print"<input type=hidden name=records value=\"$records\">". NEWLINE;
print"</form>". NEWLINE;

html_print_footer();


# ---------------------------------------------------------------------
# $Log: requirement_tests_assoc_page.php,v $
# Revision 1.5  2007/02/03 10:25:53  gth2
# no message
#
# Revision 1.4  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2006/01/05 03:19:31  gth2
# fixing bug with filter on req-to-test assoc page - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>