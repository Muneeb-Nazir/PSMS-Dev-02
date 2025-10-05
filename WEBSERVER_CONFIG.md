# 🌐 Web Server Configuration Guide

## Nginx Configuration

### Full Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/psms-laravel-complete/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html index.htm;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;
}
```

### SSL Configuration (with Let's Encrypt)

```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/psms-laravel-complete/public;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    index index.php index.html index.htm;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

### Setup Steps

```bash
# 1. Create config file
sudo nano /etc/nginx/sites-available/psms

# 2. Paste configuration above

# 3. Enable site
sudo ln -s /etc/nginx/sites-available/psms /etc/nginx/sites-enabled/

# 4. Test configuration
sudo nginx -t

# 5. Reload Nginx
sudo systemctl reload nginx

# 6. Install SSL (optional but recommended)
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

---

## Apache Configuration

### Virtual Host Configuration

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/psms-laravel-complete/public

    <Directory /var/www/psms-laravel-complete/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/psms-error.log
    CustomLog ${APACHE_LOG_DIR}/psms-access.log combined
</VirtualHost>
```

### SSL Configuration

```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/psms-laravel-complete/public

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem

    <Directory /var/www/psms-laravel-complete/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/psms-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/psms-ssl-access.log combined
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>
```

### Setup Steps

```bash
# 1. Enable required modules
sudo a2enmod rewrite
sudo a2enmod ssl

# 2. Create config file
sudo nano /etc/apache2/sites-available/psms.conf

# 3. Paste configuration above

# 4. Enable site
sudo a2ensite psms.conf

# 5. Test configuration
sudo apache2ctl configtest

# 6. Reload Apache
sudo systemctl reload apache2

# 7. Install SSL (optional but recommended)
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

---

## File Permissions

```bash
# Set ownership (replace 'www-data' with your web server user)
sudo chown -R www-data:www-data /var/www/psms-laravel-complete

# Set directory permissions
sudo find /var/www/psms-laravel-complete -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/psms-laravel-complete -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /var/www/psms-laravel-complete/storage
sudo chmod -R 775 /var/www/psms-laravel-complete/bootstrap/cache
```

---

## Shared Hosting (cPanel)

### Setup Steps

1. **Upload Files**
   - Upload entire folder via File Manager or FTP
   - Place in `public_html/psms` or similar

2. **Configure Document Root**
   - In cPanel, go to "Domains" or "Addon Domains"
   - Set document root to: `public_html/psms/public`

3. **Create Database**
   - Go to "MySQL Databases"
   - Create new database
   - Create user and assign to database
   - Note credentials

4. **Run Installer**
   - SSH into your account (if available)
   - Run: `cd public_html/psms && ./INSTALLER.sh`
   - Or manually configure (see QUICK_START.md)

5. **Set Permissions**
   - In File Manager, set permissions:
   - `storage/` → 755
   - `bootstrap/cache/` → 755

---

## Environment Variables

Important `.env` settings for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

---

## Performance Optimization

### Enable OPcache (PHP)

```ini
; Add to php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### Laravel Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

---

## Security Checklist

- [ ] SSL certificate installed
- [ ] Firewall configured (allow 80, 443)
- [ ] File permissions set correctly
- [ ] `.env` file not publicly accessible
- [ ] `APP_DEBUG=false` in production
- [ ] Database credentials secure
- [ ] Regular backups configured
- [ ] Server software up to date

---

## Troubleshooting

### 500 Internal Server Error

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check web server logs
# Nginx:
sudo tail -f /var/log/nginx/error.log

# Apache:
sudo tail -f /var/log/apache2/error.log
```

### Permission Denied Errors

```bash
# Fix storage permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### 404 Not Found (except homepage)

- Check if mod_rewrite is enabled (Apache)
- Check if `.htaccess` exists in `public/`
- Verify web server configuration

---

**Your web server is now configured! 🎉**
