@echo off
cd /d "%~dp0"

echo ========================================================
echo               Nex-on - Git Pull
echo ========================================================
echo.

echo Pulling latest changes from GitHub (origin/main)...
echo.
git pull origin main

echo.
if %ERRORLEVEL% EQU 0 (
    echo ========================================================
    echo   [SUCCESS] Local repository is up to date!
    echo ========================================================
) else (
    echo ========================================================
    echo   [ERROR] Pull encountered an issue (code %ERRORLEVEL%).
    echo ========================================================
)
echo.
pause
