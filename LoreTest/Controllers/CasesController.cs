using LoreTest.Data;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace LoreTest.Controllers
{
    [ApiController]
    [Authorize(AuthenticationSchemes = JwtBearerDefaults.AuthenticationScheme)]
    [IgnoreAntiforgeryToken]
    [Microsoft.AspNetCore.RateLimiting.EnableRateLimiting("api-policy")]
    public class CasesController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public CasesController(ApplicationDbContext context)
        {
            _context = context;
        }

        [HttpGet("api/suites/{suiteId}/cases")]
        public async Task<ActionResult<IEnumerable<TestCaseDto>>> GetCasesForSuite(int suiteId)
        {
            var suiteExists = await _context.TestSuites.AnyAsync(s => s.Id == suiteId);
            if (!suiteExists)
            {
                return NotFound($"Test Suite with ID {suiteId} not found.");
            }

            var cases = await _context.TestCases
                .Where(c => c.TestSuiteId == suiteId)
                .Select(c => new TestCaseDto
                {
                    Id = c.Id,
                    Title = c.Title,
                    Description = c.Description,
                    PreConditions = c.PreConditions,
                    Dependencies = c.Dependencies,
                    TestData = c.TestData,
                    PostCondition = c.PostCondition,
                    Status = c.Status,
                    Priority = c.Priority,
                    DefectId = c.DefectId,
                    Notes = c.Notes,
                    TestSuiteId = c.TestSuiteId
                })
                .ToListAsync();

            return Ok(cases);
        }

        [HttpGet("api/cases/{id}")]
        public async Task<ActionResult<TestCaseDto>> GetCase(int id)
        {
            var testCase = await _context.TestCases.FindAsync(id);
            if (testCase == null)
            {
                return NotFound($"Test Case with ID {id} not found.");
            }

            return Ok(new TestCaseDto
            {
                Id = testCase.Id,
                Title = testCase.Title,
                Description = testCase.Description,
                PreConditions = testCase.PreConditions,
                Dependencies = testCase.Dependencies,
                TestData = testCase.TestData,
                PostCondition = testCase.PostCondition,
                Status = testCase.Status,
                Priority = testCase.Priority,
                DefectId = testCase.DefectId,
                Notes = testCase.Notes,
                TestSuiteId = testCase.TestSuiteId
            });
        }

        [HttpPost("api/suites/{suiteId}/cases")]
        public async Task<ActionResult<TestCaseDto>> CreateCase(int suiteId, [FromBody] CreateTestCaseDto dto)
        {
            if (!User.IsInRole("Admin") && !User.IsInRole("Administrator") && !User.IsInRole("Editor"))
            {
                return Forbid(JwtBearerDefaults.AuthenticationScheme);
            }

            if (dto == null || string.IsNullOrWhiteSpace(dto.Title))
            {
                return BadRequest("Title is required.");
            }

            var suiteExists = await _context.TestSuites.AnyAsync(s => s.Id == suiteId);
            if (!suiteExists)
            {
                return NotFound($"Test Suite with ID {suiteId} not found.");
            }

            var testCase = new TestCase
            {
                Title = dto.Title,
                Description = dto.Description,
                PreConditions = dto.PreConditions,
                Dependencies = dto.Dependencies,
                TestData = dto.TestData,
                PostCondition = dto.PostCondition,
                Status = dto.Status ?? "Draft",
                Priority = dto.Priority ?? "Medium",
                DefectId = dto.DefectId,
                Notes = dto.Notes,
                TestSuiteId = suiteId
            };

            _context.TestCases.Add(testCase);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetCase), new { id = testCase.Id }, new TestCaseDto
            {
                Id = testCase.Id,
                Title = testCase.Title,
                Description = testCase.Description,
                PreConditions = testCase.PreConditions,
                Dependencies = testCase.Dependencies,
                TestData = testCase.TestData,
                PostCondition = testCase.PostCondition,
                Status = testCase.Status,
                Priority = testCase.Priority,
                DefectId = testCase.DefectId,
                Notes = testCase.Notes,
                TestSuiteId = testCase.TestSuiteId
            });
        }

        [HttpPut("api/cases/{id}")]
        public async Task<IActionResult> UpdateCase(int id, [FromBody] UpdateTestCaseDto dto)
        {
            if (!User.IsInRole("Admin") && !User.IsInRole("Administrator") && !User.IsInRole("Editor"))
            {
                return Forbid(JwtBearerDefaults.AuthenticationScheme);
            }

            if (dto == null || string.IsNullOrWhiteSpace(dto.Title))
            {
                return BadRequest("Title is required.");
            }

            var testCase = await _context.TestCases.FindAsync(id);
            if (testCase == null)
            {
                return NotFound($"Test Case with ID {id} not found.");
            }

            testCase.Title = dto.Title;
            testCase.Description = dto.Description;
            testCase.PreConditions = dto.PreConditions;
            testCase.Dependencies = dto.Dependencies;
            testCase.TestData = dto.TestData;
            testCase.PostCondition = dto.PostCondition;
            testCase.Status = dto.Status;
            testCase.Priority = dto.Priority;
            testCase.DefectId = dto.DefectId;
            testCase.Notes = dto.Notes;

            await _context.SaveChangesAsync();

            return NoContent();
        }

        [HttpDelete("api/cases/{id}")]
        public async Task<IActionResult> DeleteCase(int id)
        {
            if (!User.IsInRole("Admin") && !User.IsInRole("Administrator"))
            {
                return Forbid(JwtBearerDefaults.AuthenticationScheme);
            }

            var testCase = await _context.TestCases.FindAsync(id);
            if (testCase == null)
            {
                return NotFound($"Test Case with ID {id} not found.");
            }

            _context.TestCases.Remove(testCase);
            await _context.SaveChangesAsync();

            return NoContent();
        }
    }

    public class TestCaseDto
    {
        public int Id { get; set; }
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
        public string? PreConditions { get; set; }
        public string? Dependencies { get; set; }
        public string? TestData { get; set; }
        public string? PostCondition { get; set; }
        public string? Status { get; set; }
        public string? Priority { get; set; }
        public string? DefectId { get; set; }
        public string? Notes { get; set; }
        public int TestSuiteId { get; set; }
    }

    public class CreateTestCaseDto
    {
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
        public string? PreConditions { get; set; }
        public string? Dependencies { get; set; }
        public string? TestData { get; set; }
        public string? PostCondition { get; set; }
        public string? Status { get; set; }
        public string? Priority { get; set; }
        public string? DefectId { get; set; }
        public string? Notes { get; set; }
    }

    public class UpdateTestCaseDto
    {
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
        public string? PreConditions { get; set; }
        public string? Dependencies { get; set; }
        public string? TestData { get; set; }
        public string? PostCondition { get; set; }
        public string? Status { get; set; }
        public string? Priority { get; set; }
        public string? DefectId { get; set; }
        public string? Notes { get; set; }
    }
}
