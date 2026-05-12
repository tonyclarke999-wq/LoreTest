using System;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Text.Json;
using System.Threading.Tasks;

namespace LoreTest.Utilities
{
    public class JiraIntegrationService
    {
        private readonly HttpClient _httpClient;

        public JiraIntegrationService(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        public async Task<(string Title, string Description)?> FetchIssueDetailsAsync(string jiraReference, string token)
        {
            if (string.IsNullOrWhiteSpace(jiraReference) || string.IsNullOrWhiteSpace(token))
                return null;

            if (!Uri.TryCreate(jiraReference, UriKind.Absolute, out var uri))
                throw new ArgumentException("Jira Reference must be a valid full URL (e.g., https://your-domain.atlassian.net/browse/PROJ-123).");

            var segments = uri.Segments;
            string issueKey = segments[segments.Length - 1].Trim('/');
            
            string baseUrl = $"{uri.Scheme}://{uri.Authority}";
            string apiUrl = $"{baseUrl}/rest/api/2/issue/{issueKey}";

            var request = new HttpRequestMessage(HttpMethod.Get, apiUrl);
            
            string cleanToken = token.Trim();
            if (cleanToken.StartsWith("Basic ", StringComparison.OrdinalIgnoreCase))
            {
                request.Headers.Authorization = new AuthenticationHeaderValue("Basic", cleanToken.Substring(6).Trim());
            }
            else if (cleanToken.StartsWith("Bearer ", StringComparison.OrdinalIgnoreCase))
            {
                request.Headers.Authorization = new AuthenticationHeaderValue("Bearer", cleanToken.Substring(7).Trim());
            }
            else if (cleanToken.Contains(":"))
            {
                var bytes = System.Text.Encoding.UTF8.GetBytes(cleanToken);
                request.Headers.Authorization = new AuthenticationHeaderValue("Basic", Convert.ToBase64String(bytes));
            }
            else
            {
                // Default to Basic auth for Atlassian cloud tokens if email is not provided, 
                // though it typically requires email:token. If it's a PAT, it needs Bearer.
                // Let's default to Bearer which works for PAT on Jira Data Center.
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
