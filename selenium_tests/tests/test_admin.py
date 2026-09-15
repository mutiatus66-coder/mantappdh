import time
from selenium.webdriver.common.by import By
from pages.login_page import LoginPage
from base.base_page import BasePage


class TestAdmin:

    def test_admin_full_crud(self, driver):
        """Full CRUD Admin — login sekali, semua CRUD"""

        login_page = LoginPage(driver)
        base = BasePage(driver)

        # ============================================================
        # 1. LOGIN (SEKALI)
        # ============================================================
        login_page.login_admin()
        print("✅ Login Admin berhasil")

        # ============================================================
        # 2. EVENT
        # ============================================================
        print("\n=== EVENT ===")
        driver.get("http://localhost:8000/event")
        time.sleep(2)
        print("✅ Halaman Event terbuka")

        nama_event = f"Event Test {int(time.time())}"
        base.click((By.ID, "btnTambahEvent"))
        time.sleep(1)
        base.fill((By.ID, "inputNamaEvent"), nama_event)
        base.select_by_value((By.ID, "inputJenis"), "INOTEK")
        base.click((By.ID, "btnSimpanEvent"))
        time.sleep(2)
        print(f"✅ Event ditambahkan: {nama_event}")

        nama_event_baru = f"Event Updated {int(time.time())}"
        edit_btn = base.find_by_xpath(
            f"//tr[contains(., '{nama_event}')]//button[contains(@class, 'btn-edit-event')]"
        )
        driver.execute_script("arguments[0].scrollIntoView(true);", edit_btn)
        edit_btn.click()
        time.sleep(1)
        base.fill((By.ID, "inputNamaEvent"), nama_event_baru)
        base.select_by_value((By.ID, "inputJenis"), "INODA")
        base.click((By.ID, "btnSimpanEvent"))
        time.sleep(2)
        print(f"✅ Event diubah: {nama_event_baru}")

        hapus_btn = base.find_by_xpath(
            f"//tr[contains(., '{nama_event_baru}')]//button[contains(@class, 'btn-hapus-event')]"
        )
        driver.execute_script("arguments[0].scrollIntoView(true);", hapus_btn)
        driver.execute_script("arguments[0].click();", hapus_btn)
        time.sleep(1)
        base.click((By.ID, "btnHapusEvent"))
        time.sleep(2)
        print(f"✅ Event dihapus: {nama_event_baru}")

        # ============================================================
        # 3. SUB EVENT
        # ============================================================
        print("\n=== SUB EVENT ===")
        driver.get("http://localhost:8000/sub-event")
        time.sleep(2)
        print("✅ Halaman Sub Event terbuka")

        nama_sub = f"Sub Event Test {int(time.time())}"
        base.click((By.ID, "btnTambahSubEvent"))
        time.sleep(1)
        base.fill((By.ID, "seTahun"), "2026")
        base.select_by_index((By.ID, "seEvent"), 1)
        base.fill((By.ID, "seSubEvent"), nama_sub)
        base.fill((By.ID, "seKategori"), "Test")
        base.fill((By.ID, "seMulai"), "2026-01-01")
        base.fill((By.ID, "seBerakhir"), "2026-12-31")
        base.click((By.ID, "btnSimpanSE"))
        time.sleep(3)
        print(f"✅ Sub Event ditambahkan: {nama_sub}")

        hapus_btn = base.find_by_xpath(
            f"//tr[contains(., '{nama_sub}')]//button[contains(@class, 'btn-hapus-se')]"
        )
        driver.execute_script("arguments[0].scrollIntoView(true);", hapus_btn)
        driver.execute_script("arguments[0].click();", hapus_btn)
        time.sleep(1)
        base.click((By.ID, "btnHapusSE"))
        time.sleep(2)
        print(f"✅ Sub Event dihapus: {nama_sub}")

        # ============================================================
        # 4. PENGUMUMAN
        # ============================================================
        print("\n=== PENGUMUMAN ===")
        driver.get("http://localhost:8000/pengumuman")
        time.sleep(2)
        print("✅ Halaman Pengumuman terbuka")

        nama_peng = f"Pengumuman Test {int(time.time())}"
        base.click((By.ID, "btnTambahPengumuman"))
        time.sleep(1)
        base.fill((By.ID, "pJudul"), nama_peng)
        base.fill((By.ID, "pDeskripsi"), "Deskripsi test")
        base.select_by_value((By.ID, "pStatus"), "Published")
        base.click((By.ID, "btnSimpanPengumuman"))
        time.sleep(2)
        print(f"✅ Pengumuman ditambahkan: {nama_peng}")

        driver.get("http://localhost:8000/pengumuman")
        time.sleep(3)

        hapus_btn = base.find_by_xpath(
            f"//tr[contains(., '{nama_peng}')]//button[contains(@class, 'btn-hapus-pengumuman')]"
        )
        driver.execute_script("arguments[0].scrollIntoView(true);", hapus_btn)
        time.sleep(1)
        driver.execute_script("arguments[0].click();", hapus_btn)
        time.sleep(2)
        driver.execute_script("document.getElementById('btnHapusPengumuman')?.click();")
        time.sleep(2)
        print(f"✅ Pengumuman dihapus: {nama_peng}")

        # ============================================================
        # 5. USER
        # ============================================================
        print("\n=== USER ===")
        driver.get("http://localhost:8000/user")
        time.sleep(2)
        print("✅ Halaman User terbuka")

        ts = int(time.time())
        nama_user = f"User Test {ts}"
        email_user = f"user{ts}@test.com"

        base.click((By.ID, "btnTambahUser"))
        time.sleep(1)
        base.fill((By.ID, "inputNama"), nama_user)
        base.fill((By.ID, "inputEmail"), email_user)
        base.select_by_value((By.ID, "inputHakAkses"), "peserta")
        base.fill((By.ID, "inputPassword"), "password123")
        base.click((By.ID, "statusAktif"))
        base.click((By.ID, "btnSimpanUser"))
        time.sleep(2)
        print(f"✅ User ditambahkan: {nama_user}")

        print("\n🎉 ADMIN FULL CRUD SELESAI!")
