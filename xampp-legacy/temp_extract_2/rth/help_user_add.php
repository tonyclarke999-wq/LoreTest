<?php
include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();


print"<br>". NEWLINE;
print"<p class='help-page-title'>Help: Adding A User</p>". NEWLINE;

print"<base target='_self'>". NEWLINE;


print"<ul>". NEWLINE;
print"<li class='help-link'><a href='#user_overview'>Overview</a>". NEWLINE;
print"<li class='help-link'><a href='#user_add'>Adding A User</a>". NEWLINE;
print"<li class='help-link'><a href='#user_edit'>Editing A User</a>". NEWLINE;
print"<li class='help-link'><a href='#user_delete'>Deleting A User</a>". NEWLINE;
print"<li class='help-link'><a href='#user_email'>User Email</a>". NEWLINE;
print"</ul>". NEWLINE;

print"<table>";


print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;



print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;


util_add_spacer();

# USER OVERVIEW
print"<tr>".NEWLINE;
print"<td class='help-title'><a name='user_overview'>Overview</a></td>";
print"</tr>".NEWLINE;
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; The purpose of the User section of rth is to allow certain permissions to particular users. Only users with administrative rights can add a user to the rth database.  When adding a user, a default project must be selected.  This is the project that will first appear to the user when they log into the system for the first time.  Once a user is added to the system, the user can be associated to other projects by an administrator or anyone with \"Manager\" permissions to the specific project.  The administrator or manager can define a role for the user and set some of the users email preferences.</td>".NEWLINE;
print"</tr>".NEWLINE;

# USER ADD
print"<tr>".NEWLINE;
print"<td class='help-title'><a name='user_add'>Overview</a></td>";
print"</tr>".NEWLINE;
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; As mentioned above, only an administrator can add a user to rth.  The add user form has several selections which will be described here.  The \"Username,\" \"Firt Name,\" \"Last Name,\" etc. fields are fairly self-explanatory.  The system will not allow two users with the same username to be added to he system.  By checking the \"RTH Administrator\" checkbox, you are giving the new user rights as an rth admin.  When adding a new user, the Admin must decide what projects the user will have rights to and decide on a default project.  The rights that the admin grants when creating the user will apply to all the projects selected in the \"Add to Project\" listbox.   If an admin wishes to grant different rights to different projects, they must add the user to one project with the appropriate permissions and then add them to other projects in the \"Manage Project\" page to give the user the correct permissions to other projects.  The user should only be added to the system one time using the \"Add New User\" form.  After that, the admin or manager will simply associate the user to other projects using the \"Add User to Project\" form on the manage project page. </td>".NEWLINE;
print"</tr>".NEWLINE;
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; The \"Add New User\" form has a series of options that will define the users role, permissions, and preferences in the system.  The \"Delete Rights\" checkbox will give the user rights to delete Releases, Builds, Testsets, Test Results, etc. from the system.  The delete link will always appear to users with Admin or Manager roles to a project but the ability to delete will be disabled for every other user unless the \"Delete Rights\" is checked. The \"Email Testset\" checkbox will determine whether a user is sent an email when a new Testset is created.  Maybe you have a distributed test team and you want to make sure all the users know immediately when you're ready to begin testing.  The \"Email Discussions\" checkbox will send the user an email when someone posts a discussion to a requirement.  This is intended to let the user know as soon as someone posts a discussions to a requirement.  \"QA Owner\" and \"BA Owner\" are used to differentiate peoples roles.  If a user is listed as a \"BA Owner,\" they will appear in the BA Owner listbox throughout the application.  There is one caveat.  The user will only appear in the list box if they are assigned to an item on that page.  For instance, if you navigate to the Tests Page and the user is listed as a BA Owner but that user is not assigned as a BA Owner to any tests, they will not appear in the listbox.</td>".NEWLINE;
print"</tr>".NEWLINE;


?>


<TABLE CELLSPACING=0 CELLPADDING=5 BORDER="0" WIDTH=100%>
	<TR>

	<TD VALIGN="top">

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="overview"></a><B>Purpose</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">The purpose of the User section of rth is to allow certain permissions to particular users.
										Only administration can add a user.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="adding"></a><B>Adding A User</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">To add a User first navigate to the "User Page" by selecting the "User" link located in the main toolbar at the top of the browser.
										Next select the "Add User" link generated above the table.
										The table that is populated asks for the New User's User Name, Password, First Name, Last Name, Title, E-mail, Phone, Extension, Office, and Department.
										Please fill out information accordingly.
										The field provided for the User's Rights please select accordingly from the drop down menu provided.
										When all the appropriate information has been filled out select the "Add" button and the user will be returned to the refreshed "User Page."</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="filtering"></a><B>Filtering By User</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">To search for a specific User in Tempest navigate to the "User Page" by selecting the "User" link provided in the main toolbar at the top of the browser.
										In the Filter Dialog box select the First Name, Last Name, User Rights or Office from the drop down box.
										To start the search select the "Filter" button.
										The screen will regenerate and display the information the user is seeking.</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="editing"></a><B>Editing User</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">To Edit a User already entered in Tempest please navigate to the "User Page" by selecting the "User" link provided in the main toolbar at the top of the browser.
										Scroll down the screen to the User to be edited and select the "Edit" link.
										Edit the User accordingly and click the "Save" button.
										The screen will refresh and return the user to the "User Page."</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->

		<P ALIGN="left"><font FACE="arial" SIZE="2"><a name="deleting"></a><B>Deleting User</B></FONT><BR>
		<FONT FACE="arial" SIZE="2">To Delete a User already entered in Tempest please navigate to the "User Page" by selecting the "User" link provided in the main toolbar at the top of the browser.
										Scroll down the screen to the User to be Deleted and select the "Delete" link.
										Delete the User and the screen will refresh and return focus to the "User Page."</P>
		<h5><font face="Arial" size="1"><a href="#top">Back to Top</a> </font></h5>
		<!--Above code is link to go back to the top of the screen.-->


		<BR>
	</TR>

	
</TABLE>

<hr>
<p><a href='help_index.php'>Return To Index</a></p>

</BODY>
</HTML>