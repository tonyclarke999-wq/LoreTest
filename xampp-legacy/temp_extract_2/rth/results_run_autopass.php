<?php

include"./api/include_api.php";
auth_authenticate_user();

session_validate_form_reset();

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

# Links to pages
$page                   	= basename(__FILE__);

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('auto_pass_page') );




print"<div align='center'>". NEWLINE;
print"The following tests have been Passed by the system:<BR>";
print"<table class=width80 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
html_tbl_print_header( lang_get('testset_id') );
html_tbl_print_header( lang_get('test_id') );
html_tbl_print_header( lang_get('test_name') );
html_tbl_print_header( lang_get('test_run_id') );
print"</tr>". NEWLINE;
		
		
$testset_id = $_GET['testset_id'];


global $db;
$results_tbl		= TEST_RESULTS_TBL;
$f_test_run_id		= TEST_RESULTS_TS_UNIQUE_RUN_ID;
$f_test_id			= TEST_RESULTS_TEMPEST_TEST_ID;
$f_testset_id		= TEST_RESULTS_TEST_SET_ID;
$f_test_name		= TEST_RESULTS_TEST_SUITE;
$f_status			= TEST_RESULTS_TEST_STATUS;
$f_assigned_to		= TEST_RESULTS_ASSIGNED_TO;
$f_started			= TEST_RESULTS_STARTED;
$f_finished			= TEST_RESULTS_FINISHED;
$f_time_started		= TEST_RESULTS_TIME_STARTED;
$f_time_finished	= TEST_RESULTS_TIME_FINISHED;


$q = "SELECT DISTINCT( $f_test_id ) 
	 FROM $results_tbl 
	 WHERE $f_testset_id = '$testset_id'";
$rs = db_query( $db, $q ); 

while($row = db_fetch_row( $db, $rs ) ) {
	
	$test_id = $row[TEST_RESULTS_TEMPEST_TEST_ID];

	//Get the latest run of the test
	$q_results = "SELECT MAX($f_test_run_id) as MAX 
					 FROM $results_tbl 
					 WHERE $f_testset_id = '$testset_id' 
					 AND $f_test_id = '$test_id'
					 AND $f_finished = '1'";
	//print"$q_results<BR>";
	
	$rs_results = db_query( $db, $q_results );
	$row_results = db_fetch_row( $db, $rs_results );
	$max_testresults_id = $row_results['MAX'];
	
	//Get the Unique Run ID from the latest run
	$q_run_id = "SELECT $f_test_run_id FROM $results_tbl WHERE $f_test_run_id = '$max_testresults_id'";

	$rs_run_id = db_query( $db, $q_run_id );
	$num_run_id = db_num_rows( $db, $rs_run_id );
	
	if($num_run_id != 0){

		$row_run_id = db_fetch_row( $db, $rs_run_id ); 

		//Gets the status of the individual verifications that are FAIL for the test run
		$vr_tbl				= VERIFY_RESULTS_TBL;
		$f_verify_id		= VERIFY_RESULTS_ID;
		$f_vr_test_run_id	= VERIFY_RESULTS_TS_UNIQUE_RUN_ID;
		$f_status			= VERIFY_RESULTS_TEST_STATUS;

		$unique_run_id = $row_run_id[TEST_RESULTS_TS_UNIQUE_RUN_ID];
		
		$query_results = "SELECT $f_status 
						  FROM $vr_tbl 
						  WHERE ( $f_vr_test_run_id = '$unique_run_id' AND $f_status != 'PASS') 
						  OR ($f_vr_test_run_id = '$unique_run_id' AND $f_status != 'INFO')";

		
		$rs_results = db_query( $db, $query_results ); // = mysql_query($query_results);
		$num_results = db_num_rows( $db, $rs_results ); //$num_results = mysql_num_rows($recordset_results);
		
		//If there are no FAILS and the test isnt a Manual Test, then continue with Passing it		
		if( 0 == $num_results && 'S' == (substr($unique_run_id,0,1) ) ){
			//Checks that the test has not been previously passed

			$assoc_tbl				= TEST_TS_ASSOC_TBL;
			$f_assoc_id				= TEST_TS_ASSOC_ID;
			$f_assoc_ts_id			= TEST_TS_ASSOC_TS_ID;
			$f_assoc_test_id		= TEST_TS_ASSOC_TEST_ID;
			$f_assoc_status			= TEST_TS_ASSOC_STATUS;
			$f_assoc_assigned_to	= TEST_TS_ASSOC_ASSIGNED_TO;
			$f_assoc_timestamp		= TEST_TS_ASSOC_TIMESTAMP;
			
			$q_check = "SELECT $f_assoc_status
						FROM $assoc_tbl
						WHERE $f_assoc_ts_id = '$testset_id' 
						AND $f_assoc_test_id  = '$test_id]'";

			//print"<BR>------$query_check<BR>";
			$rs_check = db_query( $db, $q_check );
			$row_check = db_fetch_array( $db, $rs_check );
			$status = $row_check[TEST_TS_ASSOC_STATUS];
			
			//If the test has not been passed, then pass it
			if($status != 'Passed') {

				$test_name = test_get_name( $test_id );
				
				print"<tr>". NEWLINE;
				print"<td>$testset_id</td>". NEWLINE;
				print"<td>$test_id</td>". NEWLINE;
				print"<td>$test_name</td>". NEWLINE;
				print"<td><a href='results_view_verifications_page.php?test_run_id=$unique_run_id&testset_id=$testset_id&test_id=$test_id'>$unique_run_id</td>". NEWLINE;
				print"</tr>". NEWLINE;
				
				$q_update = "UPDATE $assoc_tbl 
								SET $f_assoc_status = 'Passed', 
									$f_assoc_assigned_to = 'System', 
									$f_assoc_timestamp = '$current_date' 
									WHERE $f_assoc_ts_id = '$testset_id' 
									AND $f_assoc_test_id = '$test_id'";
				db_query( $db, $q_update);
				
			}
		} 
	}
}

print"</table>". NEWLINE;
print"</div>". NEWLINE;


print"<br><br>". lang_get('autopass_complete') ."<br><br><br><br>". NEWLINE;

html_print_footer();

?>