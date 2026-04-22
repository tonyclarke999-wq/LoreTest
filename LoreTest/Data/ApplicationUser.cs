using Microsoft.AspNetCore.Identity;

namespace LoreTest.Data
{
    // Add profile data for application users by adding properties to the ApplicationUser class
    public class ApplicationUser : IdentityUser
    {
        public string? Name { get; set; }
        public string? JobTitle { get; set; }
        public string? Role { get; set; }
        public DateOnly? StartDate { get; set; }
        public DateOnly? EndDate { get; set; }
    }

}
