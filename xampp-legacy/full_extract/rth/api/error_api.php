<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Error API
#
# $RCSfile: error_api.php,v $ $Revision: 1.2 $
# ------------------------------------

# ----------------------------------------------------------------------
# Set error handler function
# ----------------------------------------------------------------------
if ( DEBUG == ON ) {
    set_error_handler ('error_debug_handler');
} else {
    set_error_handler ('error_user_handler');
}

# ----------------------------------------------------------------------
# Place at the top of any page that contains a form for validation.
# If the user is redirected back to the form with an error code this function
# will call the function that displays the error message
# OUTPUT:
#   if error is set on request, display an appropriate warning message
# ----------------------------------------------------------------------
function error_report_check( $get_vars ) {

    if( empty( $get_vars['failed'] ) ) {
        return;
    } else {
        error_report_display_msg( $get_vars['error'] );
    }

}

# ----------------------------------------------------------------------
# Place at the top of any page that contains a form for validation.
# Redirect user and set up error message for display
# INPUT:
#   $redirect_page: name of page to redirect to
#   $err_no: warning to display (see properties_inc)
# ----------------------------------------------------------------------
function error_report_show( $redirect_page, $err_no ) {

	if( stristr( $redirect_page, '?' ) ) {
		$concat = '&';
	} else {
		$concat = '?';
	}

    $page = $redirect_page . $concat ."failed=true&error=$err_no";

    html_redirect( $page );

    exit;
}

# ----------------------------------------------------------------------
# Display warning message to user
# INPUT:
#   $err_no: Error to display
# ----------------------------------------------------------------------
function error_report_display_msg( $err_no ) {
    # errors are defined in properties_inc.php and strings_english.txt
    $err_array = lang_get( 'err_msg' );
    $message = $err_array[$err_no];
    print"<p class=error>$message</p>";

}

# ----------------------------------------------------------------------
# Error handler for production mode
# Output:
#   Appropriate error message
# ----------------------------------------------------------------------
function error_user_handler( $errno, $errstr, $errfile, $errline) {

    # check if errors were disabled with @ somewhere in this call chain
    if ( 0 == error_reporting() ) {
        return;
    }

    # format error message for writing to mail and logs
    $err_msg = error_format($errno, $errstr, $errfile, $errline);

    switch($errno) {
        // system warning
        case E_WARNING:
            error_admin_alert($err_msg);
            if ( ON == SHOW_WARNINGS ) {
                error_user_message('System Warning', $errstr, $errfile, $errline);
                die();
            }
            break;

        // system notice
        case E_NOTICE:
            error_admin_alert($err_msg);
            if ( ON == SHOW_NOTICES ) {
                error_user_message('System Notice', $errstr, $errfile, $errline);
                die();
            }
            break;

        // user-triggered fatal error
        case E_USER_ERROR:
            error_admin_alert($err_msg);
            error_user_message('User Defined Error', error_get_user_message ($errstr), $errfile, $errline);
            die();
			break;

        // user-triggered warning
        case E_USER_WARNING: // not currently used
            error_user_message('User Defined Warning', error_get_user_message ($errstr), $errfile, $errline);
            error_admin_alert($err_msg);
        	break;
        // user-triggered notice
        case E_USER_NOTICE: // not currently used
            error_user_message('User Defined Notice', error_get_user_message ($errstr), $errfile, $errline);
            error_admin_alert($err_msg);
        	break;

    }
}


# ----------------------------------------------------------------------
# Error handler for debug mode
# Output:
#   Appropriate error message
# ----------------------------------------------------------------------
function error_debug_handler( $errno, $errstr, $errfile, $errline)
{
    # check if errors were disabled with @ somewhere in this call chain
    if ( 0 == error_reporting() ) {
        return;
    }

    switch($errno)
    {

        // system warning
        case E_WARNING:
            error_debug_message('System Warning', $errstr, $errfile, $errline);
        break;

        // system warning
        case E_NOTICE:
            error_debug_message('System Notice', $errstr, $errfile, $errline);
        break;

        // user-triggered fatal error
        case E_USER_ERROR:
            error_debug_message('User Defined Error', error_get_user_message ($errstr), $errfile, $errline);
            die();
        break;

        // user-triggered warning
        case E_USER_WARNING: // not currently used
            error_debug_message('User Defined Warning', error_get_user_message ($errstr), $errfile, $errline);
        break;
        // user-triggered notice
        case E_USER_NOTICE: // not currently used
            error_debug_message('User Defined Notice', error_get_user_message ($errstr), $errfile, $errline);
        break;

    }
}


