<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# --------------------------------------------------
# Login Reset Password Page
#
# $RCSfile: login_reset_password_page.php,v $ $Revision: 1.2 $
# --------------------------------------------------

include_once"./api/include_api.php";

html_window_title();
html_print_body();
html_page_title( lang_get('forgot_password_page') . PAGE_TITLE );

print"<div align=center>";

error_report_check( $_GET );

print"<br>";

print"<form method=post action='login_reset_password_action.php'>";

print lang_get('email_address').": ";
print"<input type=text size=35 name=email>";
print"<input type=submit value='".lang_get("submit_btn")."'>";

print"</form>";

print"</div>";

html_print_footer();

# ------------------------------
# $Log: login_reset_password_page.php,v $
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------
?>