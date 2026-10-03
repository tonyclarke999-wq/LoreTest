<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Detail Page
#
# $RCSfile: test_detail_page.php,v $  $Revision: 1.17 $
# ---------------------------------------------------------------------

include"./api/include_api.php";

auth_authenticate_user();
$step_action="";
$step_expected="";

$page						= basename(__FILE__);
$update_action_page			= 'test_detail_update_page.php';
$copy_action_page			= 'test_detail_copy_page.php';
$add_version_page			= 'test_add_version_page.php';
$delete_page				= 'delete_page.php';
$new_upload_action_page		= 'test_detail_new_upload_action.php';
$row_test_step_add_action_page  = 'test_step_add_action.php';
$row_test_step_renumber_page    = 'test_step_renumber_action.php';
$row_test_step_edit_page		= 'test_step_edit_page.php';
$delete_page				= 'delete_page.php';
$active_version_page		= 'test_version_make_active_action.php';
$test_page					= 'test_page.php';
#$row_test_step_delete_page		= 'delete_page.php';

$num                    = 0;
$bg_color               = '';
$row_style				= '';

$project_id				= $_GET['project_id'];
$s_test_details			= session_set_properties("test", $_GET);
$test_id				= $s_test_details['test_id'];
$s_user_properties		= session_get_user_properties();
$s_user_id				= $s_user_properties['user_id'];
$test_name 				= test_get_name( $test_id );


#validation of several item existence
$pm = project_get_name($project_id);
if( !empty($pm) ) {

    $project_name = project_get_name($project_id);
} else {

 	error_report_show('login.php', PROJECT_NOT_EXISTS);
}

if( !user_has_rights($project_id, $s_user_id, USER)){
	error_report_show('login.php', NO_RIGHTS_TO_VIEW_PROJECT);
}

if( !test_id_exists($project_id, $test_id)){
	error_report_show('login.php', TEST_NOT_EXISTS);
}

session_set_new_project_name($project_name);
session_reset_project();
session_initialize();

session_setLogged_in(TRUE);
session_set_application_details( $project_name, session_get_username() );


$s_user_properties		= session_get_user_properties();
$s_project_properties   = session_get_project_properties();
$s_show_options 		= session_get_show_options();
$s_test_details			= session_set_properties("test", $_GET);
$teststep_display_options= session_set_display_options("test_steps", $_POST);

$s_user_id				= $s_user_properties['user_id'];
$s_username				= $s_user_properties['username'];
$s_tempest_admin		= $s_user_properties['tempest_admin'];
$s_project_rights		= $s_user_properties['project_rights'];
$s_delete_rights		= $s_user_properties['delete_rights'];
$s_email				= $s_user_properties['email'];

$order_by				= $teststep_display_options['order_by'];
$order_dir				= $teststep_display_options['order_dir'];

#$project_name           = $s_project_properties['project_name'];
#$project_id				= $s_project_properties['project_id'];




$show_priority 			= $s_show_options['show_priority'];

$test_id				= $s_test_details['test_id'];
//$test_version_id		= $s_test_details['test_version_id'];

$redirect_url			= $page . "?test_id=". $test_id ."&project_id=". $project_id;

$test_name 				= test_get_name( $test_id );

session_set_display_options('test_detail', $_POST);
$s_test_detail_options 	= session_set_display_options('test_detail', $_GET);
$s_tab					= $s_test_detail_options['tab'];
$s_page_number			= $s_test_detail_options['page_number'];

if( isset($_GET['test_id']) ) {

	//$s_page_number = 1;
}


if( isset($_GET['failed']) ) {
	$is_validation_failure = $_GET['failed'];
} else {
	$is_validation_failure = false;
}



$project_manager		= user_has_rights( $project_id, $s_user_id, MANAGER );
$user_has_delete_rights	= ($s_delete_rights==="Y" || $project_manager);


html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('test_detail_page') );
html_page_header( $db, $project_name );
html_print_menu();

test_menu_print($page);

error_report_check( $_GET );

$row = test_get_detail( $test_id );

$test_name       	= $row[TEST_NAME];
$test_purpose    	= $row[TEST_PURPOSE];
$test_comments		= $row[TEST_COMMENTS];
$ba_owner        	= $row[TEST_BA_OWNER];
$qa_owner        	= $row[TEST_QA_OWNER];
$tester	        	= $row[TEST_TESTER];
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
$email_ba_owner		= $row[TEST_EMAIL_BA_OWNER];
$email_qa_owner		= $row[TEST_EMAIL_QA_OWNER];

