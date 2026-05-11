using Microsoft.Extensions.Localization;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Options;

namespace LoreTest.Data
{
    public class DynamicLocalizerFactory(
            IOptions<LocalizationOptions> localizationOptions,
            IDbContextFactory<ApplicationDbContext> dbContextFactory,
            ILoggerFactory loggerFactory) : IStringLocalizerFactory
    {
        private readonly ResourceManagerStringLocalizerFactory _resourceFactory = new(localizationOptions, loggerFactory);
        private readonly IDbContextFactory<ApplicationDbContext> _dbContextFactory = dbContextFactory;

        public IStringLocalizer Create(Type resourceSource)
        {
            var fallback = _resourceFactory.Create(resourceSource);
            return new DynamicLocalizer(fallback, _dbContextFactory);
        }

        public IStringLocalizer Create(string baseName, string location)
        {
            var fallback = _resourceFactory.Create(baseName, location);
            return new DynamicLocalizer(fallback, _dbContextFactory);
        }
    }
}
