using System.Text.RegularExpressions;
using Microsoft.Playwright.MSTest;
using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace LoreTest.Playwright
{
    [TestClass]
    [DoNotParallelize]
    public partial class ProjectTests : PageTest
    {
        private string _baseUrl = "";
        private static string _createdProjectTitle = "";
        private static string _createdProjectId = "";
        private static string _createdSuiteTitle = "";
        private static string _createdSuiteId = "";
        private static string _createdCaseId1 = "";
        private static string _createdCaseId2 = "";

        [TestInitialize]
        public async Task Setup()
        {
            _baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5001";

            // Start Playwright Tracing
            await Context.Tracing.StartAsync(new()
            {
                Screenshots = true,
                Snapshots = true,
                Sources = true
            });
        }

        [TestCleanup]
        public async Task Teardown()
        {
            var testName = TestContext.TestName;
            var isPassed = TestContext.CurrentTestOutcome == UnitTestOutcome.Passed;

            // Determine the results directory and ensure it exists
            string resultsDir = Path.Combine(Directory.GetCurrentDirectory(), "allure-results");
            try
            {
                if (!Directory.Exists(resultsDir))
                {
                    Directory.CreateDirectory(resultsDir);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Failed to create allure-results directory: {ex.Message}");
                resultsDir = Directory.GetCurrentDirectory();
            }

            // 1. Capture Screenshot on Failure
            if (!isPassed)
            {
                try
                {
                    var screenshotPath = Path.Combine(resultsDir, $"screenshot_{testName}.png");
                    await Page.ScreenshotAsync(new() { Path = screenshotPath, FullPage = true });
                    TestContext.AddResultFile(screenshotPath);
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"Failed to capture screenshot: {ex.Message}");
                }
            }

            // 2. Stop Tracing and attach to MSTest TestContext
            try
            {
                var tracePath = Path.Combine(resultsDir, $"trace_{testName}.zip");
                await Context.Tracing.StopAsync(new() { Path = tracePath });
                if (File.Exists(tracePath))
                {
                    TestContext.AddResultFile(tracePath);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Failed to stop tracing: {ex.Message}");
            }

            // 3. E2E CRUD Cleanup: Delete the newly created project via UI if it still exists and the test failed
            if (!isPassed && !string.IsNullOrEmpty(_createdProjectTitle))
            {
                try
                {
                    Console.WriteLine($"E2E CRUD Cleanup: Attempting to delete project '{_createdProjectTitle}' due to test failure.");
                    await Page.GotoAsync($"{_baseUrl}/projects");
                    await Page.WaitForTimeoutAsync(1500);

                    // Log back in if session expired
                    if (Page.Url.Contains("/Account/Login"))
                    {
                        await Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
                        await Page.FillAsync("input[id='Input.Password']", "Password1-");
                        await Page.ClickAsync("button[type='submit']");
                        await Page.GotoAsync($"{_baseUrl}/projects");
                        await Page.WaitForTimeoutAsync(1500);
                    }

                    var projectRow = Page.Locator($"tr:has-text('{_createdProjectTitle}')");
                    if (await projectRow.CountAsync() > 0)
                    {
                        // Click the delete button link in this specific row
                        await projectRow.Locator("a[href^='/projects/delete/']").ClickAsync();
                        await Page.WaitForTimeoutAsync(1500);

                        // Click the actual Delete button on the confirmation page
                        await Page.ClickAsync("button.btn-danger");
                        await Page.WaitForTimeoutAsync(1000);

                        Console.WriteLine($"E2E CRUD Cleanup: Successfully deleted project '{_createdProjectTitle}'");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"Failed to clean up test project: {ex.Message}");
                }
                finally
                {
                    _createdProjectTitle = "";
                    _createdProjectId = "";
                    _createdSuiteTitle = "";
                    _createdSuiteId = "";
                    _createdCaseId1 = "";
                    _createdCaseId2 = "";
                }
            }
        }

        [TestMethod]
        public async Task Test01_CreateAndUpdate_ShouldSucceed()
        {
            // 1. Navigate to Login Page
            await Page.GotoAsync($"{_baseUrl}/Account/Login");

            // 2. Fill in credentials and submit
            await Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
            await Page.FillAsync("input[id='Input.Password']", "Password1-");
            await Page.ClickAsync("button[type='submit']");

            // 3. Verify logged in successfully
            await Expect(Page.Locator("span.nav-text:has-text('Logout')")).ToBeVisibleAsync();

            // 4. Navigate to projects index and verify title
            await Page.GotoAsync($"{_baseUrl}/projects");
            await Page.WaitForTimeoutAsync(1500); // Allow Blazor interactive server setup
            await Expect(Page).ToHaveTitleAsync(ProjectsTitleRegex());

            // 5. Navigate to Create Project page directly
            await Page.GotoAsync($"{_baseUrl}/projects/create");
            await Page.WaitForTimeoutAsync(1500);

            // 6. Fill in create form
            var projectTitle = $"Playwright Project {DateTime.Now.Ticks}";
            _createdProjectTitle = projectTitle;
            var projectDescription = "This is a project created by automated Playwright E2E tests.";

            await Page.FillAsync("#title", projectTitle);
            await Page.FillAsync(".ql-editor", projectDescription); // Quill rich editor contains a .ql-editor editable div

            // 7. Click submit to create
            await Page.ClickAsync("form button.btn-primary");

            // 8. Verify redirection back to index and that the new project is listed
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectsUrlRegex());

            var projectRow = Page.Locator($"tr:has-text('{projectTitle}')");
            await Expect(projectRow).ToBeVisibleAsync();

            // Extract the newly created project ID from the table row
            var idText = await projectRow.Locator(".data-mono").InnerTextAsync();
            _createdProjectId = idText.Trim();

            // 9. Navigate to edit page for the created project directly
            await Page.GotoAsync($"{_baseUrl}/projects/edit/{_createdProjectId}");
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectEditUrlRegex());

            // 10. Update details
            var updatedTitle = $"{projectTitle} - Updated";
            var updatedDescription = $"{projectDescription} - This has been updated by Playwright.";

            await Page.FillAsync("#title", updatedTitle);
            await Page.FillAsync(".ql-editor", updatedDescription);

            // 11. Click save
            await Page.ClickAsync("form button.btn-primary");

            // 12. Verify redirect and updated details in list
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectsUrlRegex());

            _createdProjectTitle = updatedTitle; // Update cleanup tracker

            var updatedRow = Page.Locator($"tr:has-text('{updatedTitle}')");
            await Expect(updatedRow).ToBeVisibleAsync();

            // 13. Navigate to Details page to verify details are correct directly
            await Page.GotoAsync($"{_baseUrl}/projects/details/{_createdProjectId}");
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectDetailsUrlRegex());
            await Expect(Page.Locator("h1, .headline-md, .title-md")).ToContainTextAsync(updatedTitle);
        }

        [TestMethod]
        public async Task Test02_CreateTestSuite_ShouldSucceed()
        {
            Assert.IsFalse(string.IsNullOrEmpty(_createdProjectId), "No project was created in the previous step.");

            // 1. Navigate to Login Page
            await Page.GotoAsync($"{_baseUrl}/Account/Login");

            // 2. Fill in credentials and submit
            await Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
            await Page.FillAsync("input[id='Input.Password']", "Password1-");
            await Page.ClickAsync("button[type='submit']");

            // 3. Verify logged in successfully
            await Expect(Page.Locator("span.nav-text:has-text('Logout')")).ToBeVisibleAsync();

            // 4. Navigate directly to Create Test Suite page
            await Page.GotoAsync($"{_baseUrl}/testsuites/create?projectId={_createdProjectId}");
            await Page.WaitForTimeoutAsync(1500);

            // 5. Fill in test suite form
            var suiteTitle = $"Playwright Suite {DateTime.Now.Ticks}";
            _createdSuiteTitle = suiteTitle;
            var suiteDescription = "This is a test suite created by automated Playwright E2E tests.";

            await Page.FillAsync("#title", suiteTitle);
            await Page.FillAsync(".ql-editor", suiteDescription);

            // 6. Click submit to create
            await Page.ClickAsync("form button.btn-primary");

            // 7. Verify redirection back to project details page
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(new Regex($".*/projects/details/{_createdProjectId}$"));

            var suiteRow = Page.Locator($"tr:has-text('{suiteTitle}')");
            await Expect(suiteRow).ToBeVisibleAsync();

            // 8. Extract the newly created suite ID from the table row
            var idText = await suiteRow.Locator(".data-mono").InnerTextAsync();
            _createdSuiteId = idText.Trim();
        }

        [TestMethod]
        public async Task Test03_CreateTestCasesAndSteps_ShouldSucceed()
        {
            Assert.IsFalse(string.IsNullOrEmpty(_createdSuiteId), "No test suite was created in the previous step.");

            // 1. Navigate to Login Page
            await Page.GotoAsync($"{_baseUrl}/Account/Login");

            // 2. Fill in credentials and submit
            await Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
            await Page.FillAsync("input[id='Input.Password']", "Password1-");
            await Page.ClickAsync("button[type='submit']");

            // 3. Verify logged in successfully
            await Expect(Page.Locator("span.nav-text:has-text('Logout')")).ToBeVisibleAsync();

            // --- CREATE TEST CASE 1 ---
            // 4. Navigate to Create Test Case page
            await Page.GotoAsync($"{_baseUrl}/testcases/create?suiteId={_createdSuiteId}");
            await Page.WaitForTimeoutAsync(1500);

            var caseTitle1 = $"TestCase 1 {DateTime.Now.Ticks}";
            await Page.FillAsync("#title", caseTitle1);
            await Page.SelectOptionAsync("#priority", "Medium");
            await Page.ClickAsync("form button.btn-primary");

            // 5. Verify redirect to suite details
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(new Regex($".*/testsuites/details/{_createdSuiteId}$"));

            // 6. Scrape TestCase 1 ID
            var caseRow1 = Page.Locator($"tr:has-text('{caseTitle1}')");
            await Expect(caseRow1).ToBeVisibleAsync();
            _createdCaseId1 = (await caseRow1.Locator(".data-mono").InnerTextAsync()).Trim();

            // 7. Add 2 Test Steps to Test Case 1
            // Step 1.1
            await Page.GotoAsync($"{_baseUrl}/teststeps/create?testCaseId={_createdCaseId1}");
            await Page.WaitForTimeoutAsync(1500);
            await Page.Locator(".ql-editor").Nth(0).FillAsync("Test Case 1 - Step 1 Description");
            await Page.Locator(".ql-editor").Nth(1).FillAsync("Test Case 1 - Step 1 Expected Result");
            await Page.ClickAsync("button.btn-primary");
            await Page.WaitForTimeoutAsync(1500);

            // Step 1.2
            await Page.GotoAsync($"{_baseUrl}/teststeps/create?testCaseId={_createdCaseId1}");
            await Page.WaitForTimeoutAsync(1500);
            await Page.Locator(".ql-editor").Nth(0).FillAsync("Test Case 1 - Step 2 Description");
            await Page.Locator(".ql-editor").Nth(1).FillAsync("Test Case 1 - Step 2 Expected Result");
            await Page.ClickAsync("button.btn-primary");
            await Page.WaitForTimeoutAsync(1500);

            // --- CREATE TEST CASE 2 ---
            // 8. Navigate to Create Test Case page
            await Page.GotoAsync($"{_baseUrl}/testcases/create?suiteId={_createdSuiteId}");
            await Page.WaitForTimeoutAsync(1500);

            var caseTitle2 = $"TestCase 2 {DateTime.Now.Ticks}";
            await Page.FillAsync("#title", caseTitle2);
            await Page.SelectOptionAsync("#priority", "High");
            await Page.ClickAsync("form button.btn-primary");

            // 9. Verify redirect to suite details
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(new Regex($".*/testsuites/details/{_createdSuiteId}$"));

            // 10. Scrape TestCase 2 ID
            var caseRow2 = Page.Locator($"tr:has-text('{caseTitle2}')");
            await Expect(caseRow2).ToBeVisibleAsync();
            _createdCaseId2 = (await caseRow2.Locator(".data-mono").InnerTextAsync()).Trim();

            // 11. Add 2 Test Steps to Test Case 2
            // Step 2.1
            await Page.GotoAsync($"{_baseUrl}/teststeps/create?testCaseId={_createdCaseId2}");
            await Page.WaitForTimeoutAsync(1500);
            await Page.Locator(".ql-editor").Nth(0).FillAsync("Test Case 2 - Step 1 Description");
            await Page.Locator(".ql-editor").Nth(1).FillAsync("Test Case 2 - Step 1 Expected Result");
            await Page.ClickAsync("button.btn-primary");
            await Page.WaitForTimeoutAsync(1500);

            // Step 2.2
            await Page.GotoAsync($"{_baseUrl}/teststeps/create?testCaseId={_createdCaseId2}");
            await Page.WaitForTimeoutAsync(1500);
            await Page.Locator(".ql-editor").Nth(0).FillAsync("Test Case 2 - Step 2 Description");
            await Page.Locator(".ql-editor").Nth(1).FillAsync("Test Case 2 - Step 2 Expected Result");
            await Page.ClickAsync("button.btn-primary");
            await Page.WaitForTimeoutAsync(1500);
        }

        [TestMethod]
        public async Task Test04_ExecuteTestRun_ShouldRecordCorrectly()
        {
            Assert.IsFalse(string.IsNullOrEmpty(_createdSuiteId), "No test suite was created in the previous step.");

            // 1. Navigate to Login Page
            await Page.GotoAsync($"{_baseUrl}/Account/Login");

            // 2. Fill in credentials and submit
            await Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
            await Page.FillAsync("input[id='Input.Password']", "Password1-");
            await Page.ClickAsync("button[type='submit']");

            // 3. Verify logged in successfully
            await Expect(Page.Locator("span.nav-text:has-text('Logout')")).ToBeVisibleAsync();

            // 4. Navigate to setup test run page
            await Page.GotoAsync($"{_baseUrl}/testruns/setup?projectId={_createdProjectId}");
            await Page.WaitForTimeoutAsync(1500);

            // 5. Select the test suite from dropdown
            await Page.SelectOptionAsync("#suiteSelect", new[] { _createdSuiteId });
            await Page.WaitForTimeoutAsync(1000);

            // 6. Click Start Execution
            await Page.ClickAsync("button.btn-success");
            await Page.WaitForTimeoutAsync(2000);

            // 7. Verify we are on execution page
            await Expect(Page).ToHaveURLAsync(new Regex(".*/testruns/execute/\\d+$"));

            // Step 1 (Case 1, Step 1): Mark PASS and click Next
            await Page.ClickAsync("button:has(span:has-text('check_circle'))");
            await Page.WaitForTimeoutAsync(500);
            await Page.ClickAsync("button:has-text('Next')");
            await Page.WaitForTimeoutAsync(1000);

            // Step 2 (Case 1, Step 2): Mark FAIL and click Next
            await Page.ClickAsync("button:has(span:has-text('cancel'))");
            await Page.WaitForTimeoutAsync(500);
            await Page.ClickAsync("button:has-text('Next')");
            await Page.WaitForTimeoutAsync(1000);

            // Step 3 (Case 2, Step 1): Mark PASS and click Next
            await Page.ClickAsync("button:has(span:has-text('check_circle'))");
            await Page.WaitForTimeoutAsync(500);
            await Page.ClickAsync("button:has-text('Next')");
            await Page.WaitForTimeoutAsync(1000);

            // Step 4 (Case 2, Step 2): Mark FAIL and click Complete
            await Page.ClickAsync("button:has(span:has-text('cancel'))");
            await Page.WaitForTimeoutAsync(500);
            await Page.ClickAsync("button:has-text('Complete')");
            await Page.WaitForTimeoutAsync(2000);

            // 8. Verify redirection to test run details
            await Expect(Page).ToHaveURLAsync(new Regex(".*/testruns/details/\\d+$"));

            // 9. Assert correct results recorded
            // Verify overall pass rate is 0%
            await Expect(Page.Locator("h2.display-4")).ToContainTextAsync("0%");

            // Verify both case results show FAIL badge
            var failBadges = Page.Locator("span.badge.bg-danger:has-text('FAIL')");
            await Expect(failBadges).ToHaveCountAsync(2);
        }

        [TestMethod]
        public async Task Test05_DeleteAllInOrder_ShouldSucceed()
        {
            Assert.IsFalse(string.IsNullOrEmpty(_createdProjectId), "No project was created.");
            Assert.IsFalse(string.IsNullOrEmpty(_createdSuiteId), "No test suite was created.");
            Assert.IsFalse(string.IsNullOrEmpty(_createdCaseId1), "No test case 1 was created.");
            Assert.IsFalse(string.IsNullOrEmpty(_createdCaseId2), "No test case 2 was created.");

            // 1. Navigate to Login Page
            await Page.GotoAsync($"{_baseUrl}/Account/Login");

            // 2. Fill in credentials and submit
            await Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
            await Page.FillAsync("input[id='Input.Password']", "Password1-");
            await Page.ClickAsync("button[type='submit']");

            // 3. Verify logged in successfully
            await Expect(Page.Locator("span.nav-text:has-text('Logout')")).ToBeVisibleAsync();

            // 4. Delete Test Case 1
            await Page.GotoAsync($"{_baseUrl}/testcases/delete/{_createdCaseId1}");
            await Page.WaitForTimeoutAsync(1500);
            await Page.ClickAsync("button.btn-danger");
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(new Regex($".*/testsuites/details/{_createdSuiteId}$"));

            // 5. Delete Test Case 2
            await Page.GotoAsync($"{_baseUrl}/testcases/delete/{_createdCaseId2}");
            await Page.WaitForTimeoutAsync(1500);
            await Page.ClickAsync("button.btn-danger");
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(new Regex($".*/testsuites/details/{_createdSuiteId}$"));

            // 6. Delete Test Suite
            await Page.GotoAsync($"{_baseUrl}/testsuites/delete/{_createdSuiteId}");
            await Page.WaitForTimeoutAsync(1500);
            await Page.ClickAsync("button.btn-danger");
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(new Regex($".*/projects/details/{_createdProjectId}$"));

            // 7. Delete Project
            await Page.GotoAsync($"{_baseUrl}/projects/delete/{_createdProjectId}");
            await Page.WaitForTimeoutAsync(1500);
            await Page.ClickAsync("button.btn-danger");
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectsUrlRegex());

            // 8. Verify project no longer visible
            var deletedRow = Page.Locator($"tr:has-text('{_createdProjectTitle}')");
            await Expect(deletedRow).Not.ToBeVisibleAsync();

            // 9. Reset static tracking fields on successful completion
            _createdProjectTitle = "";
            _createdProjectId = "";
            _createdSuiteTitle = "";
            _createdSuiteId = "";
            _createdCaseId1 = "";
            _createdCaseId2 = "";
        }


        [GeneratedRegex("Test Projects")]
        private static partial Regex ProjectsTitleRegex();

        [GeneratedRegex(".*/projects$")]
        private static partial Regex ProjectsUrlRegex();

        [GeneratedRegex(".*/projects/edit/\\d+$")]
        private static partial Regex ProjectEditUrlRegex();

        [GeneratedRegex(".*/projects/details/\\d+$")]
        private static partial Regex ProjectDetailsUrlRegex();

        [GeneratedRegex(".*/projects/delete/\\d+$")]
        private static partial Regex ProjectDeleteUrlRegex();
        // Trigger CI/CD execution pipeline run
    }
}
