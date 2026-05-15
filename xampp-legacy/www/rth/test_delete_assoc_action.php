<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Delete Assoc Action Page
#
# $RCSfile: test_delete_assoc_action.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'requirement_assoc_action.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];
$properties				= session_get_properties("test");
$test_id				= $properties['test_id'];
$username				= session_get_username();
$row_style				= '';
$redirect_page			= "test_detail_page.php?test_id=".$test_id."&project_id=".$project_id;

$display_options 	= session_set_display_options( "requirements", array_merge($_POST, $_GET) );
$s_tab 				= $display_options['tab'];

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];

switch( $_GET['assoc'] ) {
case"req":
	requirement_delete_test_assoc($_GET['assoc_id']);
}


html_print_operation_successful( 'req_assoc_page', $redirect_page );

# ---------------------------------------------------------------------
# $Log: test_delete_assoc_action.php,v $
# Revision 1.3  2008/07/21 07:42:34  peter_thal
# small bug fixes for test_detail_page linking parameter
#
# Revision 1.2  2006/01/06 00:34:53  gth2
# fixed bug with associations - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>