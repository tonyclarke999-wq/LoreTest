#nullable enable
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public class TestStep
    {
        [Key]
        public int Id { get; set; }

        [Required]
        public int StepNumber { get; set; }

        public string? Description { get; set; }

        public string? ExpectedResult { get; set; }

        public string? ActualResult { get; set; }

        [Required]
        public int TestCaseId { get; set; }

        [ForeignKey(nameof(TestCaseId))]
        public TestCase? TestCase { get; set; }
    }
}
