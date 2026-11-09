@echo off
setlocal enabledelayedexpansion
cd /d "%~dp0"

echo ========================================================
echo               Nex-on - Git Push
echo ========================================================
echo.

set msg=%*
if "%msg%"=="" (
    set /p msg="Enter commit message (or press Enter for default): "
)
if "%msg%"=="" (
    set msg=Update Nexon project files
)

echo.
echo [1/3] Staging modified and new files...
git add .

echo [2/3] Committing changes...
git commit -m "%msg%"

echo [3/3] Pushing commits and tags to GitHub (origin/main)...
git push origin main --tags

echo.
if %ERRORLEVEL% EQU 0 (
    echo ========================================================
    echo   [SUCCESS] All changes pushed to GitHub successfully!
    echo ========================================================
) else (
    echo ========================================================
    echo   [NOTICE] Completed with status code %ERRORLEVEL%.
    echo ========================================================
)
echo.
pause

