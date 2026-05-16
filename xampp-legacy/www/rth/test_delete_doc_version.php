<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# $RCSfile: test_delete_doc_version.php,v $  $Revision: 1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$s_project_properties	= session_get_project_properties();
$project_id				= $s_project_properties['project_id'];
$upload_path			= $s_project_properties['test_upload_path'];
$test_id				= $_GET['test_id'];
$mantestid				= $_GET['mantestid'];
$filename				= $_GET['filename'];
#$redirect_page			= "test_doc_history_page.php?test_id=". $test_id ."mantestid=". $mantestid;

$s_user_properties		= session_get_user_properties();
$s_delete_rights		= $s_user_properties['delete_rights'];
$s_user_id				= $s_user_properties['user_id'];
$project_manager		= user_has_rights( $project_id, $s_user_id, MANAGER );
$user_has_delete_rights	= ($s_delete_rights==="Y" || $project_manager);
$redirect		= "test_detail_page.php?test_id=$test_id&project_id=$project_id&tab=2"; 

if($user_has_delete_rights){
	file_delete_unlink_file($filename,$upload_path);		
} else {
	error_report_show($redirect, FAILED_DELETE_DOC);
}

html_print_operation_successful( 'test_doc_history_page', $redirect);





# ---------------------------------------------------------------------
# $Log: test_delete_doc_version.php,v $
# Revision 1.1  2008/07/23 14:53:50  peter_thal
# delete supporting docs feature added (linux/unix)
#
#
# ---------------------------------------------------------------------

?>
