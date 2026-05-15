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
# $RCSfile: login_inc.php,v $ $Revision: 1.5 $
# --------------------------------------------------

$redirect_page 	= basename( $_SERVER['PHP_SELF'] );
$project_name	= "";
$get 			= "";

# get the query string and remove the invalid login error message
# if it is part of the query string
if( !empty($_SERVER['QUERY_STRING']) ) {

	$get = $_SERVER['QUERY_STRING'];
	$get = preg_replace("/&failed=true&error=10/", "", $get);
}

# if project_id set in the url get the project name for switching to the project
if( !empty($_GET['project_id']) ) {

	$project_name = project_get_name($_GET['project_id']);
}

if( !empty($_GET['page']) ) {

	$redirect_page = $_GET['page'];
}

# prevent getting redirected to a login_ page
if( strpos($redirect_page, 'login', 0)===0 ) {

	$redirect_page = "";
}

html_window_title();
html_print_body("login", "uname");

# display new RTH logo
print"<div style='text-align: center;'><IMG SRC='./images/login_logo.png'></div>". NEWLINE;
print"<br>". NEWLINE;

# no longer display stupid RTH String
#html_page_title(lang_get('login_page') . PAGE_TITLE);

error_report_check( $_GET );

print"<div align=center>". NEWLINE;
print"<table class=width40>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<form name=login action='login_validate.php' method=post>". NEWLINE;

# login variables taken from the query string
print"<input type=hidden name='login[switch_project]' value='$project_name'>". NEWLINE;
print"<input type=hidden name='login[page]' value='$redirect_page'>". NEWLINE;
print"<input type=hidden name='login[get]' value='$get'>". NEWLINE;

# Check for Javascript
print"<noscript>". NEWLINE;
print"<input type=hidden name=non_javascript_browser value=true>". NEWLINE;
print"</noscript>". NEWLINE;

print"<table class=inner>". NEWLINE;

# Form Title
print"<tr>". NEWLINE;
print"<td colspan=3 class=form-header-l>" . lang_get('login') ."</td>";
print"</tr>". NEWLINE;

# User Name Text Box
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('username') ."</td>". NEWLINE;
print"<td>&nbsp;</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type=text name=uname size=15></td>". NEWLINE;
print"</tr>". NEWLINE;

# Password Text Box
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('password') ."</td>". NEWLINE;
print"<td>&nbsp;</td>". NEWLINE;
print"<td class=left>". NEWLINE;
print"<input type=password name=pword size=15></td>". NEWLINE;
print"</tr>". NEWLINE;

# Save Login Checkbox
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>". lang_get('save_login') ."</td>". NEWLINE;
print"<td>&nbsp;</td>". NEWLINE;
print"<td class=left><input type=checkbox name=save_login></td>". NEWLINE;
print"</tr>". NEWLINE;

# Login Button
print"<tr>". NEWLINE;
print"<td colspan=3 class=center><input type='submit' value=". lang_get('submit_btn') ."></td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;
print"</form>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

# RESET PASSWORD
print"<br>". NEWLINE;
print"<a href='login_reset_password_page.php'>".lang_get('forgot_password')."</a>". NEWLINE;

//print"<p>username: demo</p>";
//print"<p>password: password</p>";

print"</div>". NEWLINE;

html_print_footer();

# ------------------------------
# $Log: login_inc.php,v $
# Revision 1.5  2009/02/03 12:44:07  sca_gs
# minor changes in formatting
# added logo to login page
#
# Revision 1.4  2007/03/14 17:45:53  gth2
# removing code that passes varables by reference - gth
#
# Revision 1.3  2006/08/05 22:08:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------
?>