# 🎓 FEU-OSDMS
### Online Student Disciplinary Management System

A containerized Laravel 10 application designed to streamline student records, lost asset tracking, and security incident reporting for the FEU community.

---

## 🚀 Quick Start (Docker Environment)

This project is fully containerized. You **do not** need to install PHP, MySQL, or Apache on your local machine.

### 1. Prerequisites
* **Docker Desktop**: [Download and Install](https://www.docker.com/products/docker-desktop/)
* **Port Availability**: Ensure **XAMPP MySQL is STOPPED**. This project requires port `3306` to be free.

### 2. Initial Setup
Clone the repository and prepare your local environment file:
```bash
cp .env.example .env

```

> **Note:** The `.env.example` is pre-configured with the internal Docker credentials (`DB_HOST=db`, `DB_USERNAME=sail`, `DB_PASSWORD=password`).

### 3. Spin Up the System

Build and launch the containers in detached mode:

```bash
docker-compose up -d --build

```

### 4. Application Initialization

Run these commands in your terminal to initialize the database and dependencies:

**Install Packages & Security:**

```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate

```

**Database Setup & Demo Data:**

```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed --class=DemoDataSeeder

```

**Enable Image Uploads:**

```bash
docker-compose exec app php artisan storage:link

```

---

## 🛠 Management & Access

| Resource | Access Detail |
| --- | --- |
| **Web Interface** | `http://localhost:8000` |
| **Database Host** | `127.0.0.1` |
| **Database Port** | `3306` |
| **User / Pass** | `sail` / `password` |
| **Database Name** | `feu_osdms` |
| **Stop Services** | `docker-compose down` |
| **View Logs** | `docker-compose logs -f app` |

---

## 📂 Project Structure

* **`app/`**: Core logic including Models (User, IncidentReport) and Controllers.
* **`resources/views/`**: Dashboard and Student Directory templates.
* **`database/seeders/`**: Contains the `DemoDataSeeder` for evaluation.
