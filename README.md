# LoreTest - Collaborative QA Testing Platform (v0.5)

LoreTest is a modern, web-based platform designed for managing test projects, suites, and cases with integrated bug tracking and Jira support.

## 🚀 Quick Start (Docker)

The easiest way to run LoreTest is using Docker Compose.

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running.

### Running the App
1. Create a file named `docker-compose.yml` with the following content:

```yaml
services:
  db:
    image: postgres:16-alpine
    container_name: loretest-db
    environment:
      POSTGRES_DB: loretest
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: YourSecurePassword
    volumes:
      - loretest-db-data:/var/lib/postgresql/data

  app:
    image: ghcr.io/tonyclarke999-wq/loretest:main
    container_name: loretest-app
    environment:
      - ASPNETCORE_ENVIRONMENT=Production
      - ConnectionStrings__DefaultConnection=Host=db;Port=5432;Database=loretest;Username=postgres;Password=YourSecurePassword
    ports:
      - "5000:8080"
    depends_on:
      - db

volumes:
  loretest-db-data:
```

2. Run the following command in your terminal:
   ```bash
   docker compose up -d
   ```
3. Open your browser and navigate to `http://localhost:5000`.

---

## 📦 Sharing and Distribution

### Method 1: Pulling from Registry (Recommended)
Others can simply pull the latest image from the GitHub Container Registry:
```bash
docker pull ghcr.io/tonyclarke999-wq/loretest:main
```

### Method 2: Offline Distribution (Image Files)
If you need to share the application without internet access:
1. **Export Images**:
   ```powershell
   docker save -o loretest-app.tar ghcr.io/tonyclarke999-wq/loretest:main
   docker save -o postgres.tar postgres:16-alpine
   ```
2. **Import Images** (on the other machine):
   ```powershell
   docker load -i loretest-app.tar
   docker load -i postgres.tar
   ```

### Method 3: Including Demo Data
The images contain the app but not your data. To share a "ready-to-go" demo with your projects and test cases:
1. **Export Data**:
   ```bash
   docker exec -t loretest-db pg_dumpall -c -U postgres > dump.sql
   ```
2. **Import Data**:
   Place the `dump.sql` in the same directory as your `docker-compose.yml` and add this line to the `db` service volumes:
   ```yaml
   volumes:
     - ./dump.sql:/docker-entrypoint-initdb.d/dump.sql
   ```

---

## 🔒 Security & Secret Management

LoreTest is built to be secure by default while remaining trivially simple for strangers to spin up as a demo.

### 1. Out-of-the-Box Demo Mode (Insecure Defaults)
For local development and quick-start demonstrations, LoreTest includes functional defaults in `appsettings.json` (such as local database connection strings with standard passwords and a local JWT secret key). Anyone cloning this repository can run `dotnet run` or `docker compose up` and it will work instantly with zero configuration.

### 2. Production Security (Strict Overrides)
> [!WARNING]
> **Never use the default database passwords or JWT secret keys committed in the repository in public-facing or production environments.**

For real-world and production deployments, the insecure defaults in `appsettings.json` **MUST** be overridden by injecting secure credentials via **Environment Variables**. ASP.NET Core automatically parses and prioritizes these environment overrides at startup:

| Configuration Path | Environment Variable Override | Purpose |
| :--- | :--- | :--- |
| `ConnectionStrings:DefaultConnection` | `ConnectionStrings__DefaultConnection` | Secure connection string to production PostgreSQL |
| `Jwt:Secret` | `Jwt__Secret` | Cryptographically secure 256-bit JWT signing key |

#### Local Development Secrets (Optional)
If you wish to secure your secrets locally during active development without committing them to the workspace, you can initialize and set them using the .NET `user-secrets` CLI:
```powershell
dotnet user-secrets init
dotnet user-secrets set "ConnectionStrings:DefaultConnection" "Host=localhost;Password=YourSecurePassword;..."
dotnet user-secrets set "Jwt:Secret" "YourProductionSecureJWTKeyAtLeast32Chars!"
```

---

## 🛠 Features
- **Dashboard**: Track active test runs and weekly progress at a glance.
- **Jira Integration**: Automatically create bugs and link them to Jira tickets.
- **Audit Logging**: Full traceability for all data changes.
- **Multi-language Support**: Dynamic localization for global teams.

## 📄 License
Licensed under the [MIT License](LICENSE.txt).
