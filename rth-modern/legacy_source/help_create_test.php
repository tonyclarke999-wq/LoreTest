<?php

include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();

?>

<H2><FONT COLOR="<?php echo $H2COLOR;?>">Help: Creating A Test</FONT></H2>

<base target="_top">
<!--This is the code that allows navigation by links through the page. The menu for the page.-->
<TD VALIGN="top">

<P ALIGN="left"><font FACE="arial" SIZE="2"><B>Select a link below for more information.</B></FONT><BR><BR>
<a HREF="#purpose"><font FACE="arial" SIZE="2"><B>Purpose</B></FONT></a><BR>
<a HREF="#creatingreq"><font FACE="arial" SIZE="2"><B>Creating A Test From A Requirement</B></FONT></a><BR>
<a HREF="#creatingman"><font FACE="arial" SIZE="2"><B>Creating A Test With Manual Steps</B></FONT></a><BR>
<a HREF="#automating"><font FACE="arial" SIZE="2"><B>Automating A Test</B></FONT></a><BR>
<a HREF="#passfail"><font FACE="arial" SIZE="2"><B>Marking A Test Pass/Fail</B></FONT></a><BR>
<a HREF="#table"><font FACE="arial" SIZE="2"><B>Understanding The Test Table</B></FONT></a><BR><BR>
<font FACE="arial" SIZE="2"><B>Back To:&#32;</B><a HREF="help_index.php"><font FACE="arial" SIZE="2"><B>Help Index</B></FONT></a><BR><BR>


<TABLE CELLSPACING=0 CELLPADDING=5 BORDER="0" WIDTH=100%>
	<TR>

	<TD VALIGN="top">

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="purpose"></a><B>Purpose</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Tempest is designed to follow the life-cycle of the testing process.
						The system contains requirements, from which you can create a test (sometimes thought of as a scenario).
						Each test can execute manually or can be automated.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="creatingreq"></a><B>Creating A Test From A Requirement</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">The "View Test" link on the Requirements page provides the ability to view or create a test for a requirement.
						It is best to create all the tests that will adequately cover a single requirement before adding any manual steps to the test.
						Once the test plan has been completed for each requirement, you can add the manual steps for each test.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="creatingman"></a><B>Creating A Test With Manual Steps</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Once there has been a test plan completed, manual steps can be added to the tests.
						Select the "Tests" link from the main toolbar located at the top of your screen.
						Click the "Create New Test" link located above the table hyperlinks on the screen.
						This link will generate the "Create Test Page."
						Here type the Test Name in the field provide.
						In the Req Document field select a document you would like this particular test associated with.
						Next add the Purpose of the test and select the Area Tested, Test Type, Test Owner, and Scripter from the drop down menus provided.
						Finally specify if the test is Manual or Automated and click the save button.
						Once the save button has been selected the "Tests Page" is repopulated with the New Test.
	<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
	<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="automating"></a><B>Automating A Test</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">After a test has been run against several builds, it may be considered for automation.
						It is best to automate tests that cover areas that are fairly stable and are not likely to change a great deal.
						</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="passfail"></a><B>Marking A Test Pass/Fail</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">To mark a test Pass or Fail first display the Test Results where the Test is to be passed or failed.
									To do this navigate to the Test Results by using the left hand tree view.
									Then select the TestSuiteID of the test to pass/fail.
									Next, select the "Results" link of the test to be passed/failed.
									A new page will generate and will populate a new table located at the top of the screen.
									This table contains a Pass Test column with a "Pass" link.
									If this link is selected the page will refresh and in the Assigned column the name of the person logged in will appear for passing the test.
									To add a comment after selecting the Pass link, again select the "Results" link and when the new page is populated select the "Comment" link in the smaller table located at the top of the screen.
									A text box will generate and there the user will be able to add a comment and make other changes.
									When complete select the "Save" button and the user will be returned to the Test Results Page.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->


		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="table"></a><B>Understanding The Test Table</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">The following will explain each column in the Test Table that is generate when the "Tests" link is selected from the main toolbar located at the top of the browser.<BR>
						<BR><I>M and/or A</I>&nbsp This is the first column in the table and indicates whether the test is a manual test or an automated test.
						<BR><I>Test Name</I>&nbsp The Test Name is the name given to the test*.
						<BR><I>Owner</I>&nbsp The Owner is the name of the person who developed the test set*.
						<BR><I>Test Type</I>&nbsp The Test Type is the type of test the test set is*.
						<BR><I>Area Tested</I>&nbsp Area Tested is the area that the test is related to*.
						<BR><I>Requirements</I>&nbsp The Requirements are the documents related to the test.
						<BR><I>Test Docs</I>&nbsp These are the documents associated with the tests.
						<BR><I>Scripter</I>&nbsp The Scripter is the person who wrote the automated the tests according to the documents*.
						<BR><I>Edit</I>&nbsp This link will allow the user to edit the test.
						<BR><I>Remove</I>&nbsp This link will remove the test.
					<BR>*Please note that these title headers are hyperlinks.  Select link and the column will be sorted in alphabetical order.<BR>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->


	<BR>
	</TR>
</TABLE>

<?php

print"<hr>". NEWLINE;
print"<a href='help_index.php'>". lang_get('help_return_to_index') ."</a>". NEWLINE;

?>

</BODY>
</HTML>
