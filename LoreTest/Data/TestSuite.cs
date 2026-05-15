#nullable enable
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public class TestSuite
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [StringLength(200)]
        public string Title { get; set; } = string.Empty;

        public string? Description { get; set; }

        [Required]
        public int TestProjectId { get; set; }

        [ForeignKey(nameof(TestProjectId))]
        public TestProject? Project { get; set; }

        public ICollection<TestCase> TestCases { get; set; } = [];
    }
}
