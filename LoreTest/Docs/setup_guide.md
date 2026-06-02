# LoreTest Setup & Installation Guide

This guide provides step-by-step instructions for downloading, installing, and running your own copy of the **LoreTest Collaborative QA Platform** locally.

---

## Prerequisites

Before starting, ensure you have the following installed on your machine:
* **.NET 10 SDK** (Required for compiling and running from source)
* **Docker Desktop** (Recommended for spinning up the PostgreSQL database in one click)
* **PostgreSQL** (Optional, if you prefer running a native database service instead of Docker)

---

## 📦 Option 1: Quick Run with Docker Compose (Recommended)

This is the fastest way to get a running copy of LoreTest without compiling the source code.

### Step 1: Prepare the directory
Create a new directory on your machine and navigate into it:
```bash
mkdir loretest-demo
cd loretest-demo
```

### Step 2: Create a `docker-compose.yml` file
Create a file named `docker-compose.yml` with the following content:
```yaml
services:
  db:
    image: postgres:16-alpine
    container_name: loretest-db
    environment:
      POSTGRES_DB: loretest
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: HomePlate4  # Default local password
    ports:
      - "5432:5432"
    volumes:
      - loretest-db-data:/var/lib/postgresql/data

  app:
    image: ghcr.io/tonyclarke999-wq/loretest:main
    container_name: loretest-app
    environment:
      - ASPNETCORE_ENVIRONMENT=Production
      - ConnectionStrings__DefaultConnection=Host=db;Port=5432;Database=loretest;Username=postgres;Password=HomePlate4
    ports:
      - "5000:8080"
    depends_on:
      - db

volumes:
  loretest-db-data:
```

### Step 3: Boot the services
Run the following command in your terminal:
```bash
docker compose up -d
```
This will automatically download the database and the precompiled LoreTest application image, link them, run the database migrations, and seed the default accounts.

### Step 4: Access the app
Open your browser and navigate to:
👉 **`http://localhost:5000`**

---

## 🛠️ Option 2: Running from Source (Developer Mode)

Use this method if you want to inspect, edit, or compile the codebase.

### Step 1: Clone or Download the repository
Clone the repository from GitHub:
```bash
git clone https://github.com/tonyclarke999-wq/LoreTest.git
cd LoreTest
```
*(Alternatively, you can extract the release ZIP archive `loretest-v0.6.zip` to a folder of your choice.)*

### Step 2: Spin up a Local Database
LoreTest requires a PostgreSQL instance. You can easily start one using Docker:
```bash
docker run --name loretest-db -e POSTGRES_DB=loretest -e POSTGRES_USER=postgres -e POSTGRES_PASSWORD=HomePlate4 -p 5432:5432 -d postgres:16-alpine
```

### Step 3: Configure Settings (Optional)
Check `LoreTest/appsettings.json` to ensure the connection string matches your database settings. The default out-of-the-box settings are pre-configured to look for a PostgreSQL database on `localhost:5432` with password `HomePlate4`.

### Step 4: Build and Run the App
Navigate to the C# project root and run the application:
```bash
cd LoreTest
dotnet run
```
On startup, the bootstrapper automatically checks your database connection, applies any pending Entity Framework database migrations, and seeds initial data.

### Step 5: Access the App
Open your browser and navigate to:
👉 **`http://localhost:5001`** *(or the SSL port printed in your console)*

---

## 🔑 Default Login Credentials

Once the application has started and seeded the database, you can log in immediately using the following default seeded credentials:

### 1. Administrator Account (Full Control)
* **Email**: `tonyclarke999@gmail.com` (or `admin@example.com`)
* **Password**: `Password1-`

### 2. Standard Accounts (Demo Roles)
If you wish to test different permission levels:
* **Viewer Role** (Read-only): Log in with `viewer@example.com` / `Password1-`
* **Editor Role** (Create/Edit): Log in with `editor@example.com` / `Password1-`

---

## 🛠️ Verification & Troubleshooting

### "Failed to connect to PostgreSQL..."
If you get a connection error on startup:
1. Ensure Docker is running.
2. Verify that port `5432` is not blocked by another local database instance (e.g. local PostgreSQL service).
3. If running a native PostgreSQL service, ensure the password matches `HomePlate4` or update the value inside `appsettings.json` or your `.NET User Secrets` override.

### Run the Automated Test Suite
To confirm that your C# compilation is fully correct and ready:
```bash
dotnet test LoreTest.Tests/LoreTest.Tests.csproj
```
This will compile and run the 24 unit and integration tests, skipping the live database seeder test cleanly.
