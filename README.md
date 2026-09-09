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

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 8.0+
- Redis (for queue processing)

### Installation

1. **Clone the repository** (if applicable) and navigate to the project directory:
   ```bash
   cd webform-platform
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install NPM dependencies**:
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup**:
   Copy the example environment file and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure to configure your `DB_*` and `REDIS_*` settings in the `.env` file.*

5. **Run Migrations**:
   ```bash
   php artisan migrate --seed
   ```

6. **Start the Development Servers**:
   You'll need to run the web server and the queue worker in separate terminal windows:
   ```bash
   php artisan serve
   ```
   ```bash
   php artisan queue:work
   ```

## License

This project is proprietary and confidential.
