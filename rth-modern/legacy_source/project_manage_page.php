<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Manage Project Page
#
# $RCSfile: project_manage_page.php,v $ $Revision: 1.6 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_manage_page	= 'project_manage_page.php';
$project_add_page     	= 'project_add_page.php';
$project_edit_page		= 'project_edit_page.php';
$user_manage_page		= 'user_manage_page.php';
$user_add_page			= 'user_add_page.php';
$project_manage_action	= 'project_manage_action.php';
$delete_page			= 'delete_page.php';

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];

session_set_properties("project_manage", $_GET);
$selected_project_properties 	= session_get_properties("project_manage");
$selected_project_id 			= $selected_project_properties['project_id'];

$project_manager		= user_has_rights( $selected_project_id, $user_id, MANAGER );

$redirect_url			= $page ."?project_id=". $selected_project_id;

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$row_style              = '';

if( isset($_GET['table']) ) {
	session_set_display_options($_GET['table'], $_POST);

}

$s_users_display_options	= session_get_display_options("project_manage_users");
$s_users_order_by			= $s_users_display_options['order_by'];
$s_users_order_dir			= $s_users_display_options['order_dir'];
$s_users_page_number		= $s_users_display_options['page_number'];

$s_areas_display_options	= session_get_display_options("project_manage_areas");
$s_areas_order_by			= $s_areas_display_options['order_by'];
$s_areas_order_dir			= $s_areas_display_options['order_dir'];
$s_areas_page_number		= $s_areas_display_options['page_number'];

$s_machines_display_options	= session_get_display_options("project_manage_machines");
$s_machines_order_by		= $s_machines_display_options['order_by'];
$s_machines_order_dir		= $s_machines_display_options['order_dir'];
$s_machines_page_number		= $s_machines_display_options['page_number'];

$s_testtype_display_options	= session_get_display_options("project_manage_testtype");
$s_testtype_order_by		= $s_testtype_display_options['order_by'];
$s_testtype_order_dir		= $s_testtype_display_options['order_dir'];
$s_testtype_page_number		= $s_testtype_display_options['page_number'];

$s_test_doc_type_display_options	= session_get_display_options("project_manage_test_doc_type");
$s_test_doc_type_order_by			= $s_test_doc_type_display_options['order_by'];
$s_test_doc_type_order_dir			= $s_test_doc_type_display_options['order_dir'];
$s_test_doc_type_page_number		= $s_test_doc_type_display_options['page_number'];

$s_environment_display_options	= session_get_display_options("project_manage_environment");
$s_environment_order_by			= $s_environment_display_options['order_by'];
$s_environment_order_dir		= $s_environment_display_options['order_dir'];
$s_environment_page_number		= $s_environment_display_options['page_number'];

$s_req_doc_type_display_options	= session_get_display_options("project_manage_req_doc_type");
$s_req_doc_type_order_by		= $s_req_doc_type_display_options['order_by'];
$s_req_doc_type_order_dir		= $s_req_doc_type_display_options['order_dir'];
$s_req_doc_type_page_number		= $s_req_doc_type_display_options['page_number'];

$s_req_area_covered_display_options	= session_get_display_options("project_manage_req_area_covered");
$s_req_area_covered_order_by		= $s_req_area_covered_display_options['order_by'];
$s_req_area_covered_order_dir		= $s_req_area_covered_display_options['order_dir'];
$s_req_area_covered_page_number		= $s_req_area_covered_display_options['page_number'];

html_window_title();
html_print_body();
html_page_title(project_get_name($selected_project_id) ." - ". lang_get('manage_project_page') );
html_page_header( $db, $project_name );
html_print_menu();
admin_menu_print( $page, $project_id, $user_id );

error_report_check( $_GET );

$project_details = project_get_details( $selected_project_id );

print"<div align=center>". NEWLINE;

html_project_manage_menu();

print"<br>". NEWLINE;

