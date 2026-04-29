using System;
using Microsoft.EntityFrameworkCore.Migrations;
using Npgsql.EntityFrameworkCore.PostgreSQL.Metadata;

#nullable disable

namespace LoreTest.Data.Migrations
{
    /// <inheritdoc />
    public partial class AddTestRuns : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AlterColumn<string>(
                name: "Description",
                table: "TestSteps",
                type: "text",
                nullable: true,
                oldClrType: typeof(string),
                oldType: "text");

            migrationBuilder.CreateTable(
                name: "TestRuns",
                columns: table => new
                {
                    Id = table.Column<int>(type: "integer", nullable: false)
                        .Annotation("Npgsql:ValueGenerationStrategy", NpgsqlValueGenerationStrategy.IdentityByDefaultColumn),
                    RunNumber = table.Column<int>(type: "integer", nullable: false),
                    StartTime = table.Column<DateTime>(type: "timestamp with time zone", nullable: false),
                    EndTime = table.Column<DateTime>(type: "timestamp with time zone", nullable: true),
                    TestProjectId = table.Column<int>(type: "integer", nullable: false),
                    TestSuiteId = table.Column<int>(type: "integer", nullable: false),
                    StartedByUserId = table.Column<string>(type: "text", nullable: false),
                    Status = table.Column<string>(type: "character varying(50)", maxLength: 50, nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TestRuns", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TestRuns_AspNetUsers_StartedByUserId",
                        column: x => x.StartedByUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_TestRuns_TestProjects_TestProjectId",
                        column: x => x.TestProjectId,
                        principalTable: "TestProjects",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_TestRuns_TestSuites_TestSuiteId",
                        column: x => x.TestSuiteId,
                        principalTable: "TestSuites",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "TestRunCaseResults",
                columns: table => new
                {
                    Id = table.Column<int>(type: "integer", nullable: false)
                        .Annotation("Npgsql:ValueGenerationStrategy", NpgsqlValueGenerationStrategy.IdentityByDefaultColumn),
                    TestRunId = table.Column<int>(type: "integer", nullable: false),
                    TestCaseId = table.Column<int>(type: "integer", nullable: false),
                    Status = table.Column<string>(type: "character varying(50)", maxLength: 50, nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TestRunCaseResults", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TestRunCaseResults_TestCases_TestCaseId",
                        column: x => x.TestCaseId,
                        principalTable: "TestCases",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_TestRunCaseResults_TestRuns_TestRunId",
                        column: x => x.TestRunId,
                        principalTable: "TestRuns",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "TestRunStepResults",
                columns: table => new
                {
                    Id = table.Column<int>(type: "integer", nullable: false)
                        .Annotation("Npgsql:ValueGenerationStrategy", NpgsqlValueGenerationStrategy.IdentityByDefaultColumn),
                    TestRunCaseResultId = table.Column<int>(type: "integer", nullable: false),
                    TestStepId = table.Column<int>(type: "integer", nullable: false),
                    Status = table.Column<string>(type: "character varying(50)", maxLength: 50, nullable: false),
                    ActualResult = table.Column<string>(type: "text", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TestRunStepResults", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TestRunStepResults_TestRunCaseResults_TestRunCaseResultId",
                        column: x => x.TestRunCaseResultId,
                        principalTable: "TestRunCaseResults",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_TestRunStepResults_TestSteps_TestStepId",
                        column: x => x.TestStepId,
                        principalTable: "TestSteps",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_TestRunCaseResults_TestCaseId",
                table: "TestRunCaseResults",
                column: "TestCaseId");

            migrationBuilder.CreateIndex(
                name: "IX_TestRunCaseResults_TestRunId",
                table: "TestRunCaseResults",
                column: "TestRunId");

            migrationBuilder.CreateIndex(
                name: "IX_TestRuns_StartedByUserId",
                table: "TestRuns",
                column: "StartedByUserId");

            migrationBuilder.CreateIndex(
                name: "IX_TestRuns_TestProjectId",
                table: "TestRuns",
                column: "TestProjectId");

            migrationBuilder.CreateIndex(
                name: "IX_TestRuns_TestSuiteId",
                table: "TestRuns",
                column: "TestSuiteId");

            migrationBuilder.CreateIndex(
                name: "IX_TestRunStepResults_TestRunCaseResultId",
                table: "TestRunStepResults",
                column: "TestRunCaseResultId");

            migrationBuilder.CreateIndex(
                name: "IX_TestRunStepResults_TestStepId",
                table: "TestRunStepResults",
                column: "TestStepId");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "TestRunStepResults");

            migrationBuilder.DropTable(
                name: "TestRunCaseResults");

            migrationBuilder.DropTable(
                name: "TestRuns");

            migrationBuilder.AlterColumn<string>(
                name: "Description",
                table: "TestSteps",
                type: "text",
                nullable: false,
                defaultValue: "",
                oldClrType: typeof(string),
                oldType: "text",
                oldNullable: true);
        }
    }
}
