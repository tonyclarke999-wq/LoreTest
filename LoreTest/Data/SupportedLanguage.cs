using System.ComponentModel.DataAnnotations;

namespace LoreTest.Data
{
    public class SupportedLanguage
    {
        public int Id { get; set; }

        [Required]
        [StringLength(10)]
        public string Code { get; set; } = string.Empty; // e.g. "en", "de"

        [Required]
        [StringLength(100)]
        public string Name { get; set; } = string.Empty; // e.g. "English", "Deutsch"

        public bool IsDefault { get; set; }
    }
}
