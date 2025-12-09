@echo off
mode con: cols=100 lines=30
title [PHAN 2] TAI THU VIEN (UPDATE FIX ICON)
color 0E

:: Nhan duong dan tu file setup-solution truyen sang
set "TARGET_DIR=%~1"

:: Neu lo tay chay rieng file nay thi mac dinh vao XAMPP
if "%TARGET_DIR%"=="" set "TARGET_DIR=C:\xampp\htdocs\s-news"

echo ========================================================
echo   [PHAN 2] TAI THU VIEN VAO XAMPP (FIX ICON)
echo   Target: %TARGET_DIR%\vendor
echo ========================================================

if not exist "%TARGET_DIR%\vendor" (
    echo [LOI] Khong tim thay thu muc du an!
    echo Hay chay file setup-solution.cmd truoc.
    pause
    exit
)

:: Di chuyen vao thu muc vendor de tai
cd /d "%TARGET_DIR%\vendor"

echo 1. Downloading jQuery...
curl -k -L -o "jquery\jquery.min.js" "https://code.jquery.com/jquery-3.7.1.min.js"
echo.

echo 2. Downloading Bootstrap CSS...
curl -k -L -o "bootstrap\css\bootstrap.min.css" "https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
echo.

echo 3. Downloading Bootstrap JS...
curl -k -L -o "bootstrap\js\bootstrap.bundle.min.js" "https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
echo.

echo 4. Downloading FontAwesome CSS...
curl -k -L -o "fontawesome\css\all.min.css" "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
echo.

:: --- PHAN QUAN TRONG MOI THEM: TAI BO FONT GOC ---
echo 5. Downloading FontAwesome WEBFONTS (Fix loi Icon)...
:: Tao thu muc webfonts nam ngang hang voi css
if not exist "fontawesome\webfonts" mkdir "fontawesome\webfonts"

echo    - Downloading Solid Icons...
curl -k -L -o "fontawesome\webfonts\fa-solid-900.woff2" "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-solid-900.woff2"

echo    - Downloading Brands Icons (Facebook, Google)...
curl -k -L -o "fontawesome\webfonts\fa-brands-400.woff2" "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-brands-400.woff2"

echo    - Downloading Regular Icons...
curl -k -L -o "fontawesome\webfonts\fa-regular-400.woff2" "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-regular-400.woff2"
echo.

echo ========================================================
echo   DA TAI XONG ICON! WEB DA SAN SANG.
echo ========================================================
echo   Hay mo trinh duyet va vao: http://localhost/s-news
pause
start http://localhost/s-news