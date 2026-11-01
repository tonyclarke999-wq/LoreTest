using System.Net;
using System.Net.Http.Json;
using Microsoft.Playwright.MSTest;
using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace LoreTest.Playwright
{
    [TestClass]
    public class SecurityTests : PageTest
    {
        private string _baseUrl = "";
        private static readonly HttpClient SharedHttpClient = new(new HttpClientHandler
        {
            AllowAutoRedirect = false
        });

        [TestInitialize]
        public async Task Setup()
        {
            _baseUrl = Environment.GetEnvironmentVariable("BASE_URL") ?? "http://localhost:5001";

            // Start Playwright Tracing
            await Context.Tracing.StartAsync(new()
            {
                Screenshots = true,
                Snapshots = true,
                Sources = true
            });
        }

        [TestCleanup]
        public async Task Teardown()
        {
            var testName = TestContext.TestName;
            var isPassed = TestContext.CurrentTestOutcome == UnitTestOutcome.Passed;

            string resultsDir = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "allure-results");
            string targetDir = Path.Combine(resultsDir, Environment.MachineName);
            try
            {
                if (!Directory.Exists(targetDir))
                {
                    Directory.CreateDirectory(targetDir);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Failed to create allure-results directory: {ex.Message}");
                targetDir = resultsDir;
            }

            // 1. Capture Screenshot on Failure
            if (!isPassed)
            {
                try
                {
                    var screenshotPath = Path.Combine(targetDir, $"screenshot_{testName}.png");
                    await Page.ScreenshotAsync(new() { Path = screenshotPath, FullPage = true });
                    TestContext.AddResultFile(screenshotPath);
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"Failed to capture screenshot: {ex.Message}");
                }
            }

            // 2. Stop Tracing and attach to MSTest TestContext
            try
            {
                var tracePath = Path.Combine(targetDir, $"trace_{testName}.zip");
                await Context.Tracing.StopAsync(new() { Path = tracePath });
                if (File.Exists(tracePath))
                {
                    TestContext.AddResultFile(tracePath);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Failed to stop tracing: {ex.Message}");
            }
        }

        [TestMethod]
        public async Task SecurityHeaders_ShouldBePresentOnResponses()
        {
            var response = await Page.GotoAsync($"{_baseUrl}/Account/Login");
            Assert.IsNotNull(response, "Expected an HTTP response from /Account/Login.");

            var headers = response.Headers;

            Assert.IsTrue(
                headers.TryGetValue("x-content-type-options", out var contentTypeOpts) &&
                string.Equals(contentTypeOpts, "nosniff", StringComparison.OrdinalIgnoreCase),
                "Missing or invalid X-Content-Type-Options: nosniff header.");

            Assert.IsTrue(
                headers.TryGetValue("x-frame-options", out var frameOpts) &&
                string.Equals(frameOpts, "DENY", StringComparison.OrdinalIgnoreCase),
                "Missing or invalid X-Frame-Options: DENY header.");

            Assert.IsTrue(
                headers.TryGetValue("referrer-policy", out var referrerPolicy) &&
                !string.IsNullOrWhiteSpace(referrerPolicy),
                "Missing Referrer-Policy header.");

            Assert.IsTrue(
                headers.TryGetValue("permissions-policy", out var permissionsPolicy) &&
                !string.IsNullOrWhiteSpace(permissionsPolicy),
                "Missing Permissions-Policy header.");
        }

        [TestMethod]
        public async Task Api_ProtectedEndpoints_ShouldRejectUnauthenticatedAndTamperedJwt()
        {
            // 1. Request without Authorization header -> 401 Unauthorized
            using var unauthReq = new HttpRequestMessage(HttpMethod.Get, $"{_baseUrl}/api/projects");
            unauthReq.Headers.Add("X-Forwarded-For", "198.51.100.10");
            using var unauthResp = await SharedHttpClient.SendAsync(unauthReq);
            Assert.AreEqual(HttpStatusCode.Unauthorized, unauthResp.StatusCode,
                "GET /api/projects without token must return 401 Unauthorized.");

            // 2. Request with tampered/forged JWT signature -> 401 Unauthorized
            const string forgedToken =
                "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9." +
                "eyJuYW1laWQiOiJhdHRhY2tlciIsInJvbGUiOiJBZG1pbiIsImlzcyI6IkxvcmVUZXN0QVBJIiwiYXVkIjoiTG9yZVRlc3RBUElVc2VycyIsImV4cCI6OTk5OTk5OTk5OX0." +
                "forgedSignatureValue1234567890";

            using var forgedGetReq = new HttpRequestMessage(HttpMethod.Get, $"{_baseUrl}/api/projects");
            forgedGetReq.Headers.Add("Authorization", $"Bearer {forgedToken}");
            forgedGetReq.Headers.Add("X-Forwarded-For", "198.51.100.11");

            using var forgedGetResp = await SharedHttpClient.SendAsync(forgedGetReq);
            Assert.AreEqual(HttpStatusCode.Unauthorized, forgedGetResp.StatusCode,
                "GET /api/projects with a tampered JWT signature must return 401 Unauthorized.");

            using var forgedPostReq = new HttpRequestMessage(HttpMethod.Post, $"{_baseUrl}/api/projects")
            {
                Content = JsonContent.Create(new { Title = "Unauthorized Project", Description = "Should fail" })
            };
            forgedPostReq.Headers.Add("Authorization", $"Bearer {forgedToken}");
            forgedPostReq.Headers.Add("X-Forwarded-For", "198.51.100.12");

            using var forgedPostResp = await SharedHttpClient.SendAsync(forgedPostReq);
            Assert.IsTrue(
                forgedPostResp.StatusCode == HttpStatusCode.Unauthorized ||
                forgedPostResp.StatusCode == HttpStatusCode.BadRequest,
                $"POST /api/projects with a tampered JWT must be rejected (401/400), got {(int)forgedPostResp.StatusCode}.");
        }

        [TestMethod]
        public async Task OpenRedirect_CultureEndpoint_ShouldRejectExternalUrls()
        {
            using var formContent = new FormUrlEncodedContent(new Dictionary<string, string>
            {
                ["culture"] = "en",
                ["returnUrl"] = "https://evil.example.com/phishing"
            });

            using var response = await SharedHttpClient.PostAsync($"{_baseUrl}/Culture/SetCulture", formContent);

            // LocalRedirect throws InvalidOperationException on non-local URLs, preventing 302 redirect to external host
            if (response.Headers.Location != null)
            {
                var location = response.Headers.Location.ToString();
                Assert.IsFalse(
                    location.Contains("evil.example.com", StringComparison.OrdinalIgnoreCase),
                    $"Open redirect vulnerability detected! Redirected to: {location}");
            }
        }

        [TestMethod]
        public async Task XssPayload_InLoginInput_ShouldNotExecuteScript()
        {
            bool dialogTriggered = false;
            Page.Dialog += async (_, dialog) =>
            {
                dialogTriggered = true;
                await dialog.DismissAsync();
            };

            await Page.GotoAsync($"{_baseUrl}/Account/Login");
            await Page.FillAsync("input[id='Input.Email']", "<script>window.__xssExecuted=true;alert('xss');</script>@example.com");
            await Page.FillAsync("input[id='Input.Password']", "InvalidPassword123!");
            await Page.ClickAsync("button[type='submit']");

            var xssExecuted = await Page.EvaluateAsync<bool>("() => window.__xssExecuted === true");
            Assert.IsFalse(xssExecuted, "XSS script payload executed in the DOM context.");
            Assert.IsFalse(dialogTriggered, "Unexpected browser dialog triggered by XSS payload.");
        }

        [TestMethod]
        public async Task Api_RateLimiting_ShouldReturn429WhenBurstExceedsLimit()
        {
            // Use a dedicated synthetic IP in X-Forwarded-For so this test has its own isolated rate-limit bucket
            var syntheticIp = $"203.0.113.{Random.Shared.Next(10, 250)}";
            bool hitRateLimit = false;

            for (int i = 0; i < 65; i++)
            {
                using var req = new HttpRequestMessage(HttpMethod.Get, $"{_baseUrl}/api/projects");
                req.Headers.Add("X-Forwarded-For", syntheticIp);
                using var resp = await SharedHttpClient.SendAsync(req);
                if (resp.StatusCode == HttpStatusCode.TooManyRequests)
                {
                    hitRateLimit = true;
                    break;
                }
            }

            Assert.IsTrue(hitRateLimit, "Expected rate limiter ('api-policy') to return 429 Too Many Requests after exceeding 60 requests/min.");
        }
    }
}
