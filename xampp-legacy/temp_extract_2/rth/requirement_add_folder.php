<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Requirement Add Folder
#
# $RCSfile: requirement_add_folder.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

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

if(isset($req_folder_id)){
	$parent = $req_folder_id;
}
else{
	$parent = '0';
}

?>

</head>

<?php

	print"<BR>";

if( !isset( $submit ) )
{
	print"<DIV ALIGN=center>";
	print"<TABLE BORDER=1 WIDTH=75% BGCOLOR= #666666 ALIGN=CENTER >";
		print"<TR>";
		print"<TD BGCOLOR=#99CCFF>";
			print"<TABLE WIDTH=100%>";
			print"<FORM METHOD=post name=req_add_folder ACTION=requirement_add_folder.php>";
			
			// Title
			print"<TR>";
				print"<TD COLSPAN=2 ALIGN=left><B>Add A New Requirements Folder</B></TD>";
			print"</TR>";

			// Requirement File Name
			print"<TR>";
				print"<TD ALIGN=right>Folder Name</TD>";
				print"<TD ALIGN=left><INPUT TYPE=text SIZE=60 NAME=req_foldername></TD>";
			print"</TR>";

			//Formatting
			print"<TR>";
				print"<TD></TD>";
			print"</TR>";

			print"<TR>";
				print"<TD><INPUT TYPE=hidden NAME=req_folder VALUE=$parent></TD>";
			print"</TR>";

			print"<TR>";
				print"<TD COLSPAN=2 ALIGN=center><INPUT TYPE=submit NAME=submit VALUE=Save></TD>";
			print"</TR>";

		print"</FORM>";
		print"</TABLE>";
	print"</TD>";
	print"</TR>";
	print"</TABLE>";

	print"<BR>";
}

else
{	

	$query = "INSERT INTO req_folders (name, parent) VALUES ('$req_foldername', '$req_folder')";
	$db->Execute($query);


	//header("Location: req.php");
	print"<meta http-equiv=\"Refresh\" content=\"1;URL=requirement_page.php?req_folder_id=$req_folder\">";
	exit();
}
?>
<?php
print"</div>";
html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_add_folder.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>
