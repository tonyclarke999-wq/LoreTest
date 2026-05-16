using System.Text.RegularExpressions;
using Microsoft.Playwright.MSTest;
using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace LoreTest.Playwright
{
    [TestClass]
    public partial class UserCreationTests : PageTest
    {
        private string _baseUrl = "";

        private static readonly string[] TesterRole = ["Tester"];

        [TestInitialize]
        public void Setup()
        {
            _baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5001";
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
            await Expect(Page.Locator("text=Sign out")).ToBeVisibleAsync();

            // Navigate to Create User page
            await Page.GotoAsync($"{_baseUrl}/users/create");

            // Fill in user details
            var testEmail = $"testuser_{DateTime.Now.Ticks}@example.com";
            await Page.FillAsync("#email", testEmail);
            await Page.FillAsync("#password", "Temporary123!");
            await Page.FillAsync("#name", "Test User");
            await Page.FillAsync("#jobTitle", "QA");

            // Select Role
            await Page.SelectOptionAsync("#role", TesterRole);

            // Set Start Date (InputDate might need specific format)
            await Page.FillAsync("#startDate", DateTime.Now.ToString("yyyy-MM-dd"));

            // Leave Preferred Language as default (English)
            // Submit
            await Page.ClickAsync("button[type='submit']");

            // Should redirect to users list and the new user should be there
            await Expect(Page).ToHaveURLAsync(UsersUrlRegex());
            await Expect(Page.Locator($"text={testEmail}")).ToBeVisibleAsync();
        }

        [GeneratedRegex(".*/users$")]
        private static partial Regex UsersUrlRegex();
    }
}