# $test_version_id = $row[TEST_VERS_ID]; It's in the session
//$version_no		 = $row[TEST_VERS_NUMBER];
//$latest_version	 = $row[TEST_VERS_LATEST];
//$active_version  = $row[TEST_VERS_ACTIVE];
//$test_comments   = $row[TEST_VERS_COMMENTS];
//$test_status     = $row[TEST_VERS_STATUS];
//$signoff_by		 = $row[TEST_VERS_SIGNOFF_BY];
//$signoff_date    = $row[TEST_VERS_SIGNOFF_DATE];
//$author			 = $row[TEST_AUTHOR];
$date_created	 = $row[TEST_DATE_CREATED];

print"<br>";
print"<div align=center>";

if ( !empty($row) ) {

	print"<table class=width95>". NEWLINE;
	print"<tr class='tbl_header'>". NEWLINE;
		print"<td width='50%'>". lang_get('test_id') ."</td>". NEWLINE;
		print"<td width='50%'>". lang_get('test_name') ."</td>". NEWLINE;
		#print"<td width='33%'>". lang_get('test_version') ."</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"<tr>". NEWLINE;
		$display_test_id = util_pad_id( $test_id );
		print"<td class=grid-data-c>$display_test_id</td>". NEWLINE;
		print"<td class=grid-data-c>$test_name</td>". NEWLINE;
		#print"<td class=grid-data-c>$version_no</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	print"<br>". NEWLINE;

	print"<table class=width95>". NEWLINE;
		print"<tr>". NEWLINE;

				print"<td class=grid-header-l width='25%'>". NEWLINE;
				print lang_get('test_performance') ." | ". lang_get('man_auto');
				print"</td>". NEWLINE;
				print"<td class=grid-data-l width='25%'>". NEWLINE;
				print html_print_testtype_icon( $manual, $automated, $performance );
				print"</td>". NEWLINE;
				print"<td class=grid-header-l width='25%'>". NEWLINE;
				print lang_get('last_updated_by');
				print"</td>". NEWLINE;
				print"<td class=grid-data-l width='25%'>". NEWLINE;
				print $last_updated_by;
				print"</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('test_status') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$test_status</td>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('last_updated_date') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$last_updated_date</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('area_tested') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$area_tested</td>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('assigned_to') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$assigned_to</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('testtype') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$test_type</td>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('test_assigned_by') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$assigned_by</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('qa_owner') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$qa_owner</td>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('date_assigned') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$dateassigned</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('ba_owner') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$ba_owner</td>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('date_expected') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$dateexpcomplete</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('tester') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$tester</td>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('date_complete') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$dateactcomplete</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('priority') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$test_priority</td>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('signoff_by') ."</td>". NEWLINE;
				print"<td class=grid-data-l></td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				//if($show_priority == 'Y') {
				print"<td class=grid-header-l>". lang_get('duration') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$duration</td>";
				/*}
				else {
					"We need to get rid of the show_priority session var and make it standard<br>";
				}
				*/
				print"<td class=grid-header-l>". lang_get('ba_signoff_date') ."</td>". NEWLINE;
				print"<td class=grid-data-l>$signoff_date</td>". NEWLINE;
			print"</tr>". NEWLINE;

			print"<tr>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('test_purpose') ."</td>". NEWLINE;
				print"<td class=grid-data-l>";
				print nl2br(replace_uri($test_purpose));
				print"</td>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('email_ba_owner') ."</td>". NEWLINE;
				print"<td class=grid-data-l>";
				print ("Y" == $email_ba_owner) ? "Yes" : "No";
				print"</td>". NEWLINE;
			print"</tr>". NEWLINE;

			print"<tr>". NEWLINE;	
				print"<td class=grid-header-l>". lang_get('autopass') ."</td>". NEWLINE;
				print"<td class=grid-data-l>". NEWLINE;
				print ("Y" == $autopass) ? "Yes" : "No";
				print"</td>". NEWLINE;
				print"<td class=grid-header-l>". lang_get('email_qa_owner') ."</td>". NEWLINE;
				print"<td class=grid-data-l>";
				print ("Y" == $email_qa_owner) ? "Yes" : "No";
				print"</td>". NEWLINE;
				print"</tr>". NEWLINE;
		
				/*print"<td class=grid-data-l colspan=3>". NEWLINE;
				print nl2br($test_purpose);
				print"</td>". NEWLINE;
			print"</tr>". NEWLINE;*/

			print"<tr>". NEWLINE;
				print"<td class=grid-header-l>". NEWLINE;
				print lang_get('test_comments');
				print"</td>". NEWLINE;
				print"<td class=grid-data-l colspan=3>". NEWLINE;
				print nl2br(replace_uri($test_comments));
				print"</td>". NEWLINE;
			print"</tr>". NEWLINE;


	print"</table>". NEWLINE;

	print"<table class=hide95>". NEWLINE;
	print"<tr>". NEWLINE;

		# UPDATE TEST
		print"<td class=center width='50%'>". NEWLINE;
		print"<form method=post action='$update_action_page'>". NEWLINE;
		print"<input type='submit' value='". lang_get('update_test') ."'>". NEWLINE;
		print"</form>". NEWLINE;
		print"</td>". NEWLINE;

		# ADD VERSION
		/*
		print"<td class=center width='33%'>". NEWLINE;
		print"<form name=testdetail method=post action='$add_version_page'>". NEWLINE;
		print"<input type='submit' value='". lang_get('add_version') ."'>". NEWLINE;
		print"</form>". NEWLINE;
		print"</td>". NEWLINE;
		*/

		# DELETE TEST

		# disable delete button if user does not have the rights
		if( $user_has_delete_rights ) {
			$delete_disabled = "";
		} else {
			$delete_disabled = "disabled";
		}

		print"<td class=center width='50%'>". NEWLINE;
		print"<form method=post action='$delete_page'>". NEWLINE;
		print"<input type='submit' value='". lang_get('delete_test_btn') ."' $delete_disabled>". NEWLINE;
		print"<input type='hidden' name='r_page' value='$test_page'>";
		print"<input type='hidden' name='f' value='delete_test'>";
		print"<input type='hidden' name='id' value='$test_id'>";
		print"<input type='hidden' name='msg' value='210'>";

		print"</form>". NEWLINE;
		print"</td>". NEWLINE;
		
		# COPY TEST
		
		# disable copy button if user has not project manager rigths
		if( $project_manager ) {
			$copy_disabled = "";
		} else {
			$copy_disabled = "disabled";
		}
		$s_user_projects = session_get_user_projects_excluding_current_project($project_name);
		print"<td class=center width='50%'>". NEWLINE;
		print"<form method=post action='$copy_action_page'>". NEWLINE; // TODO
		print"<table class=hide95>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=center width='50%'>". NEWLINE;
		print"<input type='submit' value='". lang_get('copy_test_btn') ."' $copy_disabled>". NEWLINE;
		print"</td>". NEWLINE;
		print"<td class=center width='50%'>". NEWLINE;
		print"<select name='copy_to_project' size='1' $copy_disabled>". NEWLINE;
		html_print_list_box_from_array(	$s_user_projects, 1 );
        print"</select>". NEWLINE;
        print"</td>". NEWLINE;
        print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"<input type='hidden' name='test_id' value='$test_id'>";
		print"<input type='hidden' name='test_name' value='$test_name'>";
		print"<input type='hidden' name='test_purpose' value='$test_purpose'>";
		print"<input type='hidden' name='test_comments' value='$test_comments'>";
		print"<input type='hidden' name='manual' value='$manual'>";
		print"<input type='hidden' name='performance' value='$performance'>";
		print"<input type='hidden' name='test_status' value='$test_status'>";
		print"<input type='hidden' name='duration' value='$duration'>";
		print"<input type='hidden' name='email_ba_owner' value='$email_ba_owner'>";
		print"<input type='hidden' name='email_qa_owner' value='$email_qa_owner'>";
		print"<input type='hidden' name='autopass' value='$autopass'>";
		print"<input type='hidden' name='automated' value='$automated'>";
        print"</form>". NEWLINE;
		print"</td>". NEWLINE;
        
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

} else {
    print"<br><span class='print'>" . lang_get('no_test_suites') . $project_name . "</span>";
}


