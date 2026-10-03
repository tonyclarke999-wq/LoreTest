<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Compare Page
#
# $RCSfile: requirement_compare.php,v $  $Revision: 1.3 $
# ----------------------------------------------------------------------

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


print"<form name=reqCompare method=post action=requirement_compare.php>";
				
//<!-- Get all versions -->
$query_versions = "SELECT Version, UID FROM requirementversion WHERE ReqID = '$ReqID'";
$recordSet_versions = $db->Execute($query_versions);
$num_versions = $recordSet_versions->NumRows();
			
	//<!-- Versions -->
	print"Compare:";
	print"<SELECT NAME='secondary_UID' SIZE=1>";
	print"<OPTION VALUE=''></OPTION>";
	while($row_versions = $recordSet_versions->FetchRow())
	{
		print"<OPTION VALUE='$row_versions[UID]'>$row_versions[Version]</OPTION>";
	}

	print"</SELECT>";
	

//<!-- Get all versions -->
$query_versions1 = "SELECT Version, UID FROM requirementversion WHERE ReqID = '$ReqID'";
$recordSet_versions1 = $db->Execute($query_versions1);
$num_versions1 = $recordSet_versions1->NumRows();


	print"with:";
	print"<SELECT NAME='primary_UID' SIZE=1>";
	print"<OPTION VALUE=''></OPTION>";
	while($row_versions1 = $recordSet_versions1->FetchRow())
	{
		print"<OPTION VALUE='$row_versions1[UID]'>$row_versions1[Version]</OPTION>";
	}

	print"</SELECT>";
	
	print"<input type=hidden name=ReqID value=$ReqID>";
	print"<input type=submit value=Compare></TD>";			
	
	
	print"</form>";
	
$req_version_tbl		= REQ_VERS_TBL;
$f_req_detail			= REQ_VERS_DETAIL;
$f_req_version			= REQ_VERS_VERSION;
$f_req_vers_id			= REQ_VERS_UNIQUE_ID;

$query_primary = "SELECT $f_req_detail, $f_req_version FROM $req_version_tbl WHERE $f_req_vers_id = '$primary_UID'";
$recordSet_primary = $db->Execute($query_primary);
$row_primary = $recordSet_primary->FetchRow();


$query_secondary = "SELECT $f_req_detail, $f_req_version FROM $req_version_tbl WHERE $f_req_vers_id = '$secondary_UID'";
$recordSet_secondary = $db->Execute($query_secondary);
$row_secondary = $recordSet_secondary->FetchRow();


print"<TABLE BORDER=1>";

print"<TR>";
print"<TH>Version $row_secondary[Version]</TH>";
print"<TH>Version $row_primary[Version]</TH>";
print"</TR>";

print"<TR>";
print"<TD VALIGN=TOP>$row_secondary[Detail]</TD>";
print"<TD VALIGN=TOP>$row_primary[Detail]</TD>";
print"</TR>";

print"</TABLE>";


html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_compare.php,v $
# Revision 1.3  2006/06/10 01:55:06  gth2
# no message
#
# Revision 1.2  2006/02/24 11:33:32  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>