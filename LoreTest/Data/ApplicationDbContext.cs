using Microsoft.AspNetCore.Components.Authorization;
#nullable enable
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;

namespace LoreTest.Data
{
    public class ApplicationDbContext(DbContextOptions<ApplicationDbContext> options, IServiceProvider serviceProvider) : IdentityDbContext<ApplicationUser>(options)
    {
        private readonly IServiceProvider _serviceProvider = serviceProvider;

        public DbSet<AuditLog> AuditLogs { get; set; }
        public DbSet<UserActivity> UserActivities { get; set; }
        public DbSet<TestProject> TestProjects { get; set; }
        public DbSet<TestSuite> TestSuites { get; set; }
        public DbSet<TestCase> TestCases { get; set; }
        public DbSet<TestStep> TestSteps { get; set; }
        public DbSet<TestRun> TestRuns { get; set; }
        public DbSet<TestRunCaseResult> TestRunCaseResults { get; set; }
        public DbSet<TestRunStepResult> TestRunStepResults { get; set; }
        public DbSet<Bug> Bugs { get; set; }
        public DbSet<BugAttachment> BugAttachments { get; set; }
        public DbSet<AppSettings> AppSettings { get; set; }
        public DbSet<SupportedLanguage> SupportedLanguages { get; set; }
        public DbSet<LocalizationField> LocalizationFields { get; set; }
        public DbSet<DynamicTranslation> DynamicTranslations { get; set; }

        protected override void OnModelCreating(ModelBuilder builder)
        {
            base.OnModelCreating(builder);

            builder.Entity<LocalizationField>()
                .HasIndex(f => f.Key)
                .IsUnique();
        }

        public override async Task<int> SaveChangesAsync(CancellationToken cancellationToken = default)
        {
            string? userId = "Unknown";

            try
            {
                var authStateProvider = _serviceProvider.GetService<AuthenticationStateProvider>();
                if (authStateProvider != null)
                {
                    var authState = await authStateProvider.GetAuthenticationStateAsync();
                    userId = authState.User?.Identity?.Name ?? authState.User?.FindFirst(System.Security.Claims.ClaimTypes.NameIdentifier)?.Value ?? "Unknown";
                }
            }
            catch
            {
                // Ignore exceptions resolving auth state (e.g. during migrations)
            }

            var auditEntries = new List<AuditLog>();
            var now = DateTime.UtcNow;

            foreach (var entry in ChangeTracker.Entries())
            {
                if (entry.Entity is AuditLog || entry.State == EntityState.Detached || entry.State == EntityState.Unchanged)
                    continue;

                var tableName = entry.Metadata.GetTableName() ?? entry.Entity.GetType().Name;

                string? primaryKey = null;
                var pk = entry.Properties.FirstOrDefault(p => p.Metadata.IsPrimaryKey());
                if (pk?.CurrentValue != null)
                {
                    primaryKey = pk.CurrentValue.ToString();
                }

                foreach (var property in entry.Properties)
                {
                    if (property.IsTemporary) continue; // skip temporary properties

                    string propertyName = property.Metadata.Name;

                    if (entry.State == EntityState.Added)
                    {
                        auditEntries.Add(new AuditLog
                        {
                            UserId = userId,
                            Action = "Create",
                            TableName = tableName,
                            PrimaryKey = primaryKey,
                            ColumnName = propertyName,
                            OldValue = null,
                            NewValue = property.CurrentValue?.ToString(),
                            Timestamp = now
                        });
                    }
                    else if (entry.State == EntityState.Deleted)
                    {
                        auditEntries.Add(new AuditLog
                        {
                            UserId = userId,
                            Action = "Delete",
                            TableName = tableName,
                            PrimaryKey = primaryKey,
                            ColumnName = propertyName,
                            OldValue = property.OriginalValue?.ToString(),
                            NewValue = null,
                            Timestamp = now
                        });
                    }
                    else if (entry.State == EntityState.Modified)
                    {
                        if (property.IsModified)
                        {
                            var originalValue = property.OriginalValue?.ToString();
                            var currentValue = property.CurrentValue?.ToString();

                            if (originalValue != currentValue)
                            {
                                auditEntries.Add(new AuditLog
                                {
                                    UserId = userId,
                                    Action = "Update",
                                    TableName = tableName,
                                    PrimaryKey = primaryKey,
                                    ColumnName = propertyName,
                                    OldValue = originalValue,
                                    NewValue = currentValue,
                                    Timestamp = now
                                });
                            }
                        }
                    }
                }
            }

            AuditLogs.AddRange(auditEntries);

            return await base.SaveChangesAsync(cancellationToken);
        }
    }
}
