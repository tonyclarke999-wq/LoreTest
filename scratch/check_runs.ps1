$resp = Invoke-RestMethod -Uri "https://api.github.com/repos/tonyclarke999-wq/LoreTest/actions/runs?per_page=10"
foreach ($run in $resp.workflow_runs) {
    Write-Output "ID: $($run.id) | Branch: $($run.head_branch) | Status: $($run.status) | Conclusion: $($run.conclusion) | Created: $($run.created_at) | Event: $($run.event) | Message: $($run.head_commit.message)"
}
