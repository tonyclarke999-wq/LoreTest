using Microsoft.Playwright.MSTest;
using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace LoreTest.Playwright
{
    [TestClass]
    public class E2ETests : PageTest
    {
        [TestMethod]
        public async Task HomePage_ShouldLoadSuccessfully()
        {
            var baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5000";
            await Page.GotoAsync(baseUrl);
            await Expect(Page).ToHaveTitleAsync(new System.Text.RegularExpressions.Regex("Home"));
        }
    }
}
