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
# $RCSfile: test_step_import_csv_page.php,v $  $Revision: 1.9 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page							= basename(__FILE__);
$update_action_page				= 'test_detail_update_page.php';
$test_detail_page				= 'test_detail_page.php';
$add_version_page				= 'test_add_version_page.php';
$delete_page					= 'delete_page.php';
$new_upload_action_page			= 'test_detail_new_upload_action.php';
$row_test_step_add_action_page  = 'test_step_add_action.php';
$row_test_step_renumber_page    = 'test_step_renumber_action.php';
$row_test_step_edit_page		= 'test_step_edit_page.php';
$delete_page					= 'delete_page.php';
$active_version_page			= 'test_version_make_active_action.php';
$test_page						= 'test_page.php';

$s_user_properties		= session_get_user_properties();
$s_project_properties   = session_get_project_properties();
$s_show_options 		= session_get_show_options();
$s_test_details			= session_set_properties("test", $_GET);

$s_user_id				= $s_user_properties['user_id'];
$s_username				= $s_user_properties['username'];
$s_tempest_admin		= $s_user_properties['tempest_admin'];
$s_project_rights		= $s_user_properties['project_rights'];
$s_delete_rights		= $s_user_properties['delete_rights'];
$s_email				= $s_user_properties['email'];

$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

$project_details		= project_get_details($project_id);
$s_show_test_input		= $project_details[PROJ_SHOW_TEST_INPUT];

