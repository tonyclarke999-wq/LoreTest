<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# --------------------------------------------------
# Login Reset Password
#
# $RCSfile: login_reset_password_action.php,v $ $Revision: 1.8 $
# --------------------------------------------------

include"./api/include_api.php";
include"./api/rndPass.class.php";

html_window_title();
html_print_body();
html_page_title( lang_get('forgot_password_page') . PAGE_TITLE );

print"<div align=center>";

error_report_check( $_GET );

print"<br>";


# if user requests password to be reset
if( isset($_POST['email']) ) {

	$email			= $_POST['email'];

	$user_details	= user_get_info_by_email($email);

	# if user does not exist
	if( !$user_details ) {
		error_report_show( "login_reset_password_page.php", ERROR_ACCOUNT_NOT_FOUND );
	}

	# create reset link
	$reset		= new rndPass(25);
	$reset_link	= $reset->PassGen();

	# add reset link to the database
	user_new_reset_password($email, $reset_link);

	$url		= RTH_URL."login_reset_password_action.php?reset_link=$reset_link";

	$subject	 = "RTH: Reset Password Request";
	$message	 = "Someone has requested your RTH password to be reset. If it was not you, please ignore this email.". NEWLINE . NEWLINE;
	$message	.= "If you do want to reset your password, please click the link below:". NEWLINE;
	$message	.= "$url";

	email_send( $recipients=array($email), $subject, $message, $headers="RTH_Admin" );

	print lang_get( "new_reset_password" );

# if user clicks the reset link in email
} elseif( isset($_GET['reset_link']) ) {

	$reset_link		= $_GET['reset_link'];

	# create new password
	$password		= new rndPass(6);
	$new_password	= $password->PassGen();

	# reset password and return users email address
	$email			= user_reset_password($reset_link, $new_password);

	# if reset password was successful, send out email with new password details
	if( $email ) {

		$user_details	= user_get_info_by_email($email);
		$username		= $user_details[USER_UNAME];

		$url		= RTH_URL."login.php";

		$subject	 = "RTH: Password has been Reset";
		$message	 = "Your RTH password has been reset.". NEWLINE . NEWLINE;
		$message	.= "Username: $username". NEWLINE;
		$message	.= "Password: $new_password". NEWLINE . NEWLINE;
		$message	.= "You may change your password by clicking '".lang_get('user_link')."' on the RTH menu.". NEWLINE .NEWLINE;
		$message	.= "Click the following link to login to RTH:". NEWLINE;
		$message	.= "$url";

		email_send( $recipients=array($email), $subject, $message, $headers="RTH_Admin" );

		print lang_get( "reset_password" );

	} else {

		error_report_show( "login.php", ERROR_CANNOT_RESET_PASSWORD );
	}
}


print"</div>";

print"<br>";

//auth_authenticate_user();

html_print_footer();

# ------------------------------
# $Log: login_reset_password_action.php,v $
# Revision 1.8  2008/02/05 13:14:48  cryobean
# bugfixes
#
# Revision 1.7  2006/12/05 05:29:20  gth2
# updates for 1.6.1 release
#
# Revision 1.6  2006/10/11 02:40:24  gth2
# adding phpMailer - gth
#
# Revision 1.5  2006/08/06 01:29:21  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.4  2006/08/05 22:08:24  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/27 17:26:16  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------
?>