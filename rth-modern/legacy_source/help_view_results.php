<?php
include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();


print"<br>". NEWLINE;
print"<p class='help-page-title'>". lang_get('help_run_test') ."</p>". NEWLINE;

print"<base target='_self'>". NEWLINE;


print"<ul>". NEWLINE;
print"<li class='help-link'><a href='#results_overview'>". lang_get('help_results_overview') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#results_view_tests'>". lang_get('help_results_view_tests') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#results_test_run'>". lang_get('help_results_view_testrun') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#results_verifications'>". lang_get('help_results_view_verifications') ."</a>". NEWLINE;
print"</ul>". NEWLINE;

print"<table>";

print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

# RESULTS OVERVIEW
print"<tr>";
print"<td class='help-title'><a name='results_overview'>". lang_get('help_results_overview') ."</a></td>";
print"</tr>";
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; RTH is a central repository for all test results, both manual and automated.  The running of automated and manual tests will be treated seperately in the <a href=\"help_run_mantest.php\">Run Test</a> help file.  In this file, we'll begin with an overview of the test results section.  You can view test results by cliking on the \"Test Results\" link on the main menu.  This link will take you to a page that prompts you for the appropriate Release, Build, and Testset.  Please read the previous help section on <a href=\"help_testset.php\">Adding Releases, Builds, and Testsets</a> if you have any questions about how to manage releases through rth.  So that we can provide accurate metrics, all test results are stored under a release and build.  The release, build, and Testset fields are free form text, so if you're not testing software, you can enter the appropriate value.</td>".NEWLINE;
print"</tr>";
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Once you have navigated to the specific testset that you want to view, you'll find several levels of test results.  Each level will be described in detail below but we'll give a high level overview here.  The first level lists all the tests that you planned to execute in the testset.  Each test has a status so that the users can get a general sense of how testing is progressing.  Are 50% of the tests passed or failed?  This view is not intended to give the users much detail about the test results but it can give them an overview of the test execution.  By clicking on the \"Test Results\" link of a particular test, you can view the next level of results.  Here you can see how many times the test was run.  The test may have been run many times against a particular build be for it passed and we felt it was important to capture how many times each test was run.  By clicking on the \"View Results\" link  beside a particular test run, you will view the individual verifications that were performed.  Here you can view each step performed in the test and whether the step passed or failed.  Each one of three \"levels\" of testing will be covered in more detail below but you should now have some sense of how navigate the test results section in rth.</td>".NEWLINE;
print"</tr>". NEWLINE;


print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();


