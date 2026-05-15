<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# User Add Action Page
#
# $RCSfile: user_add_action.php,v $  $Revision: 1.10 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
include"./api/rndPass.class.php";
auth_authenticate_user();

$redirect_page		= 'user_add_page.php';
$edit_page			= 'user_add_page.php';
$proj_properties	= session_set_properties("project_manage", $_POST);

session_validate_form_set($_POST, $edit_page);

$username		= session_validate_form_get_field("username_required");
$first_name		= session_validate_form_get_field("first_name_required");
$last_name		= session_validate_form_get_field("last_name_required");
$email			= session_validate_form_get_field("email_required");
$phone			= session_validate_form_get_field("phone");
$projects		= session_validate_form_get_field("user_add_to_projects_required");

# check username unique
if( user_get_id($username) ) {
	error_report_show( $edit_page, USERNAME_NOT_UNIQUE );
}

# check if username contains blanks
$blank = ' ';   //whitespace
if(!(strstr($username,$blank) == false)){
	error_report_show( $edit_page, USERNAME_CONTAINS_BLANK);
}
if (!preg_match("/^[a-zA-Z0-9\.]+$/", $username)){
	error_report_show( $edit_page, USERNAME_CONTAINS_INVALID_CHARS);
}

# check email unique
if( user_get_info_by_email($email) ) {
	error_report_show( $edit_page, EMAIL_NOT_UNIQUE );
}

# create new password
$password		= new rndPass(6);
$new_password	= $password->PassGen();


user_add(	$username,
			$new_password,
			$first_name,
			$last_name,
			$email,
			$phone,
			isset($_POST['user_tempest_admin']) ? "Y": "N",
			isset($_POST['user_delete_rights']) ? "Y": "N",
			isset($_POST['user_email_testset']) ? "Y": "N",
			isset($_POST['user_email_discussions']) ? "Y": "N",
			isset($_POST['user_qa_owner']) ? "Y": "N",
			isset($_POST['user_ba_owner']) ? "Y": "N",
			$projects,
			$_POST['user_project_rights'],
			$_POST['user_default_project'] );


#################################################################################
$url		= RTH_URL."login.php";

$subject	 = "New RTH User Account";
$message	 = "Welcome to RTH. Here is the information you need to login:". NEWLINE . NEWLINE;
$message	.= "Username: $username". NEWLINE;
$message	.= "Password: $new_password". NEWLINE . NEWLINE;
$message	.= "Click the link below to login:". NEWLINE;
$message	.= "$url";

email_send( $recipients=array($email), $subject, $message );
#################################################################################

session_validate_form_reset();

html_print_operation_successful( "add_users_page", $redirect_page );

# ---------------------------------------------------------------------
# $Log: user_add_action.php,v $
# Revision 1.10  2009/01/27 14:33:00  cryobean
# allow dots in username again
#
# Revision 1.9  2008/07/10 07:28:29  peter_thal
# security update:
# disabled writing spaces or apostrophe and others into login textfields
#
# Revision 1.8  2008/07/01 13:46:23  peter_thal
# now usernames can't contain whitespaces
#
# Revision 1.7  2008/01/22 07:15:50  cryobean
# fixed bug which causes that no user couldn't be added after installation of RTH, because of a bad include
#
# Revision 1.6  2006/12/05 05:02:05  gth2
# display deleted users on user page - gth
#
# Revision 1.5  2006/10/11 02:40:05  gth2
# adding phpMailer - gth
#
# Revision 1.4  2006/10/04 00:10:16  gth2
# fixing problem with case sensativity with rndPass - gth
#
# Revision 1.3  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/08/01 23:42:56  gth2
# fixing case sensativity errors reported by users - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:59  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>