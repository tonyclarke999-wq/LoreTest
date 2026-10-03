<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Detail Page
#
# $RCSfile: bug_detail_page.php,v $  $Revision: 1.5 $
# ------------------------------------

$page							= basename(__FILE__);
$update_action_page				= 'bug_detail_update_page.php';
$bug_update_action				= 'bug_update_action.php'; # used for buttons at bottom of form
$delete_page					= 'delete_page.php';
$new_upload_action_page			= "test_detail_new_upload_action.php";
$row_test_step_add_action_page  = "test_step_add_action.php";
$row_test_step_renumber_page    = 'test_step_renumber_action.php';
$row_test_step_edit_page		= 'test_step_edit_page.php';
$delete_page					= 'delete_page.php';
$active_version_page			= 'test_version_make_active_action.php';
$bug_page						= 'bug_page.php';
$test_run_page					= "results_view_verifications_page.php";

if( isset($_POST['delete']) ) {

	require_once($delete_page);
	exit;
}

if( isset($_POST['new_relationship']) ) {

	require_once($bug_update_action);
	exit;
}

include"./api/include_api.php";
auth_authenticate_user();

$num                    = 0;
$bg_color               = '';
$row_style				= '';

$s_user_properties		= session_get_user_properties();
$s_project_properties   = session_get_project_properties();
$s_show_options 		= session_get_show_options();

$s_bug_details			= session_set_properties("bug", $_GET);
$bug_id					= $s_bug_details['bug_id'];
$padded_bug_id			= util_pad_id( $bug_id );

$s_user_id				= $s_user_properties['user_id'];
$s_username				= $s_user_properties['username'];
$s_tempest_admin		= $s_user_properties['tempest_admin'];
$s_project_rights		= $s_user_properties['project_rights'];
$s_delete_rights		= $s_user_properties['delete_rights'];
$s_email				= $s_user_properties['email'];

$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

$show_priority 			= $s_show_options['show_priority'];
$redirect_url			= $page . "?bug_id=". $bug_id;


if( isset($_GET['failed']) ) {
	$is_validation_failure = $_GET['failed'];
} else {
	$is_validation_failure = false;
}

$user_tempest_admin			= user_has_rights( $project_id, $s_user_id, ADMIN );
$user_project_manager		= user_has_rights( $project_id, $s_user_id, MANAGER );

$user_has_delete_rights		= ($s_delete_rights==="Y" || $user_project_manager);

# link users back to top of page
# you can't put a link at the very start of a document, content starts after the <body>,
# besides if you just link to "#", the browser will jump to the top of the doc - md
//print"<a name='top' id='top'>". NEWLINE;

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('bug_detail_page') );
html_page_header( $db, $project_name );
html_print_menu();
bug_menu_print( $page );

//PRINT_R( bug_email_collect_recipients( $bug_id, $notify_type='New' ) );

error_report_check( $_GET );

$row = bug_get_detail( $bug_id );

$category    			= $row[CATEGORY_NAME];
$bug_project_id			= $row[BUG_PROJECT_ID];
$component				= $row[COMPONENT_NAME];
$priority        		= $row[BUG_PRIORITY];
$severity        		= $row[BUG_SEVERITY];
$bug_status				= $row[BUG_STATUS];
$reporter	     		= $row[BUG_REPORTER];
$reported_date   		= $row[BUG_REPORTED_DATE];
$assigned_to      		= $row[BUG_ASSIGNED_TO];
$assigned_to_developer	= $row[BUG_ASSIGNED_TO_DEVELOPER];
$closed		       		= $row[BUG_CLOSED];
$closed_date	 		= $row[BUG_CLOSED_DATE];
$test_verify_id       	= $row[BUG_TEST_VERIFY_ID];
$req_version_id    		= $row[BUG_REQ_VERSION_ID];
$found_in_release  		= $row[BUG_FOUND_IN_RELEASE];
$assign_to_release 		= $row[BUG_ASSIGN_TO_RELEASE];
$imp_in_release 		= $row[BUG_IMPLEMENTED_IN_RELEASE];
$discovery_period 		= $row[BUG_DISCOVERY_PERIOD];
$summary		 		= $row[BUG_SUMMARY];
$description			= $row[BUG_DESCRIPTION];

# Enter logic to warn the user if they try to jump to a bug that doesn't exist
if( empty($row) ) {

	print"<div align=center>";
	error_report_display_msg( INVALID_BUG_ID );
	print"</div>";
	exit;
}

