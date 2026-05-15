<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Notification Page
#
# $RCSfile: requirement_notification_page.php,v $  $Revision: 1.5 $
# ---------------------------------------------------------------------

if( isset($_POST['submit_button']) ) {

	require_once("requirement_notification_action.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'requirement_action.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];
$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$row_style				= '';
$records 				= "";

session_records(	"requirements_notification",
					user_requirement_notifications($project_id, $user_id) );

$order_by			= REQ_FILENAME;
$order_dir			= "ASC";
$page_number 		= 1;

#util_set_order_by($order_by, $_POST);
#util_set_order_dir($order_dir, $_POST);
#util_set_page_number($page_number, $_POST);

$display_options 	= session_set_display_options("requirements", $_POST);
$order_by			= $display_options['order_by'];
$order_dir			= $display_options['order_dir'];
$page_number 		= $display_options['page_number'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('req_notifications'));
html_page_header( $db, $project_name );
html_print_menu();
requirement_menu_print($page);

error_report_check( $_GET );

print"<br>". NEWLINE;

print"<form action='$page' method=post name=requirements id='form_order'>". NEWLINE;

print"<div align=center>". NEWLINE;
print"<table class=hide60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
$rows_requirement = requirement_get($project_id, $page_number, $order_by, $order_dir,
						"", "", "", "", "", "latest", $per_page=RECORDS_PER_PAGE_REQUIREMENT_NOTIFICATIONS, "", "");
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</div>". NEWLINE;

################################################################################
# Testset table

if($rows_requirement) {

	print"<div align=center>". NEWLINE;
	print"<table class=width60 rules=cols>". NEWLINE;

	# Table headers
	print"<tr class=tbl_header>". NEWLINE;
	html_tbl_print_sortable_header( "email" );
	html_tbl_print_sortable_header( lang_get('req_id'), REQ_ID, $order_by, $order_dir );
	html_tbl_print_sortable_header( lang_get('req_name'), REQ_FILENAME, $order_by, $order_dir );
	print"</tr>". NEWLINE;

	foreach($rows_requirement as $row_requirement) {

		$req_id		= $row_requirement[REQ_ID];
		$req_name	= $row_requirement[REQ_FILENAME];

		if( empty($records) ) {
			$records = $row_requirement[REQ_ID]." => ''";
		} else {
			$records .= ", ".$row_requirement[REQ_ID]." => ''";
		}

		$row_style = html_tbl_alternate_bgcolor($row_style);

		# Rows
		print"<tr class='$row_style'>". NEWLINE;
		if( session_records_ischecked("requirements_notification", $req_id) ) {
			print"<td><input type=checkbox name=row_$req_id value='' checked></td>". NEWLINE;
		} else {
			print"<td><input type=checkbox name=row_$req_id value=''></td>". NEWLINE;
		}
		print"<td class='tbl-1'>".util_pad_id($req_id)."</td>". NEWLINE;
		print"<td class='tbl-l'>$req_name</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}

	print"</table>". NEWLINE;
	print"</div>". NEWLINE;

	print"<div align=center>". NEWLINE;
	print"<table class=hide60>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td align=left>". NEWLINE;
	if( session_use_javascript() ) {
		print"<input id=select_all type=checkbox name=thispage onClick='checkAll( this )'>". NEWLINE;
		print"<label for=select_all>".lang_get("select_all")."</label>";
		print"&nbsp;". NEWLINE;
	}
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</div>". NEWLINE;

	print"<br>". NEWLINE;

	print"<div align=center>". NEWLINE;
	print"<input type=hidden name=records value=\"$records\">". NEWLINE;
	print"<input type=submit name=submit_button value='".lang_get("change")."'>";
	print"</div>". NEWLINE;
} else {

	print lang_get("no_requirements");
}

print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_notification_page.php,v $
# Revision 1.5  2008/08/04 06:54:58  peter_thal
# added sorting function to several tables
#
# Revision 1.4  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2005/12/28 23:16:31  gth2
# Minor bug fix.  Calling wrong session function for project_id - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>