$baseUrl = "http://localhost:5002"

function Test-Login($email, $password) {
    Write-Host "Testing login for $email..."
    $body = @{
        email = $email
        password = $password
    } | ConvertTo-Json
    
    try {
        $response = Invoke-RestMethod -Uri "$baseUrl/api/auth/login" -Method Post -Body $body -ContentType "application/json"
        Write-Host "SUCCESS! Token obtained." -ForegroundColor Green
        return $response
    } catch {
        Write-Host "FAILED! Status: $_" -ForegroundColor Red
        return $null
    }
}

$loginRes = Test-Login "tonyclarke999@gmail.com" "Password1-"
if ($null -eq $loginRes) {
    $loginRes = Test-Login "admin@example.com" "Password1-"
}

if ($null -ne $loginRes) {
    $token = $loginRes.token
    $headers = @{
        Authorization = "Bearer $token"
    }

    # Test 0: GET projects
    Write-Host "`nTest 0: Getting all projects..."
    try {
        $getRes = Invoke-WebRequest -Uri "$baseUrl/api/projects" -Method Get -Headers $headers
        Write-Host "Status: $($getRes.StatusCode) $($getRes.StatusDescription)" -ForegroundColor Green
        Write-Host "Body: $($getRes.Content)" -ForegroundColor Green
    } catch {
        Write-Host "Status: $_" -ForegroundColor Red
        if ($_.Exception.Response) {
            $stream = $_.Exception.Response.GetResponseStream()
            $reader = New-Object System.IO.StreamReader($stream)
            $errBody = $reader.ReadToEnd()
            Write-Host "Error Body: $errBody" -ForegroundColor Red
        }
    }
}
