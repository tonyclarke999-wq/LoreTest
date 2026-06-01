# Security & Resilience Review - LoreTest Application

This document provides a comprehensive security and resilience audit of the software and infrastructure built for the `LoreTest` application. It highlights active vulnerabilities and provides concrete, production-grade recommendations and architectural guides to secure the application.

---

## Executive Summary

The current implementation of `LoreTest` successfully exposes stateless REST APIs concurrently with the interactive Blazor Server UI. However, the system is exposed to several security and resilience risks that should be addressed before deploying to a public-facing production environment:

1. **Brute Force Vulnerability (High Severity)**: Account lockout and failed login throttling are disabled in both the Blazor interactive login and the JWT REST API login.
2. **Lack of Rate Limiting (Medium Severity)**: No rate limiting is applied to REST API endpoints or page requests, exposing the server to resource exhaustion (DDoS) and automated credential stuffing.
3. **Hard-coded Secrets in Configuration (Medium Severity)**: Development database connection strings and JWT signing secrets are committed directly to `appsettings.json`, and fallback default passwords are coded into `docker-compose` files.

---

## Detailed Findings & Recommendations

### 1. Brute Force & Password Throttling (High Severity)

#### Active Vulnerability
- **Interactive Logins (`Login.razor`)**:
  The password sign-in method is explicitly configured to ignore lockout:
  ```csharp
  result = await SignInManager.PasswordSignInAsync(Input.Email, Input.Password, Input.RememberMe, lockoutOnFailure: false);
  ```
  Since `lockoutOnFailure` is `false`, a malicious actor can automate hundreds of thousands of password guesses against any user account without ever triggering a lockout.
- **REST API Logins (`AuthController.cs`)**:
  The API authentication uses `UserManager.CheckPasswordAsync` directly:
  ```csharp
  if (user == null || !await _userManager.CheckPasswordAsync(user, request.Password))
  ```
  `CheckPasswordAsync` only performs a cryptographic hash validation. It **does not** increment the user's `AccessFailedCount` in the database, nor does it evaluate or trigger lockout state. Consequently, the API endpoint `POST /api/auth/login` is a perfect target for high-speed brute force attacks.

#### Recommendations
1. **Enable Lockout on Failure globally**: Configure ASP.NET Core Identity Lockout options in `Program.cs`.
2. **Update Interactive Sign-in**: Set `lockoutOnFailure: true` in `Login.razor`.
3. **Secure API Login**: Use `SignInManager.CheckPasswordSignInAsync` in `AuthController.cs` instead of `UserManager.CheckPasswordAsync`, setting `lockoutOnFailure: true` to dynamically increment failed attempts and enforce lockout statelessly.

---

### 2. Rate Limiting & DDoS Resilience (Medium Severity)

#### Active Vulnerability
- Currently, there is no rate limiting or concurrency control in the middleware pipeline. 
- High-frequency automated scrapers or malicious scripts can spam resource-intensive endpoints (such as `GET /api/bugs` or `POST /api/projects` which invoke database queries and external Jira HTTP clients) and degrade performance or crash the application.

#### Recommendations
- Implement ASP.NET Core's built-in **Rate Limiting Middleware** (available since .NET 7/8).
- Apply a **partitioned fixed-window or sliding-window rate limiter** keyed by the client's Remote IP Address (or `X-Forwarded-For` header when behind a reverse proxy) to protect all `/api/*` endpoints from abuse.

---

### 3. Hard-coded Secrets & Environment Isolation (Medium Severity)

#### Active Vulnerability
- **JWT Signing Secret**: Committed inside `appsettings.json` as a plaintext fallback:
  ```json
  "Secret": "SuperSecretKeyThatIsAtLeast32CharactersLong!"
  ```
  If this key is committed to Git, any malicious actor who gains read access to the repository can forge arbitrary JWTs with `Admin` claims, giving them full access to the database.
- **Database Connection Strings**: Plaintext credentials (`Password=HomePlate4`) are committed in `appsettings.json`.
- **Docker-Compose Credentials**: Default password fallbacks are embedded in `docker-compose.preprod.yml` and `docker-compose.test.yml`:
  ```yaml
  POSTGRES_PASSWORD: ${PREPROD_DB_PASSWORD:-HomePlate4_PreProd}
  ```

#### Recommendations
1. **Never commit secrets to repository**: Move all production/test passwords and JWT keys to environment variables or secret vaults.
2. **Use User Secrets for local development**: Use `dotnet user-secrets` to store credentials outside the workspace folder in development.
3. **Use Environment Variable injection**: Configure Kestrel to bind to environment variables (e.g., `ConnectionStrings__DefaultConnection` and `Jwt__Secret`) in production containers.

---

## Architectural Implementation Guides

Below are copy-pasteable, production-ready configurations to address each finding.

### Guide A: Rate Limiting Implementation

Add the following to `Program.cs`:

```csharp
using Microsoft.AspNetCore.RateLimiting;
using System.Threading.RateLimiting;

// 1. Register Rate Limiting Services
builder.Services.AddRateLimiter(options =>
{
    options.RejectionStatusCode = StatusCodes.Status429TooManyRequests;
    
    // IP-based partitioned fixed-window rate limiter
    options.AddPolicy("api-policy", httpContext =>
    {
        // Extract real IP behind reverse proxy if applicable
        var ipAddress = httpContext.Request.Headers["X-Forwarded-For"].FirstOrDefault() 
                        ?? httpContext.Connection.RemoteIpAddress?.ToString() 
                        ?? "anonymous";

        return RateLimitPartition.GetFixedWindowLimiter(
            partitionKey: ipAddress,
            factory: partition => new FixedWindowRateLimiterOptions
            {
                AutoReplenishment = true,
                PermitLimit = 60,                // 60 requests per minute
                Window = TimeSpan.FromMinutes(1),
                QueueLimit = 0                    // Decline immediate overruns
            });
    });
});
```