if( !empty( $project_details ) ) {

	$project_id				= $project_details[PROJ_ID];
	$project_name			= session_validate_form_get_field( 'project_name_required', $project_details[PROJ_NAME] );
	$project_description	= session_validate_form_get_field( 'project_description', $project_details[PROJ_DESCRIPTION] );
	$project_status			= session_validate_form_get_field( 'project_status', $project_details[PROJ_STATUS] );

	####################################################################################
	# Project Edit
	####################################################################################
	if( $project_manager ) {

		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class=width80>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;

		print"<form method=post action='project_edit_action.php'>". NEWLINE;
		print"<table class=inner>". NEWLINE;

		print"<input type=hidden name=project_id value=$project_id>";

		print"<tr>". NEWLINE;
		print"<td class=form-header-l colspan=2>".lang_get('edit_project')."</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# Project Name
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-r'>". lang_get('project_name') ."<span class='required'>*</span></td>". NEWLINE;
		print"<td class='form-data-l'>". NEWLINE;
		print"<input type=text size=65 maxlength=50 name='project_name_required' value='$project_name'>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# Description
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-r'>". lang_get('description') ."</td>". NEWLINE;
		print"<td class='form-data-l'>". NEWLINE;
		print"<textarea rows='4' cols='50' name='project_description' >$project_description</textarea>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# Status
		$status_list_box 			= array( 	"Y"	=> lang_get('project_enable'),
												"N" => lang_get('project_disable') );
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-r'>". lang_get('status') ."</td>". NEWLINE;
		print"<td class='form-data-l'>". NEWLINE;
		print"<select name='project_status' size=1>". NEWLINE;
		html_print_list_box_from_key_array(	$status_list_box,
											$project_status );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		print"<tr>". NEWLINE;
		print"<td class='form-lbl-r'>". lang_get('project_view_hide') ."</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;

		# Show/Hide Checkboxes
		print"<table style='margin-right:auto;margin-left:0px;'><tr>";

		$checkboxes_per_column = 4;
		$view_hide_lang_names = array(	lang_get("show_custom_1"),
										lang_get("show_custom_2"),
										lang_get("show_custom_3"),
										lang_get("show_custom_4"),
										lang_get("show_custom_5"),
										lang_get("show_custom_6"),
										lang_get("show_window"),
										lang_get("show_object"),
										lang_get("show_mem_stats"),
										lang_get("show_priority"),
										lang_get("show_test_input") );

		$view_hide_field_names = array(	PROJ_SHOW_CUSTOM_1,
										PROJ_SHOW_CUSTOM_2,
										PROJ_SHOW_CUSTOM_3,
										PROJ_SHOW_CUSTOM_4,
										PROJ_SHOW_CUSTOM_5,
										PROJ_SHOW_CUSTOM_6,
										PROJ_SHOW_WINDOW,
										PROJ_SHOW_OBJECT,
										PROJ_SHOW_MEM_STATS,
										PROJ_SHOW_PRIORITY,
										PROJ_SHOW_TEST_INPUT );

		$number_of_checkboxes 	= count($view_hide_field_names);
		$number_of_columns 		= $number_of_checkboxes/$checkboxes_per_column;

		for($i=0; $i<=$number_of_columns; $i++) {

			$start_index	= $i*$checkboxes_per_column;
			$end_index		= $start_index+$checkboxes_per_column-1;

			if( $end_index+1>$number_of_checkboxes ) {

				$end_index=$number_of_checkboxes-1;
			}

			print"<td valign=top>";

			for($j=$start_index; $j<=$end_index; $j++) {

				$field_name		= $view_hide_field_names[$j];
				$field_value	= session_validate_form_get_field( $field_name, $project_details[$field_name] );
				$lang_name		= $view_hide_lang_names[$j];
				$checked 		= "";

				if( $field_value == "Y" ) {
					$checked = "checked";
				}

				print"<input id=view_hide_$j type=checkbox name='$field_name' value=Y $checked>";
				print"<label for=view_hide_$j>$lang_name</label><br>". NEWLINE;
			}

			print"</td>";
		}

		print"</tr></table>";

		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=center colspan=2><input type=submit name='project_edit' value='".lang_get("save")."'><br><br></td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;

		print"<br>". NEWLINE;

		###########################################################################################
		# NEWS
		###########################################################################################
		print"<table class=hide100>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td><h3><a name='news'>".lang_get("news")."</a></h3></td>". NEWLINE;
		print"<td class=tbl-r><a href='#'>^ ".lang_get("top")."</a></td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;

		print"<div align=center>". NEWLINE;

		$row_subject 	= "";
		$row_body 		= "";

		$row_news 		= news_get($project_id);

		# if there is no news entered for this project, form will create news record
		#
		# else
		#
		# form will edit news record
		if( empty($row_news) ) {

			print"<form method=post action=news_add_action.php>". NEWLINE;
		} else {

			$row_subject 	= $row_news[0][NEWS_SUBJECT];
			$row_body 		= $row_news[0][NEWS_BODY];
			$news_id		= $row_news[0][NEWS_ID];

			print"<form method=post action=news_edit_action.php>". NEWLINE;
			print"<input type=hidden name=news_id value=$news_id>";
		}

		print"<input type=hidden name=project_id value=$project_id>";
		print"<input type=hidden name=poster value='".session_get_username()."'>";

		print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class=width80>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;

		# SUBJECT
		print"<tr>". NEWLINE;
		print"<td class=right>". lang_get('subject') ." <span class='required'></span></td>". NEWLINE;
		print"<td class=left>". NEWLINE;
		print"<input type='text' maxlength='100' name='subject' size=60 value='".
							session_validate_form_get_field('subject', $row_subject).
							"'>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# DESCRIPTION
		print"<tr>". NEWLINE;
		print"<td class=right>". lang_get('description') ." <span class='required'></span></td>". NEWLINE;
		print"<td class=left>". NEWLINE;
		print"<textarea name='body' rows=10 cols=60 >".
				session_validate_form_get_field('body', $row_body).
				"</textarea>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# SUBMIT BUTTON
		print"<tr>". NEWLINE;
		print"<td colspan='2' class=center><br><input type='submit' value='". lang_get('submit_btn') ."'></td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td colspan='2' class=center>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;

		/*
		maybe put a form in here to delete the news

		print"<br><br>". NEWLINE;

		print"<table class=hide80>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td align=left></td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		*/
		print"</div>". NEWLINE;
	}

	####################################################################################
	# User Section
	####################################################################################

	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td><h3><a name='users'>".lang_get("users")."</a></h3></td>". NEWLINE;
	print"<td class=tbl-r><a href='#'>^ ".lang_get("top")."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	# ----------------------------------------------------------------------------------
	# User Form
	# ----------------------------------------------------------------------------------
	$project_non_users	= project_get_non_users($selected_project_id);

	if( $project_manager && !empty($project_non_users) ) {

		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width80'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<form method=post action=project_add_users_action.php>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_user_to_project')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;

		print"<table class=hide90>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l width='33%'>".lang_get('users')."<span class='required'>*</span></td>". NEWLINE;
		print"<td class=form-header-l width='33%'>".lang_get('prefs')."</td>". NEWLINE;
		print"<td class=form-header-l width='33%'>".lang_get('user_rights')."</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# Non Users
		print"<tr>". NEWLINE;
		print"<td class=tbl-l>". NEWLINE;
		print"<select name='add_users[]' size=10 multiple>". NEWLINE;
		foreach(  $project_non_users as $row_non_user ) {

			$row_non_user_name	= $row_non_user[USER_UNAME];
			$row_non_user_id	= $row_non_user[USER_ID];
			$row_non_user_fname	= $row_non_user[USER_FNAME];
			$row_non_user_lname	= $row_non_user[USER_LNAME];

			print"<option value=$row_non_user_id>$row_non_user_lname, $row_non_user_fname ($row_non_user_name)</option>". NEWLINE;

		}
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# Preferences
		/*$add_user_delete_rights 	= session_validate_form_get_field( 'add_user_delete_rights', $project_name );
		$add_user_email_testset 	= session_validate_form_get_field( 'add_user_email_testset', $project_name );
		$add_user_email_discussion 	= session_validate_form_get_field( 'add_user_email_discussion', $project_name );
		$add_user_qa_tester 		= session_validate_form_get_field( 'add_user_qa_tester', $project_name );
		$add_user_ba_owner 			= session_validate_form_get_field( 'add_user_ba_owner', $project_name );
		*/


		print"<td class=tbl-l valign=top>". NEWLINE;

		# Delete Rights
		print"<input id=add_user_delete_rights type=checkbox name='add_user_delete_rights'>". NEWLINE;
		print"<label for=add_user_delete_rights>".lang_get('delete_rights')."</label><br>". NEWLINE;

		# Email Testset
		print"<input id=add_user_email_testset type=checkbox name='add_user_email_testset'>". NEWLINE;
		print"<label for=add_user_email_testset>".lang_get('email_testset')."</label><br>". NEWLINE;

		# Email Discussions
		print"<input id=add_user_email_discussion type=checkbox name='add_user_email_discussion'>". NEWLINE;
		print"<label for=add_user_email_discussion>".lang_get('email_discussions')."</label><br>". NEWLINE;

		# QA Tester
		print"<input id=add_user_qa_tester type=checkbox name='add_user_qa_tester'>". NEWLINE;
		print"<label for=add_user_qa_tester>".lang_get('qa_tester')."</label><br>". NEWLINE;

		# BA Owner
		print"<input id=add_user_ba_owner type=checkbox name='add_user_ba_tester'>". NEWLINE;
		print"<label for=add_user_ba_owner>".lang_get('ba_owner')."</label><br>". NEWLINE;
		print"</td>". NEWLINE;

		# User Rights
		print"<td class=tbl-l valign=top>". NEWLINE;
		print"<select name='add_users_rights' size=1>". NEWLINE;

		$user_rights_list_box_selected 	= session_validate_form_get_field( 'add_users_rights' );

		html_print_user_rights_list_box( $user_rights_list_box_selected );

		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		print"</tr>". NEWLINE;
		print"</table>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;

		print"<tr>". NEWLINE;
		print"<td class=center colspan=3><br><input type=submit name='add_users_submit' value='".lang_get("add")."'><br>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;

		print"<br>". NEWLINE;

	}

	# ----------------------------------------------------------------------------------
	# User Table
	# ----------------------------------------------------------------------------------
	print"<form action='$page?table=project_manage_users#users' method=post>";
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$rows_user_details = user_get_details_all($selected_project_id, $s_users_order_by, $s_users_order_dir, $s_users_page_number);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>";

	if( $rows_user_details ) {
		$action_page = "$page?table=project_manage_users#users";

		print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
		print"<thead>".NEWLINE;
		print"<tr>". NEWLINE;
		#html_tbl_print_header( lang_get('id'), 			USER_ID,					$s_users_order_by, $s_users_order_dir,	$action_page );
		#html_tbl_print_header( lang_get('username'), 	USER_UNAME,					$s_users_order_by, $s_users_order_dir,	$action_page );
		#html_tbl_print_header( lang_get('first'), 		USER_FNAME,					$s_users_order_by, $s_users_order_dir,	$action_page );
		#html_tbl_print_header( lang_get('last'),	 	USER_LNAME,					$s_users_order_by, $s_users_order_dir,	$action_page );
		#html_tbl_print_header( lang_get('email'), 		USER_EMAIL,					$s_users_order_by, $s_users_order_dir,	$action_page );
		#html_tbl_print_header( lang_get('phone') );
		#html_tbl_print_header( lang_get('user_rights'), PROJ_USER_PROJECT_RIGHTS,	$s_users_order_by, $s_users_order_dir,	$action_page );
		#html_tbl_print_header( lang_get('qa_owner'), PROJ_USER_QA_OWNER,			$s_users_order_by, $s_users_order_dir,	$action_page );
		#html_tbl_print_header( lang_get('ba_owner'), PROJ_USER_BA_OWNER,			$s_users_order_by, $s_users_order_dir,	$action_page );
		
		html_tbl_print_header( lang_get('id') );
		html_tbl_print_header( lang_get('username') );
		html_tbl_print_header( lang_get('first') );
		html_tbl_print_header( lang_get('last') );
		html_tbl_print_header( lang_get('email') );
		html_tbl_print_header_not_sortable( lang_get('phone') );
		html_tbl_print_header( lang_get('user_rights') );
		html_tbl_print_header( lang_get('qa_owner') );
		html_tbl_print_header( lang_get('ba_owner') );
		if( $project_manager ) {
			html_tbl_print_header_not_sortable( lang_get('edit') );
			html_tbl_print_header_not_sortable( lang_get('remove') );
		}

		print"\n</tr>". NEWLINE;
		print"</thead>".NEWLINE;
		print"<tbody>".NEWLINE;
		foreach($rows_user_details as $row_user_detail) {
			$tbl_user_id				= $row_user_detail[USER_ID];
			$tbl_user_first				= $row_user_detail[USER_FNAME];
			$tbl_user_last				= $row_user_detail[USER_LNAME];
			$tbl_username				= $row_user_detail[USER_UNAME];
			$tbl_user_email				= $row_user_detail[USER_EMAIL];
			$tbl_user_phone				= $row_user_detail[USER_PHONE];
			$tbl_user_delete_rights		= $row_user_detail[PROJ_USER_DELETE_RIGHTS];
			$tbl_user_email_testset		= $row_user_detail[PROJ_USER_EMAIL_TESTSET];
			$tbl_user_email_discussions	= $row_user_detail[PROJ_USER_EMAIL_REQ_DISCUSSION];
			$tbl_user_qa_owner			= $row_user_detail[PROJ_USER_QA_OWNER];
			$tbl_user_ba_owner			= $row_user_detail[PROJ_USER_BA_OWNER];
			$tbl_user_rights			= $row_user_detail[PROJ_USER_PROJECT_RIGHTS];

			$tbl_user_rights			= user_get_rights_string($tbl_user_rights);

			#$row_style = html_tbl_alternate_bgcolor($row_style);
			#print"<tr class='$row_style'>". NEWLINE;
			print"<tr>". NEWLINE;
			print"<td>". util_pad_id($tbl_user_id) ."</td>". NEWLINE;
			print"<td>$tbl_username</td>". NEWLINE;
			print"<td>$tbl_user_first</td>". NEWLINE;
			print"<td>$tbl_user_last</td>". NEWLINE;
			print"<td>$tbl_user_email</td>". NEWLINE;
			print"<td>$tbl_user_phone</td>". NEWLINE;
			print"<td>$tbl_user_rights</td>". NEWLINE;
			print"<td>$tbl_user_qa_owner</td>". NEWLINE;
			print"<td>$tbl_user_ba_owner</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_user_page.php?user_id=$tbl_user_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete' value='". lang_get( 'remove' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#users'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_user_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$tbl_user_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_USER_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}

			print"</tr>". NEWLINE;
		}
		print"</tbody>".NEWLINE;
		print"</table>". NEWLINE;
	} else {

		print lang_get('no_project_users');
	}

	print"<br>". NEWLINE;
/*
	####################################################################################
	# Areas Tested
	# ---------------------------------------------------------------------
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td><h3><a name=area_tested>".lang_get("area_tested")."</a></h3></td>". NEWLINE;
	print"<td class=tbl-r><a href='#'>^ ".lang_get("top")."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	# New Area Tested
	if( $project_manager ) {
		print"<form method=post action='project_add_area_action.php'>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_area_tested')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('area_tested') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=60 maxlength=50 name='area_tested_required' value='".session_validate_form_get_field( 'area_tested_required' )."'>". NEWLINE;
		print"&nbsp;<input type=submit name='new_area_tested' value='".lang_get("add")."'>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;

		print"<br>";

	}

	# ---------------------------------------------------------------------
	# Areas Tested Table
	# ---------------------------------------------------------------------
	print"<form method=post name='area_tested_table' action='$page?table=project_manage_areas#area_tested'>";
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$s_areas_tested = project_get_areas_tested($selected_project_id, $s_areas_order_by, $s_areas_order_dir, $s_areas_page_number);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $s_areas_tested ) {
		print"<input type=hidden name=table value=project_manage_areas>". NEWLINE;
		print"<table class='width70' rules='cols'>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('area_tested'), AREA_TESTED_NAME, $s_areas_order_by, $s_areas_order_dir, "$page?table=project_manage_areas#area_tested", $s_areas_page_number );
		if( $project_manager ) {
			html_tbl_print_header( lang_get('edit') );
			html_tbl_print_header( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;

		foreach($s_areas_tested as $areas) {

			$area_tested 	= $areas[AREA_TESTED_NAME];
			$area_tested_id	= $areas[AREA_TESTED_ID];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>$area_tested</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_area_tested_page.php?area_id=$area_tested_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='delete_area_tested' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_area_tested' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#area_tested'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_area_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$area_tested_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_AREA_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}

			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;

		print"<br>". NEWLINE;
	}

	####################################################################################
	# Machines
	# ----------------------------------------------------------------------------------
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td><h3><a name='machines'>".lang_get("machines")."</a></h3></td>". NEWLINE;
	print"<td class=tbl-r><a href='#'>^ ".lang_get("top")."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	# ----------------------------------------------------------------------------------
	# Machines Form
	# ----------------------------------------------------------------------------------
	if( $project_manager ) {
		print"<form method=post action='project_add_machine_action.php'>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_machine')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=hide90>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('machine_name') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=20 maxlength=20 name='machine_name_required' value='".session_validate_form_get_field( 'machine_name_required' )."'>". NEWLINE;
		print"</td>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('machine_location') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=25 maxlength=25 name='machine_location_required' value='".session_validate_form_get_field( 'machine_location_required' )."'>". NEWLINE;
		print"</td>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('machine_ip') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=15 maxlength=15 name='machine_ip_required' value='".session_validate_form_get_field( 'machine_ip_required' )."'>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=center><input type=submit name='new_area_tested' value='".lang_get("add")."'></td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
	}

	# ----------------------------------------------------------------------------------
	# Machines Table
	# ----------------------------------------------------------------------------------
	$action_page = "$page?table=project_manage_machines#machines";

	print"<br>". NEWLINE;
    print"<form method=post action='$action_page'>". NEWLINE;
	print"<input type=hidden name=table value=project_manage_machines>". NEWLINE;
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$machines = project_get_machines($selected_project_id, $s_machines_order_by, $s_machines_order_dir, $s_machines_page_number);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $machines ) {

		print"<table class='width70' rules='cols'>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('machine_name'), 		MACH_NAME, 		$s_machines_order_by, $s_machines_order_dir, $action_page, $s_machines_page_number );
		html_tbl_print_header( lang_get('machine_location'),	MACH_LOCATION, 	$s_machines_order_by, $s_machines_order_dir, $action_page, $s_machines_page_number );
		html_tbl_print_header( lang_get('machine_ip') );
		if( $project_manager ) {
			html_tbl_print_header( lang_get('edit') );
			html_tbl_print_header( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;

		foreach($machines as $machine) {
			$machine_id 		= $machine[MACH_ID];
			$machine_name 		= $machine[MACH_NAME];
			$machine_location 	= $machine[MACH_LOCATION];
			$machine_ip			= $machine[MACH_IP_ADDRESS];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>$machine_name</td>". NEWLINE;
			print"<td>$machine_location</td>". NEWLINE;
			print"<td>$machine_ip</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_machine_page.php?machine_id=$machine_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='delete_release' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_machine' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#machines'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_machine_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$machine_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_MACHINE_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
		#print"</form>". NEWLINE;

		print"<br>". NEWLINE;
	}

	####################################################################################
	# Test Type
	# ----------------------------------------------------------------------------------
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td><h3><a name='testtype'>".lang_get("testtype")."</a></h3></td>". NEWLINE;
	print"<td class=tbl-r><a href='#'>^ ".lang_get("top")."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	# ----------------------------------------------------------------------------------
	# Test Type Form
	# ----------------------------------------------------------------------------------
	if( $project_manager ) {
		print"<form method=post action='project_add_testtype_action.php'>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_testtype')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('testtype') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=60 maxlength=50 name='testtype_required' value='".session_validate_form_get_field( 'testtype_required' )."'>". NEWLINE;
		print"&nbsp;<input type=submit name='new_area_tested' value='".lang_get("add")."'>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
	}

	# ----------------------------------------------------------------------------------
	# Test Type Table
	# ----------------------------------------------------------------------------------
	print"<br>". NEWLINE;
    print"<form method=post action='$page?table=project_manage_testtype#testtype'>". NEWLINE;
	print"<input type=hidden name=table value=project_manage_testtype>". NEWLINE;
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$testtypes = project_get_test_types($selected_project_id, $s_testtype_order_by, $s_testtype_order_dir, $s_testtype_page_number);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $testtypes ) {
		print"<table class='width70' rules='cols'>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('testtype'), TEST_TYPE_TYPE, $s_testtype_order_by, $s_testtype_order_dir, "$page?table=project_manage_testtype#testtype", $s_testtype_page_number );
		if( $project_manager ) {
			html_tbl_print_header( lang_get('edit') );
			html_tbl_print_header( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;

		foreach($testtypes as $testtype) {
			$s_testtype_id 		= $testtype[TEST_TYPE_ID];
			$s_testtype_name 		= $testtype[TEST_TYPE_TYPE];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>$s_testtype_name</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_testtype_page.php?test_type_id=$s_testtype_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='delete_release' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_testtype' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#testtype'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_testtype_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$s_testtype_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_TESTTYPE_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
		#print"</form>";

		print"<br>". NEWLINE;
	}

	####################################################################################
	# Test Doc Type
	# ----------------------------------------------------------------------------------
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td><h3><a name='test_doc_type'>".lang_get("test_doc_type")."</a></h3></td>". NEWLINE;
	print"<td class=tbl-r><a href='#'>^ ".lang_get("top")."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	# ----------------------------------------------------------------------------------
	# Test Doc Type Form
	# ----------------------------------------------------------------------------------
	if( $project_manager ) {
		print"<form method=post action='project_add_testdoctype_action.php'>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_test_doc_type')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('test_doc_type') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=60 maxlength=50 name='test_doc_type_required' value='".session_validate_form_get_field( 'test_doc_type_required' )."'>". NEWLINE;
		print"&nbsp;<input type=submit name='new_area_tested' value='".lang_get("add")."'>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
	}

	# ----------------------------------------------------------------------------------
	# Test Doc Type Table
	# ----------------------------------------------------------------------------------
	print"<br>". NEWLINE;
    print"<form method=post action='$page?table=project_manage_test_doc_type#test_doc_type'>". NEWLINE;
	print"<input type=hidden name=table value=project_manage_test_doc_type>". NEWLINE;
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$s_test_doc_types = project_get_test_doc_types($selected_project_id, $s_test_doc_type_order_by, $s_test_doc_type_order_dir, $s_test_doc_type_page_number);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $s_test_doc_types ) {
		print"<table class='width70' rules=cols>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('test_doc_type'), MAN_DOC_TYPE_NAME, $s_test_doc_type_order_by, $s_test_doc_type_order_dir, "$page?table=project_manage_test_doc_type#test_doc_type", $s_test_doc_type_page_number );
		if( $project_manager ) {
			html_tbl_print_header( lang_get('edit') );
			html_tbl_print_header( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;

		foreach($s_test_doc_types as $s_test_doc_type) {
			$s_test_doc_type_id		= $s_test_doc_type[MAN_DOC_TYPE_ID];
			$s_test_doc_type_name 	= $s_test_doc_type[MAN_DOC_TYPE_NAME];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>$s_test_doc_type_name</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_testdoctype_page.php?test_doc_type_id=$s_test_doc_type_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='delete_release' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='remove_man_doc_type_from_project' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#test_doc_type'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_man_doc_type_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$s_test_doc_type_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_TEST_DOC_TYPE_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}

			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
	}

	####################################################################################
	# Environment
	# ----------------------------------------------------------------------------------
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td><h3><a name='environment'>".lang_get("environment")."</a></h3></td>". NEWLINE;
	print"<td class=tbl-r><a href='#'>^ ".lang_get("top")."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	# ----------------------------------------------------------------------------------
	# Environment Form
	# ----------------------------------------------------------------------------------
	if( $project_manager ) {
		print"<form method=post action='project_add_environment_action.php'>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_environment')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('environment') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=60 maxlength=50 name='environment_name_required' value='".session_validate_form_get_field( 'environment_required' )."'>". NEWLINE;
		print"&nbsp;<input type=submit name='new_area_tested' value='".lang_get("add")."'>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
	}

	# ----------------------------------------------------------------------------------
	# Environment Table
	# ----------------------------------------------------------------------------------
	print"<br>". NEWLINE;
    print"<form method=post action='$page?table=project_manage_environment#environment'>". NEWLINE;
	print"<input type=hidden name=table value=project_manage_environment>". NEWLINE;
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$environments = project_get_environments($selected_project_id, $s_environment_order_by, $s_environment_order_dir, $s_environment_page_number);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $environments ) {
		print"<table class='width70' rules=cols>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('environment'), MAN_DOC_TYPE_NAME, $s_environment_order_by, $s_environment_order_dir, "$page?table=project_manage_environment#environment", $s_environment_page_number );
		if( $project_manager ) {
			html_tbl_print_header( lang_get('edit') );
			html_tbl_print_header( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;

		foreach($environments as $environment) {
			$s_environment_id		= $environment[ENVIRONMENT_ID];
			$s_environment_name 	= $environment[ENVIRONMENT_NAME];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>$s_environment_name</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_environment_page.php?environment_id=$s_environment_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='delete_release' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_environment' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#environment'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_environment_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$s_environment_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_ENVIRONMENT_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
	}

	####################################################################################
	# Required Document Type
	# ----------------------------------------------------------------------------------
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td><h3><a name='req_doc_type'>".lang_get("req_doc_type")."</a></h3></td>". NEWLINE;
	print"<td class=tbl-r><a href='#'>^ ".lang_get("top")."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	# ----------------------------------------------------------------------------------
	# Required Document Type Form
	# ----------------------------------------------------------------------------------
	if( $project_manager ) {
		print"<form method=post action='project_add_reqdoctype_action.php'>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_req_doc_type')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('req_doc_type') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=60 maxlength=50 name='req_doc_type_required' value='".session_validate_form_get_field( 'req_doc_type_required' )."'>". NEWLINE;
		print"&nbsp;<input type=submit name='new_area_tested' value='".lang_get("add")."'>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
	}

	# ----------------------------------------------------------------------------------
	# Required Document Type Table
	# ----------------------------------------------------------------------------------
	print"<br>". NEWLINE;
    print"<form method=post action='$page?table=project_manage_req_doc_type#req_doc_type'>". NEWLINE;
	print"<input type=hidden name=table value=project_manage_req_doc_type>". NEWLINE;
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$req_doc_types = project_get_req_doc_types($selected_project_id, $s_req_doc_type_order_by, $s_req_doc_type_order_dir, $s_req_doc_type_page_number);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $req_doc_types ) {
		print"<table class='width70' rules=cols>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('req_doc_type'), REQ_DOC_TYPE_NAME, $s_req_doc_type_order_by, $s_req_doc_type_order_dir, "$page?table=project_manage_req_doc_type#req_doc_type", $s_req_doc_type_page_number );
		if( $project_manager ) {
			html_tbl_print_header( lang_get('edit') );
			html_tbl_print_header( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;

		foreach($req_doc_types as $req_doc_type) {
			$s_req_doc_type_id	= $req_doc_type[REQ_DOC_TYPE_ID];
			$s_req_doc_type_name 	= $req_doc_type[REQ_DOC_TYPE_NAME];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>$s_req_doc_type_name</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_reqdoctype_page.php?req_doc_type_id=$s_req_doc_type_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='delete_release' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_req_doc_type' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#req_doc_type'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_req_doc_type_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$s_req_doc_type_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_REQ_DOC_TYPE_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
	}

	####################################################################################
	# Required Area Covered
	# ----------------------------------------------------------------------------------
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td><h3><a name='req_area_covered'>".lang_get("req_area_covered")."</a></h3></td>". NEWLINE;
	print"<td class=tbl-r><a href='#'>^ ".lang_get("top")."</a></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

	# ----------------------------------------------------------------------------------
	# Required Area Covered Form
	# ----------------------------------------------------------------------------------
	if( $project_manager ) {
		print"<form method=post action='project_add_reqareacovered_action.php'>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_req_area_covered')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('req_area_covered') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=60 maxlength=50 name='req_area_covered_required' value='".session_validate_form_get_field( 'req_area_covered_required' )."'>". NEWLINE;
		print"&nbsp;<input type=submit name='new_area_tested' value='".lang_get("add")."'>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
	}

	# ----------------------------------------------------------------------------------
	# Required Area Covered	 Table
	# ----------------------------------------------------------------------------------
	print"<br>". NEWLINE;
    print"<form method=post action='$page?table=project_manage_req_area_covered#req_area_covered'>". NEWLINE;
	print"<input type=hidden name=table value=project_manage_req_area_covered>". NEWLINE;
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$req_areas_covered = project_get_req_areas_covered($selected_project_id, $s_req_area_covered_order_by, $s_req_area_covered_order_dir, $s_req_area_covered_page_number);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $req_areas_covered ) {
		print"<table class='width70' rules=cols>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('req_area_covered'), REQ_AREA_COVERAGE, $s_req_area_covered_order_by, $s_req_area_covered_order_dir, "$page?table=project_manage_req_area_covered#req_area_covered", $s_req_area_covered_page_number );
		if( $project_manager ) {
			html_tbl_print_header( lang_get('edit') );
			html_tbl_print_header( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;

		foreach($req_areas_covered as $req_area_covered) {
			$s_req_area_covered_id		= $req_area_covered[REQ_AREA_COVERAGE_ID];
			$s_req_area_covered_name 	= $req_area_covered[REQ_AREA_COVERAGE];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>$s_req_area_covered_name</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_reqareacovered_page.php?req_area_covered_id=$s_req_area_covered_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='remove_req_area_covered' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_req_area_covered' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#req_area_covered'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_req_area_covered_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$s_req_area_covered_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_REQ_AREA_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
	}
*/
} else {
	html_no_records_found_message( lang_get('no_projects') );
}

print"</div>". NEWLINE;

html_print_footer();

# ------------------------------------
# $Log: project_manage_page.php,v $
# Revision 1.6  2008/01/22 07:57:44  cryobean
# made the user table sortable
#
# Revision 1.5  2006/12/05 04:57:21  gth2
# Allow users to rename project - gth
#
# Revision 1.4  2006/08/05 22:08:24  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.3  2006/07/01 02:59:38  gth2
# correcting php notice message - gth
#
# Revision 1.2  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------

?>