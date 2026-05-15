<?php
include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();


print"<br>". NEWLINE;
print"<p class='help-page-title'>". lang_get('help_manage_proj') ."</p>". NEWLINE;


print"<base target='_self'>". NEWLINE;


print"<ul>". NEWLINE;
print"<li class='help-link'><a href='#project_overview'>". lang_get('help_project_overview') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#project_add'>". lang_get('help_project_add') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#project_prefs'>". lang_get('help_project_prefs') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#project_users'>". lang_get('help_project_users') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#project_reqs'>". lang_get('help_project_reqs') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#project_tests'>". lang_get('help_project_tests') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#project_defects'>". lang_get('help_project_defects') ."</a>". NEWLINE;
print"</ul>". NEWLINE;

print"<table>";

print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

# HELP OVERVIEW
print"<tr>";
print"<td class='help-title'><a name='project_overview'>". lang_get('help_project_overview') ."</a></td>";
print"</tr>";

print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;After installing RTH, you'll notice that the system has a default project called \"DEMO\".  Each page you view will have \"DEMO\" in the title and the list box on the top right-hand corner of the page will list only DEMO.  If you had more than one project in the system, you would see all the projects listed and you would simply select the project you wanted to view by choosing it from the list.  This is how you would navigate between different projects in the system.  If you are testing software, it may be best to think of a project as the application that you're testing.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;Before describing how to add a new project or manage a project, you may want to know how to rename the DEMO project.  This is quite simple.  Just click on the \"Manage\" link on the main menu and click on the DEMO project name.   Change \"DEMO\" to the name of your application or project and the project will be renamed.  It should be noted that editing the project name will also rename the directories used for file upload.  The direcory paths are stored in the project table (req_upload_path, test_upload_path, test_run_upload_path, and test_plan_upload_path) and on the file server and the system uses these fields to determine where to upload documents.  Finally, you must alter the path on the server to match the paths in the database.</td>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;As mentioned in the installation instructions, you may not want to keep the rth_file_upload directory in the document root of your web server.  If you decide to move the directory, you will want to update the file paths in the database and change your FILE_UPLOAD_PATH in properties_inc.php.  When creating a new project, rth will read the FILE_UPLOAD_PATH and attempt to add four directories ([project_name]_req_docs, [project_name]_test_docs, [project_name]_test_run_docs, and [project_name]_test_plan_docs) in the directory specified in the FILE_UPLOAD_PATH.  This means that your web server will need the appropriate permissions to create directories in the FILE_UPLOAD_PATH directory.  If you've set this up correctly, you will never have to alter path names in the database as described above.  You will be able to add a project through the front-end of the application and everthing will be done for you.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ADD PROJECT
print"<tr>";
print"<td class='help-title'><a name='project_add'>". lang_get('help_project_add') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;After deciding whether or not to rename the DEMO project, you may want add a new project.  Click on the \"Manage\" link on the main menu and select \"Add Project\" on the project sub-menu on the Manage Project page.  Here you will be prompted to enter the Project Name, Description, and Status.  There are a number of checkboxes below these fields that may require some explanation.  The \"Custom\" checkboxes can be renamed in the /lang/strings_english.txt file.  Any Custom field selected on this page will appear on the test results page.  This is intended to allow you to report on a custom field in the test results section.  If you're testing software realted to cars, for instance, you may want to modify custom fields to read \"Make\" and \"Model\" and have automated scripts write the Make and Model to the rth database while testing.  The \"Memory Stats\" checkbox will display memory statistics on the test results page.  The WinRunner library included with the rth package includes an add on that will allow an automated script to capture the system Memory, Swap space, Page files, etc.  This can be used to track memory issues during testing.  The \"Test Input\" checkbox will alter the standard test case template within RTH.  Similar to the Memory Stats checkbox, the \"Window\" and \"Object\" checkboxes may be useful if you're using automation.  One of the WinRunner functions that comes with RTH includes an input for the Window and Object under test.  This may be useful if you have a distributed test team and the group reviewing results is different than the group writing or running the automated tests.  Debugging problems can be easier if the automation is writing out the Window and Object it's testing when writing a  verification to RTH.  By default the standard test case in RTH contains three main fields, Action, Test Inputs, and Expected Result.  Uncheck the \"View Test Inputs\" checkbox, and the default RTH test case will not contain the additional \"Test Inputs\"field in the test case template.  After making your selections, click the Submit button.  You're now ready to configure the project.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# EDIT PROJECT PREFS
print"<tr>";
print"<td class='help-title'><a name='project_prefs'>". lang_get('help_project_prefs') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;After creating the project, you're now ready to set some other project preferences.  You can edit these project preferences from the Manage Project page.  Simply click on the \"Manage\" link on the main menu and then click the hyperlink that displays your project name.  Here you can change the project name, the view preferences described above, enter any project news, and add users.  As the \"View/Hide Columns\" was described above, we'll move on the the \"News\" form.  Here you can enter any news related to the project.  This information might contain hyperlinks to your application, release dates, links to other testing tools.  It's really intended for anything you want all RTH users to know.  Whatever is entered on this form will appear on the RTH home page when a user logs into the application.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;Below the News form is the \"Add Users Form.\"  Before adding a user to your project, a user must first be added to the system by and RTH Administrator.  The RTH Admin can decide a users default permission and add them to a project but in cases when a user is not part of your project, this is where you can add them.  Any user with Manager rights to a specific project, can add other users to that project.  Simply select the user or users you want to add to the project, choose the appropriate permissions, their user rights, and click submit.  The Delete Rights preference will give the user the ability to delete releases, builds, test sets, and test results from the system.  Leave this checkbox unchecked if you do not want the selected users to have the ability to delete this data.  The Email Testset checkbox allows an RTH Manager to decide if a user should receive and email when a new test set is created.  The manager may want to know that users are receiving an email when a test set is created so they don't have to notify the users that it's time to begin testing.  Check the Email Discussions checkbox if you want a user to receive an email whenever a user adds a discussion forum to a requirement.  The BA Owner and QA Owner fields define the users role in testing.  The BA Owner field is intended to designate the Busines Analyst responsible or related to a particular test.  The QA Owner designates the Quality Assurance engineer responsible for a test.  Any user listed as a BA Owner will appear in the BA Owner list box on the Test and Test Results pages.  Likewise, any user listed as a QA Owner will appear in the QA Owner list boxes.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;There are three levels of permissions in RTH.  A user may be a designated \"User,\" \"Developer,\" or \"Manager\" rights.  User rights give the user the ability to add and update requirements, tests, releases, test results, and defects within the project.  By giving a user Developer rights, the user can be assigned as the developer to work on a defect.  Manager rights give the user the ability to manage other users in the system.  The Manager can add and remove users and assign the user permissions to any user in the project.  In addition, an RTH Admin can control any user in the system.  The RTH Administrator is assigned when adding a user to the system, not when adding a user to a project.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# PREFS - USERS
print"<tr>";
print"<td class='help-title'><a name='project_users'>". lang_get('help_project_users') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Detail coming soon.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# PREFS - REQUIREMENTS
print"<tr>";
print"<td class='help-title'><a name='project_reqs'>". lang_get('help_project_reqs') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Detail coming soon.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# PREFS TESTS
print"<tr>";
print"<td class='help-title'><a name='project_tests'>". lang_get('help_project_tests') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Detail coming soon.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# PREFS DEFECTS
print"<tr>";
print"<td class='help-title'><a name='project_defects'>". lang_get('help_project_defects') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Detail coming soon.</td>". NEWLINE;
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