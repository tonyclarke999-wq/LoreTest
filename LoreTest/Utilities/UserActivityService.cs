using System;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.EntityFrameworkCore;
using LoreTest.Data;

#nullable enable
namespace LoreTest.Utilities
{
    public class UserActivityService
    {
        private readonly IDbContextFactory<ApplicationDbContext> _dbFactory;

        public UserActivityService(IDbContextFactory<ApplicationDbContext> dbFactory)
        {
            _dbFactory = dbFactory;
        }

        public async Task LogActivityAsync(string? username, string? userId, string action, string? details = null, string? userAgent = null)
        {
            try
            {
                using var db = await _dbFactory.CreateDbContextAsync();
                
                // Retrieve the latest application settings
                var settings = await db.AppSettings.FirstOrDefaultAsync();
                var level = settings?.TelemetryLevel ?? "LoginOnly";

                // TelemetryLevel logic
                if (string.Equals(level, "None", StringComparison.OrdinalIgnoreCase))
                {
                    return;
                }

                if (string.Equals(level, "LoginOnly", StringComparison.OrdinalIgnoreCase))
                {
                    // Only log successful logins and logouts
                    if (!string.Equals(action, "Login", StringComparison.OrdinalIgnoreCase) && 
                        !string.Equals(action, "Logout", StringComparison.OrdinalIgnoreCase))
                    {
                        return;
                    }
                }

                // If level is "Full" (or default fallback), log everything including page views and search inputs

                var activity = new UserActivity
                {
                    UserId = userId,
                    Username = username ?? "Anonymous",
                    Action = action,
                    Details = details,
                    UserAgent = string.Equals(action, "PageView", StringComparison.OrdinalIgnoreCase) ? userAgent : null,
                    Timestamp = DateTime.UtcNow
                };

                db.UserActivities.Add(activity);
                await db.SaveChangesAsync();
            }
            catch (Exception ex)
            {
                // In telemetry/logging services, failures should be caught and logged (or ignored)
                // so they never crash the main application workflow.
                Console.WriteLine($"[TELEMETRY ERROR]: Failed to log activity '{action}' for user '{username}'. Error: {ex.Message}");
            }
        }
    }
}
