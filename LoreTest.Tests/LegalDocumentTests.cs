using Microsoft.AspNetCore.Components.Authorization;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;
using Moq;
using LoreTest.Data;
using LoreTest.Utilities;
using System.Security.Claims;

namespace LoreTest.Tests
{
    [TestClass]
    public class LegalDocumentTests
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

            var user = new ClaimsPrincipal(new ClaimsIdentity(new Claim[]
            {
                new Claim(ClaimTypes.Name, "AdminUser"),
                new Claim(ClaimTypes.NameIdentifier, "admin-123"),
                new Claim(ClaimTypes.Role, "Administrator")
            }, "mock"));

            _authStateProviderMock.Setup(x => x.GetAuthenticationStateAsync())
                .ReturnsAsync(new AuthenticationState(user));

            _serviceProviderMock.Setup(x => x.GetService(typeof(AuthenticationStateProvider)))
                .Returns(_authStateProviderMock.Object);
        }

        [TestMethod]
        public void GetDefaultPrivacyPolicy_ReturnsSelfHostedTemplateWithHighlightedPlaceholders()
        {
            // Act
            var privacyText = LegalDefaults.GetDefaultPrivacyPolicy();

            // Assert
            Assert.IsFalse(string.IsNullOrWhiteSpace(privacyText));
            Assert.IsTrue(privacyText.Contains("Self-Hosted Instance"));
            Assert.IsTrue(privacyText.Contains("MIT License"));
            Assert.IsTrue(privacyText.Contains("<mark>[Organization / Company Name]</mark>"));
            Assert.IsTrue(privacyText.Contains("Data Controller"));
        }

        [TestMethod]
        public void GetDefaultTermsAndConditions_ReturnsSelfHostedTemplateWithWarrantyDisclaimer()
        {
            // Act
            var termsText = LegalDefaults.GetDefaultTermsAndConditions();

            // Assert
            Assert.IsFalse(string.IsNullOrWhiteSpace(termsText));
            Assert.IsTrue(termsText.Contains("Self-Hosted Instance"));
            Assert.IsTrue(termsText.Contains("MIT License"));
            Assert.IsTrue(termsText.Contains("AS IS"));
            Assert.IsTrue(termsText.Contains("<mark>[Organization / Company Name]</mark>"));
        }

        [TestMethod]
        public void RenderMarkdownToHtml_ConvertsMarkdownAndHighlightsBracketedPlaceholders()
        {
            // Arrange
            var inputMarkdown = "## Policy for [Acme Corp]\r\nContact [admin@acme.com] for assistance.";

            // Act
            var html = LegalDefaults.RenderMarkdownToHtml(inputMarkdown);

            // Assert
            Assert.IsTrue(html.Contains("<h2"));
            Assert.IsTrue(html.Contains("Policy for"));
            Assert.IsTrue(html.Contains("<mark class=\"placeholder-highlight\">[Acme Corp]</mark>"));
            Assert.IsTrue(html.Contains("<mark class=\"placeholder-highlight\">[admin@acme.com]</mark>"));
        }

        [TestMethod]
        public async Task AppSettings_CanPersistAndAuditCustomLegalDocuments()
        {
            // Arrange
            using var context = new ApplicationDbContext(_options, _serviceProviderMock.Object);
            var settings = new AppSettings
            {
                TranslationApi = "Mock",
                TelemetryLevel = "LoginOnly",
                PrivacyPolicy = "## Custom Internal Privacy Policy",
                TermsAndConditions = "## Custom Internal Terms and Conditions"
            };

            // Act
            context.AppSettings.Add(settings);
            await context.SaveChangesAsync();

            // Assert persistence
            var retrieved = await context.AppSettings.FirstOrDefaultAsync();
            Assert.IsNotNull(retrieved);
            Assert.AreEqual("## Custom Internal Privacy Policy", retrieved.PrivacyPolicy);
            Assert.AreEqual("## Custom Internal Terms and Conditions", retrieved.TermsAndConditions);

            // Assert audit trail
            var auditLogs = await context.AuditLogs.Where(a => a.TableName == "AppSettings").ToListAsync();
            Assert.IsTrue(auditLogs.Any(a => a.ColumnName == "PrivacyPolicy" && a.NewValue == "## Custom Internal Privacy Policy"));
            Assert.IsTrue(auditLogs.Any(a => a.ColumnName == "TermsAndConditions" && a.NewValue == "## Custom Internal Terms and Conditions"));
        }
    }
}
