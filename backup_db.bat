-- Automate daily database backup (example Windows batch script)
-- Save as backup_db.bat and schedule with Windows Task Scheduler
@echo off
set TIMESTAMP=%DATE:~10,4%-%DATE:~4,2%-%DATE:~7,2%_%TIME:~0,2%%TIME:~3,2%%TIME:~6,2%
set BACKUP_DIR=C:\xampp\backups
set DB_USER=root
set DB_PASS=
set DB_NAME=arbiter_db
if not exist %BACKUP_DIR% mkdir %BACKUP_DIR%
C:\xampp\mysql\bin\mysqldump.exe -u%DB_USER% -p%DB_PASS% %DB_NAME% > %BACKUP_DIR%\arbiter_db_%TIMESTAMP%.sql
