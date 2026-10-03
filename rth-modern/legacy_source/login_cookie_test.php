<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Test if Cookies are enabled
#
# $RCSfile: login_cookie_test.php,v $ $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";

$login_cookie_username = util_get_cookie(USER_COOKIE_NAME, '');
$login_cookie_pwd = util_get_cookie(PWD_COOKIE_NAME, '');

if ( empty($login_cookie_username) || empty($login_cookie_pwd) ) {

	error_report_show("login.php", COOKIES_NOT_ENABLED);
	exit;
}

html_redirect("main.php");

# ------------------------------
# $Log: login_cookie_test.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------

?>
