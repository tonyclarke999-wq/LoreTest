using System.Text.RegularExpressions;
using Microsoft.Playwright.MSTest;
using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace LoreTest.Playwright
{
    [TestClass]
    public partial class ProjectTests : PageTest
    {
        private string _baseUrl = "";
        private string _createdProjectTitle = "";

        [TestInitialize]
        public async Task Setup()
        {
            _baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5001";
            _createdProjectTitle = "";

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

            // 3. E2E CRUD Cleanup: Delete the newly created project via UI if it still exists
            if (!string.IsNullOrEmpty(_createdProjectTitle))
            {
                try
                {
                    Console.WriteLine($"E2E CRUD Cleanup: Attempting to delete project '{_createdProjectTitle}'");
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
            }
        }

        [TestMethod]
        public async Task ProjectCRUD_ShouldSucceed()
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
            await Page.ClickAsync("button[type='submit']");

            // 8. Verify redirection back to index and that the new project is listed
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectsUrlRegex());

            var projectRow = Page.Locator($"tr:has-text('{projectTitle}')");
            await Expect(projectRow).ToBeVisibleAsync();

            // Extract the newly created project ID from the table row
            var idText = await projectRow.Locator(".data-mono").InnerTextAsync();
            var projectId = idText.Trim();

            // 9. Navigate to edit page for the created project directly
            await Page.GotoAsync($"{_baseUrl}/projects/edit/{projectId}");
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectEditUrlRegex());

            // 10. Update details
            var updatedTitle = $"{projectTitle} - Updated";
            var updatedDescription = $"{projectDescription} - This has been updated by Playwright.";

            await Page.FillAsync("#title", updatedTitle);
            await Page.FillAsync(".ql-editor", updatedDescription);

            // 11. Click save
            await Page.ClickAsync("button[type='submit']");

            // 12. Verify redirect and updated details in list
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectsUrlRegex());

            _createdProjectTitle = updatedTitle; // Update cleanup tracker

            var updatedRow = Page.Locator($"tr:has-text('{updatedTitle}')");
            await Expect(updatedRow).ToBeVisibleAsync();

            // 13. Navigate to Details page to verify details are correct directly
            await Page.GotoAsync($"{_baseUrl}/projects/details/{projectId}");
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectDetailsUrlRegex());
            await Expect(Page.Locator("h1, .headline-md, .title-md")).ToContainTextAsync(updatedTitle);

            // 14. Navigate back to list from Details directly
            await Page.GotoAsync($"{_baseUrl}/projects");
            await Page.WaitForTimeoutAsync(1500);

            // 15. Navigate to Delete confirmation page directly
            await Page.GotoAsync($"{_baseUrl}/projects/delete/{projectId}");
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectDeleteUrlRegex());

            // 16. Click the actual Delete button to confirm deletion
            await Page.ClickAsync("button.btn-danger");

            // 17. Verify redirect and that project is no longer visible in list
            await Page.WaitForTimeoutAsync(1500);
            await Expect(Page).ToHaveURLAsync(ProjectsUrlRegex());

            var deletedRow = Page.Locator($"tr:has-text('{updatedTitle}')");
            await Expect(deletedRow).Not.ToBeVisibleAsync();

            // 18. Reset cleanup tracker on successful completion
            _createdProjectTitle = "";
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
    }
}
