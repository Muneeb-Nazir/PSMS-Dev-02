<?php
/**
 * PSMS Laravel - Web Installer
 * Professional PHP-based installer for easy deployment
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
define('MIN_PHP_VERSION', '8.1.0');
define('INSTALLER_VERSION', '1.0.0');

// Installation steps
$steps = [
    1 => 'Requirements Check',
    2 => 'Database Configuration',
    3 => 'Application Settings',
    4 => 'Admin Account',
    5 => 'Installation',
    6 => 'Complete'
];

// Get current step
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if ($step < 1) $step = 1;
if ($step > 6) $step = 6;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleFormSubmission($step);
}

// Functions
function handleFormSubmission($step) {
    switch ($step) {
        case 2:
            $_SESSION['db_host'] = $_POST['db_host'] ?? 'localhost';
            $_SESSION['db_port'] = $_POST['db_port'] ?? '3306';
            $_SESSION['db_name'] = $_POST['db_name'] ?? '';
            $_SESSION['db_user'] = $_POST['db_user'] ?? '';
            $_SESSION['db_pass'] = $_POST['db_pass'] ?? '';
            
            // Test database connection
            if (testDatabaseConnection()) {
                header('Location: install.php?step=3');
                exit;
            }
            break;
            
        case 3:
            $_SESSION['app_name'] = $_POST['app_name'] ?? 'PSMS';
            $_SESSION['app_url'] = $_POST['app_url'] ?? '';
            $_SESSION['app_env'] = $_POST['app_env'] ?? 'production';
            header('Location: install.php?step=4');
            exit;
            
        case 4:
            $_SESSION['admin_name'] = $_POST['admin_name'] ?? '';
            $_SESSION['admin_email'] = $_POST['admin_email'] ?? '';
            $_SESSION['admin_password'] = $_POST['admin_password'] ?? '';
            header('Location: install.php?step=5');
            exit;
            
        case 5:
            performInstallation();
            break;
    }
}

function testDatabaseConnection() {
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s',
            $_SESSION['db_host'],
            $_SESSION['db_port'],
            $_SESSION['db_name']
        );
        
        $pdo = new PDO($dsn, $_SESSION['db_user'], $_SESSION['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return true;
    } catch (PDOException $e) {
        $_SESSION['db_error'] = $e->getMessage();
        return false;
    }
}

function performInstallation() {
    set_time_limit(300); // 5 minutes
    
    try {
        // Step 1: Create .env file
        createEnvFile();
        
        // Step 2: Install Composer dependencies
        installComposerDependencies();
        
        // Step 3: Generate application key
        generateAppKey();
        
        // Step 4: Run migrations
        runMigrations();
        
        // Step 5: Run seeders
        runSeeders();
        
        // Step 6: Create admin user
        createAdminUser();
        
        // Step 7: Install NPM dependencies and build assets
        buildAssets();
        
        // Step 8: Set permissions
        setPermissions();
        
        // Step 9: Optimize application
        optimizeApplication();
        
        // Step 10: Create lock file
        file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
        
        $_SESSION['installation_complete'] = true;
        header('Location: install.php?step=6');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['install_error'] = $e->getMessage();
    }
}

function createEnvFile() {
    $envContent = file_get_contents(__DIR__ . '/.env.example');
    
    // Replace placeholders
    $replacements = [
        'APP_NAME=Laravel' => 'APP_NAME="' . $_SESSION['app_name'] . '"',
        'APP_ENV=local' => 'APP_ENV=' . $_SESSION['app_env'],
        'APP_DEBUG=true' => 'APP_DEBUG=' . ($_SESSION['app_env'] === 'production' ? 'false' : 'true'),
        'APP_URL=http://localhost' => 'APP_URL=' . $_SESSION['app_url'],
        'DB_HOST=127.0.0.1' => 'DB_HOST=' . $_SESSION['db_host'],
        'DB_PORT=3306' => 'DB_PORT=' . $_SESSION['db_port'],
        'DB_DATABASE=laravel' => 'DB_DATABASE=' . $_SESSION['db_name'],
        'DB_USERNAME=root' => 'DB_USERNAME=' . $_SESSION['db_user'],
        'DB_PASSWORD=' => 'DB_PASSWORD=' . $_SESSION['db_pass'],
    ];
    
    foreach ($replacements as $search => $replace) {
        $envContent = str_replace($search, $replace, $envContent);
    }
    
    file_put_contents(__DIR__ . '/.env', $envContent);
}

function installComposerDependencies() {
    exec('cd ' . __DIR__ . ' && composer install --no-dev --optimize-autoloader 2>&1', $output, $return);
    if ($return !== 0) {
        throw new Exception('Failed to install Composer dependencies: ' . implode("\n", $output));
    }
}

function generateAppKey() {
    exec('cd ' . __DIR__ . ' && php artisan key:generate --force 2>&1', $output, $return);
    if ($return !== 0) {
        throw new Exception('Failed to generate application key');
    }
}

function runMigrations() {
    exec('cd ' . __DIR__ . ' && php artisan migrate --force 2>&1', $output, $return);
    if ($return !== 0) {
        throw new Exception('Failed to run migrations: ' . implode("\n", $output));
    }
}

function runSeeders() {
    exec('cd ' . __DIR__ . ' && php artisan db:seed --force 2>&1', $output, $return);
    if ($return !== 0) {
        throw new Exception('Failed to run seeders');
    }
}

function createAdminUser() {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s', $_SESSION['db_host'], $_SESSION['db_port'], $_SESSION['db_name']),
        $_SESSION['db_user'],
        $_SESSION['db_pass']
    );
    
    $hashedPassword = password_hash($_SESSION['admin_password'], PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE role = 'admin' LIMIT 1");
    $stmt->execute([$_SESSION['admin_name'], $_SESSION['admin_email'], $hashedPassword]);
}

function buildAssets() {
    exec('cd ' . __DIR__ . ' && npm install 2>&1', $output, $return);
    exec('cd ' . __DIR__ . ' && npm run build 2>&1', $output, $return);
}

function setPermissions() {
    chmod(__DIR__ . '/storage', 0775);
    chmod(__DIR__ . '/bootstrap/cache', 0775);
}

function optimizeApplication() {
    exec('cd ' . __DIR__ . ' && php artisan config:cache 2>&1');
    exec('cd ' . __DIR__ . ' && php artisan route:cache 2>&1');
    exec('cd ' . __DIR__ . ' && php artisan view:cache 2>&1');
}

function checkRequirements() {
    $requirements = [
        'PHP Version >= ' . MIN_PHP_VERSION => version_compare(PHP_VERSION, MIN_PHP_VERSION, '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'OpenSSL Extension' => extension_loaded('openssl'),
        'Mbstring Extension' => extension_loaded('mbstring'),
        'Tokenizer Extension' => extension_loaded('tokenizer'),
        'XML Extension' => extension_loaded('xml'),
        'Ctype Extension' => extension_loaded('ctype'),
        'JSON Extension' => extension_loaded('json'),
        'BCMath Extension' => extension_loaded('bcmath'),
        'Fileinfo Extension' => extension_loaded('fileinfo'),
        'Writable storage/' => is_writable(__DIR__ . '/storage'),
        'Writable bootstrap/cache/' => is_writable(__DIR__ . '/bootstrap/cache'),
        'Composer Installed' => file_exists(__DIR__ . '/vendor/autoload.php') || commandExists('composer'),
    ];
    
    return $requirements;
}

function commandExists($command) {
    $return = shell_exec(sprintf("which %s", escapeshellarg($command)));
    return !empty($return);
}

// Check if already installed
if (file_exists(__DIR__ . '/.installed') && $step !== 6) {
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Already Installed</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 50px; text-align: center; }
            .box { background: white; padding: 40px; border-radius: 10px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #e74c3c; }
            a { color: #3498db; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>⚠️ Already Installed</h1>
            <p>PSMS Laravel is already installed on this server.</p>
            <p>To reinstall, please delete the <code>.installed</code> file.</p>
            <p><a href="/">Go to Application →</a></p>
        </div>
    </body>
    </html>
    ');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSMS Laravel Installer - Step <?php echo $step; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        .progress-bar {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 30px;
        }
        .progress-fill {
            background: white;
            height: 10px;
            border-radius: 5px;
            transition: width 0.3s;
        }
        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step-item {
            flex: 1;
            text-align: center;
            color: white;
            opacity: 0.5;
            font-size: 0.9em;
        }
        .step-item.active {
            opacity: 1;
            font-weight: bold;
        }
        .step-item.completed {
            opacity: 0.8;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .card h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.8em;
        }
        .card p {
            color: #666;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
            margin-right: 10px;
        }
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        .requirements-list {
            list-style: none;
        }
        .requirements-list li {
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .requirements-list li.pass {
            background: #d4edda;
            color: #155724;
        }
        .requirements-list li.fail {
            background: #f8d7da;
            color: #721c24;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .success-icon {
            font-size: 4em;
            color: #28a745;
            text-align: center;
            margin-bottom: 20px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .loading {
            text-align: center;
            padding: 40px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .help-text {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 PSMS Laravel Installer</h1>
            <p>Professional Petrol Station Management System</p>
            <p style="font-size: 0.9em; opacity: 0.8;">Version <?php echo INSTALLER_VERSION; ?></p>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo ($step / 6) * 100; ?>%"></div>
        </div>

        <div class="steps">
            <?php foreach ($steps as $num => $name): ?>
                <div class="step-item <?php echo $num === $step ? 'active' : ($num < $step ? 'completed' : ''); ?>">
                    <?php echo $num; ?>. <?php echo $name; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card">
            <?php if ($step === 1): ?>
                <!-- Step 1: Requirements Check -->
                <h2>📋 System Requirements</h2>
                <p>Checking if your server meets the requirements...</p>

                <?php $requirements = checkRequirements(); ?>
                <?php $allPassed = !in_array(false, $requirements); ?>

                <ul class="requirements-list">
                    <?php foreach ($requirements as $name => $passed): ?>
                        <li class="<?php echo $passed ? 'pass' : 'fail'; ?>">
                            <span><?php echo $name; ?></span>
                            <span><?php echo $passed ? '✅ Pass' : '❌ Fail'; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if (!$allPassed): ?>
                    <div class="alert alert-error">
                        <strong>⚠️ Requirements Not Met</strong><br>
                        Please fix the failed requirements before continuing.
                    </div>
                <?php endif; ?>

                <div class="button-group">
                    <div></div>
                    <?php if ($allPassed): ?>
                        <a href="install.php?step=2" class="btn btn-primary">Continue →</a>
                    <?php endif; ?>
                </div>

            <?php elseif ($step === 2): ?>
                <!-- Step 2: Database Configuration -->
                <h2>🗄️ Database Configuration</h2>
                <p>Enter your database connection details</p>

                <?php if (isset($_SESSION['db_error'])): ?>
                    <div class="alert alert-error">
                        <strong>Database Connection Failed</strong><br>
                        <?php echo htmlspecialchars($_SESSION['db_error']); ?>
                    </div>
                    <?php unset($_SESSION['db_error']); ?>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Database Host *</label>
                            <input type="text" name="db_host" value="<?php echo $_SESSION['db_host'] ?? 'localhost'; ?>" required>
                            <div class="help-text">Usually "localhost" or "127.0.0.1"</div>
                        </div>
                        <div class="form-group">
                            <label>Database Port *</label>
                            <input type="text" name="db_port" value="<?php echo $_SESSION['db_port'] ?? '3306'; ?>" required>
                            <div class="help-text">Default MySQL port is 3306</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Database Name *</label>
                        <input type="text" name="db_name" value="<?php echo $_SESSION['db_name'] ?? ''; ?>" required>
                        <div class="help-text">Create this database before continuing</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Database Username *</label>
                            <input type="text" name="db_user" value="<?php echo $_SESSION['db_user'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Database Password</label>
                            <input type="password" name="db_pass" value="<?php echo $_SESSION['db_pass'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="button-group">
                        <a href="install.php?step=1" class="btn btn-secondary">← Back</a>
                        <button type="submit" class="btn btn-primary">Test & Continue →</button>
                    </div>
                </form>

            <?php elseif ($step === 3): ?>
                <!-- Step 3: Application Settings -->
                <h2>⚙️ Application Settings</h2>
                <p>Configure your application settings</p>

                <form method="POST">
                    <div class="form-group">
                        <label>Application Name *</label>
                        <input type="text" name="app_name" value="<?php echo $_SESSION['app_name'] ?? 'PSMS'; ?>" required>
                        <div class="help-text">Your petrol station name</div>
                    </div>

                    <div class="form-group">
                        <label>Application URL *</label>
                        <input type="url" name="app_url" value="<?php echo $_SESSION['app_url'] ?? 'http://' . $_SERVER['HTTP_HOST']; ?>" required>
                        <div class="help-text">Full URL where your application will be accessed</div>
                    </div>

                    <div class="form-group">
                        <label>Environment *</label>
                        <select name="app_env" required>
                            <option value="production" <?php echo ($_SESSION['app_env'] ?? 'production') === 'production' ? 'selected' : ''; ?>>Production</option>
                            <option value="local" <?php echo ($_SESSION['app_env'] ?? '') === 'local' ? 'selected' : ''; ?>>Local/Development</option>
                        </select>
                        <div class="help-text">Use "Production" for live servers</div>
                    </div>

                    <div class="button-group">
                        <a href="install.php?step=2" class="btn btn-secondary">← Back</a>
                        <button type="submit" class="btn btn-primary">Continue →</button>
                    </div>
                </form>

            <?php elseif ($step === 4): ?>
                <!-- Step 4: Admin Account -->
                <h2>👤 Admin Account</h2>
                <p>Create your administrator account</p>

                <form method="POST">
                    <div class="form-group">
                        <label>Admin Name *</label>
                        <input type="text" name="admin_name" value="<?php echo $_SESSION['admin_name'] ?? ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Admin Email *</label>
                        <input type="email" name="admin_email" value="<?php echo $_SESSION['admin_email'] ?? ''; ?>" required>
                        <div class="help-text">You'll use this to login</div>
                    </div>

                    <div class="form-group">
                        <label>Admin Password *</label>
                        <input type="password" name="admin_password" minlength="8" required>
                        <div class="help-text">Minimum 8 characters</div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <input type="password" name="admin_password_confirm" minlength="8" required>
                    </div>

                    <div class="button-group">
                        <a href="install.php?step=3" class="btn btn-secondary">← Back</a>
                        <button type="submit" class="btn btn-primary">Continue →</button>
                    </div>
                </form>

            <?php elseif ($step === 5): ?>
                <!-- Step 5: Installation -->
                <h2>⚡ Installing...</h2>
                <p>Please wait while we set up your application</p>

                <?php if (isset($_SESSION['install_error'])): ?>
                    <div class="alert alert-error">
                        <strong>Installation Failed</strong><br>
                        <?php echo htmlspecialchars($_SESSION['install_error']); ?>
                    </div>
                    <div class="button-group">
                        <a href="install.php?step=4" class="btn btn-secondary">← Back</a>
                        <button onclick="location.reload()" class="btn btn-primary">Try Again</button>
                    </div>
                <?php else: ?>
                    <div class="loading">
                        <div class="spinner"></div>
                        <p>Installing dependencies and configuring database...</p>
                        <p style="font-size: 0.9em; color: #666;">This may take a few minutes</p>
                    </div>

                    <script>
                        // Auto-submit to trigger installation
                        setTimeout(function() {
                            var form = document.createElement('form');
                            form.method = 'POST';
                            form.action = 'install.php?step=5';
                            document.body.appendChild(form);
                            form.submit();
                        }, 1000);
                    </script>
                <?php endif; ?>

            <?php elseif ($step === 6): ?>
                <!-- Step 6: Complete -->
                <div class="success-icon">✅</div>
                <h2 style="text-align: center;">Installation Complete!</h2>
                <p style="text-align: center;">Your PSMS Laravel application is ready to use</p>

                <div class="alert alert-success">
                    <strong>🎉 Success!</strong><br>
                    Your application has been installed successfully.
                </div>

                <div class="alert alert-info">
                    <strong>📝 Login Credentials:</strong><br>
                    Email: <code><?php echo htmlspecialchars($_SESSION['admin_email'] ?? 'admin@psms.com'); ?></code><br>
                    Password: <code>[Your chosen password]</code>
                </div>

                <div class="alert alert-info">
                    <strong>🔒 Security Reminder:</strong><br>
                    • Delete or rename <code>install.php</code> file<br>
                    • Change default passwords<br>
                    • Configure SSL certificate<br>
                    • Set up regular backups
                </div>

                <div class="button-group" style="justify-content: center;">
                    <a href="/" class="btn btn-primary" style="font-size: 1.1em; padding: 18px 40px;">
                        Go to Application →
                    </a>
                </div>

                <?php
                // Clear session
                session_destroy();
                ?>
            <?php endif; ?>
        </div>

        <div style="text-align: center; color: white; margin-top: 30px; opacity: 0.8;">
            <p>PSMS Laravel v1.0.0 | © 2025 EVXO Technologies</p>
        </div>
    </div>
</body>
</html>
