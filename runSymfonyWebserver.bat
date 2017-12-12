@ECHO OFF
CHOICE /M "Did you read the comments in the batch file?" /C YN 
IF ERRORLEVEL 2 GOTO SLACKER
REM **************************************************
REM Run this batch file in an existing symfony project folder
REM **************************************************

D:\PHP\v7.0\php bin\console server:run
GOTO END
REM *******************BETTER WAY**************************
REM In Visual Studio
REM 1. -> Tools -> External Tools...
REM 2. Click Add Button
REM 3. Title: 	    Symfony Webserver RUN
REM 4. Command:	    D:\PHP\v7.0\php.exe
REM 5. Arguments:   bin\console server:run
REM 6. Initial Dir: $(ProjectDir)
REM 7. [unchecked]  Use Output window 
REM 8. Click Apply Button
REM 9. -> Tools -> Symfony Webserver RUN
:SLACKER
notepad %0
:END