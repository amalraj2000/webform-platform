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

## Interviewer Setup Guide

Follow these steps to set up the project entirely within Docker (no local PHP or Node.js required):

1. **Clone the repository**:
   Open your terminal and run:
   ```bash
   git clone https://github.com/amalraj2000/webform-platform.git
   cd webform-platform
   ```

2. **Install Composer Dependencies (Docker Only)**:
   Use a temporary Docker container to install the PHP vendor dependencies:
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs
   ```

3. **Environment Setup**:
   Create a `.env` file from the sample file:
   ```bash
   cp .env.example .env
   ```

4. **Start the Docker Containers**:
   Use Laravel Sail to spin up the application server, MySQL, and Redis:
   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Generate App Key, Migrate, and Seed Database**:
   Run the Artisan commands inside your running Sail container:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```

6. **Install NPM Dependencies and Build Assets**:
   Compile the frontend assets inside the Sail container:
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

7. **Start the Queue Worker**:
   The form pipeline relies on asynchronous queues. Start a worker to process them:
   ```bash
   ./vendor/bin/sail artisan queue:work
   ```

8. **Start Working & Testing**:
   - The application is now accessible at **`http://localhost`** *(Sail exposes the app on port 80 automatically)*.
   - **Company Registration & Login**: You can register a new company account and log in.
   - **Form Creation**: Once logged in, you can create new forms, add conditional fields, and test submitting them by publishing the form and opening the public link.
   - **Superadmin Dashboard**: You can log in as a superadmin (use `superadmin@gmail.com` / `password`) to monitor the platform, allowing you to see all registered companies and their forms.

## Testing
To run the automated PHPUnit test suite:
```bash
php artisan test
# OR if using Docker:
./vendor/bin/sail artisan test
```

## License

This project is proprietary and confidential.
