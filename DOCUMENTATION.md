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

## 🚀 Quick Test Guide

1. Follow the setup instructions in the `README.md` to start the server.
2. Go to `http://localhost:8000` and click "Sign into Dashboard".
3. Log in with `superadmin@gmail.com` / `password`.
4. Create a new form and add a few fields (try adding a Conditional field!).
5. Click "Publish Version".
6. Click "Open Public Form" and try filling it out yourself.
7. Go back to your Admin Dashboard and click the "Refresh" button under Recent Submissions to see your data instantly appear!
