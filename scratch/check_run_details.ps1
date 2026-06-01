$resp = Invoke-RestMethod -Uri "https://api.github.com/repos/tonyclarke999-wq/LoreTest/actions/runs/26408126800"
Write-Output "Run Number: $($resp.run_number)"
