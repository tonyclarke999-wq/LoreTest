<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Version History Page
#
# $RCSfile: test_doc_history_page.php,v $  $Revision: 1.6 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$s_project_properties	= session_get_project_properties();
$s_project_id				= $s_project_properties['project_id'];
$s_project_name			= $s_project_properties['project_name'];
$test_id				= $_GET['test_id'];
$manual_test_id			= $_GET['mantestid'];
$display_test_id		= util_pad_id( $test_id );
$test_name				= test_get_name( $test_id );

$s_user_properties		= session_get_user_properties();
$s_delete_rights		= $s_user_properties['delete_rights'];
$s_user_id				= $s_user_properties['user_id'];

$project_manager		= user_has_rights( $s_project_id, $s_user_id, MANAGER );
$user_has_delete_rights	= ($s_delete_rights==="Y" || $project_manager);

$message = lang_get('delete_confirm_suppdoc');

$row_style = '';

html_window_title();
html_print_body();
html_page_title($s_project_name ." - ". lang_get('test_doc_history_page') );
html_page_header( $db, $s_project_name );
html_print_menu();


#### Change to api submenu function for this page type ####
test_menu_print ($page);
error_report_check( $_GET );

print"<br><br>". NEWLINE;

# Display test information
print"<div align=center>". NEWLINE;
print"<table class=width95>". NEWLINE;
print"<tr class='tbl_header'>". NEWLINE;
	print"<td width='50%'>". lang_get('test_id') ."</td>". NEWLINE;
	print"<td width='50%'>". lang_get('test_name') ."</td>". NEWLINE;
	#print"<td width='33%'>". lang_get('test_version') ."</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
	print"<td class=grid-data-c><a href='test_detail_page.php?test_id=$test_id&project_id=$s_project_id&tab=2'>$display_test_id</a></td>". NEWLINE;
	print"<td class=grid-data-c>$test_name</td>". NEWLINE;
	#print"<td class=grid-data-c>$version_no</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;



$rows = test_get_document_detail($manual_test_id);

print"<br><br>". NEWLINE;

if( !empty($rows) ) {

	print"<br>". NEWLINE;
	print"<table class=width95>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;

		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
			print"<td class=grid-header-c>". lang_get('file_type') ."</td>". NEWLINE;
			print"<td class=grid-header-c>". lang_get('file_name') ."</td>". NEWLINE;
			print"<td class=grid-header-c>". lang_get('version') ."</td>". NEWLINE;
			print"<td class=grid-header-c>". lang_get('view') ."</td>". NEWLINE;
			print"<td class=grid-header-c>". lang_get('download') ."</td>". NEWLINE;
			if($user_has_delete_rights){
				print"<td class=grid-header-c>". lang_get('delete') ."</td>". NEWLINE;
			}
			print"<td class=grid-header-c>". lang_get('uploaded_by') ."</td>". NEWLINE;
			print"<td class=grid-header-c>". lang_get('date_added') ."</td>". NEWLINE;
			print"<td class=grid-header-c>". lang_get('info') ."</td>". NEWLINE;
		print"</tr>". NEWLINE;

		foreach($rows as $row) {

			$display_name		= $row[MAN_TD_DISPLAY_NAME];
			$man_test_id		= $row[MAN_TD_MANUAL_TEST_ID];
			$filename			= $row[MAN_TD_VER_FILENAME];
			$comments			= $row[MAN_TEST_DOCS_VERS_COMMENTS];
			$time_stamp			= $row[MAN_TD_VER_TIME_STAMP];
			$uploaded_by		= $row[MAN_TD_VER_UPLOADED_BY];
			$version			= $row[MAN_TD_VER_VERSION];
			$doc_type			= $row[MAN_TEST_DOCS_VERS_MANUAL_DOC_TYPE_NAME];

			$fname = $s_project_properties['test_upload_path'] . $filename;
			if(IGNORE_VERSION_FILENAME_VALIDATION){
				$file_name = substr($filename,28);	
			}else
				$file_name = $display_name;
			

			print"<tr>". NEWLINE;
				print"<td class=grid-data-c>".html_file_type( $filename )."</td>". NEWLINE;
				print"<td class=grid-data-c>$file_name</td>". NEWLINE;
				print"<td class=grid-data-c>$version</td>". NEWLINE;
				print"<td class=grid-data-c>";
				print"<a href='$fname' target='new'>" . lang_get('view') . "</a>";
				print"</td>". NEWLINE;
				print"<td class=grid-data-c>";
				print"<a href='download.php?upload_filename=$fname'>" . lang_get('download') . "</a>";
				print"</td>". NEWLINE;
				if($user_has_delete_rights){
					print"<td class=grid-data-c>";
					#print"<a href='test_delete_doc_version.php?test_id=$test_id&mantestid=$manual_test_id&filename=$filename'>";
					print '<a onclick="return confirmSubmit(\''.$message.'\')" href="test_delete_doc_version.php?test_id='.$test_id. '&mantestid='. $man_test_id .'&filename='.$filename.'">';
					print lang_get('delete')."</a></td>";
				}
				print"<td class=grid-data-c>$uploaded_by</td>". NEWLINE;
				print"<td class=grid-data-c>$time_stamp</td>". NEWLINE;
				print"<td class=grid-data-c>". NEWLINE;

				  if($comments) {
					  print"<img src='". IMG_SRC . "/info.gif' title='$comments'>";
				  }
				  else {
					  print"&nbsp;";
				  }

				print"</td>". NEWLINE;
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;


}
print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_doc_history_page.php,v $
# Revision 1.6  2008/08/07 10:57:51  peter_thal
# Now blanks are replaced with underscores by adding a new supporting doc
#
# Revision 1.5  2008/08/05 10:42:43  peter_thal
# small changes: delete confirm SuppDocs, Error message file upload, disabled sorting teststeps
#
# Revision 1.4  2008/07/23 14:53:50  peter_thal
# delete supporting docs feature added (linux/unix)
#
# Revision 1.3  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.2  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1  2006/03/12 21:29:47  gth2
# Adding initial version - gth
#
# ---------------------------------------------------------------------
?>