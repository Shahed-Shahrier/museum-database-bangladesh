#!/bin/bash
# Setup script for GitHub Codespaces

echo "Setting up Museum Database application..."

# Verify mysqli extension is loaded
if ! php -m | grep -q mysqli; then
    echo "WARNING: mysqli extension not found. Installing now..."
    sudo apt-get update
    sudo apt-get install -y php8.3-mysqli php8.3-mysql
    sudo phpenmod mysqli
fi

# Start MySQL service
echo "Starting MySQL..."
sudo systemctl start mysql
sleep 2

# Configure MySQL
echo "Configuring MySQL..."
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';" 2>/dev/null || true
sudo mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true

# Create database
echo "Creating database..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS MUSEUM_DATABASE;"

# Import schema
echo "Importing database schema..."
mysql -u root MUSEUM_DATABASE < museum_database.sql
mysql -u root MUSEUM_DATABASE < update_tickets_event.sql
mysql -u root MUSEUM_DATABASE < update_users_role.sql
mysql -u root MUSEUM_DATABASE < create_bookings_table.sql

echo ""
echo "✅ Database setup complete!"
echo ""
echo "To start the application, run:"
echo "  php -S 0.0.0.0:8000"
echo ""
