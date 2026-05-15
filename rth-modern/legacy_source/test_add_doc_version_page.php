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
# $RCSfile: test_add_doc_version_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$s_project_properties	= session_get_project_properties();
$project_id				= $s_project_properties['project_id'];
$project_name			= $s_project_properties['project_name'];
$test_id				= $_GET['test_id'];
$display_test_id		= util_pad_id( $test_id );
$test_name				= test_get_name( $test_id );

if( isset( $_POST['manual_test_id'] ) && $_POST['manual_test_id'] != "" ) {
	$manual_test_id = $_POST['manual_test_id'];
}
else {
	$manual_test_id	= $_GET['manual_test_id'];
}

$row_style = '';

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('file_upload_page') );
html_page_header( $db, $project_name );
html_print_menu();

#### Change to api submenu function for this page type ####
test_menu_print ($page);

print"<br><br>". NEWLINE;

$row = test_get_uploaded_document_detail( $manual_test_id );

$manual_test_id		= $row[MAN_TD_VER_MANUAL_TEST_ID];
$file_name			= $row[MAN_TD_DISPLAY_NAME];
$doc_type			= $row[MAN_TEST_DOCS_VERS_MANUAL_DOC_TYPE_NAME];


# Display test information and link back to test detail
print"<div align=center>". NEWLINE;
print"<table class=width95>". NEWLINE;
print"<tr class='tbl_header'>". NEWLINE;
	print"<td width='50%'>". lang_get('test_id') ."</td>". NEWLINE;
	print"<td width='50%'>". lang_get('test_name') ."</td>". NEWLINE;
	#print"<td width='33%'>". lang_get('test_version') ."</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
	print"<td class=grid-data-c><a href='test_detail_page.php?test_id=$test_id&project_id=$project_id&tab=2'>$display_test_id</a></td>". NEWLINE;
	print"<td class=grid-data-c>$test_name</td>". NEWLINE;
	#print"<td class=grid-data-c>$version_no</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;


print"<br><br>". NEWLINE;

error_report_check( $_GET );

# Display file upload form
print"<br>". NEWLINE;
print"<span class='required'>*</span> <span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
print"<table class=width95>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

	print"<form enctype=multipart/form-data name=upload action='test_add_doc_version_action.php' method=post>". NEWLINE;
	print"<table class=inner>". NEWLINE;

	print"<tr class=left>". NEWLINE;
		print"<td class=form-header-l colspan=2>" . lang_get('upload_new_file_version') . " $file_name</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>" . lang_get('file_name') . "<span class='required'>*</span></td>". NEWLINE;
		print"<td class=form-data-l><input type='file' name='uploadfile_required' size='60'></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>" . lang_get('comments') . "</td>". NEWLINE;
		print"<td class=form-data-l><textarea rows='2' cols='45' name='comments'>".  session_validate_form_get_field ('comments') ."</textarea></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"<tr>". NEWLINE;
		print"<td class=form-lbl-r>" .  lang_get('file_type') . "</td>". NEWLINE;
		print"<td class=form-data-l>". NEWLINE;
		print"<select name=doc_type>". NEWLINE;
			$test_types = test_get_test_type( $project_id, $blank=true );
			html_print_list_box_from_array( $test_types, $doc_type );
		print"</select></td>". NEWLINE;
	print"</tr>". NEWLINE;

	print"<input type='hidden' name='manual_test_id' value='$manual_test_id'>". NEWLINE;
	print"<input type='hidden' name='MAX_FILE_SIZE' value='25000000'>". NEWLINE; 

	print"<tr>". NEWLINE;
		print"<td class=center colspan=2><input type='submit' value='Upload'></td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;
print"<br>". NEWLINE;
print"<br>". NEWLINE;

print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_add_doc_version_page.php,v $
# Revision 1.3  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.2  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1  2006/03/12 21:30:54  gth2
# Adding initial version - gth
#
# ---------------------------------------------------------------------
?>