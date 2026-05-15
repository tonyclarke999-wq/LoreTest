<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Home Page 2
#
# $RCSfile: home_page2.php,v $ 
# $Revision: 1.5 $
# ------------------------------------

include_once"./api/include_api.php";
auth_authenticate_user();

if( isset($_POST['submit_btn']) ) {

	if( $_POST['submit_btn']==lang_get('edit') ) {

		require_once("news_edit_page.php");
		exit;
	}

	if( $_POST['submit_btn']==lang_get('delete') ) {

		require_once("delete_page.php");
		exit;
	}

	if( $_POST['submit_btn']==lang_get("new_post") ) {

		require_once("news_add_page.php");
		exit;
	}
}

session_validate_form_reset();

$project_name 		= session_get_project_name();
$page				= basename(__FILE__);
$row_style			= '';

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

$test_detail_page		= 'test_detail_page.php';
$req_detail_page		= 'requirement_detail_page.php';
$results_page			= 'results_page.php';

html_window_title();
html_print_body();
html_page_title($project_name ." - HOME");
html_page_header( $db, $project_name );
html_print_menu();
//html_print_sub_menu( $page, array() );

error_report_check( $_GET );


//print"<h3>".lang_get("news")."</h3>". NEWLINE;


print"<div align=center>". NEWLINE;

print"<table width=80% border=0>". NEWLINE;
print"<tr>". NEWLINE;
print"<td align=left><font size=3><b>". lang_get('news') ."</b></font></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;


print"<table class=width80 rules=cols>". NEWLINE;

$rows_news = news_get($project_id);

if($rows_news) {

	//print"<div align=center>". NEWLINE;

	foreach($rows_news as $row_news) {

		$news_id		= $row_news[NEWS_ID];
		$subject 		= $row_news[NEWS_SUBJECT];
		$body			= $row_news[NEWS_BODY];
		$poster			= $row_news[NEWS_POSTER];
		$news_modified	= $row_news[NEWS_MODIFIED];

		//print"<table class=width80 rules=cols>". NEWLINE;

		# NEWS TITLE
		print"<tr class='tbl-header-l'>". NEWLINE;
		print"<td><b>$subject - $news_modified - ".user_get_display_name($project_id, $poster)."</b></td>". NEWLINE;
		print"</tr>". NEWLINE;

		# NEWS
		print"<tr>". NEWLINE;
		print"<td align=left class=news-body>".util_html_encode_string($body)."</td>". NEWLINE;
		print"</tr>". NEWLINE;
/*
		# EDIT, DELETE LINKS
		print"<tr>". NEWLINE;
			print"<td align=left>". NEWLINE;
			print"<form action='$page' method=post>". NEWLINE;
			print"<input type=hidden name=news_id value=$news_id>". NEWLINE;
			print"[<input type=submit name=submit_btn value='".lang_get("edit")."' class=page-numbers>]". NEWLINE;
			print"[<input type=submit name=submit_btn value='".lang_get("delete")."' class=page-numbers>]". NEWLINE;
			print"<input type='hidden' name='r_page' value='$page'>". NEWLINE;
			print"<input type='hidden' name='f' value='delete_news_post'>". NEWLINE;
			print"<input type='hidden' name='id' value='$news_id'>". NEWLINE;
			print"<input type='hidden' name='project_id' value='$project_id'>". NEWLINE;
			print"<input type='hidden' name='msg' value='". DEL_NEWS ."'>". NEWLINE;
			print"[<input type=submit name=submit_btn value='".lang_get("new_post")."' class=page-numbers>]". NEWLINE;
			print"</form>". NEWLINE;
			print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
*/
		//print"</table>". NEWLINE;

		//print"<br>". NEWLINE;

	}

} else { # The default news if there isn't any

		# DEFAULT NEWS
		print"<tr class='tbl-header-l'>". NEWLINE;
		print"<td><b>". lang_get('home_welcome') ."</b></td>". NEWLINE;
		print"</tr>". NEWLINE;

		# NEWS
		print"<tr>". NEWLINE;
		print"<td align=left class=news-body>".lang_get('home_notes')."</td>". NEWLINE;
		print"</tr>". NEWLINE;

}

