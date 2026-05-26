using System.Text.RegularExpressions;
using Microsoft.Playwright;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using LoreTest.Specs.Support;
using Reqnroll;
using static Microsoft.Playwright.Assertions;

namespace LoreTest.Specs.StepDefinitions
{
    [Binding]
    public class ProjectSteps
    {
        private readonly WebTestContext _context;
        private readonly string _baseUrl;

        private string _createdProjectTitle = "";
        private string _createdProjectId = "";
        private string _createdSuiteTitle = "";
        private string _createdSuiteId = "";
        private string _createdCaseId1 = "";
        private string _createdCaseId2 = "";

        public ProjectSteps(WebTestContext context)
        {
            _context = context;
            _baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5001";
        }

        [Given(@"the user is authenticated as an administrator")]
        public async Task GivenTheUserIsAuthenticatedAsAnAdministrator()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.GotoAsync($"{_baseUrl}/Account/Login");
            await _context.Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
            await _context.Page.FillAsync("input[id='Input.Password']", "Password1-");
            await _context.Page.ClickAsync("button[type='submit']");
            await Expect(_context.Page.Locator("span.nav-text:has-text('Logout')")).ToBeVisibleAsync();
        }

        [Given(@"they navigate to the Projects page")]
        public async Task GivenTheyNavigateToTheProjectsPage()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.GotoAsync($"{_baseUrl}/projects");
            await _context.Page.WaitForTimeoutAsync(1500);
            await Expect(_context.Page).ToHaveTitleAsync(new Regex("Test Projects"));
        }

        [When(@"they create a new project with a unique title and description")]
        public async Task WhenTheyCreateANewProjectWithAUniqueTitleAndDescription()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.GotoAsync($"{_baseUrl}/projects/create");
            await _context.Page.WaitForTimeoutAsync(1500);

            _createdProjectTitle = $"Specs Project {DateTime.Now.Ticks}";
            var description = "Created by automated BDD tests.";

            await _context.Page.FillAsync("#title", _createdProjectTitle);
            await _context.Page.FillAsync(".ql-editor", description);
            await _context.Page.ClickAsync("form button.btn-primary");
            await _context.Page.WaitForTimeoutAsync(1500);
        }

        [Then(@"the new project should be listed on the Projects index page")]
        public async Task ThenTheNewProjectShouldBeListedOnTheProjectsIndexPage()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await Expect(_context.Page).ToHaveURLAsync(new Regex(".*/projects$"));
            var projectRow = _context.Page.Locator($"tr:has-text('{_createdProjectTitle}')");
            await Expect(projectRow).ToBeVisibleAsync();

            var idText = await projectRow.Locator(".data-mono").InnerTextAsync();
            _createdProjectId = idText.Trim();
        }

        [Then(@"they update the project title and description")]
        public async Task ThenTheyUpdateTheProjectTitleAndDescription()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.GotoAsync($"{_baseUrl}/projects/edit/{_createdProjectId}");
            await _context.Page.WaitForTimeoutAsync(1500);

            _createdProjectTitle = $"{_createdProjectTitle} - Updated";
            var updatedDescription = "Updated by automated BDD tests.";

            await _context.Page.FillAsync("#title", _createdProjectTitle);
            await _context.Page.FillAsync(".ql-editor", updatedDescription);
            await _context.Page.ClickAsync("form button.btn-primary");
            await _context.Page.WaitForTimeoutAsync(1500);
        }

        [Then(@"the updated details should be successfully visible on the details page")]
        public async Task ThenTheUpdatedDetailsShouldBeSuccessfullyVisibleOnTheDetailsPage()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await Expect(_context.Page).ToHaveURLAsync(new Regex(".*/projects$"));
            
            await _context.Page.GotoAsync($"{_baseUrl}/projects/details/{_createdProjectId}");
            await _context.Page.WaitForTimeoutAsync(1500);
            await Expect(_context.Page.Locator("h1, .headline-md, .title-md")).ToContainTextAsync(_createdProjectTitle);
        }

        [When(@"they create a test suite inside the project")]
        public async Task WhenTheyCreateATestSuiteInsideTheProject()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.GotoAsync($"{_baseUrl}/testsuites/create?projectId={_createdProjectId}");
            await _context.Page.WaitForTimeoutAsync(1500);

            _createdSuiteTitle = $"Specs Suite {DateTime.Now.Ticks}";
            var description = "Created by automated BDD tests.";

            await _context.Page.FillAsync("#title", _createdSuiteTitle);
            await _context.Page.FillAsync(".ql-editor", description);
            await _context.Page.ClickAsync("form button.btn-primary");
            await _context.Page.WaitForTimeoutAsync(1500);
        }

        [Then(@"the test suite should be visible in the project details")]
        public async Task ThenTheTestSuiteShouldBeVisibleInTheProjectDetails()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await Expect(_context.Page).ToHaveURLAsync(new Regex($".*/projects/details/{_createdProjectId}$"));

            var suiteRow = _context.Page.Locator($"tr:has-text('{_createdSuiteTitle}')");
            await Expect(suiteRow).ToBeVisibleAsync();

            var idText = await suiteRow.Locator(".data-mono").InnerTextAsync();
            _createdSuiteId = idText.Trim();
        }

        [When(@"they add two test cases, each with two test steps")]
        public async Task WhenTheyAddTwoTestCasesEachWithTwoTestSteps()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");

            // --- Case 1 ---
            await _context.Page.GotoAsync($"{_baseUrl}/testcases/create?suiteId={_createdSuiteId}");
            await _context.Page.WaitForTimeoutAsync(1500);

            var caseTitle1 = $"BDD Case 1 {DateTime.Now.Ticks}";
            await _context.Page.FillAsync("#title", caseTitle1);
            await _context.Page.SelectOptionAsync("#priority", "Medium");
            await _context.Page.ClickAsync("form button.btn-primary");
            await _context.Page.WaitForTimeoutAsync(1500);

            var caseRow1 = _context.Page.Locator($"tr:has-text('{caseTitle1}')");
            await Expect(caseRow1).ToBeVisibleAsync();
            _createdCaseId1 = (await caseRow1.Locator(".data-mono").InnerTextAsync()).Trim();

            // Steps for Case 1
            await _context.Page.GotoAsync($"{_baseUrl}/teststeps/create?testCaseId={_createdCaseId1}");
            await _context.Page.WaitForTimeoutAsync(1500);
            await _context.Page.Locator(".ql-editor").Nth(0).FillAsync("Step 1 Desc");
            await _context.Page.Locator(".ql-editor").Nth(1).FillAsync("Step 1 Expected");
            await _context.Page.ClickAsync("button.btn-primary");
            await _context.Page.WaitForTimeoutAsync(1500);

            await _context.Page.GotoAsync($"{_baseUrl}/teststeps/create?testCaseId={_createdCaseId1}");
            await _context.Page.WaitForTimeoutAsync(1500);
            await _context.Page.Locator(".ql-editor").Nth(0).FillAsync("Step 2 Desc");
            await _context.Page.Locator(".ql-editor").Nth(1).FillAsync("Step 2 Expected");
            await _context.Page.ClickAsync("button.btn-primary");
            await _context.Page.WaitForTimeoutAsync(1500);

            // --- Case 2 ---
            await _context.Page.GotoAsync($"{_baseUrl}/testcases/create?suiteId={_createdSuiteId}");
            await _context.Page.WaitForTimeoutAsync(1500);

            var caseTitle2 = $"BDD Case 2 {DateTime.Now.Ticks}";
            await _context.Page.FillAsync("#title", caseTitle2);
            await _context.Page.SelectOptionAsync("#priority", "High");
            await _context.Page.ClickAsync("form button.btn-primary");
            await _context.Page.WaitForTimeoutAsync(1500);

            var caseRow2 = _context.Page.Locator($"tr:has-text('{caseTitle2}')");
            await Expect(caseRow2).ToBeVisibleAsync();
            _createdCaseId2 = (await caseRow2.Locator(".data-mono").InnerTextAsync()).Trim();

            // Steps for Case 2
            await _context.Page.GotoAsync($"{_baseUrl}/teststeps/create?testCaseId={_createdCaseId2}");
            await _context.Page.WaitForTimeoutAsync(1500);
            await _context.Page.Locator(".ql-editor").Nth(0).FillAsync("Step 1 Desc");
            await _context.Page.Locator(".ql-editor").Nth(1).FillAsync("Step 1 Expected");
            await _context.Page.ClickAsync("button.btn-primary");
            await _context.Page.WaitForTimeoutAsync(1500);

            await _context.Page.GotoAsync($"{_baseUrl}/teststeps/create?testCaseId={_createdCaseId2}");
            await _context.Page.WaitForTimeoutAsync(1500);
            await _context.Page.Locator(".ql-editor").Nth(0).FillAsync("Step 2 Desc");
            await _context.Page.Locator(".ql-editor").Nth(1).FillAsync("Step 2 Expected");
            await _context.Page.ClickAsync("button.btn-primary");
            await _context.Page.WaitForTimeoutAsync(1500);
        }

        [Then(@"both test cases should be visible in the test suite details")]
        public async Task ThenBothTestCasesShouldBeVisibleInTheTestSuiteDetails()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.GotoAsync($"{_baseUrl}/testsuites/details/{_createdSuiteId}");
            await _context.Page.WaitForTimeoutAsync(1500);

            var cases = _context.Page.Locator("tr .data-mono");
            var count = await cases.CountAsync();
            Assert.IsTrue(count >= 2, $"Expected at least 2 test cases listed, but found {count}.");
        }

        [When(@"they start a test run and execute the steps with mixed pass and fail outcomes")]
        public async Task WhenTheyStartATestRunAndExecuteTheStepsWithMixedPassAndFailOutcomes()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.GotoAsync($"{_baseUrl}/testruns/setup?projectId={_createdProjectId}");
            await _context.Page.WaitForTimeoutAsync(1500);

            await _context.Page.SelectOptionAsync("#suiteSelect", new[] { _createdSuiteId });
            await _context.Page.WaitForTimeoutAsync(1000);

            await _context.Page.ClickAsync("button.btn-success");
            await _context.Page.WaitForTimeoutAsync(2000);

            await Expect(_context.Page).ToHaveURLAsync(new Regex(".*/testruns/execute/\\d+$"));

            // Step 1: PASS
            await _context.Page.ClickAsync("button:has(span:has-text('check_circle'))");
            await _context.Page.WaitForTimeoutAsync(500);
            await _context.Page.ClickAsync("button:has-text('Next')");
            await _context.Page.WaitForTimeoutAsync(1000);

            // Step 2: FAIL
            await _context.Page.ClickAsync("button:has(span:has-text('cancel'))");
            await _context.Page.WaitForTimeoutAsync(500);
            await _context.Page.ClickAsync("button:has-text('Next')");
            await _context.Page.WaitForTimeoutAsync(1000);

            // Step 3: PASS
            await _context.Page.ClickAsync("button:has(span:has-text('check_circle'))");
            await _context.Page.WaitForTimeoutAsync(500);
            await _context.Page.ClickAsync("button:has-text('Next')");
            await _context.Page.WaitForTimeoutAsync(1000);

            // Step 4: FAIL
            await _context.Page.ClickAsync("button:has(span:has-text('cancel'))");
            await _context.Page.WaitForTimeoutAsync(500);
            await _context.Page.ClickAsync("button:has-text('Complete')");
            await _context.Page.WaitForTimeoutAsync(2000);
        }

        [Then(@"the test run pass rate should be ""(.*)""")]
        public async Task ThenTheTestRunPassRateShouldBe(string expectedRate)
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await Expect(_context.Page).ToHaveURLAsync(new Regex(".*/testruns/details/\\d+$"));
            await Expect(_context.Page.Locator("h2.display-4")).ToContainTextAsync(expectedRate);
        }

        [Then(@"both test cases should display a ""(.*)"" outcome")]
        public async Task ThenBothTestCasesShouldDisplayAOutcome(string expectedOutcome)
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            var failBadges = _context.Page.Locator($"span.badge.bg-danger:has-text('{expectedOutcome}')");
            await Expect(failBadges).ToHaveCountAsync(2);
        }

        [When(@"they delete the created test cases, suite, and project")]
        public async Task WhenTheyDeleteTheCreatedTestCasesSuiteAndProject()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");

            // 1. Delete TestCase 1
            await _context.Page.GotoAsync($"{_baseUrl}/testcases/delete/{_createdCaseId1}");
            await _context.Page.WaitForTimeoutAsync(1500);
            await _context.Page.ClickAsync("button.btn-danger");
            await _context.Page.WaitForTimeoutAsync(1500);

            // 2. Delete TestCase 2
            await _context.Page.GotoAsync($"{_baseUrl}/testcases/delete/{_createdCaseId2}");
            await _context.Page.WaitForTimeoutAsync(1500);
            await _context.Page.ClickAsync("button.btn-danger");
            await _context.Page.WaitForTimeoutAsync(1500);

            // 3. Delete Suite
            await _context.Page.GotoAsync($"{_baseUrl}/testsuites/delete/{_createdSuiteId}");
            await _context.Page.WaitForTimeoutAsync(1500);
            await _context.Page.ClickAsync("button.btn-danger");
            await _context.Page.WaitForTimeoutAsync(1500);

            // 4. Delete Project
            await _context.Page.GotoAsync($"{_baseUrl}/projects/delete/{_createdProjectId}");
            await _context.Page.WaitForTimeoutAsync(1500);
            await _context.Page.ClickAsync("button.btn-danger");
            await _context.Page.WaitForTimeoutAsync(1500);
        }

        [Then(@"the project should no longer be listed on the Projects page")]
        public async Task ThenTheProjectShouldNoLongerBeListedOnTheProjectsPage()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.GotoAsync($"{_baseUrl}/projects");
            await _context.Page.WaitForTimeoutAsync(1500);

            var deletedRow = _context.Page.Locator($"tr:has-text('{_createdProjectTitle}')");
            await Expect(deletedRow).Not.ToBeVisibleAsync();
        }
    }
}
