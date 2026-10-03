# Changelog

All notable changes to the **LoreTest** project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [0.7.2] - 2026-10-02

### Added
- **Configurable Legal Documents**: Added dedicated `/privacy` (Privacy Policy) and `/terms` (Terms and Conditions) pages with customizable markdown content and automatic HTML rendering.
- **Admin Legal Settings**: Administrative interface (`/admin/legal`) to configure and save custom privacy policies and terms into PostgreSQL with EF Core audit logging.
- **Default Self-Hosted Legal Templates**: Included open-source/self-hosted default legal templates with highlighted organizational placeholders (`<mark>[Organization / Company Name]</mark>`).
- **Footer and Login Integration**: Compacted login screen and footer to provide direct links to the Privacy Policy and Terms and Conditions above the fold.
- **Unit & Integration Tests**: Added test coverage in `LegalDocumentTests.cs` for default template generation, markdown rendering, and database persistence.

### Changed
- **CI/CD Pipeline Upgrade**:
  - Upgraded GitHub Actions workflow (`deploy.yml`) to Node.js 24 runtime actions (`actions/checkout@v7`, `docker/login-action@v4`, `docker/metadata-action@v6`, `docker/build-push-action@v7`, `actions/upload-artifact@v7`, `actions/download-artifact@v8`).
  - Resolved all Node.js 20 runner deprecation warnings across test, build-and-push, deploy, and report jobs.

### Fixed
- Fixed null reference and unused field compiler warnings across Blazor components (`Settings.razor`, `Create.razor`, `NavMenu.razor`).
- Updated MSTest assertions to use modern `Assert.Contains` semantics.

---

## [0.7.1] - 2026-10-02

### Added
- Observability stack setup with Prometheus, Grafana, and `docker-stats-exporter`.
- Jira API token configuration guidance and helper links in administrative settings.

### Fixed
- WCAG 2.2 AA accessibility violations including button outline contrast, table text visibility, search icon contrast, and ARIA labels.
- InteractiveServer render mode for `UserActivityTracker` in `MainLayout`.

---

## [0.7.0] - 2026-06-06

### Added
- REST API reference documentation and client usage guides.
- Automated Allure test reports deployment to GitHub Pages.
- Playwright trace viewer support for automated browser runs.

---

## [0.6.0] - 2026-05-24

### Added
- Full Playwright E2E and Reqnroll BDD specification test suites.
- Multi-environment containerized deployments for test and preproduction environments.

---

## [0.5.0] - 2026-05-15

### Added
- Audit logging system for entity tracking and changes.
- System analytics and administrative reporting dashboard.

---

## [0.1.0] - [0.4.0]

### Added
- Core test management features: projects, test suites, test cases, and test runs.
- PostgreSQL database integration with EF Core code-first migrations.
- ASP.NET Core Identity authentication with role-based authorization (Administrator, Editor, Viewer).
- Dynamic localization and multi-language support.
