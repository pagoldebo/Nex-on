# PHP & Laravel Upgrade Guide

This document records the step-by-step procedures for upgrading the **PHP runtime** in XAMPP on Windows and upgrading the **Laravel Framework** for the application.

---

## Part 1: Upgrading PHP in XAMPP (Windows) from 8.2.12 to PHP 8.5

When running XAMPP on Windows, PHP runs as an Apache module (`mod_php`). To upgrade PHP safely without breaking Apache or existing configurations, follow these steps:

### 1. Prerequisites & Version Requirements
- **Architecture**: Must be **64-bit (x64)**.
- **Thread Safety**: You **MUST** download the **Thread Safe (TS)** build. The Non-Thread Safe (NTS) build does not provide the `php8apache2_4.dll` module and will cause Apache to fail on startup.
- **Compiler**: Match your Apache compiler version, typically **VS16**.

### 2. Step-by-Step Upgrade Procedure

#### Step 1: Stop Services in XAMPP
1. Open the **XAMPP Control Panel**.
2. Click **Stop** on both **Apache** and **MySQL**.
3. Completely exit the XAMPP Control Panel from the system tray.

#### Step 2: Download PHP 8.5 (Thread Safe)
1. Go to official Windows PHP binaries: [https://windows.php.net/download/](https://windows.php.net/download/).
2. Locate the **PHP 8.5.x** release section.
3. Download the **VS16 x64 Thread Safe** ZIP archive (e.g. `php-8.5.x-Win32-vs16-x64.zip`).

#### Step 3: Backup Current PHP Folder
1. Open Windows File Explorer and navigate to `C:\xampp`.
2. Rename the current `php` directory to create a fallback backup:
   ```text
   C:\xampp\php  -->  C:\xampp\php-8.2.12-backup
   ```

#### Step 4: Extract New PHP 8.5
1. In `C:\xampp`, create a new empty directory named `php` (`C:\xampp\php`).
2. Extract all contents from the downloaded PHP 8.5 ZIP archive directly into `C:\xampp\php`.

#### Step 5: Configure `php.ini`
1. In `C:\xampp\php`, copy `php.ini-development` (or `php.ini-production`) and rename it to `php.ini`.
2. Open `C:\xampp\php\php.ini` in your code editor.
3. Enable the extension directory by uncommenting (removing the leading `;`):
   ```ini
   extension_dir = "ext"
   ```
4. Enable the required extensions for Laravel and MySQL:
   ```ini
   extension=curl
   extension=fileinfo
   extension=gd
   extension=mbstring
   extension=mysqli
   extension=openssl
   extension=pdo_mysql
   extension=pdo_sqlite
   extension=sqlite3
   extension=zip
   ```
5. *(Recommended)* Set realistic memory and upload limits:
   ```ini
   upload_max_filesize = 64M
   post_max_size = 64M
   memory_limit = 512M
   max_execution_time = 300
   ```

#### Step 6: Verify Apache Module Configuration
Open `C:\xampp\apache\conf\extra\httpd-xampp.conf` and confirm the module loading directives:
```apache
LoadFile "C:/xampp/php/php8ts.dll"
LoadFile "C:/xampp/php/libpq.dll"
LoadFile "C:/xampp/php/libsqlite3.dll"
LoadModule php_module "C:/xampp/php/php8apache2_4.dll"
```
Ensure that `php8apache2_4.dll` and `php8ts.dll` exist in `C:\xampp\php`. If `libpq.dll` or `libsqlite3.dll` are not in the new PHP root, copy them from `C:\xampp\php-8.2.12-backup`.

#### Step 7: Restart Apache & Verify
1. Open **XAMPP Control Panel** and click **Start** on **Apache** and **MySQL**.
2. Verify the active PHP version via CLI:
   ```powershell
   C:\xampp\php\php.exe -v
   ```
3. Open your browser and navigate to `http://localhost/dashboard/phpinfo.php` to confirm PHP 8.5 is active in Apache.

#### Step 8: Add PHP to Windows Environment Variables (PATH)
1. Press `Win + S`, type **Environment Variables**, and select **Edit the system environment variables**.
2. Click **Environment Variables...** > select `Path` under **System variables** > click **Edit**.
3. Add `C:\xampp\php` to the list and click **OK**.
4. Reopen PowerShell so the `php` command is globally accessible.

---

## Part 2: Upgrading Laravel Framework from v12.42.0 to Future Versions

Laravel manages dependencies using **Composer**. The current project configuration in `composer.json` specifies:
```json
"require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1"
}
```

---

### Scenario A: Minor & Patch Updates (Within Laravel 12)
Minor and patch releases (e.g. `v12.42.0` &rarr; `v12.43.0` &rarr; `v12.50.0`) are published weekly. They include bug fixes, security patches, and backward-compatible improvements. Because of the caret `^12.0` in `composer.json`, Composer permits all 12.x updates automatically.

#### Steps:
1. **Update only the Laravel framework and its immediate dependencies:**
   ```powershell
   composer update laravel/framework --with-dependencies
   ```
   *(Or update all project packages by running `composer update`)*

2. **Clear and rebuild compiled framework cache:**
   ```powershell
   C:\xampp\php\php.exe artisan optimize:clear
   ```

3. **Check the newly installed framework version:**
   ```powershell
   C:\xampp\php\php.exe artisan --version
   ```

---

### Scenario B: Major Version Upgrade (e.g. Laravel 12 &rarr; Laravel 13)
Major versions are published annually (typically in Q1). They may introduce breaking changes, updated dependencies, and higher minimum PHP version requirements.

#### Step 1: Check Minimum PHP Requirements
Check the minimum PHP requirement for the target major version. If Laravel 13 requires PHP 8.3+, ensure your PHP runtime is updated first.

#### Step 2: Read the Official Upgrade Guide
Always consult the official upgrade documentation:
```text
https://laravel.com/docs/13.x/upgrade
```
Pay close attention to:
- **High Impact Changes**
- Deprecated or renamed methods in Eloquent, Routing, or Service Providers
- Environment or configuration file format changes

#### Step 3: Update `composer.json`
Update the PHP constraint, framework version constraint, and core development packages:
```json
"require": {
    "php": "^8.3",
    "laravel/framework": "^13.0",
    "laravel/tinker": "^2.10"
},
"require-dev": {
    "nunomaduro/collision": "^8.6",
    "phpunit/phpunit": "^11.5"
}
```

#### Step 4: Run Composer Update
Execute Composer with full dependency resolution:
```powershell
composer update --with-all-dependencies
```
> **Tip:** If a third-party package blocks installation, run `composer outdated` to identify which community packages need updated version constraints.

#### Step 5: Run Database Migrations & Rebuild Cache
```powershell
C:\xampp\php\php.exe artisan migrate
C:\xampp\php\php.exe artisan optimize:clear
```

#### Step 6: Run Automated Tests
Verify that all storefront routes, PWA functions, and admin panel modules pass without issues:
```powershell
C:\xampp\php\php.exe artisan test
```

---

### Automated Upgrade Tools
- **[Laravel Shift](https://laravelshift.com/)**: The automated upgrade service for Laravel projects. It scans your repository, applies code modifications, updates config files, and creates a ready-to-merge Pull Request.
- **[Rector PHP](https://getrector.com/)**: Useful for automated codebase refactoring and upgrading syntax to modern PHP features.