print"</table>". NEWLINE;
print"</div>";
print"<br>". NEWLINE;


# LATEST TEST RESULTS
print"<div align=center>". NEWLINE;

print"<table width=80% border=0>". NEWLINE;
print"<tr>". NEWLINE;
print"<td align=left><font size=3><b>". lang_get('latest_test_results') ."</b></font></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
#print"<h3>".lang_get("latest_test_results")."</h3>". NEWLINE;

$release_id = admin_get_max_release_id_from_build_tbl( $project_id );
$build_id = admin_get_max_build_id( $release_id );
$testset_id = admin_get_max_testset( $build_id );

if( empty($release_id) ) { # There are no tests associated to the testset or no test sets.

	print lang_get('no_releases_in_project');
} elseif( empty($build_id) ) {

	print lang_get('no_builds_in_release');
} elseif( empty($testset_id) ) {

	print lang_get('no_testsets_in_build');
} else {  # There are tests associated to the test set so we can display the test status.

		$release_name	= admin_get_release_name( $release_id );
		$build_info		= admin_get_build($project_id, $build_id);
		$build_name		= $build_info[BUILD_NAME];
		$build_date		= $build_info[BUILD_DATE_REC];

	print"<table rules=cols class=width80>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('release_name') );
		html_tbl_print_header( lang_get('build_name') );
		html_tbl_print_header( lang_get('build_date_received') );
		print"</tr>". NEWLINE;

		print"<tr>". NEWLINE;
		print"<td class='tbl-data-c'>$release_name</td>". NEWLINE;
		print"<td class='tbl-data-c'>$build_name</td>". NEWLINE;
		print"<td class='tbl-data-c'>$build_date</td>". NEWLINE;
		print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	print"<br>". NEWLINE;

	$statuses = results_get_teststatus_by_project( $project_id );
	$results_url = $results_page ."?release_id=$release_id&amp;build_id=$build_id&amp;testset_id=$testset_id";

	print"<table rules=cols class=width80>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('testset_name') );
		# Display table headers based on the statuses of the project
		foreach( $statuses as $status ) {
			html_tbl_print_header( $status );
		}
		print"</tr>". NEWLINE;
		
		// testset_get_last_5: function that returns an array with last 5 testsets created
		$rows_testsets = &testset_get_last_5($project_id); 
		
		
		if($rows_testsets) {
			
			foreach($rows_testsets as $row_testset) {
			
				$testset_id	= $row_testset['TestSetID'];
				$num_tests = admin_count_tests_in_testset( $testset_id );
				$testset_name = admin_get_testset_name( $testset_id );
				$row_style = html_tbl_alternate_bgcolor($row_style);
				print"<tr class=$row_style>". NEWLINE;
				$results_url = $results_page ."?release_id=$release_id&amp;build_id=$build_id&amp;testset_id=$testset_id";
				print"<td class='tbl-data-c'><a href='$results_url'>$testset_name</a></td>";
				# Display table data based on the statuses of the project as well
				foreach( $statuses as $status ) {
					$num = results_get_num_tests_by_status( $testset_id, $status );
					print"<td class='tbl-data-c'>$num</td>". NEWLINE;
				} print"</tr>";
			}   
		} 
	
		print"</table>". NEWLINE;
}

print"</div>". NEWLINE;
print"<br>";

# DISPLAY SOMETHING LIKE PROD.  SOME STATS, A GRAPH, AND A LINK TO THE LATEST TEST RESULTS
# MAKE SURE THERE ARE TESTS ADDED TO THE TEST SET WHEN GIVING THE LATEST TEST RESULTS LINK

# LATEST CHANGES TO TESTS
print"<div align=center>";

