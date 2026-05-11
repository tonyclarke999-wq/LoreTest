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
        public int FieldId { get; set; }

        [ForeignKey("FieldId")]
        public LocalizationField Field { get; set; } = null!;

        [Required]
        public string TranslatedValue { get; set; } = string.Empty;
    }
}
