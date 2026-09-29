
md
# Eve Moon Scan Analyzer

Modern moon scan analyzer for Eve Online — a fork and modernization of eve-skylizer.

Eve Moon Scan Analyzer helps track and analyze moon scan data, maintain scan history, and work with EVE Online market and pricing information in a modern PHP application.

## Overview

This project is built with PHP 8.2 and Laminas MVC, and is designed to run with MySQL and optional Docker support. It provides a structured way to process moon scans, store results, and expose the data through API endpoints.

## Features

- Moon scan analysis and tracking
- Scan history management
- EVE Online ESI authentication support
- Material pricing updates
- REST-style API routes
- Doctrine ORM integration
- MySQL-backed persistence
- Docker development environment
- PHPUnit, PHPStan, and coding standards tooling

## Tech Stack

- PHP 8.2
- Laminas MVC
- Doctrine ORM / DBAL
- MySQL 8
- Composer
- Docker / Docker Compose

## Project Structure

```text
.
├── config/                  # Application configuration
├── docker/                  # Docker-related files
├── migrations/              # Doctrine migration files
├── module/                  # Laminas application module
├── public/                  # Public web entry point
├── src/                     # Application source code
├── tests/                   # Automated tests
├── .env.example             # Example environment configuration
├── composer.json            # Composer dependencies and scripts
├── docker-compose.yml       # Docker Compose configuration
├── LICENSE                  # Apache 2.0 license
├── README.md                # Project overview
├── phpunit.xml.dist         # PHPUnit configuration
├── phpstan.neon.dist        # PHPStan configuration
├── migrations.php           # Migration config
└── .gitignore
Requirements
Local development
PHP 8.2+
Composer
MySQL 8+
Required PHP extensions:
gd
intl
pdo
pdo_mysql
json
Optional
Docker
Docker Compose
Quick Start
Using Docker (recommended)
Clone the repository:
bash
git clone https://github.com/gimmihoff/eve-moon-scan-analyzer.git
cd eve-moon-scan-analyzer
Copy the example environment file:
bash
cp .env.example .env
Update the environment values in .env for your database and EVE Online API credentials.

Start the application:

bash
docker compose up --build
Open the app in your browser:
Text
http://localhost:8080
Manual installation
Install PHP 8.2 and the required extensions.

Clone the repository:

bash
git clone https://github.com/gimmihoff/eve-moon-scan-analyzer.git
cd eve-moon-scan-analyzer
Install PHP dependencies:
bash
composer install
Create a MySQL database.

Configure environment variables for your local setup.

Start the app:

bash
composer serve
or:

bash
php -S 0.0.0.0:8080 -t public/ public/index.php
Visit:
Text
http://localhost:8080
Environment Configuration
The application reads environment variables from the shell and from the project config files in config/autoload/.

Common variables include:

DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASSWORD
DB_CHARSET
APP_ENV
APP_DEBUG
ESI_DATASOURCE
ESI_CLIENT_ID
ESI_SECRET_KEY
ESI_CALLBACK_URL
JWT_SECRET
An example is provided in .env.example.

API Routes
The project includes API endpoints for:

Authentication
Moon data access
Scan tracking
Pricing operations
Examples from the routing configuration include:

/auth
/api
/api/moons
/api/scans
/api/pricing
Development
Run the test suite:

bash
composer test
Run static analysis:

bash
composer stan
Run code style checks:

bash
composer cs-check
License
This project is licensed under the Apache License 2.0. See the LICENSE file for details.

Contributing
Contributions are welcome. Please fork the repository and submit a pull request with a clear description of the changes.

If you want a more visually polished version with badges and a project screenshot section, I can also create that for you.

Code

INSTALL.md

```md
# Installation Guide

This guide explains how to install and run Eve Moon Scan Analyzer locally.

## Prerequisites

Before installing, ensure you have:

- Git
- PHP 8.2+
- Composer
- MySQL 8+
- Optional: Docker and Docker Compose

Required PHP extensions:

- gd
- intl
- pdo
- pdo_mysql
- json

## Option 1: Docker Installation

This is the easiest and most reliable setup.

### Step 1: Clone the repository

```bash
git clone https://github.com/gimmihoff/eve-moon-scan-analyzer.git
cd eve-moon-scan-analyzer
Step 2: Create the environment file
bash
cp .env.example .env
Step 3: Update environment values
Edit .env and configure:

database credentials
EVE Online ESI values
local app settings
Step 4: Start the app
bash
docker compose up --build
Step 5: Open the application
Text
http://localhost:8080
The Docker setup starts the PHP application and MySQL database containers automatically.

Option 2: Manual Installation
Step 1: Install PHP requirements
Install PHP 8.2 and required extensions, then verify the installation:

bash
php -v
composer --version
Step 2: Clone the repository
bash
git clone https://github.com/gimmihoff/eve-moon-scan-analyzer.git
cd eve-moon-scan-analyzer
Step 3: Install Composer dependencies
bash
composer install
Step 4: Create the database
Create a MySQL database for the project, for example:

SQL
CREATE DATABASE eve_scanner;
Step 5: Configure environment variables
Set the following environment variables:

bash
export DB_HOST=localhost
export DB_PORT=3306
export DB_NAME=eve_scanner
export DB_USER=root
export DB_PASSWORD=password
export DB_CHARSET=utf8mb4
export APP_ENV=development
export APP_DEBUG=true
export ESI_DATASOURCE=tranquility
export ESI_CLIENT_ID=your_client_id_here
export ESI_SECRET_KEY=your_secret_key_here
export ESI_CALLBACK_URL=http://localhost:8080/auth/callback
export JWT_SECRET=change_me
Step 6: Start the app
bash
composer serve
Or run the built-in PHP server manually:

bash
php -S 0.0.0.0:8080 -t public/ public/index.php
Step 7: Open the app
Text
http://localhost:8080
Troubleshooting
MySQL connection issues
Check your database config:

DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASSWORD
ESI login not working
Verify that:

your EVE Online client ID is correct
your secret key is valid
ESI_CALLBACK_URL matches the callback URL configured in the EVE developer portal
Missing PHP extensions
On Debian/Ubuntu, install the required packages:

bash
sudo apt-get update
sudo apt-get install -y php8.2-gd php8.2-intl php8.2-mysql php8.2-xml
