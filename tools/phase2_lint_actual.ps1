$root = 'C:\xampp\htdocs\originalshargh'
$php = 'C:\xampp\php\php.exe'
$files = Get-ChildItem -Path $root -Recurse -File -Filter '*.php' | Sort-Object FullName
$lines = @(
    "PHASE2_PHP_LINT - $(Get-Date -Format o)",
    "PHP_FILES_TOTAL=$($files.Count)"
)
$failCount = 0
foreach ($f in $files) {
    $output = & $php -l $f.FullName 2>&1
    $text = ($output | Out-String).TrimEnd()
    if ($LASTEXITCODE -ne 0 -or ($text -notmatch 'No syntax errors detected')) {
        $failCount++
        $lines += ''
        $lines += "LINT_FAIL: $($f.FullName)"
        $lines += $text
    }
}
$lines += ''
$lines += "PHP_LINT_ERRORS=$failCount"
$lines | Set-Content -Path "$root\phase2_php_lint.txt" -Encoding UTF8
Write-Host "DONE"
