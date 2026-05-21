using System.Text.RegularExpressions;
using Microsoft.Playwright.MSTest;
using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace LoreTest.Playwright
{
    [TestClass]
    public partial class E2ETests : PageTest
    {


        [TestInitialize]
        public async Task Setup()
        {
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

            // 1. Capture Screenshot on Failure
            if (!isPassed)
            {
                try
                {
                    var screenshotPath = Path.Combine(Directory.GetCurrentDirectory(), $"screenshot_{testName}.png");
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
                var tracePath = Path.Combine(Directory.GetCurrentDirectory(), $"trace_{testName}.zip");
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
        }

        [TestMethod]
        public async Task HomePage_ShouldLoadSuccessfully()
        {
            var baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5001";
            await Page.GotoAsync($"{baseUrl}/Account/Login");
            await Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
            await Page.FillAsync("input[id='Input.Password']", "Password1-");
            await Page.ClickAsync("button[type='submit']");
            await Page.GotoAsync(baseUrl);
            await Expect(Page).ToHaveTitleAsync(DashboardRegex());
        }

        [GeneratedRegex("Dashboard")]
        private static partial Regex DashboardRegex();
    }
}
