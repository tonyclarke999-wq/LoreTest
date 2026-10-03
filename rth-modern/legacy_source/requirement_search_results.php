<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Search Results Page
#
# $RCSfile: requirement_search_results.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";


$page                   = basename(__FILE__);
#### Change to page that form is submitted to or hyperlinks link to ####
$action_page            = 'requirement_action.php';
$num                    = 0;
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];

html_window_title();

auth_authenticate_user();

#### Change to title of page ####
html_page_title($project_name ." - REQUIREMENTS");
html_page_header( $db, $project_name );

html_print_menu();

#### Change to api submenu function for this page type ####
requirement_menu_print ($page);
html_print_body();

error_report_check( $_GET );

print"<br>";

print"<form>";

// print column headers
print"<TABLE BORDER=1 WIDTH=100% CELLSPACING=0 CELLPADDING=2 ALIGN=CENTER>";
print"<TR BGCOLOR=#99CCFF ALIGN=CENTER VALIGN=TOP>";
print"<TH ALIGN=center VALIGN=top></TH>";
print"<TH ALIGN=center VALIGN=top>Name</TH>";
print"<TH ALIGN=center VALIGN=top>Path</TH>";
print"<TH ALIGN=center VALIGN=top>ReqID</TH>";
print"<TH ALIGN=center VALIGN=top>Type</TH>";
print"<TH ALIGN=center VALIGN=top>Area Covered</TH>";
print"<TH ALIGN=center VALIGN=top>Status</TH>";
print"<TH ALIGN=center VALIGN=top>Version</TH>";
print"<TH ALIGN=center VALIGN=top>Locked</TH>";
print"<TH ALIGN=center VALIGN=top>Assigned To Release</TH>";
print"<TH ALIGN=center VALIGN=top>Detail</TH>";
print("</TR>");


$query = "SELECT ReqID, ReqFileName, AreaSpecd, Type, Locked_By, Req_Folder_ID FROM Requirement";
$recordSet = $db->Execute($query);
$num = $recordSet->NumRows();

$query_count = $num;

if($num){

	while($row = $recordSet->FetchRow())
	{
		//get latest version
		$query_version = "SELECT Version, UID, Detail, Status, Assign_Release FROM requirementversion WHERE ReqID = '$row[ReqID]' ORDER BY UID DESC LIMIT 1";
		$recordSet_version = $db->Execute($query_version);
		$num_version = $recordSet_version->NumRows();

		if($num_version)
		{
			while($row_version = $recordSet_version->FetchRow())
			{
				$latest_version = $row_version['Version'];
				$Detail = $row_version['Detail'];
				$Status = $row_version['Status'];
				$Assign_Release = $row_version['Assign_Release'];
			}
		}

		// code to left pad the TestID with 0s.
		$DisplayTestID = sprintf("%05s",trim($row['ReqID']));

		#if($rowcolor=='1'){
		#	$rowcolor='0';
		#	print"<TR BGCOLOR=#FFFFFF ALIGN=LEFT VALIGN=TOP>";
		#}
		#else{
		#	$rowcolor='1';
		#	print"<TR BGCOLOR=#FFFFCC ALIGN=LEFT VALIGN=TOP>";
		#}		


		//print("<TR>");
		print("<TD><input type=checkbox></TD>");
		print("<TD><img src='./images/icons/file.gif'><A HREF= 'requirement_detail.php?reqID=$row[ReqID]'> $row[ReqFileName]</A></TD>");
		
		
		print("<TD>");
		
		build_path($row['Req_Folder_ID'], "", $db);
		
		print("</TD>");
		print("<TD><A HREF= 'requirement_detail.php?reqID=$row[ReqID]'>$DisplayTestID</A></TD>");
		print("<TD>$row[Type]</TD>");
		print("<TD>$row[AreaSpecd]</TD>");
		print("<TD>$Status &nbsp</TD>");
		print("<TD>$latest_version</TD>");
	
		if($row['Locked_By']){
			print("<TD><img src='./icons/lock.gif'>$row[Locked_By]</TD>");		
		}
		else{
			print("<TD>&nbsp</TD>");
		}
		
		print("<TD>$Assign_Release &nbsp</TD>");
		print("<TD>$Detail &nbsp</TD>");
		print("</TR>");
	}
}
else
{

}
print"</TABLE>";

	print"<SELECT NAME='secondary_UID' SIZE=1>";
	print"<OPTION VALUE='Assign_To_Release'>Assign To Release</OPTION>";
	print"<OPTION VALUE='Lock'>Lock</OPTION>";
	print"<OPTION VALUE='Unlock'>Unlock</OPTION>";
	print"<OPTION VALUE='Change_Status'>Change Status</OPTION>";
	print"<OPTION VALUE='Move'>Move</OPTION>";
	print"<OPTION VALUE='Delete'>Delete</OPTION>";
	print"</SELECT>";
	
	print"<input type=submit value=OK></TD>";		

	print "&nbsp &nbsp &nbspexport to";
	print"<SELECT NAME='req_export' SIZE=1>";
	print"<OPTION VALUE='CSV'>CSV</OPTION>";
	print"<OPTION VALUE='RTF'>Rich Text Format</OPTION>";
	print"<OPTION VALUE='PDF'>PDF</OPTION>";
	print"<OPTION VALUE='HTML'>HTML</OPTION>";
	print"<OPTION VALUE='XML'>XML</OPTION>";
	print"<OPTION VALUE='TXT'>TXT</OPTION>";
	print"</SELECT>";
	
	print"<input type=submit value=OK></TD>";		


?>
<P>
<div align="center">

</FORM>
<?php
print"</div>";
html_print_footer();


# ---------------------------------------------------------------------
# $Log: requirement_search_results.php,v $
# Revision 1.2  2006/06/10 01:55:06  gth2
# no message
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>