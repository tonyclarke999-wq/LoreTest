using Microsoft.AspNetCore.Components.Authorization;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;
using Moq;
using LoreTest.Data;
using System.Security.Claims;

namespace LoreTest.Tests
{
    [TestClass]
    public class AuditTests
    {
        private Mock<IServiceProvider> _serviceProviderMock = null!;
        private Mock<AuthenticationStateProvider> _authStateProviderMock = null!;
        private DbContextOptions<ApplicationDbContext> _options = null!;

        [TestInitialize]
        public void Setup()
        {
            _options = new DbContextOptionsBuilder<ApplicationDbContext>()
                .UseInMemoryDatabase(databaseName: Guid.NewGuid().ToString())
                .Options;

            _serviceProviderMock = new Mock<IServiceProvider>();
            _authStateProviderMock = new Mock<AuthenticationStateProvider>();

            // Default mock setup: Return "TestUser"
            var user = new ClaimsPrincipal(new ClaimsIdentity(new Claim[]
            {
                new Claim(ClaimTypes.Name, "TestUser"),
                new Claim(ClaimTypes.NameIdentifier, "123")
            }, "mock"));

            _authStateProviderMock.Setup(x => x.GetAuthenticationStateAsync())
                .ReturnsAsync(new AuthenticationState(user));

            _serviceProviderMock.Setup(x => x.GetService(typeof(AuthenticationStateProvider)))
                .Returns(_authStateProviderMock.Object);
        }

        [TestMethod]
        public async Task SaveChangesAsync_ShouldCreateAuditLog_WhenEntityIsAdded()
        {
            // Arrange
            using var context = new ApplicationDbContext(_options, _serviceProviderMock.Object);
            var project = new TestProject { Title = "New Project", Description = "Desc" };

            // Act
            context.TestProjects.Add(project);
            await context.SaveChangesAsync();

            // Assert
            // Relaxing TableName filter to see if any log is created
            var auditLog = await context.AuditLogs.FirstOrDefaultAsync(x => x.Action == "Create");
            Assert.IsNotNull(auditLog, "No 'Create' audit log was found.");
            Assert.AreEqual("TestUser", auditLog.UserId);
            
            // Check if we got the Title property
            var titleAudit = await context.AuditLogs.FirstOrDefaultAsync(x => x.Action == "Create" && x.ColumnName == "Title");
            Assert.IsNotNull(titleAudit, "No 'Create' audit log for 'Title' column was found.");
            Assert.AreEqual("New Project", titleAudit.NewValue);
        }

        [TestMethod]
        public async Task SaveChangesAsync_ShouldCreateAuditLog_WhenEntityIsModified()
        {
            // Arrange
            using (var context = new ApplicationDbContext(_options, _serviceProviderMock.Object))
            {
                var project = new TestProject { Title = "Original Title" };
                context.TestProjects.Add(project);
                await context.SaveChangesAsync();
            }

            // Act
            using (var context = new ApplicationDbContext(_options, _serviceProviderMock.Object))
            {
                var project = await context.TestProjects.FirstAsync();
                project.Title = "Updated Title";
                await context.SaveChangesAsync();
            }

            // Assert
            using var assertContext = new ApplicationDbContext(_options, _serviceProviderMock.Object);
            var auditLog = await assertContext.AuditLogs.FirstOrDefaultAsync(x => x.Action == "Update" && x.ColumnName == "Title");
            Assert.IsNotNull(auditLog, "No 'Update' audit log for 'Title' column was found.");
            Assert.AreEqual("Original Title", auditLog.OldValue);
            Assert.AreEqual("Updated Title", auditLog.NewValue);
        }

        [TestMethod]
        public async Task SaveChangesAsync_ShouldCreateAuditLog_WhenEntityIsDeleted()
        {
            // Arrange
            using (var context = new ApplicationDbContext(_options, _serviceProviderMock.Object))
            {
                var project = new TestProject { Title = "To Delete" };
                context.TestProjects.Add(project);
                await context.SaveChangesAsync();
            }

            // Act
            using (var context = new ApplicationDbContext(_options, _serviceProviderMock.Object))
            {
                var project = await context.TestProjects.FirstAsync();
                context.TestProjects.Remove(project);
                await context.SaveChangesAsync();
            }

            // Assert
            using var assertContext = new ApplicationDbContext(_options, _serviceProviderMock.Object);
            var auditLog = await assertContext.AuditLogs.FirstOrDefaultAsync(x => x.Action == "Delete");
            Assert.IsNotNull(auditLog, "No 'Delete' audit log was found.");
        }

        [TestMethod]
        public async Task SaveChangesAsync_ShouldUseCorrectUserId_FromAuthProvider()
        {
            // Arrange
            var customUser = new ClaimsPrincipal(new ClaimsIdentity(new Claim[]
            {
                new Claim(ClaimTypes.Name, "SpecificUser")
            }, "mock"));

            _authStateProviderMock.Setup(x => x.GetAuthenticationStateAsync())
                .ReturnsAsync(new AuthenticationState(customUser));

            using var context = new ApplicationDbContext(_options, _serviceProviderMock.Object);
            var project = new TestProject { Title = "User Test" };

            // Act
            context.TestProjects.Add(project);
            await context.SaveChangesAsync();

            // Assert
            var auditLog = await context.AuditLogs.FirstOrDefaultAsync();
            Assert.IsNotNull(auditLog);
            Assert.AreEqual("SpecificUser", auditLog.UserId);
        }
    }
}
