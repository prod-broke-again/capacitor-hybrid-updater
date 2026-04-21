#!/usr/bin/env bash
# Run from apps/laravel-fixture (Git Bash / WSL). Start: php artisan serve --port=8080
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:8080}"
PREFIX="${HYBRID_UPDATER_ROUTE_PREFIX:-api/updater}"
WEB_TOKEN="${WEB_TOKEN:-web-dev-token}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
FIXTURE_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

echo "GET /check"
curl -sS "${BASE_URL}/${PREFIX}/check?platform=android&channel=stable" | jq .

echo "POST web-bundles (needs fixtures/bundle-1.0.1.zip — run scripts/create-fixtures.ps1 once on Windows)"
curl -sS -X POST "${BASE_URL}/${PREFIX}/web-bundles" \
  -H "X-Web-Bundle-Upload-Token: ${WEB_TOKEN}" \
  -F "version=1.0.1" \
  -F "channel=stable" \
  -F "zip=@${FIXTURE_ROOT}/fixtures/bundle-1.0.1.zip"

echo
echo "Optional: set APK_PATH to a real .apk (built artifact) for POST android/releases."
if [[ -n "${APK_PATH:-}" ]]; then
  curl -sS -H "Accept: application/json" -X POST "${BASE_URL}/${PREFIX}/android/releases" \
    -H "X-Android-Upload-Token: ${ANDROID_TOKEN:-android-dev-token}" \
    -F "version=1.0.1" \
    -F "build_number=2" \
    -F "channel=stable" \
    -F "apk=@${APK_PATH}"
  echo
fi

echo "Done"
