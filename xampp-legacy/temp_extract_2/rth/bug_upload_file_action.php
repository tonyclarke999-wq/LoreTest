<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Plan File Upload Action Page
#
# $RCSfile: bug_upload_file_action.php,v $  $Revision: 1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$project_id	= session_get_project_id();
$bug_id = $_POST['bug_id'];
$redirect_page	= "bug_detail_page.php?bug_id=$bug_id";


file_add_bug_file(	$_FILES['uploadfile_required']['tmp_name'],
					$_FILES['uploadfile_required']['name'],
					$bug_id,
					$redirect_page );

html_print_operation_successful( 'bug_detail_page', $redirect_page );

# ---------------------------------------------------------------------
# $Log: bug_upload_file_action.php,v $
# Revision 1.1  2006/10/05 02:43:16  gth2
# adding file upload to the bug page - gth
#
# ---------------------------------------------------------------------

?>