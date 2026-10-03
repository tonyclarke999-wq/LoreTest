<?php

include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();

?>

<H2><FONT COLOR="<?php echo $H2COLOR;?>">Help: Adding/Editing Calendar Entries</FONT></H2>

<base target="_top">
<!--This is the code that allows navigation by links through the page. The menu for the page.-->
<TD VALIGN="top">

<P ALIGN="left"><font FACE="arial" SIZE="2"><B>Select a link below for more information.</B></FONT><BR><BR>
<a HREF="#purpose"><font FACE="arial" SIZE="2"><B>Purpose</B></FONT></a><BR>
<a HREF="#views"><font FACE="arial" SIZE="2"><B>Calendar Views</B></FONT></a><BR>
<a HREF="#adding"><font FACE="arial" SIZE="2"><B>Adding A Calendar Entry</B></FONT></a><BR>
<a HREF="#editing"><font FACE="arial" SIZE="2"><B>Editing An Entry</B></FONT></a><BR>
<a HREF="#deleting"><font FACE="arial" SIZE="2"><B>Deleting An Entry</B></FONT></a><BR>
<a HREF="#searching"><font FACE="arial" SIZE="2"><B>Searching For An Entry</B></FONT></a><BR>
<a HREF="#exporting"><font FACE="arial" SIZE="2"><B>Exporting</B></FONT></a><BR><BR>
<font FACE="arial" SIZE="2"><B>Back To:&#32;</B><a HREF="help_index.php"><font FACE="arial" SIZE="2"><B>Help Index</B></FONT></a><BR><BR>


<TABLE CELLSPACING=0 CELLPADDING=5 BORDER="0" WIDTH=100%>
	<TR>

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="purpose"></a><B>Purpose</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">The purpose of the Calendar is to keep the users informed of important dates.
						The Calendar is a way of letting everyone logged onto the system know when tests are to be run, when to sign off on a project and anything else a user should need to know.
						To view the Calendar please select the "Calendar" link located in the main toolbar at the top of your browser. </P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->


		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="views"></a><B>Calendar Views</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Once the user has selected the "Calendar" link the Calendar is generated.
						The user has a full view of the current month and small views of the month before and after current month.
						Located at the bottom of the screen the user can select either a particular month, week or year to view by using the drop down menus.
						Also provided at the bottom of the screen is a "Printer Friendly" link which will print the Calendar on 8x11 paper.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="adding"></a><B>Adding A Calendar Entry</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">To add a Calendar Entry, find the day the entry should take place on and select the '+' symbol located in the upper right hand corner of every day block.
						Also the link "Add New Entry" located at the bottom of the screen will produce the same results.
						Once the '+' symbol has been selected Tempest generates an "Add Entry Form."
						Complete the form by typing a Brief Description (which will appear to the user as a link to enable him to receive the Full Description), and a Full Description.
						Make sure the correct date has been selected from the drop down menus provided and fill in the correct Time and Duration.
						Next selected the Priority, Access, whether you want the system to send a Reminder, a Type, if the entry is Recursive and how often you would like it to be Repeated.
						Select the Save button and the entry will be displayed in the Calendar.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="editing"></a><B>Editing An Entry</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">To Edit an entry click the link of the entry to be edited.
						On the screen that is populated select the "Edit Entry" link.
						Edit the entry and selected the Save button and the Edited Entry will appear in the Calendar.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->


		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="deleting"></a><B>Deleting An Entry</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">To Delete an entry from the Calendar select the entry's link.
						On the next screen populated select the "Delete Entry" link.
						When asked, "Are you sure you want to delete this entry," select yes.
						The Calendar is displayed with the entry deleted.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="searching"></a><B>Searching For An Entry</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Located at the bottom of the screen is a link called "Search."
						Think link enables the user to search for a particular entry in the Calendar.
						The user can select the month, week or year they would like to search to find the entry of interest.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="exporting"></a><B>Exporting</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">This link allows the user to Export selected entries to a Palm Pilot.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

	<BR>
	</TR>
</TABLE>

<hr>
<p><a href='help_index.php'>Return To Index</a></p>

</BODY>
</HTML>
