# LoreTest Workspace Rules

## Jira Ticket Tracking (`KAN` Board)

Whenever you perform development, bugfix, or architecture work on **LoreTest**, create or update the corresponding Jira ticket(s) on `tonyclarke999.atlassian.net` (project `KAN`, board `1`: `https://tonyclarke999.atlassian.net/jira/software/projects/KAN/boards/1`) to reflect the work:

1. **Tooling**: Use the **Atlassian Teamwork Graph CLI** (`twg`, or `$env:LOCALAPPDATA\Programs\twg\bin\twg.exe --mode agent` on Windows) targeting `--site tonyclarke999.atlassian.net`.
2. **Check Existing Issues First**: Query `project = KAN ORDER BY updated DESC` to see if an existing ticket already covers the request before creating a new one.
3. **Update & Transition**:
   - When starting or completing work, update the ticket's description with a concise summary of changes (files, migrations, endpoints, tests) and add a verification comment (e.g., build/test results).
   - Discover available transitions (`twg jira workitem transition --id <KEY> --site tonyclarke999.atlassian.net -o json`) and transition the ticket appropriately (e.g., `In Progress` while working, `In Review` or `Done` when verified).
4. **Report Back**: Always include the resulting Jira ticket key and URL (`https://tonyclarke999.atlassian.net/browse/<KEY>`) in your response.
