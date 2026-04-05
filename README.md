# BoardSwap

An online surf shop web application built with Vue.js and a PHP REST API backend.

## Live Application

| Service | URL |
|---|---|
| Frontend (Vue SPA) | https://board-swap-web-app-wd-2.vercel.app |
| Backend (PHP REST API) | https://boardswap-webapp-wd2.onrender.com |

Public URL: https://board-swap-web-app-wd-2.vercel.app

## Login Credentials

| Role | Email | Password |
|---|---|---|
| Admin | admin@user.com | Getin123 |
| User | altepost@gmail.com | getin123 |

## Features

- Browse and search surfboards with filtering and pagination
- User registration and login with JWT authentication
- Add products to cart and proceed to checkout
- Live surf conditions powered by the Open-Meteo marine API (Hossegor, France)
- Admin panel for managing products and users

## Local Development

### Requirements
- Docker Desktop

### Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/WouterAltepost/BoardSwap-WebApp-WD2.git
   cd BoardSwap-WebApp-WD2
   ```

2. Start the Docker environment:
   ```bash
   docker compose up
   ```

3. The following services will be available:
   - Vue frontend: http://localhost:5173
   - PHP API: http://localhost:80
   - phpMyAdmin: http://localhost:8080

### Environment Variables

The backend reads the following environment variables. In local development these are set in `backend/.env`:

| Variable | Description |
|---|---|
| DB_HOST | Database host |
| DB_NAME | Database name |
| DB_USER | Database user |
| DB_PASSWORD | Database password |
| DB_CHARSET | Character set (utf8mb4) |
| JWT_SECRET | Secret key for JWT signing |

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | Vue 3, Vite, Pinia, Vue Router, Axios, Bootstrap |
| Backend | PHP 8.2, Composer, PSR-4 autoloading |
| Authentication | JWT |
| Database | MySQL / MariaDB |
| Local dev | Docker, nginx, PHP-FPM |
| Frontend hosting | Vercel |
| Backend hosting | Render.com |
| Database hosting | Railway |
