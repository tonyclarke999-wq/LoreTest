# LoreTest - Collaborative QA Testing Platform (v0.4)

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

## 🛠 Features
- **Dashboard**: Track active test runs and weekly progress at a glance.
- **Jira Integration**: Automatically create bugs and link them to Jira tickets.
- **Audit Logging**: Full traceability for all data changes.
- **Multi-language Support**: Dynamic localization for global teams.

## 📄 License
Licensed under the [MIT License](LICENSE.txt).
