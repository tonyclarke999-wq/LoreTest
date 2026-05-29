using System;
using System.Net;
using System.Net.Http;
using System.Text.Json;
using System.Threading;
using System.Threading.Tasks;
using Moq;
using Moq.Protected;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using LoreTest.Utilities;
using LoreTest.Data;

namespace LoreTest.Tests
{
    [TestClass]
    public class JiraIntegrationTests
    {
        private Mock<HttpMessageHandler> _handlerMock = null!;
        private HttpClient _httpClient = null!;
        private JiraIntegrationService _service = null!;
        private AppSettings _settings = null!;

        [TestInitialize]
        public void Setup()
        {
            _handlerMock = new Mock<HttpMessageHandler>();
            _httpClient = new HttpClient(_handlerMock.Object);
            _service = new JiraIntegrationService(_httpClient);
            _settings = new AppSettings
            {
                JiraBaseUrl = "https://test.atlassian.net",
                JiraApiToken = "my-api-token",
                JiraEmail = "user@test.com"
            };
        }

        [TestCleanup]
        public void Cleanup()
        {
            _httpClient.Dispose();
        }

        [TestMethod]
        public async Task FetchIssueDetailsAsync_Success_ReturnsDetails()
        {
            // Arrange
            var responseContent = JsonSerializer.Serialize(new
            {
                fields = new
                {
                    summary = "Test Issue Summary",
                    description = "Test Issue Description\nLine 2"
                }
            });

            _handlerMock.Protected()
                .Setup<Task<HttpResponseMessage>>(
                    "SendAsync",
                    ItExpr.Is<HttpRequestMessage>(req => 
                        req.Method == HttpMethod.Get && 
                        req.RequestUri!.ToString().Contains("/rest/api/2/issue/PROJ-123")),
                    ItExpr.IsAny<CancellationToken>()
                )
                .ReturnsAsync(new HttpResponseMessage
                {
                    StatusCode = HttpStatusCode.OK,
                    Content = new StringContent(responseContent)
                });

            // Act
            var result = await _service.FetchIssueDetailsAsync("PROJ-123", _settings);

            // Assert
            Assert.IsNotNull(result);
            Assert.AreEqual("Test Issue Summary", result.Value.Title);
            Assert.AreEqual("Test Issue Description<br>Line 2", result.Value.Description);
        }

        [TestMethod]
        public async Task FetchIssueDetailsAsync_NotFound_ThrowsException()
        {
            // Arrange
            _handlerMock.Protected()
                .Setup<Task<HttpResponseMessage>>(
                    "SendAsync",
                    ItExpr.IsAny<HttpRequestMessage>(),
                    ItExpr.IsAny<CancellationToken>()
                )
                .ReturnsAsync(new HttpResponseMessage
                {
                    StatusCode = HttpStatusCode.NotFound
                });

            // Act & Assert
            bool threw = false;
            try
            {
                await _service.FetchIssueDetailsAsync("PROJ-999", _settings);
            }
            catch (Exception)
            {
                threw = true;
            }

            Assert.IsTrue(threw, "Expected FetchIssueDetailsAsync to throw an exception on NotFound, but it did not.");
        }

        [TestMethod]
        public async Task CreateBugAsync_PlainProjectKey_CreatesBugWithoutLink()
        {
            // Arrange
            var bug = new Bug
            {
                Title = "UI Crash",
                Description = "Steps to repro..."
            };

            var postResponseContent = JsonSerializer.Serialize(new
            {
                key = "QA-5"
            });

            _handlerMock.Protected()
                .Setup<Task<HttpResponseMessage>>(
                    "SendAsync",
                    ItExpr.Is<HttpRequestMessage>(req => 
                        req.Method == HttpMethod.Post && 
                        req.RequestUri!.ToString().Contains("/rest/api/2/issue")),
                    ItExpr.IsAny<CancellationToken>()
                )
                .ReturnsAsync(new HttpResponseMessage
                {
                    StatusCode = HttpStatusCode.Created,
                    Content = new StringContent(postResponseContent)
                });

            // Act
            var result = await _service.CreateBugAsync(bug, "QA", _settings);

            // Assert
            Assert.AreEqual("QA-5", result);
            
            // Verify only one call was made (no linking call)
            _handlerMock.Protected().Verify(
                "SendAsync",
                Times.Once(),
                ItExpr.IsAny<HttpRequestMessage>(),
                ItExpr.IsAny<CancellationToken>()
            );
        }

