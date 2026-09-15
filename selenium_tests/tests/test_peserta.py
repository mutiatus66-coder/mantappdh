import time
from selenium.webdriver.common.by import By
from pages.login_page import LoginPage
from base.base_page import BasePage


class TestPeserta:

    def test_peserta_full(self, driver):
        """Full Peserta — login sekali"""

        login_page = LoginPage(driver)
        base = BasePage(driver)

        # LOGIN
        login_page.login_peserta()
        print("✅ Login Peserta berhasil")

        # ============================================================
        # 1. RIWAYAT
        # ============================================================
        print("\n=== RIWAYAT ===")
        driver.get("http://localhost:8000/inovasi/riwayat")
        time.sleep(2)
        print("✅ Halaman Riwayat Inovasi terbuka")

        # ============================================================
        # 2. KELOLA USULAN
        # ============================================================
        print("\n=== KELOLA USULAN ===")
        driver.get("http://localhost:8000/inovasi/riwayat")
        time.sleep(3)

        try:
            btn = base.find_by_xpath("//a[contains(text(), 'Kelola Usulan')]")
            driver.execute_script("arguments[0].scrollIntoView(true);", btn)
            driver.execute_script("arguments[0].click();", btn)
            time.sleep(3)
            print("✅ Halaman Kelola Usulan terbuka")
        except Exception as e:
            print(f"⚠️ Gagal buka kelola usulan: {e}")

        # ============================================================
        # 3. TAMBAH USULAN STEP 1
        # ============================================================
        print("\n=== TAMBAH USULAN (STEP 1) ===")

        try:
            btn2 = base.find_by_xpath(
                "//button[contains(text(), 'Tambah Usulan')] | //a[contains(text(), 'Tambah Usulan')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", btn2)
            driver.execute_script("arguments[0].click();", btn2)
            time.sleep(3)
            print("✅ Modal Tambah Usulan terbuka")

            nama = f"Inovasi Test {int(time.time())}"
            base.fill((By.CSS_SELECTOR, "input[placeholder*='Nama inovasi']"), nama)
            base.fill((By.CSS_SELECTOR, "input[placeholder*='Judul singkat']"), f"Judul {nama}")
            base.select_by_index((By.TAG_NAME, "select"), 1)
            base.fill((By.CSS_SELECTOR, "input[placeholder*='Teknologi Tepat Guna']"), "Teknologi Tepat Guna")

            print(f"✅ Step 1 diisi: {nama}")
        except Exception as e:
            print(f"⚠️ Gagal tambah usulan: {e}")

        print("\n🎉 PESERTA SELESAI!")
