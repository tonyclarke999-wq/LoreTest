# LoreTest REST API Reference & Usage Guide

Welcome to the LoreTest REST API documentation. The API enables programmatic access to Projects, Test Suites, Test Cases, and Bug Reports, secured using JWT (JSON Web Tokens) and ASP.NET Core Role-Based Authorization.

---

## Table of Contents
1. [Authentication & Security](#1-authentication--security)
2. [API Endpoint Reference](#2-api-endpoint-reference)
   - [Authentication](#authentication)
   - [Projects](#projects)
   - [Test Suites](#test-suites)
   - [Test Cases](#test-cases)
   - [Bugs](#bugs)
3. [Jira Dynamic Integration](#3-jira-dynamic-integration)
4. [Code Usage Examples](#4-code-usage-examples)
   - [cURL](#curl)
   - [JavaScript (Fetch)](#javascript-fetch)
   - [C# (HttpClient)](#c-httpclient)

---

## 1. Authentication & Security

The API uses **JWT Bearer Authentication**. All API requests (except `/api/auth/login`) must include the token in the HTTP `Authorization` header.

### Authentication Header format
```http
Authorization: Bearer <your_jwt_token_here>
```

### Role-Based Authorization Rules
Depending on your user profile, certain REST endpoints enforce role-based restrictions:
- **Viewer**: Read-only access to all GET endpoints.
- **Editor**: Full read and write access (GET, POST, PUT) to Projects, Suites, Cases, and Bugs.
- **Admin**: Full control including write and delete capabilities (GET, POST, PUT, DELETE) across all resources.

---

## 2. API Endpoint Reference

### Authentication

#### Authenticate and Retrieve Token
- **Route**: `POST /api/auth/login`
- **Description**: Authenticates user credentials (email or username) and returns a JWT Bearer token with expiration.
- **Anonymous Access**: Yes (No Authorization Header required).

##### Request Payload (`LoginRequest`)
```json
{
  "email": "admin@example.com",
  "password": "SuperSecretPassword123!"
}
```

##### Response Payload (`LoginResponse`)
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expiration": "2026-05-30T21:40:48Z",
  "user": {
    "id": "e2d83b9c-8517-4841-b847-d1a293849182",
    "userName": "admin",
    "email": "admin@example.com",
    "name": "Administrator",
    "role": "Admin",
    "preferredLanguage": "en"
  }
}
```

---

### Projects

#### Get All Projects
- **Route**: `GET /api/projects`
- **Authorized Roles**: `Admin`, `Editor`, `Viewer`
- **Response**: Array of `TestProjectDto` objects.

#### Get Project by ID
- **Route**: `GET /api/projects/{id}`
- **Authorized Roles**: `Admin`, `Editor`, `Viewer`
- **Response**: `TestProjectDto` object.

#### Create Project
- **Route**: `POST /api/projects`
- **Authorized Roles**: `Admin`, `Editor`

##### Request Payload (`CreateTestProjectDto`)
```json
{
  "title": "API Automation Project",
  "description": "Exposing automated test results via APIs",
  "jiraReference": "PROJ"
}
```

#### Update Project
- **Route**: `PUT /api/projects/{id}`
- **Authorized Roles**: `Admin`, `Editor`

##### Request Payload (`UpdateTestProjectDto`)
```json
{
  "title": "Updated API Automation Project",
  "description": "Exposing automated test results via APIs - updated",
  "jiraReference": "PROJ-NEW"
}
```

#### Delete Project
- **Route**: `DELETE /api/projects/{id}`
- **Authorized Roles**: `Admin`
- **Response**: `240 NoContent` on success.

---

### Test Suites

#### Get Suites for Project
- **Route**: `GET /api/projects/{projectId}/suites`
- **Authorized Roles**: `Admin`, `Editor`, `Viewer`
- **Response**: Array of `TestSuiteDto` objects.

#### Get Suite by ID
- **Route**: `GET /api/suites/{id}`
- **Authorized Roles**: `Admin`, `Editor`, `Viewer`
- **Response**: `TestSuiteDto` object.

#### Create Suite under Project
- **Route**: `POST /api/projects/{projectId}/suites`
- **Authorized Roles**: `Admin`, `Editor`

##### Request Payload (`CreateTestSuiteDto`)
```json
{
  "title": "Authentication API Tests",
  "description": "Integration scenarios testing authentication and token validations"
}
```

#### Update Suite
- **Route**: `PUT /api/suites/{id}`
- **Authorized Roles**: `Admin`, `Editor`

##### Request Payload (`UpdateTestSuiteDto`)
```json
{
  "title": "Updated Authentication API Tests",
  "description": "Updated description"
}
```

#### Delete Suite
- **Route**: `DELETE /api/suites/{id}`
- **Authorized Roles**: `Admin`

---

### Test Cases

#### Get Cases for Suite
- **Route**: `GET /api/suites/{suiteId}/cases`
- **Authorized Roles**: `Admin`, `Editor`, `Viewer`
- **Response**: Array of `TestCaseDto` objects.

#### Get Case by ID
- **Route**: `GET /api/cases/{id}`
- **Authorized Roles**: `Admin`, `Editor`, `Viewer`
- **Response**: `TestCaseDto` object.

#### Create Case under Suite
- **Route**: `POST /api/suites/{suiteId}/cases`
- **Authorized Roles**: `Admin`, `Editor`

##### Request Payload (`CreateTestCaseDto`)
```json
{
  "title": "Login with Valid Credentials returns 200",
  "description": "Verifies that correct credentials result in successful authentication",
  "preConditions": "User is registered in identity system",
  "dependencies": "Database and API must be online",
  "testData": "{\n  \"email\": \"test@example.com\",\n  \"password\": \"validPassword\"\n}",
  "postCondition": "Bearer token returned to client",
  "status": "Approved",
  "priority": "High",
  "defectId": null,
  "notes": "Automated scenario verified on CI/CD runner"
}
```

#### Update Case
- **Route**: `PUT /api/cases/{id}`
- **Authorized Roles**: `Admin`, `Editor`

##### Request Payload (`UpdateTestCaseDto`)
- Identical JSON schema to `CreateTestCaseDto`.

#### Delete Case
- **Route**: `DELETE /api/cases/{id}`
- **Authorized Roles**: `Admin`

---

### Bugs

#### Get All Bugs
- **Route**: `GET /api/bugs`
- **Authorized Roles**: `Admin`, `Editor`, `Viewer`
- **Response**: Array of `BugDto` objects.

#### Get Bug by ID
- **Route**: `GET /api/bugs/{id}`
- **Authorized Roles**: `Admin`, `Editor`, `Viewer`
- **Response**: `BugDto` object.

#### Create Bug (with optional Jira Sync)
- **Route**: `POST /api/bugs`
- **Authorized Roles**: `Admin`, `Editor`, `Viewer`
- **Description**: Submits a bug report to LoreTest database. If Jira settings are configured and `syncToJira` is requested (or is automatically determined via Project association), this endpoint will dynamically call `JiraIntegrationService` to create a corresponding Bug in your Jira Cloud instance, returning the ticket link in the response.

##### Request Payload (`CreateBugDto`)
```json
{
  "title": "NullReferenceException on Login Route",
  "description": "A NullReferenceException occurs when logging in with an unconfirmed email account.",
  "expectedResult": "Informative confirmation warning message displayed.",
  "actualResult": "HTTP 500 server crash.",
  "severity": "Critical",
  "priority": "High",
  "status": "Open",
  "environment": "PreProduction / IIS 10",
  "components": "Authentication Module",
  "labels": "backend,auth,crash",
  "assigneeId": "user-uuid-here",
  "dueDate": "2026-06-15T00:00:00Z",
  "projectId": 2,
  "syncToJira": true,
  "jiraProjectOrIssueKey": "PROJ"
}
```

##### Response Payload (`BugDto`)
```json
{
  "id": 14,
  "title": "NullReferenceException on Login Route",
  "originalCulture": "en",
  "description": "A NullReferenceException occurs when logging in with an unconfirmed email account.",
  "expectedResult": "Informative confirmation warning message displayed.",
  "actualResult": "HTTP 500 server crash.",
  "severity": "Critical",
  "priority": "High",
  "status": "Open",
  "environment": "PreProduction / IIS 10",
  "components": "Authentication Module",
  "labels": "backend,auth,crash",
  "reporterId": "admin-uuid",
  "assigneeId": "user-uuid-here",
  "reportedDate": "2026-05-30T21:02:03.456Z",
  "dueDate": "2026-06-15T00:00:00Z",
  "projectId": 2,
  "jiraBugReference": "PROJ-824"
}
```

#### Update Bug
- **Route**: `PUT /api/bugs/{id}`
- **Authorized Roles**: `Admin`, `Editor`
- **Request Payload**: Same format as standard entity attributes without `syncToJira` and `jiraProjectOrIssueKey` properties.

#### Delete Bug
- **Route**: `DELETE /api/bugs/{id}`
- **Authorized Roles**: `Admin`

---

## 3. Jira Dynamic Integration

The REST API exposes the full dynamic Jira synchronization behavior originally developed for the Blazor UI:
1. When submitting a POST request to `/api/bugs`:
   - Set `syncToJira` to `true`.
   - Provide `jiraProjectOrIssueKey` (e.g. `PROJ` for project spaces, or `PROJ-123` to link the bug as a "Relates to" issue under a parent card).
2. If `jiraProjectOrIssueKey` is empty but a valid `projectId` is provided, the API automatically falls back to utilizing the target Project's configured `JiraReference` as the space key.
3. On successful validation, the backend connects to your Jira Cloud workspace using basic/bearer authentication tokens retrieved from `AppSettings`.
4. If successful, the created Jira Ticket ID (e.g., `PROJ-824`) is saved in the local database under `JiraBugReference` and returned in the HTTP Response payload.
5. If the sync fails (e.g., due to bad network or credentials on Jira), the local record remains fully created in LoreTest, and `JiraBugReference` will return a descriptive warning string containing the error.

---

## 4. Code Usage Examples

### cURL

```bash
# 1. Obtain JWT Bearer Token
TOKEN=$(curl -s -X POST "http://localhost:5001/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "SuperSecretPassword123!"}' \
  | grep -o '"token":"[^"]*' | grep -o '[^"]*$')

# 2. Get All Projects
curl -X GET "http://localhost:5001/api/projects" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### JavaScript (Fetch)

```javascript
// 1. Authenticate and save token
async function authenticate(email, password) {
  const response = await fetch('http://localhost:5001/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  
  if (response.ok) {
    const data = await response.json();
    localStorage.setItem('api_token', data.token);
    console.log('Authenticated successfully!');
  } else {
    console.error('Login failed.');
  }
}

// 2. Fetch Projects using Bearer Token
async function getProjects() {
  const token = localStorage.getItem('api_token');
  const response = await fetch('http://localhost:5001/api/projects', {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });

  const projects = await response.json();
  return projects;
}
```

### C# (HttpClient)

```csharp
using System;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using System.Threading.Tasks;

public class LoreTestApiClient
{
    private readonly HttpClient _client;
    private string _token;

    public LoreTestApiClient(string baseUrl)
    {
        _client = new HttpClient { BaseAddress = new Uri(baseUrl) };
    }

    public async Task<bool> AuthenticateAsync(string email, string password)
    {
        var response = await _client.PostAsJsonAsync("api/auth/login", new { email, password });
        if (!response.IsSuccessStatusCode) return false;

        var result = await response.Content.ReadFromJsonAsync<LoginResponse>();
        _token = result.Token;
        
        _client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", _token);
        return true;
    }

    public async Task<List<TestProjectDto>> GetProjectsAsync()
    {
        return await _client.GetFromJsonAsync<List<TestProjectDto>>("api/projects");
    }
}

public class LoginResponse
{
    public string Token { get; set; }
}

public class TestProjectDto
{
    public int Id { get; set; }
    public string Title { get; set; }
    public string Description { get; set; }
}
```
