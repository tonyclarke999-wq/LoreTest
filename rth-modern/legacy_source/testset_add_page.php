<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Add Page
#
# $RCSfile: testset_add_page.php,v $  $Revision: 1.5 $
# ---------------------------------------------------------------------


# No Longer Used

include"./api/include_api.php";
auth_authenticate_user();

$page					= basename(__FILE__);
$form_name				= 'add_release';
$action_page			= 'testset_add_action.php';
$release_edit_page		= 'testset_page.php';
$release_signoff_page	= 'release_signoff_page.php';
$build_page				= 'build_page.php';
$delete_page			= 'delete_page.php';
$s_project_properties	= session_get_project_properties();
$project_name			= $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];
$row_style				= '';


if (isset($_GET['failed'])) {
    $is_validation_failure = $_GET['failed'];
}
else
    $is_validation_failure = false;

$s_release_table_display_options = array_merge( session_get_display_options("results"),
												session_get_properties('results_properties') );

html_window_title();
html_print_body( $form_name, 'rel_name');
html_page_title($project_name ." - ". lang_get('release_page') );
html_page_header( $db, $project_name );
html_print_menu();

error_report_check( $_GET );

$s_admin_properties = session_get_release_display_options();

?>

<script language="JavaScript" type="text/javascript">

function checkBoxValidate(cb, startCount, endCount) {

	#Evaluates if the check box has been checked in the "All Regression" etc
	//alert();
	if (eval("document.myform1.elements[" + cb + "].checked") == true) {
		#Then it CHECKS the check boxes for that Test Type
		for (j = startCount-1;  j <= endCount-1;  j++ )	{
			document.myform2.elements[j].checked = true;
		}
	} else {
		#Then it UNCHECKS the check boxes for that Test Type
		for (j = startCount-1;  j <= endCount-1;  j++ ) {
			document.myform2.elements[j].checked = false;
		}
	}
}

</script>


<?php

$test_tbl		= TEST_TBL;
$f_test_id		= TEST_ID;
$f_test_name	= TEST_NAME;
$f_test_type	= TEST_TESTTYPE;
$f_area_tested	= TEST_AREA_TESTED;
$f_deleted		= TEST_DELETED;
$f_archived		= TEST_ARCHIVED;
$f_priority		= TEST_PRIORITY;
$f_manual		= TEST_MANUAL;
$f_automated	= TEST_AUTOMATED;
$f_status		= TEST_STATUS;

# Query to get TestSuite Name for first table on page
$query = "SELECT DISTINCT($f_test_type) FROM $test_tbl WHERE $f_deleted = 'N' AND $f_archived = 'N' ORDER BY $f_test_type ASC";
$recordSet = $db->Execute($query);
$num = $recordSet->NumRows();

# Start of form
print"<FORM NAME=myform1 ENCTYPE='multipart/form-data' ACTION='testset_add_action.php' METHOD=post>". NEWLINE;


	print"<TABLE WIDTH='100%'>";
	print"<TR>";
		print"<TD colspan=2><B>Select Test Type:</B></TD>";
	print"</TR>";
	print"<TR>";
		print"<TD ALIGN=right>Test Set Name</TD>";
		print"<TD><INPUT TYPE=text NAME='testset_name' VALUE='".$_POST['testset_name']."' SIZE=30 MAXLENGTH=30></TD>";
	print"</TR>";

//This variable is for the name of the checkbox. If we rely on user to enter their own Test Type, things could be messed up by spaces etc.
$x = 1;
$endCount = 0;

while($row = $recordSet->FetchRow()) {

	// find a way to account for a blank name ''
	// for now this is okay. Test to see what happens when there's a blank entry in TestSuite.TestType
	$test_type = $row[TEST_TESTTYPE];

	/* Loop through and get the tests for each TestType */
	$query_1 = "SELECT $f_test_name, $f_test_type, $f_priority, $f_test_id, $f_manual, $f_automated, $f_status FROM $test_tbl WHERE $f_test_type = '$test_type' AND $f_deleted = 'N' AND $f_archived = 'N' ORDER BY $f_test_id ASC";


	$recordSet_1 = $db->Execute($query_1);
	$num_1 = $recordSet_1->NumRows();

	/* Get the number of test for each TestType */
	if ( isset( $startCount ) && $startCount == -1 ) {
		$startCount = 0;
		$endCount = $endCount + ( $num_1 - 1 );
	} else {
		$startCount = $endCount + 1;
		$endCount = $endCount + $num_1;
	}

	/* Create a checkbox for each test type and also if there is no test type*/
	print"<TR>";
		print"<TD ALIGN=right>All $test_type</TD>";
		print"<TD><input type=checkbox name=$x onClick='javascript:checkBoxValidate(name,$startCount,$endCount)'></TD>";
	print"</TR>". NEWLINE;
	$x++;
}

print"</TABLE>";

