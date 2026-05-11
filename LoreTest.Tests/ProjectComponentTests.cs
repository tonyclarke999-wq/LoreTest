#nullable enable
using Bunit;
using Bunit.TestDoubles;
using LoreTest.Data;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;
using Moq;
using Microsoft.Extensions.Localization;
using LoreTest.Resources;
using System.Security.Claims;

namespace LoreTest.Tests
{
    [TestClass]
    public class ProjectComponentTests : Bunit.TestContext
    {
        private DbContextOptions<ApplicationDbContext> _options = null!;

        [TestInitialize]
        public void Setup()
        {
            _options = new DbContextOptionsBuilder<ApplicationDbContext>()
                .UseInMemoryDatabase(databaseName: Guid.NewGuid().ToString())
                .Options;

            // Setup common localization mock
            var localizerMock = new Mock<IStringLocalizer<SharedResource>>();
            localizerMock.Setup(x => x[It.IsAny<string>()]).Returns((string key) => new LocalizedString(key, key));
            Services.AddSingleton(localizerMock.Object);
        }

        [TestMethod]
        public void ProjectIndex_ShouldDisplayLoading_WhenProjectsAreNull()
        {
            // Arrange
            var auth = this.AddTestAuthorization();
            auth.SetAuthorized("TestUser");

            var tcs = new TaskCompletionSource<ApplicationUser?>();
            var userManagerMock = CreateUserManagerMock();
            userManagerMock.Setup(x => x.GetUserAsync(It.IsAny<ClaimsPrincipal>()))
                .Returns(tcs.Task);
            Services.AddSingleton(userManagerMock.Object);

            using var context = new ApplicationDbContext(_options, new Mock<IServiceProvider>().Object);
            Services.AddSingleton(context);

            // Act
            var cut = RenderComponent<global::LoreTest.Components.Pages.Projects.Index>();

            // Assert
            Assert.Contains("Loading", cut.Markup);
            
            // Cleanup to avoid hanging tasks
            tcs.SetResult(null);
        }

        [TestMethod]
        public void ProjectIndex_ShouldDisplayNoProjectsFound_WhenDatabaseIsEmpty()
        {
            // Arrange
            var auth = this.AddTestAuthorization();
            auth.SetAuthorized("TestUser");

            var userManagerMock = CreateUserManagerMock();
            Services.AddSingleton(userManagerMock.Object);

            using var context = new ApplicationDbContext(_options, new Mock<IServiceProvider>().Object);
            Services.AddSingleton(context);

            // Act
            var cut = RenderComponent<global::LoreTest.Components.Pages.Projects.Index>();

            // assert
            Assert.Contains("TestProjects", cut.Markup);
            Assert.Contains("NoProjectsFound", cut.Markup);
        }

        [TestMethod]
        public void ProjectIndex_ShouldDisplayProjects_WhenDatabaseHasData()
        {
            // Arrange
            var auth = this.AddTestAuthorization();
            auth.SetAuthorized("TestUser");

            var userManagerMock = CreateUserManagerMock();
            Services.AddSingleton(userManagerMock.Object);

            using var context = new ApplicationDbContext(_options, new Mock<IServiceProvider>().Object);
            context.TestProjects.Add(new TestProject { Title = "Project 1", Description = "Desc 1" });
            context.TestProjects.Add(new TestProject { Title = "Project 2", Description = "Desc 2" });
            context.SaveChanges();
            Services.AddSingleton(context);

            // Act
            var cut = RenderComponent<global::LoreTest.Components.Pages.Projects.Index>();

            // Assert
            var rows = cut.FindAll("tbody tr");
            Assert.HasCount(2, rows);
            Assert.Contains("Project 1", cut.Markup);
            Assert.Contains("Project 2", cut.Markup);
        }

        [TestMethod]
        public void ProjectIndex_ShouldShowCreateButton_WhenUserIsEditor()
        {
            // Arrange
            var auth = this.AddTestAuthorization();
            auth.SetAuthorized("EditorUser");

            var user = new ApplicationUser { UserName = "EditorUser", Role = "Editor" };
            var userManagerMock = CreateUserManagerMock();
            userManagerMock.Setup(x => x.GetUserAsync(It.IsAny<ClaimsPrincipal>()))
                .ReturnsAsync(user);
            Services.AddSingleton(userManagerMock.Object);

            using var context = new ApplicationDbContext(_options, new Mock<IServiceProvider>().Object);
            Services.AddSingleton(context);

            // Act
            var cut = RenderComponent<global::LoreTest.Components.Pages.Projects.Index>();

            // Assert
            Assert.Contains("CreateNew", cut.Markup);
        }

        private static Mock<UserManager<ApplicationUser>> CreateUserManagerMock()
        {
            var store = new Mock<IUserStore<ApplicationUser>>();
            return new Mock<UserManager<ApplicationUser>>(
                store.Object, null!, null!, null!, null!, null!, null!, null!, null!);
        }
    }
}
