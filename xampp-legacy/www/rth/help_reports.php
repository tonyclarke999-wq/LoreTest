<?php

include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();

?>

<BODY BGCOLOR="<?php echo $BGCOLOR; ?>">

<H2><FONT COLOR="<?php echo $H2COLOR;?>">Help: Reports</FONT></H2>

<base target="_top">
<!--This is the code that allows navigation by links through the page. The menu for the page.-->
<TD VALIGN="top">
<P ALIGN="left"><font FACE="arial" SIZE="2"><B>Select a link below for more information.</B></FONT><BR><BR>
<a HREF="#failed"><font FACE="arial" SIZE="2"><B>Failed Verifications</B></FONT></a><BR>
<a HREF="#status"><font FACE="arial" SIZE="2"><B>Test Status</B></FONT></a><BR>
<a HREF="#machinesrunning"><font FACE="arial" SIZE="2"><B>Machines Running</B></FONT></a><BR>
<a HREF="#latest"><font FACE="arial" SIZE="2"><B>Latest Testing Status</B></FONT></a><BR>
<a HREF="#signoff"><font FACE="arial" SIZE="2"><B>Sign Off Report</B></FONT></a><BR>
<a HREF="#area"><font FACE="arial" SIZE="2"><B>Areas Tested Report</B></FONT></a><BR>
<a HREF="#requirement"><font FACE="arial" SIZE="2"><B>Requirements Report</B></FONT></a><BR>
<a HREF="#machinesreport"><font FACE="arial" SIZE="2"><B>Machines Report</B></FONT></a><BR>
<a HREF="#testsummary"><font FACE="arial" SIZE="2"><B>Tests Summary</B></FONT></a><BR>
<a HREF="#sitestats"><font FACE="arial" SIZE="2"><B>Site Statistics Report</B></FONT></a><BR>
<a HREF="#allproject"><font FACE="arial" SIZE="2"><B>All Projects Report </FONT></a>(Admin only)</B><BR><BR>
<font FACE="arial" SIZE="2"><B>Back To:&#32;</B><a HREF="help_index.php"><font FACE="arial" SIZE="2"><B>Help Index</B></FONT></a><BR><BR>

