# 🚀 PSMS Laravel - Dual Installer System

## 📦 Two Installation Methods Available

Your PSMS Laravel package now includes **TWO professional installers**:

---

## 1️⃣ **Web-Based PHP Installer** (install.php) ⭐ RECOMMENDED

### **Perfect for:**
- Shared hosting (cPanel, Plesk, etc.)
- Users without SSH access
- Visual, step-by-step installation
- Beginners and non-technical users

### **How to Use:**

1. **Upload Files**
   - Upload entire folder to your web server
   - Via FTP, cPanel File Manager, or hosting control panel

2. **Access Installer**
   - Open browser and go to: `http://yourdomain.com/install.php`

3. **Follow 6 Easy Steps:**
   - ✅ **Step 1**: Requirements Check (automatic)
   - ✅ **Step 2**: Database Configuration (enter details)
   - ✅ **Step 3**: Application Settings (name, URL)
   - ✅ **Step 4**: Admin Account (create your login)
   - ✅ **Step 5**: Installation (automatic)
   - ✅ **Step 6**: Complete! (ready to use)

4. **Login**
   - Go to your application URL
   - Login with your admin credentials
   - Start using PSMS!

### **Features:**
- ✅ Beautiful, modern interface
- ✅ Progress bar and step indicators
- ✅ Real-time requirement checking
- ✅ Database connection testing
- ✅ Automatic dependency installation
- ✅ Error handling and validation
- ✅ Security checks
- ✅ One-click installation

### **Time:** 5-10 minutes (mostly automatic)

---

## 2️⃣ **Command-Line Bash Installer** (INSTALLER.sh)

### **Perfect for:**
- VPS/Cloud servers
- Users with SSH access
- Automated deployments
- Advanced users and developers

### **How to Use:**

1. **Upload Files**
   - Upload via SSH, Git, or FTP

2. **Run Installer**
   ```bash
   cd /path/to/psms-laravel-complete
   chmod +x INSTALLER.sh
   ./INSTALLER.sh
   ```

3. **Follow Prompts**
   - Enter database details
   - Confirm settings
   - Wait for installation

4. **Done!**
   - Access your application
   - Login and start using

### **Features:**
- ✅ Fast command-line installation
- ✅ Automatic prerequisite checking
- ✅ Interactive prompts
- ✅ Color-coded output
- ✅ Error handling
- ✅ Progress indicators
- ✅ Optimization included

### **Time:** 5-10 minutes

---

## 📊 Comparison

| Feature | install.php | INSTALLER.sh |
|---------|-------------|--------------|
| **Interface** | Web Browser | Command Line |
| **Access Required** | HTTP | SSH |
| **User Level** | Beginner | Intermediate |
| **Visual** | ✅ Yes | ❌ No |
| **Hosting Type** | Shared/Any | VPS/Cloud |
| **Automation** | High | High |
| **Time** | 5-10 min | 5-10 min |
| **Recommended For** | Most Users | Developers |

---

## 🎯 Which One Should You Use?

### **Use install.php if:**
- ✅ You have shared hosting (Hostinger, SiteGround, etc.)
- ✅ You don't have SSH access
- ✅ You prefer visual, step-by-step process
- ✅ You're not comfortable with command line
- ✅ You want the easiest method

### **Use INSTALLER.sh if:**
- ✅ You have VPS or cloud server
- ✅ You have SSH access
- ✅ You're comfortable with command line
- ✅ You want faster installation
- ✅ You're deploying multiple instances

---

## 🚀 Quick Start Guide

### **For Shared Hosting (Most Common):**

1. Upload files via FTP or cPanel
2. Create MySQL database in cPanel
3. Open: `http://yourdomain.com/install.php`
4. Follow the 6 steps
5. Done! 🎉

### **For VPS/Cloud Server:**

```bash
# Upload files
git clone https://github.com/Muneeb-Nazir/PSMS-Dev-02.git
cd PSMS-Dev-02

# Option 1: Web installer
# Just open http://your-ip/install.php

# Option 2: Command line installer
chmod +x INSTALLER.sh
./INSTALLER.sh
```

