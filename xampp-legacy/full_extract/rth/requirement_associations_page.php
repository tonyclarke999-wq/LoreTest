<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Associations Page
#
# $RCSfile: requirement_associations_page.php,v $  
# $Revision: 1.6 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

session_validate_form_reset();

$page                   = basename(__FILE__);
$action_page            = 'requirement_action.php';
$num                    = 0;
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];
$username				= session_get_username();
$row_style				= '';


$display_options 		= session_set_display_options("requirements_folder_view", $_POST);

$filter_doc_type		= $display_options['filter']['doc_type'];
$filter_status			= $display_options['filter']['status'];;
$filter_area_covered	= $display_options['filter']['area_covered'];;
$filter_functionality	= $display_options['filter']['functionality'];;
$filter_assign_release	= $display_options['filter']['assign_release'];;
$filter_per_page		= $display_options['filter']['per_page'];;
$filter_show_versions	= $display_options['filter']['show_versions'];;
$filter_search			= $display_options['filter']['requirement_search'];
$filter_priority		= $display_options['filter']['priority'];



$display_options 	= session_set_display_options( "requirements", array_merge($_POST, $_GET) );
$s_tab 				= $display_options['tab'];

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];
$s_req_version_id	= $s_properties['req_version_id'];

if( empty($_GET['req_version_id']) ) {
	$_GET['req_version_id'] = requirement_get_latest_version( $s_req_id );
	$s_req_version_id = $_GET['req_version_id'];
}

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get("req_folder_view_page"));
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);

error_report_check( $_GET );

print"<br>". NEWLINE;

print"<form action='$page' method=post>". NEWLINE;

print"<div align=center>". NEWLINE;

html_print_requirements_filter(	$project_id,
								$filter_doc_type,
								$filter_status,
								$filter_area_covered,
								$filter_functionality,
								$filter_assign_release,
								$filter_per_page=null,
								$filter_show_versions=null,
								$filter_search,
								$filter_priority );

print"<br>". NEWLINE;

print"</div>";

print"</form>";

$rows_top_level_requirements = requirement_get(	$project_id,
												$page_number = 0,
												$order_by=REQ_FILENAME,
												$order_dir="ASC",
												$filter_doc_type,
												$filter_status,
												$filter_area_covered,
												$filter_functionality,
												$filter_assign_release,
												$filter_show_versions='latest',
												$filter_per_page,
												$filter_search,
												$filter_priority,
												$csv_name=null,
												$root_node=true );

# tree array
$tree = array();

if( $rows_top_level_requirements ) {

	# get the children of the top level requirements
	foreach( $rows_top_level_requirements as $row_req ) {

		$root_node			= $row_req[REQ_ID];
		$req_version		= $row_req[REQ_VERS_VERSION];		
		$root_node_name		= $row_req[REQ_FILENAME];

		# build the tree array

		$tree[] = array(	"uid" 			=> $root_node,
							"name" 			=> $root_node_name,
							"children"		=> requirement_get_children($root_node) );
	}

	# print the tree array as html
	html_dynamic_tree( "requirements", $tree, $root_node=true );

} else {

	html_no_records_found_message( lang_get("no_requirements") );
}

html_print_footer();


# ---------------------------------------------------------------------
# $Log: requirement_associations_page.php,v $
# Revision 1.6  2007/02/03 10:25:53  gth2
# no message
#
# Revision 1.5  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.4  2006/05/03 22:05:19  gth2
# no message
#
# Revision 1.3  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2005/12/13 13:59:56  gth2
# Completed the addition of requirement priority - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>