using Bunit;
using Bunit.TestDoubles;
using LoreTest.Components.Pages.Projects;
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
            var cut = RenderComponent<LoreTest.Components.Pages.Projects.Index>();

            // Assert
            StringAssert.Contains(cut.Markup, "Loading");
            
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
            var cut = RenderComponent<LoreTest.Components.Pages.Projects.Index>();

            // assert
            StringAssert.Contains(cut.Markup, "TestProjects");
            StringAssert.Contains(cut.Markup, "NoProjectsFound");
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
            var cut = RenderComponent<LoreTest.Components.Pages.Projects.Index>();

            // Assert
            var rows = cut.FindAll("tbody tr");
            Assert.HasCount(2, rows);
            StringAssert.Contains(cut.Markup, "Project 1");
            StringAssert.Contains(cut.Markup, "Project 2");
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
            var cut = RenderComponent<LoreTest.Components.Pages.Projects.Index>();

            // Assert
            StringAssert.Contains(cut.Markup, "CreateNew");
        }

        private Mock<UserManager<ApplicationUser>> CreateUserManagerMock()
        {
            var store = new Mock<IUserStore<ApplicationUser>>();
            return new Mock<UserManager<ApplicationUser>>(
                store.Object, null!, null!, null!, null!, null!, null!, null!, null!);
        }
    }
}
