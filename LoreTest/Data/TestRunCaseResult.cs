using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public class TestRunCaseResult
    {
        [Key]
        public int Id { get; set; }

        [Required]
        public int TestRunId { get; set; }

        [ForeignKey(nameof(TestRunId))]
        public TestRun? TestRun { get; set; }

        [Required]
        public int TestCaseId { get; set; }

        [ForeignKey(nameof(TestCaseId))]
        public TestCase? TestCase { get; set; }

        [Required]
        [StringLength(50)]
        public string Status { get; set; } = "Pending";

        public ICollection<TestRunStepResult> StepResults { get; set; } = new List<TestRunStepResult>();
    }
}
