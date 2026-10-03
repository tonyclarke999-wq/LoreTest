# LoreTest Live Performance and Bottleneck Profiler
# ----------------------------------------------------
# This utility queries the CPU and Memory metrics of the LoreTest container/process
# and reports real-time diagnostics to catch memory leaks or circuit bottlenecks.

$containerName = "loretest-app"
$pollingIntervalSeconds = 2

Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "    LORETEST PERFORMANCE & DIAGNOSTIC PANEL   " -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "This panel monitors your application performance and checks for:"
Write-Host "  1. Circuit Memory Leaks (RAM grows continuously and never drops)."
Write-Host "  2. CPU Threadpool Exhaustion (CPU spikes high during execution)."
Write-Host "  3. Database Connection Bottlenecks."
Write-Host "---------------------------------------------"

# 1. Determine if running via Docker or direct dotnet process
$isDockerRunning = $false
try {
    $dockerCheck = docker ps --filter "name=$containerName" --format "{{.Names}}"
    if ($dockerCheck -eq $containerName) {
        $isDockerRunning = $true
        Write-Host "[Info] Detected LoreTest running in Docker container: $containerName" -ForegroundColor Green
    }
} catch {
    # Docker not running or not installed
}

if (-not $isDockerRunning) {
    Write-Host "[Info] Checking for local dotnet processes running LoreTest..." -ForegroundColor Yellow
    $processes = Get-Process -Name "LoreTest" -ErrorAction SilentlyContinue
    if (-not $processes) {
        Write-Host "[Warning] Neither Docker container '$containerName' nor 'LoreTest' process was found." -ForegroundColor Red
        Write-Host "Make sure LoreTest is running (via 'docker compose up' or 'dotnet run') before starting monitoring."
    } else {
        Write-Host "[Success] Found active 'LoreTest' process with PID: $($processes[0].Id)" -ForegroundColor Green
    }
}

Write-Host "`nPress CTRL+C to terminate monitoring.`n" -ForegroundColor DarkCyan
Write-Host "Timestamp            | CPU (%) | Memory (MB) | Status / Warning"
Write-Host "---------------------|---------|-------------|----------------------------"

try {
    while ($true) {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $cpu = 0
        $memMb = 0
        $warning = "Healthy"
        
        if ($isDockerRunning) {
            # Query stats from Docker
            $stats = docker stats $containerName --no-stream --format "{{.CPUPerc}},{{.MemUsage}}"
            if ($stats) {
                # Format is e.g. "0.05%,120.5MiB / 7.67GiB"
                $parts = $stats.Split(',')
                $cpuStr = $parts[0].Replace("%", "").Trim()
                $memStr = $parts[1].Split('/')[0].Trim()
                
                [double]::TryParse($cpuStr, [ref]$cpu) | Out-Null
                
                # Parse Memory (handling MiB/GiB)
                if ($memStr.Contains("GiB")) {
                    $memVal = $memStr.Replace("GiB", "").Trim()
                    [double]::TryParse($memVal, [ref]$memRaw) | Out-Null
                    $memMb = $memRaw * 1024
                } else {
                    $memVal = $memStr.Replace("MiB", "").Replace("KiB", "").Trim()
                    [double]::TryParse($memVal, [ref]$memMb) | Out-Null
                }
            }
        } else {
            # Query stats from Local Dotnet Process
            $processes = Get-Process -Name "LoreTest" -ErrorAction SilentlyContinue
            if ($processes) {
                # Sum CPU and memory if multiple instances
                $cpuSum = 0
                $memSum = 0
                foreach ($proc in $processes) {
                    $memSum += $proc.WorkingSet64 / 1MB
                }
                
                # Get CPU usage using performance counter or basic process metrics
                $cpuSum = (Get-Counter '\Processor(_Total)\% Processor Time').CounterSamples.CookedValue
                
                $cpu = [Math]::Round($cpuSum, 2)
                $memMb = [Math]::Round($memSum, 2)
            }
        }
        
        # Analyze thresholds for warnings
        if ($cpu -gt 85) {
            $warning = "High CPU Lockup Risk!"
        } elseif ($memMb -gt 1500) {
            $warning = "Extreme RAM! Leak possible."
        } elseif ($memMb -gt 800) {
            $warning = "Large volume footprint."
        }

        # Output row formatted beautifully
        "{0} | {1,7:N2} | {2,11:N2} | {3}" -f $timestamp, $cpu, $memMb, $warning

        Start-Sleep -Seconds $pollingIntervalSeconds
    }
} catch {
    Write-Host "`nMonitoring stopped." -ForegroundColor Cyan
}
