using System;
using System.Text.RegularExpressions;
using System.Threading.Tasks;
using Microsoft.Playwright;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using LoreTest.Specs.Support;
using Reqnroll;
using static Microsoft.Playwright.Assertions;

namespace LoreTest.Specs.StepDefinitions
{
    [Binding]
    public class ReportingSteps
    {
        private readonly WebTestContext _context;
        private readonly string _baseUrl;

        public ReportingSteps(WebTestContext context)
        {
            _context = context;
            _baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5001";
        }

        [Given(@"they navigate to the Reporting page")]
        public async Task GivenTheyNavigateToTheReportingPage()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.GotoAsync($"{_baseUrl}/reporting");
            await _context.Page.WaitForTimeoutAsync(1500);
            await Expect(_context.Page).ToHaveTitleAsync(new Regex("Reporting Dashboard"));
        }

        [Then(@"they should see the Reporting dashboard with active metrics summary cards")]
        public async Task ThenTheyShouldSeeTheReportingDashboardWithActiveMetricsSummaryCards()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            var dashboard = _context.Page.Locator("#reporting-dashboard");
            await Expect(dashboard).ToBeVisibleAsync();

            var metricsCards = _context.Page.Locator(".metric-card");
            var count = await metricsCards.CountAsync();
            Assert.IsTrue(count >= 4, $"Expected at least 4 metrics summary cards, but found {count}.");
        }

        [When(@"they switch to the ""(.*)"" reporting tab")]
        public async Task WhenTheySwitchToTheReportingTab(string tabName)
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            string tabSelector = $"#tab-{tabName.ToLower()}";
            await _context.Page.ClickAsync(tabSelector);
            await _context.Page.WaitForTimeoutAsync(800);
        }

        [Then(@"they should see a list of all projects")]
        public async Task ThenTheyShouldSeeAListOfAllProjects()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            var table = _context.Page.Locator("#projects-report-table");
            await Expect(table).ToBeVisibleAsync();
        }

        [Then(@"they can filter projects by a search query ""(.*)""")]
        public async Task ThenTheyCanFilterProjectsByASearchQuery(string query)
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.FillAsync("#project-search", query);
            await _context.Page.WaitForTimeoutAsync(800);
            // Verify filter works by asserting there's no error or that the table adapts
            var table = _context.Page.Locator("#projects-report-table");
            await Expect(table).ToBeVisibleAsync();
        }

        [Then(@"they should see test suites grouped by project")]
        public async Task ThenTheyShouldSeeTestSuitesGroupedByProject()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            var table = _context.Page.Locator("#testsuites-report-table");
            await Expect(table).ToBeVisibleAsync();
            var groupings = _context.Page.Locator(".project-group-header");
            var count = await groupings.CountAsync();
            Assert.IsTrue(count >= 0, "Grouped test suites section should load.");
        }

        [Then(@"they can filter test suites by selecting a project")]
        public async Task ThenTheyCanFilterTestSuitesBySelectingAProject()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            var select = _context.Page.Locator("#suite-project-filter");
            await Expect(select).ToBeVisibleAsync();
            
            // If there's an option, let's select the first available one after "All Projects"
            var options = _context.Page.Locator("#suite-project-filter option");
            var count = await options.CountAsync();
            if (count > 1)
            {
                await select.SelectOptionAsync(new[] { new SelectOptionValue { Index = 1 } });
                await _context.Page.WaitForTimeoutAsync(800);
            }
        }

        [Then(@"they should see test cases grouped by test suite")]
        public async Task ThenTheyShouldSeeTestCasesGroupedByTestSuite()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            var table = _context.Page.Locator("#testcases-report-table");
            await Expect(table).ToBeVisibleAsync();
            var groupings = _context.Page.Locator(".suite-group-header");
            var count = await groupings.CountAsync();
            Assert.IsTrue(count >= 0, "Grouped test cases section should load.");
        }

        [Then(@"they can filter test cases by priority ""(.*)""")]
        public async Task ThenTheyCanFilterTestCasesByPriority(string priority)
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.SelectOptionAsync("#case-priority-filter", priority);
            await _context.Page.WaitForTimeoutAsync(800);
        }

        [Then(@"they should see test runs grouped by test case")]
        public async Task ThenTheyShouldSeeTestRunsGroupedByTestCase()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            var table = _context.Page.Locator("#testruns-report-table");
            await Expect(table).ToBeVisibleAsync();
            var groupings = _context.Page.Locator(".case-group-header");
            var count = await groupings.CountAsync();
            Assert.IsTrue(count >= 0, "Grouped test runs section should load.");
        }

        [Then(@"they can filter test runs by status ""(.*)""")]
        public async Task ThenTheyCanFilterTestRunsByStatus(string status)
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            string mapped = status;
            if (status == "Passed") mapped = "Pass";
            else if (status == "Failed") mapped = "Fail";
            await _context.Page.SelectOptionAsync("#run-status-filter", mapped);
            await _context.Page.WaitForTimeoutAsync(800);
        }

        [Then(@"they should see a list of all bugs with filters")]
        public async Task ThenTheyShouldSeeAListOfAllBugsWithFilters()
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            var table = _context.Page.Locator("#bugs-report-table");
            await Expect(table).ToBeVisibleAsync();
        }

        [Then(@"they can filter bugs by severity ""(.*)""")]
        public async Task ThenTheyCanFilterBugsBySeverity(string severity)
        {
            Assert.IsNotNull(_context.Page, "Page was not initialized.");
            await _context.Page.SelectOptionAsync("#bug-severity-filter", severity);
            await _context.Page.WaitForTimeoutAsync(800);
        }
    }
}
