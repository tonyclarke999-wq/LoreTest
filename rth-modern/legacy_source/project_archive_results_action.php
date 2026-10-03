<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Archive Results Action
#
# $RCSfile: project_archive_results_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page		= 'project_archive_results_page.php';

project_archive_results($_POST['project_id'], $_POST);

html_print_operation_successful( "archive_results_page", $redirect_page );

# ------------------------------------
# $Log: project_archive_results_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>