<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Page
#
# $RCSfile: results_page.php,v $  $Revision: 1.12 $
# ---------------------------------------------------------------------

if( isset($_POST['mass_update']) ) {

	require_once("results_group_action_page.php");
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

session_validate_form_reset();

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];

if( user_has_rights( $project_id, $user_id, MANAGER ) ) {
	$user_is_manager = true;
}
else{
	$user_is_manager = false;
}

# Links to pages
$page                   	= basename(__FILE__);
$test_page					= "test_manual_test.php";
$results_test_run_page  	= "results_test_run_page.php";
$results_add_run_page		= "results_run_manual_test_page.php";
$result_update_page 		= "results_update_test_result_page.php";
$results_group_action		= "showresults_action_group.php";
$testset_status_page		= "testset_current_status.php";
$testset_signoff_page		= "testset_signoff_page.php";
$testset_lock_page			= "testset_lock_page.php";

# Initialize vars
$i                      = 0;
$num                    = 0;
$row_style              = '';

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('test_results_page') );
html_page_header( $db, $project_name );

# --------------------------------------------------
# Maybe make a get_session_test_results_option function with each of the functions below
# It would then set the release_id, build_id, and testset_id each time the page is called
#----------------------------------------------------
# set the filter session variables if the user submits the filter form

$table_options = session_set_display_options("results", $_POST);
$s_properties = session_set_properties("results", $_GET);


html_print_menu();
html_test_results_menu( $db, $page, $project_id, $s_properties, $table_options );

print"<br>". NEWLINE;

