<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test CSV Export Page
#
# $RCSfile: test_csv_export.php,v $  $Revision: 1.3 $
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
$s_test_form_filter_options = session_get_filter_options( "test" );
$s_test_table_display_options = session_get_display_options( "test" );

$where_clause = test_filter_generate_where_clause ($s_test_form_filter_options['manauto'], $s_test_form_filter_options['baowner'],
                                                   $s_test_form_filter_options['qaowner'], $s_test_form_filter_options['test_type'],
                                                   $s_test_form_filter_options['test_area']);

$order_clause = ' ORDER BY ' . $s_test_table_display_options['order_by'] . ' ' . $s_test_table_display_options['order_dir'];

# set table headers
print "Test ID, M/A, File Type, Auto Pass, Test Name, BA Owner, QA Owner, Test Type, Area Tested\r". NEWLINE;

$q = "SELECT * FROM $test_tbl WHERE $deleted = 'N' AND $archived = 'N'";

$q = $q . $where_clause . $order_clause;

$rs = &db_query( $db, $q );

while( $row = db_fetch_row( $db, $rs ) ) {

    extract( $row, EXTR_PREFIX_ALL, 'v' );

    $test_id        = ${'v_' . TEST_ID};
    $test_name      = ${'v_' . TEST_NAME};
    $ba_owner       = ${'v_' . TEST_BA_OWNER};
    $qa_owner       = ${'v_' . TEST_QA_OWNER};
    $tester			= ${'v_' . TEST_TESTER};
    $test_type      = ${'v_' . TEST_TESTTYPE};
    $manual         = ${'v_' . TEST_MANUAL};
    $automated      = ${'v_' . TEST_AUTOMATED};
    $area_tested    = ${'v_' . TEST_AREA_TESTED};
    $autopass       = ${'v_' . TEST_AUTO_PASS};

    if ("Y" == ${'v_' . TEST_AUTO_PASS} )
        $autopass = "Yes";
    else
        $autopass = "No";

    $filename = test_get_filename ($test_id ) ;

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

    $file_type = util_get_filetype( test_get_filename ($test_id ));

    print"$test_id,$script,$file_type,$autopass,$test_name,$ba_owner,$qa_owner,$tester,$test_type,$area_tested\r". NEWLINE;

}

exit;

# ---------------------------------------------------------------------
# $Log: test_csv_export.php,v $
# Revision 1.3  2007/03/14 17:45:53  gth2
# removing code that passes varables by reference - gth
#
# Revision 1.2  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>