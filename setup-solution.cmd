@echo off
setlocal enabledelayedexpansion
title [PHAN 1] SETUP VAO XAMPP (UPDATE DB + API)
color 0B

:: 0. LUU VI TRI FILE SCRIPT
set "SCRIPT_LOC=%~dp0"

:: ==========================================
:: 1. CAU HINH
:: ==========================================
set "XAMPP_ROOT=C:\xampp"
set "XAMPP_DOCS=C:\xampp\htdocs"
set "PROJECT_NAME=s-news"
set "DB_NAME=s_news_db"
set "TARGET_DIR=%XAMPP_DOCS%\%PROJECT_NAME%"

echo ========================================================
echo   [PHAN 1] KHOI TAO DU AN (UPDATE DB + API)
echo   Vi tri cai dat: %TARGET_DIR%
echo ========================================================

:: 2. CHUAN BI MOI TRUONG
if not exist "%XAMPP_ROOT%" mkdir "%XAMPP_ROOT%"
if not exist "%XAMPP_DOCS%" mkdir "%XAMPP_DOCS%"

:: 3. TAO THU MUC DU AN
echo [+] Dang tao folder du an...
if not exist "%TARGET_DIR%" mkdir "%TARGET_DIR%"
cd /d "%TARGET_DIR%"

:: --- CAP NHAT: Them folder 'api' vao danh sach ---
for %%F in (classes pages css js images vendor api admin) do (
    if not exist "%%F" mkdir "%%F"
)
mkdir "vendor\bootstrap\css" 2>nul
mkdir "vendor\bootstrap\js" 2>nul
mkdir "vendor\jquery" 2>nul
mkdir "vendor\fontawesome\css" 2>nul

:: ==========================================
:: 4. TAO FILE CODE (CO BAN)
:: ==========================================
echo [+] Dang tao file code co ban (Database, Index)...

:: 4.1. File Database.php
(
echo ^<?php
echo class Database {
echo     private $host = "localhost";
echo     private $db_name = "%DB_NAME%";
echo     private $username = "root";
echo     private $password = "";
echo     public $conn;
echo.
echo     public function getConnection^(^) {
echo         $this-^>conn = null;
echo         try {
echo             $this-^>conn = new PDO^("mysql:host=" . $this-^>host . ";dbname=" . $this-^>db_name, $this-^>username, $this-^>password^);
echo             $this-^>conn-^>exec^("set names utf8mb4"^);
echo             $this-^>conn-^>setAttribute^(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION^);
echo         } catch^(PDOException $exception^) {
echo             echo "Loi ket noi: " . $exception-^>getMessage^(^);
echo         }
echo         return $this-^>conn;
echo     }
echo }
echo ?^>
) > "classes\Database.php"

:: 4.2. File Index.php
(
echo ^<?php
echo include_once 'classes/Database.php';
echo $database = new Database^(^);
echo $db = $database-^>getConnection^(^);
echo ?^>
echo ^<!DOCTYPE html^>
echo ^<html^>
echo ^<head^>
echo     ^<title^>%PROJECT_NAME%^</title^>
echo     ^<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css"^>
echo     ^<link rel="stylesheet" href="vendor/fontawesome/css/all.min.css"^>
echo ^</head^>
echo ^<body class="bg-light p-4"^>
echo     ^<div class="container"^>
echo         ^<div class="alert alert-success mb-4"^>
echo             ^<h4^>^<i class="fa-solid fa-check"^>^</i^> DA CAP NHAT DATABASE!^</h4^>
echo             ^<p^>Da tao folder ^<b^>api/^</b^> va them bang ^<b^>comments^</b^>.^</p^>
echo         ^</div^>
echo         ^<h3 class="text-primary mb-3"^>Tin tuc moi nhat:^</h3^>
echo         ^<div class="row"^>
echo         ^<?php
echo             $stmt = $db-^>prepare^("SELECT * FROM articles"^);
echo             $stmt-^>execute^(^);
echo             while ^($row = $stmt-^>fetch^(PDO::FETCH_ASSOC^)^) {
echo                 echo '^<div class="col-md-4 mb-3"^>^<div class="card h-100 shadow-sm"^>';
echo                 echo '^<div class="card-body"^>';
echo                 echo '^<h5 class="card-title text-primary"^>' . $row['title'] . '^</h5^>';
echo                 echo '^<h6 class="card-subtitle mb-2 text-muted"^>' . $row['category'] . ' ^| View: ' . $row['views'] . '^</h6^>';
echo                 echo '^<p class="card-text"^>' . $row['summary'] . '^</p^>';
echo                 echo '^</div^>^</div^>^</div^>';
echo             }
echo         ?^>
echo         ^</div^>
echo     ^</div^>
echo     ^<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"^>^</script^>
echo ^</body^>
echo ^</html^>
) > "index.php"

:: ==========================================
:: 5. TAO FILE SQL (CAP NHAT)
:: ==========================================
echo [+] Tao file SQL (Articles + Comments)...
(
echo CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
echo USE %DB_NAME%;
echo.
echo -- Bang Bai Viet ^(Update content LONGTEXT^);
echo CREATE TABLE articles ^(
echo     id INT AUTO_INCREMENT PRIMARY KEY,
echo     title VARCHAR^(255^) NOT NULL,
echo     summary TEXT,
echo     content LONGTEXT,
echo     image_url VARCHAR^(255^),
echo     category VARCHAR^(50^) DEFAULT 'Tin tức',
echo     views INT DEFAULT 0,
echo     likes INT DEFAULT 0,
echo     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
echo ^);
echo.
echo -- Bang Binh Luan ^(Moi them^);
echo CREATE TABLE comments ^(
echo     id INT AUTO_INCREMENT PRIMARY KEY,
echo     article_id INT NOT NULL,
echo     username VARCHAR^(50^) NOT NULL,
echo     content TEXT NOT NULL,
echo     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
echo     FOREIGN KEY ^(article_id^) REFERENCES articles^(id^) ON DELETE CASCADE
echo ^);
echo.
echo INSERT INTO articles ^(title, summary, content, category, views, likes^) VALUES 
echo ^('Sinh vien CNTT che tao Robot', 'Nhom sinh vien vua ra mat san pham...', 'Noi dung chi tiet...', 'Công nghệ', 1500, 230^),
echo ^('Lich thi hoc ky moi nhat 2024', 'Phong dao tao vua cong bo lich thi...', 'Noi dung chi tiet...', 'Thông báo', 8900, 1200^),
echo ^('Khai truong cang tin moi', 'Nhieu mon an hap dan gia re...', 'Noi dung chi tiet...', 'Đời sống', 4500, 670^);
) > "setup_database.sql"

:: 6. GOI FILE DOWNLOAD
echo.
echo [!] Chuyen sang tai thu vien...
if exist "%SCRIPT_LOC%download.cmd" (
    call "%SCRIPT_LOC%download.cmd" "%TARGET_DIR%"
) else (
    echo [LOI] Khong tim thay file download.cmd
    pause
)