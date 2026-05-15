<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirements/Test Traceability Report Page
#
# $RCSfile: requirement_traceability_matrix_page.php,v $  $Revision: 1.3 $
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
$project_id	        	= $project_properties['project_id'];
$row_style		        = '';

$display_options 	    = session_set_display_options("requirements", $_POST);
$order_by		        = $display_options['order_by'];
$order_dir		        = $display_options['order_dir'];
$page_number 		    = $display_options['page_number'];

$filter_doc_type	    = $display_options['filter']['doc_type'];
$filter_status		    = $display_options['filter']['status'];;
$filter_area_covered	= $display_options['filter']['area_covered'];;
$filter_functionality	= $display_options['filter']['functionality'];;
$filter_assign_release	= $display_options['filter']['assign_release'];;
$filter_per_page	    = $display_options['filter']['per_page'];;
$filter_show_versions	= $display_options['filter']['show_versions'];;
$filter_search		    = $display_options['filter']['requirement_search'];
$filter_priority	    = $display_options['filter']['priority'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('traceability_matrix_page'));
html_page_header( $db, $project_name );
html_print_menu();

requirement_menu_print($page);

error_report_check( $_GET );

print"<br>". NEWLINE;
print"<form action='$page' method=post>". NEWLINE;
print"<div align=center>". NEWLINE;
print"<br>". NEWLINE;
print"</div>". NEWLINE;

$g_timer->mark_time( "Get requirements" );

$rows_requirement = requirement_get( $project_id, $page_number, $order_by, $order_dir,
                                     $filter_doc_type, $filter_status, $filter_area_covered, 
                                     $filter_functionality, $filter_assign_release, 
                                     $filter_show_versions, 0, $filter_search, $filter_priority, "" );

$g_timer->mark_time( "Finished get requirements" );

################################################################################
# Traceability Matrix
################################################################################

if ($rows_requirement) 
{
	print"<div align=center>". NEWLINE;
	print"<table class=width100 rules=all>". NEWLINE;

	# Table headers
	print"<tr class=tbl_header>". NEWLINE;
	html_tbl_print_header( lang_get('req_id') );
	html_tbl_print_header( lang_get('req_name') );
	html_tbl_print_header( lang_get('req_doc_type') );
	html_tbl_print_header( lang_get('test_id') );
	html_tbl_print_header( lang_get('percent_covered') );
	print"</tr>". NEWLINE;

    # Variables for summary statistics
    $req_count = 0;
    $total_coverage = 0;

	foreach ($rows_requirement as $row_requirement) 
	{
        $req_count += 1;

		$req_id					= util_pad_id( $row_requirement[REQ_ID] );
        $req_name				= $row_requirement[REQ_FILENAME];
	    $req_version_id			= $row_requirement[REQ_VERS_UNIQUE_ID];
        $req_doc_type           = $row_requirement[REQ_DOC_TYPE_NAME];

        # Set the row's background color to something special if the requirement
        # has no test cases associated with it (a bad thing).
        $req_test_relationships = requirement_get_test_relationships($req_id);
        if (empty($req_test_relationships)) 
        {
		    $row_style = ROW2_STYLE;
        }
        else 
        {
    		$row_style = ROW1_STYLE;
        }
		print"<tr class='$row_style'>". NEWLINE;

		print"<td valign=top width='5%'><a href='requirement_detail_page.php?req_id=$req_id&amp;req_version_id=$req_version_id'>$req_id</a></td>". NEWLINE;
		print"<td valign=top width='25%' class='tbl-l'>$req_name</td>". NEWLINE;
		print"<td valign=top width='10%' class='tbl-c'>$req_doc_type</td>". NEWLINE;

        # List all associated test cases for the requirement
        $coverage = 0;
        if (!empty($req_test_relationships)) 
        {
		    print"<td valign=top align=left>";
            foreach($req_test_relationships as $req_test_rels) 
            {
                $test_id = $req_test_rels[TEST_ID];
                $test_id_link = util_pad_id($test_id);
                $coverage += $req_test_rels[TEST_REQ_ASSOC_PERCENT_COVERED];
                print"<a href='test_detail_page.php?test_id=$test_id&project_id=$project_id&amp;tab=3'>$test_id_link</a>&nbsp&nbsp&nbsp";
            } 
		    print"</td>";
            $total_coverage += $coverage;
        }
        else 
        {
		    print"<td valign=top align=left>No tests</td>";
        }
		print"<td valign=top width='5%' class='tbl-c'>$coverage</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}
	print"</table>". NEWLINE;
	print"</div>". NEWLINE;

    # Print out some summary statistics
    print"<br>". NEWLINE;
    print"<table>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td class=footer-l>" . lang_get('num_req') . ":  " . $req_count . "</td>". NEWLINE;
    print"</tr>". NEWLINE;
    $total_coverage = $total_coverage / $req_count;
    print"<tr>". NEWLINE;
    print"<td class=footer-l>" . lang_get('total_coverage') . ":  " . number_format($total_coverage, 1) . " %</td>". NEWLINE;
    print"</tr>". NEWLINE;
    print"</table>". NEWLINE;
} 
else 
{
	html_no_records_found_message(lang_get("no_requirements"));
}

print"</form>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_traceability_matrix_page.php,v $
# Revision 1.3  2009/03/26 10:06:09  sca_gs
# update by Bruce Butler
#
#
# Revision 1.1      2009/01/13   bruce butler
# Added project id to line 123
#
# Revision 1.0      2009/01/06   bruce butler
# Original development.
# ---------------------------------------------------------------------
?>
