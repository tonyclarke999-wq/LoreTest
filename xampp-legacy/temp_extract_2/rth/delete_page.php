<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Delete Page
#
# $RCSfile: delete_page.php,v $  $Revision: 1.4 $
# ------------------------------------

include_once"./api/include_api.php";
auth_authenticate_user();

# Declare variables
$delete_successful = false;

session_set_properties( "release", $_GET );

$project_properties 	= session_get_project_properties();
$project_name 			= $project_properties['project_name'];
$project_id 			= $project_properties['project_id'];
$page 					= basename(__FILE__);

if( !isset( $_POST['confirm'] ) ) {
	html_window_title();
	html_print_body();
	html_page_title($project_name ." - ". lang_get('delete_page') );
	html_page_header( $db, $project_name );
	html_print_menu();

	print"<br>". NEWLINE;

	# User came from a link, now check if all post data is there
	if( isset( $_POST['r_page'] ) && isset( $_POST['f'] ) && isset( $_POST['id'] ) && isset( $_POST['msg'] ) ) {
		print"<div align=center>". NEWLINE;
		print"<table class=width40>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<form name='delete' action=$page method='post'>". NEWLINE;
		print"<input type='hidden' name='confirm_f' value='".$_POST['f']."'>". NEWLINE;
		print"<input type='hidden' name='confirm_id' value='".$_POST['id']."'>". NEWLINE;
		print"<input type='hidden' name='confirm_r_page' value='".$_POST['r_page']."'>". NEWLINE;
		print"<input type='hidden' name='confirm_msg' value='".$_POST['msg']."'>". NEWLINE;
		# For removing users from projects
		if( isset( $_POST['project_id'] ) && $_POST['project_id'] != '' ) {
			print"<input type='hidden' name='confirm_project_id' value='".$_POST['project_id']."'>". NEWLINE;
		}
		print"<table class=inner rules='none' border='0'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td colspan=2>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td colspan=2 class=form-lbl-c>".util_get_delete_msg( $_POST['msg'] )."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td colspan=2>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='center'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='confirm' value='". lang_get('yes') ."'></td>". NEWLINE;
		print"<td class='left'><input type='submit' name='confirm' value='". lang_get('no') ."'></td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</div>". NEWLINE;
	# Not all get data is set
	} else {
		print"<div class='error'>".ERROR_NO_DATA."</div>". NEWLINE;
	}
	html_print_footer();
# User submitted the form, check which option was selected
} elseif( $_POST['confirm'] == lang_get('no') ) {
	# User does not want to delete record
	html_redirect( $_POST['confirm_r_page'] );
} elseif( $_POST['confirm'] == 'Yes' ) {
	switch ( $_POST['confirm_f'] ) {
	case "delete_release":
		admin_release_delete( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_build":
		admin_build_delete( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_testset":
		admin_testset_delete( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_user_from_project":
		project_remove_user( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_area_tested_from_project":
		project_remove_area_tested( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_machine_from_project":
		project_remove_machine( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_testtype_from_project":
		project_remove_testtype( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_man_doc_type_from_project":
		project_remove_man_doc_type( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_req_doc_type_from_project":
		project_remove_req_doc_type( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_environment_from_project":
		project_remove_environment( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_req_area_covered_from_project":
		project_remove_req_area_covered( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_req_functionality_from_project":
		project_remove_req_functionality( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_test_step":
		test_delete_test_step( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_project":
		project_delete( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_user":
		user_delete( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_requirement":
		############################################################################
		# EMAIL NOTIFICATION
		############################################################################
		$recipients		= requirement_get_notify_users($_POST['confirm_project_id'], $_POST['confirm_id']);
		requirement_email($_POST['confirm_project_id'], $_POST['confirm_id'], $recipients, $action="delete");
		############################################################################
		############################################################################
		requirement_delete( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_test":
		test_delete( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_news_post":
		news_delete( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_bug_category_from_project":
		project_remove_bug_category( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "remove_bug_component_from_project":
		project_remove_bug_component( $_POST['confirm_project_id'], $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_bug":
		bug_delete( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_bugnote":
		bug_delete_bugnote( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_bug_assoc":
		bug_delete_bug_assoc( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_testplan":
		testset_delete_test_plan( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_uploaded_testrun_document":
		file_delete_test_run_doc( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	case "delete_screen":
		test_delete_screen( $_POST['confirm_id'] );
		$delete_successful = true;
		break;
	}

	/*if( $delete_successful ) {
		print"<div class=operation-successful>Delete Successful</div>". NEWLINE;
	} else {
		print"<div class=operation-successful>Delete Unsuccessful</div>". NEWLINE;
	}*/

	html_print_operation_successful( "delete_page", $_POST['confirm_r_page'] );
}

# ------------------------------------
# $Log: delete_page.php,v $
# Revision 1.4  2006/08/05 22:07:59  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/05/03 20:00:10  gth2
# no message
#
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------

?>