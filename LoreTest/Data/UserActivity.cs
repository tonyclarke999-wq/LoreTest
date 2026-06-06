using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

#nullable enable
namespace LoreTest.Data
{
    public class UserActivity
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int Id { get; set; }

        [StringLength(450)]
        public string? UserId { get; set; }

        [StringLength(256)]
        public string? Username { get; set; }

        [Required]
        [StringLength(50)]
        public string Action { get; set; } = ""; // "Login", "Logout", "PageView", "Search"

        [StringLength(2048)]
        public string? Details { get; set; } // Page URL or search term

        [StringLength(1024)]
        public string? UserAgent { get; set; } // Browser identity

        [Required]
        public DateTime Timestamp { get; set; } = DateTime.UtcNow;
    }
}
