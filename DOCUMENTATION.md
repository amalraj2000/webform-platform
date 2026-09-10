# Webform Platform - How It Works

Welcome to the Mailercloud Webform Platform! This document explains how the system works in very simple terms, so you can easily review and test the project.

## 🔑 Admin Login Credentials
When you run the project (and run the database seeders), a default Super Admin account is created for you so you can log in immediately:

- **Email:** `superadmin@gmail.com`
- **Password:** `password`

You can use these credentials to log in at `http://localhost:8000/login`.

---

## 🛠️ How The System Works

This platform is divided into three main parts:

### 1. The Form Builder (Admin Dashboard)
Once you log in, you can create new forms. 
- You can add fields like Text, Email, Checkboxes, and Dropdowns.
- You can add **Help Text** to guide users.
- You can set up **Conditional Visibility** (e.g., "Only show the Email field if the user selected 'Yes' on the previous question").
- Once you are happy with the design, you click **Publish Version**. This "freezes" the design into a version, ensuring that old submissions never break if you change the form in the future.

### 2. The Public Form (What your customers see)
After publishing, you get a unique shareable link (e.g., `http://localhost:8000/forms/some-unique-id`).
- Anyone with the link can view the form and fill it out.
- The form is smart: it actively watches what the user types. If a field is set to be hidden, it stays hidden until the exact right condition is met!
- When the user clicks "Submit", the form sends the data to the server.

### 3. The Backend Engine (How data is saved safely)
To handle a massive amount of traffic without crashing (like 10,000+ people submitting forms at the exact same time):
- When someone submits a form, the server doesn't immediately write it to the main database. 
- Instead, it quickly drops the data into a **High-Speed Redis Queue** and instantly tells the user "Submission Accepted!" (This makes the form feel incredibly fast).
- In the background, a **Queue Worker** carefully picks up the data, checks it for security (making sure nobody hacked the hidden fields), and saves it permanently to the MySQL database at a safe speed.

---

## 🛠️ Setup Guide

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
