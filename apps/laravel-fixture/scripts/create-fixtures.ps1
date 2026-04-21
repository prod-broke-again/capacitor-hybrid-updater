# Creates fixtures/bundle-1.0.1.zip (root index.html) for smoke uploads.
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
$fixtures = Join-Path $root "fixtures"
New-Item -ItemType Directory -Force -Path $fixtures | Out-Null
$tmp = Join-Path ([System.IO.Path]::GetTempPath()) ("hybrid-zip-" + [Guid]::NewGuid().ToString("n"))
New-Item -ItemType Directory -Force -Path $tmp | Out-Null
try {
  Set-Content -Path (Join-Path $tmp "index.html") -Value "<!doctype html><html><body>fixture</body></html>"
  $zip = Join-Path $fixtures "bundle-1.0.1.zip"
  if (Test-Path $zip) { Remove-Item $zip -Force }
  Compress-Archive -Path (Join-Path $tmp "index.html") -DestinationPath $zip
  Write-Host "Wrote $zip"
} finally {
  Remove-Item -Recurse -Force $tmp -ErrorAction SilentlyContinue
}