---

## 📋 Requirements (Both Methods)

- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer (auto-installed by installers)
- Node.js & NPM (for assets)
- Web server (Apache/Nginx)

### **PHP Extensions Required:**
- PDO, PDO MySQL
- OpenSSL, Mbstring
- Tokenizer, XML
- Ctype, JSON
- BCMath, Fileinfo

---

## 🔒 Security Notes

### **After Installation:**

1. **Delete install.php**
   ```bash
   rm install.php
   ```
   Or rename it to prevent re-installation

2. **Set Proper Permissions**
   ```bash
   chmod 755 storage bootstrap/cache
   ```

3. **Configure SSL**
   - Use Let's Encrypt for free SSL
   - Force HTTPS in .env

4. **Change Default Passwords**
   - Login and change admin password
   - Update all default credentials

5. **Set APP_DEBUG=false**
   - In production environment
   - Edit .env file

---

## 🎨 install.php Features

### **Beautiful Interface:**
- Modern gradient design
- Progress bar
- Step indicators
- Responsive layout
- Professional styling

### **Smart Validation:**
- Real-time requirement checking
- Database connection testing
- Form validation
- Error messages
- Success confirmations

### **Automatic Installation:**
- Creates .env file
- Installs Composer dependencies
- Generates application key
- Runs database migrations
- Seeds initial data
- Creates admin user
- Builds frontend assets
- Sets permissions
- Optimizes application
- Creates lock file

---

## 📖 Installation Steps (install.php)

### **Step 1: Requirements Check**
- Checks PHP version
- Verifies extensions
- Tests file permissions
- Validates Composer
- Shows pass/fail for each

### **Step 2: Database Configuration**
- Enter host (localhost)
- Enter port (3306)
- Enter database name
- Enter username
- Enter password
- Tests connection

### **Step 3: Application Settings**
- Application name
- Application URL
- Environment (production/local)

### **Step 4: Admin Account**
- Admin name
- Admin email
- Admin password
- Password confirmation

### **Step 5: Installation**
- Automatic process
- Shows progress
- Installs everything
- Takes 2-5 minutes

### **Step 6: Complete**
- Success message
- Login credentials
- Security reminders
- Link to application

---

## 🛠️ Troubleshooting

### **install.php Issues:**

**"Requirements Not Met"**
- Install missing PHP extensions
- Update PHP version
- Fix file permissions

**"Database Connection Failed"**
- Check database exists
- Verify credentials
- Check host/port
- Ensure MySQL is running

**"Installation Failed"**
- Check error message
- Verify Composer is installed
- Check file permissions
- Review server logs

### **INSTALLER.sh Issues:**

**"Command not found"**
- Install missing dependencies
- Check PATH variable
- Run with bash: `bash INSTALLER.sh`

**"Permission denied"**
- Make executable: `chmod +x INSTALLER.sh`
- Run with sudo if needed

---

## 🎉 After Installation

### **Both Methods Result In:**
- ✅ Complete Laravel application
- ✅ Database configured
- ✅ Admin account created
- ✅ Assets compiled
- ✅ Application optimized
- ✅ Ready to use!

### **Next Steps:**
1. Login to your application
2. Change admin password
3. Configure settings
4. Add fuel types
5. Create users
6. Start managing!

---

## 📞 Support

### **Documentation:**
- START_HERE.md - Overview
- README.md - Features
- QUICK_START.md - Manual installation
- WEBSERVER_CONFIG.md - Server setup

### **Installation Methods:**
- install.php - Web installer
- INSTALLER.sh - Command line installer
- QUICK_START.md - Manual step-by-step

---

## 🎊 Summary

You now have **TWO professional installers**:

1. **install.php** - Beautiful web-based installer (recommended for most users)
2. **INSTALLER.sh** - Fast command-line installer (for advanced users)

**Both are production-ready and fully automated!**

Choose the one that fits your hosting environment and comfort level.

**Happy installing! 🚀**
