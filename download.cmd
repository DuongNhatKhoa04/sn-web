@echo off
:: Chinh kich thuoc cua so cho de nhin
mode con: cols=100 lines=30
title [FILE 2] TOOL TAI THU VIEN (SAFE MODE)
color 0E

echo ========================================================
echo   [PHAN 2] TAI THU VIEN (CHE DO AN TOAN)
echo ========================================================
echo.

:: 1. CAU HINH DUONG DAN
set "TARGET_DIR=D:\sn-web\s-news"

:: 2. KIEM TRA SO BO
:: Them lenh pause o day de neu co loi o tren thi no van dung lai cho ban doc
echo Dang tim thu muc: %TARGET_DIR%...

if not exist "%TARGET_DIR%\vendor" (
    echo.
    echo [LOI NGHIEM TRONG] Khong tim thay thu muc D:\sn-web\s-news\vendor
    echo Ban hay chay File 1 de tao thu muc truoc da!
    echo.
    echo Nhan phim bat ky de thoat...
    pause >nul
    exit
)

:: 3. CHUYEN HUONG VAO O D (Tranh loi duong dan dai)
D:
cd "%TARGET_DIR%\vendor"

echo.
echo [+] Da vao duoc thu muc vendor. Bat dau tai...
echo --------------------------------------------------------

:: TAI JQUERY
echo 1. Dang tai jQuery...
:: Luu y: -k de bo qua loi SSL neu mang truong chan, -L de theo doi chuyen huong
curl -k -L -o "jquery\jquery.min.js" "https://code.jquery.com/jquery-3.7.1.min.js"
echo.

:: TAI BOOTSTRAP
echo 2. Dang tai Bootstrap CSS...
curl -k -L -o "bootstrap\css\bootstrap.min.css" "https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
echo.

echo 3. Dang tai Bootstrap JS...
curl -k -L -o "bootstrap\js\bootstrap.bundle.min.js" "https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
echo.

:: TAI FONTAWESOME
:: Tao thu muc neu chua co (de phong file 1 quen tao)
if not exist "fontawesome\css" mkdir "fontawesome\css"

echo 4. Dang tai FontAwesome...
curl -k -L -o "fontawesome\css\all.min.css" "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"

echo.
echo ========================================================
echo   DA TAI XONG! (HAY KIEM TRA BEN DUOI)
echo ========================================================
echo.
echo Nhan phim bat ky de tat cua so nay...
pause >nul