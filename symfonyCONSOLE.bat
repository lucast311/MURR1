@ECHO OFF
IF ERRORLEVEL 2 GOTO SLACKER
REM ************** REQUIRED ***************
REM This just is a script that is the equivalent of running "php bin/console PARAMS"
D:\PHP\v7.0\php "%~dp0bin\console" %*
GOTO END
:SLACKER
notepad %0
:END