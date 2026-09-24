import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from selenium.webdriver.support.ui import WebDriverWait
from pages.login_page import LoginPage
from base.base_page import BasePage


class TestAdmin:

    def test_admin_full_crud(self, driver):
        """Full CRUD Admin — urut dari atas sampai bawah"""

        login_page = LoginPage(driver)
        base = BasePage(driver)

        # 🔥 Counter untuk notif
        self.total_step = 0
        self.total_berhasil = 0
        self.total_gagal = 0

        # ============================================================
        # 1. LOGIN
        # ============================================================
        print("\n" + "=" * 60)
        print("🚀 MULAI TEST ADMIN FULL CRUD")
        print("=" * 60)

        try:
            login_page.login_admin()
            self.notif_sukses("Login Admin")
        except Exception as e:
            self.notif_gagal("Login Admin", e)
            return

        # ============================================================
        # 2. DASHBOARD
        # ============================================================
        self.notif_header("DASHBOARD")
        try:
            driver.get("http://localhost:8000/index")
            time.sleep(2)
            self.notif_sukses("Buka Dashboard")
        except Exception as e:
            self.notif_gagal("Buka Dashboard", e)

        # ============================================================
        # 3. EVENT (CRUD)
        # ============================================================
        self.notif_header("EVENT")
        driver.get("http://localhost:8000/event")
        time.sleep(3)

        nama_event = f"Event Test {int(time.time())}"

        # TAMBAH
        try:
            base.js_click("btnTambahEvent")
            time.sleep(2)
            base.fill((By.ID, "inputNamaEvent"), nama_event)
            base.select_by_value((By.ID, "inputJenis"), "INOTEK")
            base.js_click("btnSimpanEvent")
            time.sleep(3)
            self.notif_sukses(f"Event DITAMBAH: {nama_event}")
        except Exception as e:
            self.notif_gagal("Event TAMBAH", e)

        # UBAH
        try:
            nama_event_baru = f"Event Updated {int(time.time())}"
            driver.get("http://localhost:8000/event")
            time.sleep(3)

            edit_btn = base.find_by_xpath(
                f"//tr[contains(., '{nama_event}')]//button[contains(@class, 'btn-edit-event')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", edit_btn)
            driver.execute_script("arguments[0].click();", edit_btn)
            time.sleep(2)
            base.fill((By.ID, "inputNamaEvent"), nama_event_baru)
            base.select_by_value((By.ID, "inputJenis"), "INODA")
            base.js_click("btnSimpanEvent")
            time.sleep(3)
            self.notif_sukses(f"Event DIUBAH: {nama_event_baru}")
        except Exception as e:
            self.notif_gagal("Event UBAH", e)
            nama_event_baru = nama_event

        # HAPUS
        try:
            driver.get("http://localhost:8000/event")
            time.sleep(3)

            hapus_btn = base.find_by_xpath(
                f"//tr[contains(., '{nama_event_baru}')]//button[contains(@class, 'btn-hapus-event')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", hapus_btn)
            driver.execute_script("arguments[0].click();", hapus_btn)
            time.sleep(2)
            base.js_click("btnHapusEvent")
            time.sleep(3)
            self.notif_sukses(f"Event DIHAPUS: {nama_event_baru}")
        except Exception as e:
            self.notif_gagal("Event HAPUS", e)

        # ============================================================
        # 4. SUB EVENT (CRUD)
        # ============================================================
        self.notif_header("SUB EVENT")
        driver.get("http://localhost:8000/sub-event")
        time.sleep(3)

        nama_sub = f"Sub Event Test {int(time.time())}"

        # TAMBAH
        try:
            base.js_click("btnTambahSubEvent")
            time.sleep(2)
            base.fill((By.ID, "seTahun"), "2026")
            base.select_by_index((By.ID, "seEvent"), 1)
            base.fill((By.ID, "seSubEvent"), nama_sub)
            base.fill((By.ID, "seKategori"), "Test")
            base.fill((By.ID, "seMulai"), "2026-01-01")
            base.fill((By.ID, "seBerakhir"), "2026-12-31")
            base.js_click("btnSimpanSE")
            time.sleep(4)
            self.notif_sukses(f"Sub Event DITAMBAH: {nama_sub}")
        except Exception as e:
            self.notif_gagal("Sub Event TAMBAH", e)

        # UBAH
        try:
            nama_sub_baru = f"Sub Event Updated {int(time.time())}"
            driver.get("http://localhost:8000/sub-event")
            time.sleep(3)

            edit_btn = base.find_by_xpath(
                f"//tr[contains(., '{nama_sub}')]//button[contains(@class, 'btn-edit-se')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", edit_btn)
            driver.execute_script("arguments[0].click();", edit_btn)
            time.sleep(2)
            base.fill((By.ID, "seSubEvent"), nama_sub_baru)
            base.js_click("btnSimpanSE")
            time.sleep(3)
            self.notif_sukses(f"Sub Event DIUBAH: {nama_sub_baru}")
        except Exception as e:
            self.notif_gagal("Sub Event UBAH", e)
            nama_sub_baru = nama_sub

        # HAPUS
        try:
            driver.get("http://localhost:8000/sub-event")
            time.sleep(3)

            hapus_btn = base.find_by_xpath(
                f"//tr[contains(., '{nama_sub_baru}')]//button[contains(@class, 'btn-hapus-se')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", hapus_btn)
            driver.execute_script("arguments[0].click();", hapus_btn)
            time.sleep(2)
            base.js_click("btnHapusSE")
            time.sleep(3)
            self.notif_sukses(f"Sub Event DIHAPUS: {nama_sub_baru}")
        except Exception as e:
            self.notif_gagal("Sub Event HAPUS", e)

        # ============================================================
        # 5. BIDANG (CRUD)
        # ============================================================
        self.notif_header("BIDANG")
        driver.get("http://localhost:8000/bidang")
        time.sleep(3)

        nama_bidang = f"Bidang Test {int(time.time())}"

        # TAMBAH
        try:
            accordion = driver.find_element(By.CSS_SELECTOR, ".accordion-button")
            driver.execute_script("arguments[0].scrollIntoView(true);", accordion)
            driver.execute_script("arguments[0].click();", accordion)
            time.sleep(3)

            tambah_btn = driver.find_element(By.CSS_SELECTOR, ".btn-tambah-bidang")
            driver.execute_script("arguments[0].scrollIntoView(true);", tambah_btn)
            driver.execute_script("arguments[0].click();", tambah_btn)
            time.sleep(2)

            base.fill((By.ID, "bidangNama"), nama_bidang)
            base.js_click("statusAktifBidang")
            base.js_click("btnSimpanBidang")
            time.sleep(3)
            self.notif_sukses(f"Bidang DITAMBAH: {nama_bidang}")
        except Exception as e:
            self.notif_gagal("Bidang TAMBAH", e)

        # UBAH
        try:
            nama_bidang_baru = f"Bidang Updated {int(time.time())}"
            driver.get("http://localhost:8000/bidang")
            time.sleep(3)

            accordion2 = driver.find_element(By.CSS_SELECTOR, ".accordion-button")
            driver.execute_script("arguments[0].scrollIntoView(true);", accordion2)
            driver.execute_script("arguments[0].click();", accordion2)
            time.sleep(3)

            edit_bidang = base.find_by_xpath(
                f"//tr[contains(., '{nama_bidang}')]//button[contains(@class, 'btn-ubah-bidang')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", edit_bidang)
            driver.execute_script("arguments[0].click();", edit_bidang)
            time.sleep(2)
            base.fill((By.ID, "bidangNama"), nama_bidang_baru)
            base.js_click("statusNonaktifBidang")
            base.js_click("btnSimpanBidang")
            time.sleep(3)
            self.notif_sukses(f"Bidang DIUBAH: {nama_bidang_baru}")
        except Exception as e:
            self.notif_gagal("Bidang UBAH", e)
            nama_bidang_baru = nama_bidang

        # HAPUS
        try:
            driver.get("http://localhost:8000/bidang")
            time.sleep(3)

            accordion3 = driver.find_element(By.CSS_SELECTOR, ".accordion-button")
            driver.execute_script("arguments[0].scrollIntoView(true);", accordion3)
            driver.execute_script("arguments[0].click();", accordion3)
            time.sleep(3)

            hapus_bidang = base.find_by_xpath(
                f"//tr[contains(., '{nama_bidang_baru}')]//button[contains(@class, 'btn-hapus-bidang')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", hapus_bidang)
            driver.execute_script("arguments[0].click();", hapus_bidang)
            time.sleep(2)
            base.js_click("btnHapusBidang")
            time.sleep(3)
            self.notif_sukses(f"Bidang DIHAPUS: {nama_bidang_baru}")
        except Exception as e:
            self.notif_gagal("Bidang HAPUS", e)

        # ============================================================
        # 6. USER (CRUD)
        # ============================================================
        self.notif_header("USER")
        driver.get("http://localhost:8000/user")
        time.sleep(3)

        ts = int(time.time())
        nama_user = f"User Test {ts}"
        email_user = f"user{ts}@test.com"

        # TAMBAH
        try:
            base.js_click("btnTambahUser")
            time.sleep(2)
            base.fill((By.ID, "inputNama"), nama_user)
            base.fill((By.ID, "inputEmail"), email_user)
            base.select_by_value((By.ID, "inputHakAkses"), "peserta")
            base.fill((By.ID, "inputPassword"), "password123")
            base.js_click("statusAktif")
            base.js_click("btnSimpanUser")
            time.sleep(3)
            self.notif_sukses(f"User DITAMBAH: {nama_user}")
        except Exception as e:
            self.notif_gagal("User TAMBAH", e)

        # UBAH
        try:
            nama_user_baru = f"User Updated {int(time.time())}"
            driver.get("http://localhost:8000/user")
            time.sleep(3)

            edit_user = base.find_by_xpath(
                f"//tr[contains(., '{email_user}')]//button[contains(@class, 'btn-edit-user')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", edit_user)
            driver.execute_script("arguments[0].click();", edit_user)
            time.sleep(2)
            base.fill((By.ID, "inputNama"), nama_user_baru)
            base.select_by_value((By.ID, "inputHakAkses"), "penilai")
            base.js_click("statusNonaktif")
            base.js_click("btnSimpanUser")
            time.sleep(3)
            self.notif_sukses(f"User DIUBAH: {nama_user_baru}")
        except Exception as e:
            self.notif_gagal("User UBAH", e)

        # HAPUS
        try:
            driver.get("http://localhost:8000/user")
            time.sleep(3)

            hapus_user = base.find_by_xpath(
                f"//tr[contains(., '{email_user}')]//button[contains(@class, 'btn-hapus-user')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", hapus_user)
            driver.execute_script("arguments[0].click();", hapus_user)
            time.sleep(2)
            base.js_click("btnHapusUser")
            time.sleep(3)
            self.notif_sukses(f"User DIHAPUS")
        except Exception as e:
            self.notif_gagal("User HAPUS", e)

        # ============================================================
        # 7. PENILAI (CRUD)
        # ============================================================
        self.notif_header("PENILAI")
        driver.get("http://localhost:8000/penilai")
        time.sleep(3)

        try:
            detail_btn = base.find_by_xpath("//a[contains(text(), 'Detail')]")
            driver.execute_script("arguments[0].scrollIntoView(true);", detail_btn)
            driver.execute_script("arguments[0].click();", detail_btn)
            time.sleep(3)
            detail_url = driver.current_url

            # TAMBAH
            base.js_click("btnTambahPenilai")
            time.sleep(2)
            select = driver.find_element(By.ID, "penilaiUserId")
            Select(select).select_by_index(1)
            time.sleep(1)
            base.js_click("btnSimpanPenilai")
            time.sleep(4)
            self.notif_sukses("Penilai DITAMBAH")

            # UBAH
            try:
                driver.get(detail_url)
                time.sleep(3)

                edit_penilai = driver.find_element(By.CSS_SELECTOR, ".btn-edit-penilai")
                driver.execute_script("arguments[0].scrollIntoView(true);", edit_penilai)
                driver.execute_script("arguments[0].click();", edit_penilai)
                time.sleep(2)
                select2 = driver.find_element(By.ID, "penilaiUserId")
                options2 = select2.find_elements(By.TAG_NAME, "option")
                if len(options2) > 2:
                    Select(select2).select_by_index(2)
                else:
                    Select(select2).select_by_index(1)
                time.sleep(1)
                base.js_click("btnSimpanPenilai")
                time.sleep(4)
                self.notif_sukses("Penilai DIUBAH")
            except Exception as e:
                self.notif_gagal("Penilai UBAH", e)

            # HAPUS
            try:
                driver.get(detail_url)
                time.sleep(3)

                hapus_penilai = driver.find_element(By.CSS_SELECTOR, ".btn-hapus-penilai")
                driver.execute_script("arguments[0].scrollIntoView(true);", hapus_penilai)
                driver.execute_script("arguments[0].click();", hapus_penilai)
                time.sleep(2)
                base.js_click("btnHapusPenilai")
                time.sleep(4)
                self.notif_sukses("Penilai DIHAPUS")
            except Exception as e:
                self.notif_gagal("Penilai HAPUS", e)
        except Exception as e:
            self.notif_gagal("Penilai", e)

        # ============================================================
        # 8. PENGUMUMAN (CRUD)
        # ============================================================
        self.notif_header("PENGUMUMAN")
        driver.get("http://localhost:8000/pengumuman")
        time.sleep(3)

        nama_peng = f"Pengumuman Test {int(time.time())}"

        # TAMBAH
        try:
            base.js_click("btnTambahPengumuman")
            time.sleep(2)
            base.fill((By.ID, "pJudul"), nama_peng)
            base.fill((By.ID, "pDeskripsi"), "Deskripsi test")
            base.select_by_value((By.ID, "pStatus"), "Published")
            base.js_click("btnSimpanPengumuman")
            time.sleep(3)
            self.notif_sukses(f"Pengumuman DITAMBAH: {nama_peng}")
        except Exception as e:
            self.notif_gagal("Pengumuman TAMBAH", e)

        # UBAH
        try:
            nama_peng_baru = f"Pengumuman Updated {int(time.time())}"
            driver.get("http://localhost:8000/pengumuman")
            time.sleep(3)

            edit_peng = base.find_by_xpath(
                f"//tr[contains(., '{nama_peng}')]//button[contains(@class, 'btn-edit-pengumuman')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", edit_peng)
            driver.execute_script("arguments[0].click();", edit_peng)
            time.sleep(2)
            base.fill((By.ID, "pJudul"), nama_peng_baru)
            base.select_by_value((By.ID, "pStatus"), "Draft")
            base.js_click("btnSimpanPengumuman")
            time.sleep(3)
            self.notif_sukses(f"Pengumuman DIUBAH: {nama_peng_baru}")
        except Exception as e:
            self.notif_gagal("Pengumuman UBAH", e)
            nama_peng_baru = nama_peng

        # HAPUS
        try:
            driver.get("http://localhost:8000/pengumuman")
            time.sleep(3)

            hapus_peng = base.find_by_xpath(
                f"//tr[contains(., '{nama_peng_baru}')]//button[contains(@class, 'btn-hapus-pengumuman')]"
            )
            driver.execute_script("arguments[0].scrollIntoView(true);", hapus_peng)
            driver.execute_script("arguments[0].click();", hapus_peng)
            time.sleep(2)
            base.js_click("btnHapusPengumuman")
            time.sleep(3)
            self.notif_sukses(f"Pengumuman DIHAPUS: {nama_peng_baru}")
        except Exception as e:
            self.notif_gagal("Pengumuman HAPUS", e)

        # ============================================================
        # 9. INDIKATOR TAHAP 1 (CRUD)
        # ============================================================
        self.notif_header("INDIKATOR TAHAP 1")
        driver.get("http://localhost:8000/indikator/tahap-1")
        time.sleep(3)

        try:
            detail_btn = base.find_by_xpath("//a[contains(text(), 'Detail')]")
            driver.execute_script("arguments[0].scrollIntoView(true);", detail_btn)
            driver.execute_script("arguments[0].click();", detail_btn)
            time.sleep(4)
            detail_url = driver.current_url

            # TAMBAH
            nama_ind1 = f"Indikator Test {int(time.time())}"
            base.js_click("btnTambahIndikator")
            time.sleep(2)
            base.fill((By.ID, "inputNamaIndikator"), nama_ind1)
            base.select_by_value((By.ID, "selectJenis"), "substansi")
            base.js_click("btnSimpanIndikator")
            time.sleep(4)
            self.notif_sukses(f"Indikator Tahap 1 DITAMBAH: {nama_ind1}")

            # UBAH
            try:
                nama_ind1_baru = f"Indikator Updated {int(time.time())}"
                driver.get(detail_url)
                time.sleep(3)

                edit_ind = base.find_by_xpath(
                    f"//tr[contains(., '{nama_ind1}')]//button[contains(@class, 'btn-edit-indikator')]"
                )
                driver.execute_script("arguments[0].scrollIntoView(true);", edit_ind)
                driver.execute_script("arguments[0].click();", edit_ind)
                time.sleep(2)
                base.fill((By.ID, "inputNamaIndikator"), nama_ind1_baru)
                base.js_click("btnSimpanIndikator")
                time.sleep(4)
                self.notif_sukses(f"Indikator Tahap 1 DIUBAH: {nama_ind1_baru}")
            except Exception as e:
                self.notif_gagal("Indikator Tahap 1 UBAH", e)
                nama_ind1_baru = nama_ind1

            # HAPUS
            try:
                driver.get(detail_url)
                time.sleep(3)

                hapus_ind = base.find_by_xpath(
                    f"//tr[contains(., '{nama_ind1_baru}')]//button[contains(@class, 'btn-hapus-indikator')]"
                )
                driver.execute_script("arguments[0].scrollIntoView(true);", hapus_ind)
                driver.execute_script("arguments[0].click();", hapus_ind)
                time.sleep(2)
                driver.execute_script("document.querySelector('#modalHapusIndikator .btn-danger')?.click();")
                time.sleep(4)
                self.notif_sukses(f"Indikator Tahap 1 DIHAPUS")
            except Exception as e:
                self.notif_gagal("Indikator Tahap 1 HAPUS", e)
        except Exception as e:
            self.notif_gagal("Indikator Tahap 1", e)

        # ============================================================
        # 10. INDIKATOR TAHAP 2 (CRUD)
        # ============================================================
        self.notif_header("INDIKATOR TAHAP 2")
        driver.get("http://localhost:8000/indikator/tahap-2")
        time.sleep(3)

        try:
            detail_btn = base.find_by_xpath("//a[contains(text(), 'Detail')]")
            driver.execute_script("arguments[0].scrollIntoView(true);", detail_btn)
            driver.execute_script("arguments[0].click();", detail_btn)
            time.sleep(4)
            detail_url2 = driver.current_url

            # TAMBAH
            nama_ind2 = f"Indikator Tahap 2 Test {int(time.time())}"
            base.js_click("btnTambahIndikator")
            time.sleep(2)
            base.fill((By.ID, "inputNamaIndikator"), nama_ind2)
            base.select_by_value((By.ID, "inputJenis"), "Subtansi Inovasi")
            base.fill((By.ID, "inputKeterangan"), "Keterangan test")
            base.fill((By.ID, "inputNilaiMinimal"), "0")
            base.fill((By.ID, "inputNilaiMaksimal"), "100")
            base.js_click("btnSimpanIndikator")
            time.sleep(4)
            self.notif_sukses(f"Indikator Tahap 2 DITAMBAH: {nama_ind2}")

            # UBAH
            try:
                nama_ind2_baru = f"Indikator Tahap 2 Updated {int(time.time())}"
                driver.get(detail_url2)
                time.sleep(3)
                driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
                time.sleep(2)

                edit_ind2 = base.find_by_xpath(
                    f"//tr[contains(., '{nama_ind2}')]//button[contains(@class, 'btn-edit-indikator')]"
                )
                driver.execute_script("arguments[0].scrollIntoView(true);", edit_ind2)
                driver.execute_script("arguments[0].click();", edit_ind2)
                time.sleep(2)
                base.fill((By.ID, "inputNamaIndikator"), nama_ind2_baru)
                base.js_click("btnSimpanIndikator")
                time.sleep(4)
                self.notif_sukses(f"Indikator Tahap 2 DIUBAH: {nama_ind2_baru}")
            except Exception as e:
                self.notif_gagal("Indikator Tahap 2 UBAH", e)
                nama_ind2_baru = nama_ind2

            # HAPUS
            try:
                driver.get(detail_url2)
                time.sleep(3)
                driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
                time.sleep(2)

                hapus_ind2 = base.find_by_xpath(
                    f"//tr[contains(., '{nama_ind2_baru}')]//button[contains(@class, 'btn-hapus-indikator')]"
                )
                driver.execute_script("arguments[0].scrollIntoView(true);", hapus_ind2)
                driver.execute_script("arguments[0].click();", hapus_ind2)
                time.sleep(2)
                driver.execute_script("document.querySelector('#modalHapus .btn-danger')?.click();")
                time.sleep(4)
                self.notif_sukses(f"Indikator Tahap 2 DIHAPUS")
            except Exception as e:
                self.notif_gagal("Indikator Tahap 2 HAPUS", e)
        except Exception as e:
            self.notif_gagal("Indikator Tahap 2", e)

        # ============================================================
        # 11-14. MENU LAIN (Read Only)
        # ============================================================
        self.notif_header("MENU LAIN")

        menu_lain = [
            ("Riwayat", "http://localhost:8000/inovasi/riwayat"),
            ("Rekap Nilai", "http://localhost:8000/inovasi/rekap-nilai"),
            ("Penilaian Tahap 1", "http://localhost:8000/penilaian/tahap-1"),
            ("Penilaian Tahap 2", "http://localhost:8000/penilaian/tahap-2"),
        ]

        for nama, url in menu_lain:
            try:
                driver.get(url)
                time.sleep(2)
                self.notif_sukses(f"Buka {nama}")
            except Exception as e:
                self.notif_gagal(f"Buka {nama}", e)

        # ============================================================
        # SUMMARY
        # ============================================================
        print("\n" + "=" * 60)
        print("📊 SUMMARY HASIL TEST")
        print("=" * 60)
        print(f"✅ Total Berhasil : {self.total_berhasil}")
        print(f"❌ Total Gagal    : {self.total_gagal}")
        print(f"📌 Total Step     : {self.total_step}")
        print("=" * 60)

        if self.total_gagal == 0:
            print("🎉 SEMUA TEST BERHASIL!")
        else:
            print(f"⚠️ Ada {self.total_gagal} test yang gagal")
        print("=" * 60)

    # ================================================================
    # HELPER: NOTIF
    # ================================================================
    def notif_header(self, nama):
        """Notif header untuk setiap section"""
        print("\n" + "─" * 60)
        print(f"📂 {nama}")
        print("─" * 60)

    def notif_sukses(self, pesan):
        """Notif sukses"""
        self.total_step += 1
        self.total_berhasil += 1
        print(f"  ✅ [{self.total_step}] {pesan}")

    def notif_gagal(self, pesan, error=None):
        """Notif gagal"""
        self.total_step += 1
        self.total_gagal += 1
        print(f"  ❌ [{self.total_step}] {pesan} GAGAL")
        if error:
            print(f"     ⚠️ Error: {str(error)[:100]}")
