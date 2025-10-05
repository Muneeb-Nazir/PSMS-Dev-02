# 🚀 PSMS Laravel - Quick Start Guide

## 📦 What You Have

This is a **complete, production-ready Laravel application** for Petrol Station Management.

---

## ⚡ Quick Installation (3 Steps)

### **Step 1: Upload to Your Server**

Upload this entire folder to your web server via:
- FTP/SFTP
- cPanel File Manager
- Git
- SSH

### **Step 2: Run the Auto-Installer**

```bash
cd /path/to/psms-laravel-complete
chmod +x INSTALLER.sh
./INSTALLER.sh
```

The installer will:
- ✅ Check PHP/Composer requirements
- ✅ Install dependencies
- ✅ Configure database
- ✅ Run migrations
- ✅ Seed initial data
- ✅ Build frontend assets
- ✅ Set permissions
- ✅ Optimize application

**Time**: 5-10 minutes

### **Step 3: Configure Web Server**

Point your web server document root to: `public/`

See `WEBSERVER_CONFIG.md` for Nginx/Apache configuration.

---

## 🎯 Default Login Credentials

**Admin Account:**
- Email: `admin@psms.com`
- Password: `admin123`

**Operator Account:**
- Email: `sarah@elitefuelstation.com`
- Password: `password123`

⚠️ **Change these passwords immediately after first login!**

---

## 📋 Requirements

- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js & NPM (for frontend assets)

### Required PHP Extensions:
- php-cli
- php-mysql
- php-xml
- php-mbstring
- php-curl
- php-zip
- php-bcmath
- php-gd

---

## 🔧 Manual Installation (If Auto-Installer Fails)

```bash
# 1. Install Composer dependencies
composer install --no-dev --optimize-autoloader

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Configure .env file
nano .env
# Set DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Run migrations
php artisan migrate --force

# 6. Seed database
php artisan db:seed --force

# 7. Install NPM dependencies
npm install

# 8. Build assets
npm run build

# 9. Set permissions
chmod -R 775 storage bootstrap/cache

# 10. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🌐 Web Server Configuration

### **Nginx**

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/psms-laravel-complete/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### **Apache**

The `.htaccess` file is already included in the `public/` directory.

Just point your document root to: `/path/to/psms-laravel-complete/public`

---

## ✅ Features Included

- ✅ **Dashboard** - Statistics, revenue tracking, alerts
- ✅ **Transactions** - Complete fuel transaction management
- ✅ **Vehicles** - Vehicle registration and tracking
- ✅ **Fuel Types** - Manage fuel types and pricing
- ✅ **Stock Management** - Real-time stock monitoring
- ✅ **Accounts** - Financial account management
- ✅ **User Management** - Role-based access control
- ✅ **Settings** - 6-category configuration system
- ✅ **Responsive Design** - Works on all devices
- ✅ **Security** - CSRF protection, SQL injection prevention

---

## 🎊 After Installation

1. Visit your application URL
2. Login with admin credentials
3. Change default passwords
4. Configure settings (General, Tax, Email, etc.)
5. Add your fuel types and pricing
6. Start managing transactions!

---

## 📞 Need Help?

- Check `TROUBLESHOOTING.md` for common issues
- See `WEBSERVER_CONFIG.md` for server setup
- Read `DEPLOYMENT_GUIDE.md` for detailed instructions

---

## 🔒 Security Checklist

After installation:

- [ ] Change all default passwords
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure SSL certificate
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Configure firewall rules
- [ ] Set up regular backups

---

**Ready to go! Your PSMS is now installed and ready to use! 🎉**
