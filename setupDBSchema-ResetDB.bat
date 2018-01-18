@ECHO OFF
DEL /Q .\app\sqlite.db
.\symfonyCONSOLE.bat doctrine:schema:create