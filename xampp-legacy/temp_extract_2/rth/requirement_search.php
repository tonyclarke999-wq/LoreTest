<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Search Page
#
# $RCSfile: requirement_search.php,v $  $Revision: 1.2 $
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


		print"<TABLE BORDER=1 WIDTH=85% ALIGN=CENTER cellspacing=0 cellpadding=2 BGCOLOR= #666666>";
			print"<TR>";
			print"<TD bgcolor=#99CCFF>";
			print"<TABLE cols=2 border=0 rules=all WIDTH=100% cellspacing=0 cellpadding=2 ALIGN=CENTER>";
			print"<FORM NAME=req_edit METHOD=post ACTION=requirement_search_results.php>";
			
			//<!-- Title -->
			print"<TR><TD align=left colspan=4 bgcolor=#99CCFF bordercolor=#FFFFFF><B>Filter For Requirements</B></TD></TR>";
			
			//<!-- REQUIREMENT NAME-->
			//print"<TR>";
			//	print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF NOWRAP>Requirement Name</TD>";
			//	print"<TD align=left bgcolor=#99CCFF bordercolor=#FFFFFF>&nbsp<INPUT TYPE=text SIZE=66 NAME='reqfilename' VALUE='$row[ReqFileName]'></TD>";
			//print"</TR>";


			print"<TR>";
			print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Requirement Status</TD>";
			print"<TD align=left bgcolor=#99CCFF bordercolor=#FFFFFF>&nbsp<SELECT NAME=reqstatus SIZE=1>";
				print"<OPTION VALUE='Submitted'>Submitted</OPTION>";
				print"<OPTION VALUE='Reviewed'>Reviewed</OPTION>";
				print"<OPTION VALUE='Approved'>Approved</OPTION>";
				print"<OPTION VALUE='Implemented'>Implemented</OPTION>";
				#print"<OPTION VALUE='$reqstatus' SELECTED>$reqstatus</OPTION>";
				print"<OPTION VALUE=''>&nbsp</OPTION>";
			print"</SELECT></TD>". NEWLINE;
			print"</TD>";
			print"</TR>";



			//<!-- Get area tested -->
			$query_areacoverage = "SELECT AreaCoverage FROM ReqAreaCoverage";
			$recordSet_areacoverage = $db->Execute($query_areacoverage);
			$num_areacoverage = $recordSet_areacoverage->NumRows();
		
			if($num_areacoverage){
				//<!-- AREA TESTED -->
				print"<TR>";
					print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Area Covered</TD>";
					print"<TD align=left bgcolor=#99CCFF bordercolor=#FFFFFF>&nbsp<SELECT NAME='areacoverage' SIZE=1>";
					while($row_areacoverage = $recordSet_areacoverage->FetchRow())
					{
						print"<OPTION VALUE='$row_areacoverage[AreaCoverage]'>$row_areacoverage[AreaCoverage]</OPTION>";
					}
					#print"<OPTION SELECTED VALUE='$row[AreaSpecd]'>$row[AreaSpecd]</OPTION>";
					print"<OPTION VALUE=''>&nbsp</OPTION>";
					print"</SELECT></TD>";
			print"</TR>";	
			}
			
			//<!-- Get test type -->
			$query_reqdoctyye = "SELECT ReqDocTypeName FROM ReqDocType";
			$recordSet_reqdoctyye = $db->Execute($query_reqdoctyye);
			$num_reqdoctyye = $recordSet_reqdoctyye->NumRows();
		
			if($num_reqdoctyye){
				//<!-- TEST TYPE - We may need to add some categories to this list -->
				print"<TR>";
					print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Requirement Type</TD>";
					print"<TD align=left bgcolor=#99CCFF bordercolor=#FFFFFF>&nbsp<SELECT NAME='reqdoctype' SIZE=1>";
					while($row_reqdoctyye = $recordSet_reqdoctyye->FetchRow())
					{
						print"<OPTION VALUE='$row_reqdoctyye[ReqDocTypeName]'>$row_reqdoctyye[ReqDocTypeName]</OPTION>";
					}
					#print"<OPTION SELECTED VALUE='$row[Type]'>$row[Type]</OPTION>";
					print"<OPTION VALUE=''>&nbsp</OPTION>";
					print"</SELECT></TD>";
				print"</TR>";
			}		


			//<!-- Get release info -->
			$query_release = "SELECT ReleaseName FROM Release";
			$recordSet_release = $db->Execute($query_release);
			$num_release = $recordSet_release->NumRows();
		
			if($num_release){
				//<!-- Release -->
				print"<TR>";
					print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Assigned To Release</TD>";
					print"<TD align=left bgcolor=#99CCFF bordercolor=#FFFFFF>&nbsp<SELECT NAME='release' SIZE=1>";
					while($row_release = $recordSet_release->FetchRow())
					{
						print"<OPTION VALUE='$row_release[ReleaseName]'>$row_release[ReleaseName]</OPTION>";
					}
					#print"<OPTION SELECTED VALUE='$row[Assign_Release]'>$row[Assign_Release]</OPTION>";
					print"<OPTION VALUE=''>&nbsp</OPTION>";
					print"</SELECT></TD>";
				print"</TR>";
			}		




			//print"<TR>";
			//print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Revision Type</TD>";
			//print"<TD align=left bgcolor=#99CCFF bordercolor=#FFFFFF>&nbsp<SELECT NAME=revision_type SIZE=1>";
			//	print"<OPTION VALUE='Minor'>Minor</OPTION>";
			//	print"<OPTION VALUE='Major'>Major</OPTION>";
			//print"</SELECT></TD>". NEWLINE;
			//print"</TD>";
			//print"</TR>";

			
			
			//print"<TR>";
			//print"<TD ALIGN=right bgcolor=#99CCFF bordercolor=#FFFFFF>Reason for Change/ Mantis Ticket</TD>";
			//print"<TD align=left bgcolor=#99CCFF bordercolor=#FFFFFF><INPUT TYPE=text SIZE=66 NAME='reason'></TD>";
			//print"</TD>";
			//print"</TR>";


				#print"<input type=hidden name=reqID value='$row[ReqID]'>";
				#print"<input type=hidden name=UID value='$row[UID]'>";
				#print"<input type=hidden name=version value='$row[Version]'>";

				print"<tr>";
				print"<td align=center colspan=2>";
					print"<input type=submit name=submit value='Filter'>";
				print"</td>";
				print"</tr>";
				print"</form>";
				print"</table>";
				
			

	print"</table>";


?>
<?php
print"</div>";
html_print_footer();


# ---------------------------------------------------------------------
# $Log: requirement_search.php,v $
# Revision 1.2  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>