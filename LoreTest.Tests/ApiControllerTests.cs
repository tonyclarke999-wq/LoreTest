using System;
using System.Collections.Generic;
using System.Security.Claims;
using System.Threading.Tasks;
using LoreTest.Controllers;
using LoreTest.Data;
using LoreTest.Utilities;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;
using Microsoft.AspNetCore.Authentication;
using Moq;

namespace LoreTest.Tests
{
    [TestClass]
    public class ApiControllerTests
    {
        private DbContextOptions<ApplicationDbContext> _dbOptions = null!;
        private Mock<UserManager<ApplicationUser>> _userManagerMock = null!;
        private Mock<SignInManager<ApplicationUser>> _signInManagerMock = null!;
        private Mock<IConfiguration> _configMock = null!;
        private Mock<JiraIntegrationService> _jiraServiceMock = null!;

        [TestInitialize]
        public void Setup()
        {
            _dbOptions = new DbContextOptionsBuilder<ApplicationDbContext>()
                .UseInMemoryDatabase(databaseName: Guid.NewGuid().ToString())
                .Options;

            var store = new Mock<IUserStore<ApplicationUser>>();
            _userManagerMock = new Mock<UserManager<ApplicationUser>>(
                store.Object, null!, null!, null!, null!, null!, null!, null!, null!);

            var contextAccessorMock = new Mock<IHttpContextAccessor>();
            var claimsFactoryMock = new Mock<IUserClaimsPrincipalFactory<ApplicationUser>>();
            var optionsMock = new Mock<IOptions<IdentityOptions>>();
            optionsMock.Setup(o => o.Value).Returns(new IdentityOptions());
            var loggerMock = new Mock<ILogger<SignInManager<ApplicationUser>>>();
            var schemesMock = new Mock<IAuthenticationSchemeProvider>();
            var confirmationMock = new Mock<IUserConfirmation<ApplicationUser>>();

            _signInManagerMock = new Mock<SignInManager<ApplicationUser>>(
                _userManagerMock.Object,
                contextAccessorMock.Object,
                claimsFactoryMock.Object,
                optionsMock.Object,
                loggerMock.Object,
                schemesMock.Object,
                confirmationMock.Object
            );

            _configMock = new Mock<IConfiguration>();
            // Setup default JWT config
            var sectionMock = new Mock<IConfigurationSection>();
            sectionMock.Setup(s => s.Key).Returns("Jwt");
            sectionMock.Setup(s => s.Value).Returns((string)null!);

            var secretSection = new Mock<IConfigurationSection>();
            secretSection.Setup(s => s.Value).Returns("SuperSecretKeyThatIsAtLeast32CharactersLong!");
            sectionMock.Setup(s => s.GetSection("Secret")).Returns(secretSection.Object);

            var issuerSection = new Mock<IConfigurationSection>();
            issuerSection.Setup(s => s.Value).Returns("LoreTestAPI");
            sectionMock.Setup(s => s.GetSection("Issuer")).Returns(issuerSection.Object);

            var audienceSection = new Mock<IConfigurationSection>();
            audienceSection.Setup(s => s.Value).Returns("LoreTestAPIUsers");
            sectionMock.Setup(s => s.GetSection("Audience")).Returns(audienceSection.Object);

            var expirySection = new Mock<IConfigurationSection>();
            expirySection.Setup(s => s.Value).Returns("60");
            sectionMock.Setup(s => s.GetSection("ExpiryInMinutes")).Returns(expirySection.Object);

            _configMock.Setup(c => c.GetSection("Jwt")).Returns(sectionMock.Object);

            var httpClient = new System.Net.Http.HttpClient();
            _jiraServiceMock = new Mock<JiraIntegrationService>(httpClient);
        }

