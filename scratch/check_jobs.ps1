foreach ($jobId in @("124857032508", "124885881621")) {
    Write-Output "=== ANNOTATIONS FOR JOB $jobId ==="
    $annotations = Invoke-RestMethod -Uri "https://api.github.com/repos/tonyclarke999-wq/LoreTest/check-runs/$jobId/annotations"
    foreach ($a in $annotations) {
        Write-Output "[$($a.annotation_level)] $($a.path):$($a.start_line) - $($a.title): $($a.message)"
    }
}
