# 🎉 PSMS - Petrol Station Management System (Laravel)

**Complete, Production-Ready Laravel Application**

---

## 📦 What's Included

This is a **complete Laravel application** with:

- ✅ **8 Controllers** - All business logic
- ✅ **7 Models** - Complete database relationships
- ✅ **7 Migrations** - Full database schema
- ✅ **3 Seeders** - Initial data
- ✅ **1 Middleware** - Role-based access control
- ✅ **Complete Routes** - All endpoints configured
- ✅ **Auto-Installer** - One-command deployment
- ✅ **Documentation** - Comprehensive guides

---

## ⚡ Quick Start (3 Steps)

### **Step 1: Upload to Server**

Upload this folder to your web server via FTP, SSH, or cPanel.

### **Step 2: Run Auto-Installer**

```bash
cd /path/to/psms-laravel-complete
chmod +x INSTALLER.sh
./INSTALLER.sh
```

The installer will:
- Check requirements
- Install dependencies
- Configure database
- Run migrations
- Seed data
- Build assets
- Set permissions

**Time**: 5-10 minutes

### **Step 3: Configure Web Server**

Point document root to: `public/`

See `WEBSERVER_CONFIG.md` for details.

---

## 🎯 Default Credentials

**Admin:**
- Email: `admin@psms.com`
- Password: `admin123`

**Operator:**
- Email: `sarah@elitefuelstation.com`
- Password: `password123`

⚠️ **Change these immediately after login!**

---

## 📋 Requirements

- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js & NPM

### PHP Extensions:
- php-cli, php-mysql, php-xml, php-mbstring
- php-curl, php-zip, php-bcmath, php-gd

---

## 📚 Documentation

- **QUICK_START.md** - Installation guide
- **INSTALLER.sh** - Auto-installer script
- **WEBSERVER_CONFIG.md** - Nginx/Apache setup
- **TROUBLESHOOTING.md** - Common issues

---

## ✨ Features

### **Dashboard**
- Real-time statistics
- Revenue tracking
- Low stock alerts
- Recent transactions

### **Transaction Management**
- Create fuel transactions
- Search & filter
- Payment tracking
- Stock integration

### **Vehicle Management**
- Register vehicles
- Track history
- Owner information

### **Fuel Type Management**
- Manage fuel types
- Set pricing
- Stock tracking

### **Stock Management**
- Real-time monitoring
- Low stock alerts
- Update quantities

### **Account Management**
- Cash, Bank, Credit accounts
- Balance tracking
- Transaction integration

### **User Management** (Admin only)
- Role-based access (Admin, Manager, Operator)
- User status control
- Activity tracking

### **Settings System** (Admin only)
- General settings
- Tax configuration
- Email settings
- Alert preferences
- Backup settings
- Security options

---

## 🔒 Security Features

- ✅ Role-based access control
- ✅ CSRF protection
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ Password hashing (bcrypt)
- ✅ Session management
- ✅ Input validation

---

## 🎨 Technology Stack

- **Backend**: Laravel 11, PHP 8.1+
- **Database**: MySQL/MariaDB
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Authentication**: Laravel Breeze
- **Build Tool**: Vite

---

## 📊 Database Schema

### Tables:
1. **users** - User accounts with roles
2. **fuel_types** - Fuel types and pricing
3. **vehicles** - Vehicle registration
4. **transactions** - Fuel transactions
5. **stock** - Fuel stock levels
6. **accounts** - Financial accounts
7. **settings** - System configuration

---

## 🚀 Deployment Options

### **Option 1: Laravel Forge** (Recommended)
- Automated deployment
- One-click setup
- $17/month (Forge + DigitalOcean)
- **Time**: 2-3 hours

### **Option 2: Shared Hosting**
- Budget-friendly
- cPanel support
- $3-5/month
- **Time**: 4-6 hours

### **Option 3: Own VPS**
- Full control
- Manual setup
- $5/month
- **Time**: 8-12 hours

---

## 📞 Support

For issues or questions:
1. Check `TROUBLESHOOTING.md`
2. Review `WEBSERVER_CONFIG.md`
3. See Laravel documentation: https://laravel.com/docs

---

## 📄 License

MIT License - Free to use and modify

---

## 🎊 Credits

Built with Laravel 11 and modern web technologies.

---

**Ready to deploy? Run `./INSTALLER.sh` to get started!**

---

## 📝 Version

**Version**: 1.0.0  
**Release Date**: October 2025  
**Laravel Version**: 11.x  
**PHP Version**: 8.1+

---

## 🔄 Updates

To update the application:

```bash
git pull origin main
composer install
php artisan migrate
php artisan config:cache
npm install && npm run build
```

---

**Enjoy your new Petrol Station Management System! 🎉**
