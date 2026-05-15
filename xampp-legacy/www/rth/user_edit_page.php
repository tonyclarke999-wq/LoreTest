<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# User Edit Page
#
# $RCSfile: user_edit_page.php,v $  $Revision: 1.5 $
# ---------------------------------------------------------------------

$display_options	= session_set_display_options("user_edit", $_POST);
$order_by			= $display_options['order_by'];
$order_dir			= $display_options['order_dir'];
$page_number		= $display_options['page_number'];
$row_style 			= '';

$user_assoc_project_names	= array();
$rows_user_projects 		= user_get_projects_info($selected_user_id, $order_by, $order_dir);
$user_info 					= user_get_info($selected_user_id);

$selected_username	= $user_info[USER_UNAME];
$selected_firstname	= $user_info[USER_FNAME];
$selected_lastname	= $user_info[USER_LNAME];
$selected_email		= $user_info[USER_EMAIL];
$selected_phone		= $user_info[USER_PHONE];
$selected_email		= $user_info[USER_EMAIL];
$selected_admin		= $user_info[USER_ADMIN];

$tempest_admin					= user_has_rights( $project_id, $user_id, ADMIN );
$selected_user_tempest_admin	= user_has_rights( $project_id, $selected_user_id, ADMIN );

$user_associated_project_names = array();

foreach($rows_user_projects as $row_user_project) {
	$user_associated_project_names[$row_user_project[PROJ_ID]] = $row_user_project[PROJ_NAME];
}

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('user_edit_page') );
html_page_header( $db, $project_name );
html_print_menu();
admin_user_print( $page, $project_id, $user_id );

print"<br>". NEWLINE;

error_report_check( $_GET );

print"<div align=center>";
print"<form method=post action='$action_page'>";

########################################################################################
# Below, fields which should not be available to the user because of their access rights
# will be disabled. When a field is disabled, it does not return a value. In this case a
# hidden field of the same name is used to hold the value.
########################################################################################

########################################################################################
# Edit User Table
$user_unassociated_projects = user_get_unassociated_projects($selected_user_id, PROJ_NAME, "ASC");

if( $selected_user_tempest_admin ) {

	print"$selected_username is an Administrator with access to all projects. However, you can add $selected_username to projects and select preferences for each project.<br><br>";
}

print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class=width80 rules=cols>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;
print"<table class=inner>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=form-header-l colspan=3>".lang_get('edit_user')."</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;

# Username
print"<td class=form-lbl-r width='33%'>".lang_get('username')."</td>". NEWLINE;
print"<td class=form-lbl-l width='33%'>$selected_username<input type=hidden name=username value='$selected_username'";
	if (!$tempest_admin && ($user_id!=$selected_user_id)) {
		print ' disabled';
	}
	print"></td>". NEWLINE;

	if ( $tempest_admin && !empty($user_unassociated_projects) ) {
		print"<td class=form-lbl-l>";
		print lang_get("add_to_projects");
		print"</td>". NEWLINE;
	}

print"</tr>". NEWLINE;
print"<tr>". NEWLINE;

# Change Password
print"<td class=form-lbl-r>".lang_get('change_password')."</td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=11 type=password name='password' maxlength=20 ";
	if (!$tempest_admin && ($user_id!=$selected_user_id)) {
		print ' disabled';
	}
	print"></td>". NEWLINE;

# Add To Projects
if ( $tempest_admin && !empty($user_unassociated_projects) ) {
	print"<td class=form-lbl-l width='33%' rowspan=8 valign=top>". NEWLINE;
	print"<select tabindex=100 name='add_to_projects[]' size=11 multiple>";
	html_print_list_box_from_array($user_unassociated_projects, session_validate_form_get_field("add_to_projects"));
	print"</select>". NEWLINE;
	print"</td>". NEWLINE;
}
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
# Change Password Confirm
print"<td class=form-lbl-r>".lang_get('password_confirm')."</td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=15 type=password name='password_confirm' maxlength=20 ";
	if (!$tempest_admin && ($user_id!=$selected_user_id)) {
		print ' disabled';
	}
	print"></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;