$test_id				= util_pad_id( $s_test_details['test_id'] );
$test_version_id		= $s_test_details['test_version_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('test_import_csv_page') );
html_page_header( $db, $project_name );
html_print_menu();

$row = test_get_detail( $test_id );

$test_name       	= $row[TEST_NAME];
$test_purpose    	= $row[TEST_PURPOSE];
//$test_comments   = $row[TEST_COMMENTS];
$ba_owner        	= $row[TEST_BA_OWNER];
$qa_owner        	= $row[TEST_QA_OWNER];
$test_type       	= $row[TEST_TESTTYPE];
$area_tested     	= $row[TEST_AREA_TESTED];
$test_priority   	= $row[TEST_PRIORITY];
$manual          	= $row[TEST_MANUAL];
$automated       	= $row[TEST_AUTOMATED];
$performance	 	= $row[TEST_LR];
$autopass        	= $row[TEST_AUTO_PASS];
$assigned_to     	= $row[TEST_ASSIGNED_TO];
$assigned_by     	= $row[TEST_ASSIGNED_BY];
$dateassigned    	= $row[TEST_DATE_ASSIGNED];
$dateexpcomplete 	= $row[TEST_DATE_EXPECTED];
$dateactcomplete 	= $row[TEST_DATE_COMPLETE];
$duration		 	= $row[TEST_DURATION];
$test_status     	= $row[TEST_STATUS];
$signoff_by		 	= $row[TEST_SIGNOFF_BY];
$signoff_date    	= $row[TEST_SIGNOFF_DATE];
$last_updated_date 	= $row[TEST_LAST_UPDATED];
$last_updated_by 	= $row[TEST_LAST_UPDATED_BY];

print"<br>". NEWLINE;
print"<table class=width100 rules='cols' border='1'>". NEWLINE;
print"<tr>". NEWLINE;
html_tbl_print_header( lang_get('test_id') );
html_tbl_print_header( lang_get('test_name') );
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td><a href='$test_detail_page?test_id=$test_id&project_id=$project_id'>$test_id</a></td>". NEWLINE;
print"<td>$test_name</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

//print"<b>Import Test Steps for Test: $test_name</b>";
/*
if( IMPORT_EXPORT_TO_EXCEL ) {
	print"<font color=red><br><br><b>Warning: Make sure the excel file is in the following format:</b></font>";
}
else {
	print"<font color=red><br><br><b>Warning: Make sure the csv file is in the following format:</b></font>";
}
*/

print"<br><br>";

# EXAMPLE TEMPLATE
print"<table border=0 width='90%' align=center>";
print"<tr colspan='4'>". NEWLINE;
print"<td><b>". lang_get('correct_file_format') .":</b></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;


print"<table border=1 width='90%' align=center>";

print"<tr style='font-weight:bold'>". NEWLINE;
print"<td nowrap>". lang_get('test_step_no') ."</td>". NEWLINE;
print"<td nowrap>". lang_get('action') ."</td>". NEWLINE;
print"<td nowrap>". lang_get('step_inputs') ."</td>". NEWLINE;
print"<td nowrap>". lang_get('expected_result') ."</td>". NEWLINE;
print"<td nowrap>". lang_get('info_step') ." (Y)</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td>1</td>". NEWLINE;
print"<td>The actor enters the URL</td>";
print"<td>url: http://www.rth.net</td>";
print"<td>The system displays a request for a User ID and Password.</td>". NEWLINE;
print"<td>Y</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td>2</td>". NEWLINE;
print"<td>The actor enters their User Id and Password.</td>". NEWLINE;
print"<td>username: john password: doe</td>". NEWLINE;
print"<td>The system displays the home page</td>". NEWLINE;
print"<td>Y</td>". NEWLINE;
print"</tr>";

print"<tr>". NEWLINE;
print"<td>3</td>". NEWLINE;
print"<td>Verify that the Client's name and address appears as the first listed location.</td>". NEWLINE;
print"<td>&nbsp;</td>". NEWLINE;
print"<td>The Client's name and addressappears as the first listed location.". NEWLINE;
print"Name: John Doe Address: 10 Wilbur Street City: NY</td>". NEWLINE;
print"<td>&nbsp;</td>". NEWLINE;
print"</tr>";

print"<tr>". NEWLINE;
print"<td>4</td>". NEWLINE;
print"<td>Verify that the Radio Button is selected on the Yellow Line.</td>";
print"<td>&nbsp;</td>";
print"<td>Radio Button is selected on the Yellow Line</td>". NEWLINE;
print"<td>&nbsp;</td>". NEWLINE;
print"</tr>";

print"<tr>". NEWLINE;
print"<td>...</td>". NEWLINE;
print"<td>...</td>". NEWLINE;
print"<td>...</td>". NEWLINE;
print"<td>...</td>". NEWLINE;
print"<td>...</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;



print"<br>";
print"<br>";
print"<br>";

error_report_check( $_GET );

print"<br>";
if( IMPORT_EXPORT_TO_EXCEL ) {
	print"<a href='import_test_steps_example.xls' target='_blank'>Download Template</a>";
}
else {
	print"<a href='import_test_steps_example.csv' target='_blank'>Download Template</a>";
}
//print" (Right click: \"Save Target As...\")";
print"<br>";
print"<br>";


print"<b>". lang_get('upload_file') .":&nbsp;</b>";
print"<form enctype='multipart/form-data' name='upload' method=post action='test_step_import_csv_action.php'>";
print"<input type=hidden name=test_id value=$test_id>";
print"<input type=hidden name=test_version_id value=$test_version_id>";

print"<input type=file name=upload_file size=45>";

print"<input type='submit' value='Upload'>";

print"</form>";

html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_step_import_csv_page.php,v $
# Revision 1.9  2009/03/20 07:14:57  sca_gs
# changed example data
#
# Revision 1.8  2008/07/21 07:42:34  peter_thal
# small bug fixes for test_detail_page linking parameter
#
# Revision 1.7  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.6  2006/06/24 14:34:14  gth2
# updating changes lost with cvs problem.
#
# Revision 1.5  2006/04/11 12:11:03  gth2
# create a test version when uploading test steps - gth
#
# Revision 1.4  2006/04/09 17:33:30  gth2
# removing unnecessary code - gth
#
# Revision 1.3  2006/02/09 12:35:22  gth2
# cleaning up page for csv/excel import - gth
#
# Revision 1.2  2006/01/05 23:30:35  gth2
# changing upload file name - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>