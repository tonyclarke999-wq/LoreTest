using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace LoreTest.Data.Migrations
{
    /// <inheritdoc />
    public partial class AddPrivacyPolicyAndTermsToAppSettings : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "PrivacyPolicy",
                table: "AppSettings",
                type: "text",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "TermsAndConditions",
                table: "AppSettings",
                type: "text",
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "PrivacyPolicy",
                table: "AppSettings");

            migrationBuilder.DropColumn(
                name: "TermsAndConditions",
                table: "AppSettings");
        }
    }
}
