using Microsoft.Extensions.Localization;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Options;

namespace LoreTest.Data
{
    public class DynamicLocalizerFactory : IStringLocalizerFactory
    {
        private readonly ResourceManagerStringLocalizerFactory _resourceFactory;
        private readonly IDbContextFactory<ApplicationDbContext> _dbContextFactory;

        public DynamicLocalizerFactory(
            IOptions<LocalizationOptions> localizationOptions,
            IDbContextFactory<ApplicationDbContext> dbContextFactory,
            ILoggerFactory loggerFactory)
        {
            _resourceFactory = new ResourceManagerStringLocalizerFactory(localizationOptions, loggerFactory);
            _dbContextFactory = dbContextFactory;
        }

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
