<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Page
#
# $RCSfile: requirement_page.php,v $  $Revision: 1.5 $
# ---------------------------------------------------------------------

if( isset($_POST['mass_req_update']) ) {

	require_once("requirement_group_action_page.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'requirement_action.php';
$num                    = 0;
$bg_color               = '';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];
$row_style				= '';

$display_options 	= session_set_display_options("requirements", $_POST);
$order_by			= $display_options['order_by'];
$order_dir			= $display_options['order_dir'];
$page_number 		= $display_options['page_number'];

$filter_doc_type		= $display_options['filter']['doc_type'];
$filter_status			= $display_options['filter']['status'];;
$filter_area_covered	= $display_options['filter']['area_covered'];;
$filter_functionality	= $display_options['filter']['functionality'];;
$filter_assign_release	= $display_options['filter']['assign_release'];;
$filter_per_page		= $display_options['filter']['per_page'];;
$filter_show_versions	= $display_options['filter']['show_versions'];;
$filter_search			= $display_options['filter']['requirement_search'];
$filter_priority		= $display_options['filter']['priority'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('req_page'));
html_page_header( $db, $project_name );
html_print_menu();
requirement_menu_print($page);

error_report_check( $_GET );

print"<br>". NEWLINE;

print"<form action='$page' method=post id='form_order'>". NEWLINE;

print"<div align=center>". NEWLINE;

html_print_requirements_filter(	$project_id,
								$filter_doc_type,
								$filter_status,
								$filter_area_covered,
								$filter_functionality,
								$filter_assign_release,
								$filter_per_page,
								$filter_show_versions,
								$filter_search,
								$filter_priority );

print"<br>". NEWLINE;

print"</div>". NEWLINE;

$g_timer->mark_time( "Get requirements" );

$rows_requirement = requirement_get( $project_id, $page_number, $order_by, $order_dir,
									$filter_doc_type, $filter_status, $filter_area_covered, $filter_functionality, $filter_assign_release, $filter_show_versions, $filter_per_page, $filter_search, $filter_priority, $csv_name="requirements" );

$g_timer->mark_time( "Finished get requirements" );

################################################################################
# Testset table

if($rows_requirement) {


	print"<div align=center>". NEWLINE;

	print"<table class=width100 rules=cols>". NEWLINE;

	# Table headers
	print"<tr class=tbl_header>". NEWLINE;
	print"<th></th>";
	html_tbl_print_sortable_header( lang_get('req_id'), REQ_ID, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('version'),	REQ_VERS_VERSION, $order_by, $order_dir );
	html_tbl_print_sortable_header( "" );
	html_tbl_print_sortable_header( lang_get('req_name'), REQ_FILENAME, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_detail'), REQ_VERS_DETAIL, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_type'), REQ_DOC_TYPE_NAME, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('status'), REQ_VERS_STATUS, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_area'), REQ_AREA_COVERAGE, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('functionality') );
	html_tbl_print_sortable_header( lang_get('req_locked_by'), REQ_LOCKED_BY, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_locked_date'),	REQ_LOCKED_DATE, $order_by, $order_dir );
	print"</tr>". NEWLINE;

	foreach($rows_requirement as $row_requirement) {

		$req_id					= util_pad_id( $row_requirement[REQ_ID] );
		$req_name				= $row_requirement[REQ_FILENAME];
		$req_version_id			= $row_requirement[REQ_VERS_UNIQUE_ID];
		$req_version			= $row_requirement[REQ_VERS_VERSION];
		$req_version_filename	= $row_requirement[REQ_VERS_FILENAME];
		$req_version_detail		= $row_requirement[REQ_VERS_DETAIL];
		$req_doc_type			= $row_requirement[REQ_DOC_TYPE_NAME];
		$req_version_status		= $row_requirement[REQ_VERS_STATUS];
		$req_area_covered		= $row_requirement[REQ_AREA_COVERAGE];
		$req_locked_by			= $row_requirement[REQ_LOCKED_BY];
		$req_locked_date		= $row_requirement[REQ_LOCKED_DATE];

		$row_style = html_tbl_alternate_bgcolor($row_style);

		$rows_functions = requirement_get_functionality($project_id, $row_requirement[REQ_ID]);

		# Rows
		print"<tr class='$row_style'>". NEWLINE;
		print"<td valign=top><input type='checkbox' name='row_req_arr[{$req_id}][{$req_version_id}]'></td>";
		print"<td valign=top><a href='requirement_detail_page.php?req_id=$req_id&amp;req_version_id=$req_version_id'>$req_id</a></td>". NEWLINE;
		print"<td valign=top nowrap class='tbl-l' valign=top>$req_version</td>". NEWLINE;
		print"<td valign=top>".html_file_type( $req_version_filename )."</td>". NEWLINE;
		print"<td valign=top class='tbl-l'>$req_name</td>". NEWLINE;
		print"<td valign=top class='tbl-l'>$req_version_detail</td>". NEWLINE;
		print"<td valign=top class='tbl-l'>$req_doc_type</td>". NEWLINE;
		print"<td valign=top class='tbl-l'>$req_version_status</td>". NEWLINE;
		print"<td valign=top class='tbl-l'>$req_area_covered</td>". NEWLINE;
		print"<td valign=top class='tbl-l'>";
		foreach($rows_functions as $key => $value) {

			print"$value; ";
		}
		print"</td>". NEWLINE;
		print"<td valign=top class='tbl-l'>$req_locked_by</td>". NEWLINE;
		print"<td valign=top class='tbl-l'>$req_locked_date</td>". NEWLINE;
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
	print"<select name=action>". NEWLINE;
		print"<option value=status>".lang_get("status")."</option>". NEWLINE;
		print"<option value=assigned_release>".lang_get("req_assign_release")."</option>". NEWLINE;
	print"</select>". NEWLINE;

	print"<input type=submit name=mass_req_update value='".lang_get("update")."'>";

} else {


	html_no_records_found_message( lang_get("no_requirements") );
}

print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_page.php,v $
# Revision 1.5  2008/08/04 06:54:58  peter_thal
# added sorting function to several tables
#
# Revision 1.4  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2005/12/13 13:59:56  gth2
# Completed the addition of requirement priority - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>