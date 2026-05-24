using System.Text.RegularExpressions;
using Microsoft.Playwright.MSTest;
using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace LoreTest.Playwright
{
    [TestClass]
    public partial class UserCreationTests : PageTest
    {


        private string _baseUrl = "";
        private string _testEmail = "";

        private static readonly string[] TesterRole = ["Tester"];

        [TestInitialize]
        public async Task Setup()
        {
            _baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5001";
            _testEmail = "";

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

            // 1. Capture Screenshot on Failure
            if (!isPassed)
            {
                try
                {
                    var screenshotPath = Path.Combine(targetDir, $"screenshot_{testName}.png");
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
                var tracePath = Path.Combine(targetDir, $"trace_{testName}.zip");
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

            // 3. CRUD Cleanup: Delete the newly created user via UI
            if (!string.IsNullOrEmpty(_testEmail))
            {
                try
                {
                    Console.WriteLine($"E2E CRUD Cleanup: Attempting to delete {_testEmail}");
                    await Page.GotoAsync($"{_baseUrl}/users");
                    await Page.WaitForTimeoutAsync(1500);

                    // Log back in if session expired
                    if (Page.Url.Contains("/Account/Login"))
                    {
                        await Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
                        await Page.FillAsync("input[id='Input.Password']", "Password1-");
                        await Page.ClickAsync("button[type='submit']");
                        await Page.GotoAsync($"{_baseUrl}/users");
                        await Page.WaitForTimeoutAsync(1500);
                    }

                    var userRow = Page.Locator($"tr:has-text('{_testEmail}')");
                    if (await userRow.CountAsync() > 0)
                    {
                        // Click the delete button link in this specific row
                        await userRow.Locator("a[href^='/users/delete/']").ClickAsync();

                        // Click the actual Delete button on the confirmation page
                        await Page.ClickAsync("button.btn-danger");

                        Console.WriteLine($"E2E CRUD Cleanup: Successfully deleted {_testEmail}");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"Failed to clean up test user: {ex.Message}");
                }
            }
        }

        [TestMethod]
        public async Task CreateUser_WithoutChangingLanguage_ShouldSucceed()
        {
            // Note: This test requires a valid admin login. 
            // If you run this, please ensure the credentials below are correct or update them.

            await Page.GotoAsync($"{_baseUrl}/Account/Login");

            // Fill in login credentials
            await Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
            await Page.FillAsync("input[id='Input.Password']", "Password1-"); // Updated with the correct password
            await Page.ClickAsync("button[type='submit']");

            // Check if we are logged in
            await Expect(Page.Locator("span.nav-text:has-text('Logout')")).ToBeVisibleAsync();

            // Navigate to Create User page
            await Page.GotoAsync($"{_baseUrl}/users/create");
            await Page.WaitForTimeoutAsync(1500); // Allow Blazor interactive connection to establish

            // Fill in user details
            _testEmail = $"testuser_{DateTime.Now.Ticks}@example.com";
            await Page.FillAsync("#email", _testEmail);
            await Page.FillAsync("#password", "Temporary123!");
            await Page.FillAsync("#name", "Test User");
            await Page.FillAsync("#jobTitle", "QA");

            // Select Role
            await Page.SelectOptionAsync("#role", TesterRole);

            // Set Start Date (InputDate might need specific format)
            await Page.FillAsync("#startDate", DateTime.Now.ToString("yyyy-MM-dd"));

            // Submit
            await Page.ClickAsync("button.btn-primary[type='submit']");

            // Wait a moment for network/validation to update
            await Page.WaitForTimeoutAsync(1000);

             if (!Page.Url.EndsWith("/users"))
             {
                 var validationMessages = await Page.Locator(".validation-message, .text-danger, .alert-danger").AllInnerTextsAsync();
                 Console.WriteLine("--- SUBMISSION VALIDATION ERRORS ---");
                 foreach (var msg in validationMessages)
                 {
                     Console.WriteLine($"Error: {msg}");
                 }
             }

            // Should redirect to users list and the new user should be there
            await Expect(Page).ToHaveURLAsync(UsersUrlRegex());
            await Expect(Page.Locator($"text={_testEmail}")).ToBeVisibleAsync();
        }

        [GeneratedRegex(".*/users$")]
        private static partial Regex UsersUrlRegex();
    }
}
