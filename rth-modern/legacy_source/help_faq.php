<?php

include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();

?>

<H2><FONT COLOR="<?php echo $H2COLOR;?>">Help: FAQ</FONT></H2>

<base target="_top">
<!--This is the code that allows navigation by links through the page. The menu for the page.-->
<TD VALIGN="top">
<P ALIGN="left"><font FACE="arial" SIZE="2"><B>Select a link below for more information.</B></FONT><BR><BR>
<a HREF="#winrunner"><font FACE="arial" SIZE="2"><B>WinRunner Results are not being shown in Tempest</B></FONT></a><BR>
<a HREF="#testsetmail"><font FACE="arial" SIZE="2"><B>How can I stop receiving emails telling me there is a new TestSet and to update my WRUN.INI file?</B></FONT></a><BR>
<a HREF="#settings"><font FACE="arial" SIZE="2"><B>Tempest displays too many columns in the results page, such as Phone Number and Claim Number. How can I remove them?</B></FONT></a><BR>
<a HREF="#delete"><font FACE="arial" SIZE="2"><B>How can I delete a test/testset/build/release?</B></FONT></a><BR>
<a HREF="#archiveresults"><font FACE="arial" SIZE="2"><B>There are too many Releases/Builds in my menu. How can I take them out of the menu but keep the results?</B></FONT></a><BR>
<a HREF="#archiveresults"><font FACE="arial" SIZE="2"><B>Archive tests</B></FONT></a><BR>
<a HREF="#archiveresults"><font FACE="arial" SIZE="2"><B>Menu colors</B></FONT></a><BR>
<a HREF="#archiveresults"><font FACE="arial" SIZE="2"><B>What to do first</B></FONT></a><BR>
<a HREF="#archiveresults"><font FACE="arial" SIZE="2"><B>Checklist for setting up project from scratch</B></FONT></a><BR>
<BR>
<font FACE="arial" SIZE="2"><B>Back To:&#32;</B><a HREF="help_index.php"><font FACE="arial" SIZE="2"><B>Help Index</B></FONT></a><BR><BR>

<TABLE CELLSPACING=0 CELLPADDING=5 BORDER="0" WIDTH=100%>
	<TR>

	<TD VALIGN="top">

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="winrunner"></a><B>WinRunner Results are not being shown in Tempest</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">
		There are several reasons why the results may not be viewable in Tempest -
		<UL>
		<LI>In the WINNT/wrun.ini file, the TESTSETID variable is not set to correspond to the TestSet in Tempest. It needs to be set to the value that was sent in the email and is shown in the Results section of Tempest.
		<br>
		<LI>The ID that Tempest uses for a WinRunner test script may not be set correctly at the beginning of the WinRunner test script, e.g.
		<br><code><font size=2>arControlArray[arc_TSuiteID]=2;</font></code>
		<LI>The ODBC connection may not be pointing to the correct database. Check that the Name is correct for the DSN. This is the session name that the WriteVerificationRecord function uses. Also check that the Configuration of the ODBC driver is correctly setup, e.g the database is 'erne'. Check these settings with the person that set up Tempest for your project.
		<LI>Relating to the previous point, the hosts file needs to include 'erne	136.184.232.9'.
		</UL>
		</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="testsetmail"></a><B>How can I stop receiving emails telling me there is a new TestSet and to update my WRUN.INI file?</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">
		This mail is generated automatically from Tempest and is only sent to those that have 'Email TestSet' selected in their User Profile. To change this, select View User from the User menu. Click on the Edit link beside your details. On the Edit page, uncheck the checkbox for 'Email TestSet' and press the Save button. The next time a new TestSet is created, you will not receive any notification of it.
		</P>

		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="settings"></a><B>Tempest displays too many columns in the results page, such as Phone Number and Claim Number. How can I remove them?</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">
		When Tempest is created for a project, these columns are established to be shown unless specified. They can be easily removed by sending an email to <A HREF='mailto:Tempest_Admin'>Tempest Admin</A>.
		<BR>
		The columns that can be modified are :
		<BR>TestCase
		<BR>PolicyID
		<BR>ClaimID
		<BR>nNumber
		<BR>PhoneNo
		<BR>QuoteID
		<BR>Window
		<BR>Object
		<BR>Memory Statistics
		<BR>Test Priority

		</P>

		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="delete"></a><B>How can I delete a test/testset/build/release?</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">
		This will depend on the permissions that the user has. The current permissions can be viewed by clicking the <I>Users</I> menu, selecting <I>View Users</I> and clicking on the <I>Edit</I> link for the logged in user. There is the option for a user to have Delete Rights, however only an Administrator can give/remove this right. If the user feels they should have different rights, then they should speak to an Administrator of the system.
		</P>

		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="archiveresults"></a><B>There are too many Releases/Build in my menu. How can I take them out of the menu but keep the results?</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">
		This will depend on the permissions that the user has. Only administrators of Tempest will have the option to archive results through the <I>Admin/Archive/Archive Results</I> link.
		The user can select to archive a Release and the associated Builds and Testsets, or archive a Build and the associated Testsets or simply archive a Testset on it's own. Archiving is done by clicking on the checkbox beside the Release/Build/TestSet and then pressing the Archive button at the bottom of the page. Releases/Builds/TestSets can be taken out of archive by unclicking the checkbox beside the appropriate line.
		</P>

		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<BR>
	</TR>
</TABLE>


<hr>
<p><a href='help_index.php'>Return To Index</a></p>

</BODY>
</HTML>