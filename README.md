# Ticketing

## Installation

### Method 1: Using Docker

1. Clone the repository
2. Run `composer install`
3. Copy the file `.env.template` to `.env`
5. Run `docker compose up`
6. Access the application at `http://localhost:8081`

### Method 2: Using native PHP

1. Clone the repository
2. Run `composer install`
3. launch an sql server and import the file `init.sql` from the folder `ticketing`
4. Copy the file `.env.template` to `ticketing/.env`
5. Modify the `ticketing/.env` file to match your database configuration
6. Run `php -S localhost:8081 -t ticketing/`