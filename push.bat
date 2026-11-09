@echo off
setlocal enabledelayedexpansion
cd /d "%~dp0"

:: Ensure Git and PHP are in PATH
set "PATH=C:\Program Files\Git\cmd;C:\DevForge\bin\php\8.5;C:\xampp\php;%PATH%"

echo ========================================================
echo            Nex-on - Git Push and Release Manager
echo ========================================================
echo.

:: 1. Commit message
set "msg=%*"
if "!msg!"=="" (
    set /p "msg=Enter commit message (or press Enter for default): "
)
if "!msg!"=="" (
    set "msg=Update Nexon project files"
)

:: 2. Optional Release Tag
echo.
set /p "tag=Enter new release tag (e.g. v0.06, or press Enter to skip): "

:: Automatically ensure 'v' prefix format for future consistency
if not "!tag!"=="" (
    set "firstChar=!tag:~0,1!"
    if /i not "!firstChar!"=="v" (
        set "tag=v!tag!"
    )
)

echo.
echo [1/4] Staging modified and new files...
git add .

echo [2/4] Committing changes...
git commit -m "!msg!"

:: If a tag was provided, create the tag
if not "!tag!"=="" (
    echo [3/4] Creating release tag !tag!...
    git tag -a "!tag!" -m "Release !tag!: !msg!"
) else (
    echo [3/4] Skipping new tag creation...
)

echo [4/4] Pushing commits and tags to GitHub (origin/main)...
git push origin main --tags

echo.
if %ERRORLEVEL% EQU 0 (
    echo ========================================================
    echo   [SUCCESS] Pushed to GitHub successfully!
    if not "!tag!"=="" (
        echo   [RELEASE] Release tag: !tag! published to GitHub.
    )
    echo ========================================================
) else (
    echo ========================================================
    echo   [NOTICE] Completed with status code %ERRORLEVEL%.
    echo ========================================================
)
echo.
pause

