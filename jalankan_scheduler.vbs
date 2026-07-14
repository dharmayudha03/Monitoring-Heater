Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "cmd.exe /c php artisan schedule:work", 0, false