        [TestMethod]
        public async Task Auth_Login_WithValidCredentials_ReturnsJwtToken()
        {
            // Arrange
            var testUser = new ApplicationUser
            {
                Id = "user-123",
                Email = "test@example.com",
                UserName = "testuser",
                PreferredLanguage = "en",
                Role = "Admin"
            };

            _userManagerMock.Setup(m => m.FindByEmailAsync(It.IsAny<string>()))
                .ReturnsAsync(testUser);
            _signInManagerMock.Setup(m => m.CheckPasswordSignInAsync(testUser, "CorrectPassword", true))
                .ReturnsAsync(Microsoft.AspNetCore.Identity.SignInResult.Success);
            var controller = new AuthController(_userManagerMock.Object, _signInManagerMock.Object, _configMock.Object);

            // Act
            var result = await controller.Login(new LoginRequest
            {
                Email = "test@example.com",
                Password = "CorrectPassword"
            });

            // Assert
            Assert.IsInstanceOfType(result, typeof(OkObjectResult));
            var okResult = (OkObjectResult)result;
            var response = (LoginResponse)okResult.Value!;
            Assert.IsNotNull(response.Token);
            Assert.AreEqual("test-user-id", "test-user-id"); // dummy check to keep compiler happy
            Assert.AreEqual("user-123", response.User.Id);
            Assert.AreEqual("Admin", response.User.Role);
        }

        [TestMethod]
        public async Task Auth_Login_WithInvalidCredentials_ReturnsUnauthorized()
        {
            // Arrange
            _userManagerMock.Setup(m => m.FindByEmailAsync(It.IsAny<string>()))
                .ReturnsAsync((ApplicationUser)null!);

            var controller = new AuthController(_userManagerMock.Object, _signInManagerMock.Object, _configMock.Object);

            // Act
            var result = await controller.Login(new LoginRequest
            {
                Email = "wrong@example.com",
                Password = "WrongPassword"
            });

            // Assert
            Assert.IsInstanceOfType(result, typeof(UnauthorizedObjectResult));
        }

        [TestMethod]
        public async Task Projects_GetProjects_ReturnsAllProjects()
        {
            // Arrange
            using var context = new ApplicationDbContext(_dbOptions, new Mock<IServiceProvider>().Object);
            context.TestProjects.Add(new TestProject { Title = "Project 1", Description = "Desc 1" });
            context.TestProjects.Add(new TestProject { Title = "Project 2", Description = "Desc 2" });
            await context.SaveChangesAsync();

            var controller = new ProjectsController(context);

            // Act
            var result = await controller.GetProjects();

            // Assert
            Assert.IsInstanceOfType(result.Result, typeof(OkObjectResult));
            var okResult = (OkObjectResult)result.Result!;
            var projects = (List<TestProjectDto>)okResult.Value!;
            Assert.AreEqual(2, projects.Count);
            Assert.AreEqual("Project 1", projects[0].Title);
        }

        [TestMethod]
        public async Task Projects_CreateProject_AsAdmin_Succeeds()
        {
            // Arrange
            using var context = new ApplicationDbContext(_dbOptions, new Mock<IServiceProvider>().Object);
            var controller = new ProjectsController(context);

            var userPrincipal = new ClaimsPrincipal(new ClaimsIdentity(new[]
            {
                new Claim(ClaimTypes.NameIdentifier, "user-123"),
                new Claim(ClaimTypes.Role, "Admin")
            }, "TestAuth"));

            controller.ControllerContext = new ControllerContext
            {
                HttpContext = new DefaultHttpContext { User = userPrincipal }
            };

            // Act
            var result = await controller.CreateProject(new CreateTestProjectDto
            {
                Title = "New API Project",
                Description = "Created via API"
            });

            // Assert
            Assert.IsInstanceOfType(result.Result, typeof(CreatedAtActionResult));
            var createdResult = (CreatedAtActionResult)result.Result!;
            var projectDto = (TestProjectDto)createdResult.Value!;
            Assert.AreEqual("New API Project", projectDto.Title);

            var dbProject = await context.TestProjects.FirstOrDefaultAsync(p => p.Id == projectDto.Id);
            Assert.IsNotNull(dbProject);
            Assert.AreEqual("New API Project", dbProject.Title);
        }

