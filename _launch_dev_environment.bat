@ECHO OFF
TITLE AB_DEVENV_SCRIPT

ECHO   ##################################
ECHO "#  \  | |  |_ \ _ \_ _|  __|   \   #"
ECHO "# |\/ | |  |  /   /  |  (     _ \  #"
ECHO "#_|  _|\__/_|_\_|_\___|\___|_/  _\ #"
ECHO   ##################################
ECHO.
ECHO   ______________====______________
ECHO =*_______________AB_______________*=
ECHO   Austins Dev Environment Launcher
ECHO   *^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^*

:GITBASH
	START .\_0pen_Git_Bash_Here.lnk

:MySQLMSG
	ECHO.
	ECHO   _______________________!!!!_______________________
	ECHO =_________________________AB_________________________=
	ECHO  git pull, boot the MySQL server VM and press any key
	PAUSE

:STARTWEBSRV
	REM Start Webserver
	ECHO.
	ECHO   ______====______
	ECHO =________AB________=
	ECHO  Starting Webserver
	START "" .\runSymfonyWebserver.bat

:OPENSHAREPOINT
	REM Open Sharepoint
	ECHO.
	ECHO   ______====______
	ECHO =________AB________=
	ECHO  Opening Sharepoint
	START "" .\_MURR_Sharepoint.lnk
	START "" .\_MURR_Sharepoint_IE.website
	

:PRECHROME
	REM Open Debug Chrome
	ECHO.
	ECHO   ______====______
	ECHO =________AB________=
	ECHO      Chrome DBG
	
	tasklist /FI "IMAGENAME eq chrome.exe" 2>NUL | find /I /N "chrome.exe">NUL
	IF "%ERRORLEVEL%"=="0" GOTO RELAUNCHQ

	GOTO CDebug

:RELAUNCHQ
	TIMEOUT /T 5
	ECHO (new ActiveXObject("WScript.Shell")).AppActivate("AB_DEVENV_SCRIPT"); > focus.js
	CSCRIPT //nologo focus.js
	DEL focus.js
	
	ECHO.
	CHOICE /C:YN /N /T 20 /D Y /M " Close Chrome and re-launch in debug mode ?[Y/N]"
	IF ERRORLEVEL 2 GOTO CDError
	IF ERRORLEVEL 1 GOTO CRelaunch

:CDError
	ECHO.
	ECHO   _____________________!!!!_____________________
	ECHO !=______________________AB______________________=!
	ECHO   !!Warning: Chrome [debug mode] wasn't opened!!
	GOTO POSTCHROME

:CRelaunch
	ECHO.
	ECHO   ____====____
	ECHO =______AB______=
	ECHO  Killing Chrome
	TASKKILL /f /im chrome.exe
	GOTO CDebug
	
:CDebug
	ECHO.
	ECHO   ______====______
	ECHO =________AB________=
	ECHO  Opening Chrome DBG
	START "" "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222
	GOTO POSTCHROME
	
:POSTCHROME



:OPENVSPROJ
	REM Open DevEnv
	ECHO.
	ECHO   ______====______
	ECHO =________AB________=
	ECHO  Opening VS project
	start .\prj4-murr.sln


:ENDOFSCRIPT
TIMEOUT /T 5
ECHO (new ActiveXObject("WScript.Shell")).AppActivate("AB_DEVENV_SCRIPT"); > focus.js
CSCRIPT //nologo focus.js
DEL focus.js

ECHO.
ECHO   _____________====_____________
ECHO =*______________AB______________*=
ECHO  \  Script execution finished!  /
ECHO   *^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^*
ECHO.
PAUSE
EXIT