# Get the users permissions for the project of the bug they're trying to view
$user_project_user	= user_has_rights( $bug_project_id, $s_user_id, USER );
$bug_project		= project_get_name( $bug_project_id );

# Warn the user if they don't have access to the project
if( !$user_project_user ) {

	print"<div align=center>";
	error_report_display_msg( NO_RIGHTS_TO_PROJECT );
	print"</div>";
	exit;

}

print"<div align=center>";

if ( !empty($row) ) {

	print"<br>". NEWLINE;

	print"<table class=width95>". NEWLINE;

	# FORM TITLE
	print"<tr>". NEWLINE;
	print"<td class='white-grid-header-l' colspan='4'><b>". lang_get('bug_detail') ."</b>";
	print"&nbsp;<a href='#notes'>[". lang_get('jump_to_bugnotes') ."]</a>";
	print"&nbsp;<a href='#history'>[". lang_get('jump_to_history') ."]</a>";
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;

	# BUG_ID AND PROJECT NAME
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('bug_id') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$padded_bug_id</td>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('project_name') ."&nbsp;</td>". NEWLINE;
		print"<td class=grid-data-l>$bug_project</td>". NEWLINE;
	print"</tr>". NEWLINE;
	# BUG_STATUS AND PRIORITY
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r>". lang_get('bug_status') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$bug_status</td>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('bug_priority') ."&nbsp;</td>". NEWLINE;
		print"<td class=grid-data-l>$priority</td>". NEWLINE;
	print"</tr>". NEWLINE;
	# REPORTED_BY AND SEVERITY
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('reported_by') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$reporter</td>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('bug_severity') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$severity</td>". NEWLINE;
	print"</tr>". NEWLINE;
	# REPORTED_DATE AND BUG_CATEGORY
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('reported_date') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$reported_date</td>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('bug_category') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$category</td>". NEWLINE;
	print"</tr>". NEWLINE;
	# ASSIGNED_TO AND COMPONENT
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('assigned_to') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$assigned_to</td>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('bug_component') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$component</td>". NEWLINE;
	print"</tr>". NEWLINE;
	# ASSIGN_TO_DEVELOPER AND FOUND_IN_RELEASE
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('assigned_to_developer') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$assigned_to_developer</td>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('found_in_release') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$found_in_release</td>". NEWLINE;
	print"</tr>". NEWLINE;
	# DISCOVERY_PERIOD AND ASSIGN_TO_RELEASE
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('discovery_period') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$discovery_period</td>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('assign_to_release') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$assign_to_release</td>". NEWLINE;
	print"</tr>". NEWLINE;
	# TEST_VERIFICATION_ID AND IMPLEMENTED_IN_RELEASE
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r nowrap>". lang_get('test_verification_id') ."</td>". NEWLINE;
		if( !empty( $test_verify_id ) ) {
			$padded_verify_id = util_pad_id( $test_verify_id );
			print"<td class=grid-data-l><a href='$test_run_page?bug_page=true&verify_id=$test_verify_id'>$padded_verify_id</a></td>". NEWLINE;
		}
		else {
			print"<td class=grid-data-l></td>". NEWLINE;
		}
		print"<td class=grid-header-r nowrap>". lang_get('implemented_in_release') ."</td>". NEWLINE;
		print"<td class=grid-data-l>$imp_in_release</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r>". lang_get('bug_summary') ."</td>". NEWLINE;
		print"<td class=grid-data-l colspan=3>$summary</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"<tr>". NEWLINE;
		print"<td class=grid-header-r><br>". lang_get('bug_description') ."<br><br></td>". NEWLINE;
		print"<td class=grid-data-l colspan=3 valign=top><br>$description<br><br></td>". NEWLINE;
	print"</tr>". NEWLINE;

	print"</table>". NEWLINE;

	# UPDATE BUTTONS
	print"<table class=hide95 rules=none border=0>". NEWLINE;
	print"<tr>". NEWLINE;

		# UPDATE TEST
		print"<td class=center width='20%' valign='top'>". NEWLINE;
			print"<form method=post action='$update_action_page'>". NEWLINE;
			print"<input type='submit' value='". lang_get('update_bug') ."'>". NEWLINE;
			print"</form>". NEWLINE;
		print"</td>". NEWLINE;

		# ASSIGN TO
		print"<td class=center width='20%' valign='top'>". NEWLINE;
		print"<form method=post action='$bug_update_action'>". NEWLINE;
			print"<input type='submit' value='". lang_get('assign_to_user') ."'>". NEWLINE;
			print"<select name='update_assign_to' size='1'>";
			$users = user_get_usernames_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $users, $assigned_to);
			print"</select>";
		print"<input type='hidden' name='action' value='update_assign_to'>". NEWLINE;
		print"</form>". NEWLINE;
		print"</td>". NEWLINE;

		# ASSIGN TO DEVELOPER
		print"<td class=center width='20%' valign='top'>". NEWLINE;
		print"<form method=post action='$bug_update_action'>". NEWLINE;
			print"<input type='submit' value='". lang_get('assign_to_developer') ."'>". NEWLINE;
			print"<select name='assign_to_developer' size='1'>";
			$users = user_get_usernames_by_project($project_id, $blank=true);
			html_print_list_box_from_array( $users, $assigned_to_developer);
			print"</select>";
		print"<input type='hidden' name='action' value='update_assign_to_developer'>". NEWLINE;
		print"</form>". NEWLINE;
		print"</td>". NEWLINE;

		# CHANGE STATUS
		print"<td class=center width='20%' valign='top'>". NEWLINE;
		print"<form method=post action='$bug_update_action'>". NEWLINE;
			print"<input type='submit' value='". lang_get('change_status') ."'>". NEWLINE;
			print"<select name='update_status' size='1'>";
			$statuses = bug_get_status();
			html_print_list_box_from_array( $statuses, $bug_status);
			print"</select>";
		print"<input type='hidden' name='action' value='update_status'>". NEWLINE;
		print"</form>". NEWLINE;
		print"</td>". NEWLINE;

		# DELETE
		# disable delete button if user does not have the rights
		if( $user_has_delete_rights ) {
			$delete_disabled = "";
		} else {
			$delete_disabled = "disabled";
		}

		print"<td class=center width='20%' valign='top'>". NEWLINE;
		print"<form method=post action='$delete_page'>". NEWLINE;
		print"<input type='submit' value='". lang_get('delete_bug') ."' $delete_disabled>". NEWLINE;
		print"<input type='hidden' name='r_page' value='$bug_page'>";
		print"<input type='hidden' name='f' value='delete_bug'>";
		print"<input type='hidden' name='id' value='$bug_id'>";
		print"<input type='hidden' name='msg' value='250'>";
		print"</form>". NEWLINE;
		print"</td>". NEWLINE;

	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	print"<br>". NEWLINE;

	# RELATIONSHIPS FORM
	print"<form method=post action='$page'>". NEWLINE;
	print"<input type='hidden' name='bug_id' value='$bug_id'>". NEWLINE;
	print"<input type='hidden' name='action' value='add_relationship'>". NEWLINE;

	print"<table class='width95'>". NEWLINE; #  border=1 rules=all

	# FORM TITLE
	print"<tr>". NEWLINE;
	print"<td class='white-grid-header-l' colspan='7'><b>". lang_get('add_relationship') ."</b></td>". NEWLINE;
	print"</tr>". NEWLINE;

	# CREATE RELATION
	print"<tr>". NEWLINE;
	print"<td class=grid-header-l nowrap colspan='2'>". lang_get('new_relationship') ."</td>". NEWLINE;
	print"<td class='tbl-r' colspan='2' nowrap>". lang_get('current_bug_is') ."</td>". NEWLINE;
	print"<td class='tbl-l'>";
		print"<select name='relationship_type' size=1>". NEWLINE;
		$relationship_types = bug_get_relationship_types();
		html_print_list_box_from_key_array( $relationship_types );
		print"</select>". NEWLINE;
	print"</td>". NEWLINE;
	print"<td class='tbl-l' colspan='2'><input type='text' name='related_bug_id_required' size='10' maxlength='7'>";
	print"&nbsp;&nbsp;";
	print"<input type='submit' name=new_relationship value='". lang_get('add') ."'></td>". NEWLINE;
	print"</tr>". NEWLINE;

	# DISPLAY EXISTING RELATIONSHIPS
	bug_display_related_items( $bug_id, $user_tempest_admin, $user_project_manager );

	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	print"<br><br>";

	# FILE UPLOAD
	print"<form enctype='multipart/form-data' name='upload_defect_file' action='bug_upload_file_action.php' method=post>". NEWLINE;
	print"<table class=width95 rules=all border=3>". NEWLINE;

		# FORM TITLE
		print"<tr>". NEWLINE;
		print"<td class='white-grid-header-l' nowrap colspan='3'><b>". lang_get('file_upload') ."</b></td>". NEWLINE;
		print"</tr>". NEWLINE;
		
		# FILE UPLOAD
		print"<tr>". NEWLINE;
		print"<td class=grid-header-r >" . lang_get('file_name') . "</td>". NEWLINE;
		print"<td class=form-data-l><input type='file' name='uploadfile_required' size='60'>&nbsp;&nbsp;". NEWLINE;
		print"<input type='submit' align='left' name=new_file value='". lang_get('add') ."'></td>". NEWLINE;
		print"</tr>". NEWLINE;

		# BUG_ID
		print"<input type='hidden' name='bug_id' value='$bug_id'>". NEWLINE;

	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	$upload_rows = bug_get_uploaded_documents($bug_id);

	if( !empty($upload_rows) ) {

		print"<br>";
		print"<table class=width95>";
		print"<tr>";
		print"<td>";
		print"<table class=inner>";
		print"<tr>";
			print"<td class=grid-header-c>". lang_get('file_type') ."</td>";
			print"<td class=grid-header-c>". lang_get('file_name') ."</td>";
			print"<td class=grid-header-c>". lang_get('view') ."</td>";
			print"<td class=grid-header-c>". lang_get('download') ."</td>";
			print"<td class=grid-header-c>". lang_get('uploaded_by') ."</td>";
			print"<td class=grid-header-c>". lang_get('date_added') ."</td>";
		print"</tr>";

		foreach($upload_rows as $upload_row) {

			$filename		= $upload_row[BUG_FILE_NAME];
			$display_name	= $upload_row[BUG_FILE_DISPLAY_NAME];
			$file_id		= $upload_row[BUG_FILE_ID];
			$uploaded_by	= $upload_row[BUG_FILE_UPLOAD_BY];
			$uploaded_date	= $upload_row[BUG_FILE_UPLOAD_DATE];
			$fname			= $s_project_properties['defect_upload_path'] . $filename;

			print"<tr>";
				print"<td class=grid-data-c>".html_file_type( $filename )."</td>";
				print"<td class=grid-data-c>$display_name</td>";
				print"<td class=grid-data-c>";
				print"<a href='$fname' target='new'>" . lang_get('view') . "</a>";
				print"</td>";
				print"<td class=grid-data-c>";
				print"<a href='download.php?upload_filename=$fname'>" . lang_get('download') . "</a>";
				print"</td>";
				print"<td class=grid-data-c>$uploaded_by</td>";
				print"<td class=grid-data-c>$uploaded_date</td>";
			print"</tr>";
		}
	

		print"</table>";
		print"</td>";
		print"</tr>";
		print"</table>";
	}


	print"<br><br>";
	

	# ADD BUGNOTE TABLE
	print"<form method=post action='$bug_update_action'>". NEWLINE;
	print"<input type='hidden' name='action' value='add_bugnote'>". NEWLINE;

	print"<table class=width95>". NEWLINE;

		# FORM TITLE
		print"<tr>". NEWLINE;
		print"<td class='white-grid-header-l' colspan='2'><b>". lang_get('add_bugnote') ."</b></td>". NEWLINE;
		print"</tr>". NEWLINE;

		# BUGNOTE
		print"<tr>". NEWLINE;
		print"<td class=grid-header-r nowrap width='25%'>". lang_get('bug_note') ."<span class='required'>*</span></td>". NEWLINE;
		print"<td class=form-data-l width='75%'>". NEWLINE;
		print"<textarea rows='8' cols='80' name='bugnote_required'></textarea>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# BUTTON
		print"<tr>". NEWLINE;
		print"<td colspan='2'><input type=submit name='add_bugnote' value='". lang_get("add_bugnote") ."'></td>". NEWLINE;
		print"</tr>". NEWLINE;

	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	print"<br><br>";

	# DISPLAY BUGNOTES
	$bugnotes = bug_get_notes( $bug_id );

	if( $bugnotes ) {

		print"<a name='notes' id='notes'></a>". NEWLINE;
		print"<table class=width95>". NEWLINE;

		# TITLE
		print"<tr>". NEWLINE;
		print"<td class='white-grid-header-l' colspan='2'><b>". lang_get('bug_notes') ."</b>";
		print"&nbsp;<a href='#'>[". lang_get('back_to_top') ."]</a>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		foreach( $bugnotes as $note ) {

			$bugnote_id		= $note[BUG_NOTE_ID];
			$author			= $note[BUG_NOTE_AUTHOR];
			$date_created	= $note[BUG_NOTE_DATE_CREATED];
			$bugnote		= $note[BUG_NOTE_DETAIL];

			print"<tr>". NEWLINE;
			print"<td class='bg-color'>";
			print"<b>$author</b>";
			print"<br>". NEWLINE;
			print"$date_created";
			print"<br><br>". NEWLINE;
			print"<a href='bug_edit_bugnote_page.php?bugnote_id=$bugnote_id'>[". lang_get('edit_link') ."]</a>";

			print"<form method=post action=''>". NEWLINE;
			print"<input type='submit' name='delete' value='[". lang_get( 'delete' ) ."]' class='page-numbers'>";
			print"<input type='hidden' name='r_page' value='bug_detail_page.php?bug_id=$bug_id'>";
			print"<input type='hidden' name='f' value='delete_bugnote'>";
			print"<input type='hidden' name='id' value='$bugnote_id'>";
			print"<input type='hidden' name='msg' value='260'>";
			print"</form>". NEWLINE;

			//print"<a href='delete_page.php'>[". lang_get('delete_link') ."]</a>";
			print"</td>". NEWLINE;

			print"<td class=grid-data-l>".util_html_encode_string($bugnote)."</td>". NEWLINE;
			print"</tr>". NEWLINE;

		}

		print"</table>". NEWLINE;
		print"<br>". NEWLINE;
	}

	# BUG HISTORY SECTION
	$history_details = bug_get_history( $bug_id );

	if( $history_details ) {

		print"<a name='history' id='history'></a>". NEWLINE;
		print"<table class=width95 rules=cols>". NEWLINE;

		# FORM TITLE
		print"<tr>". NEWLINE;
		print"<td class='white-grid-header-l' colspan='4'><b>". lang_get('bug_history') ."</b>";
		print"&nbsp;<a href='#'>[". lang_get('back_to_top') ."]</a>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# COLUMN HEADERS
		print"<tr class=tbl_header>". NEWLINE;
		html_tbl_print_header( lang_get('date_modified') );
		html_tbl_print_header( lang_get('username') );
		html_tbl_print_header( lang_get('field') );
		html_tbl_print_header( lang_get('change') );
		print"</tr>". NEWLINE;

		$row_style = '';

		foreach( $history_details as $history_detail ) {

			$history_date		= $history_detail[BUG_HISTORY_DATE];
			$history_user		= $history_detail[BUG_HISTORY_USER];
			$history_field		= $history_detail[BUG_HISTORY_FIELD];
			$history_old_val	= $history_detail[BUG_HISTORY_OLD_VALUE];
			$history_new_val	= $history_detail[BUG_HISTORY_NEW_VALUE];
			$change				= '';

			if( $history_field != lang_get('new_bug') ) {

				if( !empty($history_old_val) || !empty($history_new_val) ) {
					$change = $history_old_val ." => ". $history_new_val;
				}
			}

			$row_style = html_tbl_alternate_bgcolor($row_style);
			print"<tr class='$row_style'>". NEWLINE;
			print"<td class='tbl-c'>$history_date</td>". NEWLINE;
			print"<td class='tbl-c'>$history_user</td>". NEWLINE;
			print"<td class='tbl-c'>$history_field</td>". NEWLINE;
			print"<td class='tbl-c'>$change</td>". NEWLINE;
			print"</tr>". NEWLINE;

		}

		print"</table>". NEWLINE;
		print"<br>". NEWLINE;

	}




	print"</div>". NEWLINE;

} else {
    print"<br><span class='print'>" . lang_get('no_bug_detail') . $padded_bug_id . "</span>";
}


html_print_footer();

# ------------------------------------
# $Log: bug_detail_page.php,v $
# Revision 1.5  2006/10/05 02:42:19  gth2
# adding file upload to the bug page - gth
#
# Revision 1.4  2006/08/05 22:07:58  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/02/27 17:24:55  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.2  2006/02/24 11:38:20  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------

?>