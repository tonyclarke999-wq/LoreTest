#nullable enable
using System;
using System.Text.RegularExpressions;
using Markdig;

namespace LoreTest.Utilities
{
    public static class LegalDefaults
    {
        private static readonly MarkdownPipeline MarkdownPipeline = new MarkdownPipelineBuilder()
            .UseAdvancedExtensions()
            .Build();

        public static string RenderMarkdownToHtml(string? markdown)
        {
            if (string.IsNullOrWhiteSpace(markdown))
            {
                return string.Empty;
            }

            // Convert markdown to HTML
            var html = Markdown.ToHtml(markdown, MarkdownPipeline);

            // Highlight any unformatted bracketed placeholders like [Organization Name] or [Contact Email]
            // if they aren't already wrapped in <mark> tags
            html = Regex.Replace(
                html,
                @"(?<!<mark[^>]*>)\[([A-Za-z0-9\s/_\-–—@\.,]+)\](?!</mark>)",
                @"<mark class=""placeholder-highlight"">[$1]</mark>",
                RegexOptions.IgnoreCase
            );

            return html;
        }

        public static string GetDefaultPrivacyPolicy()
        {
            return @"# Privacy Policy for LoreTest (Self-Hosted Instance)

**Effective Date:** <mark>[Effective Date, e.g. 1st January 2026]</mark>  
**Last Updated:** <mark>[Last Updated Date]</mark>  
**Operating Organization:** <mark>[Organization / Company Name]</mark>  

---

### 1. Introduction & Open Source Notice

This installation of **LoreTest** is an independently hosted and operated instance of the LoreTest collaborative QA testing platform.

LoreTest is open source software freely available on GitHub under the MIT License. **The upstream creators and maintainers of the open-source LoreTest project do NOT operate, host, or access this instance, nor do they collect, store, or receive any data submitted to this deployment.**

This Privacy Policy explains how **<mark>[Organization / Company Name]</mark>** (""**we**"", ""**us**"", or ""**our**"") collects, handles, and protects information submitted by users of this internally-hosted platform.

---

### 2. Data Controller

**<mark>[Organization / Company Name]</mark>** is the sole Data Controller for all personal data and quality assurance records processed within this self-hosted LoreTest instance.

For inquiries or to exercise privacy rights, contact:
- **Designated Contact / Team:** <mark>[IT Department / QA Lead / Data Protection Officer]</mark>
- **Contact Email:** <mark>[admin@organization.com / privacy@organization.com]</mark>
- **Internal Helpdesk / Address:** <mark>[Internal Helpdesk Link or Physical Address]</mark>

---

### 3. Information Collected in This Instance

We collect and store information required to operate testing and QA workflows:

#### A. User Account Information
- **Account Profiles:** Full name, business email address, username, job title, and assigned platform role (e.g., Administrator, Tester, Reviewer).
- **Authentication Data:** Cryptographically hashed passwords, role-based claims, session authentication cookies, and optional REST API JWT authentication tokens.
- **Language Preference:** Stored interface locale preference (e.g., English, French, German, Spanish).

#### B. Quality Assurance & Test Management Data
- **Testing Assets:** Projects, test suites, test cases, execution steps, expected outcomes, and recorded run results.
- **Bug & Defect Records:** Defect titles, descriptions, severity, reproduction steps, comments, and attachments (such as screenshots, error traces, and log snippets).
- **Audit Trails:** Automatic change records (`AuditLog`) capturing which user created, modified, or deleted testing entities for compliance, traceability, and accountability.

#### C. System Telemetry & Activity Logs
Depending on the telemetry level configured by the system administrator (`None`, `LoginOnly`, or `Full`):
- **Authentication Logs:** Timestamps of successful logins, logouts, and failed access attempts.
- **Activity Tracking:** Page views, internal search terms, and timestamps linked to user accounts.
- **Diagnostics:** User-Agent (browser/OS identity) and IP address.

#### D. Third-Party Integrations (Optional)
- **Atlassian Jira:** If configured by the administrator, Jira API tokens, user email, and base URL are stored locally in the database to synchronize bugs with external Jira boards.
- **Translation Services:** If configured, API keys for external services (Google Cloud Translation, Azure Translator) to facilitate multilingual test case translation.

---

### 4. How Information Is Used

Data held in this instance is processed exclusively for authorized internal business purposes:
1. Managing software test cases, test suites, and test execution runs.
2. Logging, prioritizing, and resolving software defects.
3. Providing auditability and traceability across development and testing cycles.
4. Securing access through authentication and role-based permissions.
5. Diagnosing technical issues and preventing unauthorized access.

---

### 5. Data Storage, Security & Retention

- **Hosting Location:** All database records and attachments reside on infrastructure provisioned and managed by **<mark>[Organization / Company Name]</mark>**.
- **Security Controls:**
  - Password hashing via ASP.NET Core Identity.
  - JSON Web Tokens (JWT) for secure REST API endpoints.
  - Rate limiting and automated account lockout after repeated failed login attempts.
  - Role-based authorization policies.
- **Retention Period:** Testing records, defect histories, and user audit logs are retained in accordance with **<mark>[Organization's Internal Data Retention Policy, e.g. duration of project lifecycle plus 3 years]</mark>**.

---

### 6. User Rights & Contact

Authorized users may request access to, correction of, or deactivation of their account profile in accordance with company policy and applicable data protection legislation (such as GDPR or local laws).

Please direct all questions, access requests, or issues regarding this instance to:  
**<mark>[IT Helpdesk / System Administrator Email]</mark>**
";
        }

        public static string GetDefaultTermsAndConditions()
        {
            return @"# Terms and Conditions for LoreTest (Self-Hosted Instance)

**Effective Date:** <mark>[Effective Date, e.g. 1st January 2026]</mark>  
**Last Updated:** <mark>[Last Updated Date]</mark>  
**Operating Organization:** <mark>[Organization / Company Name]</mark>  

---

### 1. Acceptance & Open Source Foundation

1. **Open Source Basis:** LoreTest is an open source software project licensed under the [MIT License](https://opensource.org/licenses/MIT) and freely available from GitHub. The software is provided by the upstream copyright holders ""AS IS"", without warranty of any kind.
2. **Internal Deployment:** This installation of LoreTest is hosted and administered by **<mark>[Organization / Company Name]</mark>** (""**Organization**"", ""**we**"", ""**us**"") for authorized business, software testing, and quality assurance purposes.
3. **Acceptance:** By accessing or logging into this instance, you agree to comply with these Terms and Conditions and our internal organizational policies.

---

### 2. Authorized Access & Account Security

1. **Eligibility:** Access to this system is restricted to authorized employees, contractors, and partners of **<mark>[Organization / Company Name]</mark>**.
2. **Account Responsibility:** Users must maintain the confidentiality of their login credentials. Sharing accounts or API authentication tokens is strictly prohibited.
3. **Reporting Security Incidents:** If you suspect unauthorized access to your account or discover a security vulnerability in this instance, notify **<mark>[Internal Security Team / Administrator Email]</mark>** immediately.

---

### 3. Acceptable Use Policy

When using this platform, you agree **NOT** to:
1. Store or upload sensitive unredacted personal information (such as live payment card data, citizen identification numbers, or confidential customer records) into test cases or defect logs unless explicitly authorized by **<mark>[Organization / Company Name]</mark>**'s data governance policy.
2. Upload, link, or transmit files containing malware, viruses, ransomware, or malicious scripts.
3. Execute automated test runs against third-party or production systems without explicit prior written authorization.
4. Attempt to bypass role-based access restrictions, rate limits, or audit logging mechanisms.
5. Use this instance for any unlawful purpose or in violation of **<mark>[Organization's Employee Code of Conduct / Acceptable Use Policy]</mark>**.

---

### 4. Intellectual Property & Work Product

1. **Testing Assets & Data:** All test cases, test suites, test run results, bug descriptions, and QA documentation created within this instance constitute proprietary work product belonging to **<mark>[Organization / Company Name]</mark>**.
2. **Software License:** LoreTest application code is licensed under the MIT License. Any custom extensions, internal scripts, or proprietary integrations created by the Organization remain subject to internal policies.

---

### 5. Third-Party Integrations

1. **Jira Integration:** Synchronization with Atlassian Jira is subject to your organization's enterprise licensing agreement with Atlassian.
2. **External Translation APIs:** Any translation service configured within the system must use organization-approved API credentials and adhere to organizational data-handling policies.

---

### 6. Disclaimer of Warranties & Limitation of Liability

1. **""AS IS"" Disclaimer:** IN ACCORDANCE WITH THE OPEN-SOURCE MIT LICENSE, THIS PLATFORM IS PROVIDED ""AS IS"", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT.
2. **No Guarantee of Defect Detection:** NEITHER THE UPSTREAM LORETEST DEVELOPERS NOR **<mark>[ORGANIZATION / COMPANY NAME]</mark>** GUARANTEE THAT USE OF THIS PLATFORM WILL IDENTIFY EVERY DEFECT OR FLAW IN SOFTWARE UNDER TEST, OR THAT THE SYSTEM WILL OPERATE ERROR-FREE OR WITHOUT INTERRUPTION.
3. **Limitation of Liability:** TO THE MAXIMUM EXTENT PERMITTED BY LAW, IN NO EVENT SHALL THE OPEN SOURCE MAINTAINERS OR **<mark>[ORGANIZATION / COMPANY NAME]</mark>** BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, OR CONSEQUENTIAL DAMAGES ARISING FROM THE USE OF THIS SOFTWARE.

---

### 7. Administration & Termination of Access

**<mark>[Organization / Company Name]</mark>** reserves the right to modify, suspend, or terminate user accounts, revoke API access, or decommission this instance at any time in accordance with operational requirements or for violation of these Terms.

---

### 8. Questions & Support

For administrative support, account requests, or policy questions, please contact:  
- **System Administrator:** <mark>[Administrator Name or Department]</mark>  
- **Email:** <mark>[admin@organization.com / it-helpdesk@organization.com]</mark>  
- **Governing Jurisdiction / Policy:** <mark>[Internal Company Policy / Country or State Jurisdiction]</mark>
";
        }
    }
}
