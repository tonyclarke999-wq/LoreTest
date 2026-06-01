using LoreTest.Data;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace LoreTest.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    [Authorize(AuthenticationSchemes = JwtBearerDefaults.AuthenticationScheme)]
    [IgnoreAntiforgeryToken]
    [Microsoft.AspNetCore.RateLimiting.EnableRateLimiting("api-policy")]
    public class ProjectsController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public ProjectsController(ApplicationDbContext context)
        {
            _context = context;
        }

        [HttpGet]
        public async Task<ActionResult<IEnumerable<TestProjectDto>>> GetProjects()
        {
            var projects = await _context.TestProjects
                .Select(p => new TestProjectDto
                {
                    Id = p.Id,
                    Title = p.Title,
                    Description = p.Description,
                    JiraReference = p.JiraReference
                })
                .ToListAsync();

            return Ok(projects);
        }

        [HttpGet("{id}")]
        public async Task<ActionResult<TestProjectDto>> GetProject(int id)
        {
            var project = await _context.TestProjects.FindAsync(id);

            if (project == null)
            {
                return NotFound($"Project with ID {id} not found.");
            }

            return Ok(new TestProjectDto
            {
                Id = project.Id,
                Title = project.Title,
                Description = project.Description,
                JiraReference = project.JiraReference
            });
        }

        [HttpPost]
        public async Task<ActionResult<TestProjectDto>> CreateProject([FromBody] CreateTestProjectDto dto)
        {
            // Only Editor and Admin/Administrator can create projects
            if (!User.IsInRole("Admin") && !User.IsInRole("Administrator") && !User.IsInRole("Editor"))
            {
                return Forbid(JwtBearerDefaults.AuthenticationScheme);
            }

            if (dto == null || string.IsNullOrWhiteSpace(dto.Title))
            {
                return BadRequest("Title is required.");
            }

            var project = new TestProject
            {
                Title = dto.Title,
                Description = dto.Description,
                JiraReference = dto.JiraReference
            };

            _context.TestProjects.Add(project);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetProject), new { id = project.Id }, new TestProjectDto
            {
                Id = project.Id,
                Title = project.Title,
                Description = project.Description,
                JiraReference = project.JiraReference
            });
        }

        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateProject(int id, [FromBody] UpdateTestProjectDto dto)
        {
            // Only Editor and Admin/Administrator can edit projects
            if (!User.IsInRole("Admin") && !User.IsInRole("Administrator") && !User.IsInRole("Editor"))
            {
                return Forbid(JwtBearerDefaults.AuthenticationScheme);
            }

            if (dto == null || string.IsNullOrWhiteSpace(dto.Title))
            {
                return BadRequest("Title is required.");
            }

            var project = await _context.TestProjects.FindAsync(id);
            if (project == null)
            {
                return NotFound($"Project with ID {id} not found.");
            }

            project.Title = dto.Title;
            project.Description = dto.Description;
            project.JiraReference = dto.JiraReference;

            await _context.SaveChangesAsync();

            return NoContent();
        }

        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteProject(int id)
        {
            // Only Admin/Administrator can delete projects
            if (!User.IsInRole("Admin") && !User.IsInRole("Administrator"))
            {
                return Forbid(JwtBearerDefaults.AuthenticationScheme);
            }

            var project = await _context.TestProjects.FindAsync(id);
            if (project == null)
            {
                return NotFound($"Project with ID {id} not found.");
            }

            _context.TestProjects.Remove(project);
            await _context.SaveChangesAsync();

            return NoContent();
        }
    }

    public class TestProjectDto
    {
        public int Id { get; set; }
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
        public string? JiraReference { get; set; }
    }

    public class CreateTestProjectDto
    {
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
        public string? JiraReference { get; set; }
    }

    public class UpdateTestProjectDto
    {
        public string Title { get; set; } = string.Empty;
        public string? Description { get; set; }
        public string? JiraReference { get; set; }
    }
}
