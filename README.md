# Mailercloud Forms Platform

A scalable, multi-tenant webform building platform built with Laravel. This platform empowers businesses to design custom webforms, securely capture high-throughput responses, and analyze data in the cloud.

## Key Features

- **Dynamic Form Builder**: Create complex forms on the fly with customizable input types and logic.
- **High Concurrency Pipeline**: Built for scale. An asynchronous Redis queue pipeline ensures no submissions are dropped even during massive traffic spikes (10,000+ submissions/min).
- **Multi-Tenant Isolation**: Enterprise-grade security guaranteeing strict data separation between different company accounts.
- **Responsive UI**: A modern, dynamic frontend styled with Tailwind CSS.

## Architecture Highlights

- **PHP 8.x & Laravel 11.x**: Leveraging the latest in the PHP ecosystem.
- **MySQL JSON Columns**: Seamlessly storing dynamic form submissions without the overhead of EAV tables or NoSQL complexity.
- **Redis Queueing**: Immediate HTTP 202 responses for data ingestion, keeping the web server instantly ready for the next request.
- **IP-Based Rate Limiting**: Built-in protections against spam and DDoS attacks.

## Getting Started

You can run this project either natively using PHP/Composer or via Docker using Laravel Sail (recommended for easy setup).

### Method 1: Using Docker & Laravel Sail (Recommended)
This is the easiest way to run the project, as it guarantees you have the exact correct versions of PHP, MySQL, and Redis without installing them on your host machine.

1. **Prerequisites**: You only need [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running.
2. **Clone the repository**:
   ```bash
   git clone https://github.com/amalraj2000/webform-platform.git
   cd webform-platform
   ```
3. **Install Composer Dependencies** (using a temporary container):
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs
   ```
4. **Environment Setup**:
   ```bash
   cp .env.example .env
   ```
5. **Start the Sail Containers**:
   ```bash
   ./vendor/bin/sail up -d
   ```
6. **Generate App Key, Migrate, and Seed Database**:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate --seed
   ```
7. **Install NPM dependencies and build assets**:
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```
8. **Start the Queue Worker** (Required for processing form submissions):
   ```bash
   ./vendor/bin/sail artisan queue:work
   ```
   
**The application is now accessible at `http://localhost`.**

---

### Method 2: Native Setup (PHP/Composer)

1. **Prerequisites**:
   - PHP 8.2 or higher
   - Composer
   - Node.js & NPM
   - MySQL 8.0+
   - Redis (must be running on your system)

2. **Installation**:
   ```bash
   git clone https://github.com/amalraj2000/webform-platform.git
   cd webform-platform
   composer install
   npm install && npm run build
   ```

3. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Important: Update your `.env` file with your local `DB_*` and `REDIS_*` credentials.*

4. **Run Migrations**:
   ```bash
   php artisan migrate --seed
   ```

5. **Start the Development Servers**:
   You will need two separate terminal windows.
   
   Window 1 (Web Server):
   ```bash
   php artisan serve
   ```
   
   Window 2 (Queue Worker):
   ```bash
   php artisan queue:work
   ```

**The application is now accessible at `http://localhost:8000`.**

## Testing
To run the automated PHPUnit test suite:
```bash
php artisan test
# OR if using Docker:
./vendor/bin/sail artisan test
```

## License

This project is proprietary and confidential.
