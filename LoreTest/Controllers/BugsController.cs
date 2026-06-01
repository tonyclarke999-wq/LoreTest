using System.Security.Claims;
using LoreTest.Data;
using LoreTest.Utilities;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace LoreTest.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    [Authorize(AuthenticationSchemes = JwtBearerDefaults.AuthenticationScheme)]
    [IgnoreAntiforgeryToken]
    [Microsoft.AspNetCore.RateLimiting.EnableRateLimiting("api-policy")]
    public class BugsController : ControllerBase
    {
        private readonly ApplicationDbContext _context;
        private readonly JiraIntegrationService _jiraService;

        public BugsController(ApplicationDbContext context, JiraIntegrationService jiraService)
        {
            _context = context;
            _jiraService = jiraService;
        }

        [HttpGet]
        public async Task<ActionResult<IEnumerable<BugDto>>> GetBugs()
        {
            var bugs = await _context.Bugs
                .Select(b => new BugDto
                {
                    Id = b.Id,
                    Title = b.Title,
                    OriginalCulture = b.OriginalCulture,
                    Description = b.Description,
                    ExpectedResult = b.ExpectedResult,
                    ActualResult = b.ActualResult,
                    Severity = b.Severity.ToString(),
                    Priority = b.Priority.ToString(),
                    Status = b.Status.ToString(),
                    Environment = b.Environment,
                    Components = b.Components,
                    Labels = b.Labels,
                    ReporterId = b.ReporterId,
                    AssigneeId = b.AssigneeId,
                    ReportedDate = b.ReportedDate,
                    DueDate = b.DueDate,
                    AffectedVersion = b.AffectedVersion,
                    FixVersion = b.FixVersion,
                    Resolution = b.Resolution,
                    ProjectId = b.ProjectId,
                    JiraBugReference = b.JiraBugReference
                })
                .ToListAsync();

            return Ok(bugs);
        }

        [HttpGet("{id}")]
        public async Task<ActionResult<BugDto>> GetBug(int id)
        {
            var bug = await _context.Bugs.FindAsync(id);
            if (bug == null)
            {
                return NotFound($"Bug with ID {id} not found.");
            }

            return Ok(new BugDto
            {
                Id = bug.Id,
                Title = bug.Title,
                OriginalCulture = bug.OriginalCulture,
                Description = bug.Description,
                ExpectedResult = bug.ExpectedResult,
                ActualResult = bug.ActualResult,
                Severity = bug.Severity.ToString(),
                Priority = bug.Priority.ToString(),
                Status = bug.Status.ToString(),
                Environment = bug.Environment,
                Components = bug.Components,
                Labels = bug.Labels,
                ReporterId = bug.ReporterId,
                AssigneeId = bug.AssigneeId,
                ReportedDate = bug.ReportedDate,
                DueDate = bug.DueDate,
                AffectedVersion = bug.AffectedVersion,
                FixVersion = bug.FixVersion,
                Resolution = bug.Resolution,
                ProjectId = bug.ProjectId,
                JiraBugReference = bug.JiraBugReference
            });
        }

        [HttpPost]
        public async Task<ActionResult<BugDto>> CreateBug([FromBody] CreateBugDto dto)
        {
            if (dto == null || string.IsNullOrWhiteSpace(dto.Title) || string.IsNullOrWhiteSpace(dto.Description))
            {
                return BadRequest("Title and Description are required.");
            }

            var userId = User.FindFirst(ClaimTypes.NameIdentifier)?.Value;

            Enum.TryParse<BugSeverity>(dto.Severity, true, out var severity);
            Enum.TryParse<BugPriority>(dto.Priority, true, out var priority);
            Enum.TryParse<BugStatus>(dto.Status, true, out var status);

            var bug = new Bug
            {
                Title = dto.Title,
                OriginalCulture = dto.OriginalCulture ?? "en",
                Description = dto.Description,
                ExpectedResult = dto.ExpectedResult,
                ActualResult = dto.ActualResult,
                Severity = severity,
                Priority = priority,
                Status = status,
                Environment = dto.Environment,
                Components = dto.Components,
                Labels = dto.Labels,
                ReporterId = userId,
                AssigneeId = dto.AssigneeId,
                ReportedDate = DateTime.UtcNow,
                DueDate = dto.DueDate,
                AffectedVersion = dto.AffectedVersion,
                FixVersion = dto.FixVersion,
                Resolution = dto.Resolution,
                ProjectId = dto.ProjectId
            };

            _context.Bugs.Add(bug);
            await _context.SaveChangesAsync();

            // Handle Jira Integration Sync
            var settings = await _context.AppSettings.FirstOrDefaultAsync();
            bool hasJiraSettings = settings != null && !string.IsNullOrWhiteSpace(settings.JiraApiToken) && !string.IsNullOrWhiteSpace(settings.JiraBaseUrl);
            
            if (hasJiraSettings && (dto.SyncToJira || bug.ProjectId.HasValue))
            {
                try
                {
                    string targetKey = dto.JiraProjectOrIssueKey ?? "";
                    if (string.IsNullOrWhiteSpace(targetKey) && bug.ProjectId.HasValue)
                    {
                        var project = await _context.TestProjects.FindAsync(bug.ProjectId.Value);
                        targetKey = project?.JiraReference ?? "";
                    }

                    if (!string.IsNullOrWhiteSpace(targetKey))
                    {
                        string jiraKey = await _jiraService.CreateBugAsync(bug, targetKey, settings!);
                        bug.JiraBugReference = jiraKey;

                        _context.Bugs.Update(bug);
                        await _context.SaveChangesAsync();
                    }
                }
                catch (Exception ex)
                {
                    // Log error but return created bug with warning
                    return CreatedAtAction(nameof(GetBug), new { id = bug.Id }, new BugDto
                    {
                        Id = bug.Id,
                        Title = bug.Title,
                        OriginalCulture = bug.OriginalCulture,
                        Description = bug.Description,
                        ExpectedResult = bug.ExpectedResult,
                        ActualResult = bug.ActualResult,
                        Severity = bug.Severity.ToString(),
                        Priority = bug.Priority.ToString(),
                        Status = bug.Status.ToString(),
                        Environment = bug.Environment,
                        Components = bug.Components,
                        Labels = bug.Labels,
                        ReporterId = bug.ReporterId,
                        AssigneeId = bug.AssigneeId,
                        ReportedDate = bug.ReportedDate,
                        DueDate = bug.DueDate,
                        AffectedVersion = bug.AffectedVersion,
                        FixVersion = bug.FixVersion,
                        Resolution = bug.Resolution,
                        ProjectId = bug.ProjectId,
                        JiraBugReference = $"Warning: Saved in LoreTest but failed to sync to Jira: {ex.Message}"
                    });
                }
            }

            return CreatedAtAction(nameof(GetBug), new { id = bug.Id }, new BugDto
            {
                Id = bug.Id,
                Title = bug.Title,
                OriginalCulture = bug.OriginalCulture,
                Description = bug.Description,
                ExpectedResult = bug.ExpectedResult,
                ActualResult = bug.ActualResult,
                Severity = bug.Severity.ToString(),
                Priority = bug.Priority.ToString(),
                Status = bug.Status.ToString(),
                Environment = bug.Environment,
                Components = bug.Components,
                Labels = bug.Labels,
                ReporterId = bug.ReporterId,
                AssigneeId = bug.AssigneeId,
                ReportedDate = bug.ReportedDate,
                DueDate = bug.DueDate,
                AffectedVersion = bug.AffectedVersion,
                FixVersion = bug.FixVersion,
                Resolution = bug.Resolution,
                ProjectId = bug.ProjectId,
                JiraBugReference = bug.JiraBugReference
            });
        }

        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateBug(int id, [FromBody] UpdateBugDto dto)
        {
            if (dto == null || string.IsNullOrWhiteSpace(dto.Title) || string.IsNullOrWhiteSpace(dto.Description))
            {
                return BadRequest("Title and Description are required.");
            }

            var bug = await _context.Bugs.FindAsync(id);
            if (bug == null)
            {
                return NotFound($"Bug with ID {id} not found.");
            }

            Enum.TryParse<BugSeverity>(dto.Severity, true, out var severity);
            Enum.TryParse<BugPriority>(dto.Priority, true, out var priority);
            Enum.TryParse<BugStatus>(dto.Status, true, out var status);

            bug.Title = dto.Title;
            bug.Description = dto.Description;
            bug.ExpectedResult = dto.ExpectedResult;
            bug.ActualResult = dto.ActualResult;
            bug.Severity = severity;
            bug.Priority = priority;
            bug.Status = status;
            bug.Environment = dto.Environment;
            bug.Components = dto.Components;
            bug.Labels = dto.Labels;
            bug.AssigneeId = dto.AssigneeId;
            bug.DueDate = dto.DueDate;
            bug.AffectedVersion = dto.AffectedVersion;
            bug.FixVersion = dto.FixVersion;
            bug.Resolution = dto.Resolution;
            bug.ProjectId = dto.ProjectId;

            await _context.SaveChangesAsync();

            return NoContent();
        }

        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteBug(int id)
        {
            var bug = await _context.Bugs.FindAsync(id);
            if (bug == null)
            {
                return NotFound($"Bug with ID {id} not found.");
            }

            _context.Bugs.Remove(bug);
            await _context.SaveChangesAsync();

            return NoContent();
        }
    }

    public class BugDto
    {
        public int Id { get; set; }
        public string Title { get; set; } = string.Empty;
        public string OriginalCulture { get; set; } = "en";
        public string Description { get; set; } = string.Empty;
        public string? ExpectedResult { get; set; }
        public string? ActualResult { get; set; }
        public string Severity { get; set; } = string.Empty;
        public string Priority { get; set; } = string.Empty;
        public string Status { get; set; } = string.Empty;
        public string? Environment { get; set; }
        public string? Components { get; set; }
        public string? Labels { get; set; }
        public string? ReporterId { get; set; }
        public string? AssigneeId { get; set; }
        public DateTime ReportedDate { get; set; }
        public DateTime? DueDate { get; set; }
        public string? AffectedVersion { get; set; }
        public string? FixVersion { get; set; }
        public string? Resolution { get; set; }
        public int? ProjectId { get; set; }
        public string? JiraBugReference { get; set; }
    }

    public class CreateBugDto
    {
        public string Title { get; set; } = string.Empty;
        public string? OriginalCulture { get; set; } = "en";
        public string Description { get; set; } = string.Empty;
        public string? ExpectedResult { get; set; }
        public string? ActualResult { get; set; }
        public string Severity { get; set; } = "Minor";
        public string Priority { get; set; } = "Medium";
        public string Status { get; set; } = "Open";
        public string? Environment { get; set; }
        public string? Components { get; set; }
        public string? Labels { get; set; }
        public string? AssigneeId { get; set; }
        public DateTime? DueDate { get; set; }
        public string? AffectedVersion { get; set; }
        public string? FixVersion { get; set; }
        public string? Resolution { get; set; }
        public int? ProjectId { get; set; }

        // Jira Sync properties
        public bool SyncToJira { get; set; } = false;
        public string? JiraProjectOrIssueKey { get; set; }
    }

    public class UpdateBugDto
    {
        public string Title { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
        public string? ExpectedResult { get; set; }
        public string? ActualResult { get; set; }
        public string Severity { get; set; } = "Minor";
        public string Priority { get; set; } = "Medium";
        public string Status { get; set; } = "Open";
        public string? Environment { get; set; }
        public string? Components { get; set; }
        public string? Labels { get; set; }
        public string? AssigneeId { get; set; }
        public DateTime? DueDate { get; set; }
        public string? AffectedVersion { get; set; }
        public string? FixVersion { get; set; }
        public string? Resolution { get; set; }
        public int? ProjectId { get; set; }
    }
}
