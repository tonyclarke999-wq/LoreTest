<?php
include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();


print"<br>". NEWLINE;
print"<p class='help-page-title'>". lang_get('help_requirements') ."</p>". NEWLINE;


print"<base target='_self'>". NEWLINE;

print"<ul>". NEWLINE;
print"<li class='help-link'><a href='#req_overview'>". lang_get('help_req_overview') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#req_add'>". lang_get('help_req_add') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#req_view'>". lang_get('help_req_view') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#req_detail'>". lang_get('help_req_detail') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#req_add_version'>". lang_get('help_req_add_version') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#req_history'>". lang_get('help_req_history') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#req_req_assoc'>". lang_get('help_req_assoc') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#req_test_assoc'>". lang_get('help_req_test_assoc') ."</a>". NEWLINE;
print"</ul>". NEWLINE;


print"<table>";

print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

# HELP OVERVIEW
print"<tr>";
print"<td class='help-title'><a name='req_overview'>". lang_get('help_req_overview') ."</a></td>";
print"</tr>";

print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;RTH is designed to help manage requirements in several ways.  First, any requirement uploaded into RTH is stored under version control and the the system records the date, time, and person who uploaded the requirement.  Version control is crucial to managing files effectively.  Second, you can create assignments for each requirement version.  You can assign each version to a release or to an individual.   You can quickly find all requirements assigned to a particular release or person by using the filter funcionality on the main requirements page.  Simply filter for a release and all requirements assigned to the release appear.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;You can also associate certain meta-data to a requirement version.  Meta data is simply data that describes data.  There are several such fields related to requirements.  RTH allows users to associate the Document Type, Functionality, Area Covered, Priority to each requirement version.  These four fields are all meta-data which any RTH manager or administrator can control.  By clicking on the Manage hyperlink on the main menu, and selecting the appropriate Project, users can add values that are meaningful to their particular project in the fields described above.  The Release field is similar as the field is also populated with any release names added by clicking on the 'Release' hyperlink.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;Another advantage of storing requirements in RTH is that you can relate requirements to other requirements or relate requirements to tests.  This is useful if you want to run tests and see exactly which requirements you've tested and the percetage of the requirements covered.  Even if you store your requirements in another system, you can still take advantage of this reporting capability by simply storing a reference to a requirement within RTH.  For instance, you may store your requirements in a web based version control system or on a file server.  Simply, cut and paste the url or path to the requirement, link your tests, and you can run a series of tests and produce a real-time requirement coverage report.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;In some cases, requirements are not needed after the initial design and deployment of a product.  In many companies, requests from customers are logged and a ticketing system (change control tool, bug tracker, help desk ticketing system, or combination of the three) becomes the system of record.  The ticketing system may contain everything a developer needs to enhance the product. RTH contains a defect tracking system but can also link quite easily into any web based ticketing system.  See the help section on change control for more information.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ADD REQUIREMENT
print"<tr>";
print"<td class='help-title'><a name='req_add'>". lang_get('help_req_add') ."</a></td>";
print"</tr>". NEWLINE;

