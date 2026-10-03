<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: release_signoff_action.php,v $  $Revision: 1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page = 'release_page.php';

session_validate_form_set($_POST, $redirect_page);

# Update the release status
admin_release_signoff(	session_validate_form_get_field('release_id'),
						session_validate_form_get_field('qa_status'),
						session_validate_form_get_field('qa_comments') );

html_print_operation_successful( 'release_signoff_page', $redirect_page);


# ------------------------------------
# $Log: release_signoff_action.php,v $
# Revision 1.1  2006/04/05 12:38:41  gth2
# updates to release sign-off - th
#
# ------------------------------------
?>