if( !empty( $s_properties['testset_id'] ) && $s_properties['testset_id'] != 'all')
{

    $testset_id = $s_properties['testset_id'];
	// ------------------------------------------------------------
	// run query to make sure there are tests in the testset.
	// if not, dont show all the forms and filters below
	//--------------------------------------------------------------
	$locked 		= testset_get_lock_status($testset_id);
	
	print"<table class='hide100'>". NEWLINE;

	print"<tr>". NEWLINE;
	print"<td width='50%'>&nbsp;</td>". NEWLINE;
	print"<td width='25%' align='right'><a href='results_run_autopass.php?testset_id=$testset_id' target='_blank'>". lang_get('run_autopass') ."</td>". NEWLINE;
	print"<td width='25%' align='right'><a href='results_display_est_test_time.php?testset_id=$testset_id' target='_blank'>". lang_get('est_time') ."</td>". NEWLINE;
	print"</tr>". NEWLINE;

	print"</table>". NEWLINE;

    # -------------- NOTE ------------------------------
    # May need to update the queries for the filters so that they pull back only those values that apply
    # to the tests in the testset.  Requires and INNER JOIN and may require a new list_box function
    # that employs inner joins
    # ----------------------------------------------
    print"<div align=center>". NEWLINE;
    print"<table class=width100>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td>". NEWLINE;
    	print"<form method=post action='$page'>". NEWLINE;
        print"<table class=inner rules=none border=0>". NEWLINE;

        # TITLES FOR HEADER DIALOG
        print"<tr align=left>". NEWLINE;
        print"<td class='form-header-c'>". lang_get('man_auto') ."</td>". NEWLINE;
        print"<td class='form-header-c'>". lang_get('ba_owner') ."</td>". NEWLINE;
        print"<td class='form-header-c'>". lang_get('qa_owner') ."</td>". NEWLINE;
        print"<td class='form-header-c'>". lang_get('testtype') ."</td>". NEWLINE;
        print"<td class='form-header-c'>". lang_get('area_tested') ."</td>". NEWLINE;
        print"<td class='form-header-c'>". lang_get('test_status') ."</td>". NEWLINE;
        print"<td class='form-header-c'>". lang_get('show') ."</td>". NEWLINE;
        print"<td>&nbsp;</td>". NEWLINE;
        print"</tr>". NEWLINE;

        print"<tr>". NEWLINE;

        //$s_TestFormFilterOptions = session_getTestFormFilterOptions();
        //$s_TestTableDisplayOptions = session_getTestTableDisplayOptions();

        # MANUAL/AUTOMATED
        print"<td align='center'>". NEWLINE;
        print"<select name='manual_auto'>". NEWLINE;
        $man_auto = test_get_man_auto_values();
        html_print_list_box_from_array( $man_auto, $selected=$table_options['filter']['manual_auto'] );
        print"</select>". NEWLINE;
        print"</td>". NEWLINE;

        # BA OWNER
        print"<td align='center'>". NEWLINE;
        print"<select name='ba_owner'>". NEWLINE;
        $ba_owners = testset_get_test_testset_value($project_id, $testset_id, TEST_BA_OWNER, $blank=true);
        html_print_list_box_from_array( $ba_owners, $selected=$table_options['filter']['ba_owner'] );
        print"</select>". NEWLINE;
        print"</td>". NEWLINE;

        # QA OWNER
        print"<td align='center'>". NEWLINE;
        print"<select name='qa_owner'>". NEWLINE;
        $qa_owners = testset_get_test_testset_value($project_id, $testset_id, TEST_QA_OWNER, $blank=true);
        html_print_list_box_from_array( $qa_owners, $selected=$table_options['filter']['qa_owner'] );
        print"</select>". NEWLINE;
        print"</td>". NEWLINE;

        # TEST TYPE
        print"<td align='center'>". NEWLINE;
        print"<select name='test_type'>". NEWLINE;
        $test_type = testset_get_test_testset_value($project_id, $testset_id, TEST_TESTTYPE, $blank=true);
        html_print_list_box_from_array( $test_type, $selected=$table_options['filter']['test_type'] );
        print"</select>". NEWLINE;
        print"</td>". NEWLINE;

        # AREA TESTED
        print"<td align='center'>". NEWLINE;
        print"<select name='area_tested'>". NEWLINE;
        $area_tested = testset_get_test_testset_value($project_id, $testset_id, TEST_AREA_TESTED, $blank=true);
        html_print_list_box_from_array( $area_tested, $selected=$table_options['filter']['area_tested'] );
        print"</select>". NEWLINE;
        print"</td>". NEWLINE;

        # TEST STATUS
        print"<td align='center'>". NEWLINE;
        print"<select name='test_status'>". NEWLINE;
        $test_status = testset_get_test_testset_value($project_id, $testset_id, TEST_TS_ASSOC_STATUS, $blank=true);
        html_print_list_box_from_array( $test_status, $selected=$table_options['filter']['test_status'] );
        print"</select>". NEWLINE;
        print"</td>". NEWLINE;

        # PER PAGE
        print"<td align='center'>". NEWLINE;
        print"<input type='text' size='3' maxlength='3' name='per_page' value='" . $table_options['filter']['per_page'] . "'>". NEWLINE;
        print"</td>". NEWLINE;

        print"<td align='center'><input type='submit' value='Filter'></td>". NEWLINE;

        print"</tr>". NEWLINE;
        print"</table>". NEWLINE;

        # ---------------------------------------------------------------------
        # May need to hide release_id, build_id, and testset_id and pass them over as hidden vars
        # ---------------------------------------------------------------------
        print"<input type=hidden name=results_form_filter_value value=true>". NEWLINE;
        print"<input type=hidden name=page_number value=" . $table_options['page_number'] . ">". NEWLINE;
        print"<input type=hidden name=order_by value=" . $table_options['order_by'] . ">". NEWLINE;
        print"<input type=hidden name=order_dir value=" . $table_options['order_dir'] . ">". NEWLINE;
        print"<input type=hidden name=release_id value=" . $s_properties['release_id'] . ">". NEWLINE;
        print"<input type=hidden name=build_id value=" . $s_properties['build_id'] . ">". NEWLINE;
        print"<input type=hidden name=testset_id value=" . $s_properties['testset_id'] . ">". NEWLINE;
        print"</form>". NEWLINE;
    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;
    print"</table>". NEWLINE;
    print"</div>". NEWLINE;
    print"<br>". NEWLINE;

    print"<form method='post' action='$page' name='results_form' id='form_order'>". NEWLINE;

    $row = results_filter_rows(	$project_id,
    							$table_options['filter']['manual_auto'],
    							$table_options['filter']['ba_owner'],
    							$table_options['filter']['qa_owner'],
    							$table_options['filter']['test_type'],
    							$table_options['filter']['area_tested'],
    							$table_options['filter']['test_status'],
    							$table_options['filter']['per_page'],
    							$table_options['order_by'],
    							$table_options['order_dir'],
    							$table_options['page_number'],
    							$s_properties['release_id'],
    							$s_properties['build_id'],
    							$s_properties['testset_id'] );

    $page_count = ceil($num / $table_options['filter']['per_page'] );

    $order_by = $table_options['order_by'];
    $order_dir = $table_options['order_dir'];

	if( $row ) {
		if($locked){
			print"<h3 class='hint'> <img src='images/locked.png' alt='locked'> Testset locked</h3>". NEWLINE;
		}
		print"<table class='sortable' rules=cols>". NEWLINE;
		print"<thead>".NEWLINE;
		print"<tr>". NEWLINE;

		#this column is only diplayed, if user is manager
		if( $user_is_manager && !$locked){
			print"<th class='unsortable'></th>". NEWLINE;	
		}
		
		#html_tbl_print_header( lang_get('id'), TEST_ID, $order_by, $order_dir );
		html_tbl_print_sortable_header( lang_get('id'),TEST_ID, $order_by, $order_dir );
		print"<th class='unsortable'></th>". NEWLINE;
		html_tbl_print_sortable_header( lang_get('test_name'), TEST_NAME, $order_by, $order_dir );
		html_tbl_print_sortable_header( lang_get('ba_owner'), TEST_BA_OWNER, $order_by, $order_dir );
		html_tbl_print_sortable_header( lang_get('qa_owner'), TEST_QA_OWNER, $order_by, $order_dir );
		html_tbl_print_sortable_header( lang_get('testtype'), TEST_TESTTYPE, $order_by, $order_dir );
		html_tbl_print_sortable_header( lang_get('area_tested'), TEST_AREA_TESTED, $order_by, $order_dir );
		html_tbl_print_sortable_header( lang_get('test_run') );
		html_tbl_print_sortable_header( lang_get('tester'), TEST_TS_ASSOC_TBL.".".TEST_TS_ASSOC_ASSIGNED_TO,$order_by, $order_dir);
		html_tbl_print_sortable_header( lang_get('info') );
		print"<th class='unsortable'></th>". NEWLINE;
		#html_tbl_print_header( lang_get('bug') );
		html_tbl_print_sortable_header( lang_get('test_status'), TEST_TS_ASSOC_STATUS, $order_by, $order_dir );
		if(!$locked){
			html_tbl_print_header_not_sortable( lang_get('run_test') );
			html_tbl_print_header_not_sortable( lang_get('update') );
		}
		print"</tr>". NEWLINE;
		print"</thead>".NEWLINE;
		print"<tbody>".NEWLINE;
		
		#html_tbl_print_header( lang_get('test_name') );
		#html_tbl_print_header( lang_get('ba_owner') );
		#html_tbl_print_header( lang_get('qa_owner') );
		#html_tbl_print_header( lang_get('testtype') );
		#html_tbl_print_header( lang_get('area_tested') );	
		#html_tbl_print_header( lang_get('test_doc') );
		#html_tbl_print_header( lang_get('priority') );
		#if($s_show_priority == 'Y')
			#html_tbl_print_header( lang_get('priority') );
		//print_r($row);
		//foreach( $row as $key => $value ) {
			//print"key = $key & value = $value<br>". NEWLINE;
		//}
		foreach( $row as $row_detail ) {

		$test_id                = $row_detail[TEST_ID];
		$test_name              = $row_detail[TEST_NAME];
		$manual                 = $row_detail[TEST_MANUAL];
		$automated              = $row_detail[TEST_AUTOMATED];
		$auto_pass              = $row_detail[TEST_AUTO_PASS];
		$ba_owner               = $row_detail[TEST_BA_OWNER];
		$qa_owner               = $row_detail[TEST_QA_OWNER];
		$test_type              = $row_detail[TEST_TESTTYPE];
		$area_tested            = $row_detail[TEST_AREA_TESTED];
		$priority               = $row_detail[TEST_PRIORITY];
		$test_ts_assoc_id		= $row_detail[TEST_TS_ASSOC_ID];
		$assigned_to            = $row_detail[TEST_TS_ASSOC_ASSIGNED_TO];
		$comments               = $row_detail[TEST_TS_ASSOC_COMMENTS];
		$testset_status         = $row_detail[TEST_TS_ASSOC_STATUS];

		$row_style = html_tbl_alternate_bgcolor( $row_style );
		print"<tr class='$row_style'>". NEWLINE;
		
		#Testoutput, not needed
		#print"<p>Userid:$user_id Projektid:$project_name userismanager:$manager</p>".NEWLINE;
		
		#print"<tr>".NEWLINE;
		
		# this column is only displayed if user is manager
		if( $user_is_manager && !$locked){
			print"<td><input type='checkbox' name='row_results_arr[{$test_id}]'></td>". NEWLINE;
		}
		print"<td class='tbl-l'>$test_id</td>". NEWLINE;
		print"<td class='tbl-l' nowrap>".html_print_testtype_icon( $manual, $automated)."</td>". NEWLINE;
		print"<td class='tbl-l' >$test_name</td>". NEWLINE;
		#print"<td class='left'>$auto_pass</td>". NEWLINE;
		print"<td class='tbl-l'>$ba_owner</td>". NEWLINE;
		print"<td class='tbl-l'>$qa_owner</td>". NEWLINE;
		print"<td class='tbl-l' nowrap>$test_type</td>". NEWLINE;
		print"<td class='tbl-l' nowrap>$area_tested</td>". NEWLINE;
		#print"<td class='tbl-c'><a href='$test_page?test_id=$test_id' target='_blank'>". lang_get('docs_link') ."</a></td>". NEWLINE;
		#if($s_show_priority == 'Y')
			#print"<td class='left'>$priority</td>". NEWLINE;
		print"<td class='tbl-c'><a href='$results_test_run_page?test_id=$test_id&amp;testset_id=$s_properties[testset_id]'>". lang_get('results_link') ."</a></td>". NEWLINE;
		print"<td class='tbl-l'>$assigned_to</td>". NEWLINE;
		# -------- Comment Icon ----------
		if( !empty($comments) ) {
			print"<td class='tbl-c'><img src='images/info.gif' title='$comments'></td>". NEWLINE;
		}
		else {
			print"<td>&nbsp;</td>". NEWLINE;
		}
		# ------- Test Status Icon ---------
		print"<td class='tbl-c'>".html_teststatus_icon($testset_status)."</td>". NEWLINE;

		print"<td class='tbl-l' nowrap>$testset_status</td>". NEWLINE;
		# -------- Manual Test Run link -----------
		if(!$locked){
			if($manual == 'YES' ) {
				print"<td class='tbl-c'><a href='$results_add_run_page?test_id=$test_id&amp;testset_id=$s_properties[testset_id]'>". lang_get('run_test') ."</a></td>". NEWLINE;  // &amp;testsetname=$testsetname
			}
			else {
				print"<td class='tbl-c'>&nbsp;</td>". NEWLINE;
			}
			print"<td class='tbl-c'><a href='$result_update_page?test_id=$test_id&amp;testset_id=$s_properties[testset_id]'>". lang_get('update') ."</a></td>". NEWLINE;  // &amp;testsetname=$testsetname
		}
		print"</tr>". NEWLINE;

		}
		print"</tbody>".NEWLINE;
		print"</table>". NEWLINE;
		
		#option is displayed,if user is manager
		if( $user_is_manager && !$locked){
			print"<table>". NEWLINE;
			print"<tr>". NEWLINE;
			print"<td align='left'>". NEWLINE;
			if( session_use_javascript() ) {
				print"<input id=select_all type=checkbox name=thispage onClick='checkAll( this )'>\n". NEWLINE;
				print"<label for=select_all>".lang_get("select_all")."</label>\n". NEWLINE;
			}
			print"<select name='action'>\n". NEWLINE;
			$selected_value = '';
			$test_run_status = results_get_teststatus_by_project( $project_id );
			html_print_list_box_from_array( $test_run_status, $selected_value);
			print"</select>\n". NEWLINE;
			print"</td>\n". NEWLINE;
			print"<td><input type='hidden' name='testset_id' value='$testset_id'></td>\n". NEWLINE;
			print"<td><input type='submit' name=mass_update value='OK'></td>\n". NEWLINE;
			print"</table>\n". NEWLINE;
		}

	} else {

		print html_no_records_found_message( lang_get("no_tests_testset") );
	}

    print"</form>\n". NEWLINE;

}