# First Name
print"<td class=form-lbl-r>".lang_get('first_name')."<span class='required'>*</span></td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=20 type=text name='first_name_required' maxlength=20 value='".session_validate_form_get_field("first_name_required", $selected_firstname)."'";
	if (!$tempest_admin && ($user_id!=$selected_user_id)) {
		print ' disabled';
	}
	print"></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;

# Last Name
print"<td class=form-lbl-r>".lang_get('last_name')."<span class='required'>*</span></td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=25 type=text name='last_name_required' maxlength=20 value='".session_validate_form_get_field("last_name_required", $selected_lastname)."'";
	if (!$tempest_admin && ($user_id!=$selected_user_id)) {
		print ' disabled';
	}
	print"></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;

# Email
print"<td class=form-lbl-r>".lang_get('email')."<span class='required'>*</span></td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=30 type=text name='email_required' maxlength=50 value='".session_validate_form_get_field("email_required", $selected_email)."'";
	if (!$tempest_admin && ($user_id!=$selected_user_id)) {
		print ' disabled';
	}
	print"></td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;

# Phone
print"<td class=form-lbl-r>".lang_get('phone')."</td>". NEWLINE;
print"<td class=form-lbl-l><input tabindex=35 type=text name='phone' maxlength=25 value='".session_validate_form_get_field("phone", $selected_phone)."'";
	if (!$tempest_admin && ($user_id!=$selected_user_id)) {
		print ' disabled';
	}
	print"></td>". NEWLINE;
print"</tr>". NEWLINE;

# Default Project
$default_project_id = user_get_default_project_id($selected_username);

if( !empty($user_associated_project_names) ) {

	print"<tr>". NEWLINE;
	print"<td class=form-lbl-r>".lang_get('default_project')."</td>". NEWLINE;
	print"<td class=form-lbl-l>". NEWLINE;
	print"<select tabindex=101 name='default_project' size=1";
		if (!$tempest_admin && ($user_id!=$selected_user_id)) {
			print ' disabled';
		}
		print">". NEWLINE;
	html_print_list_box_from_key_array(	$user_associated_project_names,
										session_validate_form_get_field("default_project",
																		$default_project_id ) );
	print"</select>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
} else {

	print"<tr><td colspan=2>". NEWLINE;
	print"<input type=hidden name=default_project value=$default_project_id>". NEWLINE;
	print"</td></tr>". NEWLINE;
}

# Tempest Rights
print"<tr>". NEWLINE;
print"<td class=form-lbl-r>".lang_get('tempest_admin')."</td>". NEWLINE;
print"<td class=form-lbl-l>". NEWLINE;
print"<input type=hidden name='tempest_admin' value='N'>". NEWLINE;
print"<input tabindex=60 type=checkbox name='tempest_admin' value='Y' ";
	if (session_validate_form_get_field('tempest_admin', $selected_admin)=="Y") {
		print ' checked';
	}
	if (!$tempest_admin) {
		print" disabled>". NEWLINE;
	} else {
		print">". NEWLINE;
	}
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class=center colspan=3><br><input tabindex=200 type=submit name='submit_button' value='".lang_get("edit")."'><br>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"<br>";

