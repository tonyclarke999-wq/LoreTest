using Microsoft.EntityFrameworkCore.Migrations;
using Npgsql.EntityFrameworkCore.PostgreSQL.Metadata;

#nullable disable

namespace LoreTest.Data.Migrations
{
    /// <inheritdoc />
    public partial class AddTestSuitesAndCasesAndSteps : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "TestSuites",
                columns: table => new
                {
                    Id = table.Column<int>(type: "integer", nullable: false)
                        .Annotation("Npgsql:ValueGenerationStrategy", NpgsqlValueGenerationStrategy.IdentityByDefaultColumn),
                    Title = table.Column<string>(type: "character varying(200)", maxLength: 200, nullable: false),
                    Description = table.Column<string>(type: "text", nullable: true),
                    TestProjectId = table.Column<int>(type: "integer", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TestSuites", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TestSuites_TestProjects_TestProjectId",
                        column: x => x.TestProjectId,
                        principalTable: "TestProjects",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "TestCases",
                columns: table => new
                {
                    Id = table.Column<int>(type: "integer", nullable: false)
                        .Annotation("Npgsql:ValueGenerationStrategy", NpgsqlValueGenerationStrategy.IdentityByDefaultColumn),
                    Title = table.Column<string>(type: "character varying(200)", maxLength: 200, nullable: false),
                    Description = table.Column<string>(type: "text", nullable: true),
                    PreConditions = table.Column<string>(type: "text", nullable: true),
                    Dependencies = table.Column<string>(type: "text", nullable: true),
                    TestData = table.Column<string>(type: "text", nullable: true),
                    PostCondition = table.Column<string>(type: "text", nullable: true),
                    Status = table.Column<string>(type: "character varying(50)", maxLength: 50, nullable: true),
                    Priority = table.Column<string>(type: "character varying(50)", maxLength: 50, nullable: true),
                    DefectId = table.Column<string>(type: "character varying(100)", maxLength: 100, nullable: true),
                    Notes = table.Column<string>(type: "text", nullable: true),
                    TestSuiteId = table.Column<int>(type: "integer", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TestCases", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TestCases_TestSuites_TestSuiteId",
                        column: x => x.TestSuiteId,
                        principalTable: "TestSuites",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "TestSteps",
                columns: table => new
                {
                    Id = table.Column<int>(type: "integer", nullable: false)
                        .Annotation("Npgsql:ValueGenerationStrategy", NpgsqlValueGenerationStrategy.IdentityByDefaultColumn),
                    StepNumber = table.Column<int>(type: "integer", nullable: false),
                    Description = table.Column<string>(type: "text", nullable: false),
                    ExpectedResult = table.Column<string>(type: "text", nullable: true),
                    ActualResult = table.Column<string>(type: "text", nullable: true),
                    TestCaseId = table.Column<int>(type: "integer", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TestSteps", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TestSteps_TestCases_TestCaseId",
                        column: x => x.TestCaseId,
                        principalTable: "TestCases",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_TestCases_TestSuiteId",
                table: "TestCases",
                column: "TestSuiteId");

            migrationBuilder.CreateIndex(
                name: "IX_TestSteps_TestCaseId",
                table: "TestSteps",
                column: "TestCaseId");

            migrationBuilder.CreateIndex(
                name: "IX_TestSuites_TestProjectId",
                table: "TestSuites",
                column: "TestProjectId");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "TestSteps");

            migrationBuilder.DropTable(
                name: "TestCases");

            migrationBuilder.DropTable(
                name: "TestSuites");
        }
    }
}
