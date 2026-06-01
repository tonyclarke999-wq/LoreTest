$resp = Invoke-RestMethod -Uri "https://api.github.com/repos/tonyclarke999-wq/LoreTest/actions/runs/26406839683/artifacts"
foreach ($art in $resp.artifacts) {
    Write-Output "Artifact: $($art.name) | Size: $($art.size_in_bytes) | Download URL: $($art.archive_download_url)"
}
