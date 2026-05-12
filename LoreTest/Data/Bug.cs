using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public enum BugSeverity
    {
        Critical,
        Major,
        Minor,
        Cosmetic
    }

    public enum BugPriority
    {
        High,
        Medium,
        Low
    }

    public enum BugStatus
    {
        Open,
        InProgress,
        Resolved,
        Closed,
        Reopened
    }

    public class Bug
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [StringLength(200)]
        public string Title { get; set; } = string.Empty;

        [Required]
        [StringLength(10)]
        public string OriginalCulture { get; set; } = "en";

        [Required]
        public string Description { get; set; } = string.Empty;

        public string? ExpectedResult { get; set; }
        public string? ActualResult { get; set; }

        public BugSeverity Severity { get; set; }
        public BugPriority Priority { get; set; }
        public BugStatus Status { get; set; } = BugStatus.Open;

        public string? Environment { get; set; }
        public string? Components { get; set; }
        public string? Labels { get; set; }

        public string? ReporterId { get; set; }
        public ApplicationUser? Reporter { get; set; }

        public string? AssigneeId { get; set; }
        public ApplicationUser? Assignee { get; set; }

        public DateTime ReportedDate { get; set; } = DateTime.UtcNow;
        public DateTime? DueDate { get; set; }

        public string? AffectedVersion { get; set; }
        public string? FixVersion { get; set; }
        public string? Resolution { get; set; }

        public int? ProjectId { get; set; }
        public TestProject? Project { get; set; }

        [StringLength(200)]
        public string? JiraBugReference { get; set; }

        public ICollection<BugAttachment> Attachments { get; set; } = [];
    }

    public class BugAttachment
    {
        [Key]
        public int Id { get; set; }

        [Required]
        public string FileName { get; set; } = string.Empty;

        [Required]
        public string FilePath { get; set; } = string.Empty;

        public string? ContentType { get; set; }
        public long FileSize { get; set; }
        public DateTime UploadedDate { get; set; } = DateTime.UtcNow;

        public int BugId { get; set; }
        public Bug? Bug { get; set; }
    }
}
