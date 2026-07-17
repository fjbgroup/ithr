$files = @(
  "resources\views\layouts\app.blade.php",
  "resources\views\it\layouts\app.blade.php",
  "resources\views\wt\layouts\admin.blade.php",
  "resources\views\lms\layout\app.blade.php",
  "resources\views\layouts\guest.blade.php",
  "resources\views\auth\login.blade.php",
  "resources\views\wt\auth\login.blade.php",
  "resources\views\it\layouts\auth.blade.php"
)

foreach ($file in $files) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw

        # 1. Update HTML onclick to pass event
        $content = $content -replace 'onclick="toggleTheme\(\)"', 'onclick="toggleTheme(event)"'
        $content = $content -replace 'onclick="pubToggleTheme\(\)"', 'onclick="pubToggleTheme(event)"'

        # 2. Update WT listener
        $content = $content -replace 'addEventListener\(''click'', function\(\) \{ toggleTheme\(\); \}\)', 'addEventListener(''click'', toggleTheme)'

        Set-Content $file -Value $content
    }
}
