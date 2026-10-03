$path = "C:\Users\Linux\.gemini\antigravity\brain\027f3f1b-137a-4976-8fb4-114c27d0eb02\.system_generated\logs\transcript.jsonl"
$lines = Get-Content $path
foreach ($line in $lines) {
    if ($line.Trim() -ne "") {
        $obj = ConvertFrom-Json $line -ErrorAction SilentlyContinue
        if ($obj -and $obj.type -eq "USER_INPUT") {
            Write-Output "=== USER INPUT ==="
            Write-Output $obj.content
        }
    }
}
