<?php

include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();

?>

<H2><FONT COLOR="<?php echo $H2COLOR;?>">Help: Documents Uploading/Downloading</FONT></H2>

<base target="_top">
<!--This is the code that allows navigation by links through the page. The menu for the page.-->
<TD VALIGN="top">
<P ALIGN="left"><font FACE="arial" SIZE="2"><B>Select a link below for more information.</B></FONT><BR><BR>
<a HREF="#purpose"><font FACE="arial" SIZE="2"><B>Purpose</B></FONT></a><BR>
<a HREF="#requirement"><font FACE="arial" SIZE="2"><B>Requirement Documents</B></FONT></a><BR>
<a HREF="#test"><font FACE="arial" SIZE="2"><B>Test Documents</B></FONT></a><BR><BR>
<font FACE="arial" SIZE="2"><B>Back To:&#32;</B><a HREF="help_index.php"><font FACE="arial" SIZE="2"><B>Help Index</B></FONT></a><BR><BR>

<TABLE CELLSPACING=0 CELLPADDING=5 BORDER="0" WIDTH=100%>
	<TR>

	<TD VALIGN="top">

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="purpose"></a><B>Purpose</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">This section will help clarify the difference between Uploading and Downloading Documents when dealing with Requirements and Tests.
									The help section is divided by a Requirement Section and a Test Section.
									Under each will be an explanation of how each process is done.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="requirement"></a><U><B>Requirement Documents</U></B></FONT><BR>

			<BLOCKQUOTE><a HREF="#readingreq"><font FACE="arial" SIZE="2"><B>Reading A Requirement Document</B></FONT></a><BR>
			<a HREF="#uploadingreq"><font FACE="arial" SIZE="2"><B>Uploading Documents</B></FONT></a><BR>
			<a HREF="#showlog"><font FACE="arial" SIZE="2"><B>Show Log Of Documents</B></FONT></a>
			<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
			<!--Above code is link to go back to the top of the screen.--></BLOCKQUOTE>

				<BLOCKQUOTE><BLOCKQUOTE><P ALIGN="left"><font FACE="arial" SIZE="2"><a name="readingreq"></a><B>Reading A Requirement Document</B></FONT><BR>
				<FONT FACE="arial" SIZE="2">To read the document that was uploaded by the user listed in the "Uploaded By" column, please select the "Requirements" link located in the main toolbar at the top of the screen.
									Select the "Docs" link listed under the Req Docs column.
									Then choose either the Download or View link.
									Download will save it to the hard drive of the PC and view will allow the user to read the document.
									Please note if the "View" link is chosen the document is not saved to the local hard drive.
									 </P>
				<h5><font face="Arial" size="1"><a href="#requirement">Back to Requirement Documents</a> </font></h5>
				<!--Above code is link to go back to the top of the screen.-->

				<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="uploadingreq"></a><B>Uploading Documents</B></FONT><BR>
				<FONT FACE="arial" SIZE="2">To Upload a Document select the "Requirements" link located in the main toolbar located at the top of the screen.
									Select the "Docs" link listed under the Req Docs column.
									To add a <B>New Version</B> of the Document either type the path in the text box provided or browse using the button provided.
									Verify the version is correct and then select the "Upload" button.
									The page will regenerate and populate a new link to the document just added.</P>
				<h5><font face="Arial" size="1"><a href="#requirement">Back to Requirement Documents</a> </font></h5>
				<!--Above code is link to go back to the top of the screen.-->

				<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="showlog"></a><B>Show Log Of Documents</B></FONT><BR>
				<FONT FACE="arial" SIZE="2">To view the history of a particular document, first display the Requirements page by selecting the "Requirement" link located in the main toolbar at the top of the browser.
									Select the "Docs" link listed under the Req Docs column.
									Next select the "Show Log" link in the Show Log column.
									A table will populate with the a complete history of the document.</P>
				<h5><font face="Arial" size="1"><a href="#requirement">Back to Requirement Documents</a> </font></h5>
				<!--Above code is link to go back to the top of the screen.--></BLOCKQUOTE></BLOCKQUOTE>


		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="test"></a><U><B>Test Documents</U></B></FONT><BR>

				<BLOCKQUOTE><a HREF="#test2"><font FACE="arial" SIZE="2"><B>Test Document Table</B></FONT></a><BR>
				<a HREF="#readingreq2"><font FACE="arial" SIZE="2"><B>Reading A Requirement Document</B></FONT></a><BR>
				<a HREF="#uploadingreq2"><font FACE="arial" SIZE="2"><B>Uploading Documents</B></FONT></a><BR>
				<a HREF="#showlog2"><font FACE="arial" SIZE="2"><B>Show Log Of Documents</B></FONT></a>
				<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
				<!--Above code is link to go back to the top of the screen.--></BLOCKQUOTE>

					<BLOCKQUOTE><BLOCKQUOTE><P ALIGN="left"><font FACE="arial" SIZE="2"><a name="test2"></a><B>Test Documents Table</B></FONT><BR>
					<FONT FACE="arial" SIZE="2">The main form lists the names of all the Documents in the system, such as informational fields, and several other links.
						The Doc Type field lists the type of document.
						The Requirement may be a rules document, use case document, or contain functional specifications.
						This Requirement field gives you the ability to search for a specific file type.
						The Version field will provide the version of the Requirement Document most recently uploaded into the system.
						The Area Covered field is again designed to let the user filter out requirement documents that are not of interest.
						This field typically lists areas of functionality in the application.</P>
					<h5><font face="Arial" size="1"><a href="#test">Back to Test Documents</a> </font></h5>
					<!--Above code is link to go back to the top of the screen.-->


					<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="readingreq2"></a><B>Reading A Test Document</B></FONT><BR>
					<FONT FACE="arial" SIZE="2">To read a Document that was uploaded by the user listed in the "Uploaded By" column, please select the "Test" link located in the main toolbar located at the top of the screen.
									Select the "Docs" link listed under the Req Docs column.
									Then select the Download or View links.
									Download will save it to the hard drive of the PC and View will allow the user to read the document.
									Please note if the "View" link is chosen the document is not saved to the local hard drive.
									</P>
					<h5><font face="Arial" size="1"><a href="#test">Back to Test Documents</a> </font></h5>
					<!--Above code is link to go back to the top of the screen.-->

				<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="uploadingreq2"></a><B>Uploading Documents</B></FONT><BR>
				<FONT FACE="arial" SIZE="2">To Upload a Document select the "Test" link located in the main toolbar located at the top of the screen.
									Select the "Docs" link listed under the Req Docs column.
									To add a <B>New Document</B> either type the path or browse and select the document to add it in the box that appears.
									Verify the version is correct and then select the "Upload" button.
									The page will regenerate and populate a new link to the document just added.</P>
				<h5><font face="Arial" size="1"><a href="#test">Back to Test Documents</a> </font></h5>
				<!--Above code is link to go back to the top of the screen.-->

				<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="showlog2"></a><B>Show Log Of Documents</B></FONT><BR>
				<FONT FACE="arial" SIZE="2">To view what users have Downloaded, Uploaded or Viewed a particular document first display the Requirements page by selecting the "Tests" link located in the main toolbar at the top of the browser.
									Select the "Docs" link listed under the Req Docs column.
									Next select the "Show Log" link in the Show Log column.
									A table will populate with the a complete history of the documents.</P>
				<h5><font face="Arial" size="1"><a href="#test">Back to Test Documents</a> </font></h5>
				<!--Above code is link to go back to the top of the screen.--></BLOCKQUOTE></BLOCKQUOTE>


		<BR>
	</TR>
</TABLE>

<hr>
<p><a href='help_index.php'>Return To Index</a></p>

</BODY>
</HTML>
