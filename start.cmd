@echo off
title Proxy by Frago9876543210
:proxy_start
bin\php\php.exe src\proxy\start.php
timeout 5
goto proxy_start
pause
