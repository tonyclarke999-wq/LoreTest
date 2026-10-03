<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Edit Bug Component Action
#
# $RCSfile: bug_edit_bugnote_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$bugnote_id = $_POST['bugnote_id'];
$bug_id		= $_POST['bug_id'];

$redirect_on_success	= "bug_detail_page.php?bug_id=$bug_id";
$redirect_on_error		= "bug_edit_bugnote_page.php?bugnote_id=$bugnote_id";

session_validate_form_set($_POST, $redirect_on_error);

bug_edit_bugnote( $bug_id,
				  $bugnote_id,
				  session_validate_form_get_field('bugnote_required') );

session_validate_form_reset();

html_print_operation_successful( "edit_bugnote_page", $redirect_on_success );

# ------------------------------------
# $Log: bug_edit_bugnote_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>