        [TestMethod]
        public async Task Projects_CreateProject_AsViewer_ReturnsForbid()
        {
            // Arrange
            using var context = new ApplicationDbContext(_dbOptions, new Mock<IServiceProvider>().Object);
            var controller = new ProjectsController(context);

            var userPrincipal = new ClaimsPrincipal(new ClaimsIdentity(new[]
            {
                new Claim(ClaimTypes.NameIdentifier, "user-123"),
                new Claim(ClaimTypes.Role, "Viewer")
            }, "TestAuth"));

            controller.ControllerContext = new ControllerContext
            {
                HttpContext = new DefaultHttpContext { User = userPrincipal }
            };

            // Act
            var result = await controller.CreateProject(new CreateTestProjectDto
            {
                Title = "Viewer Project",
                Description = "Should fail"
            });

            // Assert
            Assert.IsInstanceOfType(result.Result, typeof(ForbidResult));
        }

        [TestMethod]
        public async Task Bugs_CreateBug_WithJiraSync_CallsJiraService()
        {
            // Arrange
            using var context = new ApplicationDbContext(_dbOptions, new Mock<IServiceProvider>().Object);
            
            // Seed settings and a project
            var settings = new AppSettings
            {
                JiraBaseUrl = "https://jira.example.com",
                JiraEmail = "user@example.com",
                JiraApiToken = "token-123"
            };
            context.AppSettings.Add(settings);

            var project = new TestProject
            {
                Title = "Jira Project",
                JiraReference = "PROJ"
            };
            context.TestProjects.Add(project);
            await context.SaveChangesAsync();

            _jiraServiceMock.Setup(j => j.CreateBugAsync(It.IsAny<Bug>(), "PROJ", It.IsAny<AppSettings>()))
                .ReturnsAsync("PROJ-999");

            var controller = new BugsController(context, _jiraServiceMock.Object);

            var userPrincipal = new ClaimsPrincipal(new ClaimsIdentity(new[]
            {
                new Claim(ClaimTypes.NameIdentifier, "user-123"),
                new Claim(ClaimTypes.Role, "Editor")
            }, "TestAuth"));

            controller.ControllerContext = new ControllerContext
            {
                HttpContext = new DefaultHttpContext { User = userPrincipal }
            };

            // Act
            var result = await controller.CreateBug(new CreateBugDto
            {
                Title = "Critical Bug",
                Description = "Some crash",
                Severity = "Critical",
                Priority = "High",
                ProjectId = project.Id,
                SyncToJira = true
            });

            // Assert
            Assert.IsInstanceOfType(result.Result, typeof(CreatedAtActionResult));
            var createdResult = (CreatedAtActionResult)result.Result!;
            var bugDto = (BugDto)createdResult.Value!;
            Assert.AreEqual("PROJ-999", bugDto.JiraBugReference);

            _jiraServiceMock.Verify(j => j.CreateBugAsync(It.IsAny<Bug>(), "PROJ", It.IsAny<AppSettings>()), Times.Once);
        }

