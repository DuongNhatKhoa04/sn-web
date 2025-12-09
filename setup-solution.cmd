@echo off
setlocal enabledelayedexpansion
title [PHAN 1] SETUP VAO XAMPP (Force Mode)
color 0B

:: 0. LUU VI TRI FILE SCRIPT
set "SCRIPT_LOC=%~dp0"

:: ==========================================
:: 1. CAU HINH
:: ==========================================
:: Chung ta se bo qua viec kiem tra, cu mac dinh la co XAMPP
set "XAMPP_ROOT=C:\xampp"
set "XAMPP_DOCS=C:\xampp\htdocs"
set "PROJECT_NAME=s-news"
set "DB_NAME=s_news_db"
set "TARGET_DIR=%XAMPP_DOCS%\%PROJECT_NAME%"

echo ========================================================
echo   [PHAN 1] KHOI TAO DU AN (FORCE MODE)
echo   Vi tri cai dat: %TARGET_DIR%
echo ========================================================

:: 2. TU DONG SUA LOI THIEU THU MUC
echo [+] Dang chuan bi moi truong...

:: Neu chua co folder xampp thi tu tao luon (de phong)
if not exist "%XAMPP_ROOT%" mkdir "%XAMPP_ROOT%"

:: Neu co xampp ma thieu htdocs thi tu tao htdocs luon
if not exist "%XAMPP_DOCS%" (
    echo [!] Phat hien thieu thu muc htdocs, dang tu tao...
    mkdir "%XAMPP_DOCS%"
)

:: 3. TAO THU MUC DU AN
echo [+] Dang tao folder du an...
if not exist "%TARGET_DIR%" mkdir "%TARGET_DIR%"

cd /d "%TARGET_DIR%"

for %%F in (classes pages css js images vendor) do (
    if not exist "%%F" mkdir "%%F"
)

mkdir "vendor\bootstrap\css" 2>nul
mkdir "vendor\bootstrap\js" 2>nul
mkdir "vendor\jquery" 2>nul
mkdir "vendor\fontawesome\css" 2>nul

:: 4. TAO FILE SQL
echo [+] Tao file SQL...
(
echo CREATE DATABASE IF NOT EXISTS %DB_NAME%;
echo USE %DB_NAME%;
echo CREATE TABLE articles (id INT PRIMARY KEY AUTO_INCREMENT, title VARCHAR(255));
echo INSERT INTO articles (title) VALUES ('Chao mung den voi S-News tren XAMPP');
) > "setup_database.sql"

:: 5. TAO INDEX.PHP
echo [+] Tao file Index.php...
(
echo ^<!DOCTYPE html^>
echo ^<html^>
echo ^<head^>
echo     ^<title^>%PROJECT_NAME%^</title^>
echo     ^<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css"^>
echo ^</head^>
echo ^<body class="p-5"^>
echo     ^<div class="container"^>
echo         ^<div class="alert alert-success"^>
echo             ^<h1^>CHAY THANH CONG TREN LOCALHOST!^</h1^>
echo             ^<p^>Web dang chay tai: %TARGET_DIR%^</p^>
echo         ^</div^>
echo         ^<button class="btn btn-primary"^>Nut Bam Bootstrap^</button^>
echo     ^</div^>
echo     ^<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"^>^</script^>
echo ^</body^>
echo ^</html^>
) > "index.php"

:: 6. GOI FILE DOWNLOAD
echo.
echo [!] Chuyen sang tai thu vien...

:: Goi file download bang duong dan tuyet doi
if exist "%SCRIPT_LOC%download.cmd" (
    call "%SCRIPT_LOC%download.cmd" "%TARGET_DIR%"
) else (
    echo [LOI] Khong tim thay file 'download.cmd' tai:
    echo %SCRIPT_LOC%
    echo Vui long kiem tra lai xem file download co nam cung cho khong.
    pause
)