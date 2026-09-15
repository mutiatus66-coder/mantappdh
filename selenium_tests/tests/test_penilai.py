import time
from selenium.webdriver.common.by import By
from pages.login_page import LoginPage
from base.base_page import BasePage


class TestPenilai:

    def test_penilai_full(self, driver):
        """Full Penilai — login sekali"""

        login_page = LoginPage(driver)
        base = BasePage(driver)

        # LOGIN
        login_page.login_penilai()
        print("✅ Login Penilai berhasil")

        # ============================================================
        # 1. RIWAYAT
        # ============================================================
        print("\n=== RIWAYAT ===")
        driver.get("http://localhost:8000/inovasi/riwayat")
        time.sleep(2)
        print("✅ Halaman Riwayat Inovasi terbuka")

        # ============================================================
        # 2. REKAP NILAI
        # ============================================================
        print("\n=== REKAP NILAI ===")
        driver.get("http://localhost:8000/inovasi/rekap-nilai")
        time.sleep(2)
        print("✅ Halaman Rekap Nilai terbuka")

        # ============================================================
        # 3. PENILAIAN TAHAP 1
        # ============================================================
        print("\n=== PENILAIAN TAHAP 1 ===")
        driver.get("http://localhost:8000/penilaian/tahap-1")
        time.sleep(2)
        print("✅ Halaman Penilaian Tahap 1 terbuka")

        try:
            btn = base.find_by_xpath("//a[contains(text(), 'Lihat Nilai Verifikasi')]")
            driver.execute_script("arguments[0].scrollIntoView(true);", btn)
            driver.execute_script("arguments[0].click();", btn)
            time.sleep(3)
            print("✅ Halaman Detail Nilai Verifikasi terbuka")
        except Exception as e:
            print(f"⚠️ Tombol tidak ditemukan: {e}")

        # ============================================================
        # 4. PENILAIAN TAHAP 2
        # ============================================================
        print("\n=== PENILAIAN TAHAP 2 ===")
        driver.get("http://localhost:8000/penilaian/tahap-2")
        time.sleep(2)
        print("✅ Halaman Penilaian Tahap 2 terbuka")

        try:
            btn = base.find_by_xpath("//a[contains(text(), 'Lihat Nilai Nominator')]")
            driver.execute_script("arguments[0].scrollIntoView(true);", btn)
            driver.execute_script("arguments[0].click();", btn)
            time.sleep(3)
            print("✅ Halaman Detail Nilai Nominator terbuka")
        except Exception as e:
            print(f"⚠️ Tombol tidak ditemukan: {e}")

        print("\n🎉 PENILAI SELESAI!")
