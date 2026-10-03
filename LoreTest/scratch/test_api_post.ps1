$body = @{
    email = "tonyclarke999@gmail.com"
    password = "Password1-"
} | ConvertTo-Json

$login = Invoke-RestMethod -Uri "http://localhost:5002/api/auth/login" -Method Post -Body $body -ContentType "application/json"
$token = $login.token
$headers = @{
    Authorization = "Bearer $token"
}

# Test 3: Literal JSON Array
Write-Host "Test 3: Creating project with a literal JSON array..."
$literalArrayBody = '[ { "title": "Test Literal Array Project", "description": "Created via literal array", "jiraReference": "ARRAY-LIT" } ]'

try {
    $res3 = Invoke-RestMethod -Uri "http://localhost:5002/api/projects" -Method Post -Headers $headers -Body $literalArrayBody -ContentType "application/json"
    Write-Host "SUCCESS! Response:" -ForegroundColor Green
    $res3 | ConvertTo-Json | Write-Host
} catch {
    Write-Host "FAILED! Status: $_" -ForegroundColor Red
    if ($_.Exception.Response) {
        $stream = $_.Exception.Response.GetResponseStream()
        $reader = New-Object System.IO.StreamReader($stream)
        $errBody = $reader.ReadToEnd()
        Write-Host "Error Body: $errBody" -ForegroundColor Red
    }
}
