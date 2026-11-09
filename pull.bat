@echo off
setlocal enabledelayedexpansion
cd /d "%~dp0"

:: Ensure Git and PHP are in PATH
set "PATH=C:\Program Files\Git\cmd;C:\DevForge\bin\php\8.5;C:\xampp\php;%PATH%"

echo ========================================================
echo            Nex-on - Git Pull and Sync Manager
echo ========================================================
echo.

echo [1/3] Fetching and pulling latest commits and tags from GitHub (origin/main)...
git pull origin main --tags
if %ERRORLEVEL% NEQ 0 goto :pull_error

git fetch origin --tags

echo.
echo [2/3] Checking latest release version...
set "latestTag=None"
for /f "tokens=*" %%t in ('git describe --tags "--abbrev=0" 2^>nul') do set "latestTag=%%t"
echo       Current release version: !latestTag!

echo.
echo [3/3] Clearing Laravel caches...
if exist "artisan" (
    call php artisan view:clear >nul 2>&1
    call php artisan config:clear >nul 2>&1
    call php artisan cache:clear >nul 2>&1
    echo       Laravel view, config, and application caches cleared.
)

echo.
echo ========================================================
echo   [SUCCESS] Repository updated to version: !latestTag!
echo ========================================================
goto :end

:pull_error
echo.
echo ========================================================
echo   [ERROR] Pull encountered an issue (status code: %ERRORLEVEL%).
echo ========================================================

:end
echo.
pause

