<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Logout Page
#
# $RCSfile: logout.php,v $ $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";

##################################################################################
# Destroy the session and reload the login page                                  #
##################################################################################
session_end();

##################################################################################
# Destroy the cookie login information                                           #
##################################################################################
util_set_cookie(USER_COOKIE_NAME, "");
util_set_cookie(PWD_COOKIE_NAME, "");

html_redirect('login.php');

# ------------------------------------
# $Log: logout.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>