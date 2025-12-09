@echo off
setlocal enabledelayedexpansion

:: ==========================================
:: 1. CAU HINH DU AN
:: ==========================================
set "DRIVE=D:\sn-web"
set "PROJECT_NAME=s-news"
set "TARGET_DIR=%DRIVE%\%PROJECT_NAME%"
set "DB_NAME=s_news_db"

echo.
echo ========================================================
echo   [AUTO SETUP] KHOI TAO DU AN: %PROJECT_NAME%
echo   VI TRI CAI DAT: %TARGET_DIR%
echo ========================================================
echo.

:: 2. TAO CAY THU MUC (BAO GOM VENDOR OFFLINE)
echo [+] Buoc 1/3: Tao cau truc thu muc...

if not exist "%TARGET_DIR%" mkdir "%TARGET_DIR%"
if not exist "%TARGET_DIR%\classes" mkdir "%TARGET_DIR%\classes"
if not exist "%TARGET_DIR%\pages" mkdir "%TARGET_DIR%\pages"
if not exist "%TARGET_DIR%\css" mkdir "%TARGET_DIR%\css"
if not exist "%TARGET_DIR%\js" mkdir "%TARGET_DIR%\js"
if not exist "%TARGET_DIR%\images" mkdir "%TARGET_DIR%\images"

:: Tao folder cho thu vien Offline
if not exist "%TARGET_DIR%\vendor" mkdir "%TARGET_DIR%\vendor"
if not exist "%TARGET_DIR%\vendor\bootstrap\css" mkdir "%TARGET_DIR%\vendor\bootstrap\css"
if not exist "%TARGET_DIR%\vendor\bootstrap\js" mkdir "%TARGET_DIR%\vendor\bootstrap\js"
if not exist "%TARGET_DIR%\vendor\jquery" mkdir "%TARGET_DIR%\vendor\jquery"

:: 3. TAO FILE CODE RONG (PLACEHOLDERS)
echo [+] Buoc 2/3: Tao file ma nguon chuan OOP...

type nul > "%TARGET_DIR%\index.php"
type nul > "%TARGET_DIR%\classes\Database.php"
type nul > "%TARGET_DIR%\classes\BasePage.php"
type nul > "%TARGET_DIR%\pages\search.php"
type nul > "%TARGET_DIR%\pages\category.php"
type nul > "%TARGET_DIR%\pages\detail.php"
type nul > "%TARGET_DIR%\pages\contact.php"
type nul > "%TARGET_DIR%\pages\about.php"
type nul > "%TARGET_DIR%\css\style.css"
type nul > "%TARGET_DIR%\js\main.js"

:: 4. TAO FILE SQL TU DONG
echo [+] Buoc 3/3: Tao file SQL Database (%DB_NAME%)...

(
echo CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
echo USE %DB_NAME%;
echo.
echo CREATE TABLE articles ^(
echo     id INT AUTO_INCREMENT PRIMARY KEY,
echo     title VARCHAR^(255^) NOT NULL,
echo     summary TEXT,
echo     content TEXT,
echo     image_url VARCHAR^(255^),
echo     category VARCHAR^(50^) DEFAULT 'Thời sự',
echo     views INT DEFAULT 0,
echo     likes INT DEFAULT 0,
echo     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
echo ^);
echo.
echo INSERT INTO articles ^(title, summary, content, image_url, category, views, likes^) VALUES 
echo ^('Sinh viên CNTT chế tạo robot hỗ trợ người già', 'Nhóm sinh viên trường ĐH vừa ra mắt sản phẩm...', 'Nội dung chi tiết...', 'https://placehold.co/600x400', 'Công nghệ', 1500, 230^),
echo ^('Lịch thi học kỳ mới nhất năm 2024', 'Phòng đào tạo vừa công bố lịch thi chính thức...', 'Nội dung chi tiết...', 'https://placehold.co/600x400', 'Thời sự', 8900, 1200^),
echo ^('Căng tin trường thay đổi thực đơn giá rẻ', 'Nhiều món ăn mới hấp dẫn sinh viên...', 'Nội dung chi tiết...', 'https://placehold.co/600x400', 'Đời sống', 4500, 670^);
) > "%TARGET_DIR%\setup_database.sql"

echo.
echo ========================================================
echo   CAI DAT THANH CONG!
echo ========================================================
echo   Tiep theo, ban hay lam 3 viec sau:
echo   1. Tai Bootstrap va jQuery ve thu muc: %TARGET_DIR%\vendor
echo   2. Tao Alias 's-news' trong WAMP tro den: D:/s-news/
echo   3. Import file 'setup_database.sql' vao phpMyAdmin.
echo.
pause
explorer "%TARGET_DIR%"