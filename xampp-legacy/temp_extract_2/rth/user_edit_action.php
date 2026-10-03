<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# User Edit Action Page
#
# $RCSfile: user_edit_action.php,v $  $Revision: 1.4 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page		= 'user_edit_page.php';
$edit_page			= 'user_edit_account_page.php';
$proj_properties	= session_set_properties("project_manage", $_POST);

session_validate_form_set($_POST, $edit_page);

# Check passwords match
if( session_validate_form_get_field("password")!==session_validate_form_get_field("password_confirm") ) {
	error_report_show( $edit_page, PASSWORDS_NOT_MATCH );
}
$password = session_validate_form_get_field("password");
if(!empty($password) && !preg_match("/^[a-zA-Z0-9\.\-\*\+\?@_]+$/",$password)){
	error_report_show( $edit_page, PASSWORD_INVALID );
}


###########################################################################################
# put project prefs into an array

$associated_projects = array();

# name all the project preferences
$project_preferences = array(	"project_rights",
								"delete_rights",
								"email_testset",
								"email_discussion",
								"email_new_bug",
								"email_update_bug",
								"email_assigned_bug",
								"email_bugnote_bug",
								"email_status_bug",
								"ba_owner",
								"qa_owner",
								"project_name",
								"remove" );

foreach($_POST as $key => $value) {

	# split associated projects into an array(project_id, _project_pref_name)
	$exploded_post 			= explode( "_", $key, 2 );
	$associated_project_id 	= $exploded_post[0];

	# add projects and preferences to $associated_projects.
	# the array format is:
	#	$associated_projects[project_id][project_preference] = project_preference_value
	#
	if( isset($exploded_post[1]) ) {
		if( util_array_value_search($exploded_post[1], $project_preferences) ) {

			$associated_projects[$associated_project_id][$exploded_post[1]] = $value;
		}
	}
}


# fill in any project prefs which are left out
# i.e. unchecked checkboxes
foreach($associated_projects as $key_project => $value_preferences) {

	# look for project prefs not set in $project_pref
	foreach($project_preferences as $row_project_preference) {
	
		if( !util_array_key_search($row_project_preference, $value_preferences) ) {
			$associated_projects[$key_project][$row_project_preference] = "N";
		}
	}
}

if( isset($_POST['add_to_projects']) ) {

	$add_to_projects = $_POST['add_to_projects'];
} else {
	$add_to_projects = array();
}

user_edit(	session_validate_form_get_field("username"),
			session_validate_form_get_field("password"),
			session_validate_form_get_field("first_name_required"),
			session_validate_form_get_field("last_name_required"),
			session_validate_form_get_field("email_required"),
			session_validate_form_get_field("phone"),
			$_POST['tempest_admin'],
			$_POST['default_project'],
			$associated_projects,
			$add_to_projects );

session_validate_form_reset();

# ---------------------------------------------------------------------
# $Log: user_edit_action.php,v $
# Revision 1.4  2008/08/04 06:54:58  peter_thal
# added sorting function to several tables
#
# Revision 1.3  2008/07/10 07:28:29  peter_thal
# security update:
# disabled writing spaces or apostrophe and others into login textfields
#
# Revision 1.2  2006/02/27 17:25:54  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:59  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>