# ----------------------------------------------------------------------
# Show debug mode error message
# Output:
#   Appropriate error message
# ----------------------------------------------------------------------
function error_debug_message($error_type, $errstr, $errfile, $errline){
    echo'<table class=hide75 align=center>';
    echo"<tr><td class=error>$error_type :- " . $errstr ."</td></tr>";
    echo"<tr><td class=error>File: ". $errfile ."</td></tr>";
    echo"<tr><td class=error>Line: ". $errline ."</td></tr>";
    echo'</table>';
}


# ----------------------------------------------------------------------
# Show production mode error message
# Output:
#   Appropriate error message
# ----------------------------------------------------------------------
function error_user_message($error_type, $errstr, $errfile, $errline){
    echo'<table class=hide75 align=center>\n';
    echo'<tr><td class=error>$error_type :- ' . $errstr .'</td></tr>\n';
    echo'<tr><td class=error>File: '. basename($errfile) .'</td></tr>\n';
    echo'<tr><td class=error>Line: '. $errline .'</td></tr>\n';
    echo'</table>\n';
}


# ----------------------------------------------------------------------
# Map PHP Error constants to their types for display
# Output:
#   English Error Type
# ----------------------------------------------------------------------
function error_type_map($errno) {

    $error_constant;

    switch($errno)
    {
        case E_ERROR:
            $error_constant = 'E_ERROR';
            break;
        case E_WARNING:
            $error_constant = 'E_WARNING';
            break;
        case E_NOTICE:
            $error_constant = 'E_NOTICE';
            break;
        case E_USER_ERROR:
            $error_constant = 'E_USER_ERROR';
            break;
        case E_USER_WARNING:
            $error_constant = 'E_USER_WARNING';
            break;
        case E_USER_NOTICE:
            $error_constant = 'E_USER_NOTICE';
            break;
        default:
            $error_constant = "Error Value = $errno";
            break;
    }
    return $error_constant;
}


# ----------------------------------------------------------------------
# Format Error Message
# Output:
#   Formatted error message text
# ----------------------------------------------------------------------
function error_format($errno, $errstr, $errfile, $errline) {

    $dt = date("Y-m-d H:i:s (T)");
    $user_details = session_get_user_properties();
    $project_details = session_get_project_properties();
    $user_name = $user_details['username'];
    $project_name = $project_details['project_name'];

    $error_type = error_type_map($errno);
    $err = "$error_type". NEWLINE;
    $err .= "Time: " . $dt . "". NEWLINE;
    $err .= "Message: " . $errstr . "". NEWLINE;
    $err .= "File: " . $errfile . "". NEWLINE;
    $err .= "Line Number: " . $errline. "". NEWLINE;
    $err .= "User: " . $user_name. "". NEWLINE;
    $err .= "Project: " . $project_name. "". NEWLINE;
    $err .= "Server: " . $_SERVER['SERVER_NAME']. "". NEWLINE;
    return $err;
}


# ----------------------------------------------------------------------
# Alert administrators to error condition
# Output:
#   Error is logged on server
#   Error is mailed to administrator
# ----------------------------------------------------------------------
function error_admin_alert($err_msg) {

    //error_log($err_msg, 3, $g_tempest_log);
    error_log($err_msg, 1, ADMIN_EMAIL);
}


# ----------------------------------------------------------------------
# Translate error message code to error message text
# Output:
#   Error message text
# ----------------------------------------------------------------------
function error_get_user_message($err_num) {

    $err_array = lang_get( 'err_msg' );

    if (is_numeric($err_num)) {
        $err_array = lang_get( 'err_msg' );
        return $err_array[$err_num];
    }
    else {
        // adodb text error - just pass back
        return $err_num;
    }
}

# --------------------------------------------------------
# $Log: error_api.php,v $
# Revision 1.2  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# --------------------------------------------------------
?>
