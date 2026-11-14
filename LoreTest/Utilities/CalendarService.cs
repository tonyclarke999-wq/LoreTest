#nullable enable
using System.Security.Claims;
using LoreTest.Data;
using Microsoft.EntityFrameworkCore;

namespace LoreTest.Utilities
{
    public class CalendarConflictInfo
    {
        public Guid ConflictingEventId { get; set; }
        public string ConflictingEventTitle { get; set; } = string.Empty;
        public DateTime StartTimeUtc { get; set; }
        public DateTime EndTimeUtc { get; set; }
        public List<string> ConflictingUserNames { get; set; } = [];
    }

    public class CalendarFilterOptions
    {
        public int? ProjectId { get; set; }
        public string? UserId { get; set; }
        public string? SearchTerm { get; set; }
        public HashSet<CalendarEventType> EventTypes { get; set; } =
        [
            CalendarEventType.TestRunExecution,
            CalendarEventType.BugTriage,
            CalendarEventType.TeamMeeting,
            CalendarEventType.ReleaseFreeze,
            CalendarEventType.Milestone,
            CalendarEventType.FocusTime
        ];
        public bool OnlyConflicts { get; set; }
    }

    public class CalendarEventInput
    {
        public Guid? Id { get; set; }
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
        public DateTime StartTimeUtc { get; set; }
        public DateTime EndTimeUtc { get; set; }
        public bool IsAllDay { get; set; }
        public CalendarEventType EventType { get; set; } = CalendarEventType.TeamMeeting;
        public MeetingPlatform Platform { get; set; } = MeetingPlatform.None;
        public string? MeetingUrl { get; set; }
        public int? ProjectId { get; set; }
        public int? TestRunId { get; set; }
        public List<string> AttendeeUserIds { get; set; } = [];
    }

    public class CalendarService(IDbContextFactory<ApplicationDbContext> dbContextFactory)
    {
        private readonly IDbContextFactory<ApplicationDbContext> _dbContextFactory = dbContextFactory;

        public static bool IsAdmin(ClaimsPrincipal user) =>
            user.IsInRole("Administrator") || user.IsInRole("Admin");

        public async Task<List<CalendarEvent>> GetEventsAsync(
            DateTime rangeStartUtc,
            DateTime rangeEndUtc,
            ClaimsPrincipal currentUser,
            CalendarFilterOptions? filter = null)
        {
            await using var db = await _dbContextFactory.CreateDbContextAsync();
            var userId = currentUser.FindFirstValue(ClaimTypes.NameIdentifier) ?? string.Empty;
            var isAdmin = IsAdmin(currentUser);

            var query = db.CalendarEvents
                .AsNoTracking()
                .Include(e => e.Organizer)
                .Include(e => e.Project)
                .Include(e => e.TestRun)
                .Include(e => e.Attendees)
                    .ThenInclude(a => a.User)
                .Where(e => e.StartTimeUtc < rangeEndUtc && e.EndTimeUtc > rangeStartUtc);

            // Role-based visibility: Testers & Editors see only their own or invited events;
            // Administrators see all events.
            if (!isAdmin)
            {
                query = query.Where(e =>
                    e.OrganizerId == userId ||
                    e.Attendees.Any(a => a.UserId == userId));
            }
            else if (!string.IsNullOrWhiteSpace(filter?.UserId))
            {
                var targetUserId = filter.UserId;
                query = query.Where(e =>
                    e.OrganizerId == targetUserId ||
                    e.Attendees.Any(a => a.UserId == targetUserId));
            }

            if (filter != null)
            {
                if (filter.ProjectId.HasValue)
                {
                    query = query.Where(e => e.ProjectId == filter.ProjectId.Value);
                }

                if (filter.EventTypes.Count > 0)
                {
                    query = query.Where(e => filter.EventTypes.Contains(e.EventType));
                }
                else
                {
                    return [];
                }

                if (filter.OnlyConflicts)
                {
                    query = query.Where(e => e.HasConflict);
                }

                if (!string.IsNullOrWhiteSpace(filter.SearchTerm))
                {
                    var term = filter.SearchTerm.Trim().ToLower();
                    query = query.Where(e =>
                        e.Title.ToLower().Contains(term) ||
                        (e.Description != null && e.Description.ToLower().Contains(term)));
                }
            }

            return await query
                .OrderBy(e => e.StartTimeUtc)
                .ToListAsync();
        }

