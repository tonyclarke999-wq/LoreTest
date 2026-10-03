using Microsoft.Playwright;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using LoreTest.Specs.Support;
using Reqnroll;

namespace LoreTest.Specs.StepDefinitions
{
    [Binding]
    public class LoginSteps
    {
        private readonly WebTestContext _context;
        private readonly string _baseUrl;

        public LoginSteps(WebTestContext context)
        {
            _context = context;
            _baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5001";
        }

        [Given(@"the user navigates to the login page")]
        public async Task GivenTheUserNavigatesToTheLoginPage()
        {
            Assert.IsNotNull(_context.Page, "Playwright IPage was not initialized properly.");
            await _context.Page.GotoAsync($"{_baseUrl}/Account/Login");
        }

        [When(@"they sign in with administrator credentials")]
        public async Task WhenTheySignInWithAdministratorCredentials()
        {
            Assert.IsNotNull(_context.Page, "Playwright IPage was not initialized properly.");
            await _context.Page.FillAsync("input[id='Input.Email']", "tonyclarke999@gmail.com");
            await _context.Page.FillAsync("input[id='Input.Password']", "Password1-");
            await _context.Page.ClickAsync("button[type='submit']");
        }

        [When(@"they navigate to the home page")]
        public async Task WhenTheyNavigateToTheHomePage()
        {
            Assert.IsNotNull(_context.Page, "Playwright IPage was not initialized properly.");
            await _context.Page.GotoAsync(_baseUrl);
        }

        [Then(@"the dashboard title should be displayed")]
        public async Task ThenTheDashboardTitleShouldBeDisplayed()
        {
            Assert.IsNotNull(_context.Page, "Playwright IPage was not initialized properly.");
            var title = await _context.Page.TitleAsync();
            Assert.IsTrue(title.Contains("Dashboard"), $"Expected page title to contain 'Dashboard', but was '{title}'");
        }
    }
}
