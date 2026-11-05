@echo off
set msg=%*
if "%msg%"=="" (
    set /p msg="Enter commit message (or press Enter for default): "
)
if "%msg%"=="" (
    set msg=Update Nexon project files
)

echo.
echo [1/3] Staging changes...
git add .

echo [2/3] Committing changes...
git commit -m "%msg%"

echo [3/3] Pushing to GitHub...
git push

echo.
echo Sync completed successfully!
pause
