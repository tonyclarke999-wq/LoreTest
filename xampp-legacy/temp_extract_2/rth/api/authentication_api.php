<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Authentication API
#
# $RCSfile: authentication_api.php,v $ $Revision: 1.4 $
# ------------------------------------

# ----------------------------------------------------------------------
# Attempt to login the user with the given password
# OUTPUT:
#   Error message if login details do not match
#   Session is initialized if login details match
# ----------------------------------------------------------------------
function auth_attempt_login($username="", $password="") {

    $login_method = LOGIN_METHOD;

    if ( $login_method == 'LDAP' ) {
        if (ldap_authenticate($username, $password)) {

			#user successfully authenticated, proceed with login
			auth_login($username);
        }
    } else {
        if ( auth_does_password_match( $username, $password ) ) {

			#user successfully authenticated, proceed with login
			auth_login($username);
        }
    }

    # check if user logged in
    $logged_in = session_getLogged_in();


	# if user not logged in, login failed, redirect back to the page where the user
	# tried to login
	if( !$logged_in ) {

		$switch_project 	= $_POST['login']['switch_project'];
		$redirect_page		= $_POST['login']['page'];
		$redirect_page_get	= $_POST['login']['get'];

		# redirect to the appropriate page
		if( empty( $redirect_page ) ) {

			error_report_show( "home_page.php?", INVALID_LOGIN );
		} else {

			error_report_show( $redirect_page."?".$redirect_page_get, INVALID_LOGIN );
		}
    }
}


# ----------------------------------------------------------------------
# Check if given password matches that stored for the user
# OUTPUT:
#   False if login details do not match
#   True if login details do match
# ----------------------------------------------------------------------
function auth_does_password_match( $user_name, $password ) {

    # for encrypted passwords, first encrypt the given password for comparison
    if (LOGIN_METHOD == 'MD5') {
        $processed_password = auth_process_plain_password($password);
    }
    else {
        $processed_password = $password;
    }

    return auth_verify_login($user_name, $processed_password);
}


# ----------------------------------------------------------------------
# Encrypt given password
# INPUT
#   password
# OUTPUT:
#   md5 encrypted password
# ----------------------------------------------------------------------
function auth_process_plain_password( $password ) {

    $processed_password = md5( $password );

    return $processed_password;
}


# ----------------------------------------------------------------------
# Verify Login Details
# OUTPUT:
#   False if login details do not match
#   True if login details do match
# ----------------------------------------------------------------------
function auth_verify_login( $user_name, $password ) {

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_username				= $tbl_user .".". USER_UNAME;
	$f_user_lname			= $tbl_user .".". USER_LNAME;
	$f_user_fname			= $tbl_user .".". USER_FNAME;
	$f_user_password		= $tbl_user .".". USER_PWORD;

    $q = "	SELECT $f_user_id
    		FROM $tbl_user
    		WHERE $f_username = '$user_name'
    			AND $f_user_password = '$password' ";

    global $db;

    $record_set = db_query( $db, $q );
    $num = db_num_rows( $db, $record_set );

    if ($num == 0) {
        return false;
    }
    else {
        return true;
    }

}


# ----------------------------------------------------------------------
# Verify Login Details
# OUTPUT:
#   False if login details do not match
#   True if login details do match
# ----------------------------------------------------------------------
function auth_verify_user_settings($user_name) {

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_username				= $tbl_user .".". USER_UNAME;
	$f_user_lname			= $tbl_user .".". USER_LNAME;
	$f_user_fname			= $tbl_user .".". USER_FNAME;
	$f_user_password		= $tbl_user .".". USER_PWORD;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_proj_user_rights		= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;
	$f_email_testset		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_TESTSET;
	$f_email_discussion		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_REQ_DISCUSSION;
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

    $q = "	SELECT
    			$f_user_default_project,
    			$f_proj_user_rights
			FROM $tbl_user
			INNER JOIN $tbl_proj_user_assoc ON $f_proj_user_proj_id = $f_user_default_project
				AND $f_proj_user_user_id = $f_user_id
			WHERE $f_username = '$user_name'";

    global $db;

	$record_set = db_query( $db, $q );
    $row = db_fetch_row( $db, $record_set );

    # if the user doesn't have a default project. This should never happen.
    if( empty($row[USER_DEFAULT_PROJECT]) ) {
        return NO_DEFAULT_PROJ;
    }

    return 0;
}


# ----------------------------------------------------------------------
# Prepare Session for user
# OUTPUT:
#   Session has been set up for the user
# ----------------------------------------------------------------------
function auth_login($username) {

    # check users project rights
    $auth_return_code = auth_verify_user_settings( $username);

    if (($auth_return_code == NO_DEFAULT_PROJ) || ($auth_return_code == NO_USER_RIGHTS)) {
        error_report_show("login.php", $auth_return_code );
        exit;
    }

    session_initialize();
    session_setLogged_in(TRUE);
    session_set_application_details( user_get_default_project_name($username), $username );
}


# ----------------------------------------------------------------------
# Check user is logged in
# ----------------------------------------------------------------------
function auth_authenticate_user() {

	# check user logged into correct host
	$parsed_url = parse_url(RTH_URL);
		
	// account for host running on port other than 80
	if( array_key_exists('port', $parsed_url )  ) {
		$hostname = $parsed_url['host'] .":". $parsed_url['port'];
	}
	else {
		$hostname = $parsed_url['host'];
	}
	
	if( $_SERVER['HTTP_HOST'] != $hostname ) {
		html_redirect( RTH_URL."login.php" );
	}
	

    # check if user logged in
    $logged_in = session_getLogged_in();

	$login_cookie_username 	= util_get_cookie(USER_COOKIE_NAME, '');
	$login_cookie_pwd 		= util_get_cookie(PWD_COOKIE_NAME, '');

    if( !$logged_in ) {

        # User not logged in

		# try logging in using username from cookies and password from user
	    if( !empty($login_cookie_username) ) {

			include("login_confirm_password_inc.php");
			exit;

		# Else get username and password from user
        } else {

        	include("login_inc.php");
        	exit;
        }

    }

}


# ----------------------------------------------------------------------
# Set Tempest username and password cookies
# OUTPUT:
#   Two cookies have been stored for Tempest.
# ----------------------------------------------------------------------
function auth_set_login_cookies($username, $password){

    # when using encryption, encrypt password cookie
    if (LOGIN_METHOD == 'MD5') {
        $processed_password = auth_process_plain_password($password);
    }
    else {
        $processed_password = $password;
    }

    util_set_cookie( USER_COOKIE_NAME, $username);
    util_set_cookie( PWD_COOKIE_NAME, $processed_password);
}

# ------------------------------
# $Log: authentication_api.php,v $
# Revision 1.4  2007/03/14 17:21:26  gth2
# removing pass by reference - gth
#
# Revision 1.3  2006/08/01 23:42:56  gth2
# fixing case sensativity errors reported by users - gth
#
# Revision 1.2  2006/06/30 00:55:42  gth2
# removing &$db from api files - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# ------------------------------
?>
