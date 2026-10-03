<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# $RCSfile: test_add_doc_version_action.php,v $  $Revision: 1.6 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();


$page				= basename(__FILE__);
$s_test_details		= session_get_properties("test");
$test_id			= $s_test_details['test_id'];


$project_properties	= session_get_project_properties();
$project_id			= $project_properties['project_id'];
$upload_path		= $project_properties['test_upload_path'];
$s_user				= session_get_user_properties();
$username			= $s_user['username'];
$test_name			= test_get_name( $test_id );

if(isset($_POST['comments'])){
	$comments			= $_POST['comments'];	
}
if(isset($_POST['doc_type'])){
	$doc_type			= $_POST['doc_type'];	
}

if( isset( $_POST['manual_test_id'] ) && $_POST['manual_test_id'] != "" ) {

	$manual_test_id = $_POST['manual_test_id']; //session_validate_form_get_field('manual_test_id');
}
else {
	$manual_test_id		= $_GET['manual_test_id'];
}

$redirect_page		= "test_detail_page.php?test_id=$test_id&project_id=$project_id&tab=2";
$redirect_on_error	= "test_add_doc_version_page.php?test_id=$test_id&manual_test_id=$manual_test_id";

session_validate_form_set($_POST, $redirect_page);


# Make sure the user entered a file name
$file_temp_name = $_FILES['uploadfile_required']['tmp_name'];
$file_name		= str_replace(" ","_",$_FILES['uploadfile_required']['name']);
$file_size		= $_FILES['uploadfile_required']['size'];


# Make sure the user filled in the file name.
if( !isset( $file_temp_name ) || !is_uploaded_file( $file_temp_name) ) {

	error_report_show($redirect_on_error, REQUIRED_FIELD_MISSING);
}

if( $file_size != '0' && is_uploaded_file($file_temp_name) ) {

	//print"manual_test_id = $manual_test_id<br>";

	file_add_supporting_test_doc_version($file_temp_name, $file_name, $test_id, $manual_test_id, $comments, $doc_type );

}
else{
	error_report_show($redirect_on_error, FAILED_FILE_UPLOAD);
}

html_print_operation_successful( 'file_upload_page', $redirect_page );

# ---------------------------------------------------------------------
# $Log: test_add_doc_version_action.php,v $
# Revision 1.6  2008/08/07 10:57:51  peter_thal
# Now blanks are replaced with underscores by adding a new supporting doc
#
# Revision 1.5  2008/08/05 07:22:33  peter_thal
# fixed save bug for comment and doc type field
#
# Revision 1.4  2008/07/23 14:53:50  peter_thal
# delete supporting docs feature added (linux/unix)
#
# Revision 1.3  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.2  2006/04/09 20:46:21  gth2
# adding code lost during cvs outage - gth
#
# Revision 1.1  2006/03/12 21:29:09  gth2
# Adding initial version - gth
#
# ---------------------------------------------------------------------

?>