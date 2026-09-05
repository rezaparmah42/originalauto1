$out = 'C:\xampp\htdocs\originalshargh\db_verify_output.txt'
$results = @()
$results += '===PROCESS==='
$results += (Get-Process mysqld -ErrorAction SilentlyContinue | Select-Object Id,ProcessName,Path | Format-Table | Out-String)
$results += '===PORT==='
$results += (netstat -ano | Select-String ':3306\s' | Out-String)
$results += '===MYSQL CLI==='
$results += (& 'C:\xampp\mysql\bin\mysql.exe' -u root -e 'SELECT USER() AS user, VERSION() AS version;' 2>&1 | Out-String)
$results += '===PHP PDO==='
$results += (& 'C:\xampp\php\php.exe' -d display_errors=1 -r 'require "C:/xampp/htdocs/originalshargh/config/config.php"; require "C:/xampp/htdocs/originalshargh/app/Core/Database.php"; use App\\Core\\Database; try { $db = Database::connect(); echo "CONNECTED\n"; $row = $db->query("SELECT DATABASE() AS db, USER() AS user")->fetch(); echo "DB=" + $row["db"] + "\n"; echo "USER=" + $row["user"] + "\n"; } catch { echo "ERROR: " + $_.Exception.Message; exit 1 }' 2>&1 | Out-String)
$results += '===SHOW DATABASE==='
$results += (& 'C:\xampp\mysql\bin\mysql.exe' -u root -e 'SHOW DATABASES LIKE "original_east"; SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = "original_east" AND TABLE_NAME IN ("users","customers","articles","products","services","invoices","payments","repairs","workshop_tasks","notifications"); SELECT COUNT(*) AS count_tables FROM information_schema.TABLES WHERE TABLE_SCHEMA = "original_east"; USE original_east; SHOW TABLES;' 2>&1 | Out-String)
$results | Out-File -FilePath $out -Encoding utf8
