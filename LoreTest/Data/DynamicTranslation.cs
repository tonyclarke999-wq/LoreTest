using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public class DynamicTranslation
    {
        public int Id { get; set; }

        [Required]
        public int LanguageId { get; set; }

        [ForeignKey("LanguageId")]
        public SupportedLanguage Language { get; set; } = null!;

        [Required]
        [StringLength(255)]
        public string FieldKey { get; set; } = string.Empty;

        [Required]
        public string TranslatedValue { get; set; } = string.Empty;
    }
}
