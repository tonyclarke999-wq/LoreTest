# LoreTest Image Export Script
# This script pulls the latest images and saves them as .tar files for offline sharing.

$APP_IMAGE = "ghcr.io/tonyclarke999-wq/loretest:main"
$DB_IMAGE = "postgres:16-alpine"
$EXPORT_DIR = "./dist"

# Create export directory if it doesn't exist
if (!(Test-Path $EXPORT_DIR)) {
    New-Item -ItemType Directory -Path $EXPORT_DIR
}

Write-Host "--- Pulling latest images ---" -ForegroundColor Cyan
docker pull $APP_IMAGE
docker pull $DB_IMAGE

Write-Host "`n--- Saving App Image ---" -ForegroundColor Cyan
docker save -o "$EXPORT_DIR/loretest-app.tar" $APP_IMAGE

Write-Host "--- Saving DB Image ---" -ForegroundColor Cyan
docker save -o "$EXPORT_DIR/postgres.tar" $DB_IMAGE

# Copy docker-compose.yml as a template
Write-Host "`n--- Creating sharing package ---" -ForegroundColor Cyan
$composeContent = @"
services:
  db:
    image: $DB_IMAGE
    container_name: loretest-db
    environment:
      POSTGRES_DB: loretest
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: ChangeMe
    volumes:
      - loretest-db-data:/var/lib/postgresql/data

  app:
    image: $APP_IMAGE
    container_name: loretest-app
    environment:
      - ASPNETCORE_ENVIRONMENT=Production
      - ConnectionStrings__DefaultConnection=Host=db;Port=5432;Database=loretest;Username=postgres;Password=ChangeMe
    ports:
      - "5000:8080"
    depends_on:
      - db

volumes:
  loretest-db-data:
"@

$composeContent | Out-File -FilePath "$EXPORT_DIR/docker-compose.yml" -Encoding utf8

Write-Host "`nDone! Everything you need is in the '$EXPORT_DIR' folder." -ForegroundColor Green
Write-Host "You can now zip this folder and send it to others."
Write-Host "They will just need to run 'docker load -i <file>' for both images, then 'docker compose up -d'."
