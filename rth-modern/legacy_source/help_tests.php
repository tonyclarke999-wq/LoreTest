<?php
include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();


print"<br>". NEWLINE;
print"<p class='help-page-title'>". lang_get('help_tests') ."</p>". NEWLINE;


print"<base target='_self'>". NEWLINE;


print"<ul>". NEWLINE;
print"<li class='help-link'><a href='#test_overview'>". lang_get('help_test_overview') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#test_add'>". lang_get('help_test_add') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#test_view'>". lang_get('help_test_view') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#test_detail'>". lang_get('help_test_detail') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#test_add_steps'>". lang_get('help_test_add_steps') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#test_associate_reqs'>". lang_get('help_test_associate_req') ."</a>". NEWLINE;
print"</ul>". NEWLINE;

print"<table>";

print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

# HELP OVERVIEW
print"<tr>";
print"<td class='help-title'><a name='test_overview'>". lang_get('help_test_overview') ."</a></td>";
print"</tr>";

print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;The test module within RTH is the heart of the system and relates in some way to every other module in the system (requirements, releases, test execution, and defects).  A thorough understanding of the test module is important to a successful implementation of RTH, although the modules within RTH can be used independently.  You can, for instance, use only the defect tracking module without using the functionality within the test module.  The level of integration is up to you, but it's best if you have a thorough understanding of both the Test and Test Results module if you want to use RTH to monitor and report on testing.  There is a seperate help section on Test Results but the lines between Test and Test Results may blur at times so we strongly recommend that you read both sections.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;The testing module is designed to foster a distributed or localized test team.  Many of the fields related to tests may not be needed if you have a small local test team, but we wanted to include enough flexibility to support a distributed team.  The 'BA Owner' and 'QA Owner' fields, for instance, assume that there is a division of labor between the Business Analyst that's writing a requirement or test and the QA engineer who is automating or executing the test.  Our assumption is that the person writing the test case and ther person automating a test could be in different departments, offices, or on a different continent altogether and we wanted the design to support this type of organizational structure.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp;The Test module is designed to store all tests. The system is designed to provide a single repository for all tests and test results whether they be unit tests, manual test, or automated tests.  You can associate certain meta-data to a test which helps to organize the tests.  There are several such fields related to tests.  RTH allows users to define a Test Status, Test Area, Test Type, BA Owner, QA Owner, Tester etc. for each test.  All the data related to tests is discussed in detail below.  By clicking on the Manage hyperlink on the main menu, and selecting the appropriate Project, users can add values that are meaningful to their particular project in the fields described above.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ADD TEST
print"<tr>";
print"<td class='help-title'><a name='test_add'>". lang_get('help_test_add') ."</a></td>";
print"</tr>". NEWLINE;

