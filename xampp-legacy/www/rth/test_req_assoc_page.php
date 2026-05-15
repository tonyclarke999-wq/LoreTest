<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Req Assoc Page
#
# $RCSfile: test_req_assoc_page.php,v $  $Revision: 1.8 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_button']) ) {

	require_once("test_req_assoc_action.php");
	exit;
}


include"./api/include_api.php";
auth_authenticate_user();


$page					= basename(__FILE__);
$action_page            = 'test_req_assoc_action.php';
$project_properties     = session_get_project_properties();
$s_project_name         = $project_properties['project_name'];
$s_project_id			= $project_properties['project_id'];
$s_test_details			= session_set_properties("test", $_GET);
$s_test_id				= $s_test_details['test_id'];
$row_style				= '';
$records 				= "";

$s_test_details			= session_set_properties("test", $_GET);
$test_id				= $s_test_details['test_id'];
$test_name       		= test_get_name($test_id);

session_records("test_req_assoc", test_get_req_assoc_ids($s_test_id) );

$filter_per_page		= 100;
$filter_doc_type		= "";
$filter_status			= "";
$filter_area_covered	= "";
$filter_functionality	= "";
$filter_assign_release	= "";
$filter_show_versions	= "latest";
$filter_search			= "";
$filter_priority		= "";

#$order_by 		= REQ_FILENAME;
#$order_dir		= "ASC";
#$page_number	= 1;

#util_set_filter('per_page', $filter_per_page, $_POST);
#util_set_filter('doc_type', $filter_doc_type, $_POST);
#util_set_filter('status', $filter_status, $_POST);
#util_set_filter('area_covered', $filter_area_covered, $_POST);
#util_set_filter('functionality', $filter_functionality, $_POST);
#util_set_filter('assign_release', $filter_assign_release, $_POST);
#util_set_filter('requirement_search', $filter_search, $_POST);
#util_set_filter('priority', $filter_priority, $_POST);

$s_display_options 		= session_set_display_options("requirement_assoc", $_POST);

#util_set_order_by($order_by, $_POST);
#util_set_order_dir($order_dir, $_POST);
#util_set_page_number($page_number, $_POST);

$order_by		= $s_display_options['order_by'];
$order_dir		= $s_display_options['order_dir'];
$page_number	= $s_display_options['page_number'];

$filter_per_page = $s_display_options['filter']['per_page'];
$filter_doc_type= $s_display_options['filter']['doc_type'];
$filter_status= $s_display_options['filter']['status'];
$filter_area_covered= $s_display_options['filter']['area_covered'];
$filter_functionality= $s_display_options['filter']['functionality'];
$filter_assign_release= $s_display_options['filter']['assign_release'];
$filter_search = $s_display_options['filter']['requirement_search'];
$filter_priority= $s_display_options['filter']['priority'];


# Set or Reset the % covered session vars
if( sizeof($_POST) ) {
	session_validate_form_set($_POST);
} else {
	session_validate_form_reset();
}

html_window_title();
html_print_body();
html_page_title($s_project_name ." - ". lang_get('test_req_assoc_page') );
html_page_header( $db, $s_project_name );

html_print_menu();
test_menu_print ($page);

error_report_check( $_GET );

print"<br>". NEWLINE;

print"<table class=width100>". NEWLINE;

print"<tr>". NEWLINE;
print"<th width='50%'>". lang_get('test_id') ."</td>". NEWLINE;
print"<th width='50%'>". lang_get('test_name') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class=grid-data-c><a href='test_detail_page.php?test_id=$test_id&project_id=$s_project_id'>". sprintf("%05s",$test_id) ."</a></td>". NEWLINE;
print"<td class=grid-data-c>$test_name</td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;

print"<form method=post action='$page' name='requirement_assoc' id='form_order'>". NEWLINE;

print"<br>". NEWLINE;
print"<div align=center>". NEWLINE;

# Filtering for all versions doesn't make sense here
html_print_requirements_filter(	$s_project_id,
								$filter_doc_type,
								$filter_status,
								$filter_area_covered,
								$filter_functionality,
								$filter_assign_release,
								$filter_per_page,
								$filter_show_versions=null,
								$filter_search,
								$filter_priority );

								
print"</div>". NEWLINE;

print"<br>";