        [TestMethod]
        public async Task CreateBugAsync_IssueKey_CreatesBugWithLink()
        {
            // Arrange
            var bug = new Bug
            {
                Title = "Memory Leak",
                Description = "Occurs on startup."
            };

            var parentDetailsResponse = JsonSerializer.Serialize(new
            {
                fields = new
                {
                    summary = "Parent Issue",
                    description = "Parent Desc",
                    project = new { key = "QA" }
                }
            });

            var bugCreateResponse = JsonSerializer.Serialize(new
            {
                key = "QA-22"
            });

            // 1. Setup GET parent details
            _handlerMock.Protected()
                .Setup<Task<HttpResponseMessage>>(
                    "SendAsync",
                    ItExpr.Is<HttpRequestMessage>(req => 
                        req.Method == HttpMethod.Get && 
                        req.RequestUri!.ToString().Contains("/rest/api/2/issue/QA-1")),
                    ItExpr.IsAny<CancellationToken>()
                )
                .ReturnsAsync(new HttpResponseMessage
                {
                    StatusCode = HttpStatusCode.OK,
                    Content = new StringContent(parentDetailsResponse)
                });

            // 2. Setup POST create bug
            _handlerMock.Protected()
                .Setup<Task<HttpResponseMessage>>(
                    "SendAsync",
                    ItExpr.Is<HttpRequestMessage>(req => 
                        req.Method == HttpMethod.Post && 
                        req.RequestUri!.ToString().Contains("/rest/api/2/issue") && 
                        !req.RequestUri!.ToString().Contains("/issueLink")),
                    ItExpr.IsAny<CancellationToken>()
                )
                .ReturnsAsync(new HttpResponseMessage
                {
                    StatusCode = HttpStatusCode.Created,
                    Content = new StringContent(bugCreateResponse)
                });

            // 3. Setup POST link issue
            _handlerMock.Protected()
                .Setup<Task<HttpResponseMessage>>(
                    "SendAsync",
                    ItExpr.Is<HttpRequestMessage>(req => 
                        req.Method == HttpMethod.Post && 
                        req.RequestUri!.ToString().Contains("/rest/api/2/issueLink")),
                    ItExpr.IsAny<CancellationToken>()
                )
                .ReturnsAsync(new HttpResponseMessage
                {
                    StatusCode = HttpStatusCode.Created
                });

            // Act
            var result = await _service.CreateBugAsync(bug, "QA-1", _settings);

            // Assert
            Assert.AreEqual("QA-22", result);

            // Verify three calls total were made (GET details, POST issue, POST link)
            _handlerMock.Protected().Verify(
                "SendAsync",
                Times.Exactly(3),
                ItExpr.IsAny<HttpRequestMessage>(),
                ItExpr.IsAny<CancellationToken>()
            );
        }

        [TestMethod]
        public async Task CreateBugAsync_IssueKeyParentFetchFails_FallsBackToManualParsing()
        {
            // Arrange
            var bug = new Bug
            {
                Title = "Visual Glitch",
                Description = "Wrong color."
            };

            var bugCreateResponse = JsonSerializer.Serialize(new
            {
                key = "LORE-99"
            });

            // 1. Setup GET parent details to fail (simulate Unauthorized or Forbidden)
            _handlerMock.Protected()
                .Setup<Task<HttpResponseMessage>>(
                    "SendAsync",
                    ItExpr.Is<HttpRequestMessage>(req => 
                        req.Method == HttpMethod.Get && 
                        req.RequestUri!.ToString().Contains("/rest/api/2/issue/LORE-12")),
                    ItExpr.IsAny<CancellationToken>()
                )
                .ReturnsAsync(new HttpResponseMessage
                {
                    StatusCode = HttpStatusCode.Forbidden
                });

            // 2. Setup POST create bug (verifies project key 'LORE' was parsed manually)
            _handlerMock.Protected()
                .Setup<Task<HttpResponseMessage>>(
                    "SendAsync",
                    ItExpr.Is<HttpRequestMessage>(req => 
                        req.Method == HttpMethod.Post && 
                        req.RequestUri!.ToString().Contains("/rest/api/2/issue") && 
                        !req.RequestUri!.ToString().Contains("/issueLink")),
                    ItExpr.IsAny<CancellationToken>()
                )
                .ReturnsAsync(new HttpResponseMessage
                {
                    StatusCode = HttpStatusCode.Created,
                    Content = new StringContent(bugCreateResponse)
                });

            // 3. Setup POST link issue to fail or succeed (we will verify it is called)
            _handlerMock.Protected()
                .Setup<Task<HttpResponseMessage>>(
                    "SendAsync",
                    ItExpr.Is<HttpRequestMessage>(req => 
                        req.Method == HttpMethod.Post && 
                        req.RequestUri!.ToString().Contains("/rest/api/2/issueLink")),
                    ItExpr.IsAny<CancellationToken>()
                )
                .ReturnsAsync(new HttpResponseMessage
                {
                    StatusCode = HttpStatusCode.Created
                });

            // Act
            var result = await _service.CreateBugAsync(bug, "LORE-12", _settings);

            // Assert
            Assert.AreEqual("LORE-99", result);
        }
    }
}
