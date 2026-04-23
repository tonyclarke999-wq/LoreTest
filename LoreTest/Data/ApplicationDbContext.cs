using Microsoft.AspNetCore.Components.Authorization;
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;

namespace LoreTest.Data
{
    public class ApplicationDbContext : IdentityDbContext<ApplicationUser>
    {
        private readonly IServiceProvider _serviceProvider;

        public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options, IServiceProvider serviceProvider) : base(options)
        {
            _serviceProvider = serviceProvider;
        }

        public DbSet<AuditLog> AuditLogs { get; set; }

        public override async Task<int> SaveChangesAsync(CancellationToken cancellationToken = default)
        {
            string? userId = "Unknown";
            
            try
            {
                using var scope = _serviceProvider.CreateScope();
                var authStateProvider = scope.ServiceProvider.GetService<AuthenticationStateProvider>();
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
                if (pk != null && pk.CurrentValue != null)
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

            foreach (var auditEntry in auditEntries)
            {
                AuditLogs.Add(auditEntry);
            }

            return await base.SaveChangesAsync(cancellationToken);
        }
    }
}
