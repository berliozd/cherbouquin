@ECHO off

REM Assume php.exe is executable, and that zf.php will reside in the
REM same file as this one
SET PHP_BIN=e:\dev\wamp2.0\bin\php\php5.3.0\php.exe
SET PHP_DIR=%~dp0
GOTO RUN

:RUN
SET GIT_DEPLOY_SCRIPT=%PHP_DIR%\GitDeploy.php
"%PHP_BIN%" -d safe_mode=Off -f "%GIT_DEPLOY_SCRIPT%" -- %*
pause