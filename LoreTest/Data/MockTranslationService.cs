using System.Threading.Tasks;

namespace LoreTest.Data
{
    public class MockTranslationService(ApplicationDbContext dbContext) : ITranslationService
    {
        private readonly ApplicationDbContext _dbContext = dbContext;

        public async Task<string> TranslateAsync(string text, string fromCulture, string toCulture)
        {
            if (string.IsNullOrEmpty(text) || fromCulture == toCulture)
            {
                return text;
            }

            var settings = await Microsoft.EntityFrameworkCore.EntityFrameworkQueryableExtensions.FirstOrDefaultAsync(_dbContext.AppSettings);
            var apiName = settings?.TranslationApi ?? "Mock";

            // In a real app, this would call Google Translate, Azure Translator, etc.
            // For now, we simulate a translation by adding a prefix.
            return $"[{apiName}: {toCulture.ToUpper()}] {text}";
        }
    }
}