Apply the middleware in `Program.cs` (place it **after** `UseRouting()` and **before** `UseAuthentication()` to protect authentication endpoints from DDoS):

```csharp
app.UseRateLimiter();
```

Decorate all API controllers under `LoreTest/Controllers/` with the `[EnableRateLimiting("api-policy")]` attribute:

```csharp
using Microsoft.AspNetCore.RateLimiting;

namespace LoreTest.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    [EnableRateLimiting("api-policy")]
    public class ProjectsController : ControllerBase
    {
        // ...
    }
}
```

---

### Guide B: Brute Force & Lockout Protection

#### 1. Configure Lockout Options in `Program.cs`:
```csharp
builder.Services.AddIdentityCore<ApplicationUser>(options =>
    {
        options.SignIn.RequireConfirmedAccount = true;
        options.Stores.SchemaVersion = IdentitySchemaVersions.Version3;
        
        // Lockout Settings
        options.Lockout.AllowedForNewUsers = true;
        options.Lockout.DefaultLockoutTimeSpan = TimeSpan.FromMinutes(15);
        options.Lockout.MaxFailedAccessAttempts = 5; // Lock out after 5 consecutive failures
    })
    .AddEntityFrameworkStores<ApplicationDbContext>()
    .AddSignInManager()
    .AddDefaultTokenProviders();
```

#### 2. Update Interactive Login in `Login.razor`:
Change line 134 to enable lockout:
```csharp
result = await SignInManager.PasswordSignInAsync(Input.Email, Input.Password, Input.RememberMe, lockoutOnFailure: true);
```

#### 3. Update JWT Auth Login in `AuthController.cs`:
Use `SignInManager.CheckPasswordSignInAsync` (which correctly tracks failed attempts and enforces lockout states):

```csharp
// Inject SignInManager into the AuthController constructor
private readonly SignInManager<ApplicationUser> _signInManager;
private readonly UserManager<ApplicationUser> _userManager;

public AuthController(
    UserManager<ApplicationUser> userManager, 
    SignInManager<ApplicationUser> signInManager, 
    IConfiguration configuration)
{
    _userManager = userManager;
    _signInManager = signInManager;
    _configuration = configuration;
}

[HttpPost("login")]
public async Task<IActionResult> Login([FromBody] LoginRequest request)
{
    // ... input validation ...
    
    var user = await _userManager.FindByEmailAsync(request.Email) ?? await _userManager.FindByNameAsync(request.Email);
    if (user == null)
    {
        return Unauthorized("Invalid email/username or password.");
    }

    // Check password and increment failed counts if unsuccessful
    var result = await _signInManager.CheckPasswordSignInAsync(user, request.Password, lockoutOnFailure: true);
    
    if (result.IsLockedOut)
    {
        return StatusCode(StatusCodes.Status423Locked, "This account has been locked due to multiple failed login attempts. Please try again in 15 minutes.");
    }
    
    if (!result.Succeeded)
    {
        return Unauthorized("Invalid email/username or password.");
    }

    // ... generate token ...
}
```

---

### Guide C: Secret Management & Container Security

#### 1. Move secrets out of Git
Remove the plaintext database passwords and JWT secrets from `appsettings.json` in the git repository. Replace them with standard local placeholders:

```json
{
  "ConnectionStrings": {
    "DefaultConnection": "Host=localhost;Port=5432;Database=loretest;Username=postgres"
  },
  "Jwt": {
    "Secret": "LOCAL_DEVELOPMENT_SECRET_KEY_PLACEHOLDER_REPLACE_IN_PRODUCTION",
    "Issuer": "LoreTestAPI",
    "Audience": "LoreTestAPIUsers"
  }
}
```

#### 2. Local Development (User Secrets)
Use the .NET CLI `user-secrets` tool to save your local secrets securely outside your workspace directory (saved in the user profile directory):
```powershell
dotnet user-secrets init
dotnet user-secrets set "ConnectionStrings:DefaultConnection" "Host=localhost;Port=5432;Database=loretest;Username=postgres;Password=HomePlate4"
dotnet user-secrets set "Jwt:Secret" "DevSecretKeyThatIsAtLeast32CharactersLong!"
```

#### 3. Containerized Preprod & Production Injection (Environment Variables)
In production container host orchestrators (like Docker-compose, ECS, or Kubernetes), inject the real credentials using environment variables. ASP.NET Core automatically parses environment variables prefixed with `ConnectionStrings__` or mapped using double underscores `__` to override JSON configurations:

```yaml
services:
  loretest-app-preprod:
    image: ghcr.io/tonyclarke999-wq/loretest:main
    environment:
      - ASPNETCORE_ENVIRONMENT=Production
      # Dynamically inject credentials at runtime, never hardcoded in files
      - ConnectionStrings__DefaultConnection=Host=loretest-db-preprod;Port=5432;Database=loretest_preprod;Username=postgres;Password=${PREPROD_DB_PASSWORD}
      - Jwt__Secret=${PREPROD_JWT_SECRET}
```
Ensure that `${PREPROD_DB_PASSWORD}` and `${PREPROD_JWT_SECRET}` are populated dynamically on the CI/CD host or via a secured `.env` file that is in `.gitignore`.
