import os
import sys
import time
from datetime import datetime

# Load helpers and resolve venv path before importing selenium
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
import helpers
from helpers import sleep, test_data_tables, take_screenshot_session, clear_input, demo_toggle_theme

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def test1_master():
    options = Options()
    options.add_argument('--window-size=1920,1080')
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--ignore-certificate-errors')
    if os.environ.get('HEADLESS') == 'true':
        options.add_argument('--headless=new')

    driver = webdriver.Chrome(options=options)
    baseUrl = os.environ.get('BASE_URL', 'https://mantappdh.test')
    waktu = int(time.time() * 1000)

    def execute_script_click(selector):
        el = driver.find_element(By.CSS_SELECTOR, selector)
        helpers.highlight_and_click(driver, el, duration=0.8)
        sleep(1.5)

    try:
        print("[1] MASTER DATA (Selenium Python)")

        print("   -> Login Admin (dengan highlight penuh)...")
        helpers.login_admin_with_highlights(driver, baseUrl)

        # TEST TEMA
        print("   -> Test Tema Gelap & Terang...")
        demo_toggle_theme(driver, 'Admin')

        # 1. EVENT
        print("   -> Event: Tambah, Ubah, Hapus...")
        execute_script_click('a.ri-menu-item[href="/event"]')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Event_Index')
        test_data_tables(driver, 'Event')
        execute_script_click('#btnTambahEvent')
        sleep(0.8)
        take_screenshot_session(driver, 'Test1_Event_Tambah_Modal')
        inp_nama_event = driver.find_element(By.CSS_SELECTOR, '#inputNamaEvent')
        helpers.highlight_element(driver, inp_nama_event, duration=0.6)
        inp_nama_event.send_keys(f"Event {waktu}")

        inp_jenis_event = driver.find_element(By.CSS_SELECTOR, '#inputJenis')
        helpers.highlight_element(driver, inp_jenis_event, duration=0.6)
        inp_jenis_event.send_keys('INOTEK')
        execute_script_click('#btnSimpanEvent')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Event_Simpan')

        driver.execute_script("""
            let btns = document.querySelectorAll('.btn-edit-event'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        input_event = driver.find_element(By.CSS_SELECTOR, '#inputNamaEvent')
        input_event.clear()
        input_event.send_keys(f"Event Edit {waktu}")
        execute_script_click('#btnSimpanEvent')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Event_Edit_Simpan')

        driver.execute_script("""
            let btns = document.querySelectorAll('.btn-hapus-event'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        execute_script_click('#btnHapusEvent')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Event_Hapus')

        # 2. SUB EVENT
        print("   -> Sub Event: Tambah, Ubah, Hapus...")
        execute_script_click('a.ri-menu-item[href="/sub-event"]')
        sleep(2)
        take_screenshot_session(driver, 'Test1_SubEvent_Index')
        test_data_tables(driver, 'Sub Event')
        execute_script_click('#btnTambahSubEvent')
        sleep(0.8)
        take_screenshot_session(driver, 'Test1_SubEvent_Tambah_Modal')
        driver.find_element(By.CSS_SELECTOR, '#seTahun').send_keys(str(datetime.now().year + 1))
        driver.execute_script("""
            let sel = document.getElementById('seEvent'); 
            if(sel && sel.options.length > 1) sel.selectedIndex = 1;
        """)
        driver.find_element(By.CSS_SELECTOR, '#seSubEvent').send_keys(f"Sub Event {waktu}")
        driver.find_element(By.CSS_SELECTOR, '#seKategori').send_keys('Kategori Test')
        driver.find_element(By.CSS_SELECTOR, '#seMulai').send_keys('2025-01-01')
        driver.find_element(By.CSS_SELECTOR, '#seBerakhir').send_keys('2025-12-31')
        execute_script_click('#btnSimpanSE')
        sleep(2)
        take_screenshot_session(driver, 'Test1_SubEvent_Simpan')

        driver.execute_script("""
            let btns = document.querySelectorAll('.btn-hapus-se'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        execute_script_click('#btnHapusSE')
        sleep(2)
        take_screenshot_session(driver, 'Test1_SubEvent_Hapus')

        # 3. BIDANG
        print("   -> Bidang: Tambah, Hapus...")
        execute_script_click('a.ri-menu-item[href="/bidang"]')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Bidang_Index')
        test_data_tables(driver, 'Bidang')
        driver.execute_script("""
            let acc = document.querySelector('.bidang-accordion-btn'); 
            if(acc) { acc.scrollIntoView({block:'center'}); acc.click(); }
        """)
        sleep(1.5)
        driver.execute_script("""
            let btn = document.querySelector('.btn-tambah-bidang'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        driver.find_element(By.CSS_SELECTOR, '#bidangNama').send_keys(f"Bidang {waktu}")
        execute_script_click('#statusAktifBidang')
        execute_script_click('#btnSimpanBidang')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Bidang_Simpan')

        driver.execute_script("""
            let btns = document.querySelectorAll('.btn-hapus-bidang'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        execute_script_click('#btnHapusBidang')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Bidang_Hapus')

        # 4. USER
        print("   -> User: Tambah, Login As, Riwayat, Kembali ke Admin, Hapus...")
        execute_script_click('a.ri-menu-item[href="/user"]')
        sleep(2)
        take_screenshot_session(driver, 'Test1_User_Index')
        test_data_tables(driver, 'User')
        execute_script_click('#btnTambahUser')
        sleep(0.8)
        take_screenshot_session(driver, 'Test1_User_Tambah_Modal')
        driver.find_element(By.CSS_SELECTOR, '#inputNama').send_keys('User Test')
        driver.find_element(By.CSS_SELECTOR, '#inputEmail').send_keys(f"user_{waktu}@test.com")
        driver.find_element(By.CSS_SELECTOR, '#inputHakAkses').send_keys('peserta')
        driver.find_element(By.CSS_SELECTOR, '#inputPassword').send_keys('Password123!')
        execute_script_click('#btnSimpanUser')
        sleep(2)
        user_search_input = driver.find_element(By.CSS_SELECTOR, '.dt-search input')
        clear_input(driver, user_search_input)
        user_search_input.send_keys(f"user_{waktu}@test.com")
        sleep(1)

        # Klik Login As (Impersonasi User)
        print("      -> Klik Login As pada user baru (dengan Highlight)...")
        btn_login_as = driver.find_element(By.CSS_SELECTOR, 'a[href*="/login-as"]')
        helpers.highlight_and_click(driver, btn_login_as, duration=1.2)
        sleep(2.5)
        take_screenshot_session(driver, 'Test1_User_LoginAs')

        # Demonstrasi Navigasi & Interaksi Halaman User
        print("      -> Demonstrasi Halaman User (Riwayat Inovasi & DataTables)...")
        try:
            link_riwayat = driver.find_element(By.CSS_SELECTOR, 'a.ri-menu-item[href="/inovasi/riwayat"], a[href*="/inovasi/riwayat"]')
            helpers.highlight_and_click(driver, link_riwayat, duration=1.2)
            sleep(2.5)
        except Exception:
            driver.get(f"{baseUrl}/inovasi/riwayat")
            sleep(2.5)
        take_screenshot_session(driver, 'Test1_User_Riwayat')
        test_data_tables(driver, 'User Riwayat Inovasi')

        # Kembali ke akun Admin (melalui banner impersonasi)
        print("      -> Kembali ke akun Admin (Menekan tombol banner impersonasi dengan Highlight)...")
        btn_login_back = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, 'a[href*="/user/login-back"], a.btn-warning'))
        )
        helpers.highlight_and_click(driver, btn_login_back, duration=1.5)
        sleep(2.5)
        take_screenshot_session(driver, 'Test1_User_LoginBack')

        # Hapus user yang baru dibuat di Admin Master User
        print("      -> Hapus user baru di Master User Admin...")
        user_search_input_after = driver.find_element(By.CSS_SELECTOR, '.dt-search input')
        helpers.highlight_element(driver, user_search_input_after, duration=0.8)
        clear_input(driver, user_search_input_after)
        user_search_input_after.send_keys(f"user_{waktu}@test.com")
        sleep(1)
        btn_hapus_user = driver.find_element(By.CSS_SELECTOR, '.btn-hapus-user')
        helpers.highlight_and_click(driver, btn_hapus_user, duration=1.0)
        sleep(0.8)
        execute_script_click('#btnHapusUser')
        sleep(2)
        take_screenshot_session(driver, 'Test1_User_Hapus')

        # 5. PENGUMUMAN
        print("   -> Pengumuman: Tambah, Hapus...")
        execute_script_click('a.ri-menu-item[href="/pengumuman"]')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Pengumuman_Index')
        test_data_tables(driver, 'Pengumuman')
        execute_script_click('#btnTambahPengumuman')
        sleep(0.8)
        take_screenshot_session(driver, 'Test1_Pengumuman_Tambah_Modal')
        driver.find_element(By.CSS_SELECTOR, '#pJudul').send_keys(f"Pengumuman {waktu}")
        driver.find_element(By.CSS_SELECTOR, '#pDeskripsi').send_keys('Deskripsi')
        driver.find_element(By.CSS_SELECTOR, '#pStatus').send_keys('Draft')
        execute_script_click('#btnSimpanPengumuman')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Pengumuman_Simpan')

        driver.execute_script("""
            let btns = document.querySelectorAll('.btn-hapus-pengumuman'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        execute_script_click('#btnHapusPengumuman')
        sleep(2)
        take_screenshot_session(driver, 'Test1_Pengumuman_Hapus')

        # LOGOUT
        print("   -> Logout...")
        driver.execute_script("""
            let avatar = document.querySelector('#kt_header_user_menu_toggle .symbol') || document.querySelector('.cursor-pointer.symbol'); 
            if(avatar) { avatar.scrollIntoView({block:'center'}); avatar.click(); }
        """)
        sleep(1)
        driver.execute_script("""
            let signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out')); 
            if(signOut) { signOut.click(); }
        """)
        sleep(3)

        print("✅ MASTER TEST SELESAI")

    except Exception as e:
        print("❌ Error:", e)
        sys.exit(1)
    finally:
        driver.quit()

if __name__ == '__main__':
    test1_master()
