using System.ComponentModel.DataAnnotations;

namespace LoreTest.Data
{
    public class TestProject
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [StringLength(200)]
        public string Title { get; set; } = string.Empty;

        public string? Description { get; set; }
    }
}
