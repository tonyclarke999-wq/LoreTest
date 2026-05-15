<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Archive Tests Action
#
# $RCSfile: project_archive_tests_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page = 'project_archive_tests_page.php';

session_records("archive_tests");

project_archive_tests($_POST['project_id']);

html_print_operation_successful( "archive_tests_page", $redirect_page );

# ------------------------------------
# $Log: project_archive_tests_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>