# Begin section for Test Steps - Documentation - Requirement Association
print"<br>";

print"<table class=hide100>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
test_sub_menu_print( $test_id, $project_id, $page, $s_tab );
print"</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<tr>". NEWLINE;
print"<td align=center>". NEWLINE;

if( $user_has_delete_rights ) {
	switch( $s_tab ) {
	case 3:
		print"[ <a href='test_req_assoc_page.php?test_id=$test_id'>".lang_get("edit_assoc")."</a> ]". NEWLINE;
		break;
	}
} else {

	print"&nbsp;";
}

print"</td>". NEWLINE;
print"</tr>". NEWLINE;

//error_report_check( $_GET );

print"<tr>". NEWLINE;
print"<td align=center>". NEWLINE;

switch( $s_tab ) {

	# TEST STEPS
	case '1':

		if( IMPORT_EXPORT_TO_EXCEL ) {
			print"[ <a href='test_step_import_csv_page.php'>".lang_get('import_teststeps_excel')."</a> ]". NEWLINE;
			print"[ <a href='csv_export.php?table=test_steps'>".lang_get('export_teststeps_excel')."</a> ]". NEWLINE;
		}
		else {
			print"[ <a href='test_step_import_csv_page.php'>".lang_get('import_teststeps_csv')."</a> ]". NEWLINE;
			print"[ <a href='csv_export.php?table=test_steps'>".lang_get('export_teststeps_csv')."</a> ]". NEWLINE;
		}
		

		print"<form method=post name='test_steps' action='$redirect_url' id='form_order'>". NEWLINE;
		print"<table class=hide100>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		$rows_test_steps = test_get_test_steps( $test_id, $s_page_number, $csv_name=null ,$order_by,$order_dir);
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;

		# Find out if there are existing test steps
		#$row = test_get_test_steps( $test_id );
		$num_test_steps = sizeof($rows_test_steps);

		if( $num_test_steps != '0' ) {  # Display test steps if they exist

			print"<table class='width100' rules='cols'>". NEWLINE;
			print"<tr class='tbl_header'>". NEWLINE;
			#html_tbl_print_header( lang_get('step_no') );
			#html_tbl_print_header( lang_get('step_action') );
			#html_tbl_print_header( lang_get('test_inputs') );
			#html_tbl_print_header( lang_get('step_expected') );
			#html_tbl_print_header( lang_get('step_edit') );
			html_tbl_print_header( lang_get('step_no') );
			html_tbl_print_header(lang_get('step_action') );
			html_tbl_print_header( lang_get('test_inputs') );
			html_tbl_print_header( lang_get('step_expected') );
			html_tbl_print_header( lang_get('step_edit') );
			print"</tr>". NEWLINE;

			$row_style = '';
			foreach($rows_test_steps as $row_test_step ) {

				$row_test_step_id	= $row_test_step[TEST_STEP_ID];
				$step_number     	= $row_test_step[TEST_STEP_NO];
				$step_action     	= $row_test_step[TEST_STEP_ACTION];
				$step_test_inputs  	= $row_test_step[TEST_STEP_TEST_INPUTS];
				$step_expected   	= $row_test_step[TEST_STEP_EXPECTED];
				$info_step 			= $row_test_step[TEST_STEP_INFO_STEP];

				$info_step_class = "";
				if( $info_step=="Y" ) {
					$info_step_class = "class='test-step-info'";
				}
				$row_style = html_tbl_alternate_bgcolor( $row_style );
				print"<tr class='$row_style'>". NEWLINE;
				print"<td align=center><div $info_step_class>$step_number</div></td>". NEWLINE;
				html_print_teststep_with_hyperlinks($info_step_class, $step_action, $step_test_inputs, $step_expected);
				
				#print"<td align=left><div $info_step_class>$step_action</div></td>". NEWLINE;
				#print"<td align=left><div $info_step_class>$step_test_inputs</div></td>". NEWLINE;
				#print"<td align=left><div $info_step_class>$step_expected</div></td>". NEWLINE;
				print"<td class='tbl-c'>";
					print"<a href='$row_test_step_edit_page?test_id=$test_id&amp;test_step_id=$row_test_step_id'>[". lang_get('edit_link') ."]</a>&nbsp". NEWLINE;
					print"<form name='delete_test_step' method=post action='$delete_page'>". NEWLINE;
					print"<input type='submit' name='delete' value='[". lang_get( 'delete' ) ."]' class='page-numbers'>";
					print"<input type='hidden' name='r_page' value='$redirect_url'>". NEWLINE;
					print"<input type='hidden' name='f' value='delete_test_step'>". NEWLINE;
					print"<input type='hidden' name='id' value='$row_test_step_id'>". NEWLINE;
					print"<input type='hidden' name='msg' value='100'>". NEWLINE;
					print"</form>". NEWLINE;
				print"</td>". NEWLINE;
				print"</tr>". NEWLINE;

			}

			print"</table>". NEWLINE;
			print"<br>". NEWLINE;
			# RENUMBER TEST STEPS BUTTON
			print"<form action=$row_test_step_renumber_page method=post>". NEWLINE;
			print"<input type='submit' name=renumber value='". lang_get( 'renumber_steps' ) ."'>". NEWLINE;
			print"<input type='hidden' name='test_id' value='$test_id'>". NEWLINE;
			//print"<input type='hidden' name='test_version_id' value='$test_version_id'>";
			print"</form>". NEWLINE;

		}

		print"<br><br>". NEWLINE;



		# ------------------------------------------------------
		# Begin Test Step Form that allows users to add steps
		# ------------------------------------------------------
		print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;

		print"<table class='width100'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;

		print"<a name='steps' id='steps'></a>". NEWLINE;

		print"<form method=post name='test_step_add' action=$row_test_step_add_action_page>". NEWLINE;
		//print"<td><input type='hidden' name='test_version_id' value='$test_version_id'></td>";

		print"<table class='inner'>". NEWLINE;
		print"<tr>". NEWLINE;
			print"<td colspan=2 align=left class='form-lbl-l'>". lang_get('add_test_step') ."</td>". NEWLINE;
		print"</tr>";
		# ADD STEP AFTER...
		print"<tr>". NEWLINE;
			print"<td class='form-lbl-r'>". lang_get('position') ."</td>". NEWLINE;

			$test_step_tbl  = TEST_STEP_TBL;
			$f_test_id		= TEST_STEP_TEST_ID;
			$f_test_step_no	= TEST_STEP_NO;

			print"<td align=left>". NEWLINE;
			print"<select name='location'>";
			print"<option value='end'>At end of table</option>";
				$q = "SELECT * FROM $test_step_tbl WHERE $f_test_id = '$test_id' ORDER BY $f_test_step_no";
				$rs = db_query( $db, $q );
				while($rw = db_fetch_row( $db, $rs ) ){
					print"<option value=$rw[$f_test_step_no]>After Step $rw[$f_test_step_no]</option>";
				}
			print"</select>". NEWLINE;
			print"</td>". NEWLINE;
		print"</tr>";

		print"<tr>". NEWLINE;
			print"<td class='form-lbl-r'>". lang_get('info_step') ."</td>". NEWLINE;
			print"<td align=left><input type=checkbox name=info_step></td>";
		print"</tr>";

		# ACTION
		$action = session_validate_form_get_field("step_action_required", "", session_use_FCKeditor());
		print"<tr>";
			print"<td class='form-lbl-r'>". lang_get('step_action') ." <span class='required'>*</span></td>". NEWLINE;
			print"<td align=left>";
			html_FCKeditor("step_action_required", 600, 200, $action);
			print"</td>". NEWLINE;
		print"</tr>";

		# TEST INPUTS
		$test_inputs = session_validate_form_get_field("step_test_inputs", "", session_use_FCKeditor());
						print"<tr>";
							print"<td class='form-lbl-r'>". lang_get('test_inputs');
							//<span class='required'>*</span></td>". NEWLINE;
							print"<td align=left>";
							html_FCKeditor("step_test_inputs", 600, 200, $action);
							print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		
		# EXPTECTED RESULT
		$expected_result = session_validate_form_get_field("step_expected_required", "", session_use_FCKeditor());
		print"<tr>";
			print"<td class='form-lbl-r'>". lang_get('step_expected') ." <span class='required'>*</span></td>". NEWLINE;
			print"<td align=left>";
			html_FCKeditor("step_expected_required", 600, 200, $expected_result);
			print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		print"<tr>". NEWLINE;
			print"<td><input type='hidden' name='test_id' value='$test_id'></td>". NEWLINE;
		print"</tr>". NEWLINE;

		util_add_spacer();

		# SUBMIT BUTTON
		print"<tr>". NEWLINE;
		print"<td colspan='3' class=center><input type='submit' value='". lang_get('add_step') ."'></td>". NEWLINE;
		print"</tr>". NEWLINE;

		util_add_spacer();

		print"</table>". NEWLINE;
		print"</form>". NEWLINE;

		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;

		break;

	# SUPPORTING DOCUMENTATION
	# FILE UPLOAD
	case '2':

		$upload_rows = test_get_uploaded_documents($test_id);
		$message = lang_get('delete_confirm_suppdoc');

		if( !empty($upload_rows) ) {

			print"<br>";
			print"<table class=width95>";
			print"<tr>";
			print"<td>";
				print"<table class=inner>";
				print"<tr>";
					print"<td class=grid-header-c>". lang_get('file_type') ."</td>";
					print"<td class=grid-header-c>". lang_get('file_name') ."</td>";
					print"<td class=grid-header-c>". lang_get('version') ."</td>";
					print"<td class=grid-header-c>". lang_get('view') ."</td>";
					print"<td class=grid-header-c>". lang_get('download') ."</td>";
					print"<td class=grid-header-c>". lang_get('show_history') ."</td>";
					print"<td class=grid-header-c>". lang_get('add_version') ."</td>";
					if($user_has_delete_rights){
						print"<td class=grid-header-c>". lang_get('delete') ."</td>";	
					}
					//print"<td class=grid-header-c>". lang_get('edit') ."</td>";
					print"<td class=grid-header-c>". lang_get('uploaded_by') ."</td>";
					print"<td class=grid-header-c>". lang_get('date_added') ."</td>";
					print"<td class=grid-header-c>". lang_get('info') ."</td>";
					//print"<td class=grid-header-c>". lang_get('delete_latest') ."</td>";
				print"</tr>";

				foreach($upload_rows as $upload_row) {

					$display_name  = $upload_row[MAN_TD_DISPLAY_NAME];
					$man_test_id   = $upload_row[MAN_TD_MANUAL_TEST_ID];
					$doc_detail_row = test_get_uploaded_document_detail($man_test_id);
					$filename      = $doc_detail_row[MAN_TD_VER_FILENAME];
					$comments      = $doc_detail_row[MAN_TEST_DOCS_VERS_COMMENTS];
					$time_stamp    = $doc_detail_row[MAN_TD_VER_TIME_STAMP];
					$uploaded_by   = $doc_detail_row[MAN_TD_VER_UPLOADED_BY];
					$version       = $doc_detail_row[MAN_TD_VER_VERSION];
					$doc_type      = $doc_detail_row[MAN_TEST_DOCS_VERS_MANUAL_DOC_TYPE_NAME];

					$fname = $s_project_properties['test_upload_path'] . $filename;
					$download_filename = urlencode($fname);
					if(IGNORE_VERSION_FILENAME_VALIDATION){
						$file_name = substr($filename,28);	
					}else
						$file_name = $display_name;

					print"<tr>";
						print"<td class=grid-data-c>".html_file_type( $filename )."</td>";
						print"<td class=grid-data-c>$file_name</td>";
						print"<td class=grid-data-c>$version</td>";
						print"<td class=grid-data-c>";
						print"<a href='$fname' target='new'>" . lang_get('view') . "</a>";
						print"</td>";
						print"<td class=grid-data-c>";
						print"<a href='download.php?upload_filename=$download_filename'>" . lang_get('download') . "</a>";
						print"</td>";
						print"<td class=grid-data-c>";
						print"<a href='test_doc_history_page.php?test_id=$test_id&mantestid=$man_test_id'>" . lang_get('show') . "</a>";
						print"</td>";
						print"<td class=grid-data-c>";
						print"<a href='test_add_doc_version_page.php?test_id=$test_id&manual_test_id=$man_test_id'>" . lang_get('add') . "</a>";
						print"</td>";
						if($user_has_delete_rights){
							print"<td class=grid-data-c>".NEWLINE;
							print '<a onclick="return confirmSubmit(\''.$message.'\')" href="test_delete_doc_action.php?test_id='.$test_id. '&manual_test_id='. $man_test_id .'">' . lang_get('delete') . '</a>'.NEWLINE;
							print"</td>";
						}
						//print"<td class=grid-data-c>";
						//print"<a href='test_doc_edit_page.php?mantestid=$man_test_id'>" . lang_get('edit') . "</a>";
						//print"</td>";
						print"<td class=grid-data-c>$uploaded_by</td>";
						print"<td class=grid-data-c>$time_stamp</td>";
						print"<td class=grid-data-c>";

						  if($comments) {
							  print"<img src='". IMG_SRC . "/info.gif' title='$comments'>";
						  }
						  else {
							  print"&nbsp;";
						  }

						print"</td>";
						//print"<td class=grid-data-c>";
						//print"<a href='test_doc_delete_page.php?mantestid=$man_test_id'>" . lang_get('delete') . "</a>";
						//print"</td>";
					print"</tr>";
				}

				print"</table>";
			print"</td>";
			print"</tr>";
			print"</table>";


		}
		else {
			print"<br><span class='print'><div align='center'>" . lang_get('no_documentation') . "</div></span>";
		}

		print"<br>". NEWLINE;
		print"<table class=width95>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
			print"<form enctype=multipart/form-data name=upload action='$new_upload_action_page' method=post>". NEWLINE;
			print"<table class=inner>". NEWLINE;

			print"<tr class=left>". NEWLINE;
				print"<td class=form-header-l colspan=2>" . lang_get('upload_heading') . "</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=form-lbl-r>" . lang_get('file_name') . "</td>". NEWLINE;
				print"<td class=form-data-l><input type='file' name='uploadfile' size='60'></td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=form-lbl-r>" . lang_get('comments') . "</td>". NEWLINE;
				print"<td class=form-data-l><textarea rows='2' cols='45' name='comments'></textarea></td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=form-lbl-r>" .  lang_get('file_type') . "</td>". NEWLINE;
				print"<td class=form-data-l>". NEWLINE;
				print"<select name=doc_type>". NEWLINE;
					$test_types = test_get_test_type( $project_id, $blank=true );
					html_print_list_box_from_array( $test_types );
				print"</select></td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td colspan=2><input type='hidden' name='MAX_FILE_SIZE' value='25000000'></td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"<tr>". NEWLINE;
				print"<td class=center colspan=2><input type='submit' value='Upload'></td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"</table>". NEWLINE;
			print"</form>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"<br>". NEWLINE;
		print"<br>". NEWLINE;

		break;

	# REQ ASSOC
	case '3':

		/*
		print"<br>";
		print"<b><div align='left'>" . lang_get('req_assoc_section') . "</div></b>";
		print"<hr>";
		print"<br>";
		*/
		$associated_row = test_get_associated_requirements($test_id);
		if (!empty($associated_row)) {

		print"<table cellpadding=4 width='90%'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;

		print"<table rules=cols class=width100>". NEWLINE;
		print"<tr>". NEWLINE;
			print"<th class=grid-header-c>". lang_get('req_id') ."</th>";
			print"<th class=grid-header-c>". lang_get('req_name') ."</th>";
			print"<th class=grid-header-c>". lang_get('percent_covered_test') ."</th>";
			/*print"<th class=grid-header-c>";
			print lang_get('edit') . ' ' .lang_get('percent_covered') . $test_name ;
			print"</th>";*/
			if( $user_has_delete_rights ) {
				print"<th class=grid-header-c>". lang_get('delete') ."</th>";
			}
		print"</tr>";

		foreach( $associated_row as $row_associated_reqs ) {
			$assoc_id			= $row_associated_reqs[TEST_REQ_ASSOC_ID];
			$req_id				= $row_associated_reqs[REQ_ID];
			$req_filename		= $row_associated_reqs[REQ_FILENAME];
			$req_assoc_covered	= $row_associated_reqs[TEST_REQ_ASSOC_PERCENT_COVERED];
			$req_id_link		= util_pad_id($req_id);

			$row_style = html_tbl_alternate_bgcolor($row_style);
			print"<tr class='$row_style'>". NEWLINE;
				print"<td class='tbl-c'><a href='requirement_detail_page.php?req_id=$req_id&tab=2'>$req_id_link</a></td>";
				print"<td class='tbl-l'>$req_filename</td>";
				print"<td class='tbl-c'>$req_assoc_covered%</td>";
				//print"<td class='tbl-c'><a href='test_req_edit_coverage_page.php?req_id=$req_id&amp;tab=2'>" . lang_get('edit') ."</a></td>";
				if( $user_has_delete_rights ) {
					print"<td class='tbl-c'><a href='test_delete_assoc_action.php?assoc=req&amp;assoc_id=$assoc_id'>" . lang_get('delete') ."</a></td>";
				}
			print"</tr>";

		}
		print"</table>";
		print"</td>";
		print"</tr>";
		print"</table>";
		}
		else {
			print("<br><div align='left'><span class='print'>" . lang_get('no_req_assoc') . "</span></div>");
		}
		break;
	/*
	case '4':

	print"<br>";
	print"<table class=width95>";
		print"<tr>";
		print"<td>";
			print"<table class=inner>";
			print"<tr>";
				print"<td class=grid-header-c>". lang_get('test_version') ."</td>";
				print"<td class=grid-header-c>". lang_get('created_by') ."</td>";
				print"<td class=grid-header-c>". lang_get('test_created_date') ."</td>";
				print"<td class=grid-header-c>". lang_get('test_status') ."</td>";
				print"<td class=grid-header-c>". lang_get('signoff_by') ."</td>";
				print"<td class=grid-header-c>". lang_get('signoff_date') ."</td>";
				print"<td class=grid-header-c>". lang_get('signoff') ."</td>";
				print"<td class=grid-header-c>". lang_get('active') ."</td>";
				print"<td class=grid-header-c>". lang_get('make_active') ."</td>";
				print"<td class=grid-header-c>". lang_get('view_test') ."</td>";
			print"</tr>";

		$tst_versions = test_get_all_versions( $test_id );

		foreach( $tst_versions as $tst_version ) {

			$tst_version_id		= $tst_version[TEST_VERS_ID];
			$tst_version_no		= $tst_version[TEST_VERS_NUMBER];
			$tst_created_by		= $tst_version[TEST_VERS_AUTHOR];
			$tst_created_date	= $tst_version[TEST_VERS_DATE_CREATED];
			$tst_version_status = $tst_version[TEST_VERS_STATUS];
			$tst_active			= $tst_version[TEST_VERS_ACTIVE];
			$tst_signoff_by		= $tst_version[TEST_VERS_SIGNOFF_BY];
			$tst_signoff_date	= $tst_version[TEST_VERS_SIGNOFF_DATE];

			$signoff_page = "blah.php";
			$active_url = $active_version_page . "?test_id=$test_id&tst_version_id=$tst_version_id";

			print"<tr>". NEWLINE;
				print"<td class='grid-data-c'>$tst_version_no</td>". NEWLINE;
				print"<td class='grid-data-c'>$tst_created_by</td>". NEWLINE;
				print"<td class='grid-data-c'>$tst_created_date</td>". NEWLINE;
				print"<td class='grid-data-c'>$tst_version_status</td>". NEWLINE;
				print"<td class='grid-data-c'>$tst_signoff_by</td>". NEWLINE;
				print"<td class='grid-data-c'>$tst_signoff_date</td>". NEWLINE;
				# SIGN OFF
				if( $tst_signoff_by == '' && $tst_signoff_date == '' ) {
					$signoff_page = 'Blah.php';
					print"<td class='grid-data-c'><a href='$signoff_page'>". lang_get('signoff') ."</a></td>". NEWLINE;
				}
				else {
					print"<td class='grid-data-c'></td>". NEWLINE;
				}
				# ACTIVE
				if( $tst_active == 'Y' ) {
					print"<td class='grid-data-c'>X</td>". NEWLINE;
				}
				else {
					print"<td class='grid-data-c'></td>". NEWLINE;
				}
				# MAKE ACTIVE
				if( $tst_active == 'Y' ) {
					print"<td class='grid-data-c'></td>". NEWLINE;
				}
				else {
					print"<td class='grid-data-c'><a href='$active_url'>". lang_get('make_active') ."</a></td>". NEWLINE;
				}
				# VIEW
				if( $tst_version_no == $version_no ) {
					print"<td class='grid-data-c'></td>". NEWLINE;
				}
				else {
					print"<td class='grid-data-c'><a href='$signoff_page'>". lang_get('view') ."</a></td>". NEWLINE;
				}
			print"</tr>". NEWLINE;
		}

		print"</table>";
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	break;
	*/
}

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</div>";


