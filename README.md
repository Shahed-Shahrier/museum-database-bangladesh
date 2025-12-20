# Unified Museum Database for Bangladesh

## Project Overview
This project is a web-based application designed to manage a unified database for museums in Bangladesh. It serves two main types of users:
1.  **General Users**: Can browse museums, events, art pieces, and purchase tickets.
2.  **Administrators**: Have full control over the database, including managing museums, events, artifacts, and ticket pricing.

## Features

### User Features
*   **Authentication**: Secure registration and login system.
*   **Browse Content**: View lists of Museums, Art Pieces, Artists, Galleries, and Events.
*   **Ticket Purchase**: Buy tickets for specific museums.
*   **Profile Management**: View personal profile and history of purchased tickets.

### Admin Features
*   **Dashboard**: Overview of system statistics.
*   **Content Management (CRUD)**: Create, Read, Update, and Delete records for:
    *   Museums
    *   Events
    *   Art Pieces
    *   Artists
    *   Galleries
    *   Tickets (Pricing and Types)
*   **User Management**: Create new users and manage access.
*   **Security**: Admin-only pages are protected by role-based access control.

## Technology Stack
*   **Frontend**: HTML5, CSS3 (Custom styling).
*   **Backend**: PHP 8.3.
*   **Database**: MariaDB / MySQL.
*   **Environment**: Linux (Ubuntu via GitHub Codespaces) / Windows (XAMPP compatible).

## Database Schema
The system uses a relational database with the following key tables:
*   `users`: Stores user credentials and roles ('admin' or 'guest').
*   `museum`: Core entity containing museum details.
*   `tickets`: Defines ticket types and prices for each museum.
*   `bookings`: Tracks tickets purchased by users (replaces the legacy `visitor` table).
*   `events`: Upcoming museum events.
*   `art_piece`: Individual artifacts and artworks.
*   `artist`: Artists associated with the art pieces.
*   `gallery`: Specific rooms or sections within a museum.

## Installation & Setup

### Prerequisites
*   PHP 8.0 or higher.
*   MySQL or MariaDB server.

### 1. Database Setup
1.  Create a database named `MUSEUM_DATABASE`.
2.  Import the schema and data:
    ```bash
    sudo mysql -e "CREATE DATABASE IF NOT EXISTS MUSEUM_DATABASE;"
    sudo mysql MUSEUM_DATABASE < museum_database.sql
    sudo mysql MUSEUM_DATABASE < update_tickets_event.sql
    sudo mysql MUSEUM_DATABASE < update_users_role.sql
    ```

### 2. Configuration
The database connection is defined in `config.php`.
*   **Host**: `localhost`
*   **User**: `root`
*   **Password**: (Empty by default)
*   **Database**: `MUSEUM_DATABASE`

### 3. Running the Application
**On Linux / Codespaces:**
Due to environment specifics, use the custom INI file to ensure MySQL extensions are loaded:
```bash
php -c php-custom.ini -S 0.0.0.0:8000
```

**On XAMPP (Windows):**
Place the project folder in `htdocs` and start Apache and MySQL via the XAMPP Control Panel. Access via `http://localhost/your-folder-name`.

## File Structure
*   `index.php`: Homepage.
*   `config.php`: Database connection settings.
*   `auth.php`: Authentication helper functions.
*   `header.php` / `footer.php`: Reusable UI components.
*   **Content Pages**: `museums.php`, `events.php`, `art_pieces.php`, `artists.php`, `galleries.php`.
*   **User Actions**: `login.php`, `register.php`, `buy_tickets.php`, `profile.php`.
*   **Admin Actions**: `dashboard.php`, `tickets.php` (Management), `create_user.php`.

## Usage Guide
*   **Default Admin Account**:
    *   Username: `admin77`
    *   (Password is hashed in the database. If lost, create a new user and manually update their role to 'admin' in the DB).
*   **Guest Access**:
    *   Click "Register" on the login page to create a new account.
    *   Guests can view all content but only see "Buy Tickets" after logging in.
