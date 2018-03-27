REM Open Debug Chrome
:PRECHROME
	tasklist /FI "IMAGENAME eq chrome.exe" 2>NUL | find /I /N "chrome.exe">NUL
	IF "%ERRORLEVEL%"=="0" GOTO RELAUNCHQ

	GOTO CDebug

:RELAUNCHQ
	REM echo (new ActiveXObject("WScript.Shell")).AppActivate("AB_DEVENV_SCRIPT"); > focus.js
	REM cscript //nologo focus.js
	REM del focus.js
	
	CHOICE /C:YN /N /T 20 /D Y /M "Close Chrome and re-launch in debug mode (Y/N)?"
	IF ERRORLEVEL 2 GOTO CDError
	IF ERRORLEVEL 1 GOTO CRelaunch

:CDError
	ECHO !!Warning: Chrome [debug mode] wasn't opened!!
	GOTO POSTCHROME

:CRelaunch
	TASKKILL /f /im chrome.exe
	GOTO CDebug
	
:CDebug
	START "" "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222
	GOTO POSTCHROME
	
:POSTCHROME