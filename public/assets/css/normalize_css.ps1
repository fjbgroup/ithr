$path = "c:\xampp\htdocs\nexus\public\assets\css\wtsystem.css"
$lines = Get-Content $path

$exceptions = "title|stat-value|header|modal-title|h1|h2|h3|summary-value|history-date|history-title"

for ($i=0; $i -lt $lines.Length; $i++) {
    if ($lines[$i] -notmatch $exceptions) {
        # Replace font-size
        $lines[$i] = $lines[$i] -replace "font-size\s*:\s*[0-9.]+px\s*(!important)?\s*;", ""
        # Replace font-weight
        $lines[$i] = $lines[$i] -replace "font-weight\s*:\s*(500|600|700|800|900|bold)\s*(!important)?\s*;", ""
    }
}
Set-Content -Path $path -Value ($lines -join "
")

$path2 = "c:\xampp\htdocs\nexus\public\assets\css\wt-inventory.css"
$lines2 = Get-Content $path2
for ($i=0; $i -lt $lines2.Length; $i++) {
    if ($lines2[$i] -notmatch $exceptions) {
        # Replace font-size
        $lines2[$i] = $lines2[$i] -replace "font-size\s*:\s*[0-9.]+px\s*(!important)?\s*;", ""
        # Replace font-weight
        $lines2[$i] = $lines2[$i] -replace "font-weight\s*:\s*(500|600|700|800|900|bold)\s*(!important)?\s*;", ""
    }
}
Set-Content -Path $path2 -Value ($lines2 -join "
")
