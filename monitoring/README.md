# Docker Infrastructure Monitoring Stack

This directory contains the monitoring stack for checking on the status, health, and resource usage (CPU, Memory, Disk, Network) of the Docker containers running `loretest`, `rth-modern`, `xampp-legacy`, and other host/system infrastructure.

It is built using:
* **Prometheus:** A time-series database to gather and store metrics.
* **Grafana:** A visualization UI with pre-provisioned data sources and dashboards.
* **Docker Stats Exporter:** A Docker API socket metrics collector.
* **Node Exporter:** A host machine/VM system metrics collector.

---

## Port Allocations

| Service | Port | Description |
| :--- | :--- | :--- |
| **Grafana** | `3000` | Main Dashboard Interface |
| **Prometheus** | `9090` | Time-Series Database / Targets |
| **Docker Stats Exporter** | `9487` | Container metrics details page |
| **Node Exporter** | `9100` | Machine metrics details page |

---

## How to Run the Monitoring Stack

To start the monitoring services in the background:

```bash
docker compose up -d
```

To view running containers and logs:

```bash
docker compose ps
docker compose logs -f
```

To shut down the monitoring services:

```bash
docker compose down
```

---

## Viewing the Dashboards

1. Open your browser and navigate to **[http://localhost:3000](http://localhost:3000)** (Grafana).
2. Log in using the default credentials:
   * **Username:** `admin`
   * **Password:** `admin`
   *(You will be prompted to choose a new password on first login; you can skip this step if desired).*
3. Open the main menu on the top-left, click **Dashboards**, and select the **Docker Infrastructure Dashboard** under the **Infrastructure** folder.
4. Here you will see:
   * Real-time running container count.
   * CPU usage percentage per container.
   * Memory usage in megabytes/gigabytes and percentage of limit.
   * Network receive/transmit rate over time.
   * Disk read/write rates.
