<?php
include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();


print"<br>". NEWLINE;
print"<p class='help-page-title'>". lang_get('help_releases') ."</p>". NEWLINE;

print"<base target='_self'>". NEWLINE;


print"<ul>". NEWLINE;
print"<li class='help-link'><a href='#release_overview'>". lang_get('help_release_overview') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#release_add'>". lang_get('help_add_release') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#build_add'>". lang_get('help_add_build') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#testset_add'>". lang_get('help_add_testset') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#testset_edit'>". lang_get('help_edit_testset') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#testset_view'>". lang_get('help_view_testset') ."</a>". NEWLINE;
print"</ul>". NEWLINE;

print"<table>";

print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

# RELEASE OVERVIEW
print"<tr>";
print"<td class='help-title'><a name='release_overview'>". lang_get('help_release_overview') ."</a></td>";
print"</tr>";

print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;  By clicking on the 'Release' hyperlink on the main menu, users can add Releases and Builds to a project in RTH.  Requirements and Defects can be assigned to any Release added to a project.  This feature is designed to help with Scope/Release management.  By assigning Requirements or Defects to a release, it is easy to view the workload for a given release.  The results of all test execution within RTH are related back to a release, build, and test set.  Without adding a release and build to RTH, you cannot run any tests.  If you're using Ant or some other means of automating a build, tying the build process into RTH should not be much trouble.  Simply write a record to the Release and Build tables in RTH with each build.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;Before explaining how to add or edit a Test Set, you may be hoping that we'll explain its meaning. A Test Set is a group of tests planned to run against a specific build and release.  If there is a build that only effects a specific module of your application, you may want to run only your tests that cover this area of the application. Hence, the test set is born.  As mentioned above, a test set must be associated with a release and build, therefore, a description of how to add a release and build will follow.  Any user can add a release, build, or test set but only those with Manager rights or who have Delete rights can delete them.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ADD RELEASE
print"<tr>";
print"<td class='help-title'><a name='release_add'>". lang_get('help_add_release') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; To add a release to a project, click on the 'Release' link on the main menu.  This will bring up the 'Release Page'.  This page enables a user to add a new Release to the system.  The top of the page contains the 'Add New Release' form.  Simply add a Release Name and an optional Description and hit the 'Add' button at the bottom of the form.  The new release will then appear below the 'Add Release' form in a table.  If you need to edit or delete the Release, simply click on the 'Edit' or 'Delete' link.  The 'Delete' link will only appear to users with Manager rights or those who have been granted 'Delete' rights on the Manage User page.  Keep in mind that deleting a release will delete all Builds, Test Sets, and Test Results related to that release.  Simply click on the release name to navigate to the 'Build Page'.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ADD BUILD
print"<tr>";
print"<td class='help-title'><a name='build_add'>". lang_get('help_add_build') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Once you've added a Release, the 'Build Page' will look very familiar. Simply type the necessary information into the 'Add Build to Release Form' and click the 'Add' button at the bottom of the form.  Like the release page the new build will appear below the Add Build form.  Click on the build name hyperlink and the 'Test Set Page' will appear.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ADD TESTSET
print"<tr>";
print"<td class='help-title'><a name='testset_add'>". lang_get('help_add_testset') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Adding a test set is very similar to adding a Release and Build with one additional step described below. Start by entering the Test Set Name in the 'Add Testset to Build' form.   Again, clicking the 'Add' button will make the new Test Set appear in the table below the form.  After adding a new Test Set, you will notice that a hyperlink will appear in second column of the table, 'Edit/Add Tests'.  The link will read 'Add Tests' after the initial creating of the Test Set.  Click on the 'Add Tests' hyperlink and you will be taken to another page where you can decide what tests should be executed as part of that Test Set.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;  The 'Add Tests' page will allow you filter for a group of tests and then select them by clicking the check box beside each test you want to run.  Similar to other tables throughout the application, you can also check the 'Select All' check box at the bottom of the table to select all the tests on the page.  Once you select the appropriate tests, click the 'Create' button at the bottom of the page.  This will add the selected tests to the Test Set and send out an e-mail to all users who have 'E-mail on TestSet' selected in the user preferences.  You can also add tests to a Test Set by clicking the 'Copy' hyperlink on the main Test Set page.  The copy functionality allows a user to copy tests from a previous test set.  In addition, the user can add tests to the test set based on the status of the test from a prior test run.  For instance, you may want to run all the test from the previous Test Set that failed or were not run.  The copy functionality will allow this.  After clicking the copy link, you will be promted to select test from a prior Release, Build, and Test Set.  Once you've selected the Test Set you want to copy test from, you see a list of tests.  This time however, the status of the tests will appear and you can copy all the same tests from a previous test run or only those tests with a certain status.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; You may also notice a form at the bottom of the Test Set page.  This form allows a user to upload a Test Plan document.  The Test Sets that are added to a project are seen as part of the overall Test Plan for a given release and build.  Each file uploaded using this form is stored under version control.  Please see the help section on uploading and downloading files for more details.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# EDIT TESTSET
print"<tr>";
print"<td class='help-title'><a name='testset_edit'>". lang_get('help_edit_testset') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; After adding tests to a Test Set the hyperlink in the 'Edit/Add Tests' column of the Test Set table will read 'Edit Testset'.  Once you've added tests to the test set, you can click on this link to modify your selection of tests.  This form should look quite familiar by now.  The edit page allows the user to filter for particular tests and select or de-select the appropriate tests.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; The 'Edit' and 'Delete' link to the far right hand side of the Test Set table allow the user to edit the Test Set Name and Description or to Delete the Test Set.  Deleting a Test Set will delete all associated tests and test results.  </td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# VIEW TESTSET
print"<tr>";
print"<td class='help-title'><a name='testset_view'>". lang_get('help_view_testset') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; After creating and adding tests to a Test Set, you will navigate to the Results Page to view the test results for that Test Set.  Click on the 'Results' link on the main menu to view the test results for a given Release, Build, and Test Set.  The test results section is described in much greater detail in the help section 'Test Results'.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();


print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;
util_add_spacer();

print"</table>";

?>