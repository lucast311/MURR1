@ECHO OFF
IF ERRORLEVEL 2 GOTO SLACKER
REM ************** REQUIRED ***************
REM This just is a script that when executed will run tests for Story 14a
php phpunit-5.7.23.phar tests/AppBundle/Controller/EduMatCreationControllerTest.php
GOTO END
:SLACKER
notepad %0
:END