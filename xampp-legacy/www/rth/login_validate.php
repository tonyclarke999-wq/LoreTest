<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Validate Login Information
#
# $RCSfile: login_validate.php,v $ $Revision: 1.3 $
# ------------------------------------

include"./api/include_api.php";

$switch_project 	= $_POST['login']['switch_project'];
$redirect_page		= $_POST['login']['page'];
$redirect_page_get	= $_POST['login']['get'];
$page				= 'login.php';

if( isset( $_POST['uname'] ) ) {
    $username = $_POST['uname'];
    #added validation, to avoid sql injection
    if(!preg_match("/^[a-zA-Z0-9\.]+$/", $username))
	{
		error_report_show($page, INVALID_LOGIN);
	}
} else {
    $username = '';
}

if( isset( $_POST['pword'] ) ) {
    $password = $_POST['pword'];
    #added validation, to avoid sql injection
    if(!preg_match("/^[a-zA-Z0-9\.\-\*\+\?@_]+$/",$password)){
		error_report_show( $edit_page, INVALID_LOGIN );
	}
} else {
    $password = '';
}

auth_attempt_login($username, $password);

# save login
if( isset( $_POST['save_login'] ) ) {
	auth_set_login_cookies($username, $password);
}

# check for javascript
if( isset( $_POST['non_javascript_browser'] ) ) {
	session_set_javascript_enabled(false);
} else {
	session_set_javascript_enabled(true);
}

# if switch_project is not empty then switch project
if( !empty( $switch_project ) ) {

	include"login_switch_proj.php";

# else redirect to the appropriate page
} else {

	if( empty( $redirect_page ) ) {

		html_redirect("home_page.php");
	} else {

		html_redirect( $redirect_page."?".$redirect_page_get );
	}
}

# ------------------------------
# $Log: login_validate.php,v $
# Revision 1.3  2009/01/27 14:33:02  cryobean
# allow dots in username again
#
# Revision 1.2  2008/07/10 07:28:29  peter_thal
# security update:
# disabled writing spaces or apostrophe and others into login textfields
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------
?>
