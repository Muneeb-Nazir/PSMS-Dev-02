#!/bin/bash

# ============================================
# PSMS Laravel Auto-Installer
# ============================================
# This script automatically installs and configures
# the Laravel PSMS on your server
# ============================================

set -e

echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║                                                            ║"
echo "║     PSMS - Petrol Station Management System               ║"
echo "║     Laravel Auto-Installer v1.0                           ║"
echo "║                                                            ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then 
    print_warning "Please do not run as root. Run as your web user."
    exit 1
fi

echo "Starting installation..."
echo ""

# Step 1: Check PHP
print_info "Checking PHP installation..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    print_success "PHP $PHP_VERSION found"
    
    # Check PHP version
    if php -r "exit(version_compare(PHP_VERSION, '8.1.0', '>=') ? 0 : 1);"; then
        print_success "PHP version is 8.1 or higher"
    else
        print_error "PHP 8.1 or higher is required. Current: $PHP_VERSION"
        exit 1
    fi
else
    print_error "PHP is not installed"
    echo ""
    echo "Please install PHP 8.1+ with the following extensions:"
    echo "  - php-cli, php-mysql, php-xml, php-mbstring"
    echo "  - php-curl, php-zip, php-bcmath, php-gd"
    exit 1
fi

# Step 2: Check Composer
print_info "Checking Composer installation..."
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | grep -oP '\d+\.\d+\.\d+' | head -1)
    print_success "Composer $COMPOSER_VERSION found"
else
    print_error "Composer is not installed"
    echo ""
    echo "Install Composer from: https://getcomposer.org/download/"
    exit 1
fi

# Step 3: Check MySQL
print_info "Checking MySQL/MariaDB..."
if command -v mysql &> /dev/null; then
    print_success "MySQL client found"
else
    print_warning "MySQL client not found in PATH (may still work)"
fi

echo ""
echo "════════════════════════════════════════════════════════════"
echo "  Prerequisites Check Complete!"
echo "════════════════════════════════════════════════════════════"
echo ""

# Step 4: Get database credentials
echo "Please provide your database credentials:"
echo ""

read -p "Database Host [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Database Port [3306]: " DB_PORT
DB_PORT=${DB_PORT:-3306}

read -p "Database Name: " DB_DATABASE
while [ -z "$DB_DATABASE" ]; do
    print_error "Database name is required"
    read -p "Database Name: " DB_DATABASE
done

read -p "Database Username: " DB_USERNAME
while [ -z "$DB_USERNAME" ]; do
    print_error "Database username is required"
    read -p "Database Username: " DB_USERNAME
done

read -sp "Database Password: " DB_PASSWORD
echo ""

# Step 5: Get application URL
echo ""
read -p "Application URL (e.g., https://yourdomain.com): " APP_URL
while [ -z "$APP_URL" ]; do
    print_error "Application URL is required"
    read -p "Application URL: " APP_URL
done

echo ""
echo "════════════════════════════════════════════════════════════"
echo "  Configuration Summary"
echo "════════════════════════════════════════════════════════════"
echo "Database Host: $DB_HOST"
echo "Database Port: $DB_PORT"
echo "Database Name: $DB_DATABASE"
echo "Database User: $DB_USERNAME"
echo "App URL: $APP_URL"
echo "════════════════════════════════════════════════════════════"
echo ""

read -p "Continue with installation? (y/n): " CONFIRM
if [ "$CONFIRM" != "y" ] && [ "$CONFIRM" != "Y" ]; then
    print_warning "Installation cancelled"
    exit 0
fi

echo ""
print_info "Starting installation process..."
echo ""

# Step 6: Install Composer dependencies
print_info "Installing Composer dependencies (this may take a few minutes)..."
if composer install --no-interaction --prefer-dist --optimize-autoloader; then
    print_success "Composer dependencies installed"
else
    print_error "Failed to install Composer dependencies"
    exit 1
fi

# Step 7: Create .env file
print_info "Creating .env configuration file..."
if [ -f .env ]; then
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    print_warning "Existing .env backed up"
fi

cp .env.example .env
print_success ".env file created"

# Step 8: Configure .env
print_info "Configuring environment variables..."

# Generate APP_KEY
APP_KEY=$(php artisan key:generate --show)

# Update .env file
sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g" .env
sed -i "s|DB_HOST=.*|DB_HOST=$DB_HOST|g" .env
sed -i "s|DB_PORT=.*|DB_PORT=$DB_PORT|g" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|g" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|g" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|g" .env
sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|g" .env

print_success "Environment configured"

# Step 9: Test database connection
print_info "Testing database connection..."
if php artisan db:show &> /dev/null; then
    print_success "Database connection successful"
else
    print_error "Cannot connect to database. Please check your credentials."
    exit 1
fi

# Step 10: Run migrations
print_info "Creating database tables..."
if php artisan migrate --force; then
    print_success "Database tables created"
else
    print_error "Failed to create database tables"
    exit 1
fi

# Step 11: Seed database
print_info "Seeding initial data..."
if php artisan db:seed --force; then
    print_success "Initial data seeded"
else
    print_warning "Failed to seed data (you can do this manually later)"
fi

# Step 12: Install NPM dependencies
print_info "Installing NPM dependencies..."
if command -v npm &> /dev/null; then
    if npm install; then
        print_success "NPM dependencies installed"
        
        print_info "Building frontend assets..."
        if npm run build; then
            print_success "Frontend assets built"
        else
            print_warning "Failed to build assets (you can do this manually later)"
        fi
    else
        print_warning "Failed to install NPM dependencies (you can do this manually later)"
    fi
else
    print_warning "NPM not found. You'll need to install Node.js and run 'npm install && npm run build'"
fi

# Step 13: Set permissions
print_info "Setting file permissions..."
chmod -R 775 storage bootstrap/cache
print_success "Permissions set"

# Step 14: Clear caches
print_info "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
print_success "Caches cleared"

# Step 15: Optimize
print_info "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Application optimized"

echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║                                                            ║"
echo "║     ✓ Installation Complete!                              ║"
echo "║                                                            ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "════════════════════════════════════════════════════════════"
echo "  Default Login Credentials"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "  Admin Account:"
echo "  Email: admin@psms.com"
echo "  Password: admin123"
echo ""
echo "  Operator Account:"
echo "  Email: sarah@elitefuelstation.com"
echo "  Password: password123"
echo ""
echo "════════════════════════════════════════════════════════════"
echo "  ⚠️  IMPORTANT: Change these passwords immediately!"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "Your application is ready at: $APP_URL"
echo ""
echo "Next steps:"
echo "  1. Configure your web server (Nginx/Apache)"
echo "  2. Point document root to: $(pwd)/public"
echo "  3. Visit your application URL"
echo "  4. Login and change default passwords"
echo ""
echo "For web server configuration, see: WEBSERVER_CONFIG.md"
echo ""
print_success "Installation completed successfully!"
echo ""