print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; To add a new test to RTH, simply click on the 'Tests' hyperlink on the main menu and then select 'Add Test' from the main test page.  Clicking the 'Add Test' hyperlink will take you to the 'Add Test' form.  The form includes the following fields:</td>". NEWLINE;
print"</tr>". NEWLINE;
# TEST NAME
print"<tr><td class='help-text'><b>Test Name:</b> The name of the Test. It is beneficial to decide on a naming convention for tests.  Whether using RTH or a file server, a good naming convention will help users find tests and help understand relationships between tests.</td></tr>". NEWLINE;
# TEST PURPOSE
print"<tr><td class='help-text'><b>Purpose:</b> The general purpose or description of the test.  This field is intended to contain general information about the test and help other team members (managers, developers, other testers) get a feel for what the test covers and how it relates to the entire test bed.</td></tr>". NEWLINE;
# COMMENTS
print"<tr><td class='help-text'><b>Comments:</b> Free-form text with any additional comments relating to the test.</td></tr>". NEWLINE;
# TEST STATUS
print"<tr><td class='help-text'><b>Status:</b> The status of the test.  The default test status when creating a new test is 'New'.  The possible values are 'New', 'Assigned', 'WIP', 'Ready for Review', 'Completed', 'Rework', 'Review Test Case', and 'Review Requirement'.  There is some workflow built into RTH that will automatically update the test status.  If a test is related to a requirement and the requirement changes in any way, the system will automatically update the test status to 'Review Requirement'.  This is intended to help a tester to understand what tests need to be reviewed with changes to the requirements.  If the test changes in any way (a test step is added or modified, a new file is uploaded, etc.) the system will change the status to 'Review Test Case'.  This is intended to help when there is a distibuted test team where one person is writing a test while another is automating the test.  We want to make sure an automation engineer knows when a test case is updated so that they can update their automation.  The other statuses are intended to help understand the phase of development of the test.  Is the test currently being developed (WIP), ready for peer review (Ready for Review), ready for execution (Completed), etc.  The test status also appears on the 'Create Test Set' page and can help to define what tests should be included in a test set.  Please see the help section on 'Adding, Editing, a Release, Build, or Test Set' for more detail.</td></tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; NOTE: You can easily update or changed the test statuses by updating the test_get_status function within the test api.</td></tr>". NEWLINE;
# PRIORITY
print"<tr><td class='help-text'><b>Priority:</b> The default values are 'High', 'Medium', and 'Low'.  This field may be used to prioritize the development of the tests or the test execution.  You can easily update the values of this field by changing the test_get_priorities function within the test api.</td></tr>". NEWLINE;
# TEST AREA
print"<tr><td class='help-text'><b>Test Area:</b> The data in this field is populated by a project manager or rth administrator through the Management module of the application.  This field is intended to help users group test by the area of the application.  This field is a filerable field on the main test page and helps users to easily find and group tests.  The label of this field may not make sense for your project so it's best to think of this as a sort of custom field.  In fact, you can easily change the label of this field by updating the text in strings_english.txt.  There are some reports that a run against this field so it's best to have a good understanding of the reporting section before creating any definition for this field. <a href='#warning_on_meta_data'>". lang_get('help_warning_on_meta_data')  ."</a></td></tr>". NEWLINE;
# TEST TYPE
print"<tr><td class='help-text'><b>Test Type:</b> The 'Test Type' field is similar to the 'Test Area' field in that the values are unique to a project and are populated by a rth manager.  This field is a filerable field on the main test page and helps users to easily find and group tests.  The values in this field are similar to the 'Test Area' fild and can the label and values can be modified in the same way as the 'Test Area' field.</td></tr>". NEWLINE;
# BA OWNER
print"<tr><td class='help-text'><b>BA Owner:</b> This field is populated with any user associated to the project designated as a 'BA Owner'.  This field is used to identify the Business Analyst responsible for the test.  This field can also help Business Analysts easily filter for the tests that they're responsible for developing and maintaining.  This field is also useful if you have a distributed test team.</td></tr>". NEWLINE;
# QA OWNER
print"<tr><td class='help-text'><b>QA Owner:</b> This field is populated with any user associated to the project designated as a 'QA Owner'.  This field is used to identify the QA Engineer responsible for the test.  This field can also help a user easily find the tests that they're responsible for developing and maintaining.  This field is intended to help when there is a distributed test team.</td></tr>". NEWLINE;
# TESTER
print"<tr><td class='help-text'><b>Tester:</b> This field is populated with with all the users associated to your RTH project.  This field is used to identify the person responsible for running the test.  This field can also help a user easily find the tests that they're responsible for executing.  Typically, this field is used to designate the person responsible for exectuing the test.  This field is also useful if you have a distributed test team.</td></tr>". NEWLINE;
# ASSIGNED TO
print"<tr><td class='help-text'><b>Assigned To:</b> This dropdown is populated with all the users associated to your RTH project.  This field is used to identify the person assigned to work on the test.</td></tr>". NEWLINE;
# ASSIGN BY
print"<tr><td class='help-text'><b>Assigned By:</b> This dropdown is populated with all the users associated to your RTH project.  During the development of RTH we have gone back and forth on the importance of the following assingment and date fields.  One thought was that this information belongs in a project plan not a test management system.  These fields provide more meaningful reports with the implementation of test versions.  We have added and removed test versions several times but found they confused most user and made the tool too complicated.  We found a simpler way to implement test versions through the tool but these fields remain.  They can be used to assign tests for development and set dates but we ultimately leave it up to the user as to the best way to use these fields.</td></tr>". NEWLINE;
# DATE ASSIGNED
print"<tr><td class='help-text'><b>Date Assigned:</b> The date someone was assigned to work on the test.</td></tr>". NEWLINE;
# DATE EXPECTED
print"<tr><td class='help-text'><b>Date Expected:</b> The date someone was assigned to work on the test.</td></tr>". NEWLINE;
# DATE COMPLETED
print"<tr><td class='help-text'><b>Date Complete:</b> The date someone was assigned to work on the test.</td></tr>". NEWLINE;
# SIGN OFF DATE
print"<tr><td class='help-text'><b>Sign Off Date:</b> The date someone was assigned to work on the test.</td></tr>". NEWLINE;
# DURATION
print"<tr><td class='help-text'><b>Duration:</b> The time it takes to execute a test.  This field is used to calculate the amount of time it will take to complete running all the tests in a Test Set.  See the help section on test result or click on the \"Estimate Time To Complete\" link on the Test Results page to see how this field is used to calculate the time it will take to complete executing all the tests in a test set.</td></tr>". NEWLINE;
# AUTO PASS
print"<tr><td class='help-text'><b>Autopass:</b> RTH has a an auto-pass feature which allows users to pass tests without viewing test results.  Click the auto-pass hyperlink on the Test Results page and the system will automatically review the results of any test marked as 'AutoPass'.  If all the verifications within the test run have passed, the test will be automatically passed by the user 'System'.</td></tr>". NEWLINE;
# MANUAL
print"<tr><td class='help-text'><b>Manual:</b> This checkbox is used to denote a manual test.  You can then filter for all manual tests on the main Test or Test Result pages.</td></tr>". NEWLINE;
# AUTOMATED
print"<tr><td class='help-text'><b>Automated:</b> This checkbox is used to denote an automated test.  Checking this will allow you to filter for all automated tests on the main Test or Test Result pages.</td></tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# VIEW TESTS
print"<tr>";
print"<td class='help-title'><a name='test_view'>". lang_get('help_test_view') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Click on the 'Tests' link on the main menu, and you'll be taken to the main Test page.  This page displays all the tests associated to the project.  The page is made up of several parts.  The top of the page contains the Test sub-menu.  This should be fairly straght forward.  Next are a series of filters.  These filters are intended to help the user find a specific test or group of tests.  Select a value from one or more of the list boxes and click the Filter button.  This will limit the number of tests returned to the page.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; The next section is the table header.  This section diplays the number of records showing and the total number of records returned, links to page numbers, export to csv/excel functionality, and the column headers.  In addition to the filter functionality, clicking on a column header (TestID, DocType, etc.) allows the user to sort on a specific column.  For instance, a user may wish to filter for a particalar 'QA Owner' and display 100 records per page.  This will limit the number of records reurned (visible at the top of the table, Showing 1 - 100 of x).  After limiting the records by filtering, the user can then sort the data by clicking on any of the column headers.  An arrow will appear beside the column showing whether the column is sorted in ascending or descending order.  The page is sorted by the Test Name column by default.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; The next section of the page is the Test table which displays the actual test data.  Most of these columns are self-explanitory but the first two columns require some explanation.  The checkboxes down the left hand side of the table are intended to help users update multiple tests at once.  This mass update functionality is the fastest way to update many tests at once.  Simply filter for the tests that need to be updated and click the 'Select All' checkbox at the bottom of the page.  Selecting this checkbox will check every record on the page.  You can now select a value from the list box at the bottom of the page and click Update.  This will take the user to a page where they can select from a list of values and update all the tests selected with a chosen value.   Please be aware that the Select All checkbox at the bottom of the page will only appear if the user has JavaScript enabled on their browser.  If they don't have this enbabled, the user will have to select each checkbox manually.  The second column contains a unique Test ID.  Click on one of the TestID links to view the Test in more detail.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# TEST DETAIL
print"<tr>";
print"<td class='help-title'><a name='test_detail'>". lang_get('help_test_detail') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Click on TestID link on the Test page to view the detail of a test.  The Test Detail page allows the user to work on a specific test.  This is where the user will update the test, add or upload test steps, upload test documents, or associate requirements to the test.  There are two buttons on this page that allow a user to Update the test or Delete the test.  Clicking the 'Delete' button won't actually delete the test from the database but the test will no longer appear on the test page.  We do this so that you can still view any test run that occurred prior to deleting the test.  You will no longer see the test on the test page but you will be able to view the test results from previous releases.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ADD TEST STEPS
print"<tr>";
print"<td class='help-title'><a name='test_add_steps'>". lang_get('help_test_add_steps') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; There are two different ways to add test steps to a test but it is not necessary to add test steps to the system in order to execute a test (See the help section on 'Running a Test' to get more detail). By clicking on the 'Test Steps' tab at the bottom of the Test Detail page, you can upload test steps from an excel or csv format or enter the test steps one at a time in the 'Add Test Step' form.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; You can simply enter data in the 'Add Test Step' form and click the 'Add Step' button to begin build a test case.  The form contains three fields: 'Action', 'Test Inputs', and the 'Expected Result'.  You'll notice that there are two other objects on the form, a list box asking where you want to insert the step, and an 'Info Step' checkbox.  The listbox will allow you to insert a test step in the middle of other steps should some test steps already exist.  Simply select 'after step 2.0' for instance, and the step will be added after the second step of your test case.  The new step will be step 2.1 but you can renumber the step so that it's a whole number by clicking the 'Renumber Steps' button.  The 'Info Step' checkbox is intended to denote a step that won't result in a passed or failed state.  An example may be the preconditions for your test.  This field will appear in red and won't be reported on as a passed and failed step in the reporting section.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; If you're more comfortable writing a test case in excel, you can download a test case template from RTH and upload the file into RTH as test steps.  You can download the template by clicking on the 'Import Steps from Excel' or 'Import Steps from CVS' hyperlink.  The choice between using excel or csv if a global setting that is designated in the rth configuration file, properties_inc.php. This will take you to a page where you can download a test case template (click the 'Download Template' link).  Simply fill out the test case and save it in a .csv (comma seperated values) or excel format on your local device.  You can now return to the 'Import Tests Steps' page in RTH, browse to the file, and click the 'Upload' button.  This will automatically import your test steps into the test.  This action will also upload a version of this file under the Support Docs tab.  In this way, you can easily return to an earlier version of the test steps.  If you need to return to a previous version of the test, download the appropriate version and import the steps.</td>". NEWLINE;
print"</tr>". NEWLINE;
print"<tr>". NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Adding the test steps into RTH will help to report on the status of manual testing when it's time to run a test.  You can upload test step for any test but it is especially beneficial for a test that will be executed manually.  Because each individual step is stored as a record in the database, each record will be written out as an individual verification when running the test.  This will allow for more detailed reporting about the number of test steps passed and failed and allow for linkages between verifications and defects.</td>". NEWLINE;
print"</tr>". NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# ASSOCIATING A TEST TO REQS
print"<tr>";
print"<td class='help-title'><a name='test_associate_reqs'>". lang_get('help_test_associate_req') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; From the Test Detail page, click on the 'Req Assoc' (Requirement Association) hyperlink to view or update associations between the current test and requirements.  Clicking this hyperlink will open the 'Test/Requirement Assoc' page where you can filter for the requirements that link to the test.  Similar to tables on other pages, simply select the checkbox beside the appropriate requirement.  You can also specify the percent of the requirement covered by entering a number in the '% Covered by Test' text box.  This is important because a test may only cover 100% or only a small percentage of a requirement.  This value will have a direct impact on the Requirements Coverage report.  The Requirements Coverage report will tell you what percent of all the requirements in the system were test in a given test run.  This report is only useful if you have entered a percentage in the '% Covered' text box.</td>". NEWLINE;
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