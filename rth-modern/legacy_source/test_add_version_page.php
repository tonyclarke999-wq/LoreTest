<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Add Version Page
#
# $RCSfile: test_add_version_page.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";


$page                   = basename(__FILE__);
$action_page            = 'test_add_version_action.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];

$s_show_options = session_get_show_options();
$show_priority = $s_show_options['show_priority'];

$s_project_properties = session_get_project_properties();
$project_id = $s_project_properties['project_id'];

$s_test_details		= session_set_properties("test", $_GET);
$test_id			= $s_test_details['test_id'];
$test_version_id	= $s_test_details['test_version_id'];

$test_name = test_get_name( $test_id );

if (isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
} else {
    $is_validation_failure = false;
}

global $db;

html_window_title();

auth_authenticate_user();

html_page_title($project_name ." -  ". lang_get( 'add_test_version_page' ));
html_page_header( $db, $project_name );

html_print_menu();
test_menu_print ($page);
html_print_body();

error_report_check( $_GET );

print"<br>";

print"<div align=center>";
print "<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span><br>";
print "<div align='center'>";
print"<table class=width75>";
print"<tr>";
print"<td>";
    print"<table class=inner rules=none border=0>";
    print"<form method=post action=$action_page>";

    print"<tr>";
    print"<td class=form-header-l colspan=2>".lang_get('add_test_version')." $test_name</td>";
    print"</tr>";

	# COMMENTS
	$comments = "Release:\nBuild:";
    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('test_comments') ."</td>";
    print"<td class=form-data-l>";
        print "<textarea rows='4' cols='50' name='test_comments'>" .
               session_validate_form_get_field( 'test_comments' );
			   print"$comments";
        print "</textarea>";
    print "</td>";
    print"</tr>";

	# STATUS
    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('test_status') ."</td>";
    print"<td class=form-data-l>";
        $selected_value = session_validate_form_get_field( 'test_status' );
		$statuses = test_get_status( $blank=true );
        print"<select name='test_status' size=1>";
       	html_print_list_box_from_array( $statuses, $selected_value);
        print"</select>";
    print "</td>";
    print"</tr>";
	
	/*
	if ($show_priority == 'Y') {
        print"<tr>";
        print"<td class=form-lbl-r>". lang_get('priority') ."</td>";
        print"<td class=form-data-l>";
            $priorities = test_get_priorities();
            $selected_value = session_validate_form_get_field( 'testpriority' );
            print"<select name='testpriority' size=1>";
            html_print_list_box_from_array( $priorities, $selected_value);
            print"</select>";
        print "</td>";
        print"</tr>";
    }

	# AREA TESTED
    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('area_tested') ."</td>";
    print"<td class=form-data-l>";
        $selected_value = session_validate_form_get_field( 'testareatested' );
        print"<select name='testareatested' size=1>";
        html_print_list_box( $db, AREA_TESTED_TBL, AREA_TESTED_NAME, $project_id, $selected_value);
        print"</select>";
    print "</td>";
    print"</tr>";

	# TEST TYPE
    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('testtype') ."<span class='required'>*</span></td>";
    print"<td class=form-data-l>";
        $selected_value = session_validate_form_get_field( 'testtype_required' );
        print"<select name='testtype_required' size=1>";
		$test_types = test_get_test_type( $project_id, $blank=true);
        html_print_list_box_from_array( $test_types, $selected_value);
        print"</select>";
    print "</td>";
    print"</tr>";

    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('ba_owner') ."</td>";
    print"<td class=form-data-l>";
        $selected_value = session_validate_form_get_field( 'ba_owner' );
        print"<select name='ba_owner' size=1>";
        $ba_owner = user_get_baowners_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $ba_owner, $selected_value);
        print"</select>";
    print "</td>";
    print"</tr>";


    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('qa_owner') ."</td>";
    print"<td class=form-data-l>";
        $selected_value = session_validate_form_get_field( 'qa_owner');
        print"<select name='qa_owner' size=1>";
        $qa_owner = user_get_qaowners_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $qa_owner, $selected_value );
        print"</select>";
    print "</td>";
    print"</tr>";
	*/
    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('test_assigned_to') ."</td>";
    print"<td class=form-data-l>";
        $selected_value = session_validate_form_get_field( 'assigned_to' );
        print"<select name='assigned_to' size=1>";
        $assign_to_users = user_get_usernames_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $assign_to_users, $selected_value);
        //html_print_list_box_with_join( $db, USER_TBL, PROJECT_USER_ASSOC_TBL, USER_UNAME, USER_ID, PROJECT_USER_ASSOC_TBL, PROJ_ID, $project_id, $selected_value);
        print"</select>";
    print "</td>";
    print"</tr>" ;
	
	/*
    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('test_assigned_by') ."</td>";
    print"<td class=form-data-l>";
        $selected_value = session_validate_form_get_field( 'assigned_by' );
        print"<select name='assigned_by' size=1>";
        $assign_by_users = user_get_usernames_by_project($project_id, $blank=true);
        html_print_list_box_from_array( $assign_by_users, $selected_value);
        print"</select>";
    print "</td>";
    print"</tr>" ;

    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('date_assigned') . "</td>";
    print"<td class=form-data-l>";
        print "<input type=text size=10 maxlength=10 name='dateassigned' value=" .
              session_validate_form_get_field( 'dateassigned' );
        print ">";
        print "&nbsp;&nbsp;" . lang_get('correct_date_format');
    print "</td>";
    print"</tr>" ;

    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('date_expected') ."</td>";
    print"<td class=form-data-l>";
        print "<input type=text size=10 maxlength=10 name='dateexpcomplete' value=" .
              session_validate_form_get_field( 'dateexpcomplete' );
        print">";
        print "&nbsp;&nbsp;" . lang_get('correct_date_format');
    print "</td>";
    print"</tr>" ;

    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('date_complete') ."</td>";
    print"<td class=form-data-l>";
        print "<input type=text size=10 maxlength=10 name='dateactcomplete' value=" .
              session_validate_form_get_field( 'dateactcomplete' );
        print ">";
        print "&nbsp;&nbsp;" . lang_get('correct_date_format');
    print "</td>";
    print"</tr>" ;

    print"<tr>";
    print"<td class='form-lbl-r'>". lang_get('ba_signoff_date') ."</td>";
    print"<td class='form-data-l'>";
        print "<input type=text size=10 maxlength=10 name='datebasignoff' value=" .
              session_validate_form_get_field( 'datebasignoff' );
        print ">";
        print "&nbsp;&nbsp;" . lang_get('correct_date_format');
    print "</td>";
    print"</tr>" ;

	# Duration
	print"<tr>";
	 print"<td class='form-lbl-r'>". lang_get('duration') ."</td>";
    print"<td class='form-data-l'><input type='text' size='3' name='test_duration' value=" .
		session_validate_form_get_field( 'test_duration' );
        print ">";
		print"&nbsp;&nbsp;". lang_get('in_minutes') ."</td>";
	print"</tr>";

    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('autopass') ."</td>";
    print"<td class=form-data-l>";
        print "<input type=checkbox name='chk_autopass' value='Y'";
        if (session_validate_form_get_field('chk_autopass')) print ' checked';
        print ">";
    print "</td>";
    print"</tr>" ;

    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('manual') ."</td>";
    print"<td class=form-data-l>";
        print "<input type=checkbox name='chk_steps' value='YES'";
        if (session_validate_form_get_field('chk_steps')) print ' checked';
        print">";
    print "</td>";
    print"</tr>" ;

    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('automated') ."</td>";
    print"<td class=form-data-l>";
        print "<input type=checkbox name='chk_auto' value='YES'";
        if (session_validate_form_get_field('chk_auto')) print ' checked';
        print">";
    print "</td>";
    print"</tr>" ;

    print"<tr>";
    print"<td class=form-lbl-r>". lang_get('test_performance') ."</td>";
    print"<td class=form-data-l>";
        print "<input type=checkbox name='chk_performance' value='YES'";
        if (session_validate_form_get_field('chk_performance')) print ' checked';
        print">";
    print "</td>";
    print"</tr>" ;
	*/
	print"<input type='hidden' name='test_id' value='$test_id'>";
	print"<input type='hidden' name='test_version_id' value='$test_version_id'>";

    print"<tr><td class=center colspan=2><input type=submit name='save' value='". lang_get( 'add') ."'><br><br></td>";

    print"</form>";
    print"</table>";
print"</td>";
print"</tr>";
print"</table>";
print"<br>";
print"</div>";
print"</div>";


html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_add_version_page.php,v $
# Revision 1.2  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>
