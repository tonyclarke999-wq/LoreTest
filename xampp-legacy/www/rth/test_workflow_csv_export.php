<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Workflow CSV Export Page
#
# $RCSfile: test_workflow_csv_export.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();


header( 'Content-Type: text/plain; name=data.csv' );
header( 'Content-Transfer-Encoding: BASE64;' );
header( 'Content-Disposition: attachment; filename=data.csv' );

global $db;
$deleted            = TEST_TBL. "." .TEST_DELETED;
$archived           = TEST_TBL. "." .TEST_ARCHIVED;
$test_tbl           = TEST_TBL;

# get filter and sort options off the session
$s_test_workflow_form_filter_options = session_get_filter_options( "test_workflow" );
$s_test_workflow_table_display_options = session_get_display_options( "test_workflow" );

$where_clause = test_workflow_filter_generate_where_clause ($s_test_workflow_form_filter_options['manauto'],
                                                            $s_test_workflow_form_filter_options['baowner'],
                                                            $s_test_workflow_form_filter_options['qaowner'],
                                                            $s_test_workflow_form_filter_options['test_status']);


$order_clause = ' ORDER BY ' . $s_test_workflow_table_display_options['order_by'] . ' ' . $s_test_workflow_table_display_options['order_dir'];

# set table headers
print "Test ID, M/A, Test Name, Test Status, Priority, BA Owner, QA Owner, Date Assigned, Date Expected, Date Complete, BA Sign Off Date, Info\r". NEWLINE;

$q = "SELECT * FROM $test_tbl WHERE $deleted = 'N' AND $archived = 'N'";

$q = $q . $where_clause . $order_clause;

$rs = &db_query( &$db, $q );

while( $row = db_fetch_row( $db, $rs ) ) {

    extract( $row, EXTR_PREFIX_ALL, 'v' );

    $test_id        = ${'v_' . TEST_ID};
    $test_name      = ${'v_' . TEST_NAME};
    $ba_owner       = ${'v_' . TEST_BA_OWNER};
    $qa_owner       = ${'v_' . TEST_QA_OWNER};
    $tester			= ${'v_' . TEST_TESTER};
    $test_status    = ${'v_' . TEST_STATUS};
    $test_priority  = ${'v_' . TEST_PRIORITY};
    $manual         = ${'v_' . TEST_MANUAL};
    $automated      = ${'v_' . TEST_AUTOMATED};
    $autopass       = ${'v_' . TEST_AUTO_PASS};

    $date_assigned  = ${'v_' . TEST_DATE_ASSIGNED};
    $date_expected  = ${'v_' . TEST_DATE_EXPECTED};
    $date_complete  = ${'v_' . TEST_DATE_COMPLETE};
    $ba_signoff     = ${'v_' . TEST_BA_SIGNOFF};
    $comments       = ${'v_' . TEST_COMMENTS};

    # FIGURE OUT WHAT SYMBOL TO EXPORT ("M", "A", "M/A", OR "")
    if( $manual == "YES" ) {
        if( $automated == "YES" ) {
            $script = "M/A";
        }
        else {
            $script = "M";
        }
    }
    elseif( $automated == "YES" ) {
        $script = "A";
    }
    else {
        $script = "";
    }

    $comments = util_prepare_text_for_export($comments);

    print"$test_id,$script,$test_name,$test_status,$test_priority,$ba_owner,$qa_owner, $tester, $date_assigned,$date_expected,$date_complete,$ba_signoff,$comments\r". NEWLINE;

}

exit;

# ---------------------------------------------------------------------
# $Log: test_workflow_csv_export.php,v $
# Revision 1.2  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>