        public async Task<List<CalendarConflictInfo>> CheckConflictsAsync(
            DateTime startUtc,
            DateTime endUtc,
            IEnumerable<string> participantUserIds,
            Guid? excludeEventId = null)
        {
            var participantSet = participantUserIds
                .Where(id => !string.IsNullOrWhiteSpace(id))
                .Distinct()
                .ToHashSet();

            if (participantSet.Count == 0 || endUtc <= startUtc)
            {
                return [];
            }

            await using var db = await _dbContextFactory.CreateDbContextAsync();

            var overlappingEvents = await db.CalendarEvents
                .AsNoTracking()
                .Include(e => e.Organizer)
                .Include(e => e.Attendees)
                    .ThenInclude(a => a.User)
                .Where(e => e.StartTimeUtc < endUtc && e.EndTimeUtc > startUtc)
                .Where(e => !excludeEventId.HasValue || e.Id != excludeEventId.Value)
                .ToListAsync();

            var conflicts = new List<CalendarConflictInfo>();

            foreach (var ev in overlappingEvents)
            {
                var collidingNames = new List<string>();

                if (participantSet.Contains(ev.OrganizerId))
                {
                    collidingNames.Add(FormatUserName(ev.Organizer));
                }

                foreach (var att in ev.Attendees)
                {
                    if (att.Status != AttendeeStatus.Declined &&
                        participantSet.Contains(att.UserId) &&
                        att.UserId != ev.OrganizerId)
                    {
                        collidingNames.Add(FormatUserName(att.User));
                    }
                }

                if (collidingNames.Count > 0)
                {
                    conflicts.Add(new CalendarConflictInfo
                    {
                        ConflictingEventId = ev.Id,
                        ConflictingEventTitle = ev.Title,
                        StartTimeUtc = ev.StartTimeUtc,
                        EndTimeUtc = ev.EndTimeUtc,
                        ConflictingUserNames = collidingNames.Distinct().ToList()
                    });
                }
            }

            return conflicts;
        }

        public async Task<DateTime?> SuggestNextFreeSlotAsync(
            DateTime desiredStartUtc,
            TimeSpan duration,
            IEnumerable<string> participantUserIds,
            Guid? excludeEventId = null)
        {
            if (duration <= TimeSpan.Zero)
            {
                duration = TimeSpan.FromMinutes(30);
            }

            var candidateStart = RoundUpToHalfHour(desiredStartUtc);
            var searchLimit = candidateStart.AddDays(7);

            while (candidateStart < searchLimit)
            {
                // Prefer standard working hours (08:30 - 18:00) on weekdays
                if (candidateStart.DayOfWeek is DayOfWeek.Saturday or DayOfWeek.Sunday ||
                    candidateStart.TimeOfDay < TimeSpan.FromHours(8.5) ||
                    candidateStart.Add(duration).TimeOfDay > TimeSpan.FromHours(18))
                {
                    candidateStart = candidateStart.Date.AddDays(1).AddHours(9);
                    continue;
                }

                var conflicts = await CheckConflictsAsync(
                    candidateStart,
                    candidateStart.Add(duration),
                    participantUserIds,
                    excludeEventId);

                if (conflicts.Count == 0)
                {
                    return candidateStart;
                }

                candidateStart = candidateStart.AddMinutes(30);
            }

            return null;
        }

