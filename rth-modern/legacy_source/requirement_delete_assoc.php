<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Delete Assoc
#
# $RCSfile: requirement_delete_assoc.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------
# ------------------------------------
# $RCSfile: requirement_delete_assoc.php,v $
# ------------------------------------

# This file is no longer used
/*


include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page            = 'requirement_assoc_action.php';
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];
$username				= session_get_username();
$row_style				= '';

$display_options 	= session_set_display_options( "requirements", array_merge($_POST, $_GET) );
$s_tab 				= $display_options['tab'];

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];

switch( $_GET['assoc'] ) {
case "req":
	$q = "DELETE FROM Requirement_Assoc WHERE ReqAssocID = '$assoc_id'"; # need to find out if this is ever called.  I don't think it is.
	$db->Execute($q);
case "test":
	$q = "DELETE FROM ". TEST_REQ_ASSOC_TBL ." WHERE ". TEST_REQ_ASSOC_ID ." = '$assoc_id'";
	$db->Execute($q);
case "release":
	$q = "DELETE FROM ". REQ_VERS_ASSOC_REL ." WHERE ". REQ_VERS_ASSOC_REL_ID ." = '$assoc_id'";
	$db->Execute($q);
}


html_print_operation_successful( 'req_assoc', "requirement_detail_page.php" );

# ---------------------------------------------------------------------
# $Log: requirement_delete_assoc.php,v $
# Revision 1.3  2006/02/24 11:33:32  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.2  2006/02/15 03:11:20  gth2
# correcting case - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

*/
?>