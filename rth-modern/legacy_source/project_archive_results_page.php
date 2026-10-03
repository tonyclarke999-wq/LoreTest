<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Archive Results Page
#
# $RCSfile: project_archive_results_page.php,v $  $Revision: 1.3 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page           		= basename(__FILE__);
$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];
$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$row_style              = '';

session_set_properties("project_manage", $_GET);
$selected_project_properties 	= session_get_properties("project_manage");
$selected_project_id 			= $selected_project_properties['project_id'];

html_window_title();
html_print_body();
html_page_title(project_get_name($selected_project_id) ." - ". lang_get('archive_results_page') );
html_page_header( $db, $project_name );
html_print_menu();
admin_menu_print( $page, $project_id, $user_id );

html_project_manage_menu();
html_project_manage_tests_menu();

if( !user_has_rights( $selected_project_id, $user_id, MANAGER ) ) {
	print"<div align=center>";
	error_report_display_msg( NO_RIGHTS_TO_VIEW_PAGE );
	print"</div>";
	exit;
}

error_report_check( $_GET );

print"<div align=center>". NEWLINE;

print"<br>". NEWLINE;

########################################################################################
#

$release_array = admin_get_release_array($selected_project_id);

if( empty($release_array) ) {

	print lang_get('no_archive_results');
} else {
print"<form action='project_archive_results_action.php' method=post>". NEWLINE;
print"<input type=hidden name=project_id value=$selected_project_id>";
print"<table class=width40 rules=cols>". NEWLINE;

print"<tr>". NEWLINE;
html_tbl_print_header( lang_get('release')."/ ".lang_get("build")."/ ".lang_get("testset") );
html_tbl_print_header( lang_get('archive') );
print"</tr>". NEWLINE;

	foreach($release_array as $row_release) {
		$row_style = html_tbl_alternate_bgcolor( $row_style );

		$release_name		= $row_release[RELEASE_NAME];
		$release_id			= $row_release[RELEASE_ID];
		$release_archive	= $row_release[RELEASE_ARCHIVE];

		$checked = "";
		if( $release_archive=="Y" ) {

			$checked = "checked";
		}

		print"<tr class='$row_style'>". NEWLINE;
		print"<td class=tbl-l>$release_name</td>". NEWLINE;
		print"<td><input type=checkbox name='releases[$release_id]' $checked></td>". NEWLINE;
		print"</tr>". NEWLINE;

		foreach($row_release["builds"] as $row_build) {
			$row_style = html_tbl_alternate_bgcolor( $row_style );

			$build_name		= $row_build[BUILD_NAME];
			$build_id		= $row_build[BUILD_ID];
			$build_archive	= $row_build[BUILD_ARCHIVE];

			$checked = "";
			if( $build_archive=="Y" ) {

				$checked = "checked";
			}

			print"<tr class='$row_style'>". NEWLINE;
			print"<td class=tbl-l>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$build_name</td>". NEWLINE;
			print"<td><input type=checkbox name='builds[$build_id]' $checked></td>". NEWLINE;
			print"</tr>". NEWLINE;

			foreach($row_build["testsets"] as $row_testset) {
				$row_style = html_tbl_alternate_bgcolor( $row_style );

				$testset_name		= $row_testset[TS_NAME];
				$testset_id			= $row_testset[TS_ID];
				$testset_archive	= $row_testset[TS_ARCHIVE];

				$checked = "";
				if( $testset_archive=="Y" ) {

					$checked = "checked";
				}


				print"<tr class='$row_style'>". NEWLINE;
				print"<td class=tbl-l>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$testset_name</td>". NEWLINE;
				print"<td><input type=checkbox name='testsets[$testset_id]' $checked></td>". NEWLINE;
				print"</tr>". NEWLINE;

			}
		}
	}

	print"</table>". NEWLINE;

	print"<br>";

	print"<input type=submit value='".lang_get("archive")."'>";
	print"</form>". NEWLINE;

}

print"</div>". NEWLINE;

html_print_footer();

?>