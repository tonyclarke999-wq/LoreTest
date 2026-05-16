<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# --------------------------------------------------
# Login Page
#
# $RCSfile: login.php,v $ $Revision: 1.2 $
# --------------------------------------------------

include("./api/include_api.php");
auth_authenticate_user();


$login_cookie_username 	= util_get_cookie(USER_COOKIE_NAME, '');
$login_cookie_pwd 		= util_get_cookie(PWD_COOKIE_NAME, '');



# try logging in using username from cookies and password from user
if( !empty($login_cookie_username) ) {

	include("login_confirm_password_inc.php");
	exit;

# Else get username and password from user
} else {

	include("login_inc.php");
	exit;
}


# ------------------------------------
# $Log: login.php,v $
# Revision 1.2  2006/08/01 23:42:56  gth2
# fixing case sensativity errors reported by users - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>
