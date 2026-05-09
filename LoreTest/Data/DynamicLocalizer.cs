using Microsoft.Extensions.Localization;
using Microsoft.EntityFrameworkCore;
using System.Globalization;

namespace LoreTest.Data
{
    public class DynamicLocalizer(IStringLocalizer fallbackLocalizer, IDbContextFactory<ApplicationDbContext> dbContextFactory) : IStringLocalizer
    {
        private readonly IStringLocalizer _fallbackLocalizer = fallbackLocalizer;
        private readonly IDbContextFactory<ApplicationDbContext> _dbContextFactory = dbContextFactory;

        public LocalizedString this[string name]
        {
            get
            {
                var value = GetTranslationFromDb(name);
                if (value != null)
                {
                    return new LocalizedString(name, value, false);
                }
                return _fallbackLocalizer[name];
            }
        }

        public LocalizedString this[string name, params object[] arguments]
        {
            get
            {
                var value = GetTranslationFromDb(name);
                if (value != null)
                {
                    return new LocalizedString(name, string.Format(value, arguments), false);
                }
                return _fallbackLocalizer[name, arguments];
            }
        }

        private string? GetTranslationFromDb(string key)
        {
            try
            {
                var culture = CultureInfo.CurrentUICulture.Name;
                using var context = _dbContextFactory.CreateDbContext();

                var translation = context.DynamicTranslations
                    .Include(t => t.Language)
                    .FirstOrDefault(t => t.FieldKey == key && (t.Language.Code == culture || t.Language.Code == culture.Split('-', StringSplitOptions.None)[0]));

                return translation?.TranslatedValue;
            }
            catch
            {
                return null;
            }
        }

        public IEnumerable<LocalizedString> GetAllStrings(bool includeParentCultures)
        {
            return _fallbackLocalizer.GetAllStrings(includeParentCultures);
        }
    }
}
