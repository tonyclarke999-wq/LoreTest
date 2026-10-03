<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: bug_page.php,v $ $Revision: 1.4 $
# ------------------------------------

if( isset($_POST['mass_update_btn']) ) {

	require_once("bug_group_action_page.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'test_action.php';
$detail_page			= 'bug_detail_page.php';
$bug_update_url			= 'bug_detail_update_page.php';
$num                    = 0;
$bg_color               = '';
$project_properties     = session_get_project_properties();
$project_name			= $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

if( isset($_POST['filter_jump'] )  && $_POST['filter_jump'] != '' ) {
	html_redirect( "$detail_page?bug_id=$_POST[filter_jump]" );
}
else {
	$filter_jump="";
}

$s_display_options 		= session_set_display_options("bug", $_POST);
$order_by				= $s_display_options['order_by'];
$order_dir				= $s_display_options['order_dir'];
$page_number			= $s_display_options['page_number'];

$filter_per_page		= $s_display_options['filter']['per_page'];
$filter_bug_status		= $s_display_options['filter']['status'];
$filter_bug_category	= $s_display_options['filter']['category'];
$filter_bug_component	= $s_display_options['filter']['component'];
$filter_reported_by		= $s_display_options['filter']['reported_by'];
$filter_assigned_to		= $s_display_options['filter']['assigned_to'];
$filter_assigned_to_dev	= $s_display_options['filter']['assigned_to_developer'];
$filter_found_in_rel	= $s_display_options['filter']['found_in_release'];
$filter_assigned_to_rel	= $s_display_options['filter']['assigned_to_release'];
$filter_view_closed		= $s_display_options['filter']['view_closed'];
$filter_search			= $s_display_options['filter']['bug_search'];


/*
print"filter_per_page = $filter_per_page<br>";
print"filter_bug_status = $filter_bug_status<br>";
print"filter_bug_category = $filter_bug_category<br>";
print"filter_bug_component = $filter_bug_component<br>";
print"filter_reported_by = $filter_reported_by<br>";
print"filter_assigned_to = $filter_assigned_to<br>";
print"filter_assigned_to_dev = $filter_assigned_to_dev<br>";
print"filter_found_in_rel = $filter_found_in_rel<br>";
print"filter_assigned_to_rel = $filter_assigned_to_rel<br>";
print"filter_view_closed = $filter_view_closed<br>";
print"filter_search = $filter_search<br>";
*/


html_window_title();
html_print_body();
html_page_title( $project_name ." - ". lang_get('bug_page') );
html_page_header( $db, $project_name );
html_print_menu();
bug_menu_print( $page );

error_report_check( $_GET );

print"<br>";
print"<form method='post' action='$page' name='bug_form'>". NEWLINE;
print"<div align=center>". NEWLINE;

html_print_bug_filter(	$project_id,
						$filter_bug_status,
						$filter_bug_category,
						$filter_bug_component,
						$filter_reported_by,
						$filter_assigned_to,
						$filter_assigned_to_dev,
						$filter_found_in_rel,
						$filter_assigned_to_rel,
						$filter_per_page,
						$filter_view_closed,
						$filter_search,
						$filter_jump);


print"<br>". NEWLINE;

$g_timer->mark_time( "Load rows to display on page from db into memory" );



$row = bug_get( $project_id,
				$page_number,
				$order_by,
				$order_dir,
				$filter_bug_status,
				$filter_bug_category,
				$filter_bug_component,
				$filter_reported_by,
				$filter_assigned_to,
				$filter_assigned_to_dev,
				$filter_found_in_rel,
				$filter_assigned_to_rel,
				$filter_per_page,
				$filter_view_closed,
				$filter_search,
				$filter_jump,
				$csv_export="bugs");


print"</div>". NEWLINE;

$g_timer->mark_time( "Finished load rows to display on page from db into memory" );
if( $row ) {
	print"<div align=center>". NEWLINE;

	print"<table class=width100 rules=cols>". NEWLINE;

	print"<tr class=tbl_header>". NEWLINE;
	print"<th></th>". NEWLINE;
	print"<th></th>". NEWLINE;
	html_tbl_print_header( lang_get('bug_id'), BUG_ID, $order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_priority'), BUG_PRIORITY,	$order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_status'), BUG_STATUS, $order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_category'), BUG_CATEGORY, $order_by, $order_dir  );
	html_tbl_print_header( lang_get('reported_by'),	BUG_REPORTER, $order_by, $order_dir );
	html_tbl_print_header( lang_get('assigned_to'),	BUG_ASSIGNED_TO, $order_by, $order_dir );
	#html_tbl_print_header( lang_get('found_in_release'), BUG_FOUND_IN_RELEASE,	$order_by, $order_dir );
	#html_tbl_print_header( lang_get('assigned_to_release'), BUG_ASSIGN_TO_RELEASE, $order_by, $order_dir );
	html_tbl_print_header( lang_get('bug_summary') );
	print"</tr>". NEWLINE;
	$g_timer->mark_time( "Outputting main html table to browser" );

	$row_style = '';

	foreach( $row as $row_bug_detail ) {

		$bug_id				= $row_bug_detail[BUG_ID];
		$priority			= $row_bug_detail[BUG_PRIORITY];
		$bug_status			= $row_bug_detail[BUG_STATUS];
		$category			= $row_bug_detail[CATEGORY_NAME];
		$component			= $row_bug_detail[COMPONENT_NAME];
		$reported_by	    = $row_bug_detail[BUG_REPORTER];
		$reported_date		= $row_bug_detail[BUG_REPORTED_DATE];
		$assigned_to		= $row_bug_detail[BUG_ASSIGNED_TO];
		$found_in_release   = $row_bug_detail[BUG_FOUND_IN_RELEASE];
		$assign_to_release  = $row_bug_detail[BUG_ASSIGN_TO_RELEASE];
		$discovery_period	= $row_bug_detail[BUG_DISCOVERY_PERIOD];
		$summary			= $row_bug_detail[BUG_SUMMARY];


		$display_bug_id = util_pad_id($bug_id);

		#$filename = test_get_filename($bug_id);

		$row_style = html_tbl_alternate_bgcolor($row_style);
		print"<tr class='$row_style'>". NEWLINE;
		print"<td><input type='checkbox' name='row_bug_arr[{$bug_id}]'></td>". NEWLINE;
		print"<td class='tbl-c'><a href='$bug_update_url?bug_id=$bug_id'><img src='".IMG_SRC."update.gif' title='". lang_get('update_bug') ."' border=0></a></td>". NEWLINE;

		//$detail_url = $detail_page ."?bug_id=". $bug_id ."&test_version_id=". $test_version_id;
		$detail_url = $detail_page ."?bug_id=". $bug_id;

		print"<td class='tbl-c'><a href='$detail_url' title='". lang_get('bug_view_detail') ."'>$display_bug_id</a></td>". NEWLINE;
		print"<td class='tbl-c'>$priority</td>". NEWLINE;
		print"<td class='tbl-c'>$bug_status</td>". NEWLINE;
		print"<td class='tbl-c'>$category</td>". NEWLINE;
		print"<td class='tbl-c'>$reported_by</td>". NEWLINE;
		print"<td class='tbl-c'>$assigned_to</td>". NEWLINE;
		#print"<td class='tbl-c'>$found_in_release</td>". NEWLINE;
		#print"<td class='tbl-c'>$assign_to_release</td>". NEWLINE;
		print"<td class='tbl-c' width='35%'>$summary</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	$g_timer->mark_time( "Finished outputting main html table to browser" );

	print"</table>". NEWLINE;
	print"</div>". NEWLINE;


	if( session_use_javascript() ) {
		//print"<input type=checkbox name=all_tests value=all onClick=checkall('bug_form', this.form.all_tests.checked)\">Select All&nbsp;&nbsp;";
		print"<input id=select_all type=checkbox onClick='checkAll( this )'>". NEWLINE;
		print"<label for=select_all>".lang_get("select_all")."</label>". NEWLINE;
	}
	print"<select name=action>";
	print"<option value=bug_status>". lang_get('bug_status') ."</option>";
	print"<option value=assign_to>". lang_get('assign_to') ."</option>";
	print"<option value=assign_to_dev>". lang_get('assign_to_developer') ."</option>";
	print"<option value=bug_more>". lang_get('bug_move') ."</option>";
	print"</select>";
	print"&nbsp;";
	print"<input type=submit name=mass_update_btn value='".lang_get("update")."'>";

} else {

	html_no_records_found_message( lang_get('no_tests') );
}

print"</form>". NEWLINE;

html_print_footer();

# ------------------------------------
# $Log: bug_page.php,v $
# Revision 1.4  2007/02/25 23:17:39  gth2
# fixing bugs for release 1.6.1 - gth
#
# Revision 1.3  2006/08/05 22:07:59  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------

?>
