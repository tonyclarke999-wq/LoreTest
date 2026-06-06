#nullable enable
using System.ComponentModel.DataAnnotations;

namespace LoreTest.Data
{
    public class AppSettings
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [StringLength(100)]
        public string TranslationApi { get; set; } = "Mock"; // e.g. "Google", "Azure", "Mock"

        [Required]
        [StringLength(50)]
        public string TelemetryLevel { get; set; } = "LoginOnly"; // e.g. "None", "LoginOnly", "Full"

        [StringLength(500)]
        public string? TranslationApiKey { get; set; }

        [StringLength(500)]
        public string? JiraApiToken { get; set; }

        [StringLength(200)]
        public string? JiraBaseUrl { get; set; }

        [StringLength(200)]
        public string? JiraEmail { get; set; }

        public DateTime LastUpdated { get; set; } = DateTime.UtcNow;
        public string? UpdatedBy { get; set; }
    }
}
