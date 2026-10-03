<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Close Defect Action Page
#
# $RCSfile: bug_close_action.php,v $  $Revision   $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();


$s_bug_details			= session_get_properties( "bug" );
$bug_id					= $s_bug_details['bug_id'];
$redirect_on_success	= "bug_detail_page.php?bug_id=$bug_id";
$redirect_on_error		= "bug_close_page.php?bug_id=$bug_id";

session_validate_form_set($_POST, $redirect_on_error);

bug_close(	$bug_id,
			session_validate_form_get_field('closed_reason_code_required'),
			session_validate_form_get_field('bugnote') );

session_validate_form_reset();

html_print_operation_successful( 'update_bug_page', $redirect_on_success );

# ------------------------------------
# $Log: bug_close_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>