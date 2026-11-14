#nullable enable
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public enum CalendarEventType
    {
        TestRunExecution,
        BugTriage,
        TeamMeeting,
        ReleaseFreeze,
        SprintMilestone,
        FocusTime
    }

    public enum MeetingPlatform
    {
        None,
        MicrosoftTeams,
        GoogleMeet,
        CustomUrl
    }

    [Table("CalendarEvents")]
    public class CalendarEvent
    {
        [Key]
        public Guid Id { get; set; } = Guid.NewGuid();

        [Required]
        [StringLength(200)]
        public string Title { get; set; } = string.Empty;

        [StringLength(2000)]
        public string? Description { get; set; }

        [Required]
        public DateTime StartTimeUtc { get; set; }

        [Required]
        public DateTime EndTimeUtc { get; set; }

        public bool IsAllDay { get; set; }

        [Required]
        public CalendarEventType EventType { get; set; } = CalendarEventType.TeamMeeting;

        public MeetingPlatform Platform { get; set; } = MeetingPlatform.None;

        [StringLength(1000)]
        public string? MeetingUrl { get; set; }

        [StringLength(200)]
        public string? MeetingId { get; set; }

        public bool HasConflict { get; set; }

        [StringLength(1000)]
        public string? ConflictSummary { get; set; }

        [Required]
        public string OrganizerId { get; set; } = string.Empty;

        [ForeignKey(nameof(OrganizerId))]
        public ApplicationUser? Organizer { get; set; }

        public int? ProjectId { get; set; }

        [ForeignKey(nameof(ProjectId))]
        public TestProject? Project { get; set; }

        public int? TestRunId { get; set; }

        [ForeignKey(nameof(TestRunId))]
        public TestRun? TestRun { get; set; }

        [StringLength(255)]
        public string? RecurrenceRule { get; set; }

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
        public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

        public ICollection<CalendarEventAttendee> Attendees { get; set; } = [];
    }
}
