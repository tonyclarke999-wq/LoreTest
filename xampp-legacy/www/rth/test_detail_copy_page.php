<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Add Page
#
# $RCSfile: test_detail_copy_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];

$s_show_options = session_get_show_options();
$show_priority = $s_show_options['show_priority'];


$s_project_properties = session_get_project_properties();

if (isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
} else {
    $is_validation_failure = false;
}

global $db;

html_window_title();
html_print_body();
html_page_title($project_name ." -  ". lang_get( 'copy_test_page' ));
html_page_header( $db, $project_name );
html_print_menu();
test_menu_print($page);

error_report_check( $_GET );

session_validate_form_set($_POST);
$project_id 	= project_get_id(session_validate_form_get_field('copy_to_project'));
$test_id		= session_validate_form_get_field('test_id');
$test_name 		= session_validate_form_get_field('test_name');
$test_purpose	= session_validate_form_get_field('test_purpose');
$test_comments	= session_validate_form_get_field('test_comments');
$test_status	= session_validate_form_get_field('test_status');
$manual			= session_validate_form_get_field('manual');
$performance	= session_validate_form_get_field('performance');
$duration		= session_validate_form_get_field('duration');
$email_ba_owner	= session_validate_form_get_field('email_ba_owner');
$email_qa_owner	= session_validate_form_get_field('email_qa_owner');
$autopass		= session_validate_form_get_field('autopass');
$automated		= session_validate_form_get_field('automated');

print"<p class='warning' align=center>". lang_get('supporting_docs_warning'). "</p>".NEWLINE;

print"<div align=center>". NEWLINE;

print"<form method=post action=test_detail_copy_action.php>". NEWLINE;

# requirement this new test is associated to
if( !empty($_GET["assoc_req"]) ) {

	print"<input type=hidden name=assoc_req value=".$_GET["assoc_req"].">". NEWLINE;
}

print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span><br>". NEWLINE;

