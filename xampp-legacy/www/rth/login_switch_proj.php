<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: login_switch_proj.php,v $ $Revision: 1.1.1.1 $
# ------------------------------------

include_once"./api/include_api.php";

$logged_in 			= session_getLogged_in();
$username			= $_POST['uname'];
$switch_project 	= $_POST['login']['switch_project'];
$redirect_page		= $_POST['login']['page'];
$redirect_page_get	= $_POST['login']['get'];

# If user not logged in, then redirect back to the page they tried to login from
# auth_authenticate_user() will display the login forms
if(!$logged_in) {

	html_redirect( $redirect_page."?".$redirect_page_get );
}

# Check that $switch_project is not blank and that the user has access rights to the project.
# Doing this to check access rights when loggin in from urls that contain the $_GET[project_id] variable.
if( !empty($switch_project) && user_has_rights( project_get_id($switch_project), user_get_id($username), USER) ) {

    $new_project_name = $switch_project;
} else {

    error_report_show('login.php', PROJECT_SWITCH_FAILED);
}

session_set_new_project_name($new_project_name);
session_reset_project();
session_initialize();

session_setLogged_in(TRUE);
session_set_application_details( $new_project_name, session_get_username() );

if( isset($_POST['javascript_disabled']) ) {

	session_set_javascript_enabled( false );
} else {
	session_set_javascript_enabled( true );
}


# redirect to the appropriate page
if( !empty( $redirect_page ) ) {

	html_redirect( $redirect_page."?".$redirect_page_get );
} else {

	html_redirect("home_page.php");
}

# ------------------------------------
# $Log: login_switch_proj.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------

?>