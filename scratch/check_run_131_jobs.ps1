$resp = Invoke-RestMethod -Uri "https://api.github.com/repos/tonyclarke999-wq/LoreTest/actions/runs/26397079281/jobs"
foreach ($job in $resp.jobs) {
    Write-Output "Job: $($job.name) | Status: $($job.status) | Conclusion: $($job.conclusion)"
    foreach ($step in $job.steps) {
        Write-Output "  Step: $($step.name) | Status: $($step.status) | Conclusion: $($step.conclusion)"
    }
}
