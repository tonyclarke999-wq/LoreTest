<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Version View Page
#
# $RCSfile: requirement_version_view.php,v $  $Revision: 1.2 $
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

html_print_body();
html_page_title($project_name ." - REQUIREMENTS");
html_page_header( $db, $project_name );
html_print_menu();

#### Change to api submenu function for this page type ####
requirement_menu_print ($page);

error_report_check( $_GET );

print"<br>";

$tbl_req 					= REQ_TBL;
$f_req_id 					= $tbl_req .".". REQ_ID;
$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
$f_req_type		 			= $tbl_req .".". REQ_TYPE;

$tbl_req_ver				= REQ_VERS_TBL;
$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;


$query = "SELECT $f_req_filename, $f_req_id, $f_req_area_covered, $f_req_ver_status, $f_req_type, $f_req_ver_version, $f_req_ver_uploaded_by, $f_req_ver_timestamp, $f_req_ver_uid, $f_req_ver_filename, $f_req_ver_comments, $f_req_ver_detail FROM $tbl_req INNER JOIN $tbl_req_ver ON $f_req_id  = $f_req_ver_req_id WHERE $f_req_ver_uid = '$req_version_id'";
#print("query= $query");
$recordSet = $db->Execute($query);
$row = $recordSet->FetchRow();
$num = $recordSet->NumRows();

if( $num ) {

		print"<br>";

			$req_filename		= $row[REQ_FILENAME];
			$req_area			= $row[REQ_AREA_COVERED];
			$req_type			= $row[REQ_TYPE];
			$req_status			= $row[REQ_VERS_STATUS];
			$req_version		= $row[REQ_VERS_VERSION];
			$req_uploaded_by	= $row[REQ_VERS_UPLOADED_BY];
			$req_timestamp		= $row[REQ_VERS_TIMESTAMP];
			$req_detail			= $row[REQ_VERS_DETAIL];

			print"<TABLE cols=2 border=1 rules=all WIDTH=80% cellspacing=0 cellpadding=2 ALIGN=CENTER>";

			//<!-- Title -->
			print"<TR><TD align=left colspan=4 bgcolor=#99CCFF bordercolor=#FFFFFF><B>Req Version</B></TD></TR>";

			//<!-- REQUIREMENT NAME-->
			print"<TR>";
				print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF NOWRAP>Requirement Name</TD>";
				print"<TD align=left>$req_filename</TD>";
			print"</TR>";

			print"<TR>";
					print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Area Covered</TD>";
					print"<TD align=left>$req_area</TD>";
			print"</TR>";

				print"<TR>";
					print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Requirement Type</TD>";
					print"<TD align=left>$req_type</TD>";
				print"</TR>";

				print"<TR>";
					print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Requirement Status</TD>";
					print"<TD align=left>$req_status</TD>";
				print"</TR>";

				print"<TR>";
					print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Version</TD>";
					print"<TD align=left>$req_version</TD>";
				print"</TR>";

				print"<TR>";
					print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Author</TD>";
					print"<TD align=left>$req_uploaded_by</TD>";
				print"</TR>";

				print"<TR>";
					print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>TimeStamp</TD>";
					print"<TD align=left>$req_timestamp</TD>";
				print"</TR>";


			print"<TR>";
				print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Detail</TD>";
				print"<TD>$req_detail</TD>";
			print"</TR>";

			print"</table>";

}
else{
	print("<H3>There are either no Requirements that meet your search criteria or none associated to $s_project_name.</H3>\n");
}

# ---------------------------------------------------------------------
# $Log: requirement_version_view.php,v $
# Revision 1.2  2006/02/24 11:33:08  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>