# display all test sets if the user has selected a build id
elseif( !empty( $s_properties['build_id'] ) && $s_properties['build_id'] != 'all') {

    # Move this above the if loop so it can be used by all queries on the page
    $testset_tbl                = TS_TBL;
    $db_testset_id              = TS_TBL .".". TS_ID;
    $db_testset_name            = TS_TBL .".". TS_NAME;
    $db_testset_date_created    = TS_TBL .".". TS_DATE_CREATED;
    $db_testset_desc            = TS_TBL .".". TS_DESCRIPTION;
    $db_testset_status          = TS_TBL .".". TS_STATUS;
    $db_testset_signoff_by      = TS_TBL .".". TS_SIGNOFF_BY;
    $db_testset_signoff_date    = TS_TBL .".". TS_SIGNOFF_DATE;
    $db_testset_comments        = TS_TBL .".". TS_SIGNOFF_COMMENTS;
    $db_testset_orderby         = TS_TBL .".". TS_ORDERBY;
    $db_testset_build_id        = TS_TBL .".". TS_BUILD_ID;
    $db_testset_lock_date		= TS_TBL .".". TS_LOCKCHANGE_DATE;
    $db_testset_lock_by			= TS_TBL .".". TS_LOCK_BY;
    $db_testset_lock_comment	= TS_TBL .".". TS_LOCK_COMMENT;


    $q = "SELECT $db_testset_id, $db_testset_build_id, $db_testset_name, $db_testset_date_created, $db_testset_desc, $db_testset_status, $db_testset_signoff_by, $db_testset_signoff_date, $db_testset_comments, $db_testset_orderby, $db_testset_lock_by, $db_testset_lock_date, $db_testset_lock_comment FROM $testset_tbl WHERE $db_testset_build_id = '$s_properties[build_id]' ORDER BY $db_testset_orderby ASC". NEWLINE;
    $rs = db_query( $db, $q );
    $num = db_num_rows( $db, $rs );
    //print"$q". NEWLINE;

    # QUERY FOR THE MAX TestSetOrderBy. USED IN TABLE FORMATTING
    $q_orderby = "SELECT MAX($db_testset_orderby) FROM $testset_tbl WHERE $db_testset_build_id = '$s_properties[build_id]'". NEWLINE;
    $max_orderby = db_get_one($db, $q_orderby); //->GetOne($q_orderby);
    //print"$q_orderby<br>". NEWLINE;

        if( $num > 0 ) {

            //print"<H3 align=center>Test Plan for Release: <B> $row_build[ReleaseName] </B> and Build:  <B> $row_build[BuildName] </B> </H3>". NEWLINE;
            print"<p> The following Test Plan lists the different types/ stages of testing that are required in order to ensure that the application is adequately tested. Each stage of testing should be signed off to provide assurance and traceability that the application is ready to be released.</p>". NEWLINE;

            # simplify the url used later on the page for href
            $query_string = "release_id=". $s_properties['release_id'] ."&amp;build_id=". $s_properties['build_id'];

            print"<br>". NEWLINE;
            print"<div align='center'>". NEWLINE;
            print"<table class='width100' rules='cols'>". NEWLINE;
            print"<tr class='tbl_header'>". NEWLINE;
            html_tbl_print_header( lang_get('id') );
            if( $max_orderby > 1 ) {
                html_tbl_print_header( lang_get('up') );
                html_tbl_print_header( lang_get('down') );
            }
            html_tbl_print_header( lang_get('testset_name') );
            html_tbl_print_header( lang_get('date_created') );
            html_tbl_print_header( lang_get('description') );
            html_tbl_print_header( lang_get('status') );
            # page not implemented yet, column disabled!
            #html_tbl_print_header( lang_get('detail') ); 
            html_tbl_print_header( lang_get('locked_by'));
            html_tbl_print_header( lang_get('lock_date'));
            html_tbl_print_header( lang_get('lock_comment'));
            if($user_is_manager){
            	html_tbl_print_header( lang_get('lock'));
            }
            html_tbl_print_header( lang_get('signed_off_by') );
            html_tbl_print_header( lang_get('sign_off_date') );
            html_tbl_print_header( lang_get('comments') ); 
            html_tbl_print_header( lang_get('sign_off') );
            print"</tr>". NEWLINE;



            while( $row = db_fetch_row( $db, $rs ) ) {

                $testset_id             = $row[TS_ID];
                $testset_name           = $row[TS_NAME];
                $testset_date_created   = $row[TS_DATE_CREATED];
                $testset_description    = $row[TS_DESCRIPTION];
                $testset_status         = $row[TS_STATUS];
                $testset_signoff_date   = $row[TS_SIGNOFF_DATE];
                $testset_signoff_by     = $row[TS_SIGNOFF_BY];
                $testset_comments       = $row[TS_SIGNOFF_COMMENTS];
                $testset_orderby        = $row[TS_ORDERBY];
                $testset_lockcomment	= $row[TS_LOCK_COMMENT];
                $testset_lock_by		= $row[TS_LOCK_BY];
                $testset_lock_date		= $row[TS_LOCKCHANGE_DATE];
                $locked					= testset_get_lock_status($testset_id);
                
                if($locked){
                	$lock_link = 'unlock';
                	$lockdatetext = 'locked_on';
                }else{
                	$lock_link = 'lock';
                	$lockdatetext = 'unlocked_on';
                }

                # Format testset_id and date.  Alternate bg_color
                $formatted_testset_id  =  sprintf("%05s",trim( $testset_id ) );
                $formatted_date_created = substr($testset_date_created, 0, 10);
                $formatted_signoff_date = substr($testset_signoff_date, 0, 10);
                $formatted_lock_date	= substr($testset_lock_date, 0, 10);
                $row_style = html_tbl_alternate_bgcolor( $row_style );


                # Display table data
                print"<tr class='$row_style'>". NEWLINE;
                $number_of_tests = admin_count_tests_in_testset($testset_id);
                if ($number_of_tests > 0) { 
                print"<td align='center'><a href='$page?$query_string&amp;testset_id=$testset_id'>$formatted_testset_id</a></td>". NEWLINE;
                }
                else { print"<td align='center'>$formatted_testset_id</td>". NEWLINE;
                }
                # Remove up arrow from the first record and down arrow from the last record if there is more than one testset
                if( $max_orderby > 1 ) {

                    if( $testset_orderby != 1 ) {
                        #print"<td align='center'><a href='testset_reorder.php?$query_string&amp;testset_id=$testset_id&amp;row=$testset_orderby&amp;move=up'><img src='./images/up_arrow.gif' width=10 height=10 border=0></a></td>". NEWLINE;
                    	print"<td align='center'><img src='./images/up_arrow.gif' width=10 height=10 border=0></td>". NEWLINE;
                    }
                    else {
                        print"<td align='center'></td>". NEWLINE;
                    }
                    if( $testset_orderby != $max_orderby ) {
                        #print"<td align='center'><a href='testset_reorder.php?$query_string&amp;testset_id=$testset_id&amp;row=$testset_orderby&amp;move=down'><img src='./images/down_arrow.gif' width=10 height=10 border=0></a></td>". NEWLINE;
                    	print"<td align='center'><img src='./images/down_arrow.gif' width=10 height=10 border=0></td>". NEWLINE;
                    }
                    else {
                        print"<td align='center'></td>". NEWLINE;
                    }
                }
                print"<td align='center' nowrap>$testset_name</td>". NEWLINE;
                print"<td align='center' nowrap>$formatted_date_created</td>". NEWLINE;
                print"<td class='left'>$testset_description</td>". NEWLINE;
                if ($number_of_tests > 0) { 
				     print"<td align='center' nowrap>$testset_status</td>". NEWLINE;
				}
				else { print"<td align='center' nowrap>Empty</td>". NEWLINE;
                }
                # page not implemented yet, column disabled!
                #print"<td align='center'><a href='$testset_status_page?$query_string&amp;testset_id=$testset_id'>". lang_get('report') ."</a></td>". NEWLINE;
                print"<td align='center'>$testset_lock_by</td>". NEWLINE;
                if(!empty($testset_lock_date)){
                	print"<td align='center'nowrap>". lang_get($lockdatetext) ." $formatted_lock_date</td>". NEWLINE;
                } else {
                	print"<td align='center'nowrap></td>". NEWLINE;
                }
                print"<td align='center'>$testset_lockcomment</td>". NEWLINE;
                if($user_is_manager){
                	print"<td align='center'><a href='$testset_lock_page?$query_string&amp;testset_id=$testset_id'>". lang_get($lock_link) ."</a></td>". NEWLINE;
                }
                print"<td align='center'>$testset_signoff_by</td>". NEWLINE;
                print"<td align='center'nowrap>$formatted_signoff_date</td>". NEWLINE;
                print"<td align='center'>$testset_comments</td>". NEWLINE;
                print"<td align='center'><a href='$testset_signoff_page?$query_string&amp;testset_id=$testset_id'>". lang_get('sign_off') ."</a></td>". NEWLINE;
                print"</tr>". NEWLINE;

            } # end while( $row = db_fetch_row( $db, $rs ) ) {

            print"</table>". NEWLINE;
            print"</div>". NEWLINE;

        }  # end if( $num > 0 ) {
        else {
                print"<br>". NEWLINE;
                echo"<p class='error'>". lang_get( 'no_testsets' ) ."</p>". NEWLINE;
        }

        # DO WE WANT TO ADD A GRAPH FOR ALL TEST SETS IN THE BUILD?
        # I'M THINKING THE DETAIL LINK WILL GIVE A GRAPH FOR THE INDIVIDUAL RUN

    //} # if( isset( $s_properties['testset_id'] ) && $_GET['testset_id'] != 'all') {


    # ---------------------------------------------------------------------
    # ---------------------------------------------------------------------

} # end elseif( isset( $s_properties['build_id'] ) && $_GET['build_id'] != 'all')


html_print_footer();

# ---------------------------------------------------------------------
# $Log: results_page.php,v $
# Revision 1.12  2008/08/04 06:54:58  peter_thal
# added sorting function to several tables
#
# Revision 1.11  2008/07/25 09:50:02  peter_thal
# added lock testset feature
# disabled detail column in test result, because functionality is not implemented yet
#
# Revision 1.10  2008/07/02 12:01:25  peter_thal
# test result mass update only for manager
#
# Revision 1.9  2008/01/22 09:57:38  cryobean
# made the table sortable
# removed possibility to reorder testsets because this functionality doesn't behave as expected - bug
#
# Revision 1.8  2007/02/03 10:25:53  gth2
# no message
#
# Revision 1.7  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.6  2006/05/08 15:37:33  gth2
# Changing formatting - gth
#
# Revision 1.5  2006/04/09 15:54:31  gth2
# removing some hard-coded field names
#
# Revision 1.4  2006/02/27 17:24:13  gth2
# added autopass and testset duration functionality - gth
#
# Revision 1.3  2006/02/24 11:35:34  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2006/02/09 12:34:27  gth2
# changing db field names for consistency - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>
