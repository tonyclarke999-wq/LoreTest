#nullable enable
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public enum AttendeeStatus
    {
        NeedsAction,
        Accepted,
        Declined,
        Tentative
    }

    [Table("CalendarEventAttendees")]
    public class CalendarEventAttendee
    {
        [Required]
        public Guid EventId { get; set; }

        [ForeignKey(nameof(EventId))]
        public CalendarEvent? Event { get; set; }

        [Required]
        public string UserId { get; set; } = string.Empty;

        [ForeignKey(nameof(UserId))]
        public ApplicationUser? User { get; set; }

        public AttendeeStatus Status { get; set; } = AttendeeStatus.NeedsAction;

        public bool IsRequired { get; set; } = true;

        public DateTime AddedAt { get; set; } = DateTime.UtcNow;
    }
}
