$resp = Invoke-RestMethod -Uri "https://api.github.com/repos/tonyclarke999-wq/LoreTest/actions/runs/26406839683/jobs"
foreach ($job in $resp.jobs) {
    Write-Output "Job: $($job.name) | ID: $($job.id) | Status: $($job.status) | Conclusion: $($job.conclusion)"
}
