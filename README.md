# PHP Login System

![PHP](https://img.shields.io/badge/PHP-Ready-blue)
![Selenium](https://img.shields.io/badge/Selenium-Integrated-green)
![PHPUnit](https://img.shields.io/badge/PHPUnit-Tested-success)
[![Run Selenium Test](https://github.com/DalondaIkhimokpa/PHP-Login-Form-Automation-Testing/actions/workflows/test.yml/badge.svg)](https://github.com/DalondaIkhimokpa/PHP-Login-Form-Automation-Testing/actions/workflows/test.yml)
# Full Stack PHP + QA Automation Project (VSCode + phpMyAdmin + PHPUnit + MySQL  + Java)

> **Goal:** Build a full-stack web project with login/authentication, role-based access, admin CRUD panel, dynamic filtering, and complete manual + automation testing using PHPUnit (PHP), Java Unit Tests, and Selenium (Java). Designed as a **daily repeatable QA + Dev workflow.**

## Overview

This project is a simple PHP-based login system with user authentication, registration, and admin management. It includes features such as session handling, password hashing, and database interaction using MySQL.

## Features

- User registration and login
- Admin panel for user management
- Password hashing for security
- XSS and SQL injection prevention
- PHPUnit tests for database and user functionality
- 

## 📦  Test Features

- ✅ User registration & login
- ✅ Admin role support
- ✅ Secure password hashing (using `password_hash`)
- ✅ PDO & MySQLi support
- ✅ Basic session-based access control
- ✅ Unit tests with PHPUnit
- ✅ Selenium browser test automation
- ✅ CI with GitHub Actions

## Project Structure

── .gitignore 
├── .github
│ └── workflows 
│ └── test.yml 
├── admins.php 
├── composer.json 
├── composer.lock 
├── dash-board.php 
├── LICENSE 
├── README.md 
├── run-tests.sh 
├── images/ │ 
└── manual-screenshots
└── script-screenshots
└── sql-screenshots
├── log-in.php 
├── log-out.php 
├── registers.php 
├── tests/ │ 
├── └──Browser
│ └── BrowserTest.php 
├── └── SeleniumTest.php 
├── └── Test.php 
├── └── Unit
│ └── DatabaseTest.php 
├── └──AdminTest.php 
├── └── UserAuthTest.php 
├── └── SimpleTest.php 
├──  bootstrap.php
├── test_db.php
├── script_db.php
├── vendor/ │
├── autoload.php │ 
└── ...phpunit.xml 

## Test Methods

Each test method checks specific functionality:

Database Automated Test-PHP
testDatabaseConnection(): Verifies the database connection is established.
testUsersTableExists(): Ensures the users table exists in the database.
testAdminUserExists(): Confirms at least one admin user exists.
testPasswordHashing(): Validates that all passwords are hashed properly.

### Manual Testing - Excel

Manual testing involves manually interacting with the application
to ensure that the application functions as expected.

**Manual Testing Steps:**
**Open a web browser.**
Navigate to the application's URL.
Log in with valid credentials.
**Register a new user.**
Log out of the application.
Manual Testing Results:
**Expected Results:**
Expected Results: The application should display a login page.

### **SQL Testing- phpMyAdmin**

* **Type** : **Database Validation Testing**
* **Subtypes** :
* **Structural Testing** : Verifying schema (tables, columns)
* **Data Integrity Testing** : Checking constraints/relationships
* **Purpose** :
* Directly validate database state
* Verify CRUD operations
* Check query results

### **Script- `Localhost Web browser`**

* **Type** : **Unit Test** (for database initialization)
* **Characteristics** :
* Tests a single function (database creation)
* Automated (runs via browser)
* Returns pass/fail status
* **Purpose** :
* Verify the database setup code works
* Prevent "database not found" errors
* Part of **DevOps/Deployment Testing**

## Install dependencies:

Set up the database:

Import the users.sql file into your MySQL database.
Update the database credentials in db.php.
Run the application:

Place the project in your web server's root directory (e.g., XAMPP's htdocs).
Access the application via http://localhost/php-login-system.
Running Tests
Install PHPUnit:

Run the tests:

License
This project is licensed under the MIT License. See the LICENSE file for details.

1 vulnerability
Place the README.md in the root directory and the DatabaseTest.php file in the tests directory. Run the PHPUnit tests using the provided command.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Dalonda/php-login-system.git
   cd php-login-system

   Install dependencies:

   ```

Set up the database:

Import the users.sql file into your MySQL database.
Update the database credentials in db.php.
Run the application:

Place the project in your web server's root directory (e.g., XAMPP's htdocs).
Access the application via http://localhost/php-login-system.
Running Tests
Install PHPUnit:

Run the tests:

License
This project is licensed under the MIT License. See the LICENSE file for details.