<TABLE CELLSPACING=0 CELLPADDING=5 BORDER="0" WIDTH=100%>
	<TR>

	<TD VALIGN="top">

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="failed"></a><B>Failed Verifications</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Failed Verifications" link to view a high level summary of verifications for a particular release, build and testset.
										The user selects the Release, Build and then TestSet to view. If the user wishes to change to a different Release, Build or TestSet, simply click on the underlined titles.
										From here the user can view the Verifications Passed and Verifications Failed for the selected TestSet.
										Also please note that the Verifications Failed is a link if there are failed verifications to view.
										The user can click on the number of fails and find more information on them, including a link to the actual test run that the fail occurred in. </P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->


		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="status"></a><B>Test Status</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Test Status" link to view the status of the tests for a particular Release, Build and TestSet.
										The user selects the Release, Build, TestSet and the type of test status to view.
										If the user wishes to change to a different Release, Build, TestSet or Test Status, simply click on the underlined titles.
										This Report will display whether the test was Automated or Manual, the Test Name, Owner, Scripter, Area Tested, Assigned, Info, Test Status, Time Tested and Time Approved.
										Please note if the cursor is held over the 'i' in the Info column the user will be able to find out more information on that particular test. </P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="machines"></a><B>Machines Running</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Machines Running" link to display information about what tests are being run at that moment.
										Information will be provided on the location of the machine running the test (only if the details have been entered in the Machines section of the Admin/Preferences menu), name of the machine, test being ran, testset the test is being ran against, time the test was started and the time since the last verification.
										If no tests are being ran, then a message will let the user know this.
										</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->


		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="latest"></a><B>Latest Testing Status</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Latest Testing Status" link to display information about the current status for the latest release and build.
										Information includes the Release, Build, date the build was received (inputed into Tempest), number of TestSets used and the overall number of Tests in the TestSet(s).
										This pie chart gives the user a view at Build level and not at TestSet level. Therefore, the Build may include various TestSets and this chart gives the overall status on them.
										</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->


		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="signoff"></a><B>Sign Off Report</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Sign Off Report" link to display the information about the status of testing for a particular Release, Build and TestSet.
										The user selects the Release, Build and then TestSet to view. If the user wishes to change to a different Release, Build or TestSet, simply click on the underlined titles.
										The information is displayed in 3 methods. <BR>
										1) The pie charts show visually the status of the selected TestSet. The left pie chart displays the status of the testing in relation to the number of tests held in Tempest. Example : the percent of tests passed for a TestSet is 100%, however this chart will show that only 20 out of the 60 tests held in Tempest were ran and therefore show a percentage of 33.3% Tests passed. <BR>
										2) The table of information below the pie charts give the raw figures for the status of the tests and the number of tests used.
										<BR>
										3) The last table displays the test details on an individual test level. The user can view whether the test was Automated or Manual, the Test Name, Area Tested, Assigned, Test Status, Operating System, Info, Time Tested and Time Approved.
										</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="area"></a><B>Area Tested Report</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Area Tested Report" link to display the information about the areas tested for a particular Release, Build and TestSet.
										The user selects the Release, Build and then TestSet to view. If the user wishes to change to a different Release, Build or TestSet, simply click on the underlined titles.
										The user can view the Areas, the number of tests that test that Area, tests used in the TestSet for that Area and the percentage of the Area tested.
										<BR>
										This report would be useful if a new build only changed in a particular Area, and someone wanted to check that all tests were being used against testing that build.
										</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="requirement"></a><B>Requirements Report</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Requirements Report" link to display the information about the requirements for a particular Release, Build and TestSet.
										The user selects the Release, Build and then TestSet to view. If the user wishes to change to a different Release, Build or TestSet, simply click on the underlined titles.
										The user can view the Requirements that have been tested in the selected TestSet. The table contains the requirement name, test associated to it, test status, percentage of requirement the test covers and the total percentage of the requirement covered by the testset.
										</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="machinesreport"></a><B>Machines Report</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Machines Report" link to display the information about which machines have ran tests within a given set of dates.
										The user selects the dates they wish to see what machines have ran which tests. Once the dates have been validated, the system will display 2 tables.
										<BR>
										The first table displays the test name, machine name, machine location (only if the details have been entered in the Machines section of the Admin/Preferences menu), date/time test started (if it was an automated test only), date/time test finished and the duration of the test (if it was an automated test only).
										<BR>
										The second table gives an overall picture of the machines used to run the tests within the given timescale. It displays the machine name, how many tests were ran on this machine, overall time spent testing and the percentage of time that the machine was running tests.
										</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="testsummary"></a><B>Test Summary</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Test Summary" link to display information about the Tests.
										The user can view various bar graphs showing Status, Priority, Scripters, BA Owners, Areas Tested and Types of all the tests.
										This report is very useful for a general overview of the Tests.
										</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="testsummary"></a><B>Site Statistics Report</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "Site Statistics Report" link to display information about the user habits within Tempest.
										The user can view 4 bar graphs :
										<BR>
										Hits per page - this shows the pages that have been accessed since 1st November 2002 and how many times each page has been hit.
										<BR>
										Logins - displays the users that have logged into the system and how many times they have accessed Tempest since November 2002.
										<BR>
										Number of Logins in the current Month - shows how many times users have logged into Tempest in the month.
										<BR>
										Number of page hits in the current Month - shows how many hits to Tempest in the month.
										</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a></font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="allproject"></a><B>All Project Stats</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Reports" link displayed in the menu.
										Select the "All Project Stats" link to display information about all projects within Tempest.
										The user can view 3 line graphs :
										<BR>
										Page hits in the current month - this displays the number of pages hit for each individual project on each day of the month.
										<BR>
										Logins - displays the users that have logged into the system and how many times they have accessed Tempest since November 2002.
										<BR>
										Number of Logins in the current Month - shows how many times users have logged into Tempest in the month.
										<BR>
										Number of page hits in the current Month - shows how many hits to Tempest in the month.
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