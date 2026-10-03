using Microsoft.Playwright;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using Reqnroll;

namespace LoreTest.Specs.Support
{
    [Binding]
    public class WebTestHooks
    {
        private readonly WebTestContext _context;
        private readonly ScenarioContext _scenarioContext;
        private readonly TestContext _msTestContext;

        public WebTestHooks(WebTestContext context, ScenarioContext scenarioContext, TestContext msTestContext)
        {
            _context = context;
            _scenarioContext = scenarioContext;
            _msTestContext = msTestContext;
        }

        [BeforeScenario]
        public async Task BeforeScenario()
        {
            await _context.InitializeAsync();

            // Start Playwright Tracing
            if (_context.Context != null)
            {
                await _context.Context.Tracing.StartAsync(new()
                {
                    Screenshots = true,
                    Snapshots = true,
                    Sources = true
                });
            }
        }

        [AfterScenario]
        public async Task AfterScenario()
        {
            var isPassed = _scenarioContext.TestError == null;
            var testName = _scenarioContext.ScenarioInfo.Title.Replace(" ", "_");
            
            string resultsDir = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "allure-results");
            string targetDir = Path.Combine(resultsDir, Environment.MachineName);
            
            try
            {
                if (!Directory.Exists(targetDir))
                {
                    Directory.CreateDirectory(targetDir);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Failed to create allure-results directory: {ex.Message}");
                targetDir = resultsDir;
            }

            if (_context.Page != null && _context.Context != null)
            {
                // 1. Capture Screenshot on Failure
                if (!isPassed)
                {
                    try
                    {
                        var screenshotPath = Path.Combine(targetDir, $"screenshot_{testName}.png");
                        await _context.Page.ScreenshotAsync(new() { Path = screenshotPath, FullPage = true });
                        _msTestContext.AddResultFile(screenshotPath);
                        Console.WriteLine($"Failure screenshot captured: {screenshotPath}");
                    }
                    catch (Exception ex)
                    {
                        Console.WriteLine($"Failed to capture screenshot: {ex.Message}");
                    }
                }

                // 2. Stop Tracing and attach to MSTest TestContext
                try
                {
                    var tracePath = Path.Combine(targetDir, $"trace_{testName}.zip");
                    await _context.Context.Tracing.StopAsync(new() { Path = tracePath });
                    if (File.Exists(tracePath))
                    {
                        _msTestContext.AddResultFile(tracePath);
                        Console.WriteLine($"Playwright trace saved: {tracePath}");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"Failed to stop tracing: {ex.Message}");
                }
            }

            // Cleanup resources
            if (_context.Page != null)
            {
                await _context.Page.CloseAsync();
            }
            if (_context.Browser != null)
            {
                await _context.Browser.CloseAsync();
            }
            _context.Playwright?.Dispose();
        }
    }
}
