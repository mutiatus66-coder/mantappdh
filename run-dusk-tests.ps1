# run-dusk-tests.ps1
# Jalankan tiap METHOD test Dusk secara terpisah (bukan cuma per class)
# untuk menghindari isu isolasi browser antar-method dalam satu class.

$tests = @(
    "LoginTest::test_user_can_login",

    "RoleAccessTest::test_penilai_cannot_access_admin_page",
    "RoleAccessTest::test_peserta_cannot_access_admin_page",

    "DashboardAccessTest::test_admin_can_access_admin_panel",
    "DashboardAccessTest::test_penilai_cannot_access_admin_panel",
    "DashboardAccessTest::test_peserta_cannot_access_admin_panel",
    "DashboardAccessTest::test_penilai_can_access_penilai_index",
    "DashboardAccessTest::test_peserta_can_access_riwayat_usulan",

    "AdminCrudPagesTest::test_admin_can_view_event_index",
    "AdminCrudPagesTest::test_admin_can_view_sub_event_index",
    "AdminCrudPagesTest::test_admin_can_view_bidang_index",
    "AdminCrudPagesTest::test_admin_can_view_user_index",

    "PenilaianTest::test_penilai_can_access_penilaian_tahap1_index",
    "PenilaianTest::test_penilai_can_access_penilaian_tahap2_index",
    "PenilaianTest::test_peserta_cannot_access_penilaian_tahap1",

    "LoginAsImpersonationTest::test_admin_can_login_as_another_user_and_return"
)

$failed = @()

foreach ($t in $tests) {
    Write-Host "`n=== $t ===" -ForegroundColor Cyan
    php artisan dusk --filter="$t"

    if ($LASTEXITCODE -ne 0) {
        $failed += $t
    }
}

Write-Host "`n`n=== RINGKASAN ===" -ForegroundColor Yellow
Write-Host "Total: $($tests.Count) | Pass: $($tests.Count - $failed.Count) | Fail: $($failed.Count)"

if ($failed.Count -eq 0) {
    Write-Host "Semua test PASS." -ForegroundColor Green
} else {
    Write-Host "Test yang gagal:" -ForegroundColor Red
    $failed | ForEach-Object { Write-Host " - $_" -ForegroundColor Red }
}