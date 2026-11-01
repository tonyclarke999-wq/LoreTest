@echo off
echo Adding Windows Firewall rule for LoreTest (ports 5001, 5002, and 5003)...
netsh advfirewall firewall add rule name="LoreTest Environments (5001-5003)" dir=in action=allow protocol=TCP localport=5001-5003 profile=any
if %ERRORLEVEL% EQU 0 (
    echo SUCCESS: Firewall rule added successfully!
) else (
    echo ERROR: Failed to add rule. Please make sure this script is run as Administrator.
)
pause
