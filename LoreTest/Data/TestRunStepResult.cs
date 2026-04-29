using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public class TestRunStepResult
    {
        [Key]
        public int Id { get; set; }

        [Required]
        public int TestRunCaseResultId { get; set; }

        [ForeignKey(nameof(TestRunCaseResultId))]
        public TestRunCaseResult? CaseResult { get; set; }

        [Required]
        public int TestStepId { get; set; }

        [ForeignKey(nameof(TestStepId))]
        public TestStep? TestStep { get; set; }

        [Required]
        [StringLength(50)]
        public string Status { get; set; } = "Not Run";

        public string? ActualResult { get; set; }
    }
}
