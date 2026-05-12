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

        public async Task<string> CreateBugAsync(LoreTest.Data.Bug bug, string parentJiraReference, LoreTest.Data.AppSettings settings)
        {
            ArgumentNullException.ThrowIfNull(settings);

            string token = settings.JiraApiToken ?? "";
            string baseUrl = settings.JiraBaseUrl ?? "";
            string email = settings.JiraEmail ?? "";

            if (string.IsNullOrWhiteSpace(token) || string.IsNullOrWhiteSpace(baseUrl))
                throw new ArgumentException("Jira API Token and Base URL must be configured in Admin Settings.");

            // Parse the parent issue key from the reference
            string parentKey = parentJiraReference.Trim();
            if (Uri.TryCreate(parentJiraReference, UriKind.Absolute, out var uri))
            {
                var segments = uri.Segments;
                parentKey = segments[^1].Trim('/');
            }

            string projectKey = "";
            try
            {
                var (_, _, parentProjectKey) = await FetchIssueDetailsInternalAsync(parentKey, settings);
                projectKey = parentProjectKey;
            }
            catch (Exception ex)
            {
                // Fallback to manual parsing if API fetch fails, but log it or handle it
                var hyphenIndex = parentKey.IndexOf('-');
                if (hyphenIndex <= 0)
                    throw new Exception($"Could not determine Jira project space from '{parentKey}'. Error: {ex.Message}");
                projectKey = parentKey[..hyphenIndex];
            }

            baseUrl = baseUrl.TrimEnd('/');
            string apiUrl = $"{baseUrl}/rest/api/2/issue";

            // Construct payload without parent field
            var payload = new
            {
                fields = new
                {
                    project = new { key = projectKey },
                    summary = bug.Title,
                    description = bug.Description,
                    issuetype = new { name = "Bug" }
                }
            };

            var request = new HttpRequestMessage(HttpMethod.Post, apiUrl)
            {
                Content = new StringContent(JsonSerializer.Serialize(payload), System.Text.Encoding.UTF8, "application/json")
            };

            string cleanToken = token.Trim();
            string authHeader;
            if (!string.IsNullOrWhiteSpace(email))
            {
                var bytes = System.Text.Encoding.UTF8.GetBytes($"{email.Trim()}:{cleanToken}");
                authHeader = "Basic " + Convert.ToBase64String(bytes);
            }
            else
            {
                authHeader = "Bearer " + cleanToken;
            }

            request.Headers.Add("Authorization", authHeader);

            var response = await _httpClient.SendAsync(request);
            var content = await response.Content.ReadAsStringAsync();

            if (!response.IsSuccessStatusCode)
            {
                throw new Exception($"Failed to create Jira bug. Status code: {response.StatusCode}. Response: {content}");
            }

            using var doc = JsonDocument.Parse(content);
            if (!doc.RootElement.TryGetProperty("key", out var keyProp))
            {
                throw new Exception("Jira issue was created, but no key was returned.");
            }

            string newIssueKey = keyProp.GetString() ?? "";

            // Create "Relates to" link
            string linkUrl = $"{baseUrl}/rest/api/2/issueLink";
            var linkPayload = new
            {
                type = new { name = "Relates" }, // Standard name is often "Relates" or "Relates to"
                inwardIssue = new { key = parentKey },
                outwardIssue = new { key = newIssueKey }
            };

            var linkRequest = new HttpRequestMessage(HttpMethod.Post, linkUrl)
            {
                Content = new StringContent(JsonSerializer.Serialize(linkPayload), System.Text.Encoding.UTF8, "application/json")
            };
            linkRequest.Headers.Add("Authorization", authHeader);

            var linkResponse = await _httpClient.SendAsync(linkRequest);
            if (!linkResponse.IsSuccessStatusCode)
            {
                // We don't throw here to avoid failing the whole process if just the link fails, 
                // but maybe we should log it. For now, let's keep the bug key.
            }

            return newIssueKey;
        }

        private async Task<(string Title, string Description, string ProjectKey)> FetchIssueDetailsInternalAsync(string issueKey, LoreTest.Data.AppSettings settings)
        {
            string token = settings.JiraApiToken ?? "";
            string baseUrl = settings.JiraBaseUrl ?? "";
            string email = settings.JiraEmail ?? "";

            baseUrl = baseUrl.TrimEnd('/');
            string apiUrl = $"{baseUrl}/rest/api/2/issue/{issueKey}";

            var request = new HttpRequestMessage(HttpMethod.Get, apiUrl);

            string cleanToken = token.Trim();
            if (!string.IsNullOrWhiteSpace(email))
            {
                var bytes = System.Text.Encoding.UTF8.GetBytes($"{email.Trim()}:{cleanToken}");
                request.Headers.Authorization = new AuthenticationHeaderValue("Basic", Convert.ToBase64String(bytes));
            }
            else
            {
                request.Headers.Authorization = new AuthenticationHeaderValue("Bearer", cleanToken);
            }

            var response = await _httpClient.SendAsync(request);
            if (!response.IsSuccessStatusCode)
            {
                throw new Exception($"Failed to fetch Jira parent details. Status: {response.StatusCode}");
            }

            var content = await response.Content.ReadAsStringAsync();
            using var doc = JsonDocument.Parse(content);
            var root = doc.RootElement;

            if (root.TryGetProperty("fields", out var fields))
            {
                string title = fields.TryGetProperty("summary", out var summaryProp) ? summaryProp.GetString() ?? "" : "";
                string description = fields.TryGetProperty("description", out var descProp) ? descProp.GetString() ?? "" : "";
                string projectKey = fields.TryGetProperty("project", out var projectProp) && projectProp.TryGetProperty("key", out var keyProp) ? keyProp.GetString() ?? "" : "";

                return (title, description, projectKey);
            }

            throw new Exception("Invalid Jira API response.");
        }
    }
}
