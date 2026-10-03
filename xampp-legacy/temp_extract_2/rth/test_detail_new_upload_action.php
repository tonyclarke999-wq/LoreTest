<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Detail New Upload Action Page
#
# $RCSfile: test_detail_new_upload_action.php,v $  $Revision: 1.8 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page				= basename(__FILE__);
$s_test_details		= session_get_properties("test");
$project_prop		= session_get_project_properties();
$test_id			= $s_test_details['test_id'];
$project_id			= $project_prop['project_id'];
$redirect_page		= "test_detail_page.php?test_id=$test_id&project_id=$project_id&tab=2";
$redirect_on_error	= "test_detail_page.php?test_id=$test_id&project_id=$project_id&tab=2";

session_validate_form_set($_POST, $redirect_page);

$project_properties	= session_get_project_properties();
$upload_path		= $project_properties['test_upload_path'];
$s_user				= session_get_user_properties();
$username			= $s_user['username'];
$test_name			= test_get_name( $test_id );
$comments			= session_validate_form_get_field('comments');
$doc_type			= session_validate_form_get_field('doc_type');

$upload_file_name	= $_FILES['uploadfile']['name'];
$_FILES['uploadfile']['name'] = str_replace(" ","_",$upload_file_name);

# ------------------------------------------------
# FILE UPLOAD
# ------------------------------------------------
if( $_FILES['uploadfile']['size'] != '0' && is_uploaded_file($_FILES['uploadfile']['tmp_name']) ) {

	file_add_supporting_test_doc($_FILES['uploadfile']['tmp_name'], $_FILES['uploadfile']['name'], $test_id, $comments, $doc_type );

}
else{
	error_report_show( $redirect_on_error, NO_FILE_SPECIFIED );
}

html_print_operation_successful( 'file_upload_page', $redirect_page );

# ---------------------------------------------------------------------
# $Log: test_detail_new_upload_action.php,v $
# Revision 1.8  2008/08/07 10:57:51  peter_thal
# Now blanks are replaced with underscores by adding a new supporting doc
#
# Revision 1.7  2008/07/23 14:53:50  peter_thal
# delete supporting docs feature added (linux/unix)
#
# Revision 1.6  2008/07/09 07:13:24  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.5  2006/06/30 00:39:46  gth2
# correct page title - gth
#
# Revision 1.4  2006/04/11 12:11:03  gth2
# create a test version when uploading test steps - gth
#
# Revision 1.3  2006/04/05 12:39:30  gth2
# no message
#
# Revision 1.2  2006/01/09 02:02:14  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>