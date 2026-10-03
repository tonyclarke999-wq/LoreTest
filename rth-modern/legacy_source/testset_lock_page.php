<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# testset lock page
#
# $RCSfile: testset_lock_page.php,v $ $Revision: 1.1 $
# ------------------------------------

include"./api/include_api.php";

$page                   = basename(__FILE__);
$action_page            = 'testset_lock_action.php';
$num                    = 0;
$bg_color               = '';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];


if (isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
}
else
    $is_validation_failure = false;

if (isset($_GET['testset_id'])) {

    $testset_id = $_GET['testset_id'];
}

if (isset($_GET['build_id'])) {

    $build_id = $_GET['build_id'];
}
$locked	= testset_get_lock_status($testset_id);
if($locked){
	$page_title = 'testset_unlock_page';
	$submit_btn = lang_get('unlock');
}else{
	$page_title = 'testset_lock_page';
	$submit_btn = lang_get('lock');
}

html_window_title();

auth_authenticate_user();

html_page_title($project_name ." - ". lang_get($page_title) );
html_page_header( $db, $project_name );

html_print_menu();
html_print_body();
print"<br><br>";

error_report_check( $_GET );


$row = testset_get( $testset_id, $build_id );
	
	
if (!empty($row)) {
	
	extract( $row, EXTR_PREFIX_ALL, 'v' );

//	$testset_id			= ${'v_' . TS_ID};
	$testset_name       = ${'v_' . TS_NAME};
	$date_created       = ${'v_' . TS_DATE_CREATED};
	$description        = ${'v_' . TS_DESCRIPTION};
	$status		        = ${'v_' . TS_STATUS};
	$comments			= ${'v_' . TS_SIGNOFF_COMMENTS};


//	print"<div align=center>";
//	print "<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span><br/>";
	print"<div align='center'>";
	print"<table class=width65 rules='none' border='0'>";
	print"<tr>";
	print"<td>";
	    print"<table class=inner rules='none' border='0'>";
    	print"<form method=post action=$action_page>";

    	print"<tr class=left>";
    	print"<td class=print-category colspan=2>".lang_get('testset_signoff')."</td>";
    	print"</tr>";

    	print"<tr class=left>";
		    print"<td class=right>". lang_get('testset_name') .":</td>";
	    	print"<td class=left>$testset_name</td>";
    	print"</tr>";

    	print"<tr class=left>";
			print"<td class=right>". lang_get('date_created') .":</td>";
			print"<td class=left>$date_created</td>";
    	print"</tr>";

    	print"<tr class=left>";
			print"<td class=right>". lang_get('description') .":</td>";
			print"<td class=left>$description</td>";
    	print"</tr>";
    	
    	
    	print"<tr class=left>";
			print"<td class='right'>". lang_get('comments') .":</td>";
			print"<td class='left'><textarea rows='5' cols='50' name='lock_comment'></textarea></td>";
    	print"</tr>";
    	
    	print"<tr><td><input type='hidden' name='testset_id' value='$testset_id'></td></tr>";
		print"<tr><td><input type='hidden' name='build_id' value='$build_id'></td></tr>";
		print"<tr><td class=center colspan=2><input type=submit name='save' value='$submit_btn'><br/><br/></td>";
		
		print"</form>";
		print"</table>";

	print"</td>";
	print"</tr>";
	print"</table>";
	print"<br>";
	print"</div>";
	print"</div>";
}	
html_print_footer();
	
	
	
	

# ------------------------------------
# $Log: testset_lock_page.php,v $
# Revision 1.1  2008/07/25 09:50:02  peter_thal
# added lock testset feature
# disabled detail column in test result, because functionality is not implemented yet
#
# ------------------------------------
?>
