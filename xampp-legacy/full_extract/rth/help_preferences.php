<?php

include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();

?>

<H2><FONT COLOR="<?php echo $H2COLOR;?>">Help: Preferences</FONT></H2>

<base target="_top">
<!--This is the code that allows navigation by links through the page. The menu for the page.-->
<TD VALIGN="top">
<P ALIGN="left"><font FACE="arial" SIZE="2"><B>Select a link below for more information.</B></FONT><BR><BR>
<a HREF="#areatested"><font FACE="arial" SIZE="2"><B>Area Tested</B></FONT></a><BR>
<a HREF="#environment"><font FACE="arial" SIZE="2"><B>Environment</B></FONT></a><BR>
<a HREF="#machines"><font FACE="arial" SIZE="2"><B>Machines</B></FONT></a><BR>
<a HREF="#testtype"><font FACE="arial" SIZE="2"><B>Test Type</B></FONT></a><BR><BR>
<font FACE="arial" SIZE="2"><B>Back To:&#32;</B><a HREF="help_index.php"><font FACE="arial" SIZE="2"><B>Help Index</B></FONT></a><BR><BR>

<TD VALIGN="top">
<TABLE CELLSPACING=0 CELLPADDING=5 BORDER="0" WIDTH=100%>
	<TR>
	<TD VALIGN="top">
		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="areatested"></a><B>Area Tested</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Admin" link located in the main toolbar located at the top of the Tempest window.
										Next select the "Preferences" link located above the table populated.
										Select the "Area Tested" link to display the data in the table.
										Here, if the "Edit" link is selected the Area Tested Name can be edited.
										Once the Save button is clicked the user is brought back to the Area Tested display screen.
										If the user wishes to Delete an entry do so by selecting the "Delete" link and choosing 'yes' when asked if positive about deletion.
										To Add a New Area Tested simply type the name into the Add box and click the "Add" button.</P>
	<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
	<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="environment"></a><B>Environment</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Admin" link located in the main toolbar located at the top of the Tempest window.
										Next select the "Preferences" link located above the table populated.
										Select the "Environment" link to display the data in the table.
										Here, if the "Edit" link is selected the Environment Name can be edited.
										Once the Save button is clicked the user is brought back to the Environment display screen.
										If the user wishes to Delete an entry, do so by selecting the "Delete" link and choosing 'yes' when asked if positive about deletion.
										To Add a New Environment simply type the name into the Add box and click the "Add" button. </P>
	<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
	<!--Above code is link to go back to the top of the screen.-->

	<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="machines"></a><B>Machines</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Admin" link located in the main toolbar located at the top of the Tempest window.
										Next select the "Preferences" link located above the table populated.
										Select the "Machines" link to display the data in the table.
										Here, if the "Edit" link is selected the Machine Name, Machine Location and Machine IPAddress can be edited.
										Once the Save button is clicked the user is brought back to the Machines display screen.
										If the user wishes to Delete an entry, do so by selecting the "Delete" link and choosing 'yes' when asked if positive about deletion.
										To Add a New Machine simply type the name into the Add box and click the "Add" button. </P>
	<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
	<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="testtype"></a><B>Test Type</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">Select the "Admin" link located in the main toolbar located at the top of the Tempest window.
										Next select the "Preferences" link located above the table populated.
										Select the "Test Type" link to display the data in the table.
										Here, if the "Edit" link is selected the Test Type Name can be edited.
										Once the Save button is clicked the user is brought back to the Test Type display screen.
										If the user wishes to Delete an entry do so by selecting the "Delete" link and choosing 'yes' when asked if positive about deletion.
										To Add a New Test Type simply type the name into the Add box and click the "Add" button. </P>
	<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
	<!--Above code is link to go back to the top of the screen.-->

		<BR>
	</TR>
</TABLE>

<hr>
<p><a href='help_index.php'>Return To Index</a></p>

</BODY>
</HTML>