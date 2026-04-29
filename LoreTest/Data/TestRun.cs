using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public class TestRun
    {
        [Key]
        public int Id { get; set; }

        [Required]
        public int RunNumber { get; set; }

        [Required]
        public DateTime StartTime { get; set; }

        public DateTime? EndTime { get; set; }

        [Required]
        public int TestProjectId { get; set; }

        [ForeignKey(nameof(TestProjectId))]
        public TestProject? Project { get; set; }

        [Required]
        public int TestSuiteId { get; set; }

        [ForeignKey(nameof(TestSuiteId))]
        public TestSuite? TestSuite { get; set; }

        [Required]
        public string StartedByUserId { get; set; } = string.Empty;

        [ForeignKey(nameof(StartedByUserId))]
        public ApplicationUser? User { get; set; }

        [Required]
        [StringLength(50)]
        public string Status { get; set; } = "In Progress";

        public ICollection<TestRunCaseResult> CaseResults { get; set; } = new List<TestRunCaseResult>();
    }
}