session_validate_form_reset();


html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_detail_page.php,v $
# Revision 1.17  2009/03/23 11:58:52  cryobean
# encoded the filename for download link on the supporting docs tab. used the function urlencode().
#
# Revision 1.16  2009/01/28 07:15:09  cryobean
# Paging is now enabled for test steps. More than 50 teststeps are now possible.
#
# Revision 1.15  2008/08/07 10:57:51  peter_thal
# Now blanks are replaced with underscores by adding a new supporting doc
#
# Revision 1.14  2008/08/05 10:42:43  peter_thal
# small changes: delete confirm SuppDocs, Error message file upload, disabled sorting teststeps
#
# Revision 1.13  2008/08/04 06:54:58  peter_thal
# added sorting function to several tables
#
# Revision 1.12  2008/07/23 14:53:50  peter_thal
# delete supporting docs feature added (linux/unix)
#
# Revision 1.11  2008/07/09 07:13:25  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.10  2008/01/22 08:17:46  cryobean
# added copy test feature
#
# Revision 1.9  2007/11/15 12:58:48  cryobean
# bugfixes
#
# Revision 1.8  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.7  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.6  2006/06/24 14:34:14  gth2
# updating changes lost with cvs problem.
#
# Revision 1.5  2006/04/05 12:39:30  gth2
# no message
#
# Revision 1.4  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.3  2006/01/16 13:27:45  gth2
# adding excel integration - gth
#
# Revision 1.2  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>