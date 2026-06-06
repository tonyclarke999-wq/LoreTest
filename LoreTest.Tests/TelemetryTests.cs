using System;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using Moq;
using LoreTest.Data;
using LoreTest.Utilities;

namespace LoreTest.Tests
{
    [TestClass]
    public class TelemetryTests
    {
        private DbContextOptions<ApplicationDbContext> _options = null!;
        private Mock<IServiceProvider> _serviceProviderMock = null!;
        private Mock<IDbContextFactory<ApplicationDbContext>> _dbFactoryMock = null!;

        [TestInitialize]
        public void Setup()
        {
            _options = new DbContextOptionsBuilder<ApplicationDbContext>()
                .UseInMemoryDatabase(databaseName: Guid.NewGuid().ToString())
                .Options;

            _serviceProviderMock = new Mock<IServiceProvider>();
            _dbFactoryMock = new Mock<IDbContextFactory<ApplicationDbContext>>();

            // Setup DB Factory to return a fresh in-memory DbContext
            _dbFactoryMock.Setup(x => x.CreateDbContextAsync(default))
                .ReturnsAsync(() => new ApplicationDbContext(_options, _serviceProviderMock.Object));
        }

        private async Task SetupTelemetrySettings(string level)
        {
            using var context = new ApplicationDbContext(_options, _serviceProviderMock.Object);
            var settings = new AppSettings { TelemetryLevel = level };
            context.AppSettings.Add(settings);
            await context.SaveChangesAsync();
        }

        [TestMethod]
        public async Task LogActivityAsync_ShouldWriteNothing_WhenTelemetryLevelIsNone()
        {
            // Arrange
            await SetupTelemetrySettings("None");
            var service = new UserActivityService(_dbFactoryMock.Object);

            // Act
            await service.LogActivityAsync("testuser", "123", "Login", "User logged in");
            await service.LogActivityAsync("testuser", "123", "PageView", "/projects");

            // Assert
            using var assertContext = new ApplicationDbContext(_options, _serviceProviderMock.Object);
            var activities = await assertContext.UserActivities.ToListAsync();
            Assert.AreEqual(0, activities.Count, "No activity logs should be recorded when TelemetryLevel is 'None'.");
        }

        [TestMethod]
        public async Task LogActivityAsync_ShouldLogOnlyLoginsAndLogouts_WhenTelemetryLevelIsLoginOnly()
        {
            // Arrange
            await SetupTelemetrySettings("LoginOnly");
            var service = new UserActivityService(_dbFactoryMock.Object);

            // Act
            await service.LogActivityAsync("testuser", "123", "Login", "User logged in");
            await service.LogActivityAsync("testuser", "123", "PageView", "/projects");
            await service.LogActivityAsync("testuser", "123", "Logout", "User logged out");

            // Assert
            using var assertContext = new ApplicationDbContext(_options, _serviceProviderMock.Object);
            var activities = await assertContext.UserActivities.ToListAsync();
            
            Assert.AreEqual(2, activities.Count, "Exactly 2 activities (Login, Logout) should be recorded.");
            Assert.IsTrue(activities.Any(a => a.Action == "Login"), "Login action should be logged.");
            Assert.IsTrue(activities.Any(a => a.Action == "Logout"), "Logout action should be logged.");
            Assert.IsFalse(activities.Any(a => a.Action == "PageView"), "PageView action should NOT be logged under LoginOnly.");
        }

        [TestMethod]
        public async Task LogActivityAsync_ShouldLogAllActivitiesIncludingPageViews_WhenTelemetryLevelIsFull()
        {
            // Arrange
            await SetupTelemetrySettings("Full");
            var service = new UserActivityService(_dbFactoryMock.Object);
            var testUserAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0";

            // Act
            await service.LogActivityAsync("testuser", "123", "Login", "User logged in");
            await service.LogActivityAsync("testuser", "123", "PageView", "/projects", testUserAgent);
            await service.LogActivityAsync("testuser", "123", "Search", "Search query: projects");

            // Assert
            using var assertContext = new ApplicationDbContext(_options, _serviceProviderMock.Object);
            var activities = await assertContext.UserActivities.ToListAsync();

            Assert.AreEqual(3, activities.Count, "All 3 activities should be logged.");
            
            var pageView = activities.FirstOrDefault(a => a.Action == "PageView");
            Assert.IsNotNull(pageView, "PageView should be logged.");
            Assert.AreEqual("/projects", pageView.Details);
            Assert.AreEqual(testUserAgent, pageView.UserAgent, "User-Agent should be recorded for PageView.");
            
            var search = activities.FirstOrDefault(a => a.Action == "Search");
            Assert.IsNotNull(search, "Search action should be logged.");
            Assert.IsNull(search.UserAgent, "User-Agent should not be recorded for actions other than PageView.");
        }
    }
}
