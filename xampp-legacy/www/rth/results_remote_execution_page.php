<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Step Import Page
#
# $RCSfile: results_remote_execution_page.php,v $  
# $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$s_user_properties		= session_get_user_properties();
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];
$testset_id				= $_GET['testset_id'];
$testset_id				= util_pad_id( $testset_id);
$testset_name			= testset_get_name( $testset_id );


html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('remote_execution_page') );
//html_page_header( $db, $project_name );

error_report_check( $_GET );

print"<br>";
print"<div align='center'>". NEWLINE;
print"<table class=width90 rules=cols>";
print"<tr class='tbl_header'>";
html_tbl_print_header( lang_get('testset_id') );
html_tbl_print_header( lang_get('testset_name') );
print"</tr>";

print"<tr>";
print"<td class='tbl-c'>$testset_id</a></td>";
print"<td class='tbl-c'>$testset_name</td>";
print"</tr>";
print"</table>";
print"<br><br>";

# EXAMPLE TEMPLATE
/*
print"<table border=0 width='90%' align=center>";
print"<tr colspan='4'>". NEWLINE;
print"<td><b>". lang_get('correct_file_format') .":</b></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

# SAMPLE FILE
print"<table border=1 width='90%' rules=cols align=center>";

print"<tr style='font-weight:bold' class='tbl-header'>". NEWLINE;
print"<th nowrap>". lang_get('run') ."</td>". NEWLINE;
print"<th nowrap>". lang_get('test_name') ."</td>". NEWLINE;
print"<th nowrap>". lang_get('test_path') ."</td>". NEWLINE;
print"<th nowrap>". lang_get('parameters') ."</td>". NEWLINE;
print"<th nowrap>". lang_get('priority') ."</td>". NEWLINE;
print"<th nowrap>". lang_get('machine_name') ."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr class='row-1'>". NEWLINE;
print"<td>Yes</td>". NEWLINE;
print"<td>Demo Test1</td>";
print"<td>C:\Sandbox\Tests\DemoTestOne</td>";
print"<td>x=1#y=2</td>". NEWLINE;
print"<td>1</td>". NEWLINE;
print"<td>10.110.90.10</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr class='row-2'>". NEWLINE;
print"<td>Yes</td>". NEWLINE;
print"<td>Demo Test2</td>";
print"<td>C:\Sandbox\Tests\DemoTestTwo</td>";
print"<td>x=3#y=4</td>". NEWLINE;
print"<td>2</td>". NEWLINE;
print"<td>10.110.90.10</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr class='row-1'>". NEWLINE;
print"<td>Yes</td>". NEWLINE;
print"<td>Demo Test3</td>";
print"<td>C:\Sandbox\Tests\DemoTestThree</td>";
print"<td>user=john#password=doe</td>". NEWLINE;
print"<td>1</td>". NEWLINE;
print"<td>10.110.90.21</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr class='row-2'>". NEWLINE;
print"<td>No</td>". NEWLINE;
print"<td>Demo Test4</td>";
print"<td>C:\Sandbox\Tests\DemoTestFour</td>";
print"<td></td>". NEWLINE;
print"<td>2</td>". NEWLINE;
print"<td>10.110.90.21</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br><br>". NEWLINE;
*/

# DESCRIPTION OF FIELDS
/*
print"<table border=0 width='90%' align=center>";
print"<tr><td align='left'><b>". lang_get('run') ."</b> - Whether or not to run the test.</td></tr>". NEWLINE;
print"<tr><td align='left'><b>". lang_get('test_name') ."</b> - The textual description of the test as it appears in RTH.</td></tr>". NEWLINE;
print"<tr><td align='left'><b>". lang_get('test_path') ."</b> - The absolute path to the test.</td></tr>". NEWLINE;
print"<tr><td align='left'><b>". lang_get('parameters') ."</b> - Any parameters that must be passed to the test.</td></tr>". NEWLINE;
print"<tr><td align='left'><b>". lang_get('priority') ."</b> - The order of execution (tests can only be ordered when they run on the same machine)</td></tr>". NEWLINE;
print"<tr><td align='left'><b>". lang_get('machine_name') ."</b> - The IP Address of the machine to be used.</td></tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br><br>". NEWLINE;
*/

# FILE UPLOAD FORM
print"<table class=width60>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<form method='POST' enctype='multipart/form-data' action='results_remote_execution_action.php'>". NEWLINE;
print"<table class=inner>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td colspan='2'><h4>". lang_get('upload_tests') ."</h4></td>". NEWLINE;
print"</tr>". NEWLINE;

# TEST TOOLS
$tool_array = array( 'WinRunner', );
print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('test_tool') ."</td>". NEWLINE;
print"<td class='form-data-l'>". NEWLINE;
print"<select name='test_tool'>";
	html_print_list_box_from_array( $tool_array );
print"</select>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>";

# FILE
print"<tr>". NEWLINE;
	print"<td class='form-lbl-r'>". lang_get('upload_tests') ."</td>". NEWLINE;
	print"<td class='form-data-l'><input type='file' name='f'></input></td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td colspan='2' class='form-data-c'><input type='submit' value='". lang_get('submit_btn') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;
print"</form>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</div>". NEWLINE;

print"<br><br>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: results_remote_execution_page.php,v $
# Revision 1.3  2009/03/20 07:14:09  sca_gs
# changed example data
#
# Revision 1.2  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1  2006/05/03 20:23:13  gth2
# no message
#
# ---------------------------------------------------------------------

?>