        public async Task<(CalendarEvent Event, List<CalendarConflictInfo> Conflicts)> SaveEventAsync(
            CalendarEventInput input,
            ClaimsPrincipal currentUser)
        {
            var userId = currentUser.FindFirstValue(ClaimTypes.NameIdentifier)
                ?? throw new InvalidOperationException("User is not authenticated.");
            var isAdmin = IsAdmin(currentUser);

            await using var db = await _dbContextFactory.CreateDbContextAsync();

            var allParticipants = input.AttendeeUserIds
                .Where(id => !string.IsNullOrWhiteSpace(id))
                .Append(userId)
                .Distinct()
                .ToList();

            var conflicts = await CheckConflictsAsync(
                input.StartTimeUtc,
                input.EndTimeUtc,
                allParticipants,
                input.Id);

            CalendarEvent entity;
            if (input.Id.HasValue)
            {
                entity = await db.CalendarEvents
                    .Include(e => e.Attendees)
                    .FirstOrDefaultAsync(e => e.Id == input.Id.Value)
                    ?? throw new KeyNotFoundException("Calendar event not found.");

                if (!isAdmin && entity.OrganizerId != userId)
                {
                    throw new UnauthorizedAccessException("Only the organizer or an administrator can edit this event.");
                }

                db.CalendarEventAttendees.RemoveRange(entity.Attendees);
            }
            else
            {
                entity = new CalendarEvent
                {
                    OrganizerId = userId,
                    CreatedAt = DateTime.UtcNow
                };
                db.CalendarEvents.Add(entity);
            }

            entity.Title = input.Title.Trim();
            entity.Description = string.IsNullOrWhiteSpace(input.Description) ? null : input.Description.Trim();
            entity.StartTimeUtc = DateTime.SpecifyKind(input.StartTimeUtc, DateTimeKind.Utc);
            entity.EndTimeUtc = DateTime.SpecifyKind(input.EndTimeUtc, DateTimeKind.Utc);
            entity.IsAllDay = input.IsAllDay;
            entity.EventType = input.EventType;
            entity.Platform = input.Platform;
            entity.ProjectId = input.ProjectId;
            entity.TestRunId = input.TestRunId;
            entity.UpdatedAt = DateTime.UtcNow;

            // Permissive conflict model: allow saving, but record and report conflicts
            entity.HasConflict = conflicts.Count > 0;
            entity.ConflictSummary = conflicts.Count > 0
                ? string.Join("; ", conflicts.Select(c =>
                    $"{string.Join(", ", c.ConflictingUserNames)} overlapped with '{c.ConflictingEventTitle}' ({c.StartTimeUtc:HH:mm}-{c.EndTimeUtc:HH:mm})"))
                : null;

            // Online meeting URL handling
            if (input.Platform == MeetingPlatform.None)
            {
                entity.MeetingUrl = null;
                entity.MeetingId = null;
            }
            else if (!string.IsNullOrWhiteSpace(input.MeetingUrl))
            {
                entity.MeetingUrl = input.MeetingUrl.Trim();
            }
            else
            {
                var (url, meetingId) = GenerateMeetingLink(input.Platform, entity.Id);
                entity.MeetingUrl = url;
                entity.MeetingId = meetingId;
            }

            // Add attendees (excluding organizer or keeping organizer as accepted)
            foreach (var attId in input.AttendeeUserIds.Where(id => !string.IsNullOrWhiteSpace(id) && id != entity.OrganizerId).Distinct())
            {
                entity.Attendees.Add(new CalendarEventAttendee
                {
                    EventId = entity.Id,
                    UserId = attId,
                    Status = AttendeeStatus.NeedsAction,
                    IsRequired = true,
                    AddedAt = DateTime.UtcNow
                });
            }

            await db.SaveChangesAsync();
            return (entity, conflicts);
        }

        public async Task UpdateRsvpAsync(Guid eventId, string userId, AttendeeStatus status)
        {
            await using var db = await _dbContextFactory.CreateDbContextAsync();
            var attendee = await db.CalendarEventAttendees
                .FirstOrDefaultAsync(a => a.EventId == eventId && a.UserId == userId);

            if (attendee != null)
            {
                attendee.Status = status;
                await db.SaveChangesAsync();
            }
        }

        public async Task DeleteEventAsync(Guid eventId, ClaimsPrincipal currentUser)
        {
            var userId = currentUser.FindFirstValue(ClaimTypes.NameIdentifier);
            var isAdmin = IsAdmin(currentUser);

            await using var db = await _dbContextFactory.CreateDbContextAsync();
            var entity = await db.CalendarEvents.FirstOrDefaultAsync(e => e.Id == eventId);
            if (entity == null) return;

            if (!isAdmin && entity.OrganizerId != userId)
            {
                throw new UnauthorizedAccessException("Only the organizer or an administrator can delete this event.");
            }

            db.CalendarEvents.Remove(entity);
            await db.SaveChangesAsync();
        }

        public static (string Url, string MeetingId) GenerateMeetingLink(MeetingPlatform platform, Guid seed)
        {
            var shortCode = seed.ToString("N")[..8];
            return platform switch
            {
                MeetingPlatform.MicrosoftTeams => (
                    $"https://teams.microsoft.com/l/meetup-join/19%3ameeting_{shortCode}%40thread.v2/0",
                    $"teams-{shortCode}"),
                MeetingPlatform.GoogleMeet => (
                    $"https://meet.google.com/{shortCode[..3]}-{shortCode[3..7]}-{shortCode[7..]}",
                    $"meet-{shortCode}"),
                _ => (string.Empty, string.Empty)
            };
        }

        private static string FormatUserName(ApplicationUser? user) =>
            user == null ? "Unknown User" : (!string.IsNullOrWhiteSpace(user.Name) ? user.Name : user.Email ?? user.UserName ?? "User");

        private static DateTime RoundUpToHalfHour(DateTime dt)
        {
            var minutes = dt.Minute < 30 ? 30 : 60;
            return new DateTime(dt.Year, dt.Month, dt.Day, dt.Hour, 0, 0, DateTimeKind.Utc).AddMinutes(minutes);
        }
    }
}
