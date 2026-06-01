$body = @{
    email = "tonyclarke999@gmail.com"
    password = "Password1-"
} | ConvertTo-Json

$login = Invoke-RestMethod -Uri "http://localhost:5002/api/auth/login" -Method Post -Body $body -ContentType "application/json"
$token = $login.token
$headers = @{
    Authorization = "Bearer $token"
}

Write-Host "Sending GET request to projects endpoint..."
$projects = Invoke-RestMethod -Uri "http://localhost:5002/api/projects" -Method Get -Headers $headers
$projects | ConvertTo-Json | Write-Host
