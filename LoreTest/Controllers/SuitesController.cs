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
    public class SuitesController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public SuitesController(ApplicationDbContext context)
        {
            _context = context;
        }

        [HttpGet("api/projects/{projectId}/suites")]
        public async Task<ActionResult<IEnumerable<TestSuiteDto>>> GetSuitesForProject(int projectId)
        {
            var projectExists = await _context.TestProjects.AnyAsync(p => p.Id == projectId);
            if (!projectExists)
            {
                return NotFound($"Project with ID {projectId} not found.");
            }

            var suites = await _context.TestSuites
                .Where(s => s.TestProjectId == projectId)
                .Select(s => new TestSuiteDto
                {
                    Id = s.Id,
                    Title = s.Title,
                    Description = s.Description,
                    TestProjectId = s.TestProjectId
                })
                .ToListAsync();

            return Ok(suites);
        }

        [HttpGet("api/suites/{id}")]
        public async Task<ActionResult<TestSuiteDto>> GetSuite(int id)
        {
            var suite = await _context.TestSuites.FindAsync(id);
            if (suite == null)
            {
                return NotFound($"Test Suite with ID {id} not found.");
            }

            return Ok(new TestSuiteDto
            {
                Id = suite.Id,
                Title = suite.Title,
                Description = suite.Description,
                TestProjectId = suite.TestProjectId
            });
        }

        [HttpPost("api/projects/{projectId}/suites")]
        public async Task<ActionResult<TestSuiteDto>> CreateSuite(int projectId, [FromBody] CreateTestSuiteDto dto)
        {
            if (!User.IsInRole("Admin") && !User.IsInRole("Administrator") && !User.IsInRole("Editor"))
            {
                return Forbid(JwtBearerDefaults.AuthenticationScheme);
            }

            if (dto == null || string.IsNullOrWhiteSpace(dto.Title))
            {
                return BadRequest("Title is required.");
            }

            var projectExists = await _context.TestProjects.AnyAsync(p => p.Id == projectId);
            if (!projectExists)
            {
                return NotFound($"Project with ID {projectId} not found.");
            }

            var suite = new TestSuite
            {
                Title = dto.Title,
                Description = dto.Description,
                TestProjectId = projectId
            };

            _context.TestSuites.Add(suite);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetSuite), new { id = suite.Id }, new TestSuiteDto
            {
                Id = suite.Id,
                Title = suite.Title,
                Description = suite.Description,
                TestProjectId = suite.TestProjectId
            });
        }

        [HttpPut("api/suites/{id}")]
        public async Task<IActionResult> UpdateSuite(int id, [FromBody] UpdateTestSuiteDto dto)
        {
            if (!User.IsInRole("Admin") && !User.IsInRole("Administrator") && !User.IsInRole("Editor"))
            {
                return Forbid(JwtBearerDefaults.AuthenticationScheme);
            }

            if (dto == null || string.IsNullOrWhiteSpace(dto.Title))
            {
                return BadRequest("Title is required.");
            }

            var suite = await _context.TestSuites.FindAsync(id);
            if (suite == null)
            {
                return NotFound($"Test Suite with ID {id} not found.");
            }

            suite.Title = dto.Title;
            suite.Description = dto.Description;

            await _context.SaveChangesAsync();

            return NoContent();
        }

        [HttpDelete("api/suites/{id}")]
        public async Task<IActionResult> DeleteSuite(int id)
        {
            if (!User.IsInRole("Admin") && !User.IsInRole("Administrator"))
            {
                return Forbid(JwtBearerDefaults.AuthenticationScheme);
            }

            var suite = await _context.TestSuites.FindAsync(id);
            if (suite == null)
            {
                return NotFound($"Test Suite with ID {id} not found.");
            }

            _context.TestSuites.Remove(suite);
            await _context.SaveChangesAsync();

            return NoContent();
        }
    }

    public class TestSuiteDto
    {
        public int Id { get; set; }
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
        public int TestProjectId { get; set; }
    }

    public class CreateTestSuiteDto
    {
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
    }

    public class UpdateTestSuiteDto
    {
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
    }
}
