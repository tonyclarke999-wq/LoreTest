$allureUrl = "https://github.com/allure-framework/allure2/releases/download/2.29.0/allure-2.29.0.zip"
$zipPath = Join-Path $pwd "allure.zip"
$destDir = Join-Path $pwd "allure-cli"
if (!(Test-Path $destDir)) {
    New-Item -ItemType Directory -Force -Path $destDir
    Write-Host "Downloading Allure CLI from $allureUrl..."
    Invoke-WebRequest -Uri $allureUrl -OutFile $zipPath
    Write-Host "Extracting archive to $destDir..."
    Expand-Archive -Path $zipPath -DestinationPath $destDir -Force
    Remove-Item $zipPath -Force
    Write-Host "Allure CLI successfully downloaded and extracted."
} else {
    Write-Host "Allure CLI already exists at $destDir."
}