$rows_requirement = requirement_get( $s_project_id, 
									 $page_number, 
									 $order_by, 
									 $order_dir, 
									 $filter_doc_type, 
									 $filter_status,
									 $filter_area_covered, 
									 $filter_functionality,
									 $filter_assign_release, 
									 $filter_show_versions, 
									 $filter_per_page,
									 $filter_search,
									 $filter_priority);
						
						
################################################################################
# Testset table

if($rows_requirement) {


	print"<div align=center>". NEWLINE;

	print"<table class=width100 rules=cols>". NEWLINE;

	# Table headers
	print"<tr class=tbl_header>". NEWLINE;
	print"<th></th>";
	html_tbl_print_sortable_header( lang_get('percent_covered_test') );
	html_tbl_print_sortable_header( lang_get('req_id'), REQ_TBL.".".REQ_ID, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_name'), REQ_TBL.".".REQ_FILENAME, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_detail'), REQ_VERS_TBL.".".REQ_VERS_DETAIL, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_type'), REQ_DOC_TYPE_TBL.".".REQ_DOC_TYPE_NAME, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('status'), REQ_VERS_TBL.".".REQ_VERS_STATUS, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_area'), REQ_AREA_COVERAGE_TBL.".".REQ_AREA_COVERAGE, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('functionality') );
	html_tbl_print_sortable_header( lang_get('req_locked_by'), REQ_TBL.".".REQ_LOCKED_BY, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_locked_date'),REQ_TBL.".".REQ_LOCKED_DATE, $order_by, $order_dir );
	print"</tr>". NEWLINE;

	foreach($rows_requirement as $row_requirement) {

		$req_id					= $row_requirement[REQ_ID];
		$req_version_id			= $row_requirement[REQ_VERS_UNIQUE_ID];

		# Name of the % covered text input
		$pc_covered_input_name = "percent_covered_". $req_id;

		# Get the % covered from db
		$percent_covered = test_requirement_get_pc_covered($s_test_id, $req_id);

		# Save and get the % covered from the session
		$percent_covered = session_validate_form_get_field($pc_covered_input_name, $percent_covered);

		$row_style = html_tbl_alternate_bgcolor($row_style);

		$rows_functions = requirement_get_functionality($s_project_id, $row_requirement[REQ_ID]);

		if( session_records_ischecked("test_req_assoc", $req_id) ) {
			$checked = "checked";
		} else {
			$checked = "";
		}

		# Build list of records
		if( empty($records) ) {
			$records = $req_id." => ''";
		} else {
			$records .= ", ".$req_id." => ''";
		}


		# Rows
		print"<tr class='$row_style'>". NEWLINE;
		print"<td><input type='checkbox' name='row_$req_id' $checked></td>";
		print"<td><input type=text name=$pc_covered_input_name size=3 maxlength=3 value='$percent_covered'></td>";
		print"<td>".util_pad_id($row_requirement[REQ_ID])."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_FILENAME]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_VERS_DETAIL]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_DOC_TYPE_NAME]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_VERS_STATUS]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_AREA_COVERAGE]."</td>". NEWLINE;
		print"<td class='tbl-l'>";
		foreach($rows_functions as $key => $value) {

			print$value."<br>";
		}
		print"</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_LOCKED_BY]."</td>". NEWLINE;
		print"<td class='tbl-l'>".$row_requirement[REQ_LOCKED_DATE]."</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	print"</table>". NEWLINE;

	print"</div>". NEWLINE;

	//print lang_get("update").": &nbsp;". NEWLINE;
	if( session_use_javascript() ) {
		print"<input id=select_all type=checkbox name=thispage onClick='checkAll( this )'>". NEWLINE;
		print"<label for=select_all>".lang_get("select_all")."</label>";
		print"&nbsp;". NEWLINE;
	}

	print"<div align=center>". NEWLINE;
	print"<input type='submit' name=submit_button value='".lang_get("edit")."'>". NEWLINE;
	print"</div>". NEWLINE;

} else {

	print lang_get("no_requirements");
}

print"</div>". NEWLINE;

print"<input type=hidden name=records value=\"$records\">". NEWLINE;
print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_req_assoc_page.php,v $
# Revision 1.8  2008/08/04 06:54:58  peter_thal
# added sorting function to several tables
#
# Revision 1.7  2008/07/09 07:13:25  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.6  2007/02/03 10:26:19  gth2
# no message
#
# Revision 1.5  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.4  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.3  2006/01/06 00:46:46  gth2
# correcting some minor problems with strings - gth
#
# Revision 1.2  2006/01/06 00:34:53  gth2
# fixed bug with associations - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>