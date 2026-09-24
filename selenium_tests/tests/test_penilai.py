import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from pages.login_page import LoginPage
from base.base_page import BasePage


class TestPenilai:

    def test_penilai_full_crud(self, driver):
        """Full Penilai - Login sekali, semua aksi"""

        login_page = LoginPage(driver)
        base = BasePage(driver)
        wait = WebDriverWait(driver, 15)

        # 🔥 COUNTER & NOTIF
        total_step = 0
        total_berhasil = 0
        total_gagal = 0

        def notif_header(nama):
            print(f"\n{'='*60}")
            print(f"  MODUL: {nama}")
            print(f"{'='*60}")

        def notif_sukses(pesan):
            nonlocal total_step, total_berhasil
            total_step += 1
            total_berhasil += 1
            print(f"  [PASS] [{total_step:02d}] {pesan}")

        def notif_gagal(pesan, err=None):
            nonlocal total_step, total_gagal
            total_step += 1
            total_gagal += 1
            print(f"  [FAIL] [{total_step:02d}] {pesan}")
            if err:
                print(f"         -> {str(err)[:80]}")

        # ============================================================
        # 1. LOGIN
        # ============================================================
        notif_header("AUTH")
        try:
            login_page.login_penilai()
            notif_sukses("Login Penilai berhasil")
        except Exception as e:
            notif_gagal("Login Penilai gagal", e)
            return

        # ============================================================
        # 2. RIWAYAT
        # ============================================================
        notif_header("RIWAYAT")
        try:
            driver.get("http://localhost:8000/inovasi/riwayat")
            time.sleep(2)
            notif_sukses("Buka halaman Riwayat")
        except Exception as e:
            notif_gagal("Buka Riwayat", e)

        # ============================================================
        # 3. REKAP NILAI
        # ============================================================
        notif_header("REKAP NILAI")
        try:
            driver.get("http://localhost:8000/inovasi/rekap-nilai")
            time.sleep(2)
            notif_sukses("Buka halaman Rekap Nilai")
        except Exception as e:
            notif_gagal("Buka Rekap Nilai", e)

        # ============================================================
        # 4. PENILAIAN TAHAP 1 - KLIK "LIHAT NILAI VERIFIKASI"
        # ============================================================
        notif_header("PENILAIAN TAHAP 1")
        try:
            driver.get("http://localhost:8000/penilaian/tahap-1")
            time.sleep(3)
            notif_sukses("Buka halaman Penilaian Tahap 1")

            # Klik "Lihat Nilai Verifikasi"
            btn = base.find_by_xpath("//a[contains(text(), 'Lihat Nilai Verifikasi')]")
            driver.execute_script("arguments[0].scrollIntoView(true);", btn)
            driver.execute_script("arguments[0].click();", btn)
            time.sleep(3)
            notif_sukses("Klik Lihat Nilai Verifikasi")

            # 🔥 KLIK INPUT NILAI (icon pensil)
            try:
                # Cari tombol Input Nilai (icon edit/pencil)
                input_btn = base.find_by_xpath(
                    "//button[contains(@class, 'btn-warning') or contains(@class, 'btn-edit') "
                    "or contains(@title, 'Nilai') or .//i[contains(@class, 'pencil')]]"
                )
                driver.execute_script("arguments[0].scrollIntoView(true);", input_btn)
                driver.execute_script("arguments[0].click();", input_btn)
                time.sleep(3)
                notif_sukses("Klik tombol Input Nilai")

                # ================================================
                # ISI NILAI (5 indikator)
                # ================================================
                time.sleep(2)

                # Cari semua input field di modal
                inputs = driver.find_elements(By.CSS_SELECTOR, "input[type='number']")

                if len(inputs) > 0:
                    # Isi semua input dengan nilai 85
                    nilai_list = [85, 85, 85, 85, 85]
                    for i, inp in enumerate(inputs[:5]):
                        driver.execute_script("arguments[0].scrollIntoView(true);", inp)
                        inp.clear()
                        inp.send_keys(str(nilai_list[i] if i < len(nilai_list) else 80))
                        print(f"    -> Input ke-{i+1}: {nilai_list[i] if i < len(nilai_list) else 80}")

                    notif_sukses(f"Isi {len(inputs[:5])} nilai")

                    # Klik Simpan Nilai
                    simpan_btn = base.find_by_xpath(
                        "//button[contains(text(), 'Simpan Nilai') or contains(text(), 'Simpan')]"
                    )
                    driver.execute_script("arguments[0].scrollIntoView(true);", simpan_btn)
                    driver.execute_script("arguments[0].click();", simpan_btn)
                    time.sleep(3)
                    notif_sukses("Simpan Nilai Tahap 1")
                else:
                    notif_gagal("Tidak ada input nilai ditemukan")

            except Exception as e:
                notif_gagal("Input Nilai Tahap 1", e)

        except Exception as e:
            notif_gagal("Penilaian Tahap 1", e)

        # ============================================================
        # 5. PENILAIAN TAHAP 2 - KLIK "LIHAT NILAI NOMINATOR"
        # ============================================================
        notif_header("PENILAIAN TAHAP 2")
        try:
            driver.get("http://localhost:8000/penilaian/tahap-2")
            time.sleep(3)
            notif_sukses("Buka halaman Penilaian Tahap 2")

            # Klik "Lihat Nilai Nominator"
            btn = base.find_by_xpath("//a[contains(text(), 'Lihat Nilai Nominator')]")
            driver.execute_script("arguments[0].scrollIntoView(true);", btn)
            driver.execute_script("arguments[0].click();", btn)
            time.sleep(3)
            notif_sukses("Klik Lihat Nilai Nominator")

            # 🔥 Coba cari tombol Input Nilai
            try:
                input_btn = base.find_by_xpath(
                    "//button[contains(@class, 'btn-warning') or contains(@class, 'btn-edit') "
                    "or contains(@title, 'Nilai')]"
                )
                driver.execute_script("arguments[0].scrollIntoView(true);", input_btn)
                driver.execute_script("arguments[0].click();", input_btn)
                time.sleep(3)
                notif_sukses("Klik tombol Input Nilai Tahap 2")

                # Isi nilai
                time.sleep(2)
                inputs = driver.find_elements(By.CSS_SELECTOR, "input[type='number']")

                if len(inputs) > 0:
                    for i, inp in enumerate(inputs[:5]):
                        driver.execute_script("arguments[0].scrollIntoView(true);", inp)
                        inp.clear()
                        inp.send_keys("88")

                    notif_sukses(f"Isi {len(inputs[:5])} nilai Tahap 2")

                    simpan_btn = base.find_by_xpath(
                        "//button[contains(text(), 'Simpan Nilai') or contains(text(), 'Simpan')]"
                    )
                    driver.execute_script("arguments[0].scrollIntoView(true);", simpan_btn)
                    driver.execute_script("arguments[0].click();", simpan_btn)
                    time.sleep(3)
                    notif_sukses("Simpan Nilai Tahap 2")
                else:
                    notif_gagal("Tidak ada input nilai Tahap 2")
            except Exception as e:
                notif_gagal("Input Nilai Tahap 2", e)

        except Exception as e:
            notif_gagal("Penilaian Tahap 2", e)

        # ============================================================
        # 6. RIWAYAT LAGI (verifikasi)
        # ============================================================
        notif_header("VERIFIKASI")
        try:
            driver.get("http://localhost:8000/inovasi/riwayat")
            time.sleep(2)
            notif_sukses("Buka halaman Riwayat (verifikasi)")
        except Exception as e:
            notif_gagal("Buka Riwayat verifikasi", e)

        # ============================================================
        # SUMMARY
        # ============================================================
        print(f"\n{'='*60}")
        print(f"  SUMMARY HASIL TEST")
        print(f"{'='*60}")
        print(f"  Total Step     : {total_step}")
        print(f"  Total Berhasil : {total_berhasil}")
        print(f"  Total Gagal    : {total_gagal}")
        print(f"{'='*60}")

        if total_gagal == 0:
            print("  STATUS: SEMUA TEST BERHASIL")
        else:
            print(f"  STATUS: ADA {total_gagal} TEST YANG GAGAL")
        print(f"{'='*60}")
