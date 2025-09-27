## 🛠️ Technology Stack

-   **Backend**: Laravel 12, PHP 8.2+
-   **Database**: PostgreSQL
-   **Authentication**: Laravel Sanctum
-   **Queue System**: Database-driven job queues
-   **Event Streaming**: Apache Kafka
-   **Export**: Laravel Excel (Maatwebsite/Laravel-Excel)
-   **Testing**: PEST PHP
-   **Containerization**: Docker & Docker Compose
-   **Web Server**: Nginx
-   **Process Manager**: Supervisor (for queue workers)

## 🚀 Quick Start

### Documentation link

-   https://documenter.getpostman.com/view/14679973/2sB3QDvshy#0d1b72b7-64bf-4f13-ac36-8fe00b069dad

### Prerequisites

-   Docker and Docker Compose installed
-   Git

### 1. Clone the Repository

```bash
git clone https://github.com/SteveEnny/StreamLedger_api.git
cd StreamLedger_api
```

### 2. Environment Setup

> **Note for Assessment**: This repository includes a pre-configured `.env` file for easy setup and evaluation purposes. In production environments.

The `.env` file is already configured with the following settings:

-   PostgreSQL database connection
-   Kafka broker configuration
-   Queue system setup
-   Sanctum authentication domains

### 3. Build and Run with Docker

```bash
# Build and start all services
docker-compose up -d --build

# Generate application key optional since env file is pushed
docker-compose exec app php artisan key:generate

# Run database migrations
docker-compose exec app php artisan migrate

# Seed the database (optional)
docker-compose exec app php artisan db:seed
```

## 🐳 Docker Services

### Container Architecture

| Service     | Port | Description                           |
| ----------- | ---- | ------------------------------------- |
| `app`       | 8000 | Laravel application (PHP-FPM + Nginx) |
| `postgres`  | 5432 | PostgreSQL database                   |
| `kafka`     | 9092 | Apache Kafka message broker           |
| `zookeeper` | 2181 | Kafka dependency                      |

### Docker Commands

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f app

# Stop all services
docker-compose down

# Rebuild containers
docker-compose down && docker-compose up -d --build

# Access application shell
docker-compose exec app bash
```

## ⚙️ Artisan Commands

### Essential Commands

```bash
# Generate application key
docker-compose exec app php artisan key:generate

# Run database migrations
docker-compose exec app php artisan migrate

# Rollback migrations
docker-compose exec app php artisan migrate:rollback

# Seed database
docker-compose exec app php artisan db:seed

# Clear application cache
docker-compose exec app php artisan cache:clear

# Clear configuration cache
docker-compose exec app php artisan config:clear
```

### Queue Management

```bash
# Start queue worker
docker-compose exec app php artisan queue:work

# List queued jobs
docker-compose exec app php artisan queue:monitor

# Clear failed jobs
docker-compose exec app php artisan queue:flush
```

### Testing

```bash
# Run all PEST tests
docker-compose exec app php artisan test

# Run specific test file
docker-compose exec app php artisan test tests/Feature/TransactionTest.php
docker-compose exec app php artisan test tests/Feature/UserTest.php
docker-compose exec app php artisan test tests/Feature/WalletTest.php

```

## 📡 API Endpoints

### Authentication

-   `POST /api/v1/register` - User registration
-   `POST /api/v1/login` - User login
-   `POST /api/v1/logout` - User logout

### Wallet & Transactions

-   `GET /api/v1/wallet` - Get user wallet details and balance
-   `POST /api/v1/transactions` - Create new transaction (credit/debit)
-   `GET /api/v1/transactions` - List user transactions (paginated)
-   `POST /api/v1/transactions/export` - Generate Excel export (async)

### Example API Usage

```bash

```

## 🔧 Development Setup

### Local Development (without Docker)

```bash
# Install dependencies
composer install
npm install

# Generate application key (if not already set)
php artisan key:generate

# Start local servers
php artisan serve
php artisan queue:work
```

## 📁 Project Structure

```
├── app/
│   ├── Actions/              # Business logic actions
│   │   └── CreateTransaction.php
│   ├── Http/Controllers/     # API controllers
│   ├── Models/              # Eloquent models
│   ├── Jobs/                # Queue jobs
│   ├── Observers/                # Observers
│   │   └── TransactionObserver
│   └── Services/            # Service classes
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── tests/
│   ├── Feature/            # Feature tests
│   └── Unit/               # Unit tests
├── docker/                 # Docker configuration
├── docker-compose.yml      # Container orchestration
└── Dockerfile             # Application container
```

## 🧪 Testing

The application includes comprehensive PEST tests covering:

-   Authentication flow
-   Transaction creation and validation
-   Wallet balance management
-   API endpoint functionality
-   Kafka event publishing
-   Excel export generation

```bash
# Run all tests
docker-compose exec app php artisan test

```
