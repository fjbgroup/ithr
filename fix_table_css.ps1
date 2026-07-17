$cssFiles = @(
  "public\assets\css\style.css",
  "public\it-assets\css\style.css",
  "public\assets\css\wtsystem.css",
  "public\assets\css\lms.css"
)

foreach ($f in $cssFiles) {
  if (Test-Path $f) {
    $content = Get-Content -Path $f -Raw
    $content = $content -replace "\.table-responsive table \{\s*width: 100%;\s*min-width: 700px;\s*\}", ".table-responsive table:not(.rb-m-stack) { width: 100%; min-width: 700px; }`n  .table-responsive table.rb-m-stack { width: 100%; min-width: 0 !important; }"
    Set-Content -Path $f -Value $content
  }
}