print"<table width=80% border=0>". NEWLINE;
print"<tr>". NEWLINE;
print"<td align=left><font size=3><b>". lang_get('latest_tests') ."</b></font></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
#print"<h3>".lang_get("latest_tests")."</h3>". NEWLINE;

$rows_tests = test_get_last_modified($project_id);

if($rows_tests) {

	print"<table rules=cols class=width80>". NEWLINE;
	print"<tr>";
	html_tbl_print_header( lang_get('test_id') );
	html_tbl_print_header( lang_get('test_name') );
	html_tbl_print_header( lang_get('last_updated_by') );
	html_tbl_print_header( lang_get('last_updated') );
	print"</tr>";

	foreach($rows_tests as $row_test) {

		$test_id				= $row_test[TEST_ID];
		$test_name 				= $row_test[TEST_NAME];
		$test_last_updated		= $row_test[TEST_LAST_UPDATED];
		$test_last_updated_by	= $row_test[TEST_LAST_UPDATED_BY];

		$display_test_id = util_pad_id( $test_id );

		$row_style = html_tbl_alternate_bgcolor($row_style);

		print"<tr class=$row_style>". NEWLINE;
		print"<td><a href='$test_detail_page?test_id=$test_id&project_id=$project_id'>$display_test_id</a></td>". NEWLINE;
		print"<td>$test_name</td>". NEWLINE;
		print"<td>$test_last_updated_by</td>". NEWLINE;
		print"<td>$test_last_updated</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}
	print"</table>". NEWLINE;

} else {

	print lang_get('no_tests_in_project');
}

print"</div>". NEWLINE;
print"<br>". NEWLINE;

# LATEST CHANGES TO REQUIREMENTS
print"<div align=center>". NEWLINE;

print"<table width=80% border=0>". NEWLINE;
print"<tr>". NEWLINE;
print"<td align=left><font size=3><b>". lang_get('latest_requirements') ."</b></font></td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
#print"<h3>".lang_get("latest_requirements")."</h3>". NEWLINE;

$rows_reqs = requirement_get_last_updated($project_id);

if($rows_reqs) {

	print"<table rules=cols class=width80>". NEWLINE;
	print"<tr>";
	html_tbl_print_header( lang_get('req_id') );
	html_tbl_print_header( lang_get('req_version') );
	html_tbl_print_header( lang_get('req_name') );
	html_tbl_print_header( lang_get('last_updated_by') );
	html_tbl_print_header( lang_get('last_updated') );
	print"</tr>";

	foreach($rows_reqs as $rows_req) {

		$req_id					= $rows_req[REQ_ID];
		$req_version_id			= $rows_req[REQ_VERS_UNIQUE_ID];
		$req_name 				= $rows_req[REQ_FILENAME];
		$req_last_updated		= $rows_req[REQ_VERS_LAST_UPDATED];
		$req_last_updated_by	= $rows_req[REQ_VERS_LAST_UPDATED_BY];
		$req_version			= $rows_req[REQ_VERS_VERSION];

		$display_req_id = util_pad_id( $req_id );

		$row_style = html_tbl_alternate_bgcolor($row_style);

		print"<tr class=$row_style>". NEWLINE;
		print"<td><a href='$req_detail_page?req_id=$req_id&amp;req_version_id=$req_version_id'>$display_req_id</a></td>". NEWLINE;
		print"<td>$req_version</td>". NEWLINE;
		print"<td>$req_name</td>". NEWLINE;
		print"<td>$req_last_updated_by</td>". NEWLINE;
		print"<td>$req_last_updated</td>". NEWLINE;
		print"</tr>". NEWLINE;
	}
	print"</table>". NEWLINE;

} else {

	print lang_get('no_requirements_in_project');
}

print"</div>";

html_print_footer();

#------------------------------------
# $Log: home_page2.php,v $
# Revision 1.5  2008/07/21 07:42:34  peter_thal
# small bug fixes for test_detail_page linking parameter
#
#
# ------------------------------------

?>