print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;RTH can store requirements in two different ways.  Click on the Requirements link on the main menu, and you'll notice two options for adding requirements on the sub-menu.  You can add a 'Requirement Record' or add a 'Requirement File'.  Deciding how to store requirements depends on the level of traceability your project requires and the types of requirements you're using.  If you need detailed test coverage reports, you may want to add record based requirements.  While record-based requirements offer greater traceability, they are more work to maintain.  Generally, the extra work isn't worth the effort unless you're developing something that would cause great harm if it failed.  NASA and the NYSE, for instance, might document each requirement as a record so that they can ensure that every requirement is adequately tested.  If your company is already using documents for their requirements and they don't require detailed traceability between test results and requirements, document based requirements may suffice.  Many companies have success using a combination of the two.  Using file based requirement to upload nuclear documents such as use cases or gui specifications, and using record based requirements for functional requirements.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;Click on the Add Requirement - Record or Add Requirement - File link and you will be taken to the Add Requirement form. The form includes the following fields:</td>". NEWLINE;
print"</tr>". NEWLINE;
# Requirement Name
print"<tr><td class='help-text'><b>Requirement Name:</b> The name of the Requirement. It is beneficial to decide on a naming convention for requirements.  Whether using RTH or a file server, a good naming convention will help users find requirements and help understand relationships between requirements.</td></tr>". NEWLINE;
# REQUIREMENT DETAIL
print"<tr><td class='help-text'><b>Requirement Detail:</b> This block of text is the body of the requirement and is stored under version control.  This field only appears when Add Requirement - Record is selected.  An example requirement might be: </td></tr>". NEWLINE;
print"<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;1.1.1 The system shall require the user to enter a username when logging in.</td></tr>". NEWLINE;
print"<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;1.1.2 The system shall not allow a username of less than 6 characters.</td></tr>". NEWLINE;
print"<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;1.1.3 The system shall not allow a user to enter a username of more than 20 characters.  etc.</td></tr>". NEWLINE;
# FILE UPLOAD
print"<tr><td class='help-text'><b>File Upload:</b> If adding a Requirement file, the file upload box appears.  Click the Browse button and navigate through the file system to the requirement you want to upload.  Double-click on the file and the path and file name will appear in the text box.  The system administrator can limit the size of uploaded files so file size is a consideration when uploading files.  The file is written to a file server and it is always a good idea to check with your system administrator to make sure the server is backed up nightly.</td></tr>". NEWLINE;
# REASON FOR CHANGE
print"<tr><td class='help-text'><b>Reason For Change:</b>An explanation of why the change is needed can be useful when looking back through the history of the requirement. This field is free form text.</td></tr>". NEWLINE;
# REQ VERSION
print"<tr><td class='help-text'><b>Version:</b> This is set to version 1.0 when creating a new requirement but a user can edit this field. It is suggested that users don't change this value unless migrating requirements from another system and the requirement version is already beyond version 1.0</td></tr>". NEWLINE;
# AREA COVERED
print"<tr><td class='help-text'><b>Area Covered:</b> The data in this field is populated by by each project. <a href='#warning_on_meta_data'>". lang_get('help_warning_on_meta_data')  ."</a></td></tr>". NEWLINE;
# DOC TYPE
print"<tr><td class='help-text'><b>Doc Type:</b> This data in this field is populated by by each project.  The complexity of one application or project may require detailed requirements to develop an application while another may not.  This field allow each project to specify the types of requirement documents required for the project.  One project may need only a few screen shots, while another may need Use Cases, System Requirements, Gui Specifications, etc.  This field is intended to give each project the flexibility they need in developing requirements.</td></tr>". NEWLINE;
# REQ STATUS
print"<tr><td class='help-text'><b>Status:</b> This field can be modified for each RTH instance. The default values are: New, Reviewed, Approved, Rejected, and Implemented</td></tr>". NEWLINE;
# REQ PRIORITY
print"<tr><td class='help-text'><b>Priority:</b> This field can be modified for the entire RTH instance by updating the requirement_get_priorities function within the requirements api. The default values are: High, Medium, and Low.</td></tr>". NEWLINE;
# ASSIGNED TO RELEASE
/* print"<tr><td class='help-text'><b>Assigned To Release:</b>This field is populated with all releases entered into the system that are not archived.  For more information on Releases and RTH, please see the help section on Adding/Editing a Release, Build, or Test Set.</td></tr>". NEWLINE; */
# ASSIGN TO
print"<tr><td class='help-text'><b>Assign To:</b>This dropdown contains all the users associated to your RTH project.  For more on adding users, return to the Help Index and select the section on Adding a User.</td></tr>". NEWLINE;
# FUNCTIONALITY
print"<tr><td class='help-text'><b>Functionality:</b>  The data in this multi-select list box is populated by by each project.  To select more than one value from this list box, hold down the Ctrl key and left mouse click on your selections.  The values in this field are project specific and are added or updated by anyone with manager rights to the project.  See the help section on Managing a Project for more information.<a href='#warning_on_meta_data'>". lang_get('help_warning_on_meta_data')  ."</a></td></tr>". NEWLINE;
# CHANGE REQUEST ID
/*
print"<tr><td class='help-text'><b>Change Request ID:</b> This field appears only when requested.  This is a text field that allows users to enter a change ticket number that becomes a hyperlink back to a change request system.  Linking each requirement back to a change ticket can help organizations track all changes (requirements, enhancements, bug fixes, etc) back to a single system.  RTH can easily link to any web based system.  Organizations are currently using this field to link to some popular open-source systems such as <a href='http://www.mantisbt.org' target='_blank'>Mantis</a> and <a href='http://www.bugzilla.org' target='_blank'>Bugzilla</a> as well as several commercial products.</td></tr>". NEWLINE;
*/

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# VIEW REQUIREMENTS
print"<tr>";
print"<td class='help-title'><a name='req_view'>". lang_get('help_req_view') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Click on the Requirements link on the main menu, and you'll be taken to the main Requirements page.  This page displays all the requirements loaded into the project.  The page is made up of several parts.  The top of the page contains the Requirements sub menu.  This should be fairly straght forward.  Next are a series of filters.  These filters are intended to help the user find a specific requirement or group of requirements.  Select a value from one or more of the list boxes in the filter form and click the Filter button.  This will limit the number of requirements returned to the page.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; The next section is the table header.  This section diplays the number of records showing and the total number of records returned, links to page numbers, export to csv functionality, and the column headers.  In addition to the filter functionality, clicking on a column header (ReqID, DocType, etc.) allows the user to sort on a specific column.  A user may wish to filter for a Doc Type of 'Use Case' and display 100 records per page.  This will limit the number of records reurned (visible at the top of the table, Showing 1 - 100 of 101).  After limiting the records by filtering, the user can then sort the data by clicking on any of the column headers.  An arrow will appear beside the column showing whether the column is sorted in ascending or descending order.  The page is sorted by the Requirement Name column by default.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; The next section of the page is the Requirement table which displays the actual requirement data.  Most of these columns are self-explanitory but the first two columns require some explanation.  The checkboxes down the left hand side of the table are intended to help users update multiple requirements at once.  This mass update functionality is the fastest way to update many requirements at once.  Simply filter for the requirements that need to be updated and click the 'Select All' checkbox at the bottom of the page.  Selecting this checkbox will check every record on the page.  You can now select a value from the list box at the bottom of the page and click Update.  This will take the user to a page where they can select from a list of values and update all the requirements selected with a chosen value.   Please be aware that the Select All checkbox at the bottom of the page will only appear if the user has JavaScript enabled on their browser.  If they don't have this enbabled, the user will have to select each checkbox manually.  The second column contains a unique Requirement ID.  Click on one of the ReqID links to view the Requirement in more detail.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# REQUIREMENT DETAIL
print"<tr>";
print"<td class='help-title'><a name='req_detail'>". lang_get('help_req_detail') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Click on Requirement ID (ReqID) link on the Requirements page to view the detail of a requirement.  The Requirement Detail page allows the user to work on a specific requirement.  This is where the user will add a new requirement version, update, delete, download, lock and unlock the requirement. This page also contains links that display the requirement version history, download a requirement file, and relate one requirement to another.  The bottom of the page contains four 'tabs' that allow the user to view any related requirements, tests, releases, or discussions related to the parent requirement.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ADD A NEW VERSION
print"<tr>";
print"<td class='help-title'><a name='req_add_version'>". lang_get('help_req_add_version') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; From the Requirement Detail page, click on the Add New Version button to add a new version of a requirement.  If the requirement is file based, you'll be prompted to browse to the location of the new file version.  If it's record based, you can edit the text in the Detail section.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# VIEW REQUIREMENT HISTORY
print"<tr>";
print"<td class='help-title'><a name='req_history'>". lang_get('help_req_history') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; From the Requirement Detail page, click on the 'History' hyperlink to view the version history of a requirement.  The Requirement History page will display some basic information about each version.  You'll see the date the version was created, who created it, the status, etc.  You can click the 'View' hyperlink to view all the detail of a particular version.  If you click on the 'View' link, you will notice that the buttons and tabs normally at the bottom of the page are not present.  The buttons are absent so that users cannot update older versions of a requirement after a newer version has been created.  This sometimes confuses users but it is a common feature of any version control system.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ASSOCIATING REQUIREMENTS TO OTHER REQUIREMENTS
print"<tr>";
print"<td class='help-title'><a name='req_req_assoc'>". lang_get('help_req_assoc') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; There are two ways to associate requirements to other requirements.  From the Requirement Detail page, you can click on the either the 'File' or 'Record' hyperlink to create a new requirement.  Using one of these links to create a requirement will automatically relate the new requirement back to the parent (the requirement you were viewing when you clicked the hyperlink).   You can also click on the 'Requirements Assoc' tab at the bottom of the page and click the 'Edit Children' link.  This method of association allows you to simultaneously relate many requirements back to the parent.  After clicking on the 'Edit Children' link, you will see a list of all the requirements in the project and can filter for those that you want to relate.  Filter for the appropriate requirements and check (or uncheck if you're removing an association) the checkbox beside the requirements you'd like to relate.  Click the 'Update' button and you will return to the requirement detail page where you will see all the associated requiements under the 'Requirement Assoc' tab.  As mentioned above, you can remove any association by selecting 'Edit Children' or you can click the 'Delete' hyperlink beside the associated requirement.  Hitting the 'Delete' link will not delete the requirement but merely delete the association between the requirements.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ASSOCIATING REQUIREMENTS TO TESTS
print"<tr>";
print"<td class='help-title'><a name='req_test_assoc'>". lang_get('help_req_test_assoc') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Like the functionality allowing relationships between requirements, there are two ways to relate a test to a requirement.  From the Requirement Detail page, you can click the 'Test' hyperlink to create a new test.  Creating a test in this manner will automatically relate the new test back to the parent requirement.  You can also click on the 'Test Assoc' tab at the bottom of the page and click the 'Edit Associations' link.  After clicking on the 'Edit Associations link, you will see a list of all tests in the project.  Filter for the tests that cover the parent requirement and then check the appropriate check boxes beside the tests that cover the requirement.  You may also update the percentage of the requirement covered by the test by entering a number in the '% covered by Test' column.  The reason for this is that a test may execute only some of the steps necessary to completely test the requirement.  After selecting the tests and entering the percent covered, click the 'Edit' button and you will return the the Requirement Detail page and see the associated tests under the 'Test Assoc' tab.  You can also create the requirement to test association from the Test Detail page.  See the help section on Tests to find the details.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# META-DATA WARNING
print"<tr>". NEWLINE;
print"<td class='help-title'><a name='warning_on_meta_data'>". lang_get('help_meta_data_warning_title') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;". lang_get('help_meta_data_warning_text')."</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;
util_add_spacer();

print"</table>";

?>