# RESULTS - VIEW TESTS
print"<tr>";
print"<td class='help-title'><a name='results_view_tests'>". lang_get('help_results_view_tests') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Here we will go into greater detail about the first level of test results, the \"Results Page.\" As mentioned earler, you can navigate to the results page by selecting a specific Release, Build, and Testset.  This page displays all the tests that were planned for execution.  The page has a filter similar to the filter on the test page that will help you find a particular test or tests.  By filtering on \"Test Status,\" the page acts as a type of report.  If you want to view all the tests that have failed, simply filter for the test status of \"Failed\" and you can quickly see what tests (or what area of your application) may need attention.  The filter is also useful for the engineers who are assigned to executed the tests.  If the user has been assigned as the \"QA Owner\" of particular tests in the test section of the application, they can filter for their name in the QA Owner list box and quickly view all the tests that they're responsible for running.</td>".NEWLINE;
print"</tr>";
print"<tr>";
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Just above the filter form you'll notice two links, \"Run Autopass\" and \"Estimated Time to Complete\".  The first link is useful when you're writing a lot of automated test results to rth.  By clicking the \"Run Autopass\" link, every automated test that has no failed verifications will automatically be passed.  This allows your testers to focus only on those automated tests that have failed.  I should mention that autopass does not automatically pass every automated test run.  There are some checks and balances.  First, autopass will only review those tests where \"Autopass\" is checked in the test properties.  You must check the \"Autopass\" checkbox on the Update Test form before the test will be included in the automatic review.  We feel that both the test and the functionality should be very stable before designating the test for autopass.  The autopass feature also reviews only the latest automated test run.  It will not pass at test if the test has been run three times and the first two test runs pass but the last test runs has a failure.  In fact, the system only reviews the latest test run.  The \"Estimated Time to Complete\" link is only useful if you enter the \"Duration\" field for each test.  If you specify how many minutes it takes to run each test, the system will tell you how much longer it will take to run the remaining tests in the Testset when you click on the \"Estimated Time\" link.  This may be useful if you're on a very tight schedule.  Perhaps you're wondering whether you need your staff to work the weekend.  If you've entered accurate values in the \"Duration\" field for each test, you can find out how much longer it will take to execute the tests remaining in the Testset.</td>".NEWLINE;
print"</tr>";
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Below the filter, you'll find the table displaying all the tests.  The columns that are displayed are described below.  Much of this information is also covered in the <a href=help_tests.php>help section on tests</a>.
<ul>
<li><b>checkbox</b> - This box is used for mass update functionality which is described below this list.</li>
<li><b>ID</b> - A unique test id generated by the database when the test is added to the system.</li>
<li><b>Manual/Automated indicator</b> - This icon defines whether the test is a manual (M), automated (A), or both (M/A).</li>
<li><b>Test Name</b> - The name of the test.</li>
<li><b>BA Owner</b> - The username of the Business Analyst who is responsible for the test or the area of the application covered by the test.  This may be the person who wrote the requirements, the use case, or the test case.  This field is particualarly useful when you have a distributed test team.   If your automation engineer in Bangalore has a question about the functionality of the application, they'll know who to contact by looking up the BA Owner.  For smaller companies, this field may not be necessary and you can easily change the label of this field to something that makes more sense for your organization by editing the string_english.txt file in the /lang directory of the rth installation.</li>
<li><b>QA Owner</b> - The username of the Quality Assurance engineer responsible for the test.  Again, this may be your automation engineer or simply the person responsible for running the test.  It's really up to you to use this listbox in a way that makes sense for your organization.</li>
<li><b>Test Type</b> - A user defined field that is intended to help you group your tests in a logical way.  The field was initially intended to capture types of tests such as \"Unit\", \"Integration\", and \"Regression\" but it's best if you use whatever values help you to find and report on your tests.</li>
<li><b>Test Area</b> - A user defined field that is intended to help you group your tests in a logical way.  Test Area was intended to denote areas of your application.  For an online banking system, the Test Areas might be \"Login\", \"Account Info\", and \"Fund Transfer\" but it's best if you don't limit yourself by following our intention for field.  Again, use it in any way that allows you to easily find and report on your tests.</li>
<li><b>Test Reults link</b> - This link will take you to the page that displays each test run for this test.  If the test was run five times before passing, you'll see five records when you click on this link.</li>
<li><b>Tester</b> - The username of the user who updated the Test Status for this test.  We will discuss how to update the status below.</li>
<li><b>Info</b> - This column contains any comments the user may have made when they updated the test Status.  The field will be blank if there is not test status and an information icon (i) if the user has entered a comment.  You can view the comment by holding your mouse over the information icon.</li>
<li><b>Test Status</b> - The overall status of the test.  This is a very high level status that amounts to a sign off on the test.  The test may have been run 10 times, and each test run may have 2 failed verification, but maybe you still want to pass the test. Perhaps the 2 bugs are known bugs that are acceptable to your users.  This field gives you a way to report the test status to management at a very high level so that they can decide if a release is ready to ship.</li>
<li><b>Run Test</b> - This link will only appear if the test is designated as a \"Manual Test.\"  This link will allow you to run any manual test.  A Run Test link will also appear for automated tests if you have installed the Remote Test Execution module.</li>
<li><b>Update</b> - This link allows user to update the Test Status.  When you click on this link, you will be directed to a form that prompts the user for the \"Test Status,\"  \"Comments,\" a \"Not Run/Failed Reason\" (used only if the test wasn't run or failed), and a list of users that you may want to email when you submit the form.</li>
</ul>
</td>".NEWLINE;
print"</tr>";
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Below the list of scheduled tests, is one additional piece of functionality, the \"Mass Update\" list box.  The mass update functionality on this page works the same it does on the Requirement, Test, and Defect pages.  Simply, check off the tests you want to update, select a status from the dropdown, and hit the OK button.  When hitting the OK button, the user is redirected to a page where they can enter Comments. This functionality is intended to help the users speed through their updates.  A user may filter for tests assigned to them, hit the \"Select All\" checkbox at the bottom of the page, and change the test status of all their test to \"Passed\" with a single click.</td>".NEWLINE;
print"</tr>";

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# RESULTS - TEST RUN
print"<tr>".NEWLINE;
print"<td class='help-title'><a name='results_test_run'>". lang_get('help_results_view_testrun') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; As mentined above, you can view the test runs for a particular test by clicking the \"Test Results\" link from the Test Run Results page.  The test run page is made up of three parts.  The sub-menu which allows a user to navigate back to the previous Testset, a table containing an overview of the test, and a list displaying how many times a test was run and some detail about each run.  You'll notice that the menu is stored in memory as long as you're logged into the application so you can easily return to the same test set by clicking the \"Test Results\" link on the main menu.  The table containing the test details also contains a link but this link will redirect you to the Test Detail page.  This might be useful in case you need to review the steps or any other detail about the test.  Below the menu and the test details, you'll find a list showing how many times the test was run.</td>".NEWLINE;
print"</tr>";
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; The table detailing the test run may look slightly different depending on whether the test is manual or automated.  If you run the test manually, some information, such as the Machine Name, will not be captured.  If you use the <a href=\"http://www.bhtconsulting.com/gpage2.html\" target=\"_blank\">automated test libraries</a> provied by the rth team, the automated scripts will capture the Machine Name, Operating System, and Service Pack of the test device.  In addition, the automated scripts capture who logged into the machine as well as statistics about the memory utilization on the machine as each verification is written out to the rth database.  The memory statistics are less important for a simple web application, but there are cases where it is important to know the impact your client has on the end users device.  The Machine Name, and OS Service Pack will only appear if running an automated test but a user can record some of this information when running the test manually.  Running tests manually is described in the <a href=\"help_run_mantest.php\">next help section</a>.  Whether the test is manual or automated, the list of test runs contains when each test run was executed, what environment it was run in, and the number of test steps passed, failed, or incompleted.  You can view the individual verifications by clicking on the \"View Results\" link on the right-hand side of the page.</td>".NEWLINE;
print"</tr>".NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# RESULTS - VERIFICATIONS
print"<tr>";
print"<td class='help-title'><a name='results_verifications'>". lang_get('help_results_view_verifications') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; By clicking on the \"View Results\" link of a particular test run, you can see the individual verifications performed for a particular test.  At the top of the page is a table displaying some of the test run details and some important links.  By clicking on the \"Pass\" link you can pass the test and return directly to the Testset page.  Click \"Fail\" and you will fail the test and return directly to the Testset page.  By clicking on \"Comments\" the user will be redirected to a form where they can define the status of the test and enter any comments before beging returned to the Testset page. This is the same form the user completes when hitting the \"Update\" button from the Testset page.  The final link at the top of the page, \"Continue Test Run\" is only used when a user wants to continue running a manual test.  Perhaps the user was unable to complete the entire test in one sitting (or wisely saves their work).  The \"Continue\" link will allow them to return to an unfinished test run.  It also is one way in which they can edit the test run, although the \"Verifications\" page also allows users to edit any of the test steps.</td>".NEWLINE;
print"</tr>".NEWLINE;
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; The body of the \"Verifications Page\" contains the steps that were executed while testing the application.  The columns include Step Number, Action, Expected Result, Actual Result and Status.  In addition is an Edit link which allows the user to add comments to a particular step or associate a defect to the step.  Click the \"Defect\" link to create a bug or the \"Edit\" link to associate a bug and add a Comment to the Actual Result.  In addition to these fields are some custom fields that can be displayed or hidden in the project preferences.  These filds are intended for automated testing.  When running an automated test, you may want to post information that will help a user recreate a problem manually if there's problem.  Like any of the other text throughout the application, you can rename any of the custom fields by updating \"custom_\" strings in the /lang/strings_english.txt file.  In addition, are other fields that are used by automated tests.  the \"Window\" and \"Object\" columns can be used to define the window and object an automated test is acting upon.  Again, this is intended to help a  manual tester reproduce problems found through automation.  The \"Line Number\" and \"Memory\" fields are also used by automation.  If you use the libraries supplied by rth, these values will be populated as the automated test progresses.  The Line No field will display the line number of the script when the step was executed.  This can help the automation engineer track down any potential problems with their script.</td>".NEWLINE;
print"</tr>".NEWLINE;
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; Below the test steps, is a form for file upload.  Here a user can upload any file relevant to the test.  If the user wants to capture a screen shot to clarify the error they found while testing, this is where they might upload the file.  The user can also upload files in the defect section of the application so it's really up to the user whether to upload the file within the test or in the defect section.  This file might also contain test results that don't fit the table format provided by rth.  Perhaps the output of the test is raw data that needs to be stored for every test run.  The file upload form is for any type of test results that might be relevant to the tesst run.</td>".NEWLINE;
print"</tr>".NEWLINE;


print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();


print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;
util_add_spacer();

print"</table>";

?>