print"</form>";
print"<FORM NAME=myform2 ENCTYPE='multipart/form-data' ACTION='testset_add_action.php' METHOD=post\n>";

print"<TABLE BORDER=1 RULES=COLS WIDTH='90%' CELLSPACING=0 CELLPADDING=2 ALIGN=center>". NEWLINE;

$query_2 = "SELECT $f_test_name, $f_test_type, $f_priority, $f_test_id, $f_manual, $f_automated, $f_status, $f_area_tested FROM $test_tbl WHERE $f_deleted = 'N' AND $f_archived = 'N' ORDER BY $f_test_type ASC";
$recordSet_2 = $db->Execute($query_2);
$num_2 = $recordSet_2->NumRows();

/* If there are TestTypes in TestSuite, print the table */
if($num_2) {
	//print"<TABLE BORDER=1 RULES=COLS WIDTH=100% CELLSPACING=0 CELLPADDING=2 ALIGN=center>";
	print"<TR BGCOLOR='#99CCFF' ALIGN=CENTER VALIGN=TOP>". NEWLINE;
	//print"<TD ALIGN=center VALIGN=top><FONT SIZE=2>#</FONT></TD>";
	print"<TD ALIGN=center VALIGN=top>M/A</TD>";
	print"<TD ALIGN=center VALIGN=top>Test Name</TD>";
	print"<TD ALIGN=center VALIGN=top>Test Type</TD>";
	/*
	if($s_show_priority == 'Y')
	{
		print"<TD ALIGN=center VALIGN=top><FONT SIZE=2>Priority</FONT></TD>";
	}
	*/
	print"<TD ALIGN=center VALIGN=top>Area Tested</TD>";
	print"<TD ALIGN=center VALIGN=top>Status</TD>";
	print"<TD ALIGN=center VALIGN=top>Include</TD>";
	print"</TR>". NEWLINE;

	$i = 0;
	$rowcolor='1';

	/* While there are TestTypes in TestSuite, get the TemepestTestID */
	while($row_2 = $recordSet_2->FetchRow()) {
		############### Alternate Row Color ######################
		if($rowcolor=='1') {
			$rowcolor='0';
			print"<TR BGCOLOR='#FFFFFF' ALIGN=CENTER VALIGN=TOP>";
		} else {
			$rowcolor='1';
			print"<TR BGCOLOR='#FFFFCC' ALIGN=CENTER VALIGN=TOP>";
		}

		########### Show Manual - Automated icons #####################
		if($row_2['Steps']=='YES' && $row_2['Script'] == 'YES') {
			print("<TD ALIGN=LEFT VALIGN=TOP><span class=manual>M</span><span class=auto>A</span></TD>\n");
		} elseif($row_2['Script']=='YES') {
			print("<TD ALIGN=LEFT VALIGN=TOP><span class=auto>A</span></TD>\n");
		} elseif($row_2['Steps']=='YES') {
			print("<TD ALIGN=LEFT VALIGN=TOP><span class=manual>M</span></TD>\n");
		} else {
			print"<TD ALIGN=LEFT VALIGN=TOP></TD>";
		}
		print"<TD ALIGN=left VALIGN=top>$row_2[TEST_NAME]</TD>". NEWLINE;
		print"<TD ALIGN=left VALIGN=top>$row_2[TEST_TESTTYPE]</TD>". NEWLINE;
		/*
		if($s_show_priority == 'Y') {
			print"<TD ALIGN=left VALIGN=top><FONT SIZE=2>$row_2[Priority]</FONT></TD>". NEWLINE;
		}
		*/
		print"<TD ALIGN=left VALIGN=top>$row_2[TEST_AREA_TESTED]</TD>";
		print"<TD ALIGN=left VALIGN=top>$row_2[TEST_STATUS]</TD>". NEWLINE;

		print"<TD><INPUT TYPE='checkbox' name='chbox[$i]'></TD>". NEWLINE;
		//print("ckbox = $row_3[TestType][$i]<BR>");
		$i++;
		print"</TR>". NEWLINE;
	}
}



print"</table>";

print"<TABLE BORDER=0 RULES=COLS WIDTH=100% CELLSPACING=2 CELLPADDING=2 ALIGN=center>". NEWLINE;
		print"<TR>";
		print"<TD COLSPAN=2 ALIGN=center><INPUT TYPE=submit VALUE=Create></TD>";
		print"</TR>";
print"</table>";
//print"<input name=testset_description type=hidden value='".$_POST['testset_description']."'>";
print"</form>";


# ---------------------------------------------------------------------
# $Log: testset_add_page.php,v $
# Revision 1.5  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.4  2006/06/24 14:34:14  gth2
# updating changes lost with cvs problem.
#
# Revision 1.3  2006/02/24 11:32:49  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.2  2006/02/09 12:34:27  gth2
# changing db field names for consistency - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>