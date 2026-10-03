using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace LoreTest.Data.Migrations
{
    /// <inheritdoc />
    public partial class RelinkTranslationsToFieldsFinal : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "LocalizationFields",
                columns: table => new
                {
                    Id = table.Column<int>(type: "integer", nullable: false),
                    Key = table.Column<string>(type: "character varying(255)", maxLength: 255, nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_LocalizationFields", x => x.Id);
                });

            // Copy data from existing translations to fields
            migrationBuilder.Sql("INSERT INTO \"LocalizationFields\" (\"Id\", \"Key\") SELECT \"FieldId\", MIN(\"FieldKey\") FROM \"DynamicTranslations\" GROUP BY \"FieldId\"");

            migrationBuilder.DropColumn(
                name: "FieldKey",
                table: "DynamicTranslations");

            migrationBuilder.CreateIndex(
                name: "IX_DynamicTranslations_FieldId",
                table: "DynamicTranslations",
                column: "FieldId");

            migrationBuilder.CreateIndex(
                name: "IX_LocalizationFields_Key",
                table: "LocalizationFields",
                column: "Key",
                unique: true);

            migrationBuilder.AddForeignKey(
                name: "FK_DynamicTranslations_LocalizationFields_FieldId",
                table: "DynamicTranslations",
                column: "FieldId",
                principalTable: "LocalizationFields",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_DynamicTranslations_LocalizationFields_FieldId",
                table: "DynamicTranslations");

            migrationBuilder.DropTable(
                name: "LocalizationFields");

            migrationBuilder.DropIndex(
                name: "IX_DynamicTranslations_FieldId",
                table: "DynamicTranslations");

            migrationBuilder.AddColumn<string>(
                name: "FieldKey",
                table: "DynamicTranslations",
                type: "character varying(255)",
                maxLength: 255,
                nullable: false,
                defaultValue: "");
        }
    }
}
