$env:BASE_URL="http://localhost:5002"
dotnet test LoreTest.Playwright/LoreTest.Playwright.csproj --filter "FullyQualifiedName~ProjectTests" --logger "trx" --results-directory "LoreTest.Playwright/bin/Debug/net10.0/allure-results"