        [TestMethod]
        [Ignore("Local developer database seeder utility - requires active local PostgreSQL database.")]
        public async Task Seeder_DatabaseAdminUser()
        {
            var optionsBuilder = new DbContextOptionsBuilder<ApplicationDbContext>();
            optionsBuilder.UseNpgsql("Host=localhost;Port=5432;Database=loretest;Username=postgres;Password=HomePlate4");
            
            using var context = new ApplicationDbContext(optionsBuilder.Options, new Mock<IServiceProvider>().Object);
            
            var users = await context.Users.ToListAsync();
            Console.WriteLine("=== REGISTERED USERS IN DATABASE ===");
            foreach (var u in users)
            {
                Console.WriteLine($"ID: {u.Id}, Email: {u.Email}, Role: {u.Role}, Confirmed: {u.EmailConfirmed}");
            }

            var adminEmail = "tonyclarke999@gmail.com";
            var adminUser = await context.Users.FirstOrDefaultAsync(u => u.Email == adminEmail);
            var hasher = new PasswordHasher<ApplicationUser>();

            if (adminUser == null)
            {
                Console.WriteLine($"Admin '{adminEmail}' not found. Creating it...");
                var newAdmin = new ApplicationUser
                {
                    Id = Guid.NewGuid().ToString(),
                    Email = adminEmail,
                    NormalizedEmail = adminEmail.ToUpperInvariant(),
                    UserName = adminEmail,
                    NormalizedUserName = adminEmail.ToUpperInvariant(),
                    EmailConfirmed = true,
                    Role = "Administrator",
                    PreferredLanguage = "en",
                    StartDate = DateOnly.FromDateTime(DateTime.Today)
                };
                newAdmin.PasswordHash = hasher.HashPassword(newAdmin, "Password1-");
                context.Users.Add(newAdmin);
                await context.SaveChangesAsync();
                Console.WriteLine($"Successfully created admin user: {adminEmail} with Password1-");
            }
            else
            {
                Console.WriteLine($"Admin '{adminEmail}' exists. Forcing password reset to 'Password1-' and confirming email...");
                adminUser.EmailConfirmed = true;
                adminUser.Role = "Administrator";
                adminUser.PasswordHash = hasher.HashPassword(adminUser, "Password1-");
                context.Users.Update(adminUser);
                await context.SaveChangesAsync();
                Console.WriteLine("Successfully updated password hash and role.");
            }

            // Also reset admin@example.com to Password1- just in case
            var fallbackEmail = "admin@example.com";
            var fallbackUser = await context.Users.FirstOrDefaultAsync(u => u.Email == fallbackEmail);
            if (fallbackUser != null)
            {
                Console.WriteLine($"Force resetting fallback user '{fallbackEmail}' password to 'Password1-' and confirming email...");
                fallbackUser.EmailConfirmed = true;
                fallbackUser.Role = "Administrator";
                fallbackUser.PasswordHash = hasher.HashPassword(fallbackUser, "Password1-");
                context.Users.Update(fallbackUser);
                await context.SaveChangesAsync();
            }
        }

        [TestMethod]
        public async Task Auth_Login_LockedOut_Returns423Locked()
        {
            // Arrange
            var testUser = new ApplicationUser
            {
                Id = "user-123",
                Email = "test@example.com",
                UserName = "testuser",
                PreferredLanguage = "en",
                Role = "Admin"
            };

            _userManagerMock.Setup(m => m.FindByEmailAsync(It.IsAny<string>()))
                .ReturnsAsync(testUser);
            _signInManagerMock.Setup(m => m.CheckPasswordSignInAsync(testUser, "Password123", true))
                .ReturnsAsync(Microsoft.AspNetCore.Identity.SignInResult.LockedOut);

            var controller = new AuthController(_userManagerMock.Object, _signInManagerMock.Object, _configMock.Object);

            // Act
            var result = await controller.Login(new LoginRequest
            {
                Email = "test@example.com",
                Password = "Password123"
            });

            // Assert
            Assert.IsInstanceOfType(result, typeof(ObjectResult));
            var objectResult = (ObjectResult)result;
            Assert.AreEqual(StatusCodes.Status423Locked, objectResult.StatusCode);
            Assert.IsTrue(objectResult.Value!.ToString()!.Contains("locked"));
        }

        [TestMethod]
        public async Task Auth_Login_EnforcesFailedAttemptsLimit()
        {
            // Arrange
            var testUser = new ApplicationUser
            {
                Id = "user-123",
                Email = "test@example.com",
                UserName = "testuser",
                PreferredLanguage = "en",
                Role = "Admin"
            };

            _userManagerMock.Setup(m => m.FindByEmailAsync(It.IsAny<string>()))
                .ReturnsAsync(testUser);
            _signInManagerMock.Setup(m => m.CheckPasswordSignInAsync(testUser, "WrongPassword", true))
                .ReturnsAsync(Microsoft.AspNetCore.Identity.SignInResult.Failed);

            var controller = new AuthController(_userManagerMock.Object, _signInManagerMock.Object, _configMock.Object);

            // Act
            var result = await controller.Login(new LoginRequest
            {
                Email = "test@example.com",
                Password = "WrongPassword"
            });

            // Assert
            Assert.IsInstanceOfType(result, typeof(UnauthorizedObjectResult));
            _signInManagerMock.Verify(m => m.CheckPasswordSignInAsync(testUser, "WrongPassword", true), Times.Once);
        }
    }
}
