@ECHO off

REM Assume php.exe is executable, and that zf.php will reside in the
REM same file as this one
SET PHP_BIN=e:\dev\wamp2.0\bin\php\php5.3.0\php.exe
SET PHP_DIR=%~dp0
GOTO RUN

:RUN
SET DOCTRINE_SCRIPT=%PHP_DIR%\doctrine.php
"%PHP_BIN%" -d safe_mode=Off -f "%DOCTRINE_SCRIPT%" -- %*