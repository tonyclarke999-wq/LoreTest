using System;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Text.Json;
using System.Threading.Tasks;

namespace LoreTest.Utilities
{
    public class JiraIntegrationService(HttpClient httpClient)
    {
        private readonly HttpClient _httpClient = httpClient;

        public async Task<(string Title, string Description)?> FetchIssueDetailsAsync(string jiraReference, LoreTest.Data.AppSettings settings)
        {
            if (string.IsNullOrWhiteSpace(jiraReference))
                return null;

            string token = settings?.JiraApiToken ?? "";
            string baseUrl = settings?.JiraBaseUrl ?? "";
            string email = settings?.JiraEmail ?? "";

            if (string.IsNullOrWhiteSpace(token) || string.IsNullOrWhiteSpace(baseUrl))
                throw new ArgumentException("Jira API Token and Base URL must be configured in Admin Settings.");

            // Parse the issue key from the reference
            string issueKey = jiraReference.Trim();
            if (Uri.TryCreate(jiraReference, UriKind.Absolute, out var uri))
            {
                var segments = uri.Segments;
                issueKey = segments[^1].Trim('/');
            }

            baseUrl = baseUrl.TrimEnd('/');
            string apiUrl = $"{baseUrl}/rest/api/2/issue/{issueKey}";

            var request = new HttpRequestMessage(HttpMethod.Get, apiUrl);

            string cleanToken = token.Trim();
            if (!string.IsNullOrWhiteSpace(email))
            {
                // Basic auth for Jira Cloud
                var bytes = System.Text.Encoding.UTF8.GetBytes($"{email.Trim()}:{cleanToken}");
                request.Headers.Authorization = new AuthenticationHeaderValue("Basic", Convert.ToBase64String(bytes));
            }
            else
            {
                // Fallback to Bearer token (PAT)
                request.Headers.Authorization = new AuthenticationHeaderValue("Bearer", cleanToken);
            }

            var response = await _httpClient.SendAsync(request);
            if (!response.IsSuccessStatusCode)
            {
                if (response.StatusCode == System.Net.HttpStatusCode.NotFound)
                    throw new Exception("Jira issue not found.");
                if (response.StatusCode == System.Net.HttpStatusCode.Unauthorized || response.StatusCode == System.Net.HttpStatusCode.Forbidden)
                    throw new Exception("Authentication failed. Please check the Jira API token in Settings.");
                throw new Exception($"Failed to fetch Jira details. Status code: {response.StatusCode}");
            }

            var content = await response.Content.ReadAsStringAsync();
            using var doc = JsonDocument.Parse(content);
            var root = doc.RootElement;

            if (root.TryGetProperty("fields", out var fields))
            {
                string title = fields.TryGetProperty("summary", out var summaryProp) ? summaryProp.GetString() ?? "" : "";
                string description = "";
                if (fields.TryGetProperty("description", out var descProp) && descProp.ValueKind == JsonValueKind.String)
                {
                    description = descProp.GetString() ?? "";
                    description = description.Replace("\r\n", "<br>").Replace("\n", "<br>");
                }

                return (title, description);
            }

            return null;
        }
    }
}