##########################################################################################
# User Associated Projects Table
#
//print$selected_user_tempest_admin;
if ( !empty($rows_user_projects) ) {
	print"<table class=width100 rules=cols>". NEWLINE;

	print"<tr>". NEWLINE;
	html_tbl_print_header( lang_get('project_name'),		PROJ_NAME,						$order_by, $order_dir );
	html_tbl_print_header( lang_get('project_user_rights'),	PROJ_USER_PROJECT_RIGHTS,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('delete_rights'),		PROJ_USER_DELETE_RIGHTS,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('email_testset'),		PROJ_USER_EMAIL_TESTSET, 		$order_by, $order_dir );
	html_tbl_print_header( lang_get('email_discussions'),	PROJ_USER_EMAIL_REQ_DISCUSSION,	$order_by, $order_dir );
	html_tbl_print_header( lang_get('email_new_bug'),		PROJ_USER_EMAIL_NEW_BUG, 		$order_by, $order_dir );
	html_tbl_print_header( lang_get('email_update_bug'),	PROJ_USER_EMAIL_UPDATE_BUG,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('email_assigned_bug'),	PROJ_USER_EMAIL_ASSIGNED_BUG,	$order_by, $order_dir );
	html_tbl_print_header( lang_get('email_bugnote_bug'),	PROJ_USER_EMAIL_BUGNOTE_BUG,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('email_status_bug'),	PROJ_USER_EMAIL_STATUS_BUG,		$order_by, $order_dir );
	html_tbl_print_header( lang_get('qa_tester'),			PROJ_USER_QA_OWNER,				$order_by, $order_dir );
	html_tbl_print_header( lang_get('ba_owner'), 			PROJ_USER_BA_OWNER,				$order_by, $order_dir );
	if ( $tempest_admin ) {
		html_tbl_print_header( lang_get('remove') );
	}
	print"</tr>". NEWLINE;

	foreach($rows_user_projects as $user_project_row) {

		$row_style = html_tbl_alternate_bgcolor($row_style);

		$assoc_project_id = $user_project_row[PROJ_ID];

		print"<tr class='$row_style'>". NEWLINE;

		# project name
		print"<td>".$user_project_row[PROJ_NAME]."<input type=hidden name='$assoc_project_id"."_project_name' value='".$user_project_row[PROJ_NAME]."'></td>". NEWLINE;

		# project rights
		print"<td>". NEWLINE;
		print"<input type=hidden name='$assoc_project_id"."_project_rights' value='".$user_project_row[PROJ_USER_PROJECT_RIGHTS]."'>". NEWLINE;
		print"<select name='$assoc_project_id"."_project_rights' size=1";
		if (!user_has_rights( $assoc_project_id, $user_id, MANAGER )) {
			print ' disabled';
		}
		print">". NEWLINE;

		$selected_user_rights = session_validate_form_get_field( $assoc_project_id."project_rights", $user_project_row[PROJ_USER_PROJECT_RIGHTS] );
		html_print_user_rights_list_box( $selected_user_rights );

		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# project delete rights
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_delete_rights' ";

			if (session_validate_form_get_field($assoc_project_id."_delete_rights", $user_project_row[PROJ_USER_DELETE_RIGHTS])=="Y") {
				print ' checked';
			}

			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER )) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_delete_rights' value='".$user_project_row[PROJ_USER_DELETE_RIGHTS]."'>". NEWLINE;
			} else {
				print">";
			}

			print"</td>". NEWLINE;

		# email testset
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_email_testset' ";

			if (session_validate_form_get_field($assoc_project_id."_email_testset", $user_project_row[PROJ_USER_EMAIL_TESTSET])=="Y") {
				print ' checked';
			}

			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER ) && ($user_id!=$selected_user_id)) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_email_testset' value='".$user_project_row[PROJ_USER_EMAIL_TESTSET]."'>". NEWLINE;
			} else {
				print">";
			}
			print"</td>". NEWLINE;

		# email discussion
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_email_discussion' ";

			if (session_validate_form_get_field($assoc_project_id."_email_discussion", $user_project_row[PROJ_USER_EMAIL_REQ_DISCUSSION])=="Y") {
				print ' checked';
			}

			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER ) && ($user_id!=$selected_user_id)) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_email_discussion' value='".$user_project_row[PROJ_USER_EMAIL_REQ_DISCUSSION]."'>". NEWLINE;
			} else {
				print">";
			}
			print"</td>". NEWLINE;

		# email new bug
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_email_new_bug' ";

			if (session_validate_form_get_field($assoc_project_id."_email_new_bug", $user_project_row[PROJ_USER_EMAIL_NEW_BUG])=="Y") {
				print ' checked';
			}

			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER ) && ($user_id!=$selected_user_id)) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_email_new_bug' value='".$user_project_row[PROJ_USER_EMAIL_NEW_BUG]."'>". NEWLINE;
			} else {
				print">";
			}
			print"</td>". NEWLINE;

		# email update bug
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_email_update_bug' ";

			if (session_validate_form_get_field($assoc_project_id."_email_update_bug", $user_project_row[PROJ_USER_EMAIL_UPDATE_BUG])=="Y") {
				print ' checked';
			}

			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER ) && ($user_id!=$selected_user_id)) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_email_update_bug' value='".$user_project_row[PROJ_USER_EMAIL_UPDATE_BUG]."'>". NEWLINE;
			} else {
				print">";
			}
			print"</td>". NEWLINE;

		# email assigned bug
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_email_assigned_bug' ";

			if (session_validate_form_get_field($assoc_project_id."_email_assigned_bug", $user_project_row[PROJ_USER_EMAIL_ASSIGNED_BUG])=="Y") {
				print ' checked';
			}

			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER ) && ($user_id!=$selected_user_id)) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_email_assigned_bug' value='".$user_project_row[PROJ_USER_EMAIL_ASSIGNED_BUG]."'>". NEWLINE;
			} else {
				print">";
			}
		print"</td>". NEWLINE;

		# email bugnote bug
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_email_bugnote_bug' ";

			if (session_validate_form_get_field($assoc_project_id."_email_bugnote_bug", $user_project_row[PROJ_USER_EMAIL_BUGNOTE_BUG])=="Y") {
				print ' checked';
			}

			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER ) && ($user_id!=$selected_user_id)) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_email_bugnote_bug' value='".$user_project_row[PROJ_USER_EMAIL_BUGNOTE_BUG]."'>". NEWLINE;
			} else {
				print">";
			}
		print"</td>". NEWLINE;

		# email status bug
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_email_status_bug' ";

			if (session_validate_form_get_field($assoc_project_id."_email_status_bug", $user_project_row[PROJ_USER_EMAIL_STATUS_BUG])=="Y") {
				print ' checked';
			}

			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER ) && ($user_id!=$selected_user_id)) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_email_status_bug' value='".$user_project_row[PROJ_USER_EMAIL_STATUS_BUG]."'>". NEWLINE;
			} else {
				print">";
			}
		print"</td>". NEWLINE;

		# qa owner
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_qa_owner' ";
			if (session_validate_form_get_field($assoc_project_id."_qa_owner", $user_project_row[PROJ_USER_QA_OWNER])=="Y") {
				print ' checked';
			}
			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER ) && ($user_id!=$selected_user_id)) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_qa_owner' value='".$user_project_row[PROJ_USER_QA_OWNER]."'>". NEWLINE;
			} else {
				print">";
			}
			print"</td>". NEWLINE;

		#ba owner
		print"<td>". NEWLINE;
		print"<input type=checkbox value='Y' name='$assoc_project_id"."_ba_owner' ";

			if (session_validate_form_get_field($assoc_project_id."_ba_owner", $user_project_row[PROJ_USER_BA_OWNER])=="Y") {
				print ' checked';
			}
			if (!user_has_rights( $assoc_project_id, $user_id, MANAGER ) && ($user_id!=$selected_user_id)) {
				print ' disabled>';
				print"<input type=hidden name='$assoc_project_id"."_ba_owner' value='".$user_project_row[PROJ_USER_BA_OWNER]."'>";
			} else {
				print">";
			}
			print"</td>". NEWLINE;

		# remove from project
		if ( $tempest_admin ) {
			print"<td><input type=checkbox value='Y' name='$assoc_project_id"."_remove' ";
				if (!user_has_rights( $assoc_project_id, $user_id, MANAGER )) {
					print ' disabled';
				}
				if (session_validate_form_get_field($assoc_project_id."_remove")) {
					print ' checked';
				}
				print"></td>". NEWLINE;
		}

		print"</tr>". NEWLINE;
	}
	print"</table>". NEWLINE;

	print"<br>". NEWLINE;
	print"<input type=submit name='submit_button' value='".lang_get("edit")."'>". NEWLINE;
}

print"</form>". NEWLINE;
print"</div>". NEWLINE;
html_print_footer();

# ---------------------------------------------------------------------
# $Log: user_edit_page.php,v $
# Revision 1.5  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.4  2006/02/27 17:25:54  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.3  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.2  2006/02/06 13:08:20  gth2
# fixing minor bugs - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:59  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>
