using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace LoreTest.Data
{
    public class TestCase
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [StringLength(200)]
        public string Title { get; set; } = string.Empty;

        public string? Description { get; set; }
        
        public string? PreConditions { get; set; }
        
        public string? Dependencies { get; set; }
        
        public string? TestData { get; set; }
        
        public string? PostCondition { get; set; }
        
        [StringLength(50)]
        public string? Status { get; set; }
        
        [StringLength(50)]
        public string? Priority { get; set; }
        
        [StringLength(100)]
        public string? DefectId { get; set; }
        
        public string? Notes { get; set; }

        [Required]
        public int TestSuiteId { get; set; }

        [ForeignKey(nameof(TestSuiteId))]
        public TestSuite? TestSuite { get; set; }

        public ICollection<TestStep> TestSteps { get; set; } = new List<TestStep>();
    }
}
