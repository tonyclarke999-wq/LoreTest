<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Version Make Active Action Page
# This page was used when there were test versions
# It is not used in the current test implementation
#
# $RCSfile: test_version_make_active_action.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$test_id			= $_GET['test_id'];
$project_id			= $_GET['project_id'];
$tst_version_id	= $_GET['tst_version_id'];
$redirect_page 		= "test_detail_page.php?test_id=$test_id&project_id=$project_id&tab=4";

test_make_active_version( $test_id, $tst_version_id );

html_print_operation_successful( "add_test_version_page", $redirect_page );


# ---------------------------------------------------------------------
# $Log: test_version_make_active_action.php,v $
# Revision 1.2  2008/07/21 07:42:34  peter_thal
# small bug fixes for test_detail_page linking parameter
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>