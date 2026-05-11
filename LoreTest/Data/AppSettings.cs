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

        [StringLength(500)]
        public string? TranslationApiKey { get; set; }

        public DateTime LastUpdated { get; set; } = DateTime.UtcNow;
        public string? UpdatedBy { get; set; }
    }
}
