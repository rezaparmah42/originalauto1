$ErrorActionPreference = 'Stop'
Write-Host '--- MYSQL CLIENT ---'
& 'C:\xampp\mysql\bin\mysql.exe' -uroot -e "SHOW DATABASES LIKE 'original_east'; SELECT 'mysql_client_ok' AS status;"
Write-Host "MYSQL_CLIENT_EXIT:$LASTEXITCODE"
Write-Host '--- PDO ---'
& 'C:\xampp\php\php.exe' -d display_errors=1 -r "try { \$pdo = new PDO('mysql:host=localhost;dbname=original_east;charset=utf8mb4', 'root', ''); echo 'PDO_OK\\n'; \$stmt = \$pdo->query('SELECT DATABASE() AS dbname'); \$row = \$stmt->fetch(PDO::FETCH_ASSOC); echo 'DB=' . \$row['dbname'] . '\\n'; } catch (Throwable \$e) { echo 'PDO_FAIL\\n'; echo \$e->getMessage() . '\\n'; exit(1); }"
Write-Host "PDO_EXIT:$LASTEXITCODE"
