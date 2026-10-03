<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Detail Update Page
#
# $RCSfile: test_detail_update_page.php,v $  $Revision: 1.7 $
# ---------------------------------------------------------------------

include"./api/include_api.php";

auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'test_detail_update_action.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id             = $s_project_properties['project_id'];

# Check to see if test_id is in GET (the user has clicked the pencil to go straight to update)
if( isset($_GET['test_id']) ) {
	$s_test_details		= session_set_properties("test", $_GET);
}

$s_test_details		= session_get_properties("test");
$test_id			= $s_test_details['test_id'];
//$test_version_id	= $s_test_details['test_version_id'];

$s_show_options = session_get_show_options();
$show_priority = $s_show_options['show_priority'];

if(isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
}
else
    $is_validation_failure = false;

#global $db;

html_window_title();
html_page_title($project_name ." - ". lang_get('update_test_page') );
html_page_header( $db, $project_name );
html_print_menu();
test_menu_print ($page);
html_print_body();

error_report_check( $_GET );

$row = test_get_detail( $test_id  );

if(!empty($row)) {

	$test_name       = $row[TEST_NAME];
	$ba_owner        = $row[TEST_BA_OWNER];
	$qa_owner        = $row[TEST_QA_OWNER];
	$tester			 = $row[TEST_TESTER];
	$test_type       = $row[TEST_TESTTYPE];
	$manual          = $row[TEST_MANUAL];
	$automated       = $row[TEST_AUTOMATED];
	$area_tested     = $row[TEST_AREA_TESTED];
	$autopass        = $row[TEST_AUTO_PASS];
	$test_purpose    = $row[TEST_PURPOSE];
	$test_comments   = $row[TEST_COMMENTS];
	$test_priority   = $row[TEST_PRIORITY];
	$performance     = $row[TEST_LR];
	$assigned_to     = $row[TEST_ASSIGNED_TO];
	$assigned_by     = $row[TEST_ASSIGNED_BY];
	$dateassigned    = $row[TEST_DATE_ASSIGNED];
	$dateexpcomplete = $row[TEST_DATE_EXPECTED];
	$dateactcomplete = $row[TEST_DATE_COMPLETE];
	$duration		 = $row[TEST_DURATION];
	$test_status     = $row[TEST_STATUS];
	$signoff_date    = $row[TEST_SIGNOFF_DATE];
	$signoff_by		 = $row[TEST_SIGNOFF_BY];
	$email_ba_owner	 = $row[TEST_EMAIL_BA_OWNER];
	$email_qa_owner	 = $row[TEST_EMAIL_QA_OWNER];
	//$test_status     = ${'v_' . TEST_VERS_STATUS};
	//$signoff_date    = ${'v_' . TEST_VERS_SIGNOFF_DATE};
	//$signoff_by		 = ${'v_' . TEST_VERS_SIGNOFF_BY};


	print"<br>";

	print"<div align=center>". NEWLINE;
	print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span><br>";
	print"<div align='center'>". NEWLINE;
	print"<table class=width75>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
		print"<table class=inner rules=none border=0>". NEWLINE;
		print"<form method=post action=$action_page>". NEWLINE;

		print"<input type='hidden' name='current_test_status' value='$test_status'>";

		print"<tr>". NEWLINE;
		print"<td class=print-category colspan=2>".lang_get('update_test')."</td>". NEWLINE;
		print"</tr>". NEWLINE;

		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('test_name') ."<span class='required'>*</span></td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=text size=67 name=testname_required value='" . session_validate_form_get_field('testname_required', $test_name);
			print"'>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('test_purpose') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
				print"<textarea rows='4' cols='50' name='testpurpose'>" .
				session_validate_form_get_field('testpurpose', $test_purpose) ;
				print"</textarea>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;


		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('test_comments') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<textarea rows='2' cols='50' name='testcomments'>" .
				   session_validate_form_get_field('testcomments', $test_comments);
			print"</textarea>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;


		# TEST STATUS
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('test_status') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			$selected_value = session_validate_form_get_field('teststatus', $test_status);
			$statuses = test_get_status( $blank=true );
			print"<select name='teststatus' size=1>";
			html_print_list_box_from_array( $statuses, $selected_value);
			#html_print_list_box( $db, TEST_STATUS_TBL, TEST_STATUS_STATUS, $project_id, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# PRIORITY
		if($show_priority == 'Y') {
			print"<tr>". NEWLINE;
			print"<td class=form-lbl-r>". lang_get('priority') ."</td>". NEWLINE;
			print"<td class=left>". NEWLINE;
				$priorities = test_get_priorities();
				$selected_value = session_validate_form_get_field('testpriority', $test_priority);
				print"<select name='testpriority' size=1>";
				html_print_list_box_from_array( $priorities, $selected_value);
				print"</select>". NEWLINE;
			print"</td>". NEWLINE;
			print"</tr>". NEWLINE;
		}

		# AREA TESTED
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('area_tested') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			$test_areas = test_get_areas_tested( $project_id, $blank=true );
			$selected_value = session_validate_form_get_field('testareatested', $area_tested);
			print"<select name='testareatested' size=1>";
			html_print_list_box_from_array( $test_areas, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# TEST TYPE
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('testtype') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			$test_types = test_get_test_type( $project_id, $blank=true );
			$selected_value = session_validate_form_get_field('testtype', $test_type);
			print"<select name='testtype' size=1>";
			html_print_list_box_from_array( $test_types, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# BA OWNER
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('ba_owner') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			$selected_value = session_validate_form_get_field('ba_owner', $ba_owner);
			print"<select name='ba_owner' size=1>";
			$ba_users = user_get_baowners_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $ba_users, $selected_value);
			//html_print_list_box( $db, TEST_TBL, TEST_BA_OWNER, $project_id, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# QA OWNER
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('qa_owner') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			$selected_value = session_validate_form_get_field('qa_owner', $qa_owner);
			print"<select name='qa_owner' size=1>";
			$qa_users = user_get_qaowners_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $qa_users, $selected_value );
			//html_print_list_box( $db, TEST_TBL, TEST_QA_OWNER, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# TESTER
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('tester') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			$selected_value = session_validate_form_get_field('tester', $tester);
			print"<select name='tester' size=1>";
			$tester_users = user_get_usernames_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $tester_users, $selected_value);
			//html_print_list_box_with_join( $db, USER_TBL, PROJECT_USER_ASSOC_TBL, USER_UNAME, USER_ID, PROJECT_USER_ASSOC_TBL, PROJ_ID, $project_id, $selected_value);
			/*
			print"</select>";
			print '&nbsp;&nbsp;'.lang_get('test_email_person') . "&nbsp;<input type='checkbox' NAME='email' VALUE='YES'>";
			*/
			print"</select>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# ASSIGNED TO
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('test_assigned_to') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			$selected_value = session_validate_form_get_field('assigned_to', $assigned_to);
			print"<select name='assigned_to' size=1>";
			$assign_to_users = user_get_usernames_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $assign_to_users, $selected_value);
			//html_print_list_box_with_join( $db, USER_TBL, PROJECT_USER_ASSOC_TBL, USER_UNAME, USER_ID, PROJECT_USER_ASSOC_TBL, PROJ_ID, $project_id, $selected_value);
			/*
			print"</select>";
			print '&nbsp;&nbsp;'.lang_get('test_email_person') . "&nbsp;<input type='checkbox' NAME='email' VALUE='YES'>";
			*/
			print"</select>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# ASSIGNED BY
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('test_assigned_by') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			$selected_value = session_validate_form_get_field('assigned_by', $assigned_by);
			print"<select name='assigned_by' size=1>";
			$assign_by_users = user_get_usernames_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $assign_by_users, $selected_value);
			//html_print_list_box_with_join( $db, USER_TBL, PROJECT_USER_ASSOC_TBL, USER_UNAME, USER_ID, PROJECT_USER_ASSOC_TBL, PROJ_ID, $project_id, $selected_value);
			print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# DATE ASSIGNED
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('date_assigned') . "</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=text size=10 maxlength=10 name='dateassigned' value=" .
				  session_validate_form_get_field('dateassigned', $dateassigned);
			print">";
			print"&nbsp;&nbsp;" . lang_get('correct_date_format');
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# DATE EXPECTED
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('date_expected') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=text size=10 maxlength=10 name='dateexpcomplete' value=" .
				  session_validate_form_get_field('dateexpcomplete', $dateexpcomplete);
			print">";
			print"&nbsp;&nbsp;" . lang_get('correct_date_format');
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# DATE COMPLETED
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('date_complete') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=text size=10 maxlength=10 name='dateactcomplete' value=" .
				  session_validate_form_get_field('dateactcomplete', $dateactcomplete);
			print">";
			print"&nbsp;&nbsp;" . lang_get('correct_date_format');
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		/*
		# SIGNOFF BY
		print"<tr>";
		print"<td class=form-lbl-r>". lang_get('signoff_by') ."</td>";
		print"<td class=left>";
			$selected_value = session_validate_form_get_field('sign_off_by', $signoff_by);
			print"<select name='signoff_by' size=1>";
			$users = user_get_usernames_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $users, $selected_value);
			print"</select>";
		print"</td>";
		print"</tr>" ;

		# SIGNOFF DATE
		print"<tr>";
		print"<td class=form-lbl-r>". lang_get('ba_signoff_date') ."</td>";
		print"<td class=form-data-l>";
			print"<input type=text size=10 maxlength=10 name='datebasignoff' value=" .
				  session_validate_form_get_field('datebasignoff', $signoff_date);
			print">";
			print"&nbsp;&nbsp;" . lang_get('correct_date_format');
		print"</td>";
		print"</tr>" ;
		*/


		# DURATION
		print"<tr>";
		print"<td class='form-lbl-r'>". lang_get('duration') ."</td>". NEWLINE;
		print"<td class=form-data-l>";
			print"<input type=text size='3' maxlength='3' name='test_duration' value=" .
				  session_validate_form_get_field('test_duration', $duration);
			print">";
			print"&nbsp;&nbsp;" . lang_get('in_minutes');
		print"</td>";
		print"</tr>". NEWLINE;

		# EMAIL BA OWNER
		print"<tr>";
		print"<td class=form-lbl-r>". lang_get('email_ba_owner') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=checkbox name='chk_email_ba_owner' value='Y'";
			if(session_validate_form_get_field('chk_email_ba_owner', $email_ba_owner) && $email_ba_owner == 'Y' ) { print" checked"; }
			print">";
		print"</td>";
		print"</tr>". NEWLINE;

		# EMAIL QA OWNER
		print"<tr>";
		print"<td class=form-lbl-r>". lang_get('email_qa_owner') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=checkbox name='chk_email_qa_owner' value='Y'";
			if(session_validate_form_get_field('chk_email_qa_owner', $email_qa_owner) && $email_qa_owner == 'Y' ) { print" checked"; }
			print">";
		print"</td>";
		print"</tr>". NEWLINE;

		# AUTO-PASS
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('autopass') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=checkbox name='chk_autopass' value='Y'";
			if(session_validate_form_get_field('chk_autopass', $autopass)) print ' checked';
			print">";
		print"</td>";
		print"</tr>" . NEWLINE;

		/*
		# MANAUL TEST
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('manual') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=checkbox name='chk_steps' value='YES'";
			if(session_validate_form_get_field('chk_steps', $manual)) print ' checked';
			print">";
		print"</td>". NEWLINE;
		print"</tr>" . NEWLINE;

		# AUTOMATED TEST
		print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>". lang_get('automated') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=checkbox name='chk_auto' value='YES'";
			if(session_validate_form_get_field('chk_auto', $automated)) print ' checked';
			print">";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;*/
		
		#operation to check which columns are set to 'YES' and to store a specific 
		#value into $manautovalue, to support checking if radio button value changed
		#(concerning function 'session_validate_form_get_field()')
		
		
		if($manual == 'YES'){
			$manual = 'man';
		}
		else if($automated == 'YES'){
			$automated = 'auto';
		}
		
		#radio-buttons to select auto XOR manual
    	print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r>". lang_get('manual') ."</td>". NEWLINE;
    	print"<td class=form-data-l>". NEWLINE;
    		print"<input type=radio name='chk_automanu' value='man'". NEWLINE;
    		if(session_validate_form_get_field('chk-automanu',$manual)) print ' checked';
    		print">". NEWLINE;
    	print"</td>". NEWLINE;
    	print"</tr>";
    	
    	print"<tr>". NEWLINE;
    	print"<td class=form-lbl-r>". lang_get('automated') ."</td>". NEWLINE;
    	print"<td class=form-data-l>". NEWLINE;
    		print"<input type=radio name='chk_automanu' value='auto'". NEWLINE;
    		if(session_validate_form_get_field('chk-automanu',$automated)) print ' checked';
    		print">". NEWLINE;
    	print"</td>". NEWLINE;
    	print"</tr>";
    
    	
		
		

		print"<tr>";
		print"<td class=form-lbl-r>". lang_get('test_performance') ."</td>". NEWLINE;
		print"<td class=left>". NEWLINE;
			print"<input type=checkbox name='chk_performance' value='YES'";
			if(session_validate_form_get_field('chk_performance', $performance)) print ' checked';
			print">";
		print"</td>";
		print"</tr>" . NEWLINE;

		print"<tr><td class=center colspan=2><input type=submit name='save' value='Update'><br><br></td></tr>". NEWLINE;

		print"</form>". NEWLINE;
		print"</table>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"<br>". NEWLINE;
	print"</div>". NEWLINE;
	print"</div>". NEWLINE;

}

html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_detail_update_page.php,v $
# Revision 1.7  2008/08/07 12:07:07  peter_thal
# fixed small bug with ba_owner checkbox
#
# Revision 1.6  2008/07/01 11:44:47  peter_thal
# disabled possibility to select,store and filter both options automated and manual in RTH test category
#
# Revision 1.5  2008/04/29 05:23:13  cryobean
# removed testarea, testtype and qaowner as required fields for a test
#
# Revision 1.4  2007/11/19 13:11:53  cryobean
# added test_area, test_type and qa_owner as required fields for adding and updating tests
#
# Revision 1.3  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.2  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>