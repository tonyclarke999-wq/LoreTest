try {
    $resp = Invoke-WebRequest -Uri "https://api.github.com/repos/tonyclarke999-wq/LoreTest/actions/jobs/77733066555/logs" -MaximumRedirection 5
    Write-Output "Logs successfully downloaded! Size: $($resp.Content.Length)"
    # Output the last 500 characters
    Write-Output $resp.Content.Substring($resp.Content.Length - [Math]::Min($resp.Content.Length, 2000))
} catch {
    Write-Output "Failed to get logs: $_"
}
