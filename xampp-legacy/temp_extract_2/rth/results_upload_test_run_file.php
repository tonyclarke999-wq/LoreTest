<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Results Upload Test Run File Page
#
# $RCSfile: results_upload_test_run_file.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page				= basename(__FILE__);
$s_results			= session_get_properties("results");
$test_id			= $s_results['test_id'];
$testset_id			= $s_results['testset_id'];
$test_run_id		= $s_results['test_run_id'];

$redirect_page		= "results_view_verifications_page.php?test_id=". $test_id ."&testset_id=". $testset_id;
$redirect_page_file_upload = "results_view_verifications_page.php?"." $test_id=" .$test_id."&testset_id=". $testset_id."&test_run_id=". $test_run_id; 

session_validate_form_set($_POST, $redirect_page);

$s_user				= session_get_user_properties();
$username			= $s_user['username'];
$test_name			= test_get_name( $test_id );
$comments			= $_POST['comments'];

# ------------------------------------------------
# FILE UPLOAD
# ------------------------------------------------
# NEED TO FIND OUT THE LINK FIELD IS FOR.  SPEAK TO RT
$project_properties	= session_get_project_properties();
$upload_path		= $project_properties['test_run_upload_path'];

if( $_FILES['upload_file']['size'] != '0' && is_uploaded_file($_FILES['upload_file']['tmp_name']) ) {

	file_add_test_run_doc(	$_FILES['upload_file']['tmp_name'],
							$_FILES['upload_file']['name'],
							$test_run_id,
							$username,
							$comments,
							$redirect_page_file_upload);

}
else{
	print"Error uploading file. Either the file size = 0 or the file is not a valid file";
}

html_print_operation_successful( 'run_manual_test_page', $redirect_page );

# ---------------------------------------------------------------------
# $Log: results_upload_test_run_file.php,v $
# Revision 1.2  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>