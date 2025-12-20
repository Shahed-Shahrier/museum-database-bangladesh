#!/bin/bash
# Setup script for GitHub Codespaces

echo "Setting up Museum Database application..."

# Start MySQL service
sudo systemctl start mysql
sleep 2

# Configure MySQL
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';" 2>/dev/null || true
sudo mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true

# Create database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS MUSEUM_DATABASE;"

# Import schema
echo "Importing database schema..."
mysql -u root MUSEUM_DATABASE < museum_database.sql
mysql -u root MUSEUM_DATABASE < update_tickets_event.sql
mysql -u root MUSEUM_DATABASE < update_users_role.sql
mysql -u root MUSEUM_DATABASE < create_bookings_table.sql

echo "Database setup complete!"
echo "To start the application, run: php -c php-custom.ini -S 0.0.0.0:8000"