print"<table class=width75>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

    print"<table class=inner rules=none border=0>". NEWLINE;

    print"<tr>". NEWLINE;
    print"<td class=form-header-l colspan=2>".lang_get('copy_test')."</td>". NEWLINE;
    print"</tr>". NEWLINE;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('test_name') ."<span class='required'>*</span></td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=text size=67 name=testname_required value='" . session_validate_form_get_field( 'testname_required', $test_name );
        print"'>". NEWLINE;
    print"</td>". NEWLINE;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('test_purpose') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
            print"<textarea rows='4' cols='50' name='testpurpose'>" .
            session_validate_form_get_field( 'testpurpose', $test_purpose );
            print"</textarea>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('test_comments') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<textarea rows='2' cols='50' name='testcomments'>" .
               session_validate_form_get_field( 'testcomments', $test_comments );
        print"</textarea>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('test_status') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'teststatus', $test_status );
		$statuses = test_get_status( $blank=true );
        print"<select name='teststatus' size=1>". NEWLINE;
        #html_print_list_box( $db, TEST_STATUS_TBL, TEST_STATUS_STATUS, $project_id, $selected_value);
		html_print_list_box_from_array( $statuses, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	if ($show_priority == 'Y') {
        print"<tr>". NEWLINE;
        print"<td class=form-lbl-r>". lang_get('priority') ."</td>". NEWLINE;
        print"<td class=form-data-l>". NEWLINE;
            $priorities = test_get_priorities();
            $selected_value = session_validate_form_get_field( 'testpriority' );
            print"<select name='testpriority' size=1>". NEWLINE;
            html_print_list_box_from_array( $priorities, $selected_value);
            print"</select>". NEWLINE;
        print"</td>". NEWLINE;
        print"</tr>". NEWLINE;
    }

	# AREA TESTED
    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('area_tested') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
		$areas_tested = test_get_areas_tested( $project_id, true );
        $selected_value = session_validate_form_get_field( 'testareatested' );
        print"<select name='testareatested' size=1>". NEWLINE;
        html_print_list_box_from_array( $areas_tested, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

	# TEST TYPE
    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('testtype') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'testtype' );
        print"<select name='testtype' size=1>". NEWLINE;
		$test_types = test_get_test_type( $project_id, $blank=true);
        html_print_list_box_from_array( $test_types, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('ba_owner') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'ba_owner' );
        print"<select name='ba_owner' size=1>". NEWLINE;
        $ba_owner = user_get_baowners_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $ba_owner, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;


    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('qa_owner') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'qa_owner');
        print"<select name='qa_owner' size=1>". NEWLINE;
        $qa_owner = user_get_qaowners_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $qa_owner, $selected_value );
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;
    
    
      print"<tr>". NEWLINE;
	    print"<td class=form-lbl-r>". lang_get('test_tester') ."</td>". NEWLINE;
	    print"<td class=form-data-l>". NEWLINE;
	        $selected_value = session_validate_form_get_field( 'tester' );
	        print"<select name='tester' size=1>". NEWLINE;
	        $assign_to_users = user_get_usernames_by_project($project_id, $blank=true);
	        html_print_list_box_from_array( $assign_to_users, $selected_value);
	        //html_print_list_box_with_join( $db, USER_TBL, PROJECT_USER_ASSOC_TBL, USER_UNAME, USER_ID, PROJECT_USER_ASSOC_TBL, PROJ_ID, $project_id, $selected_value);
	        print"</select>". NEWLINE;
	    print"</td>". NEWLINE;
    print"</tr>" ;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('test_assigned_to') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'assigned_to' );
        print"<select name='assigned_to' size=1>". NEWLINE;
        $assign_to_users = user_get_usernames_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $assign_to_users, $selected_value);
        //html_print_list_box_with_join( $db, USER_TBL, PROJECT_USER_ASSOC_TBL, USER_UNAME, USER_ID, PROJECT_USER_ASSOC_TBL, PROJ_ID, $project_id, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>" ;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('test_assigned_by') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        $selected_value = session_validate_form_get_field( 'assigned_by' );
        print"<select name='assigned_by' size=1>". NEWLINE;
        $assign_by_users = user_get_usernames_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $assign_by_users, $selected_value);
        print"</select>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>" ;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('date_assigned') . "</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=text size=10 maxlength=10 name='dateassigned' value=" .
              session_validate_form_get_field( 'dateassigned' );
        print">". NEWLINE;
        print"&nbsp;&nbsp;" . lang_get('correct_date_format');
    print"</td>". NEWLINE;
    print"</tr>" ;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('date_expected') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=text size=10 maxlength=10 name='dateexpcomplete' value=" .
              session_validate_form_get_field( 'dateexpcomplete' );
        print">". NEWLINE;
        print"&nbsp;&nbsp;" . lang_get('correct_date_format');
    print"</td>". NEWLINE;
    print"</tr>" ;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('date_complete') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=text size=10 maxlength=10 name='dateactcomplete' value=" .
              session_validate_form_get_field( 'dateactcomplete' );
        print">". NEWLINE;
        print"&nbsp;&nbsp;" . lang_get('correct_date_format');
    print"</td>". NEWLINE;
    print"</tr>" ;

    print"<tr>". NEWLINE;
    print"<td class='form-lbl-r'>". lang_get('ba_signoff_date') ."</td>". NEWLINE;
    print"<td class='form-data-l'>". NEWLINE;
        print"<input type=text size=10 maxlength=10 name='datebasignoff' value=" .
              session_validate_form_get_field( 'datebasignoff' );
        print">". NEWLINE;
        print"&nbsp;&nbsp;" . lang_get('correct_date_format');
    print"</td>". NEWLINE;
    print"</tr>" ;

	# Duration
	print"<tr>". NEWLINE;
	 print"<td class='form-lbl-r'>". lang_get('duration') ."</td>". NEWLINE;
    print"<td class='form-data-l'><input type='text' size='3' name='test_duration' value=" .
		session_validate_form_get_field( 'test_duration', $duration );
        print">". NEWLINE;
		print"&nbsp;&nbsp;". lang_get('in_minutes') ."</td>". NEWLINE;
	print"</tr>". NEWLINE;

	print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('email_ba_owner') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=checkbox name='chk_email_ba_owner' value='Y'". NEWLINE;
        if (session_validate_form_get_field('chk_email_ba_owner', $email_ba_owner)) print ' checked';
        print">". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>";

	print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('email_qa_owner') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=checkbox name='chk_email_qa_owner' value='Y'". NEWLINE;
        if (session_validate_form_get_field('chk_email_qa_owner', $email_qa_owner)) print ' checked';
        print">". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>";

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('autopass') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=checkbox name='chk_autopass' value='Y'". NEWLINE;
        if (session_validate_form_get_field('chk_autopass', $autopass)) print ' checked';
        print">". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>" ;

    /*print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('manual') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=checkbox name='chk_steps' value='YES'". NEWLINE;
        if (session_validate_form_get_field('chk_steps', $manual)) print ' checked';
        print">". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>" ;

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('automated') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=checkbox name='chk_auto' value='YES'". NEWLINE;
        if (session_validate_form_get_field('chk_auto', $automated)) print ' checked';
        print">". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>" ;*/
    
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

    print"<tr>". NEWLINE;
    print"<td class=form-lbl-r>". lang_get('test_performance') ."</td>". NEWLINE;
    print"<td class=form-data-l>". NEWLINE;
        print"<input type=checkbox name='chk_performance' value='YES'". NEWLINE;
        if (session_validate_form_get_field('chk_performance', $performance)) print ' checked';
        print">". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>" ;

    print"<tr><td class=center colspan=2><input type=submit name='save' value='Save'><br><br></td>". NEWLINE;

    print"</table>". NEWLINE;
    
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</form>". NEWLINE;

print"</div>". NEWLINE;


html_print_footer();

# ---------------------------------------------------------------------
#  $Log: test_detail_copy_page.php,v $
#  Revision 1.3  2008/07/01 11:44:47  peter_thal
#  disabled possibility to select,store and filter both options automated and manual in RTH test category
#
#
#
# ---------------------------------------------------------------------

?>
