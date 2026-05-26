using Microsoft.Playwright;

namespace LoreTest.Specs.Support
{
    public class WebTestContext : IDisposable
    {
        public IPlaywright? Playwright { get; private set; }
        public IBrowser? Browser { get; private set; }
        public IBrowserContext? Context { get; private set; }
        public IPage? Page { get; private set; }

        public async Task InitializeAsync()
        {
            Playwright = await Microsoft.Playwright.Playwright.CreateAsync();
            Browser = await Playwright.Chromium.LaunchAsync(new BrowserTypeLaunchOptions
            {
                Headless = true
            });
            
            Context = await Browser.NewContextAsync();
            Page = await Context.NewPageAsync();
        }

        public void Dispose()
        {
            try
            {
                Context?.CloseAsync().GetAwaiter().GetResult();
                Browser?.CloseAsync().GetAwaiter().GetResult();
                Playwright?.Dispose();
            }
            catch
            {
                // Ignore silent dispose errors
            }
        }
    }
}
